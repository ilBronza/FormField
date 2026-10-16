/** Refresh FormField select options on opening or changes to configured fields. */
module.exports = function ($)
{
    const selector = 'select[data-possible-values-route]';
    const requests = new WeakMap();
    const opening = new WeakSet();

    function isOptionsMap(value)
    {
        return value !== null && typeof value === 'object';
    }

    function readNames(field, attribute)
    {
        const names = JSON.parse($(field).attr(attribute) || '[]');

        if (! Array.isArray(names) || names.some(name => typeof name !== 'string' || ! name.trim()))
            throw new Error('Configurazione non valida: ' + attribute);

        return Array.from(new Set(names));
    }

    function normalizeName(name)
    {
        return String(name || '').replace(/\[\]$/, '');
    }

    function findFields(reference, name)
    {
        const controls = reference.form ? Array.from(reference.form.elements)
            : $('input, select, textarea').get().filter(field => ! field.form);
        const $controls = $(controls).filter('input, select, textarea');
        const flatName = $(reference).attr('data-flatname');
        const renderedName = normalizeName($(reference).attr('name'));
        const suffix = flatName && renderedName.startsWith(flatName) ? renderedName.slice(flatName.length) : '';

        // Prefer the same repeatable index, then exact names, then flat names.
        const $indexed = $controls.filter(function ()
        {
            return normalizeName(this.name) === normalizeName(name) + suffix;
        });

        if ($indexed.length)
            return $indexed;

        const $exact = $controls.filter(function ()
        {
            return normalizeName(this.name) === normalizeName(name);
        });

        if ($exact.length)
            return $exact;

        return $controls.filter(function ()
        {
            return normalizeName($(this).attr('data-flatname')) === normalizeName(name);
        });
    }

    function getContext(select)
    {
        const context = Object.create(null);

        readNames(select, 'data-possible-values-route-fields').forEach(function (name)
        {
            const $fields = findFields(select, name);
            const sameName = new Set($fields.get().map(field => field.name)).size === 1;

            if ($fields.length && sameName && $fields.get().every(field => field.type === 'radio'))
                context[name] = $fields.filter(':checked').val() ?? '';
            else if ($fields.length && sameName && $fields.get().every(field => field.type === 'checkbox'))
                context[name] = $fields.length > 1 || $fields[0].name.endsWith('[]')
                    ? $fields.filter(':checked').get().map(field => field.value) : $fields.prop('checked');
            else if ($fields.length === 1)
                context[name] = $fields.val() ?? ($fields.prop('multiple') ? [] : '');
            else
                throw new Error('Campo sorgente mancante o ambiguo: ' + name);
        });

        return context;
    }

    function getRequestContext(select)
    {
        const route = $(select).attr('data-possible-values-route');
        const context = getContext(select);

        return { route, context, key: JSON.stringify([route, context]) };
    }

    function staleResponse()
    {
        const error = new Error('Opzioni superate da una modifica del form');
        error.name = 'StaleSelectOptionsError';

        return error;
    }

    function isCurrentRequest(select, entry)
    {
        if (requests.get(select) !== entry || ! select.isConnected || $(select).is(':disabled, [readonly]'))
            return false;

        try
        {
            return getRequestContext(select).key === entry.key;
        }
        catch (error)
        {
            return false;
        }
    }

    function populate(select, list)
    {
        if (! isOptionsMap(list))
            throw new Error('Risposta non valida per le opzioni del select');

        const $select = $(select);
        const value = $select.val();
        const selectedValues = value === null ? [] : (Array.isArray(value) ? value : [value]);
        const fragment = document.createDocumentFragment();
        const values = new Set();
        const placeholder = $select.children('option[value=""]').first();
        const manualInput = $select.children('option.ibuseselectmanualinput').first();

        if (placeholder.length)
        {
            fragment.appendChild(placeholder[0].cloneNode(true));
            values.add('');
        }

        function appendOptions(parent, options)
        {
            if (! isOptionsMap(options))
                throw new Error('Lista non valida per le opzioni del select');

            Object.keys(options).forEach(function (key)
            {
                const label = options[key];

                if (label !== null && typeof label === 'object')
                    throw new Error('Etichetta non valida per le opzioni del select');

                if (values.has(key))
                    return;

                const option = document.createElement('option');
                option.value = key;
                option.textContent = label === null ? '' : String(label);
                parent.appendChild(option);
                values.add(key);
            });
        }

        if (Object.prototype.hasOwnProperty.call(list, 'groups'))
        {
            if (! isOptionsMap(list.groups))
                throw new Error('Gruppi non validi per le opzioni del select');

            if (Object.prototype.hasOwnProperty.call(list, 'null'))
                appendOptions(fragment, { '': list.null });

            Object.keys(list.groups).forEach(function (label)
            {
                const group = document.createElement('optgroup');
                group.label = label;
                appendOptions(group, list.groups[label]);
                fragment.appendChild(group);
            });
        }
        else
            appendOptions(fragment, list);

        if (manualInput.length)
            fragment.appendChild(manualInput[0].cloneNode(true));

        const remaining = selectedValues.filter(selected => values.has(String(selected)));
        $select.empty().append(fragment).val(select.multiple ? remaining : (remaining[0] ?? null));

        // Refresh Select2 labels without triggering editor saves or fetchers.
        if ($select.data('select2'))
            $select.trigger('change.select2');
    }

    window.ibFormFieldReloadSelectOptions = function (select)
    {
        const $select = $(select);
        select = $select[0];
        const route = $select.attr('data-possible-values-route');

        if (! select || ! route || $select.is(':disabled, [readonly]'))
            return null;

        let config;

        try
        {
            config = getRequestContext(select);
        }
        catch (error)
        {
            return Promise.reject(error);
        }

        const pending = requests.get(select);

        if (pending && pending.key === config.key)
            return pending.promise;

        // Keep empty multiple values explicit: jQuery omits empty arrays.
        const data = Object.create(null);
        Object.keys(config.context).forEach(function (name)
        {
            const value = config.context[name];
            data[name] = Array.isArray(value) && ! value.length ? [''] : value;
        });

        const entry = { key: config.key };
        const request = Promise.resolve($.ajax({
            url: route,
            type: 'GET',
            data: data,
            dataType: 'json',
            cache: false,
        })).then(function (response)
        {
            if (! isCurrentRequest(select, entry))
                throw staleResponse();

            const list = response && Object.prototype.hasOwnProperty.call(response, 'list')
                ? response.list : response;

            populate(select, list);

            return list;
        }, function (error)
        {
            if (! isCurrentRequest(select, entry))
                throw staleResponse();

            throw error;
        }).finally(function ()
        {
            if (requests.get(select) === entry)
                requests.delete(select);
        });

        entry.promise = request;
        requests.set(select, entry);

        return request;
    };

    function reportError(error)
    {
        if (error.name === 'StaleSelectOptionsError')
            return;

        console.error('[formfield select]', error);

        if (typeof window.addDangerNotification === 'function')
            window.addDangerNotification('Impossibile caricare le opzioni del campo');
    }

    $(document).on('change', 'input[data-refresh-select-options], select[data-refresh-select-options], textarea[data-refresh-select-options]', function ()
    {
        const source = this;

        if ($(source).is(':disabled, [readonly]'))
            return;

        let targets;

        try
        {
            targets = readNames(source, 'data-refresh-select-options');
        }
        catch (error)
        {
            reportError(error);

            return;
        }

        targets.forEach(function (name)
        {
            try
            {
                const $target = findFields(source, name);

                if ($target.length !== 1 || ! $target.is('select'))
                    throw new Error('Select da aggiornare mancante o ambiguo: ' + name);

                if ($target.is(':disabled, [readonly]'))
                    return;

                if (! $target.attr('data-possible-values-route'))
                    throw new Error('URL getter mancante per il select: ' + name);

                window.ibFormFieldReloadSelectOptions($target[0]).catch(reportError);
            }
            catch (error)
            {
                reportError(error);
            }
        });
    });

    $(document).on('select2:opening', selector, function (event)
    {
        if (opening.has(this))
            return;

        const request = window.ibFormFieldReloadSelectOptions(this);

        if (! request)
            return;

        event.preventDefault();

        // Repeated clicks while loading share the GET and open only once.
        if ($(this).data('ibFormFieldSelectOpening'))
            return;

        const select = this;
        $(select).data('ibFormFieldSelectOpening', true);

        request.then(function ()
        {
            if (! select.isConnected || $(select).is(':disabled, [readonly]') || ! $(select).data('select2'))
                return;

            opening.add(select);

            try
            {
                $(select).select2('open');
            }
            finally
            {
                opening.delete(select);
            }
        }).catch(reportError).finally(function ()
        {
            $(select).removeData('ibFormFieldSelectOpening');
        });
    });

    // Native dropdowns cannot reliably be reopened after an asynchronous GET.
    // Start on pointer/key interaction and update options as soon as it completes.
    $(document).on('mousedown keydown', selector, function (event)
    {
        if ($(this).data('select2') || (event.type === 'mousedown' && event.button !== 0))
            return;

        if (event.type === 'keydown' && ! ['ArrowDown', 'ArrowUp', 'Enter', ' ', 'F4'].includes(event.key))
            return;

        const request = window.ibFormFieldReloadSelectOptions(this);

        if (request)
            request.catch(reportError);
    });
};

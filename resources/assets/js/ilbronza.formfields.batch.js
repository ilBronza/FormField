/**
 * Batch field refresh — used by getFieldValueFromEditor, refreshFetchingFieldsValues,
 * and dtRefreshFieldsList via their public entry points.
 */
jQuery(document).ready(function ($)
{
    window.__ibFieldRefreshBatch = window.__ibFieldRefreshBatch || {
        debounceMs: 50,
        queues: {},
        timers: {},
        inflight: {}
    };

    window.__ibFindEditorFieldTargetsBatch = function (fieldName)
    {
        var $byName = $('*[name="' + fieldName + '"]');

        if ($byName.length)
            return $byName;

        return $('[data-field="' + fieldName + '"]');
    };

    window.__ibResolveEditorUrlForBatch = function (fieldNames)
    {
        if (! fieldNames || ! fieldNames.length)
            return null;

        for (var i = 0; i < fieldNames.length; i++)
        {
            var $target = window.__ibFindEditorFieldTargetsBatch(fieldNames[i]).first();

            if ($target.length)
            {
                var url = $target.data('updateeditorurl');

                if (url)
                    return url;
            }
        }

        return null;
    };

  /**
   * Apply a fetched value to a single editor target (batch path; mirrors legacy apply logic).
   */
    window.__ibApplyFieldValueFromEditorBatch = function (target, field, value)
    {
        var $target = $(target);
        var tagname = $target.prop('tagName');

        if ($target.length === 1)
        {
            if (tagname === 'INPUT')
            {
                if ($target.prop('type') === 'date')
                    $target.val(value === null || value === undefined ? '' : String(value).substring(0, 10));
                else
                {
                    var cleave = $target.data('cleaveInstance');
                    var raw = (value === null || value === undefined) ? '' : String(value);

                    if (cleave && typeof cleave.setRawValue === 'function')
                        cleave.setRawValue(raw);
                    else
                        $target.val(value);

                    $target.data('cleavevalue', value);

                    if (cleave && typeof cleave.getRawValue === 'function')
                        $target.data('originalvalue', cleave.getRawValue());
                    else
                        $target.data('originalvalue', $target.val());

                    $target.removeClass('ib-editor-dirty editorchanged');

                    try
                    {
                        if (typeof window.__ibUnmarkFieldDirty === 'function' && $target.length)
                            window.__ibUnmarkFieldDirty($target[0]);
                    }
                    catch (e) {}
                }

                $target.trigger('ibchanged');
            }
        }
        else if ($target.length > 1)
        {
            if (tagname === 'INPUT')
            {
                var normalized = value;

                if ($('input[name="' + field + '"][value="' + value + '"]').length === 0)
                {
                    if (value === 0)
                        normalized = 'false';
                    else if (value === 1)
                        normalized = 'true';
                    else if (value === null)
                        normalized = 'null';
                }

                $('input[name="' + field + '"][value="' + normalized + '"]').prop('checked', true).trigger('ibchanged');
            }
        }
    };

    window.__ibApplyBatchFieldValuesResponse = function (response)
    {
        if (! response || response.success !== true || ! response.values)
            return;

        Object.keys(response.values).forEach(function (fieldName)
        {
            var value = response.values[fieldName];
            var $targets = window.__ibFindEditorFieldTargetsBatch(fieldName);

            if (! $targets.length)
                return;

            $targets.each(function ()
            {
                window.__ibApplyFieldValueFromEditorBatch(this, fieldName, value);
            });
        });
    };

  /**
   * Direct batch read: one HTTP request for many fields.
   *
   * @param {string[]} fieldNames
   * @param {string|null} url
   * @returns {jqXHR}
   */
    window.getFieldsValuesFromEditorBatch = function (fieldNames, url)
    {
        fieldNames = (fieldNames || []).filter(function (name)
        {
            return !! String(name || '').trim();
        });

        if (! fieldNames.length)
            return $.Deferred().reject('getFieldsValuesFromEditorBatch: nessun campo').promise();

        url = url || window.__ibResolveEditorUrlForBatch(fieldNames);

        if (! url)
            return $.Deferred().reject('getFieldsValuesFromEditorBatch: updateeditorurl mancante').promise();

        return $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                'ib-editor-read-batch': true,
                fields: fieldNames,
                _method: 'PUT'
            }
        }).done(function (response)
        {
            window.__ibApplyBatchFieldValuesResponse(response);
        }).fail(function ()
        {
            if (typeof window.addDangerNotification === 'function')
                window.addDangerNotification('Impossibile leggere i valori dei campi in batch');
        });
    };

    window.flushFieldsRefreshBatch = function (url)
    {
        var state = window.__ibFieldRefreshBatch;
        var queue = state.queues[url];

        if (! queue || ! queue.size)
            return $.Deferred().resolve().promise();

        var fieldNames = Array.from(queue);

        queue.clear();
        clearTimeout(state.timers[url]);
        delete state.timers[url];

        if (state.inflight[url])
            return state.inflight[url];

        state.inflight[url] = window.getFieldsValuesFromEditorBatch(fieldNames, url).always(function ()
        {
            delete state.inflight[url];
        });

        return state.inflight[url];
    };

  /**
   * Queue field names for a debounced batch refresh on the given editor URL.
   *
   * @param {string|string[]} fieldNames
   * @param {{url?: string, debounceMs?: number}|string|null} options
   */
    window.requestFieldsRefreshBatch = function (fieldNames, options)
    {
        options = options || {};

        if (typeof options === 'string')
            options = { url: options };

        if (! Array.isArray(fieldNames))
            fieldNames = [fieldNames];

        fieldNames = fieldNames.filter(function (name)
        {
            return !! String(name || '').trim();
        });

        if (! fieldNames.length)
            return null;

        var url = options.url || window.__ibResolveEditorUrlForBatch(fieldNames);

        if (! url)
        {
            console.warn('[formfield batch] updateeditorurl mancante per:', fieldNames);

            return null;
        }

        var state = window.__ibFieldRefreshBatch;

        if (! state.queues[url])
            state.queues[url] = new Set();

        fieldNames.forEach(function (name)
        {
            state.queues[url].add(name);
        });

        clearTimeout(state.timers[url]);

        var debounceMs = options.debounceMs != null ? options.debounceMs : state.debounceMs;

        state.timers[url] = setTimeout(function ()
        {
            window.flushFieldsRefreshBatch(url);
        }, debounceMs);

        return url;
    };

    window.requestFieldRefreshBatch = function (fieldName, url)
    {
        return window.requestFieldsRefreshBatch([fieldName], { url: url });
    };

  /**
   * Parallel to refreshFetchingFieldsValues — uses the batch coordinator.
   */
    window.refreshFetchingFieldsValuesBatch = function (target)
    {
        var fetchFields = $(target).data('fetchfields');

        if (! fetchFields || ! fetchFields.length)
            return null;

        var url = $(target).data('updateeditorurl') || null;

        return window.requestFieldsRefreshBatch(fetchFields, { url: url });
    };

  /**
   * Parallel to dtRefreshFieldsList — resolves selectors to field names, then batches.
   *
   * @param {string[]} selectors
   */
    window.dtRefreshFieldsListBatch = function (selectors)
    {
        selectors = selectors || window.dtEditorRefreshingFieldList;

        if (! Array.isArray(selectors) || ! selectors.length)
            return null;

        var fieldNames = [];

        selectors.forEach(function (selector)
        {
            var $els = $(selector);

            if (! $els.length)
                return;

            $els.each(function ()
            {
                var name = $(this).attr('name') || $(this).data('field') || $(this).data('name');

                if (name)
                    fieldNames.push(String(name).replace(/\[\]$/, ''));
            });
        });

        if (! fieldNames.length)
            return null;

        return window.requestFieldsRefreshBatch(fieldNames);
    };
});

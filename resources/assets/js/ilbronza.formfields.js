require('./dropzone.min.js');
Dropzone.autoDiscover = false;


require('select2');
require('cleave.js');

jQuery(document).ready(function ($)
{
    const cleaveSelector = 'input.ibcleave';

    window.ibNormalizeCleaveRawValue = function (value)
    {
        if ((value === null) || (typeof value === 'undefined'))
            return '';

        value = String(value).trim().replace(/\s+/g, '');

        if (value === '')
            return '';

        if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(value))
            return value.replace(/\./g, '').replace(',', '.');

        if (/^-?\d+,\d+$/.test(value))
            return value.replace(',', '.');

        return value;
    }

    window.ibForceCleaveRawValueDecimals = function (value)
    {
        value = window.ibNormalizeCleaveRawValue(value);

        if (value === '')
            return '';

        let number = Number(value);

        if (Number.isNaN(number))
            return value;

        return number.toFixed(2);
    }

    window.ibCleaveIt = function (target, raw)
    {
        if (typeof window.Cleave === 'undefined')
            return null;

        let input = target;
        let $input = $(input);

        if (! $input.is(cleaveSelector))
            return null;

        let cleave = $input.data('cleaveInstance');

        if (cleave)
            return cleave;

        raw = (typeof raw === 'undefined') ? window.ibForceCleaveRawValueDecimals(input.value) : window.ibForceCleaveRawValueDecimals(raw);
        input.value = '';

        cleave = new window.Cleave(input, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            numeralDecimalScale: 2,
            delimiter: '.',
        });

        $input.data('cleaveInstance', cleave);
        cleave.setRawValue(raw);
        $input.data('originalvalue', cleave.getRawValue());

        return cleave;
    }

    window.ibRefreshCleave = function (target)
    {
        let $input = $(target);

        if (! $input.is(cleaveSelector))
            return null;

        let raw = window.ibForceCleaveRawValueDecimals($input[0].value);
        let cleave = $input.data('cleaveInstance') || window.ibCleaveIt(target, raw);

        if (cleave && (typeof cleave.setRawValue === 'function'))
            cleave.setRawValue(raw);

        if (cleave && (typeof cleave.getRawValue === 'function'))
            $input.data('originalvalue', cleave.getRawValue());
        else
            $input.data('originalvalue', $input[0].value);

        return cleave;
    }

    window.activateAllCleave = function (root)
    {
        let $root = root ? $(root) : $(document);
        let $inputs = $root.find(cleaveSelector);

        if ($root.is(cleaveSelector))
            $inputs = $inputs.add($root);

        $inputs.each(function ()
        {
            window.ibCleaveIt(this);
        });
    }

    if (! $.fn.ibOriginalVal)
    {
        $.fn.ibOriginalVal = $.fn.val;

        $.fn.val = function (value)
        {
            if ((arguments.length === 0) && this.length && this.is(cleaveSelector))
            {
                const cleave = $(this[0]).data('cleaveInstance');

                if (cleave && (typeof cleave.getRawValue === 'function'))
                    return window.ibForceCleaveRawValueDecimals(cleave.getRawValue());
            }

            return $.fn.ibOriginalVal.apply(this, arguments);
        };
    }

    $(document).on('ibchanged ib:cleave:refresh', cleaveSelector, function ()
    {
        window.ibRefreshCleave(this);
    });

    $('body').on('focus', cleaveSelector, function ()
    {
        window.ibCleaveIt(this);
    });

    $('body').on('blur', cleaveSelector, function ()
    {
        window.ibRefreshCleave(this);
    });

    $('body').on('keydown', cleaveSelector, function (e)
    {
        if (e.key !== '.')
            return;

        e.preventDefault();

        if (typeof this.setRangeText === 'function')
        {
            this.setRangeText(',', this.selectionStart, this.selectionEnd, 'end');
            this.dispatchEvent(new Event('input', { bubbles: true }));

            return;
        }

        document.execCommand('insertText', false, ',');
    });

    window.activateAllCleave();

    $(window).on('load', function ()
    {
        window.activateAllCleave();
    });

    window.changeCheckboxBoolean = function (target)
    {
        let inputName = $(target).data('name');

        let value = $(target).prop('checked');

        $('input[name="' + inputName + '"][value="' + value + '"]').prop('checked', true).change();
    }

    // window.parseMoneyField = function(target)
    // {
    // 	let step = $(target).attr('step');

    // 	let split = step.toString().split(".");

    // 	if(typeof split[1] == 'undefined')
    // 		return ;

    // 	let decimals = split[1].length || 0;

    // 	if(decimals > 0)
    // 		$(target).val(parseFloat($(target).val()).toFixed(decimals));
    // }

    // window.parseMoneyFields = function()
    // {
    // 	$('.money input').each(function()
    // 	{
    // 		window.parseMoneyField(this);

    // 	});
    // }

    // $('body').on('change', '.money input', function()
    // {
    // 	window.parseMoneyField(this);

    // });

    // window.parseMoneyFields();


    /**
     * @deprecated Sospeso — usare requestFieldRefreshBatch / getFieldsValuesFromEditorBatch.
     */
    window.__ibLegacyGetFieldValueFromEditor = function (target)
    {
        let url = $(target).data('updateeditorurl');
        let field = $(target).attr('name');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                'ib-editor-read': true,
                field: field,
                _method: 'PUT'
            },
            success: function (response)
            {
                if (response.field == null)
                {
                    console.log('Ricorda di disabilitare il reader dei campi quando non esistenti');

                    return;
                }

                let value = response.value;
                let tagname = $(target).prop("tagName");

                if (target.length == 1)
                {
                    if (tagname == 'INPUT')
                    {
                        if ($(target).prop('type') == 'date')
                            $(target).val(value.substring(0, 10));
                        else
                        {
                            let $t = $(target);
                            let cleave = $t.data('cleaveInstance');
                            let raw = (value === null || value === undefined) ? '' : String(value);

                            if (cleave && typeof cleave.setRawValue === 'function')
                                cleave.setRawValue(raw);
                            else
                                $t.val(value);

                            $t.data('cleavevalue', value);

                            if (cleave && typeof cleave.getRawValue === 'function')
                                $t.data('originalvalue', cleave.getRawValue());
                            else
                                $t.data('originalvalue', $t.val());

                            /* Nessun trigger input/change: evitano il salvataggio automatico sugli editor. */
                            $t.removeClass('ib-editor-dirty editorchanged');
                            try {
                                if (typeof window.__ibUnmarkFieldDirty === 'function' && $t.length)
                                    window.__ibUnmarkFieldDirty($t[0]);
                            } catch (e) {}
                        }

                        $(target).trigger('ibchanged');
                    }

                    // window.parseMoneyField(target);

                    else ('alewrt qua da impostare (tipo un select)');
                } else
                {
                    if (tagname == 'INPUT')
                    {
                        if ($("input[name=" + field + "][value=" + value + "]").length == 0)
                        {
                            if (value == 0)
                                value = "false";
                            else if (value == 1)
                                value = "true";
                            else if (value == null)
                                value = "null";
                        }

                        $("input[name=" + field + "][value=" + value + "]").prop('checked', true);
                        $("input[name=" + field + "][value=" + value + "]").trigger('ibchanged');
                    } else
                        alert('qua sono con il field multiplo');
                }


            },
            error: function (response)
            {
                window.addDangerNotification('Impossibile leggere il valore di ' + field);
            }
        });
    }

    window.getFieldValueFromEditor = function (target)
    {
        if (typeof window.requestFieldRefreshBatch !== 'function')
            return window.__ibLegacyGetFieldValueFromEditor(target);

        var $target = $(target).first();
        var field = $target.attr('name') || $target.data('field') || $target.data('name');

        if (! field)
            return null;

        field = String(field).replace(/\[\]$/, '');

        return window.requestFieldRefreshBatch(field, $target.data('updateeditorurl') || null);
    }

    /**
     * @deprecated Sospeso — usare refreshFetchingFieldsValuesBatch.
     */
    window.__ibLegacyRefreshFetchingFieldsValues = function (target)
    {
        var fetchFields = $(target).data('fetchfields');

        if (! fetchFields || ! fetchFields.length)
            return null;

        fetchFields.forEach(function (item)
        {
            window.__ibLegacyGetFieldValueFromEditor($('*[name="' + item + '"]'));
        });
    }

    window.refreshFetchingFieldsValues = function (target)
    {
        if (typeof window.refreshFetchingFieldsValuesBatch === 'function')
            return window.refreshFetchingFieldsValuesBatch(target);

        return window.__ibLegacyRefreshFetchingFieldsValues(target);
    }

    window.ibFindProblemsNode = function (target)
    {
        return $(target).siblings('.ib-field-problems').first();
    }

    window.ibPrintFieldProblems = function (problems, target, problemsView)
    {
        window.ibFindProblemsNode(target).remove();

        if (typeof problemsView == 'string')
            $(target).parent().append(problemsView);

        if (problems instanceof Array)
            problems.forEach(function (message)
            {
                window.addDangerNotification(message);
            });
    }

    window.sendFieldToEditor = function (field, value, url, e)
    {
        // jQuery drops empty arrays during x-www-form-urlencoded serialization.
        // If a multi-select has no selection we still want to send `value[]`.
        // By sending a single empty string, Laravel's ConvertEmptyStringsToNull middleware will turn it into `null`.
        // This allows the backend to detect an explicit "empty" value rather than a missing parameter.
        if (Array.isArray(value) && value.length === 0)
            value = [''];

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                'ib-editor': true,
                field: field,
                value: value,
                _method: 'PUT'
            },
            success: function (response)
            {
                window.refreshFetchingFieldsValues(e.target);

                if (response.ibaction == 'reloadAllTables')
                    return window.__reloadAllTables(params);

                if (response.success != true)
                    return this.error(response);

                window.ibPrintFieldProblems(response.problems, e.target, response.problemsView);

                let message = field + ' modificato con successo';

                if (response.message)
                    message = response.message;

                window.addSuccessNotification(message);
            },
            error: function ()
            {
                window.addDangerNotification('Problemi con il salvataggio di ' + field);
            }
        });
    }

    window.setValueAsHtmlClass = function (target, value)
    {
        value = value.replace("+", "plus");
        value = value.toLowerCase();

        $(target).removeClass(function (index, className)
        {
            return (className.match(/(^|\s)valclass-\S+/g) || []).join(' ');
        });

        $(target).addClass('valclass-' + value);
    }

    $('body').on('change', '*[data-valueashtmlclass="true"]', function (e)
    {
        window.setValueAsHtmlClass(this, $(this).val());
    });

    window.__ibIsUpdateEditorDateInput = function (target)
    {
        const type = String($(target).attr('type') || '').toLowerCase();

        return type === 'date' || type === 'datetime-local' || type === 'time';
    };

    window.__ibCancelUpdateEditorDateLeaveAction = function (target)
    {
        $(target).removeData('ibUpdateEditorDateLeaveToken');
    };

    window.__ibScheduleUpdateEditorDateLeaveAction = function (target, fn, delayMs)
    {
        const $el = $(target);
        const token = ($el.data('ibUpdateEditorDateLeaveSeq') || 0) + 1;

        $el.data('ibUpdateEditorDateLeaveSeq', token);
        $el.data('ibUpdateEditorDateLeaveToken', token);

        setTimeout(function ()
        {
            if ($el.data('ibUpdateEditorDateLeaveToken') !== token)
                return;

            if (document.activeElement === target)
                return;

            if (typeof fn === 'function')
                fn(target);
        }, delayMs || 150);
    };

    window.__ibSendUpdateEditorField = function (target, e)
    {
        const value = $(target).val();
        let field = $(target).attr('name');

        field = field.replace('[]', '');

        const url = $(target).data('updateeditorurl');

        window.sendFieldToEditor(field, value, url, e);
    };

    $('body').on('focus', '.update-editor-field', function ()
    {
        window.__ibCancelUpdateEditorDateLeaveAction(this);
    });

    $('body').on('change', '.update-editor-field', function (e)
    {
        if (window.__ibIsUpdateEditorDateInput(e.target))
            return;

        window.__ibSendUpdateEditorField(e.target, e);
    });

    $('body').on('blur', '.update-editor-field', function (e)
    {
        if (! window.__ibIsUpdateEditorDateInput(e.target))
            return;

        const target = e.target;

        window.__ibScheduleUpdateEditorDateLeaveAction(target, function (el)
        {
            window.__ibSendUpdateEditorField(el, e);
        });
    });
    // $('.select2').select2();

    $('.select2').each(function ()
    {
        let options = {};

        if ($(this).data('placeholder'))
            options.placeholder = $(this).data('placeholder');

        if ($(this).data('allowclear'))
            options.allowClear = ($(this).data('allowclear')) ? true : false;

        $(this).select2(options);
    });

    $(document).on('select2:open', (e) =>
    {
        var selectId = e.target.id

        $(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (key, value)
        {
            value.focus()
        })
    });

    $('body').on('click', 'span.ib-dropzone-delete', function (e)
    {
        if (!confirm('Sei sicuro?'))
            return false;

        let url = $(this).attr('href');

        let that = this;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE'
            },
            success: function (response)
            {
                $(that).parents('li').remove();
            },
            error: function (response, message)
            {
                alert(message);
            }
        });

    });

});

require('./ilbronza.formfields.batch.js');

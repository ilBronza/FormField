require('./dropzone.min.js');
Dropzone.autoDiscover = false;


require('select2');

jQuery(document).ready(function($)
{
	window.changeCheckboxBoolean = function(target)
	{
		let inputName = $(target).data('name');

		let value = $(target).prop('checked');

		$('input[name="' + inputName + '"][value="' + value + '"]').prop('checked', true).change();
	}

	window.parseMoneyField = function(target)
	{
		let step = $(target).attr('step');

		let split = step.toString().split(".");

		if(typeof split[1] == 'undefined')
			return ;

		let decimals = split[1].length || 0;

		if(decimals > 0)
			$(target).val(parseFloat($(target).val()).toFixed(decimals));
	}

	window.parseMoneyFields = function()
	{
		$('.money input').each(function()
		{
			window.parseMoneyField(this);

		});
	}

	$('body').on('change', '.money input', function()
	{
		window.parseMoneyField(this);

	});

	window.parseMoneyFields();



	window.getFieldValueFromEditor = function(target)
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
			success: function(response)
			{
				let value = response.value;
				let tagname = $(target).prop("tagName");

				if(target.length == 1)
				{
					if(tagname == 'INPUT')
					{
						if($(target).prop('type') == 'date')
							$(target).val(value.substring(0, 10));
						else
							$(target).val(value);

						$(target).trigger('ibchanged');
					}

					window.parseMoneyField(target);

					else('alewrt qua da impostare (tipo un select)');
				}
				else
				{
					if(tagname == 'INPUT')
					{
						if($("input[name=" + field + "][value=" + value + "]").length == 0)
						{
							if(value == 0)
								value = "false";
							else if(value == 1)
								value = "true";
							else if(value == null)
								value = "null";
						}

						$("input[name=" + field + "][value=" + value + "]").prop('checked', true);
						$("input[name=" + field + "][value=" + value + "]").trigger('ibchanged');
					}
					else
						alert('qua sono con il field multiplo');
				}


			},
			error: function(response)
			{
				window.addDangerNotification('Impossibile leggere il valore di ' + field);
			}
		});
	}

	window.refreshFetchingFieldsValues = function (target)
	{
		if(! $(target).data('fetchfields').length)
			return null;

		$(target).data('fetchfields').forEach(function(item)
		{
			let target = $('*[name="' + item + '"]');

			window.getFieldValueFromEditor(target);
		});

	}


	window.sendFieldToEditor = function(field, value, url, e)
	{
	    $.ajax({
	        url: url,
	        type: 'POST',
	        data: {
	            'ib-editor': true,
	            field: field,
	            value: value,
	            _method: 'PUT'
	        },
	        success: function(response)
	        {
				window.refreshFetchingFieldsValues(e.target);

				if(response.ibaction == 'reloadAllTables')
					return window.__reloadAllTables(params);

				if(response.success != true)
					return this.error(response);

				let message = field  + ' modificato con successo';

				if(response.message)
					message = response.message;

				window.addSuccessNotification(message);
	        },
	        error: function()
	        {
	            window.addSuccessNotification('Problemi con il salvataggio di ' + field);
	        }
	    });
	}

	window.setValueAsHtmlClass = function(target, value)
	{
		value = value.replace("+", "plus");
		value = value.toLowerCase();

		$(target).removeClass (function (index, className) {
			return (className.match (/(^|\s)valclass-\S+/g) || []).join(' ');
		});

		$(target).addClass('valclass-' + value);
	}

	$('body').on('change', '*[data-valueashtmlclass="true"]', function(e)
	{
		window.setValueAsHtmlClass(this, $(this).val());
	});

	$('body').on('change', '.update-editor-field', function(e)
    {
        let value = $(e.target).val();
        let field = $(e.target).attr('name');

		field = field.replace("[]", "");

        let url = $(e.target).data('updateeditorurl');

        window.sendFieldToEditor(field, value, url, e);
    });
	// $('.select2').select2();

	$('.select2').each(function()
	{
		let options = {};

		if($(this).data('placeholder'))
			options.placeholder = $(this).data('placeholder');

		if($(this).data('allowclear'))
			options.allowClear = ($(this).data('allowclear'))? true : false;

		$(this).select2(options);
	});

	$(document).on('select2:open', (e) => {
		var selectId = e.target.id

		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (key, value)
		{
		    value.focus()
		})
	});

	$('body').on('click', 'span.ib-dropzone-delete', function(e)
	{
		let url = $(this).attr('href');

		let that = this;

		$.ajax({
			url: url,
			type: 'POST',
			data: {
				_method: 'DELETE'
			},
			success: function(response)
			{
				$(that).parents('li').remove();
			},
			error: function(response, message)
			{
				alert(message);
			}
		});

	});

});

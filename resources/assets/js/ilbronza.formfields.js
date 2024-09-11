require('./dropzone.min.js');
Dropzone.autoDiscover = false;


require('select2');


jQuery(document).ready(function($)
{
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
						$(target).val(value);

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

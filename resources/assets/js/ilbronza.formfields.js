require('./dropzone.min.js');
Dropzone.autoDiscover = false;


require('select2');


jQuery(document).ready(function($)
{
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

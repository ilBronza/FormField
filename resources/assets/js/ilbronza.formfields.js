require('./dropzone.min.js');
Dropzone.autoDiscover = false;


require('select2');

jQuery(document).ready(function($)
{
	$('.select2').select2();

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

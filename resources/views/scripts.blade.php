<style type="text/css">
.ql-container
{
	min-height: 120px;
}
</style>


<script type="text/javascript">

	jQuery(document).ready(function($)
	{
		$('.selectwithmanualinput').change(function()
		{
			if($(this).val())
			{
				let name = $(this).attr('name');
				let input = $(this).siblings('input')
				name = name.replaceAll('_disabled', '');

				input.attr('name', name + '_disabled');
				$(this).attr('name', name);

				$(this).siblings('input').val('');
			}
		});

		$('.ibselectmanualinput').change(function()
		{
			let select = $(this).siblings('select')
			let name = select.attr('name');

			name = name.replaceAll('_disabled', '');

			if($(this).val())
			{
				select.attr('name', name + '_disabled');
				$(this).attr('name', name);

				select.find('option[value=""]').prop('selected', true);
			}
			else
			{
				$(this).attr('name', name + '_disabled');
				select.attr('name', name);
			}
		});
	});
</script>


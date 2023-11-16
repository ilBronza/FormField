			@if($suffix = $field->getSuffix())
				<div class="ib-suffix"><div>{{ $suffix }}</div></div>
			@endif

		@if($field->getPrefix()||$field->getSuffix())
		</div>
		@endif


		@error($field->getFormOldName()) 
		<script type="text/javascript">
			window.addDangerNotification('{{ $message }}');
		</script>
		<div class="uk-text-danger">{{ $message }}</div>
		@enderror
    </div>
</div>


@if($field->hasOpener())
<script type="text/javascript">
jQuery(document).ready(function($)
{
	$('#{{ $field->getId() }}').on('change', function()
	{
		@if($name = $field->getOpenerTargetName())

			$('*[name="{{ $name }}"]').parents('.fieldcontainer').fadeIn();

			@if($field->getOpenerRequiredAttribute())
			$('*[name="{{ $name }}"]').each(function(index, target)
			{
				$(target).prop('required', true);
			})
			@endif;
		
		@elseif($id = $field->getOpenerId())

		@elseif($containerId = $field->getOpenerContainerId())

		@endif
	})
})
</script>
@endif

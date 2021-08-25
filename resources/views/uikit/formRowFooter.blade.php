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

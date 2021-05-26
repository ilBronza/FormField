
		@error($field->getFormOldName()) 
		<script type="text/javascript">
			window.addDangerNotification('{{ $message }}');
		</script>
		<div class="uk-text-danger">{{ $message }}</div>
		@enderror
    </div>
</div>

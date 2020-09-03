<div
	class="{{ $field->getHtmlRowClassesString() }} {{ $field->getFieldTypeClass() }} {{ ($field->isClosed())? 'uk-hidden' : '' }} uk-clearfix"

	@if($id = $field->getContainerId())
		id="{{ $id }}"
	@endif

	>
	@if($label = $field->getLabel())
	<label class="uk-form-label">
		{{ $label }}
		@if($tooltip = $field->getTooltip())
			<span uk-tooltip='title:{{ $tooltip }}' uk-icon='question'></span>
		@endif
	</label>
	@endif

	<div class="uk-form-controls @if(! $label) uk-margin-remove-left @endif">
		@yield('field' . $field->getName())

		@error($field->getFormOldName()) 
		<div class="uk-text-danger">{{ $message }}</div>
		@enderror
    </div>
</div>

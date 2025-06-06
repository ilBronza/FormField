<div
	class="{{ $field->getHtmlClassesString() }} {{ $field->getFieldTypeClass() }} {{ ($field->isClosed())? 'uk-hidden' : '' }} uk-clearfix"

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

	<div class="uk-form-controls ibfieldcontent @if(! $label) uk-margin-remove-left @endif">
		@yield('field' . $field->getName())

		@if(isset($errors))

			@error($field->getFormOldName()) 
			<div class="uk-text-danger">{{ $message }}</div>
			@enderror

		@endif
    </div>
</div>

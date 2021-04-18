<div
	class="{{ $field->getHtmlRowClassesString() }} {{ $field->getFieldTypeClass() }} {{ ($field->isClosed())? 'uk-hidden' : '' }} uk-clearfix container{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"

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

	<div class="uk-form-controls uk-width-medium @if(! $label) uk-margin-remove-left @endif">

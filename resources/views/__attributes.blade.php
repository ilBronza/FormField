@if(isset($fieldIndex))
	@if($field->isMultiple())
	name="{{ $field->getName() }}[{{ $fieldIndex }}][]"
	@else
	name="{{ $field->getName() }}[{{ $fieldIndex }}]"
	@endif

	id="{{ $overrideId ?? ($field->getId() . '-' . $fieldIndex) }}"
@else
	@if($field->isMultiple())
	name="{{ $field->getName() }}[]"
	@else
	name="{{ $field->getName() }}"
	@endif

	id="{{ $overrideId ?? $field->getId() }}"
@endif

@if($field->isDisabled())
disabled
@endif

@if($field->isRequired())
required
@endif

@if($field->isReadOnly())

readonly
data-disabledtext="{{ $field->getReadOnlyText() }}"

@endif

@if($field->mustShowPlaceholder())
placeholder="{{ $field->getPlaceholder() }}"
@endif
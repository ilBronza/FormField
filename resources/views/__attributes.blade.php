@if(isset($fieldIndex))
	@if($field->isMultiple())
	name="{{ $field->getName() }}[{{ $fieldIndex }}][]"
	data-name="{{ $field->getName() }}[{{ $fieldIndex }}][]"
	@else
	name="{{ $field->getName() }}[{{ $fieldIndex }}]"
	data-name="{{ $field->getName() }}[{{ $fieldIndex }}]"
	@endif

	@if(isset($overrideId)||($field->getId()))
	id="{{ $overrideId ?? ($field->getId() . '-' . $fieldIndex) }}"
	@endif
@else
	@if($field->isMultiple())
	name="{{ $field->getName() }}[]"
	data-name="{{ $field->getName() }}[]"
	@else
	name="{{ $field->getName() }}"
	data-name="{{ $field->getName() }}"
	@endif

	@if(isset($overrideId)||($field->getId()))
	id="{{ $overrideId ?? $field->getId() }}"
	@endif
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

@if(! $field->hasAutocomplete())
autocomplete="off"
@endif

@if($field->hasFetcher())
	@include('formfield::__fetcherAttributes')
@endif

class="selectwithmanualinput {{ $field->getHtmlClassesString() }} {{ $field->getFetcherFieldClasses() }}"
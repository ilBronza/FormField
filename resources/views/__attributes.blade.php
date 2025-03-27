@if($field->isRepeatable()&&($_fieldIndex = $field->getRepeatableFieldKey()))

	name="{{ $field->getName() }}[{{ $_fieldIndex }}]"
	data-name="{{ $field->getName() }}[{{ $_fieldIndex }}]"
	id="{{ $overrideId ?? ($field->getId() . '-' . $_fieldIndex) }}"

@elseif(isset($fieldIndex))
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


data-flatname="{{ $field->getName() }}"

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

@if($field->hasFieldsToFetch())
	data-fetchfields='{!! json_encode($field->getFieldsToFetch()) !!}'
@endif

@if($field->hasFetcher())
	@include('formfield::__fetcherAttributes')
@endif

{{-- @if($field->hasUpdateEditor()) --}}
	data-updateeditorurl="{{ $field->getUpdateEditorUrl() }}"
{{-- @endif --}}

class="selectwithmanualinput {{ $field->getInputSizeClass() }} {{ $field->getHtmlClassesString() }} {{ $field->getFetcherFieldClasses() }} @if($field->hasUpdateEditor())
	update-editor-field
@endif"
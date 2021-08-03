@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	type="text"
	value="{{ $field->getFormOldValue() }}"
	step="{{ $field->getStep() }}"
	/>

@include('formfield::uikit.formRowFooter')
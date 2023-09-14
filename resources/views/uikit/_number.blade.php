@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	type="number"
	value="{{ $field->getNumberFormOldValue() }}"
	step="{{ $field->getStep() }}"
	/>

@include('formfield::uikit.formRowFooter')
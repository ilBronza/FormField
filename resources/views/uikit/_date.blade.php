@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	type="date"
	value="{{ $field->getFormOldValue() }}"

	/>

@include('formfield::uikit.formRowFooter')
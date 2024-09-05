@include('formfield::uikit.formRowHeader')

<input
		@include('formfield::__data')
		@include('formfield::__attributes')
				disabled
		type="color"
		value="{{ $field->getFormOldValue() }}"

/>

@include('formfield::uikit.formRowFooter')
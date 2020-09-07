@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	class="uk-input" 
	type="text"
	value="{{ $field->getFormOldValue() }}"

	/>

@include('formfield::uikit.formRowFooter')
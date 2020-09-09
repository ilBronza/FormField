@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	class="uk-input" 
	type="email"
	value="{{ $field->getFormOldValue() }}"

	/>

@include('formfield::uikit.formRowFooter')
@include('formfield::uikit.formRowHeader')

<div class="uk-inline">
	<span class="uk-form-icon" uk-icon="icon: calendar"></span>

	<input
		@include('formfield::__data')
		@include('formfield::__attributes')

		type="text"
		value="{{ $field->getFormOldValue() }}"
		/>

</div>

@include('formfield::uikit.formRowFooter')
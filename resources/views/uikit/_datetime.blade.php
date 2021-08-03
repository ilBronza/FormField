@include('formfield::uikit.formRowHeader')

<div class="uk-inline">
	<input
		@include('formfield::__data')
		@include('formfield::__attributes')

		step="{{ $field->getStep() }}"
		type="datetime-local"
		value="{{ $field->getFormOldValue() }}"
		/>

</div>

@include('formfield::uikit.formRowFooter')
<div uk-form-custom="target: true">
	<input
		@include('formfield::__data')
		@include('formfield::__attributes')

		@if($field->isMultiple())
		multiple
		@endif

		type="file"

		/>
	<input class="uk-input uk-form-width-medium" type="text" placeholder="{{ $field->getPlaceholder() }}" disabled>
</div>

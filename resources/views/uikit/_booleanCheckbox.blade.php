@include('formfield::uikit.formRowHeader')

	@php
		$booleanValue = $field->getFormOldValue();
	@endphp

<input
		data-name="{{ $field->getName() }}"

		class="uk-checkbox"
		type="checkbox"

		@if(($booleanValue == "true")||($booleanValue === true)))
			checked
		@endif

		@if($field->isReadOnly()) disabled @endif

		onclick="window.changeCheckboxBoolean(this);"


/>

	@foreach($field->getPossibleValuesArray() as $index => $value)

	<span class="uk-text-nowrap">
		<input
			@include('formfield::__data')
			@include('formfield::__attributes', ['overrideId' => $field->getId() . '-' . $index])

					hidden

			type="radio"
			value="{{ $value }}"

			@if($field->isReadOnly()) disabled @endif

			@if($value == $booleanValue)
			checked
			@endif
			/>
	</span>
	@endforeach

@include('formfield::uikit.formRowFooter')
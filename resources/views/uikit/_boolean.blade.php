@include('formfield::uikit.formRowHeader')

	@php
		$booleanValue = $field->getFormOldValue();
	@endphp

	@foreach($field->getPossibleValuesArray() as $index => $value)

	<span class="uk-text-nowrap">
		<input
			@include('formfield::__data')
			@include('formfield::__attributes', ['overrideId' => $field->getId() . '-' . $index])

			class="uk-radio" 
			type="radio"
			value="{{ $value }}"

			@if($field->isReadOnly()) disabled @endif

			@if($value == $booleanValue)
			checked
			@endif
			/>
		<label for="{{ $field->getId() }}-{{ $index }}">
			{{ __('formfield::values.booleanLabel' . $index) }}
		</label>
	</span>

	@endforeach

@include('formfield::uikit.formRowFooter')
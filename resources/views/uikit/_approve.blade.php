@include('formfield::uikit.formRowHeader')

	@php
		$value = $field->getFormOldValue();
	@endphp

	@if($value instanceof Carbon\Carbon)

	{{ $value->format('d m Y H:i:l') }}

	@else

	@foreach($field->getPossibleValuesArray() as $index => $value)

	<span class="uk-text-nowrap">
		<input
			@include('formfield::__data')
			@include('formfield::__attributes', ['overrideId' => $field->getId() . '-' . $index])

			class="uk-radio" 
			type="radio"
			value="{{ $value }}"

			@if($value == $value)
			checked
			@endif
			/>
		<label for="{{ $field->getId() }}-{{ $index }}">
			{{ __('fields.booleanLabel' . $index) }}
		</label>
	</span>

	@endforeach

	@endif

@include('formfield::uikit.formRowFooter')
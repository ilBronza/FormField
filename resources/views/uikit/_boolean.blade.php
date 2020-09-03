@extends('formfield::uikit.formRow')

@section('field' . $field->getName())

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

			@if($value == $booleanValue)
			checked
			@endif
			/>
		<label for="{{ $field->getId() }}-{{ $index }}">
			{{ __('formfields.booleanLabel' . $index) }}
		</label>
	</span>

	@endforeach

@endsection
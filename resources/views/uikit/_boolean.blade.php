@include('formfield::uikit.formRowHeader')


<h1>SISTEMARE IL NULLABLE - SE SALVI UN VALORE E POI VUI ANNULLARE NON ANNULLA PIU'</h1>

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
			{{ __('fields.booleanLabel' . $index) }}
		</label>
	</span>

	@endforeach

@include('formfield::uikit.formRowFooter')
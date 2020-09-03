@extends('formfield::uikit.formRow')

@section('field' . $field->getName())

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	class="uk-input" 
	type="number"
	value="{{ $field->getFormOldValue() }}"
	step="{{ $field->getStep() }}"
	/>

@endsection
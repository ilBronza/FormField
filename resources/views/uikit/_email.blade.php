@extends('formfield::uikit.formRow')

@section('field' . $field->getName())

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	class="uk-input" 
	type="email"
	value="{{ $field->getFormOldValue() }}"

	/>

@endsection
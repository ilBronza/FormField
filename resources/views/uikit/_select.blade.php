@extends('formfield::uikit.formRow')

@section('field' . $field->getName())

	@php
		$oldSelected = $field->getFormOldSelected();
	@endphp

	@if(! is_array($oldSelected))

	<h1>{{ class_basename($oldSelected) }}</h1>
	@else

	<select
		@include('formfield::__data')
		@include('formfield::__attributes')

		@if($field->isMultiple())
		multiple
		@endif
		
		class="uk-select"
	>
		<option 
			@if(empty($oldSelected[0])||(is_null($oldSelected[0])))
			selected disabled
			@endif
			value=""
			>{{ __('fields.selectFromOptions', ['fieldName' => __('fields.' . $field->getName())]) }}</option>

		@foreach($field->getPossibleValuesArray() as $index => $value)
			<option value="{{ $index }}"
				@if(in_array($index, $oldSelected))
				selected
				@endif

			>
				{{ $value }}
			</option>
		@endforeach
	</select>

	@endif

@endsection
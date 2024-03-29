@include('formfield::uikit.formRowHeader')

	@php
		$oldSelected = $field->getFormOldSelected();
	@endphp

	@foreach($field->getPossibleValuesArray() as $index => $value)

	<span class="uk-margin-small {{ $field->getStackingClass() }}">
		<input
			@include('formfield::__data')
			@include('formfield::__attributes', ['overrideId' => $field->getId() . '-' . $index])

			class="uk-checkbox" 
			type="checkbox"
			value="{{ $value }}"

			@if(in_array($value, $oldSelected))
			checked
			@endif
			/>
		<label for="{{ $field->getId() }}-{{ $index }}">
			{!! $field->getLabelByIndex($index) !!}
		</label>
	</span>

	@endforeach



{{-- 
	@if(! is_array($oldSelected))

	<h1>{{ class_basename($oldSelected) }}</h1>
	@else

	<select
		@include('formfield::__data')
		@include('formfield::__attributes')

		@if($field->isMultiple())
		multiple
		@endif

		@if($field->isSelect2())
		data-placeholder="{{ __('fields.selectFromOptions', ['fieldName' => __('fields.' . $field->getName())]) }}"
		data-allowClear="{{ ($field->isRequired())? 'false' : 'true' }}"
		@endif
		
		@if($field->hasManualInput())
		data-tag="true"
		@endif
	>
		@if($field->isSelect2())
		<option></option>
		@else
		<option 
			@if(empty($oldSelected[0])||(is_null($oldSelected[0])))
			selected disabled
			@endif
			value=""
			>{{ __('fields.selectFromOptions', ['fieldName' => __('fields.' . $field->getName())]) }}</option>
		@endif

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

	@endif --}}

@include('formfield::uikit.formRowFooter')
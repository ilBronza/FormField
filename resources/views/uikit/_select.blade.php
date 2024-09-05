@include('formfield::uikit.formRowHeader')

	@php
		$oldSelected = $field->getFormOldSelected();
	@endphp

	@if(is_array($oldSelected))

	<select
		@include('formfield::__data')
		@include('formfield::__attributes')

		@if($field->isMultiple())
		multiple
		@endif

		@if($field->isSelect2())
		data-placeholder="{{ __('formfield::values.selectFromOptions', ['fieldName' => $field->getLabel()]) }}"
		data-allowClear="{{ ($field->isRequired())? 'false' : 'true' }}"
		data-allowclear="{{ ($field->isRequired())? 'false' : 'true' }}"
		data-allow-clear="{{ ($field->isRequired())? 'false' : 'true' }}"
		@endif
		
		@if($field->hasManualInput())
		data-tag="true"
		@endif
	>
		@if(! $field->isReadOnly())
			@if((($field->isSelect2())||($field->hasManualInput())))
			<option value="">{{ __('fields.selectFromOptions', ['fieldName' => __('fields.' . $field->getName())]) }}</option>
			@else
			<option
				@if(empty($oldSelected[0])||(is_null($oldSelected[0])))
				selected disabled
				@endif
				value=""
				>{{ __('fields.selectFromOptions', ['fieldName' => __('fields.' . $field->getName())]) }}</option>
			@endif
		@endif

		@foreach($field->getPossibleValuesArray() as $index => $value)
				@if((! $field->isReadOnly())||((in_array($index, $oldSelected))))
			<option value="{{ $index }}"
				@if(in_array($index, $oldSelected))
				selected
				@endif

			>
				{{ $value }}
			</option>
				@endif
		@endforeach
			
			@if((! $field->isSelect2())&&($field->hasManualInput()))
			<option class="ibuseselectmanualinput">Manual input</option>
			@endif
	</select>

		@if((! $field->isSelect2())&&($field->hasManualInput()))
		<input class="ibselectmanualinput uk-input" type="text" name="" />
		@endif

	@endif

@include('formfield::uikit.formRowFooter')
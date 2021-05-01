@include('formfield::uikit.formRowHeader')

<div
	uk-grid class="uk-grid-collapse"
	>
	<div class="uk-flex uk-flex-middle uk-flex-center uk-width-auto">
		<i class="fas fa-arrows-alt uk-margin-small-bottom uk-padding-small uk-invisible"></i>
	</div>
	<div uk-grid class="uk-child-width-auto uk-form-stacked uk-width-expand uk-flex uk-flex-middle">
		@foreach($field->innerFields as $innerField)
		<div style="width: {{ floor(100/count($field->innerFields)) }}%;">
			<label>{{ __('fields.' . $innerField->subName) }}</label>			
		</div>
		@endforeach			
	</div>
	<div class="uk-flex uk-flex-middle uk-flex-center">
		<i class="fas fa-plus uk-margin-small-bottom uk-padding-small addjsonfake" data-id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}" ></i>
	</div>
	
</div>

<div class="valuescontainer @if($field->hasPosition()) ib-sortable @endif">
	@foreach($field->getFormOldValue() as $key => $value)

		<div
			id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}{{ $loop->index + 1 }}"
			data-position="{{ $position = ($loop->index + 1) }}"
			class="jsonvalues uk-grid-collapse"
			uk-grid
			>
			<div class="uk-flex uk-flex-middle uk-flex-center uk-width-auto">
				<i class="fas fa-arrows-alt uk-margin-small-bottom uk-padding-small"></i>
			</div>
			<div uk-grid class="uk-child-width-1-2 uk-form-stacked uk-width-expand">
				@foreach($field->getInnerFieldsByKeyValue($key, $value) as $innerField)
				<div style="width: {{ floor(100/count($field->innerFields)) }}%;">
					{!! $innerField->setValue($value[$innerField->subName] ?? $position)->render() !!}					
				</div>
				@endforeach
			</div>
			<div class="uk-flex uk-flex-middle uk-flex-center">
				<i class="fas fa-minus uk-margin-small-bottom uk-padding-small jsonvaluesremover"></i>
			</div>
			
		</div>

	@endforeach
</div>


<div class="valuestemplate uk-hiddena">
	<div
		id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"
		class="jsonvalues fakejsonvalues uk-grid-collapse"
		uk-grid
		>
		<div class="uk-flex uk-flex-middle uk-flex-center uk-width-auto">
			<i class="fas fa-arrows-alt uk-margin-small-bottom uk-padding-small"></i>
		</div>
		<div uk-grid class="uk-child-width-1-2 uk-form-stacked uk-width-expand">
			@foreach($field->innerFields as $innerField)
			<div style="width: {{ floor(100/count($field->innerFields)) }}%;">
				{!! $innerField->setValue(null)->render() !!}
			</div>
			@endforeach			
		</div>
		<div class="uk-flex uk-flex-middle uk-flex-center">
			<i class="fas fa-minus uk-margin-small-bottom uk-padding-small jsonvaluesremover"></i>
		</div>
		
	</div>
</div>

<button type="button" data-id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}" class="addjson" uk-icon="plus">{{ __('fields.addInstance') }}</button>

@include('formfield::uikit.formRowFooter')
@include('formfield::uikit.formRowHeader')

<div
	uk-grid class="uk-grid-collapse"
	>
	<div class="uk-flex uk-flex-middle uk-flex-center uk-width-auto">
		<i class="fas fa-arrows-alt uk-margin-small-bottom uk-padding-small uk-invisible"></i>
	</div>
	@if(! $field->isVertical())
	<div uk-grid class="uk-child-width-auto uk-form-stacked uk-width-expand uk-flex uk-flex-middle">
		@foreach($field->innerFields as $innerField)
		<div style="width: {{ floor(100/count($field->innerFields)) }}%;">
			<label>{{ __('fields.' . $innerField->subName) }}</label>			
		</div>
		@endforeach
	</div>
	@endif

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
			@if($field->isVertical())
			<div class="uk-form-stacked uk-width-expand childfields">
			@else
			<div uk-grid class="uk-child-width-1-2 uk-form-stacked uk-width-expand childfields">
			@endif
				@foreach($field->getInnerFieldsByKeyValue($key, $value) as $innerField)
				<div @if(! $field->isVertical()) style="width: {{ floor(100/count($field->innerFields)) }}%;" @endif>
					{!! $innerField->setValue($value[$innerField->subName] ?? null)->render() !!}					
				</div>
				@endforeach
			</div>
			<div class="uk-flex uk-flex-middle uk-flex-center">
				<i class="fas fa-minus uk-margin-small-bottom uk-padding-small jsonvaluesremover"></i>
			</div>
			
		</div>

	@endforeach
</div>


<div class="valuestemplate uk-hidden">
	<div
		id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"
		class="jsonvalues fakejsonvalues uk-grid-collapse"
		uk-grid
		>
		<div class="uk-flex uk-flex-middle uk-flex-center uk-width-auto">
			<i class="fas fa-arrows-alt uk-margin-small-bottom uk-padding-small"></i>
		</div>
			@if($field->isVertical())
			<div class="uk-form-stacked uk-width-expand childfields">
			@else
			<div uk-grid class="uk-child-width-1-2 uk-form-stacked uk-width-expand childfields">
			@endif
			
			@foreach($field->innerFields as $innerField)
			<div @if(! $field->isVertical()) style="width: {{ floor(100/count($field->innerFields)) }}%;" @endif>
				{!! $innerField->setValue(null)->render() !!}
			</div>
			@endforeach			
		</div>
		<div class="uk-flex uk-flex-middle uk-flex-center">
			<i class="fas fa-minus uk-margin-small-bottom uk-padding-small jsonvaluesremover"></i>
		</div>
		
	</div>
</div>

<button type="button" data-id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}" class="addjson uk-button uk-button-small uk-button-danger uk-margin-bottom" uk-icon="plus">{{ __('formfield::fields.addInstance') }}</button>

@include('formfield::uikit.formRowFooter')
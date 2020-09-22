@include('formfield::uikit.formRowHeader')

<div class="valuescontainer">
	<div
		uk-grid
		>
		<div uk-grid class="uk-child-width-1-2 uk-clearfix uk-form-stacked uk-width-expand">
			@foreach($field->innerFields as $innerField)
			<label>{{ __('fields.' . $innerField->subName) }}</label>
			@endforeach			
		</div>
		<div class="uk-width-auto">
			<button type="button" data-id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}" class="addjsonfake" uk-icon="plus"></button>
		</div>
		
	</div>

	@foreach($field->getFormOldValue() as $key => $value)

		<div
			id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"
			class="jsonvalues"
			uk-grid
			>
			<div uk-grid class="uk-child-width-1-2 uk-clearfix uk-form-stacked uk-width-expand">
				@foreach($field->getInnerFieldsByKeyValue($key, $value) as $innerField)
					{!! $innerField->setValue($value[$innerField->subName])->render() !!}
				@endforeach			
			</div>
			<div class="uk-width-auto">
				<span uk-icon="minus" class="jsonvaluesremover"></span>
			</div>
			
		</div>

	@endforeach
</div>


<div class="valuestemplate uk-hidden">
	<div
		id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"
		class="jsonvalues fakejsonvalues"
		uk-grid
		>
		<div uk-grid class="uk-child-width-1-2 uk-clearfix uk-form-stacked uk-width-expand">
			@foreach($field->innerFields as $innerField)
				{!! $innerField->setValue(null)->render() !!}
			@endforeach			
		</div>
		<div class="uk-width-auto">
			<span uk-icon="minus" class="jsonvaluesremover"></span>
		</div>
		
	</div>
</div>

<button type="button" data-id="{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}" class="addjson" uk-icon="plus">{{ __('fields.addInstance') }}</button>

@include('formfield::uikit.formRowFooter')
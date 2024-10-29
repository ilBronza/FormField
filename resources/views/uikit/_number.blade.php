@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	@if($decimals = $field->getDecimals())
	onchange="(function(el){el.value=parseFloat(el.value).toFixed({{ $decimals }});})(this)"
	@endif

	type="number"
	value="{{ $field->getNumberFormOldValue() }}"
	step="{{ $field->getStep() }}"
	/>

@include('formfield::uikit.formRowFooter')
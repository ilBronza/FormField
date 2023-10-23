@include('formfield::uikit.show.formRowHeader')

@if($field->isInteger())
	{{ round($field->getFormOldValue()) }}
@else
	{{ $field->getFormOldValue() }}
@endif

@include('formfield::uikit.show.formRowFooter')
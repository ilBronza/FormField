@include('formfield::uikit.show.formRowHeader')

<a target="_blank" href="{{ $field->getFormOldValue() }}">{{ $field->getFormOldValue() }}</a>

@include('formfield::uikit.show.formRowFooter')
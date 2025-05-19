@include('formfield::uikit.show.formRowHeader')

<a href="mailto:{{ $field->getFormOldValue() }}" FaIcon::>{!! FaIcon::email() !!} {{ $field->getFormOldValue() }}</a>

@include('formfield::uikit.show.formRowFooter')
@include('formfield::uikit.show.formRowHeader')


@if($field->getShowValue())
CONTROLLARE QUESTO OUTPUT, PERCHE' NON arriva dal successivo else?
	{!! $field->getShowValue() !!}
@else

	<ul>
	@foreach($field->getSelectedPossibleValuesArray() as $index => $value)
		<li>{{ $value }}</li>
	@endforeach
	</ul>


@endif

@include('formfield::uikit.show.formRowFooter')
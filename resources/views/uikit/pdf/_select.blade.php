@include('formfield::uikit.pdf.formRowHeader')


@if($field->getShowValue())
	{!! $field->getShowValue() !!}
@else

	@if(is_array($field->getFormOldSelected()))
	<ul>
		@foreach($field->getFormOldSelected() as $selected)
		<li>{!! $selected !!}</li>
		@endforeach
	</ul>
	@endif

@endif

@include('formfield::uikit.pdf.formRowFooter')
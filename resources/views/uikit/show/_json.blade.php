@include('formfield::uikit.show.formRowHeader')

<table class="uk-table">

	@if($current = current($field->getValue()))
	<tr>
		@foreach($current as $fieldName => $parameters)
		<th>
			{{ __('fields.' . $fieldName) }}
		</th>
		@endforeach
	</tr>

	@endif

	@foreach($field->getValue() as $index => $parameters)
	<tr>
		@foreach($parameters as $name => $value)
		<td>{{ $value }}</td>
		@endforeach
	</tr>
	@endforeach

</table>

@include('formfield::uikit.show.formRowFooter')
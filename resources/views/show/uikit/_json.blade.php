<div class="uk-alert uk-alert-danger">
	UTILIZZARE Il nuovo sistema con file di parametri di SAEL
</div>

<table class="uk-table">
	@if($current = current($arrayElement))
	<tr>
		@foreach($current as $fieldName => $parameters)
		<th>
			{{ __('fields.' . $fieldName) }}
		</th>
		@endforeach
	</tr>

	@endif

	@foreach($arrayElement as $index => $parameters)
	<tr>
		@foreach($parameters as $name => $value)
		<td>{{ $value }}</td>
		@endforeach
	</tr>
	@endforeach
	
</table>
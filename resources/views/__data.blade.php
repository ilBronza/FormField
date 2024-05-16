@foreach($field->getDataAttributes() as $data => $value)
	@if(is_string($value))
	data-{{ $data }}="{{ $value }}"
	@else
	data-{{ $data }}="{{ json_encode($value) }}"
	@endif
@endforeach

@if(! isset($hideOldValue))

	@if(is_string($value = $field->getFormOldValue()))
		data-originalvalue="{{ $field->getFormOldValue() }}"
	@else
		data-originalvalue="{{ json_encode($value) }}"
	@endif

@endif
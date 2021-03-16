@foreach($field->getDataAttributes() as $data => $value)
	data-{{ $data }}="{{ json_encode($value) }}"
@endforeach

	@if(is_string($value = $field->getFormOldValue()))
		data-originalvalue="{{ $field->getFormOldValue() }}"
	@else
		data-originalvalue="{{ json_encode($value) }}"
	@endif

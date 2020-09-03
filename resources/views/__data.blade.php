@foreach($field->getDataAttributes() as $data => $value)
	data-{{ $data }}="{{ json_encode($value) }}"
@endforeach
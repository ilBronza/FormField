@foreach($field->getFetcherData() as $name => $value)
data-{{ $name }}="{{ $value }}"
@endforeach
@foreach($links as $link)
<a uk-tooltip title="@lang('crud::crud.showElementPage', ['element' => $link['name']])" href="{{ $link['link'] }}">{{ $link['name'] }}</a> @if(! $loop->first)<br />@endif
@endforeach
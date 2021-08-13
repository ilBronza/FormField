@foreach($filesCollection as $file)
	<li><a href="{{ $file->getUrl() }}" uk-icon="file">{{ $file->name }}</a> &nbsp; <span data-deleteurl="{{ $file->getDeleteUrl() }}" uk-icon="trash"></span></li>
@endforeach

@foreach($filesCollection as $file)
	<li><a target="_blank" href="{{ $file->getUrl() }}" uk-icon="file">{{ $file->name }}</a> &nbsp; <span class="ib-dropzone-delete" href="{{ $file->getDeleteUrl() }}" uk-icon="trash"></span></li>
@endforeach

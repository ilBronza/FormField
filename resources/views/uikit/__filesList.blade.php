@foreach($filesCollection as $file)
	<li uk-lightbox>
		<a
	        data-type="iframe"
			href="{{ $file->getServeImageUrl() }}?iframed=true"
			uk-icon="file">
				{{ $file->name }}
		</a> &nbsp; <span class="ib-dropzone-delete" href="{{ $field->getForm()->getModel()->getDeleteMediaUrlByMedia($file) }}" uk-icon="trash"></span></li>
@endforeach

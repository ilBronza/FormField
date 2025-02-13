@foreach($filesCollection as $file)
	<li uk-lightbox>
		<a
				data-type="iframe"
				href="{{ $file->getServeImageUrl() }}?iframed=true"
		>
			<span class="ib-text-filename">{{ $file->name }}</span>
			{!! FaIcon::inline('file') !!}
		</a> &nbsp; <span class="ib-dropzone-delete" href="{{ $field->getModel()->getDeleteMediaUrlByMedia($file) }}"
						  uk-icon="trash"></span></li>
@endforeach

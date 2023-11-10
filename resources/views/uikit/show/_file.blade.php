@include('formfield::uikit.show.formRowHeader')

<ul class="uk-list">
@foreach($field->getFormOldValue() as $file)
	<li uk-lightbox>
		<a
			data-type="iframe"
			href="{{ $file->getServeImageUrl() }}?iframed=true"
			>

			@if($file->isImage())
				<img style="max-width: 100px; max-height: 60px;" src="{{ $file->getUrl() }}" />
			@else
			<span uk-icon="file">
				{{ $file->name }}
			</span>
			@endif
		</a>
	</li>
@endforeach
</ul>

@include('formfield::uikit.show.formRowFooter')
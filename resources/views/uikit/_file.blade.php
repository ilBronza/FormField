@include('formfield::uikit.formRowHeader')

<div
	@include('formfield::__data')
	@include('formfield::__attributes')

{{-- 	type="text"
	value="{{ $field->getFormOldValue() }}"
 --}}


	>
	<div class="dropzone">
		
	</div>

	<ul class="fileslist" uk-lightbox>
	</ul>

</div>

<script type="text/javascript">

	let id = "{{ ($overrideId ?? ($field->getId() . ( ($fieldIndex ?? false)? ('-' . $fieldIndex) : ''))) }}"

	let container = "div#" + id;
	let element = container + ' .dropzone';

	var myDropzone = new Dropzone(element, {
		url: "{{ $field->getUploadingUrl() }}",
		params: {
			"ib-fileupload": true,
			fieldname: $(container).attr('name'),

			//lo useremo per decidere quale file sostituire etc
			index: null,

			//uuid del file esistente
			uuid: null,

			//inutile ?
			rewriting: false,

			//thumbnails come oggetto?
			thumbnails: false,

			multiple: false,
			_method: "{{ $field->getMethod() }}"
		},
		headers: {
			'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
		}
	}).on("success", function(file, response)
	{
		$(container + ' .fileslist').append('<li><a href="' + response.fileurl + '" uk-icon="file">' + response.filename + '</a> &nbsp; <span data-deleteurl="' + response.deleteurl + '" uk-icon="trash"></span></li>');

		// window.dropzoneIbSuccess(file, response, container);
	});
</script>

@include('formfield::uikit.formRowFooter')
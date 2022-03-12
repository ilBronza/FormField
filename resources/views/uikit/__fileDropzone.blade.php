<div
	@include('formfield::__data')
	@include('formfield::__attributes')

{{-- 	type="text"
	value="{{ $field->getFormOldValue() }}"
 --}}


	>
	<div class="dropzone">
		
	</div>

	<ul class="fileslist">
		@include('formfield::uikit.__filesList', ['filesCollection' => $field->getFiles()])
	</ul>

</div>

<script type="text/javascript">

	var id = "{{ ($overrideId ?? ($field->getId() . ( ($fieldIndex ?? false)? ('-' . $fieldIndex) : ''))) }}"

	var container = "div#" + id;
	var element = container + ' .dropzone';

	var myDropzone = new Dropzone(element, {
		url: "{{ $field->getUploadingUrl() }}",
		dictDefaultMessage: 'Trascina i files in questo riquadro',
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

			multiple: {{ (($field->isMultiple()) ? 'true' : 'false') }},
			_method: "{{ $field->getMethod() }}"
		},
		headers: {
			'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
		}
	}).on("success", function(file, response)
	{
		window.addSuccessNotification(response.message);

		$( 'div#{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }} .fileslist').append('<li><a target="_blank" href="' + response.fileurl + '" uk-icon="file">' + response.filename + '</a> &nbsp; <span class="ib-dropzone-delete" href="' + response.deleteurl + '" uk-icon="trash"></span></li>');

		// window.dropzoneIbSuccess(file, response, container);
	});
</script>

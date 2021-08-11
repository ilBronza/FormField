@include('formfield::uikit.formRowHeader')

<div
	@include('formfield::__data')
	@include('formfield::__attributes')

{{-- 	type="text"
	value="{{ $field->getFormOldValue() }}"
 --}}


	>

	<ul class="fileslist">
	</ul>

</div>

<script type="text/javascript">

	let id = "{{ ($overrideId ?? ($field->getId() . ( ($fieldIndex ?? false)? ('-' . $fieldIndex) : ''))) }}"

	let element = "div#" + id;

	var myDropzone = new Dropzone(element, {
		url: "{{ $field->getUploadingUrl() }}",
		params: {
			"ib-fileupload": true,
			fieldname: $(element).attr('name'),

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
	}).on("success", function(file, response) {
			console.log(file);
			console.log(response);

			window.dropzoneIbSuccess(file, response);
			/* Maybe display some more file information on your page */
			});
</script>

@include('formfield::uikit.formRowFooter')
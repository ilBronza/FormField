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


<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
<script src="https://unpkg.com/cropperjs"></script>

<script type="text/javascript">

	window.dropzoneError = function(file, response)
	{
		window.addDangerNotification(response.message);
	}

	var id = "{{ ($overrideId ?? ($field->getId() . ( ($fieldIndex ?? false)? ('-' . $fieldIndex) : ''))) }}"

	var container = "div#" + id;
	var element = container + ' .dropzone';

	var myDropzone = new Dropzone(element, {
		url: "{{ $field->getUploadingUrl() }}",

		@if($field->hasCropper())
		transformFile: function(file, done)
		{
			// Create Dropzone reference for use in confirm button click handler
			var myDropZone = this;

			// Create the image editor overlay
			var editor = document.createElement('div');
			editor.style.position = 'fixed';
			editor.style.left = 0;
			editor.style.right = 0;
			editor.style.top = 0;
			editor.style.bottom = 0;
			editor.style.zIndex = 9999;
			editor.style.backgroundColor = '#000';
			document.body.appendChild(editor);


			// Create confirm button at the top left of the viewport
			var buttonConfirm = document.createElement('button');
			buttonConfirm.style.position = 'absolute';
			buttonConfirm.style.left = '10px';
			buttonConfirm.style.top = '10px';
			buttonConfirm.style.zIndex = 9999;
			buttonConfirm.textContent = 'Confirm';
			editor.appendChild(buttonConfirm);
			buttonConfirm.addEventListener('click', function()
			{
				// Get the canvas with image data from Cropper.js
				var canvas = cropper.getCroppedCanvas(
					{!! $field->getCroppedCanvasParameters() !!}
					);

				// Turn the canvas into a Blob (file object without a name)
				canvas.toBlob(function(blob)
				{

					// Create a new Dropzone file thumbnail
					myDropZone.createThumbnail(
						blob,
						myDropZone.options.thumbnailWidth,
						myDropZone.options.thumbnailHeight,
						myDropZone.options.thumbnailMethod,
						false, 
						function(dataURL)
						{
							// Update the Dropzone file thumbnail
							myDropZone.emit('thumbnail', file, dataURL);
							// Return the file to Dropzone
							done(blob);
						}
					);
				});

				// Remove the editor from the view
				document.body.removeChild(editor);
			});

			// Create an image node for Cropper.js
			var image = new Image();
			image.src = URL.createObjectURL(file);
			editor.appendChild(image);

			// Create Cropper.js
			var cropper = new Cropper(image, {
				aspectRatio: 1
			});
		},
		@endif








		dictDefaultMessage: '{{ trans('formfield::files.dragFilesHereLabel') }}',
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
		if(! response.success)
			return window.dropzoneError(file, response);
		
		window.addSuccessNotification(response.message);

		$( 'div#{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }} .fileslist').append('<li><a target="_blank" href="' + response.fileurl + '" uk-icon="file">' + response.filename + '</a> &nbsp; <span class="ib-dropzone-delete" href="' + response.deleteurl + '" uk-icon="trash"></span></li>');

		// window.dropzoneIbSuccess(file, response, container);
	}).on("error", function(file, response)
	{
		return window.dropzoneError(file, response);

		// window.addSuccessNotification(response.message);

		// $( 'div#{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }} .fileslist').append('<li><a target="_blank" href="' + response.fileurl + '" uk-icon="file">' + response.filename + '</a> &nbsp; <span class="ib-dropzone-delete" href="' + response.deleteurl + '" uk-icon="trash"></span></li>');

		// window.dropzoneIbSuccess(file, response, container);
	});
</script>

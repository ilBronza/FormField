@include('formfield::uikit.formRowHeader')

	@include('formfield::uikit.__contractFileDownload')

	@if($field->isDropzone())
		@include('formfield::uikit.__fileDropzone')
	@else
		@include('formfield::uikit.__fileFlat')
	@endif

@include('formfield::uikit.formRowFooter')

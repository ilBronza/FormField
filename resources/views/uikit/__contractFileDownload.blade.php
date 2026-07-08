@if($contractUrl = $field->getContractUrl())
	<a
		class="uk-margin-small-right ib-contract-file-download"
		href="{{ $contractUrl }}"
		download
		target="_blank"
		rel="noopener"
		uk-tooltip="title: {{ __('formfield::files.contractDownload') }}"
	>
		{!! FaIcon::inline('print') !!}
	</a>
@endif

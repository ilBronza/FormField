<div
	@if(! $field->isVisible())
	hidden="1"
	@endif

	@if($field->isRepeatable())
		data-delete-instance-url="{{ $field->getAjaxDeleteInstanceUrl() }}"
	@endif

	class="uk-margin-small-bottom @if($field->isRepeatable()) ib-repeatable @if($field->isFirstOfType()) ib-first-field @endif @endif {{ $field->getHtmlRowClassesString() }} {{ $field->getFieldTypeClass() }} uk-clearfix fieldcontainer {{-- Occhio che classe fieldcontainer era una volta solo container --}} fieldcontainer{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"

	@if($field->isClosed())
	style="display: none;"
	@endif

	@if($id = $field->getContainerId())
		id="{{ $id }}"
	@endif
	>

	@if(($label = $field->getLabel())&&(! $field->isButton()))
	<label class="uk-form-label {{ $field->getHtmlLabelClassesString() }} @if($field->hasDblClickCopy()) dblclickcopy @endif">

		@if($historyUrl = $field->getHistoryUrl())
		<span uk-lightbox>
			<a data-type="iframe" href="{{ $historyUrl }}">
				<i class="fas fa-database"></i>
			</a>
		</span>
		@endif

		@if($icon = $field->getFasIcon())
		<i class="fas fa-{{ $icon }}"></i>
		@endif

		{!! $label !!}

		@if($field->isRequired())
		*
		@endif

		@if($tooltip = $field->getTooltip())
			<span uk-tooltip='title:{{ $tooltip }}' uk-icon='question'></span>
		@endif

	</label>
	@endif

	<div class="uk-form-controls @if(! $label) uk-margin-remove-left @endif">

		@if($field->getPrefix()||$field->hasAlert()||$field->getSuffix())
		<div class="ib-suffix-container">
		@endif

			@if($prefix = $field->getPrefix())
				<div class="ib-prefix"><div>{{ $prefix }}</div></div>
            @endif

			@if($alert = $field->getAlertString())
				<div class="ib-prefix">
					<div>
						<span uk-tooltip="{!! $alert !!}" class="uk-button uk-button-small uk-button-danger uk-float-left">
							<i class="fa-solid fa-exclamation-triangle"></i>
						</span>
					</div>
				</div>
            @endif


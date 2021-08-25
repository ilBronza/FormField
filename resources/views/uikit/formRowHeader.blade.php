<div
	class="uk-margin-small-bottom {{ $field->getHtmlRowClassesString() }} {{ $field->getFieldTypeClass() }} {{ ($field->isClosed())? 'uk-hidden' : '' }} uk-clearfix container{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"

	@if($id = $field->getContainerId())
		id="{{ $id }}"
	@endif
	>
	@if($label = $field->getLabel())
	<label class="uk-form-label">

		@if($icon = $field->getFasIcon())
		<i class="fas fa-{{ $icon }}"></i>
		@endif

		{!! $label !!}

		@if($tooltip = $field->getTooltip())
			<span uk-tooltip='title:{{ $tooltip }}' uk-icon='question'></span>
		@endif

	</label>
	@endif

	<div class="uk-form-controls @if(! $label) uk-margin-remove-left @endif">


		@if($field->getPrefix()||$field->getSuffix())
		<div class="ib-suffix-container">
		@endif

			@if($prefix = $field->getPrefix())
				<div class="ib-prefix"><div>{{ $prefix }}</div></div>
			@endif


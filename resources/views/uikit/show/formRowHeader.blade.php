<div
	class="showfield uk-margin-small-bottom {{ $field->getHtmlRowClassesString() }} {{ $field->getFieldTypeClass() }} uk-clearfix fieldcontainer container{{ $overrideId ?? ($field->getId() . (isset($fieldIndex)? ('-' . $fieldIndex) : '')) }}"

	style="{{ ($field->isClosed())? 'display: none;' : '' }} "

	@if($id = $field->getContainerId())
		id="{{ $id }}"
	@endif
	>

	@if($label = $field->getLabel())

	<label class="uk-form-label uk-text-bold">

		@if($icon = $field->getFasIcon())
		<i class="fas fa-{{ $icon }}"></i>
		@endif

{{-- 		@if($field->isRelationship())
		<a uk-tooltip title="@lang('crud::crud.backToList')" href="{{ $field->getRelationshipTypeLink() }}">
			
		@endif
 --}}
		{!! $label !!}

{{-- 		@if($field->isRelationship())
			<i class="fas fa-list"></i>
		</a>
		@endif
 --}}
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

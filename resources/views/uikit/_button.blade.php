@include('formfield::uikit.formRowHeader')

	<button
		@include('formfield::__data', ['hideOldValue' => true])
		@include('formfield::__attributes')

		type="text"
		value="{{ $field->getValue() }}"

		>
		@if($icon = $field->getFasIcon())
		<i class="fas fa-{{ $icon }}"></i>
		@endif

		{!! $field->getLabel() !!}
			
		</button>

@include('formfield::uikit.formRowFooter')
					</span>
			@if($suffix = $field->getSuffix())
				<div class="ib-suffix"><div>{{ $suffix }}</div></div>
			@endif

		@if($field->getPrefix()||$field->getSuffix())
		</div>
		@endif

	</div>
</div>

@if(count($problems) > 0)
<span uk-tooltip="title: {{ implode("<br />>", $problems) }}" class="ib-field-problems uk-button uk-button-small uk-button-danger uk-padding-remove-horizontal">
		&nbsp;<i class="fa-solid fa-exclamation-triangle"> {{ count($problems) }} &nbsp;</i>
</span>
@endif
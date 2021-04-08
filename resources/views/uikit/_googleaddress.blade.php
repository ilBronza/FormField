@include('formfield::uikit.formRowHeader')

<input
	@include('formfield::__data')
	@include('formfield::__attributes')

	type="text"
	value="{{ $field->getFormOldValue() }}"

	/>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=API_KEY&libraries=places&callback=initMap">
</script>


<script>
function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
} 

docReady(function() {
	@if(isset($overrideId)||($field->getId()))
	let input_id="{{ $overrideId ?? $field->getId() }}";
	@endif

	const center = { lat: 50.064192, lng: -130.605469 };
	// Create a bounding box with sides ~10km away from the center point
	const defaultBounds = {
	  north: center.lat + 0.1,
	  south: center.lat - 0.1,
	  east: center.lng + 0.1,
	  west: center.lng - 0.1,
	};
	const input = document.getElementById(input_id);
	const options = {
	  bounds: defaultBounds,
	  componentRestrictions: { country: "it" },
	  fields: ["address_components", "geometry", "icon", "name"],
	  origin: center,
	  strictBounds: false,
	  types: ["establishment"],
	};
	const autocomplete = new google.maps.places.Autocomplete(input, options);
});
</script>
@include('formfield::uikit.formRowFooter')
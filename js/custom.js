// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. 
var placeSearch, autocomplete;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    //autocomplete.addListener('place_changed', fillInAddress);
}

function geolocate() {   
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    }
}

function validateDonorForm(){
	var valid=false;
	var notifier=document.getElementById("notification-par");
	var phone=document.getElementById("donate_phone").value;
	var names=document.getElementById("donate_name").value;
	var address=document.getElementById("autocomplete").value;
	var age=document.getElementById("donate_age").value;
	if(phone.length<10||phone.length>12){
		notifier.innerHTML="<p style='color:red'>Invalid phone number</p>"		
	}else if(names==null||names==""){
		notifier.innerHTML="<p style='color:red'>Invalid names</p>"
	}else if(address==null||address==""){
		notifier.innerHTML="<p style='color:red'>Invalid address</p>"
	}else if(age==null||age==""||age<=17){
		notifier.innerHTML="<p style='color:red'>Age must be greater than 17.</p>"
	}else{
		valid=true;
	}
	return valid;
}
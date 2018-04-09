// This script is to handle the emptiness of the verify-code box
function validateForm() {
	var x = document.forms["verify-form"]["verify-code"].value;
	if ( !x.match(/^\d{5}$/)) {
		alert('Please enter 5-digit code in the box!');
		return false;
	}
}
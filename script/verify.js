// This function is to ensure the entered code MUST be 5 characters
function validateForm() {
	var x = document.forms["verify-form"]["verify-code"].value;
	if ( !x.match(/^\d{5}$/)) {
		alert('Please enter 5-digit code in the box!');
		return false;
	}
}

// Function to reset verification code timeout by redirecting to another php page
function resendCode() {
	window.location.replace("http://localhost/php/resetVerificationCodeTimeout.php");
}
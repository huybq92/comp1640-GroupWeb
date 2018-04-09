// This function is to check passwords matching & empty CAPTCHA
function validateForm() {
	// Get all input values
	var username           = document.forms["register-form"]["username"].value;
	var password           = document.forms["register-form"]["password"].value;
	var special_characters = new RegExp(/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/); //unacceptable chars
	var captcha = document.forms["register-form"]["captcha"].value;
	var repassword = document.forms["register-form"]["repassword"].value;

	//Firstly check for special characters in username/password inputs
    if (special_characters.test(username)) {
        alert("Please only use standard alphanumerics for username!");
        return false;
    } else if (special_characters.test(password)) {
    	alert("Please only use standard alphanumerics for password!");
    	return false;
    }
	//Then, check if passwords are matched
	else if (password != repassword) {
		alert('Passwords are not matched!');
		return false;
	}
	// Finally, make sure the entered captcha must be 5 characters
	else if ( !captcha.match(/^\d{5}$/) ) {
		alert('Please enter 5 CAPTCHA digits in the box!');
		return false;
	}// SOURCE CODE: http://www.the-art-of-web.com/php/captcha/
}
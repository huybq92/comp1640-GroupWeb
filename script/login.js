// This script is to check if the username/password contains any special characters
// Username/password should only contains a-z, A-Z, 0-9 and some characters like
// REFERENCE CODE: https://stackoverflow.com/questions/11896599/javascript-code-to-check-special-characters
function validateForm() {
	var username           = document.forms["login-form"]["username"].value;
	var password           = document.forms["login-form"]["password"].value;
	var special_characters = new RegExp(/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/); //unacceptable chars
    if (special_characters.test(username)) {
        alert("Please only use standard alphanumerics for username!");
        return false;
    } else if (special_characters.test(password)) {
    	alert("Please only use standard alphanumerics for password!");
    	return false;
    }
    return true; // good user input
}
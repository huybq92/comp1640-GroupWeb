<?php
	// REFERENCE CODE: https://www.w3schools.com/php/php_cookies.asp
	// All of the following functions are based on the code from reference link

	// Function to create a cookie that stores username
	// This cookie will be checked by Javascript code inside login.php
	function createLoginCookie($login_user) {
		if (testCookieEnabled()) {
			// If cookie is enabled in the browser
			$cookie_name = "last_login";
			$cookie_value = $login_user; // save the username tht just have logined
			setcookie($cookie_name, $cookie_value, time() + (86400 * 7), "/"); // 86400 = 1 day ==> cookie will last for 1 week
		}
		// If cookie is not enabled, then do nothing
	}

	// This function is to check if the browser is cookie-enabled
	// - Return TRUE if enabled
	// - Return FALSE if not enabled
	function testCookieEnabled() {
		setcookie("test_cookie", "test", time() + 3600, '/'); // create a random cookie
		return (count($_COOKIE) > 0) ? true : false;
	}
?>
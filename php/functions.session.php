<?php
// #### This file contains all session-related functions ####

	// This function is to regenerate session ID every 30 minutes
	// The purpose is to enhance the security by preventing fake ID attack
	function checkSessionLifetime() {
		// REFERENCE CODE: https://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
		if (time() - $_SESSION['CREATED'] > 1800) {
		    // Session has started for more than 30 minutes (1800 seconds)
		    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID. This function ONLY changes session ID, NOT the session data
		    $_SESSION['CREATED'] = time();  // update new time
		}
	}

	function checkSessionLastActivity() {
		// REFERENCE CODE: https://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
		//Firstly, check session timeout
		if (time() - $_SESSION['LAST_ACTIVITY'] > 1800) {
		    // Last request was more than 30 minutes (1800 secs) ago
		    echo '<script>',
					'window.location.replace("../php/logout.php")',
					'</script>'; // logout and go back to login page
		} else {
			// If timeout is NOT over
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			checkSessionLifetime(); // also check for session lifetime to regenerate session ID
		}
	}

	// Function to check session for Login page
	function checkSessionInLogin() {
		if ( isset($_SESSION['login_user']) ) 
		{
			// If session exists. Then check account type
			if ($_SESSION['acc_type'] == '0') 
			{
				// Student account
				header('Location:main/main.php', true, 301);
				exit();

			} elseif ($_SESSION['acc_type'] == '1') {
				// QA coordinator account
				header('Location:assist/main.php', true, 301);
				exit();

			} elseif ($_SESSION['acc_type'] == '2') {
				// QA manager account
				header('Location:admin/main.php', true, 301);
				exit();

			} else {
				// Staff account (acc_type = '3')
				header('Location:staff/main.php', true, 301);
				exit();
			}
		}

		// If session doesn't exist, do nothing
	}

	// Function to check session in Main page
	function checkSessionInMain() {
		if ( !isset($_SESSION['login_user']) ) {
			// If session doesn't exist, go back to login page
			header('Location:login.php', true, 301);
			exit();
		} else {
			//If session exists, check if session timeout is over
			checkSessionLastActivity();
		}
	}

?>
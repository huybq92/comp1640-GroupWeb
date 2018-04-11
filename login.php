<?php
// Inside the below php, I already created an object named '$connection'
require_once 'php/class.CustomMySQLConnection.php';
// Some functions to handle timeout requirements
require_once 'php/functions.session.php';

	// Starting Session
	session_start();

	// FIRST, check if user already logged in this session.
	checkSessionInLogin();

	//This variable is used to display error message for user
	$error = "";

	// Login Button handler
	if(isset($_POST['button-login'])) {
		// Handle the username and password in forms to fix any inappropriate charset for SQL statement format
		$myusername = $connection->fixEscapeString($_POST['username']);
		$mypassword = $connection->fixEscapeString($_POST['password']);

		//Define SELECT SQL statement
		$query = "SELECT acc_id,acc_type FROM accounts WHERE username = '$myusername' AND password = '$mypassword';";
		//Define SELECT SQL statement
		$final_closure_date_query = "SELECT set_value FROM settings WHERE set_id=2 AND set_value > CURRENT_TIMESTAMP;";

		//Execute SQL statement and get result.
		$result_set = $connection->executeSELECT($query);
		//Execute SQL statement and get result.
		$final_closure_date_query_result = $connection->executeSELECT($final_closure_date_query);

		// If query successfully, myqli_result object will be returned
		if ( $result_set != FALSE && $final_closure_date_query_result != FALSE ) {
			
			//Count the number of rows from the result set
			$count_final_closure_date = mysqli_num_rows($final_closure_date_query_result);

			if ($count_final_closure_date == 1){
				// There is 1 row returned ==> the final closure date is still behind current time ==> CAN LOGIN
				//Count the number of rows from the result set
				$count = mysqli_num_rows($result_set);

				//If result matched $myusername and $mypassword, there must be 1 row in the result
				if($count == 1) {
					// Get an array from $result_set, then save some data to session variables
					$result = mysqli_fetch_assoc($result_set);
					$_SESSION['login_user'] = $myusername;
					$_SESSION['login_id'] = $result['acc_id'];
					$_SESSION['acc_type'] = $result['acc_type']; // type of account saved in session. Needed in main.php 

					//Save session variables for handling session timeout
					$_SESSION['CREATED'] = time(); // save the time when session first created
					$_SESSION['LAST_ACTIVITY'] = $_SESSION['CREATED']; // to keep track of the last time activity

					// FINALLY, redirect to main page according to account type
					if ($result['acc_type'] == '0') {
						// Student account
						header('Location:main/main.php', true, 301);
						exit();

					} elseif ($result['acc_type'] == '1') {
						 // QA coordinator account
						header('Location:assist/main.php', true, 301);
						exit();

					} elseif ($result['acc_type'] == '2') {
						// QA Manager account
						header('Location:admin/main.php', true, 301);
						exit();

					} else {
						// Staff account
						header('Location:staff/main.php', true, 301);
						exit();

					}

				} else {
					// Wrong username/password
					$error = "<span> Username or Password is not correct!</span>";
				}

			} else {
				// The final closure date is over already ==> ONLY qa_manager CAN login
				if ( $myusername == 'qa_manager') {
					//Count the number of rows from the result set
					$count = mysqli_num_rows($result_set);

					//If result matched $myusername and $mypassword, there must be 1 row in the result
					if($count == 1) {
						// Get an array from $result_set, then save some data to session variables
						$result = mysqli_fetch_assoc($result_set);
						$_SESSION['login_user'] = $myusername;
						$_SESSION['login_id'] = $result['acc_id'];
						$_SESSION['acc_type'] = $result['acc_type']; // type of account saved in session. Needed in main.php 

						//Save session variables for handling session timeout
						$_SESSION['CREATED'] = time(); // save the time when session first created
						$_SESSION['LAST_ACTIVITY'] = $_SESSION['CREATED']; // to keep track of the last time activity

						// FINALLY, redirect to main page according to account type
						if ($result['acc_type'] == '0') {
							// Student account
							header('Location:main/main.php', true, 301);
							exit();

						} elseif ($result['acc_type'] == '1') {
							 // QA coordinator account
							header('Location:assist/main.php', true, 301);
							exit();

						} elseif ($result['acc_type'] == '2') {
							// QA Manager account
							header('Location:admin/main.php', true, 301);
							exit();

						} else {
							// Staff account
							header('Location:staff/main.php', true, 301);
							exit();

						}

					} else {
						// Wrong username/password
						$error = "<span> Username or Password is not correct!</span>";
					}				
				} else {
					// If not qa_manager
					echo "<script type='text/javascript'>alert('Cannot login. The final closure date is over.');</script>";
				}

			}
	
		} else {
			//If the query is failed
			$error = "<span>Error: " + $connection->getDbConnectionError() + "</span>";
		}

    }// End of handler
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Login Page</title>
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link href='http://fonts.googleapis.com/css?family=Crete+Round' rel='stylesheet' type='text/css'>
	<script src="script/login.js"></script>
</head>

<body>
	<header>
		<div class="container">
			<a href="/"><img src = "img/tmc-logo2.png" alt = "TMC logo" /></a>
		</div>
	</header>

	<div class="container">
		<div class="form">
			<form name="login-form" action="" method="post" onsubmit="return validateForm()">
			    <p><input
			    	type="textinput"
			    	placeholder="username"
			    	onfocus="this.placeholder=''"
			    	onblur="this.placeholder='username'"
			    	name="username"
			    	maxlength="50"
			    	required></p>
			    <p><input
			    	type="password"
			    	placeholder="password"
			    	onfocus="this.placeholder=''"
			    	onblur="this.placeholder='password'"
			    	name="password"
			    	maxlength="50"
			    	required></p>
			    <?php echo $error; ?>
			    <p><input type="submit" value="LOG IN" name="button-login"><p>
			    <p class="message">Not registered? <a href="#">Create an account</a></p>
			</form>
		</div>
	</div>

	<footer>
		<div class="container">
			<p><small>Copyright 2017, Bui Quang Huy. All rights reserved.</small></p>
			<p><small><a href="#">Terms of Service</a> I <a href="#">Privacy</a></small></p>
		<div class="clear"></div>
		</div>
	</footer>
</body>
</html>
<!-- REFERENCE CODE: https://codepen.io/miroot/pen/qwIgC  -->
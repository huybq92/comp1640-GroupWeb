<?php
// REFERENCE CODE: https://github.com/PHPMailer/PHPMailer
// I'm using Gmail SMTP to send email through PHPMailer
require_once '/Library/WebServer/Documents/php-mailer/class.phpmailer.php';

	class CustomMailer {
		// Function to send mail using phpmailer object
		public static function emailVerifyCode() {
			// Get values from session variables
			$email = $_SESSION['verify_email'];
			$user  = $_SESSION['login_user'];
			$code  = CustomMailer::generateRandom5DigitCode();

			// Create mailer object from class PHPMailer
			$mail = new PHPMailer(true);

			try {
				// SMTP server settings
				$mail->SMTPDebug  = 0; //Disable verbose debug output
				$mail->IsSMTP(); // Set PHPMailer to use SMTP
				$mail->CharSet    = "UTF-8";
				$mail->SMTPSecure = 'tls'; // Enable TLS encryption
				$mail->Host       = 'smtp.gmail.com';
				$mail->Port       = '587'; // TCP port to connect to Gmail SMTP server
				$mail->Username   = 'huykungfu@gmail.com';
				$mail->Password   = 'tr*baV4S';
				$mail->SMTPAuth   = true;
				date_default_timezone_set('Asia/Singapore'); //set timezone to Singapore

				//Recipients
				$mail->setFrom ('huykungfu@gmail.com','admin@COMP1687.com');
				$mail->AddAddress("$email"); // SEND email to user account saved in session variable

				//Content
				$mail->IsHTML(true); // Set email format to HTML
				$mail->Subject = "Verification code for your account!"; // Email subject
				$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
				$mail->Body    = "Hi there, <br>This is the verification code for your account <b><i>$user</i></b>: <br><br><b>$code</b><br><br> Regards,<br>"; // Email message in HTML formart

				// SEND MAIL
				$mail->Send();

				// Until this line of code, sending mail has succedded without exception!
				$_SESSION['verify_code'] = $code; // save code to session for later validation
			  	$_SESSION['verify_code_timeout'] = time(); // also update timeout for this code
			  	return true;
			} catch (Exception $e) {
				return false;
			}
		}

		// Function ot generate random 5-digit code for verification
		public static function generateRandom5DigitCode() {
			$verify_code = '';
			for ($x = 0; $x < 5; $x += 1) {
		    	$verify_code .= rand(0,9);
		  	}
		  	return $verify_code;
		}
	}
?>
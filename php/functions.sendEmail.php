<?php
// REFERENCE CODE: https://github.com/PHPMailer/PHPMailer
// I'm using PHPMailer to send email through Gmail's SMTP Server
require_once 'class.phpmailer.php';

	// Send email to QA Coordinator to inform that a new idea has been created by user
	function notifyQACoordinator() {
		// Get values from session variables
		$email = 'huybq.1992@gmail.com';
		$user  = $_SESSION['login_user']; //'cause the user in session is the one that create new idea

		// Create a new mailer object from class PHPMailer
		$mail = new PHPMailer(true);

		try {
			// SMTP server settings
			$mail->SMTPDebug  = 0; //Disable verbose debug output
			$mail->IsSMTP(); // Set PHPMailer to use SMTP
			$mail->CharSet    = "UTF-8";
			$mail->SMTPSecure = 'ssl'; // Enable TLS encryption
			$mail->Host       = 'smtp.gmail.com';
			$mail->Port       = '465'; // TCP port to connect to Gmail SMTP server
			$mail->Username   = 'huybq92.testmail@gmail.com';
			$mail->Password   = 'tr*baV4S/?';
			$mail->SMTPAuth   = true;
			//date_default_timezone_set('Asia/Singapore'); //set timezone to Singapore

			//Recipients
			$mail->setFrom ('huybq92.testmail@gmail.com','admin@COMP1640.com');
			$mail->AddAddress("$email"); // SEND email to user account saved in session variable

			//Content
			$mail->IsHTML(true); // Set email format to HTML
			$mail->Subject = "New idea has been posted!"; // Email subject
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
			$mail->Body    = "Hi, <br><br><b><i>$user</i></b> has posted a new idea to the system!<br><br>Regards,";

			// SEND MAIL
			$mail->Send();

			// Finally, return true
			return true;
		} catch (Exception $e) {
			// any exceptions occurs
			return false;
		}
	}

	// Send email to users to inform that a new comment has been added to their idea
	function notifyUserForNewComment($user_email, $user_comment) {

		// Create a new mailer object from class PHPMailer
		$mail = new PHPMailer(true);

		try {
			// SMTP server settings
			$mail->SMTPDebug  = 0; //Disable verbose debug output
			$mail->IsSMTP(); // Set PHPMailer to use SMTP
			$mail->CharSet    = "UTF-8";
			$mail->SMTPSecure = 'ssl'; // Enable TLS encryption
			$mail->Host       = 'smtp.gmail.com';
			$mail->Port       = '465'; // TCP port to connect to Gmail SMTP server
			$mail->Username   = 'huybq92.testmail@gmail.com';
			$mail->Password   = 'tr*baV4S/?';
			$mail->SMTPAuth   = true;
			//date_default_timezone_set('Asia/Singapore'); //set timezone to Singapore

			//Recipients
			$mail->setFrom ('huybq92.testmail@gmail.com','admin@COMP1640.com');
			$mail->AddAddress("$user_email"); // SEND email to user account saved in session variable

			//Content
			$mail->IsHTML(true); // Set email format to HTML
			$mail->Subject = "Your idea has received a new comment!"; // Email subject
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
			$mail->Body    = "Hi, <br><br><b><i>$user_comment</i></b> has commented on your idea!<br><br>Regards,";

			// SEND MAIL
			$mail->Send();

			// Finally, return true
			return true;
		} catch (Exception $e) {
			// any exceptions occurs
			return false;
		}
	}

?>
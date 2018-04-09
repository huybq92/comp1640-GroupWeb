<?php
	session_start();
	unset($_SESSION['verify_code_timeout']);
	unset($_SESSION['verify_code']);
	header('Location:http://localhost/verify.htm', true, 301);
	exit();
?>
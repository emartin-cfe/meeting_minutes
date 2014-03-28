<?php
	# Check user/password provided
	$user = escapeshellcmd($_POST['login']);
	$password = escapeshellcmd($_POST['password']);

	# Attempt to authenticate user/password credentials
	$results = exec("source enable_oracle.sh; ./validateQAI_credentials.pl $user $password", $results);

	if ($results == "SUCCESS") {
		session_start();
		$_SESSION['signed_in'] = 1;
		header('Location: displayMeetings.php');
		}
	else {
		session_start();
		session_destroy();
		header('Location: sign_in.html');
		}
	exit();
?>

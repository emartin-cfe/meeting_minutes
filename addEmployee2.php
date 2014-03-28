<?php

    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injections
        $link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
        if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db("scheduling") or die(mysql_error());

	$firstName = mysql_real_escape_string($_POST['firstName']);
	$lastName = mysql_real_escape_string($_POST['lastName']);
	$email = mysql_real_escape_string($_POST['email']);
	$employeeStatus = $_POST['employeeStatus'];

	$query = 	"INSERT INTO Employees (firstName, lastName, email, status) " .
			"VALUES ('$firstName', '$lastName', '$email', '$employeeStatus')";

	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	header("Location: manageEmployees.php");
?>

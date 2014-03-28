<?php

    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);
	$noteID = mysql_real_escape_string($_GET['noteID']);
	$user = mysql_real_escape_string($_GET['user']);

	# We need to ook up the ID of the user, and insert the mapping into TaskAssignment
	$query = "SELECT employeeID FROM Employees WHERE CONCAT(firstName, ' ', lastName) LIKE '$user'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	$employeeID = $row['employeeID'];
	$query = "DELETE FROM AssignedEmployees WHERE noteID LIKE '$noteID' AND employeeID LIKE '$employeeID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: addPeople.php?noteID=$noteID&meetingID=$meetingID");
	exit();
?>

<?php

    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injections
        $link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
        if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db('scheduling') or die(mysql_error());

        $meetingID = mysql_real_escape_string($_POST['meetingID']);
        $topicID = mysql_real_escape_string($_POST['topicID']);
	$actionDescription = mysql_real_escape_string($_POST['actionDescription']);
	$assignedEmployees = mysql_real_escape_string($_POST['assignedEmployees']);
	$dueDate = mysql_real_escape_string($_POST['dueDate']);
	$priority = mysql_real_escape_string($_POST['priority']);

	# Get today's date
	$query = "SELECT DATE(NOW()) theDate";
	$dbHandle = mysql_query($query) or die(mysql_error()); $row = mysql_fetch_array($dbHandle);
	$todaysDate = $row['theDate'];

	# Insert the MeetingNote
	$query = 	"INSERT INTO MeetingNote (meetingID, topicID, new, actionDescription, priority, startDate, dueDate) VALUES " .
			"('$meetingID', '$topicID', 'new', '$actionDescription', '$priority', '$todaysDate', '$dueDate')";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Get the noteID so we can create the AssignedEmployees mapping
	$query = 	"SELECT noteID FROM MeetingNote WHERE meetingID LIKE '$meetingID' AND actionDescription LIKE '$actionDescription' " .
			"AND priority LIKE '$priority' AND DATE(startDate) LIKE '$todaysDate' AND dueDate LIKE '$dueDate'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$noteID = $row['noteID'];

	# Get the employeeID so we can create the AssignedEmployees mapping
	$query =	"SELECT employeeID FROM Employees WHERE CONCAT(firstName, ' ', lastName) LIKE '$assignedEmployees'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$employeeID = $row['employeeID'];

	# Insert the employee/task mapping
	$query =	"INSERT INTO AssignedEmployees (noteID, employeeID) VALUES ('$noteID', '$employeeID')";
	if($employeeID != ''){ $dbHandle = mysql_query($query) or die(mysql_error()); }	# Ignore the blank field in the select

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit();
?>

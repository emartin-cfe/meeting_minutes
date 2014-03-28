<!DOCTYPE HTML>

<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	# The SQL connection must be established before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# From the current list of active Employees, populate Attendance assuming all employees are present by default
	$query = 	"INSERT INTO Attendance (employeeID, meetingID, attendance) " .
		 	"SELECT employeeID, '$meetingID', 'Y' " .
			"FROM Employees " .
			"WHERE status LIKE 'active'";

	$dbHandle = mysql_query($query) or die(mysql_error());

	# At the next screen, the administrator can determine who was absent from this list
	header("Location: attendance2.php?meetingID=$meetingID");
	exit();
?>

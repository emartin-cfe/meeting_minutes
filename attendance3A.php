<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injections
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);
	$employee = mysql_real_escape_string($_GET['employee']);

	# Get the employeeID of the individual we are modifying (Buggy implementation: fails if two people have the same first/last name)
	$query = "SELECT employeeID FROM Employees WHERE CONCAT(firstName, ' ', lastName) LIKE '$employee'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$employeeID = $row['employeeID'];

	# Set the attendance of this individual as present
	$query = "UPDATE Attendance SET attendance='Y' WHERE meetingID LIKE '$meetingID' AND employeeID LIKE '$employeeID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Return to the attendance display page for more modification
	header("Location: attendance2.php?meetingID=$meetingID");
	exit();
?>

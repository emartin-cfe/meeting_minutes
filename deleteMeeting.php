<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# Delete attendance record
	$query = "DELETE FROM Attendance WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Delete AssignedEmployees linked to this meetingID
	$query = "DELETE AssignedEmployees FROM MeetingNote NATURAL JOIN AssignedEmployees WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Delete MeetingNotes linked to this meetingID
	$query = "DELETE FROM MeetingNote WHERE meetingID LIKE '$meetingID'";
        $dbHandle = mysql_query($query) or die(mysql_error());

	# Delete the meeting itself
	$query = "DELETE FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: displayMeetings.php");
	exit();
?>

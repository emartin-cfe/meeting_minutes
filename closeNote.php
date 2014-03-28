<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$noteID = mysql_real_escape_string($_GET['noteID']);
	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# Get the date of this meeting
	$query = "SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];

	# Set the close date to that meeting date
	$query = "UPDATE MeetingNote SET status='closed', closureDate='$meetingDate' WHERE meetingID LIKE '$meetingID' AND noteID LIKE '$noteID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit;
?>

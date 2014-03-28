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

	$meetingID = mysql_real_escape_string($_POST['meetingID']);
	$noteID = mysql_real_escape_string($_POST['noteID']);
	$actionDescription = mysql_real_escape_string($_POST['actionDescription']);
	$status = mysql_real_escape_string($_POST['status']);
	$priority = mysql_real_escape_string($_POST['priority']);
	$dueDate = mysql_real_escape_string($_POST['dueDate']);
	$topicID = mysql_real_escape_string($_POST['topicID']);

        # Check to see if the current status is not closed, and if it is now being changed to closed, record now as the close date
	$query = "SELECT status oldStatus FROM MeetingNote WHERE noteID LIKE '$noteID' AND meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$oldStatus = $row['oldStatus'];

	# Get the date of this meeting
	$query = "SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];


	$SQL_condition = "";

	# If the note was open and we are setting it to closed, set the closure date TO THE DATE OF THIS MEETING
	if (($oldStatus != 'closed') && ($status == 'closed')) { $SQL_condition = ", closureDate='$meetingDate' "; }

	# If the note was closed, but we are reopening it, clear the closure date
	if (($oldStatus == 'closed') && ($status == 'open')) { $SQL_condition = ", closureDate = NULL "; }

	$query = 	"UPDATE MeetingNote " .
			"SET actionDescription='$actionDescription', status='$status', priority='$priority', dueDate = '$dueDate', topicID = '$topicID' " . 
			"$SQL_condition" .
			"WHERE noteID LIKE '$noteID' AND meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit();
?>

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

	$meetingID = mysql_real_escape_string($_GET['meetingID']);
	$noteID = mysql_real_escape_string($_GET['noteID']);

	# Check to see if this note is new - if it is not, give an error screen
	$query = "SELECT new FROM MeetingNote WHERE noteID LIKE '$noteID' AND meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$new = $row['new'];

	if ($new != 'new') {
		print "You can only delete new notes! Click <a href='displayTasks.php?meetingID=$meetingID'>here</a> to return to the main menu";
		exit;
		}
	# If all is well, delete the AssignedEmployees links, then the note itself
	$query = 	"DELETE FROM AssignedEmployees " .
			"WHERE noteID LIKE '$noteID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# If all is well, delete the note
	$query = 	"DELETE FROM MeetingNote " .
			"WHERE noteID LIKE '$noteID' AND meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit();
?>

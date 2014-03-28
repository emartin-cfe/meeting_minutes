<?php

    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }

	# The sql connection must be established before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());
	$date = mysql_real_escape_string($_GET['date']);

	# New meeting inherit unclosed notes from the previous meeting

	# Get the previous meetingID
	$query = "SELECT MAX(meetingID) meetingID FROM Meetings";
	$dbHandle = mysql_query($query) or die(mysql_error()); $row = mysql_fetch_array($dbHandle); $oldMeetingID = $row['meetingID'];

	# Make a new meeting with the current date
	$query = "INSERT INTO Meetings (meetingDate) VALUES ('$date')";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Get the new meetingID
	$query = "SELECT MAX(meetingID) meetingID FROM Meetings";
	$dbHandle = mysql_query($query) or die(mysql_error()); $row = mysql_fetch_array($dbHandle); $newMeetingID = $row['meetingID'];

	# Get unclosed notes from previous meeting
	$query = 	"SELECT topicID, noteID oldNoteID, actionDescription, status, priority, DATE(startDate) startDate, dueDate " .
			"FROM MeetingNote WHERE meetingID LIKE '$oldMeetingID' AND status NOT LIKE 'closed'";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# For each note to copy
	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = mysql_real_escape_string($row['topicID']);
		$oldNoteID = mysql_real_escape_string($row['oldNoteID']);
		$actionDescription = mysql_real_escape_string($row['actionDescription']);
		$status = mysql_real_escape_string($row['status']);
		$priority = mysql_real_escape_string($row['priority']);
		$startDate = mysql_real_escape_string($row['startDate']);
		$dueDate = mysql_real_escape_string($row['dueDate']);

		# For each note in the previous meeting, make an identical new note in the the new meeting - but denote as old
		$query2 = 	"INSERT INTO MeetingNote (meetingID, topicID, new, actionDescription, status, priority, startDate, dueDate) " .
				"VALUES ('$newMeetingID', '$topicID', 'old', '$actionDescription', '$status', '$priority', '$startDate', '$dueDate')";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());

		# Get the new noteID created
		$query2 =	"SELECT noteID FROM MeetingNote " .
				"WHERE meetingID LIKE '$newMeetingID' AND actionDescription LIKE '$actionDescription' AND status LIKE '$status'" .
				"AND priority LIKE '$priority' AND DATE(startDate) LIKE '$startDate' AND dueDate LIKE '$dueDate'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$row = mysql_fetch_array($dbHandle2);
		$newNoteID = mysql_real_escape_string($row['noteID']);

		# Get the employee mappings for the original note
		$query2 = 	"SELECT employeeID FROM AssignedEmployees WHERE noteID LIKE '$oldNoteID'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		while ($row = mysql_fetch_array($dbHandle2)) {
			$employeeID = mysql_real_escape_string($row['employeeID']);
			# And copy it over for the new noteID
			$query3 = "INSERT INTO AssignedEmployees (noteID, employeeID) VALUES ('$newNoteID', '$employeeID')";
			$dbHandle3 = mysql_query($query3) or die(mysql_error());
			} # End of each mapping to copy
		} # End of each note to copy

	# At this point a brand new meeting should be created, containing all of the unclosed notes from the previous

	header('Location: attendance1.php?meetingID=' . $newMeetingID);
	exit();
?>

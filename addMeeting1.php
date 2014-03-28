<!DOCTYPE html>

<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/editNoteInputForm.css">
	<link rel="stylesheet" type="text/css" href="css/editNoteButtons.css">
	<script type="text/javascript" src="javascript/addMeeting1.js"></script>

	<link rel="stylesheet" href="calendar/calendarview.css">
	<link rel="stylesheet" href="calendar/calendarCustomization.css">
	<script src="calendar/prototype.js"></script>
	<script src="calendar/calendarview.js"></script>
	<script src="calendar/setupCalendar_editTask.js"></script>	<!-- This instantiates the calendar object -->
</head>

<?php

	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	# Get the date of the most recent meeting
	$query = "SELECT DATE(MAX(meetingDate)) recentMeeting FROM Meetings";   $dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	$unformattedMostRecentDate = $row['recentMeeting'];
	date_default_timezone_set('America/Vancouver');
	$mostRecentDate = new DateTime($row['recentMeeting']);
	$formattedMostRecentDate = $mostRecentDate->format('Y-M-d');


	# Initialize the previous date as a javascript variable to prevent older dates from being selected
	# since the current meeting date cannot be before the the previous meeting
	print 	"<body onload='initialize(\"$unformattedMostRecentDate\")'>\n" .
		"<div id='stylized' class='myform'>\n" .
		"<h1>Make new meeting</h1>\n";

	print "\t<p>The last meeting was one $formattedMostRecentDate</p>\n";	
?>
	<div class='label'>Meeting date</div>
	<div id="popupDateField" class="dateField">2013-02-19</div>

	<div class="buttons">
		<button type="submit" class="positive" onclick="return setDate()"> <img src="images/check.png" alt=""/>Save changes</button>
		<button type="button" class="positive" onclick="cancel()"> <img src="images/cross.png" alt=""/> Cancel</button>
	</div>

	</form>	
	</div>
</body>
</html>

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
	<script type="text/javascript" src="javascript/editNoteButtons.js"></script>

	<link rel="stylesheet" href="calendar/calendarview.css">
	<link rel="stylesheet" href="calendar/calendarCustomization.css">
	<script src="calendar/prototype.js"></script>
	<script src="calendar/calendarview.js"></script>
	<script src="calendar/setupCalendar_editTask.js"></script>
</head>

<body>

<?php
	# editNote.php is launched from displayTasks.php

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$noteID = mysql_real_escape_string($_GET['noteID']);
	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	$query = 	"SELECT DATE(meetingDate) meetingDate, actionDescription, status, priority, dueDate, topicID " .
			"FROM MeetingNote NATURAL JOIN Meetings NATURAL JOIN Topics WHERE noteID LIKE '$noteID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	$meetingDate = $row['meetingDate'];	$actionDescription = $row['actionDescription'];	$status = $row['status'];
	$priority = $row['priority'];		$dueDate = $row['dueDate'];	$selectedTopicID = $row['topicID'];


	# Populate the fields with their current values
print <<<HTML_OUT
<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="editNote2.php">

	<h1>Meeting: $meetingDate</h1>
	<p>Action/Decision ID: $noteID</p>
HTML_OUT;

print	"\t<label>Topic</label>\n" .
	"\t<select name='topicID' id='topicID'>" .
		"\t\t<option value=''></option>\n";
	# Display topics that this note can be assigned to
	$query = "SELECT topicID, topicName FROM Topics WHERE topicStatus LIKE 'active' ORDER BY topicName";
	$dbHandle = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = $row['topicID']; $topicName = $row['topicName'];
		if ($topicID == $selectedTopicID) { print "\t\t\t<option value='$topicID' selected>$topicName</option>\n"; }
		else { print "\t\t\t<option value='$topicID'>$topicName</option>\n"; }
		}
print	"\t</select>";

print <<<HTML_OUT
	<input type="text" class="hidden" name="meetingID" id="meetingID" value="$meetingID"/>
	<input type="text" class="hidden" name="noteID" id="noteID" value="$noteID"/>

	<label>Action/Decisions</label>
	<textarea name="actionDescription" id="actionDescription">$actionDescription</textarea>


HTML_OUT;

	# Populate the select showing the status of the note: and show the selected status by default
	print 	"\t<label>Status<span class='small'></span></label>\n" .
		"\t<select name='status' id='status'>\n";

	if ($status == 'open') {
		print 	"\t\t<option value='open' selected>open</option>\n" .
			"\t\t<option value='closed'>closed</option>\n" .
			"\t\t<option value='on hold'>on hold</option>\n";
		}

	elseif ($status == 'closed') {
		print 	"\t\t<option value='open'>open</option>\n" .
			"\t\t<option value='closed' selected>closed</option>\n" .
			"\t\t<option value='on hold'>on hold</option>\n";
		}

	elseif ($status == 'on hold') {
		print 	"\t\t<option value='open'>open</option>\n" .
			"\t\t<option value='closed'>closed</option>\n" .
			"\t\t<option value='on hold' selected>on hold</option>\n";
		}

	print 	"\t</select>\n\n";

	# Populate the select showing the priority of the note: and show the selected priority by default
	print	"\t<label>Priority<span class='small'></span></label>\n" .
		"\t<select name='priority' id='priority'>\n";

	if ($priority == 'normal') {
		print 	"\t\t<option value='normal' selected>Normal</option>\n" .
			"\t\t<option value='critical'>Critical</option>\n" .
			"\t\t<option value='low'>Low</option>\n";
		}

	elseif ($priority == 'critical') {
		print	"\t\t<option value='normal'>Normal</option>\n" .
			"\t\t<option value='critical' selected>Critical</option>\n" .
			"\t\t<option value='low'>Low</option>\n";
		}

	else {
		print	"\t\t<option value='normal'>Normal</option>\n" .
			"\t\t<option value='critical'>Critical</option>\n" .
			"\t\t<option value='low' selected>Low</option>\n";
		}

print "\t</select>\n";

print <<<HTML_OUT

	<!-- The html value in the div field and the hidden input field are mirrored to each other by javascript on submit -->
	<div class='label'>Due date</div>
	<div id="popupDateField" class="dateField">$dueDate</div>
	<input type="text" class="hidden" name="dueDate" id="dueDate" />

	<div class="buttons">
		<button type="submit" class="positive" onclick="return editNote()"> <img src="images/check.png" alt=""/>Save changes</button>
		<button type="button" class="positive" onclick="cancel('$meetingID')"> <img src="images/cross.png" alt=""/> Cancel</button>
	</div>

	</form>	
	</div>
HTML_OUT;

?>

</body>
</html>

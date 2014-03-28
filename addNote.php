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
	<link rel="stylesheet" type="text/css" href="css/addTaskInputForm.css">
	<link rel="stylesheet" type="text/css" href="css/addTaskButtons.css">
	<script type="text/javascript" src="javascript/addTaskButtons.js"></script>

	<link rel="stylesheet" href="calendar/calendarview.css">
	<link rel="stylesheet" href="calendar/addNoteCalendarCustomization.css">
	<script src="calendar/prototype.js"></script>
	<script src="calendar/calendarview.js"></script>
	<script src="calendar/setupCalendar.js"></script>
</head>

<body>

<?php
	$meetingID = $_GET['meetingID'];
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	# Set the calendar date to the current date by default for this new note
	$query = "SELECT DATE(NOW()) currDate";
	$dbHandle = mysql_query($query) or die(mysql_error()); $row = mysql_fetch_array($dbHandle);
	$todaysDate = $row['currDate'];

	# Get the date of the meeting this task is linked to
	$query = "SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error()); $row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];

	print 	"<div id='stylized' class='myform'>\n" .
		"\t<form id='form' name='form' method='post' action='addNote2.php'>\n" .
		"\t\t<h1>Adding action for $meetingDate</h1>\n\n";

	print	"\t\t<input type='text' class='hidden' name='meetingID' id='meetingID' value='$meetingID' />\n\n" .
		"\t\t<label>Topic</label>\n" .
		"\t\t<select name='topicID' id='topicID'>\n" .
		"\t\t\t<option value=''></option>\n";

	# Display topics that this note can be assigned to
	$query = "SELECT topicID, topicName FROM Topics WHERE topicStatus LIKE 'active' ORDER BY topicName";
	$dbHandle = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = $row['topicID']; $topicName = $row['topicName'];
		print "\t\t\t<option value='$topicID'>$topicName</option>\n";
		}

	print	"\t\t</select>\n\n";

	print 	"\t\t<label>Action/Decision</label>\n" .
		"\t\t<textarea name='actionDescription' id='actionDescription'></textarea>\n\n" .
		"\t\t<label>Assigned to</label>\n" .
		"\t\t<select name='assignedEmployees' id='assignedEmployees'>" .
		"\n\t\t\t<option value=''></option>";

	# Display all employees which are currently active
	$query = "SELECT CONCAT(firstName, ' ', lastName) employeeName FROM Employees WHERE status LIKE 'active'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_array($dbHandle)) {
		$employee = $row['employeeName'];
		print "\n\t\t\t<option value='$employee'>$employee</option>";
		}
	print "\n\t\t</select>\n\n";

print<<<HTML
		<!-- The contents of the div popupDateField is mapped to the hidden input field dueDate for inclusion in POST -->
        	<div class='label'>Due date</div>
        	<div id="popupDateField" class="dateField">$todaysDate</div>
        	<input type="text" class="hidden" name="dueDate" id="dueDate" />

		<label>Priority<span class="small"></span></label>
		<select name="priority" id="priority">
			<option value="normal">Normal</option>
			<option value="critical">Critical</option>
			<option value="low">Low</option>
		</select>

		<div class="buttons">
			<button type="submit" class="positive" onclick="return addTask('$meetingID')"> <img src="images/addNew.png" alt=""/> Add task</button>
			<button type="button" class="positive" onclick="cancel('$meetingID')"> <img src="images/authorize.png" alt=""/> Cancel</button>
		</div>
HTML;
?>

	</form>
</div>

</body>

</html>

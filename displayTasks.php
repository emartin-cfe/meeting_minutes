<!DOCTYPE HTML>

<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/displayTasksTable.css">
	<link rel="stylesheet" type="text/css" href="css/displayTasksButtons.css">
	<script type="text/javascript" src="javascript/displayTasksButtons.js"></script>
	<script type="text/javascript" src="javascript/displayTasksCheckBox.js"></script>
</head>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# Get the date of this meeting
	$query = "SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];

	date_default_timezone_set('America/Vancouver');
	$meetingDate = new DateTime($row['meetingDate']);
	$formattedMeetingDate = $meetingDate->format('Y-M-d');

	print 	"<body onload=initialize('$meetingID')>\n\n" .
		"<div class='tableContainer'>\n";

	############# BEGINNING OF NEW ITEMS #############
	# Display all new notes - notes that have NOT been copied into this meeting from a previous meeting.
	print   "<table id='hor-minimalist-b'>\n" .
		"<thead>\n" .
		"\t<tr>\n" .
		"\t\t<th scope='col' class='topic'>NEW ITEM</th>\n" .	"\t\t<th scope='col' class='noteID'>note</th>\n" .
		"\t\t<th scope='col'>Action/Decision ($formattedMeetingDate)</th>\n" .
		"\t\t<th scope='col'>Responsible</th>\n" .
		"\t\t<th scope='col'>Due</th>\n" .			"\t\t<th scope='col'>Status</th>\n" .
		"\t\t<th scope='col' class='hidden'>Priority</th>\n" .	"\t\t<th scope='col'>Close date</th>\n" .
		"\t\t<th></th>\n" .
		"\t</tr>\n" .
		"</thead>\n\n";

	$query = 	"SELECT topicID, topicName, noteID, actionDescription, status, priority, dueDate, closureDate " .
			"FROM MeetingNote NATURAL JOIN Topics " .
			"WHERE meetingID LIKE '$meetingID' AND new LIKE 'new' ORDER BY topicID";
	$dbHandle = mysql_query($query) or die(mysql_error());

	print "<tbody>\n";

 	$previousTopic = "";
 	$counter = 0;

	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = $row['topicID'];			$topicName = $row['topicName'];		$noteID = $row['noteID'];
		$actionsDecisions = $row['actionDescription'];	$status = $row['status'];		$priority = $row['priority'];
		$dueDate = $row['dueDate'];			$closureDate = $row['closureDate'];

		$actionsDecisions = preg_replace('/\n/', '<br/>', $actionsDecisions);
		if (strlen($actionsDecisions) > 300) { $actionsDecisions = substr($actionsDecisions,0,300) . "..."; }

		# Format it the way Richard likes it
		$dueDate = new DateTime($row['dueDate']);
		$dueDate = $dueDate->format('Y-M-d');
		if(!$row['closureDate'] == '') { $closureDate = new DateTime($row['closureDate']);      $closureDate = $closureDate->format('Y-M-d'); }
		else { $closureDate = ""; }

		# If this is a new topic, increment the counter
		if ($previousTopic != $topicName) { $counter++; }
		$previousTopic = $topicName;

                # Highlight notes if today is past the due date
		$query4 = "SELECT '$dueDate' < DATE(NOW()) late";	$dbHandle4 = mysql_query($query4) or die(mysql_error());
		$row = mysql_fetch_array($dbHandle4);			if($row['late']) { $dueDate = "<span class='red'>$dueDate</span>"; }

		# Highlight high priority notes
		if ($priority == 'critical') { $priority = "<span class='red'>$priority</red>"; }

		# For new notes, determine what employees are assigned and generate concatenated string of these employees
		$query2 = "SELECT firstName employeeName FROM AssignedEmployees NATURAL JOIN Employees WHERE noteID LIKE '$noteID'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$assignedEmployees = "";
		while($row2 = mysql_fetch_array($dbHandle2)) {
			if ($assignedEmployees == '') { $assignedEmployees = $row2['employeeName']; }
			else {  $assignedEmployees = $assignedEmployees . "; " . $row2['employeeName']; }
			}

		print "\t<tr>\n" .
		"\t\t<td class='topic'>$counter - $topicName</td>\n" .		"\t\t<td class='noteID'>$noteID</td>\n" .
		"\t\t<td class='actionDecisions'>$actionsDecisions</td>\n" .	"\t\t<td class='assignedEmployees'>$assignedEmployees</td>\n" .
		"\t\t<td class='date'>$dueDate</td>\n" .			"\t\t<td class='status'>$status</td>\n" .
		"\t\t<td class='hidden'>$priority</td>\n" .			"\t\t<td class='closureDate'>$closureDate</td>\n" .
		"\t\t<td><input type='checkbox' class='selector' value='1' name='selected_$noteID' onClick='singleCheckBoxSelection(this)'></td>\n" .
		"\t</tr>\n";
		}
	print "</tbody>\n";
	print "</table>\n";
	######### END OF NEW ITEMS ###########

        ########### BEGINNING OF OLD ITEMS ###########
	print   "<table id='hor-minimalist-b'>\n" .
		"<thead>\n" .
		"\t<tr>\n" .
		"\t\t<th scope='col' class='topic'>OLD ITEM</th>\n" .			"\t\t<th scope='col' class='noteID'>note</th>\n" .
		"\t\t<th scope='col'>Action/Decision from previous meeting</th>\n" .	"\t\t<th scope='col'>Responsible</th>\n" .
		"\t\t<th scope='col'>Due</th>\n" .					"\t\t<th scope='col'>Status</th>\n" .
		"\t\t<th scope='col' class='hidden'>Priority</th>\n" .			"\t\t<th scope='col'>Close date</th>\n" .
		"\t\t<th></th>\n" .
		"\t</tr>\n" .
		"</thead>\n\n";

	# Display all old notes - old notes are those that have been copied into this meeting from a previous meeting
	# Order by the topicID, so that the counter displays properly. Specifically, we want to display all notes relating
        # to a TOPIC as one contiguous section, and as we progress to a new topic, the counter is incremented
	$query =	"SELECT topicID, topicName, noteID, actionDescription, status, priority, dueDate, closureDate " .
			"FROM MeetingNote NATURAL JOIN Topics " .
			"WHERE meetingID LIKE '$meetingID' AND new LIKE 'old' ORDER BY topicID";
	$dbHandle = mysql_query($query) or die(mysql_error());

	print "<tbody>\n";

	$previousTopic = "";
	$counter = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = $row['topicID'];     $topicName = $row['topicName'];         $noteID = $row['noteID'];
		$status = $row['status'];       $priority = $row['priority'];           $actionsDecisions = $row['actionDescription'];

		# Replace \n with <br/> and do not make it display more than a certain number of lines...
		$actionsDecisions = preg_replace('/\n/', '<br/><br/>', $actionsDecisions);
		if (strlen($actionsDecisions) > 300) { $actionsDecisions = substr($actionsDecisions,0,300) . "..."; }
	
		# Format the date the way Richard likes it
		$dueDate = new DateTime($row['dueDate']);
		$dueDate = $dueDate->format('Y-M-d');
		if(!$row['closureDate'] == '') { $closureDate = new DateTime($row['closureDate']);      $closureDate = $closureDate->format('Y-M-d'); }
		else { $closureDate = "<a href='closeNote.php?noteID=$noteID&meetingID=$meetingID'>close</a>"; }

		# If this is a new topic, increment the counter
		if ($previousTopic != $topicName) { $counter++; }
		$previousTopic = $topicName;

		# Highlight notes if today is past the due date
		$query4 = "SELECT '$dueDate' < DATE(NOW()) late";       $dbHandle4 = mysql_query($query4) or die(mysql_error());
		$row = mysql_fetch_array($dbHandle4);                   if($row['late']) { $dueDate = "<span class='red'>$dueDate</span>"; }

		# Highlight high priority notes
		if ($priority == 'critical') { $priority = "<span class='red'>$priority</red>"; }

		# For old notes, determine what employees are assigned and generate a concatenated list
		$query2 = 	"SELECT firstName employeeName " .
				"FROM AssignedEmployees NATURAL JOIN Employees WHERE noteID LIKE '$noteID'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$assignedEmployees = "";

		while($row2 = mysql_fetch_array($dbHandle2)) {
			if ($assignedEmployees == '') { $assignedEmployees = $row2['employeeName']; }
			else {  $assignedEmployees = $assignedEmployees . "; " . $row2['employeeName']; }
			}

		print   "\t<tr>\n" .
			"\t\t<td class='topic'>$counter - $topicName</td>\n" .		"\t\t<td class='noteID'>$noteID</td>\n" .
			"\t\t<td class='actionDecisions'>$actionsDecisions</td>\n" .	"\t\t<td class='assignedEmployees'>$assignedEmployees</td>\n" .
			"\t\t<td class='date'>$dueDate</td>\n" .			"\t\t<td class='status'>$status</td>\n" .
			"\t\t<td class='hidden'>$priority</td>\n" .			"\t\t<td class='closureDate'>$closureDate</td>\n" .
			"\t\t<td><input type='checkbox' class='selector' value='1' name='selected_$noteID' onClick='singleCheckBoxSelection(this)'></td>\n" .
                        "\t</tr>\n";
		}
	print "</tbody>\n";
	print "</table>\n\n";
	########### END OF OLD ITEMS #############

	print "</div>\n";
?>

<div class="buttons">
	<button type="submit" class="positive" onclick="addTask()"> <img src="images/addNew.png"/> Add</button>
	<button type="submit" class="positive" onclick="editTask()"> <img src="images/authorize.png"/> Edit</button>
	<button type="submit" class="positive" onclick="deleteTask()"> <img src="images/cross.png"/> Delete</button>
	<button type="submit" class="positive" onclick="addPeople()"> Assign </button>
	<button type="submit" class="positive" onclick="topics()"> Topics</button>
	<button type="submit" onclick="emailStaff()">Post minutes</button>
	<button type="submit" class="positive" onclick="mainMenu()"> <img src="images/logout.png"/> Return</button>
</div>

</body>
</html>

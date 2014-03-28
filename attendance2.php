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
	<link rel="stylesheet" type="text/css" href="css/attendance2.css" />
	<link rel="stylesheet" type="text/css" href="css/attendance2Buttons.css">
	<script type="text/javascript" src="javascript/attendance2.js"></script>
</head>

<?php
	# The SQL connection must be established before mysql_real_escape_string can be used to prevent SQL injections
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# Get date of this meeting
	$query = "SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];

	# Keep meetingID as a javascript window variable so that when attendance is done we go to displayTasks.php for this particular meeting
	print 	"<body onload='initialize(\"$meetingID\")'>\n" .
		"\t<h1>Attendance record for $meetingDate</h1>\n" .
		"\t<div class='wrapper'>\n\n" .
		"\t<h2>Present</h2>\n\n" . 
		"\t<ul class='quad'>\n";

	# Select all people from this meeting that are denoted as attended
	$query = 	"SELECT CONCAT(firstName, ' ', lastName) employeeName FROM Attendance NATURAL JOIN Employees " .
			"WHERE meetingID LIKE '$meetingID' AND attendance LIKE 'Y' ORDER BY CONCAT(firstName, ' ', lastName)";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Count is used to ensure that there are <li> elements only in multiples of 4, so the 4-collumn list is presented properly
	$count = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		print "\t\t<li><a href='#' onclick='employeeAbsent(this)'>" . $row['employeeName'] . "</a></li>\n";
		$count++;
		}
	$count = $count % 4;
	while ((4-$count > 0) && (4-$count != 4)) { print "\t\t<li>&nbsp</li>\n"; $count++; }

	print 	"\t</ul>\n\n";

	################################# END OF CODE DISPLAYING PRESENT EMPLOYEES ######################################

	print	"\t<h2>Absent</h2>\n\n" .
		"\t<ul class='quad'>\n";

	# Select all people who are not in this meeting
	$query = 	"SELECT CONCAT(firstName, ' ', lastName) employeeName FROM Employees NATURAL JOIN Attendance " .
			"WHERE meetingID LIKE '$meetingID' AND attendance LIKE 'N' ORDER BY CONCAT(firstName, ' ', lastName)";
	$dbHandle = mysql_query($query) or die(mysql_error());

	# Count is used to ensure that there are <li> elements only in multiples of 4, so the 4-collumn list is presented properly
	$count = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		print "\t\t<li><a href='#' onclick='employeePresent(this)'>" . $row['employeeName'] . "</a></li>\n";
		$count++;
		}

	$count = $count % 4;
	while ((4-$count > 0) && (4-$count != 4)) { print "\t\t<li>&nbsp</li>\n"; $count++; }
?>
	</ul>
</div>

<!-- Go back to displayTasks.php -->
<div class="buttons">
	<button type="submit" class="positive" onclick="save()"> <img src="images/check.png" alt=""/> Done</button>
</div>

</body>
</html>

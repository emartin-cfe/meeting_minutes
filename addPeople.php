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
	<link rel="stylesheet" type="text/css" href="css/addPeople.css" />
	<link rel="stylesheet" type="text/css" href="css/addPeopleButtons.css">
	<script type="text/javascript" src="javascript/addPeople.js"></script>
</head>

<?php
	# SQL connection needs to be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);
	$noteID = mysql_real_escape_string($_GET['noteID']);

	# We need the meetingID so that we can return directly to displayTasks.php in the context of the correct meeting
	print 	"<body onload='initialize(\"$meetingID\", \"$noteID\")'>\n" .
		"\t<h1>Task: $noteID</h1>\n" .
		"\t<div class='wrapper'>\n\n" .
		"\t<h2>Assigned</h2>\n\n" . 
		"\t<ul class='quad'>\n";

	# Show all note/employee assignments for this note
	$query = 	"SELECT CONCAT(firstName, ' ', lastName) employeeName FROM AssignedEmployees NATURAL JOIN Employees " .
			"WHERE noteID LIKE '$noteID' ORDER BY CONCAT(firstName, ' ', lastName)";

	$dbHandle = mysql_query($query) or die(mysql_error());

	# Count is used to track how many <li> elements are being placed - if it is not a multiple of 4...
	$count = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		print "\t\t<li><a href='#' onclick='removeUser(this)'>" . $row['employeeName'] . "</a></li>\n";
		$count++;
		}
	$count = $count % 4;

	# Then fill in with dummy <li> values so that the list presents properly on the screen
	while ((4-$count > 0) && (4-$count != 4)) {
		print "\t\t<li>&nbsp</li>\n"; $count++;
		}

	print 	"\t</ul>\n\n" .
		"\t<h2>Unassigned</h2>\n\n" .
		"\t<ul class='quad'>\n";

	# Get all active employees who are NOT already assigned to this note
	$query = 	"SELECT CONCAT(firstName, ' ', lastName) employeeName FROM Employees WHERE status LIKE 'active' AND employeeID NOT IN " .
			"(SELECT employeeID FROM AssignedEmployees NATURAL JOIN Employees WHERE noteID LIKE '$noteID') " .
			"ORDER BY CONCAT(firstName, ' ', lastName)";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$count = 0;

	while($row = mysql_fetch_array($dbHandle)) {
		print "\t\t<li><a href='#' onclick='addUser(this)'>" . $row['employeeName'] . "</a></li>\n";
		$count++;
		}

	# Fill this second table in with similar dummy <li> values if necessary
	$count = $count % 4;
	while ((4-$count > 0) && (4-$count != 4)) {
		print "\t\t<li>&nbsp</li>\n"; $count++;
		}
?>
	</ul>
</div>

<div class="buttons">
	<button type="submit" class="positive" onclick="returnToTask('7')"> <img src="images/logout.png" alt=""/> Return</button>
</div>

</body>
</html>

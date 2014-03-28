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
	<link rel="stylesheet" type="text/css" href="css/displayMeetingsTable.css">
	<link rel="stylesheet" type="text/css" href="css/displayMeetingsButtons.css">
	<script type="text/javascript" src="javascript/displayMeetingsCheckBox.js"></script>
	<script type="text/javascript" src="javascript/displayMeetingsButtonBehavior.js"></script>
</head>

<body>

<h1>Previous 10 meetings</h1>

<table id="hor-minimalist-b">

<thead>
	<tr>
		<th scope="col">Meeting ID</th>
		<th scope="col">Meeting Date</th>
		<th></th>
	</tr>
</thead>

<?php
	date_default_timezone_set('America/Vancouver');
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	# Display meetings - in the order of most recent meetings first
	$query = "SELECT meetingID, DATE(meetingDate) meetingDate FROM Meetings ORDER BY meetingID DESC LIMIT 10";
	$dbHandle = mysql_query($query) or die(mysql_error());

	print "<tbody>\n";

	while($row = mysql_fetch_array($dbHandle)) {
		$meetingID = $row['meetingID'];
		$meetingDate = $row['meetingDate'];	$meetingDate = new DateTime($row['meetingDate']);
		$formattedDate = $meetingDate->format('Y-M-d');

		print 	"\t<tr>\n" .
			"\t\t<td>$meetingID</td>\n" .
			"\t\t<td>$formattedDate</td>\n" .
			"\t\t<td><input type='checkbox' class='selector' value='1' name='selected_$meetingID' onClick='singleCheckBoxSelection(this)'></td>\n" .
			"\t</tr>\n";
		}
 
	print 	"</tbody>\n" .
		"</table>\n";

	print 	"<div class='buttons'>" .
		"\t<button type=\"submit\" class=\"positive\" onclick=\"report()\"> <img src=\"images/cog.png\"/>Report</button>\n" .
		"\t<button type=\"submit\" class=\"positive\" onclick=\"mainMenu()\"><img src=\"images/logout.png\"/>Logout</button>\n" .
		"</div>\n";
?>

</body>
</html>

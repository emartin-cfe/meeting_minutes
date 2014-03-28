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
	<link rel="stylesheet" type="text/css" href="css/manageTopicsTable.css">
	<link rel="stylesheet" type="text/css" href="css/manageTopicsButtons.css">
	<script type="text/javascript" src="javascript/manageTopicsButtons.js"></script>
	<script type="text/javascript" src="javascript/manageTopicsCheckBox.js"></script>
</head>


<?php
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = $_GET['meetingID'];	

	print "<body onload='initialize(\"$meetingID\")'>\n";

	print   "<table id='hor-minimalist-b'>\n" .
		"<thead>\n" .
		"\t<tr>\n" .
		"\t\t<th scope='col' class='topicID'>topicID</th>\n" .
		"\t\t<th scope='col'>Topic name</th>\n" .
		"\t\t<th scope='col'>Topic status</th>\n" .
		"\t\t<th></th>\n" .
		"\t</tr>\n" .
		"</thead>\n\n";

	# Get a list of all topics
	$query = "SELECT topicID, topicName, topicStatus FROM Topics ORDER BY topicName";
	$dbHandle = mysql_query($query) or die(mysql_error());

	print	"<tbody>\n";

	while($row = mysql_fetch_array($dbHandle)) {
		$topicID = $row['topicID'];	$topicName = $row['topicName'];		$topicStatus = $row['topicStatus'];

		print   "\t<tr>\n" .
			"\t\t<td class='topicID'>$topicID</td>\n" .
			"\t\t<td class='topicName'>$topicName</td>\n" .
			"\t\t<td class='topicStatus'>$topicStatus</td>\n" .
			"\t\t<td class='selector'>" .
				"<input type='checkbox' class='selector' value='1' name='selected_$topicID' onClick='singleCheckBoxSelection(this)'></td>\n" .
			"\t</tr>\n";
		}

	print	"</tbody>\n";
	print	"</table>\n";
?>

<div class="buttons">
	<button type="submit" class="positive" onclick="addTopic()"> <img src="images/addNew.png" alt=""/> Add topic</button>
	<button type="submit" class="positive" onclick="editTopic()"> <img src="images/authorize.png" alt=""/> Edit topic</button>
	<button type="submit" class="positive" onclick="mainMenu()"> <img src="images/logout.png" alt=""/> Return</button>
</div>

<p><i>Inactive topics are not displayed as an option when adding new notes.</i></p>

</body>
</html>

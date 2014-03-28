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
	<link rel="stylesheet" type="text/css" href="css/editEmployeeInputForm.css">
	<link rel="stylesheet" type="text/css" href="css/editEmployeeButtons.css">
	<script type="text/javascript" src="javascript/editTopicButtons.js"></script>
</head>
<body>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$topicID = mysql_real_escape_string($_GET['topicID']);
	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	$query = "SELECT topicName, topicStatus FROM Topics WHERE topicID LIKE '$topicID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$topicName = $row['topicName']; $topicStatus = $row['topicStatus'];


print<<<HTML
<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="editTopic2.php">
	<h1>Updating topic</h1>
	<p>Enter new topic information</p>

	<input type"text" class="hidden" name="meetingID" id="meetingID" value="$meetingID"/>
	<input type"text" class="hidden" name="topicID" id="topicID" value="$topicID"/>

	<label>Topic name</label>
	<input type="text" class="topicName" name="topicName" id="topicName" value="$topicName"/>

	<label>Status</label>
HTML;

	print '<select name="topicStatus" id="topicStatus">';
	if ($topicStatus == 'active') { print "<option value='active' selected>Active</option><option value='inactive'>Inactive</option>"; }
	else { print "<option value='active'>Active</option><option value='inactive' selected>Inactive</option>"; }
	print '</select>';

print<<<HTML
	<div class="buttons">
		<button type="submit" class="positive" onclick="return editEmployee()"> <img src="images/check.png" alt=""/> Save</button>
		<button type="button" class="positive" onclick="cancel()"> <img src="images/cross.png" alt=""/> Cancel</button>
	</div>

</form>
</div>

HTML;
?>

</body>
</html>

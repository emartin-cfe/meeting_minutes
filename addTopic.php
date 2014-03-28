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
	<link rel="stylesheet" type="text/css" href="css/addTopicInputForm.css">
	<link rel="stylesheet" type="text/css" href="css/addTopicButtons.css">
	<script type="text/javascript" src="javascript/addTopicButtons.js"></script>
</head>

<?php
	$meetingID = $_GET['meetingID'];
	print	"<body onload=initialize(\"$meetingID\")>";
?>

<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="addTopic2.php">
	<h1>New topic</h1>
	<p>Notes are assigned to topics</p>

<?php
	$meetingID = $_GET['meetingID'];
	print	"\t<input type=\"text\" class=\"hidden\" name=\"meetingID\" id=\"meetingID\" value=\"$meetingID\"/>\n";
?>

	<label>Topic name</label>
	<input type="text" class="topicName" name="topicName" id="topicName" />

	<label>Status</label>
	<select name="topicStatus" id="topicStatus">
		<option value='active' selected>Active</option>
		<option value='inactive'>Inactive</option>
	</select>

	<div class="buttons">
		<button type="submit" class="positive" onclick="return addTopic()"> <img src="images/check.png" alt=""/> Add</button>
		<button type="button" class="positive" onclick="cancel()"> <img src="images/cross.png" alt=""/> Cancel</button>
	</div>

</form>
</div>

</body>
</html>

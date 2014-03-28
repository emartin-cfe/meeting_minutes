<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injections
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$topicID = mysql_real_escape_string($_POST['topicID']);
	$meetingID = mysql_real_escape_string($_POST['meetingID']);
	$topicName = mysql_real_escape_string($_POST['topicName']);
	$topicStatus = mysql_real_escape_string($_POST['topicStatus']);

	$query = 	"UPDATE Topics SET topicName='$topicName', topicStatus='$topicStatus' " .
			"WHERE topicID LIKE '$topicID'";

	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit();
?>

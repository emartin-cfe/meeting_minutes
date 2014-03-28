<?php

    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injections
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$meetingID = mysql_real_escape_string($_POST['meetingID']);
	$topicName = mysql_real_escape_string($_POST['topicName']);
	$topicStatus = mysql_real_escape_string($_POST['topicStatus']);

	$query = 	"INSERT INTO Topics (topicName, topicStatus) " .
				"VALUES ('$topicName', '$topicStatus')";

	$dbHandle = mysql_query($query) or die(mysql_error());

	header("Location: displayTasks.php?meetingID=$meetingID");
	exit();
?>

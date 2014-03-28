#!/usr/bin/php
<?php
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	# Get overdue tasks
	$query = 	"SELECT email, firstName, dueDate, daysOverdue*-1 daysLate, actionDescription " .
				"FROM " .
					"(SELECT noteID, employeeID, actionDescription, dueDate, DATEDIFF(dueDate,DATE(NOW())) daysOverdue " .
					"FROM MeetingNote NATURAL JOIN AssignedEmployees " .
					"WHERE status LIKE 'open' AND DATEDIFF(dueDate,DATE(NOW())) < 0 " .
					"AND meetingID IN (SELECT MAX(meetingID) FROM Meetings)) " .
				"A NATURAL JOIN Employees";

	$file = '/Users/emartin/Sites/periodicReminderSystem/test.out';
	$message = "";

	$dbHandle = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_array($dbHandle)) {
		$email = $row['email'];				$firstName = $row['firstName'];
		$dueDate = $row['dueDate'];			$daysOverdue = $row['daysLate'];
		$actionDescription = $row['actionDescription'];
		$actionDescription = preg_replace('//', '<br/>', $actionDescription);

		$to = $email;

		$subject = "OVERDUE TASK: $firstName";

		$message =  "<html>\n\t " .
						"<body>\n\t" .
						"<p>Dear $firstName, you have a task <b>$daysOverdue</b> days overdue." .
						" To prevent future emails, have the task closed (at the NEXT MEETING), or have the due date changed (at the NEXT MEETING).</p>\n\t" .
						"<p><b>Action:</b> $actionDescription</p>\n\t" .
						"<p>Click <a href='http://192.168.69.205/~emartin/taskTracker/production/meeting_minutes/'>here</a> to view previous meeting minutes</p>" .
						"</body>\n" .
					"</html>\n";

		$cc = 'prharrigan@cfenet.ubc.ca';

		$headers  = 'MIME-Version: 1.0' . "\r\n";							# Needed for HTML mail
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	# Needed for HTML mail
		$headers .= 'From: Meeting Minutes <meeting_minutes@cfenet.ubc.ca>' . "\r\n";
		$headers .= "CC: $cc" . "\r\n";
		mail($to, $subject, $message, $headers);
		}
?>

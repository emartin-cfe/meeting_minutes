<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	$html_output = <<<HEREDOC
<DOCTYPE html>
<head>
<style>
/* For awesome lists: http://csswizardry.com/2010/02/mutiple-column-lists-using-one-ul */
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td{ margin:0; padding:0; }
ol,ul{ list-style:none; }
h1,h2,h3,h4,h5,h6{ font-size:100%; font-weight:normal; }
html{ font-size:100%; height:101%; }
body{ font-family:Georgia, "Times New Roman", Times, serif; color:#333; background:#fff; }
h1,h2,h3,h4,h5,h6{ font-family:Helvetica, Arial, Verdana, sans-serif; margin-bottom:20px; }
h1{ font-size:1.5em; font-weight:bold; }
h2{ font-size:1.25em; font-weight:bold; }
div.wrapper{ width:600px;padding:0px; margin:0 auto;overflow:hidden; }
div.wrapper ul{ width:100%; min-width:200px; margin-bottom:20px; overflow:hidden; border-top:1px solid #ccc; }
div.wrapper li{ font-size:12px; line-height:1.5em; border-bottom:1px solid #ccc; float:left; display:inline; text-align:center; }
div.wrapper ul.double li{ width:50.000%; }
div.wrapper ul.triple li{ width:33.333%; }
div.wrapper ul.quad li  { width:25.000%; }
div.wrapper ul.six li   { width:16.666%; }
img.logo {  width:45px; height: auto;   margin-right:25px;  }
p.header { font-size:24px; color:#666666; margin-top:20px; padding-bottom:0px; margin-bottom:0px; }
.buttons { text-align:center; }
.buttons button{ margin:0 7px 0 0; background-color:#f5f5f5;    color:#565656; border:1px solid #dedede; border-top:1px solid #eee; border-left:1px solid #eee; font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif; font-size:100%;    line-height:130%; text-decoration:none; font-weight:normal; cursor:pointer; }
.buttons button{ width:220px; overflow:visible; padding:4px 10px 3px 7px; /* IE6 */ }
.buttons button[type]{ padding:5px 10px 5px 7px; /* Firefox */ line-height:17px; /* Safari */ }
*:first-child+html button[type]{ padding:4px 10px 3px 7px; /* IE7 */ }
.buttons button img, .buttons a img{ margin:0 3px -3px 0 !important; padding:0; border:none; width:16px; height:16px; }
#hor-minimalist-b { font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; font-size: 14px; background: #fff; margin-top: 40px; margin-bottom: 40px; width:100%; min-width: 1000px; border-collapse: collapse; text-align: left; line-height:1.1em; }
#hor-minimalist-b th { text-align:center; font-size: 16px; font-weight: bold; color: #000; padding: 0px 8px; border-bottom: 2px solid #555; }
#hor-minimalist-b td { text-align:left; border-bottom: 1px solid #ccc; color: #000; padding: 0px 0px; font-size:14px; vertical-align:top; }
#hor-minimalist-b td.topicName { width:100px; font-weight:bold; }
td.assignedEmployees { width:100px; }
td.dueDate, td.closureDate { width:100px; }
#hor-minimalist td.status { width:70px; text-align:center; }
#hor-minimalist-b span.red { color:red; }
</style>
</head>
<body>
HEREDOC;

	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	$meetingID = mysql_real_escape_string($_GET['meetingID']);

	# Get the date of the meeting
	$query =	"SELECT DATE(meetingDate) meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$meetingDate = $row['meetingDate'];

	date_default_timezone_set('America/Vancouver');
	$meetingDate = new DateTime(mysql_real_escape_string($row['meetingDate']));
	$formattedMeetingDate = $meetingDate->format('Y-M-d');

	$html_output .= "<h1>Lab meeting minutes: $formattedMeetingDate</h1>\n\n";
	$html_output .=	"<p class='header'>1. Attendance</p>\n\n";

	# Get attendance (absent people will be at top of the list)
	$query =	"SELECT CONCAT(firstName, ' ', SUBSTRING(lastName,1,1)) employeeName, attendance " .
			"FROM Meetings NATURAL JOIN Attendance NATURAL JOIN Employees " .
			"WHERE meetingID LIKE '$meetingID' ORDER BY attendance DESC";

	$dbHandle = mysql_query($query) or die(mysql_error());

	$html_output .= "<div class='wrapper'>\n";
	$html_output .= "<ul class='six'>\n";

	# Display the attendance
	$counter = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		$employeeName = $row['employeeName'];
		$attendance = $row['attendance'];
		$html_output .= "\t<li>$employeeName</li><li>$attendance</li>\n";
		$counter++;
		}
	$counter = $counter % 3;

	while (($counter < 3) && ($counter != 0)) {
		$html_output .= "\t<li>&nbsp</li><li>&nbsp</li>\n";
		$counter++;
		}
	$html_output .= "</ul>\n";
	$html_output .= "</div>\n\n";

	# Get the date of the previous meeting (Look for max date meeting before this meeting)
	$query = 	"SELECT MAX(DATE(meetingDate)) mostRecent " .
			"FROM Meetings " .
			"WHERE meetingDate < (SELECT meetingDate FROM Meetings WHERE meetingID LIKE '$meetingID');";	
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$recentDate = new DateTime($row['mostRecent']);
	$formattedRecentDate = $recentDate->format('Y-M-d');

	$html_output .= "<p class='header'>2. Review of items from $formattedRecentDate</p>\n";

	$html_output .=	"<table id='hor-minimalist-b'>\n\n" .
			"<thead>\n" .
			"\t<tr>\n" .
			"\t\t<th scope='col'>No.</th>\n" .
			"\t\t<th scope='col'>Actions/Decisions</th>\n" .
			"\t\t<th scope='col'>Responsible</th>\n" .
			"\t\t<th scope='col'>Due date</th>\n" .
			"\t\t<th scope='col'>Status</th>\n" .
			"\t\t<th scope='col'>Closed</th>\n" .
			"\t</tr>\n" .
			"</thead>\n\n" .
			"<tbody>\n";

	# Display the old notes for this meeting
	$query =	"SELECT topicID, topicName, noteID, actionDescription, status, priority, dueDate, closureDate " .
			"FROM MeetingNote NATURAL JOIN Topics " .
			"WHERE meetingID LIKE '$meetingID' AND new LIKE 'old' ORDER BY topicID";

	$dbHandle = mysql_query($query) or die(mysql_error());

	$previousTopic = "";
	$counter = 0;
	while($row = mysql_fetch_array($dbHandle)) {
		$noteID = $row['noteID'];	$topicName = $row['topicName'];
		$actionDescription = preg_replace('/\n/', '<br/><br/>', $row['actionDescription']);
		$status = $row['status'];	$priority = $row['priority'];	$dueDate = $row['dueDate'];	$closureDate = $row['closureDate'];

		$formattedClosureDate = '';
		if ($closureDate != '') {
			$closureDate = new DateTime($row['closureDate']);
			$formattedClosureDate = $closureDate->format('Y-M-d');
			}

		$formattedDueDate = '';
		if ($dueDate != '') {
			$dueDate = new DateTime($row['dueDate']);
			$formattedDueDate = $dueDate->format('Y-M-d');
			}

		if ($priority == 'critical') { $status = $status . " (<i>Critical</i>)"; }
 		if ($previousTopic != $topicName) { $counter++; }	$previousTopic = $topicName;

		# Get the employees assigned to each note
		$query2 = 	"SELECT CONCAT(firstName, ' ', SUBSTRING(lastName,1,1)) name " .
				"FROM AssignedEmployees NATURAL JOIN Employees " .
				"WHERE noteID LIKE '$noteID'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$assignedEmployees = "";
		while($row2 = mysql_fetch_array($dbHandle2)) {
			if ($assignedEmployees == '') { $assignedEmployees = $row2['name']; }
			else {  $assignedEmployees = $assignedEmployees . "; " . $row2['name']; }
			}
		$html_output .=	"\t<tr>\n" .
				"\t\t<td class='topicName'>($counter)<br/>$topicName</td>\n" .
				"\t\t<td class='actionDescription'>$actionDescription</td>\n" .
				"\t\t<td class='assignedEmployees'>$assignedEmployees</td>\n" .
				"\t\t<td class='dueDate'>$formattedDueDate</td>\n" .
				"\t\t<td class='status'>$status</td>\n" .
				"\t\t<td class='closureDate'>$formattedClosureDate</td>\n" .
				"\t</tr>\n";
		}
	$html_output .=	"</tbody>\n\n" .
			"</table>\n\n" .
			"<p class='header'>3. New business for $formattedMeetingDate </p>\n\n";

	$html_output .= "<table id='hor-minimalist-b'>\n" .
			"<thead>\n" .
			"\t<tr>\n" .
			"\t\t<th scope='col'>No.</th>\n" .
			"\t\t<th scope='col'>Actions/Decisions</th>\n" .
			"\t\t<th scope='col'>Responsible</th>\n" .
			"\t\t<th scope='col'>Due date</th>\n" .
			"\t\t<th scope='col'>Status</th>\n" .
			"\t\t<th scope='col'>Closed</th>\n" .
			"\t</tr>\n" .
			"</thead>\n\n" .
			"<tbody>\n\n";

	$query =	"SELECT topicID, topicName, noteID, actionDescription, status, priority, dueDate, closureDate " .
				"FROM MeetingNote NATURAL JOIN Topics " .
				"WHERE meetingID LIKE '$meetingID' AND new LIKE 'new' ORDER BY topicID";

	$dbHandle = mysql_query($query) or die(mysql_error());

	$previousTopic = "";
	$counter = 0;

	while($row = mysql_fetch_array($dbHandle)) {
		$noteID = $row['noteID'];       $topicName = $row['topicName'];         $actionDescription = $row['actionDescription'];
		$status = $row['status'];       $priority = $row['priority'];   	$dueDate = $row['dueDate'];
		$closureDate = $row['closureDate'];
		$formattedClosureDate = '';
		if ($closureDate != '') {
			$closureDate = new DateTime($row['closureDate']);
			$formattedClosureDate = $closureDate->format('Y-M-d');
			}

		$formattedDueDate = '';
		if ($dueDate != '') {
			$dueDate = new DateTime($row['dueDate']);
			$formattedDueDate = $dueDate->format('Y-M-d');
			}

		if ($priority == 'critical') { $status = $status . " (<i>Critical</i>)"; }

		if ($previousTopic != $topicName) { $counter++; }       $previousTopic = $topicName;

		$query2 = 	"SELECT CONCAT(firstName, ' ', SUBSTRING(lastName,1,1)) name " .
					"FROM AssignedEmployees NATURAL JOIN Employees WHERE noteID LIKE '$noteID'";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$assignedEmployees = "";
		while($row2 = mysql_fetch_array($dbHandle2)) {
			if ($assignedEmployees == '') { $assignedEmployees = $row2['name']; }
			else {  $assignedEmployees = $assignedEmployees . "; " . $row2['name']; }
			}
		$html_output .=	"\t<tr>\n" .
				"\t\t<td class='topicName'>($counter)<br/>$topicName</td>\n" .
				"\t\t<td class='actionDescription'>$actionDescription</td>\n" .
				"\t\t<td class='assignedEmployees'>$assignedEmployees</td>\n" .
				"\t\t<td class='dueDate'>$formattedDueDate</td>\n" .
				"\t\t<td class='status'>$status</td>\n" .
				"\t\t<td class='closureDate'>$formattedClosureDate</td>\n" .
				"\t</tr>\n";
		}

	$html_output .=	"</tbody>\n\n" .
					"</table>\n\n" .
					"</body>\n" .
					"</html>";

	# Prepare to write the html report
	$fileName = $meetingDate->format('Y-M-d');
	$filePath = "meeting_minutes/$fileName.html";

	# If report already exists, regenerate it, but don't email it
	if (file_exists($filePath)) {
		unlink($filePath);
		file_put_contents($filePath, $html_output);
		header("Location: $filePath");
		die;
		}

	# Otherwise, generate the file and email it out
	file_put_contents($filePath, $html_output);


	########## NOW, UPLOAD THE MINUTES TO THE QAI ##########
	$host = "192.168.67.9";
	$db_username = "qcs";
	$db_password = "qcs08mar05";
	$sid = "CFE9ir2";       $db = "oci:dbname=$sid";
	$conn = oci_connect("$db_username", "$db_password", "//$host/$sid");
	if (!$conn) { $m = oci_error(); echo $m['message'], "\n"; exit; }

	# FIXME: IF THERE ALREADY EXISTS THIS ID, THEN DELETE IT AND REUPLOAD UNDER THE SAME PRIMARY KEY!!
	$query =	"INSERT INTO specimen.QCS_Minutes (ID, FILENAME, CONTENT) " .
				"VALUES (specimen.QCS_MINUTES_SEQ.nextval, 'Minutes_$fileName.html', :blobdata)";

	$s = oci_parse($conn, $query);

	# Define a lob, bind it to :blobdata as a blob, and then give the lob contents
	$lob = oci_new_descriptor($conn, OCI_D_LOB);
	oci_bind_by_name($s, ':blobdata', $lob, -1, OCI_B_BLOB);
	$lob->writeTemporary($html_output, OCI_TEMP_BLOB);

	# Execute the SQL with variable binding
	oci_execute($s, OCI_DEFAULT);
	oci_commit($conn);
	$lob->close();
	oci_close($conn);

	########## NOW, EMAIL STAFF PARTICULAR DELIVERABLES ##########
	$date = $meetingDate->format('Y-M-d');

	# From the set of people who were expected to be at this meeting...
	$query = 	"SELECT employeeID, firstName, email " .
				"FROM Attendance NATURAL JOIN Employees " .
				"WHERE meetingID LIKE '$meetingID' ";
	$dbHandle = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_array($dbHandle)) {
		$employeeID = $row['employeeID'];
		$firstName = $row['firstName'];
		$email = $row['email'];

		# Get any tasks that they assigned
		$query2 =	"SELECT topicID, topicName, actionDescription, dueDate " .
					"FROM MeetingNote NATURAL JOIN AssignedEmployees NATURAL JOIN Topics " .
					"WHERE meetingID LIKE '$meetingID' AND employeeID LIKE '$employeeID' " .
					"AND MeetingNote.status NOT LIKE 'closed' " .
					"AND DATEDIFF(dueDate, DATE(NOW())) <= 7";
		$dbHandle2 = mysql_query($query2) or die(mysql_error());
		$employeeActions = "";
		$counter = 1;

		# Concatenate to the email report
		while ($row2 = mysql_fetch_array($dbHandle2)) {
			$topicID = $row2['topicID'];
			$topicName = $row2['topicName'];
			$actionDescription = preg_replace('/\n/', '<br/><br/>', $row2['actionDescription']);
			$actionDueDate = new DateTime($row2['dueDate']);
			$formattedDate = $actionDueDate->format('Y-M-d');

			$employeeActions = $employeeActions . 	"<p><b>Topic #$counter</b>: $topicName (Due $formattedDate)</p>\n" .
													"<p><b>Action</b>: $actionDescription</p>\n" .
													"<hr/>\n";
			$counter++;
			}
		#if ($employeeActions == "") { continue; }

		$to = "$email";
		$subject = "$date meeting minutes";

		$message = 	"<html><body>" .
				"<p>Dear $firstName, the $date meeting minutes has been posted to the QAI at http://192.168.68.61:3000/qcs_minutes/list</p>" .
				"<p>Meeting minutes that are due within 1 week and assigned to you are listed below. " .
				"<hr/><br/>$employeeActions</p>" .
				"</body></html>";

		$headers  = 'MIME-Version: 1.0' . "\r\n";				# Needed for HTML mail
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	# Needed for HTML mail
		$headers .= 'From: Meeting Minutes <meeting_minutes@cfenet.ubc.ca>' . "\r\n";

		mail($to, $subject, $message, $headers);
		}

	header("Location: $filePath");
	exit();
?>

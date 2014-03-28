<?php
    session_start();
    if (!$_SESSION['signed_in']) {
        header("Location: sign_in.html");
        exit;
        }
?>

<?php
	# The database connection must be initiated before mysql_real_escape_string is operable - necessary to prevent SQL injection
        $link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
        if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db("scheduling") or die(mysql_error());

	$employeeID = mysql_real_escape_string($_POST['employeeID']);
	$firstName = mysql_real_escape_string($_POST['firstName']);
	$lastName = mysql_real_escape_string($_POST['lastName']);
	$email = mysql_real_escape_string($_POST['email']);
	$employeeStatus = mysql_real_escape_string($_POST['employeeStatus']);

	$query = 	"UPDATE Employees SET firstName='$firstName', lastName='$lastName', email='$email', status='$employeeStatus' " .
			"WHERE employeeID LIKE '$employeeID'";

	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);

	header("Location: manageEmployees.php");
	exit();
?>

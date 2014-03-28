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
	<script type="text/javascript" src="javascript/editEmployeeButtons.js"></script>
</head>
<body>

<?php
	# SQL connection must be made before mysql_real_escape_string can be used to prevent SQL injection
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db("scheduling") or die(mysql_error());

	$employeeID = mysql_real_escape_string($_GET['employeeID']);

	$query = "SELECT firstName, lastName, email, status FROM Employees WHERE employeeID LIKE '$employeeID'";
	$dbHandle = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($dbHandle);
	$firstName = $row['firstName']; $lastName = $row['lastName']; $email = $row['email']; $status = $row['status'];


print<<<HTML
<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="editEmployee2.php">
	<h1>Updating employee</h1>
	<p>Enter new employee information</p>

	<input type"text" class="hidden" name="employeeID" id="employeeID" value="$employeeID"/>

	<label>First name</label>
	<input type="text" class="firstName" name="firstName" id="firstName" value="$firstName"/>

	<label>Last name</label>
	<input type="text" class="lastName" name="lastName" id="lastName" value="$lastName"/>

	<label>Email</label>
	<input type="text" class="email" name="email" id="email" value="$email"/>

	<label>Status</label>
HTML;

	print '<select name="employeeStatus" id="employeeStatus">';
	if ($status == 'active') { print "<option value='active' selected>Active</option><option value='inactive'>Inactive</option>"; }
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

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
	<link rel="stylesheet" type="text/css" href="css/manageEmployeesTable.css">
	<link rel="stylesheet" type="text/css" href="css/manageEmployeesButtons.css">
	<script type="text/javascript" src="javascript/manageEmployeesButtons.js"></script>
	<script type="text/javascript" src="javascript/manageEmployeesCheckBox.js"></script>
</head>

<body>

<?php
	$link = mysql_connect('127.0.0.1', 'task_tracker', 'task_tracker');
	if (!$link) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db('scheduling') or die(mysql_error());

	print   "<table id='hor-minimalist-b'>\n" .
		"<thead>\n" .
		"\t<tr>\n" .
		"\t\t<th scope='col' class='employeeID'>EmployeeID</th>\n" .
		"\t\t<th scope='col'>First name</th>\n" .
		"\t\t<th scope='col'>Last name</th>\n" .
		"\t\t<th scope='col'>Email</th>\n" .
		"\t\t<th scope='col'>Status</th>\n" .
		"\t\t<th></th>\n" .
		"\t</tr>\n" .
		"</thead>\n\n";

	$query = "SELECT employeeID, firstName, lastName, email, status FROM Employees";
	$dbHandle = mysql_query($query) or die(mysql_error());

	print	"<tbody>\n";

	while($row = mysql_fetch_array($dbHandle)) {
		$employeeID = $row['employeeID'];	$firstName = $row['firstName'];		$lastName = $row['lastName'];
		$email = $row['email'];			$status = $row['status'];

		print   "\t<tr>\n" .
			"\t\t<td class='employeeID'>$employeeID</td>\n" .
			"\t\t<td class='firstName'>$firstName</td>\n" .
			"\t\t<td class='lastName'>$lastName</td>\n" .
			"\t\t<td>$email</td>\n" .
			"\t\t<td class='status'>$status</td>\n" .
			"\t\t<td class='selector'>" .
				"<input type='checkbox' class='selector' value='1' name='selected_$employeeID' onClick='singleCheckBoxSelection(this)'></td>\n" .
			"\t</tr>\n";
		}

	print	"</tbody>\n";
	print	"</table>\n";
?>

<div class="buttons">
	<button type="submit" class="positive" onclick="addEmployee()"> <img src="images/addNew.png" alt=""/> Add employee</button>
	<button type="submit" class="positive" onclick="editEmployee()"> <img src="images/authorize.png" alt=""/> Edit employee</button>
	<button type="submit" class="positive" onclick="mainMenu()"> <img src="images/logout.png" alt=""/> Return</button>
</div>

</body>
</html>

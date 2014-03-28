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
	<link rel="stylesheet" type="text/css" href="css/addEmployeeInputForm.css">
	<link rel="stylesheet" type="text/css" href="css/addEmployeeButtons.css">
	<script type="text/javascript" src="javascript/addEmployeeButtons.js"></script>
</head>
<body>

<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="addEmployee2.php">
	<h1>New employee</h1>
	<p>Enter employee name and email</p>

	<label>First name</label>
	<input type="text" class="firstName" name="firstName" id="firstName" />

	<label>Last name</label>
	<input type="text" class="lastName" name="lastName" id="lastName" />

	<label>Email</label>
	<input type="text" class="email" name="email" id="email" />

	<label>Status</label>
	<select name="employeeStatus" id="employeeStatus">
		<option value='active' selected>Active</option>
		<option value='inactive'>Inactive</option>
	</select>

	<div class="buttons">
		<button type="submit" class="positive" onclick="return addEmployee()"> <img src="images/check.png" alt=""/> Add</button>
		<button type="button" class="positive" onclick="cancel()"> <img src="images/cross.png" alt=""/> Cancel</button>
	</div>

</form>
</div>

</body>
</html>

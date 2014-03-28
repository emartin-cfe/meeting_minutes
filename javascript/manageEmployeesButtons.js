function addEmployee() {
	window.location = "addEmployee.php";
	}

function editEmployee() {

	if (window.selectedEmployeeID==undefined) {
		alert("Select an employee");
		return false;
		}

	window.location = "editEmployee.php?employeeID=" + window.selectedEmployeeID;
	}

function mainMenu() {
	window.location = "displayMeetings.php";
	}

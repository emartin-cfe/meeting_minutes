function addEmployee() {
	firstName = document.getElementById("firstName").value;
	lastName = document.getElementById("lastName").value;
	if ((firstName == "") || (lastName == "")) { alert("Please fill in both name fields"); return false; }
	return true;
	}

function cancel() {
	window.location = "displayMeetings.php";
	}

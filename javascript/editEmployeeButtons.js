function editEmployee() {
	firstName = document.getElementById("firstName").value;
	lastName = document.getElementById("lastName").value;
	email = document.getElementById("email").value;
	var re = /\S+@\S+\.\S+/;
	if ( (!re.test(email)) && (!email == "")) { alert("Invalid email"); return false; }
	if ((firstName == "") || (lastName == "")) { alert("Please fill in both name fields"); return false; }
	return true;
	}

function cancel() {
	window.location = "manageEmployees.php";
	}

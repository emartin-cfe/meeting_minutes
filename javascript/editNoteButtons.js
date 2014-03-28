function editNote() {
	document.getElementById("dueDate").value = document.getElementById("popupDateField").innerHTML;
	return true;
	}

function cancel(meetingID) {
	window.location = "displayTasks.php?meetingID=" + meetingID;
	}

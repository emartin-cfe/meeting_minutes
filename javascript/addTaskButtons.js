function addTask(meetingID) {
	var e = document.getElementById("topicID");
	if(e.options[e.selectedIndex].text == "") { alert("Select a topic!"); return false; }

	if (document.getElementById("actionDescription").value == "") { alert("Enter an action/decision!"); return false; }

	// Mirror the value of the div containing the graphical calendar over to the hidden input field dueDate
	// So that the dueDate is included in the subsequent POST event
	document.getElementById("dueDate").value = document.getElementById("popupDateField").innerHTML;
	window.location = "addTask.php?meetingID=" + meetingID;
	return true;
	}

function cancel(meetingID) {
	window.location = "displayTasks.php?meetingID=" + meetingID;
	}

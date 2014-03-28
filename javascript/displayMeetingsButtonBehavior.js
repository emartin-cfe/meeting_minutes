// This is to add an entirely new experiment for validation
function addNewMeeting() {
	window.location = "addMeeting1.php";
	}

function editMeeting() {
	if (window.selectedMeetingID==undefined) {
		alert("Select a meeting");
		return false;
		}

	window.location = "displayTasks.php?meetingID=" + window.selectedMeetingID;
	}

function deleteMeeting() {
	if (window.selectedMeetingID==undefined) {
		alert("Select a meeting");
		return false;
		}

	if (confirm("DELETE meeting?")) {
		window.location = "deleteMeeting.php?meetingID=" + window.selectedMeetingID;
		}
	}

function manageEmployees() {
	window.location = "manageEmployees.php";
	}

function report() {
	window.location = "meeting_minutes"
	}

function topics() {
	window.location = "manageTopics.php";
	}

function logout() {
	window.location = "http://192.168.68.32";
	}

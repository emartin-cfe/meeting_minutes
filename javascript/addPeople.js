// This is so the addUser and removeUser functions can work properly
function initialize (meetingID, noteID) {
	window.selectedMeetingID = meetingID;
	window.selectedTaskID = noteID;
	}

// Launch a PHP page which adds this user and immediately reloads this page
function addUser (e) {
	meetingID = window.selectedMeetingID;
	noteID = window.selectedTaskID;
	newUser = e.innerHTML;
	window.location = "addPeople2A.php?meetingID=" + meetingID + "&noteID=" + noteID + "&user=" + newUser;
	}

// Launch a PHP page which removes this user and immediately reloads this page
function removeUser (e) {
	meetingID = window.selectedMeetingID;
	noteID = window.selectedTaskID;
	newUser = e.innerHTML;
	window.location = "addPeople2B.php?meetingID=" + meetingID + "&noteID=" + noteID + "&user=" + newUser;
	}

function returnToTask (noteID) {
	meetingID = window.selectedMeetingID;
	noteID = window.selectedTaskID;
	window.location = "displayTasks.php?meetingID=" + meetingID;
	}

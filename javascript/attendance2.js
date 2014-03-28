// This javascript window variable is needed for employeePresent and employeeAbsent 
// related PHP scripts know the meetingID
function initialize (meetingID) {
	window.selectedMeetingID = meetingID;
	}

// Update Attendance table to register employee as present at this meeting
function employeePresent (e) {
	meetingID = window.selectedMeetingID;
	employee = e.innerHTML;
	window.location = "attendance3A.php?meetingID=" + meetingID + "&employee=" + employee;
	}

// Update Attendance table to register employee as absent from this meeting
function employeeAbsent (e) {
	meetingID = window.selectedMeetingID;
	employee = e.innerHTML;
	window.location = "attendance3B.php?meetingID=" + meetingID + "&employee=" + employee;
	}

// When attendance is complete, launch directly to the meeting administration page
function save () {
	window.location = "displayTasks.php?meetingID=" + window.selectedMeetingID;
	}

function initialize(meetingID) {
	window.selectedMeetingID = meetingID;
	}

function addTask() {
	window.location = "addNote.php?meetingID=" + window.selectedMeetingID;
	}

function editTask() {
	if (window.selectedNoteID==undefined) {
		alert("Select a note");
		return false;
		}

	window.location = "editNote.php?noteID=" + window.selectedNoteID + "&meetingID=" + window.selectedMeetingID;
	}

function deleteTask() {

	if (window.selectedNoteID==undefined) {
		alert("Select a note");
		return false;
		}

	if (confirm("DELETE this note?")) {
		window.location = "deleteNote.php?noteID=" + window.selectedNoteID + "&meetingID=" + window.selectedMeetingID;
		}
	}

function addPeople() {
	if (window.selectedNoteID==undefined) {
		alert("Select a note");
		return false;
		}
	window.location = "addPeople.php?noteID=" + window.selectedNoteID + "&meetingID=" + window.selectedMeetingID;
	}

function topics() {
	window.location = "manageTopics.php?meetingID=" + window.selectedMeetingID;
	}

function emailStaff() {
	if(confirm("Generate minutes and email staff?")) {
		window.location = "generateReport.php?meetingID=" + window.selectedMeetingID;
		};
	}

function mainMenu() {
	window.location = "displayMeetings.php";
	}

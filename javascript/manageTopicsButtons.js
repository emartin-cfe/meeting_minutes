function initialize(meetingID) {
	window.selectedMeetingID = meetingID;
	}

function addTopic() {
	window.location = "addTopic.php?meetingID=" + window.selectedMeetingID;
	}

function editTopic() {

	if (window.selectedTopicID==undefined) {
		alert("Select a topic");
		return false;
		}

	window.location = "editTopic.php?topicID=" + window.selectedTopicID + "&meetingID=" + window.selectedMeetingID;
	}

function mainMenu() {
	window.location = "displayTasks.php?meetingID=" + window.selectedMeetingID;
	}

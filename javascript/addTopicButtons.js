function initialize (meetingID) {
	window.selectedMeetingID = meetingID;
	}

function addTopic() {
	topicName = document.getElementById("topicName").value;
	topicStatus = document.getElementById("topicStatus").value;
	if (topicName == "") { alert("Please fill in a topic name"); return false; }
	return true;
	}

function cancel() {
	window.location = "manageTopics.php?meetingID=" + window.selectedMeetingID;
	}

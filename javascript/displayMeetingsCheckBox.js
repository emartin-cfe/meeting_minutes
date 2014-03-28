function singleCheckBoxSelection(e) {
	var elements = document.getElementsByClassName('selector');

	var count = 0;
	while (count < elements.length) {
		elements[count].checked = false;
		count++;
		}

	e.checked = true;

	// The checkbox name encodes the experimentID within it
	var checkBoxName = e.name;
	var meetingID = checkBoxName.replace("selected_", "");

	// This will be used for the add content and delete functions
	window.selectedMeetingID = meetingID;	
	}

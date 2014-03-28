// Only allow a SINGLE checkbox in the list to be checked at once
function singleCheckBoxSelection(e) {

	// Before the onclick event occurs, unclick all checkboxes
	var elements = document.getElementsByClassName('selector');
	var count = 0;
	while (count < elements.length) {
		elements[count].checked = false;
		count++;
		}

	// Then set this checkbox as selected
	e.checked = true;

	// The checkbox name encodes the experimentID within it
	var checkBoxName = e.name;
	var noteID = checkBoxName.replace("selected_", "");

	// The selected noteID is stored as a javascript window variable
	window.selectedNoteID = noteID;	
	}

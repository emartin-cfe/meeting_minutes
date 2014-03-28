// This is so the addUser and removeUser functions can work properly
function initialize (minDate) {
	window.minDate = minDate;
	}

function setDate () {
	selectedDate = document.getElementById("popupDateField").innerHTML;		// Date of the current meeting being created
	minDate = window.minDate;							// Date of previous meeting

	var array1 = selectedDate.split("-");						// Split and re-instantiate as formal Date objects
	var year1 = array1[0];	var month1 = array1[1];	var day1 = array1[2];		// So that the compare operator can be used
	var date1 = new Date(year1, month1, day1, 0, 0, 0, 0);
	var array2 = minDate.split("-");
	var year2 = array2[0];	var month2 = array2[1];	var day2 = array2[2];
	var date2 = new Date(year2, month2, day2, 0, 0, 0, 0);

	if (date1.getTime() <= date2.getTime()) {			// Compare dates to see if new meeting is after the previous meeting
		alert("Your meeting must have occured after the previous meeting that took place");
		return false;
		}

	window.location = "addMeeting2.php?date=" + selectedDate;
	}

function cancel () {
	window.location = "displayMeetings.php";
	}

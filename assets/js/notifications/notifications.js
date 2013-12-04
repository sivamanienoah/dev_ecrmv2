/*
 *@notifications Jquery
 *@My Profile Module
*/

// 'accesspage' is global variable 
	
function toggleCheckbox(obj) {
		if(obj.checked) 
			document.getElementById("no_of_days").disabled = false;
		else 
			document.getElementById("no_of_days").disabled = true;
}

/////////////////
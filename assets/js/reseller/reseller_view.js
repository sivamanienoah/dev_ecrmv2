/*
 *@notifications Jquery
 *@My Profile Module
*/

// 'accesspage' is global variable 
	
$(document).ready(function() {
	$( "#reseller_tabs" ).tabs({
		beforeActivate: function( event, ui ) {
			if (ui.newPanel[0].id=='rt-tab-1')
				// loadExistingTasks();
			if (ui.newPanel[0].id=='rt-tab-2') {
				// populateJobOverview();
			}
			if (ui.newPanel[0].id=='rt-tab-3') {
			}
			if (ui.newPanel[0].id=='rt-tab-4') {
			}
			if (ui.newPanel[0].id=='rt-tab-5') {
			}
			if (ui.newPanel[0].id=='rt-tab-6') {
			}
		}
	});
});

/////////////////
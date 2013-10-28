/*
 *@notifications Jquery
 *@My Profile Module
*/

// 'accesspage' is global variable 
	
if(accesspage==1) { 
	$(function() {
		$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
		.tablesorterPager({container: $("#pager"),positionFixed: false});
		$('.data-table tr, .data-table th').hover(
			function() { $(this).addClass('over'); },
			function() { $(this).removeClass('over'); }
		);
	});
} 

function toggleCheckbox(obj) {
		if(obj.checked) 
			document.getElementById("no_of_days").disabled = false;
		else 
			document.getElementById("no_of_days").disabled = true;
}

/////////////////
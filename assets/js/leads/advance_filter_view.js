/*
 *@Advance Filter View 
*/

$('#excel').click(function() {
		//mychanges
		var sturl = "welcome/excelExport/";
		document.location.href = sturl;
		return false;
});

$(function(){
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
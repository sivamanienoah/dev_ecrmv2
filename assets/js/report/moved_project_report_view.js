/*
 *@Active Lead Report View
 *@Welcome Controller
*/

$(function(){
	$('#excel').click(function() {		
		var start_date = $('#project_search_start_date').val();
		var end_date = $('#project_search_end_date').val();
		var practices = $('#practices').val();
		practices = practices+"";
		var divisions = $('#divisions').val();
		divisions = divisions+"";
		
		var base_url = site_base_url;

		var url = base_url+"report/report_moved_project/excelExport";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input type="hidden" name="start_date" value="' +start_date+ '" />' +
		  '<input type="hidden" name="end_date" value="' +end_date+ '" />' +
		  '<input type="hidden" name="practices" value="' +practices+ '" />' +
		  '<input type="hidden" name="divisions" value="' +divisions+ '" />' +
		  '</form>');
		$('body').append(form);
		$(form).submit();
			
		//var sturl = base_url+"report/report_lead_region/excelExport/"+start_date+"/"+end_date+"/"+stage+"/"+customer+"/"+worth+"/"+owner+"/"+leadassignee;
		//document.location.href = sturl;
		
		//$('#advance_search_results').load(sturl);	
		return false;
	});
	
	
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
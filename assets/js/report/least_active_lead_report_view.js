/*
 *@Least Active Lead Report
*/

$(function(){
	$('#excel').click(function() {
		var start_date   = $('#task_search_start_date').val();
		var end_date     = $('#task_search_end_date').val();
		var stage        = $('#stage').val();
		var customer     = $('#customer').val();
		var worth        = $('#worth').val();
		var owner        = $('#owner').val();		
		var leadassignee = $('#leadassignee').val();

		var regionname   = $('#regionname').val();			
		var countryname  = $('#countryname').val();		
		var statename    = $('#statename').val();		
		var locname      = $('#locname').val();
		
		var base_url = site_base_url;

		var url = base_url+"report/report_least_active_lead/excelExport";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input type="hidden" name="start_date" value="' +start_date+ '" />' +
		  '<input type="hidden" name="end_date" value="' +end_date+ '" />' +
		  '<input type="hidden" name="stage" value="' +stage+ '" />' +
		  '<input type="hidden" name="customer" value="' +customer+ '" />' +
		  '<input type="hidden" name="worth" value="' +worth+ '" />' +
		  '<input type="hidden" name="owner" value="' +owner+ '" />' +
		  '<input type="hidden" name="leadassignee" value="' +leadassignee+ '" />' +

		  '<input type="hidden" name="regionname" value="' +regionname+ '" />' +
		  '<input type="hidden" name="countryname" value="' +countryname+ '" />' +
		  '<input type="hidden" name="statename" value="' +statename+ '" />' +
		  '<input type="hidden" name="locname" value="' +locname+ '" />' +		  
		  '</form>');
		$('body').append(form);
		$(form).submit();
			
		return false;
	});
		
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
	
});

	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
/*
 *@Least Active Lead Report
*/

$(function(){
	$('.advanced_filter').click(function(){
		$('#advance_search').slideToggle('slow');
	});
	
	$('#project_search_start_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
		 		var date2 = $('#project_search_start_date').datepicker('getDate');
        		$("#project_search_end_date").datepicker("option","minDate", date2);
     		 }
	});

	$('#project_search_end_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
			var date1 = $('#project_search_end_date').datepicker('getDate');							
			$("#project_search_start_date").datepicker("option","maxDate", date1);
		 }
	});
	 
	
	$('#report_moved_project_frm').submit(function(e){
		e.preventDefault();		
		$('#advance').hide();
		$('#load').show();
		var base_url = site_base_url; //site_base_url is global variable 		
		var start_date = $('#project_search_start_date').val();
		var end_date = $('#project_search_end_date').val();
		var practices = $('#practices').val();
		practices = practices+"";
		var divisions = $('#divisions').val();
		divisions = divisions+"";
		
		var params = {start_date:start_date,end_date:end_date,practices:practices,divisions:divisions};
		params[csrf_token_name] = csrf_hash_token; 
		
		// $('#report_error_msg').html('');
		
		/* if(start_date!='' || end_date!='')
		{ */
			$('#report_grid').load(base_url+'report/report_moved_project/get_moved_project_report',params,function(){
				$('#advance').show();
				$('#load').hide();	
			});
		/*}
		 else
		{
			$('#report_error_msg').html('Please select From date or To date');
			$('#advance').show();
			$('#load').hide();
		} */
	});
});

	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
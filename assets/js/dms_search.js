function download_files(job_id,f_name){
		window.location.href = site_base_url+'project/download_file/'+job_id+'/'+f_name;
	}
	
	$(document).ready(function(){
		var full_project = $('#projects').html();
		
		 $('.js_advanced_filter').click(function(){
			 $('#advance_filters').slideToggle();
		 })
		 
		 $("#customers").change(function(){
			var cst = $(this).val();
			if(cst){
				var params = {'customers':cst};
				params[csrf_token_name] = csrf_hash_token;				
				var obj;
				var str = '';
				$.ajax({
					type: 'POST',
					url: site_base_url+'dms_search/get_projects',
					data: params,
					success: function(data) {
						if(data){
							$('#projects').html('');
							obj = $.parseJSON(data);
							for(var i=0;i<obj.length;i++){								 
								str += "<option value='"+obj[i].lead_id+"'>"+obj[i].lead_title+"</option>"
							}							 
							$('#projects').html(str);
						}
						
					}
				});
				return false;								
			}else{
				$('#projects').html(full_project);
			}
		 })
		 
		 $('.js_reset').click(function(){
			 $('#projects').html(full_project);
		 })
		 
		 $('#dmssearch').submit(function(){
			 
			 $('#advance').hide();
			 $('#load').show();
			 $('#ajax_loader').show();
			 $('#default_view').html('');
			 $('#default_view').hide()
			 var keyword = $("#keyword").val();			 
			 var tag_keyword = $("#tag_keyword").val();			 
			 var customers = $("#customers").val();			 
			 var projects = $("#projects").val();			 
			 var extension = $("#extension").val();			 
			 var from_date = $("#from_date").val();			 
			 var to_date = $("#to_date").val();
			 var params = {'keyword':keyword,'tag_keyword':tag_keyword,'customers':customers,'projects':projects,'extension':extension,'from_date':from_date,'to_date':to_date};
			params[csrf_token_name] = csrf_hash_token;
		    
			$.ajax({
		        type: 'POST',
		        url: site_base_url+'dms_search/search',
		        data: params,
		        success: function(data) {					
					$('#load').hide();
					 $('#advance').show();
					$('#ajax_loader').hide();					
					$('#default_view').html(data);						
					$('.data-tbl').dataTable();
					$('#default_view').show();
					
		        }
		    });
		    return false;			
		 })
	})
	
	$(function() {
		// $('#from_date, #to_date').datepicker({dateFormat: 'dd-mm-yy'});
		$('#from_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
			if($('#to_date').val!='')
			{
				$('#to_date').val('');
			}
			var return_date = $('#from_date').val();
			$('#to_date').datepicker("option", "minDate", return_date);
		}});
		$('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	});
	
	
(function () {
 
var customDateDDMMMYYYYToOrd = function (date) {
    "use strict"; //let's avoid tom-foolery in this function
    // Convert to a number YYYYMMDD which we can use to order
    var dateParts = date.split(/-/);
    return (dateParts[2] * 10000) + ($.inArray(dateParts[1].toUpperCase(), ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"]) * 100) + (dateParts[0]*1);
};
 
// This will help DataTables magic detect the "dd-MMM-yyyy" format; Unshift
// so that it's the first data type (so it takes priority over existing)
jQuery.fn.dataTableExt.aTypes.unshift(
    function (sData) {
        "use strict"; //let's avoid tom-foolery in this function
        if (/^([0-2]?\d|3[0-1])-(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)-\d{4}/i.test(sData)) {
            return 'date-dd-mmm-yyyy';
        }
        return null;
    }
);
 
// define the sorts
jQuery.fn.dataTableExt.oSort['date-dd-mmm-yyyy-asc'] = function (a, b) {
    "use strict"; //let's avoid tom-foolery in this function
    var ordA = customDateDDMMMYYYYToOrd(a),
        ordB = customDateDDMMMYYYYToOrd(b);
    return (ordA < ordB) ? -1 : ((ordA > ordB) ? 1 : 0);
};
 
jQuery.fn.dataTableExt.oSort['date-dd-mmm-yyyy-desc'] = function (a, b) {
    "use strict"; //let's avoid tom-foolery in this function
    var ordA = customDateDDMMMYYYYToOrd(a),
        ordB = customDateDDMMMYYYYToOrd(b);
    return (ordA < ordB) ? 1 : ((ordA > ordB) ? -1 : 0);
};
 
})();	
	
	
$(function() {	
	$('.data-tbl1').dataTable({
		"aaSorting": [[ 2, "asc" ]], 
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"aoColumns": [{"sWidth":"5%"},{"sWidth":"15%"},{"sWidth":"25%"},{"sWidth":"25%"},{"sWidth":"10%"},{"sWidth":"5%"},{"sWidth":"6%"},{"sWidth":"10%"}]
	});
});	
/*
*@Project View 
*@
*/

$('#ad_filter').hide();
$('#excel').hide();
$('#ajax_loader').show();
var sturl = site_base_url+"project/advance_filter_search_pjt/?"+Math.random();
$('#ad_filter').load(sturl,function(){
	$('#ajax_loader').hide();
	$('#ad_filter').show();
	$('#excel').show();
});

$(function() {
	dtPjtTable();
});	
	
function dtPjtTable() {
	$('.data-tbl').dataTable({
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}

	//For Projects
	var pjtstage = $("#pjt_stage").val(); 
	// var pm_acc	 = $("#pm_acc").val(); 
	var cust	 = $("#customer1").val(); 
	var service	 = $("#services").val();
	var practice = $("#practices").val();
	var keyword	 = $("#keywordpjt").val(); 
	//alert(keyword);
	if(keyword == "Project Title, Name or Company")
		keyword = 'null';

	if (document.getElementById('advance_search_pjt'))
		document.getElementById('advance_search_pjt').style.display = 'none';

		function advanced_filter_pjt(){
			$('#advance_search_pjt').slideToggle('slow');
			$('#project_note').slideToggle('slow');
			var keyword = $("#keywordpjt").val();
			var status  = document.getElementById('advance_search_pjt').style.display;
			
			if(status == 'none') {
				var pjtstage = $("#pjt_stage").val(); 
				// var pm_acc	 = $("#pm_acc").val();
				var cust	 = $("#customer1").val(); 
				var service	 = $("#services").val();
				var practice = $("#practices").val();				
			} else {
				$("#pjt_stage").val("");
				// $("#pm_acc").val("");
				$("#customer1").val("");
				$("#services").val("");
				$("#practices").val("");
			}
		}
		
		$('#advanceFilters_pjt,#pjt_search_form').submit(function() {
			var pjtstage = $("#pjt_stage").val(); 
			// var pm_acc 	 = $("#pm_acc").val(); 
			var cust 	 = $("#customer1").val(); 
			var service  = $("#services").val();
			var practice = $('#practices').val();
			var keyword  = $("#keywordpjt").val();
			var datefilter  = $("#datefilter").val();
			var from_date   = $("#from_date").val();
			var to_date  	= $("#to_date").val();
			if(keyword == "Project Title, Name or Company")
			keyword = '';
			
			var params = {'pjtstage':pjtstage,'cust':cust,'service':service,'practice':practice,'keyword':encodeURIComponent(keyword),'datefilter':datefilter,'from_date':from_date,'to_date':to_date};
			params[csrf_token_name] = csrf_hash_token; 
			if($(this).attr("id") == 'advanceFilters_pjt'){
				$('#advance').hide();
				$('#load').show();
				$("#ad_filter" ).hide();
				$('#ajax_loader').show();
				$('#excel').hide();
			}
			
		    $.ajax({
		        type: 'POST',
		        url: site_base_url+'project/advance_filter_search_pjt',
		        data: params,
		        success: function(data) {
					$("#ad_filter" ).html(data);
					$('#load').hide();
					$('#ajax_loader').hide();
					$("#ad_filter" ).show();
					$('#advance').show();
					$('#excel').show();
		        }
		    });
		    return false;
		});
	
	function deleteProject(id) {
		$.blockUI({
			message:'<br /><h5>Are You Sure Want to Delete <br />this project?<br /><br />This will delete all the items<br />and logs attached to this Project.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
			css:{width:'440px'}
		});
	}
		
	function processDelete(id) {
		window.location.href = site_base_url+'project/delete_quote/'+id;
	}

	function cancelDel() {
		$.unblockUI();
	}
	
	$(function(){
		$('#excel').click(function() {
			var stage = $('#pjt_stage').val();
			// var pm    = $('#pm_acc').val();
			var customer = $('#customer1').val();
			var service = $('#services').val();
			var practice = $('#practices').val();
			var datefilter  = $("#datefilter").val();
			var from_date   = $("#from_date").val();
			var to_date  	= $("#to_date").val();

			var url = site_base_url+"project/excelExport";
			
			var form = $('<form action="' + url + '" method="post">' +
			  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
			  '<input type="hidden" name="stages" value="' +stage+ '" />' +
			  '<input type="hidden" name="customers" value="' +customer+ '" />' +
			  '<input type="hidden" name="services" value="' +service+ '" />' +
			  '<input type="hidden" name="practices" value="' +practice+ '" />' +
			  '<input type="hidden" name="datefilter" value="' +datefilter+ '" />' +
			  '<input type="hidden" name="from_date" value="' +from_date+ '" />' +
			  '<input type="hidden" name="to_date" value="' +to_date+ '" />' +
			  '</form>');
			$('body').append(form);
			$(form).submit();
			return false;
		});
	});
	
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
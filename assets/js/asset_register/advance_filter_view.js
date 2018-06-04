/*
 *@Advance Filter View 
*/

$('#excel').click(function() {
	document.location.href = site_base_url+"welcome/excelExport/";
	return false;
});

$(function() {
	dtTable();
	
	/*for lead field customize*/
	var show_lead_field_msg = '<div class="show-leads-fields">Loading Content.<br />';
	show_lead_field_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';
	  $('.modal-custom-fields').click(function(){
	   $.blockUI({
					message:show_lead_field_msg,
					css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default', position: 'absolute'},
					overlayCSS: {backgroundColor:'#EAEAEA', opacity: '0.9', cursor: 'wait'}
				});
		$.get(
			site_base_url+'welcome/get_lead_fields',
			{},
			function(data){
				$('.show-leads-fields').slideUp(500, function(){
					$(this).parent().css({backgroundColor: '#fff', color: '#333'});
					$(this).css('text-align', 'left').html(data).slideDown(500, function(){
						$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					});
				})
			}
		);
		return false;
	});
});	

function deleteAsset(id) {
    alert(id);return false;
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete <br />this project?<br /><br />This will delete all the items<br />and logs attached to this Lead.</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}
function processDelete(id) {
		var formdata = {};
		formdata['id'] = id;
		formdata[csrf_token_name] = csrf_hash_token;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url : site_base_url + 'welcome/delete_quote/',
			data: formdata,
			beforeSend:function(){
				$.blockUI();
			},
			success : function(response){
				// $.unblockUI();
				if(response['error'] == false) {
					$.blockUI({
						message:'<br /><h5>'+response['msg']+'</h5><br />',
						css:{width:'440px'}
					});
					$('#'+id).remove();
					setTimeout('timerfadeout()', 3000);
				} else {
					alert(response['msg']);
					return false;
				}				
			}
		});
}

function cancelDel() {
	$.unblockUI();
}

$.ajaxSetup ({
    cache: false
});

function timerfadeout() {
	$.unblockUI();
}

function dtTable() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "desc" ]],
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

//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
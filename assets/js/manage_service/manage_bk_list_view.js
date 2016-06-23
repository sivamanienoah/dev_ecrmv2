/*
 *@Manage Service View
 *@Manage Service Controller
*/

// csrf_token_name,csrf_hash_token,site_base_url & accesspageis global js variable 

$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "desc" ]],
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
	});
});
function processDelete(curr_year, curr_id) {
	window.location.href = site_base_url+'manage_service/delete_bk_values/'+curr_year+'/'+curr_id;
}
function cancelDel() {
    $.unblockUI();
}
function timerfadeout() {
	$('.dialog-err').fadeOut();
}

/*
* Editing Sales Forecast
*/
function editCurValue(curr_year, curr_id) {
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		url : site_base_url + 'manage_service/get_edit_currency_container/'+curr_year+'/'+curr_id,
		cache: false,
		type: "POST",
		data:params,
		success : function(response) {
			// console.info(response);
			// return false;
			$('.layout').unblock();
			$('#edit_currency_container').html(response);
			$.blockUI({
				message:$('#edit_currency_container'),
				css:{ 
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  '250px',
					left: ($(window).width() - 300) /2 + 'px',
					width: '350px',
					position: 'absolute',
					'overflow-y':'auto',
					'overflow-x':'hidden'
				}
			});
			$( "#edit_currency_container" ).parent().addClass( "no-scroll" );
		}
	});
}

function deleteCurValue(curr_year, curr_id){
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+curr_year+','+curr_id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
	$( ".blockUI.blockMsg.blockPage" ).addClass( "no-scroll" );
}

function add_bk_value(){
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		url : site_base_url + 'manage_service/add_form_bk_values/',
		cache: false,
		type: "POST",
		data:params,
		success : function(response) {
			// console.info(response);
			// return false;
			$('.layout').unblock();
			$('#add_currency_container').html(response);
			$.blockUI({
				message:$('#add_currency_container'),
				css:{ 
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  '250px',
					left: ($(window).width() - 300) /2 + 'px',
					width: '350px',
					position: 'absolute',
					'overflow-y':'auto',
					'overflow-x':'hidden'
				}
			});
			$( "#add_currency_container" ).parent().addClass( "no-scroll" );
		}
	});
}
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
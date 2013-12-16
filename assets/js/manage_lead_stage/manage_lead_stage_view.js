/*
 *@Manage Lead Stage View
*/

	$(document).ready(function() {
		$('#lead_stg_items').sortable({axis:'y', cursor:'move', update:prepareSortedLeadStg});
		populateLeadStage();
	});

	//populateLeadStage();

	function populateLeadStage(nosort) {
			$('.ls-container').block({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
				css: { background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333' }
			});
			$.get('manage_lead_stage/ajax_leadstg_list/',{},function(res) {
					if (typeof (res) == 'object') {
						if (res.error == false) {
							$('#lead_stg_items').empty().append(res.html);
							if (nosort != true) $('#lead_stg_items').sortable('refresh');
						} else {
							alert(res.errormsg);
						}
					} else {
						alert('Error receiving data!');
					}
					$('.ls-container').unblock();
				},
				'json'
			)
	}

	function prepareSortedLeadStg() {
		item_sort_order = $('#lead_stg_items').sortable('serialize')+'&'+csrf_token_name+'='+csrf_hash_token;
		$('.ls-container').block({message:'<h5>Processing</h5>'});
		$.post(
			'manage_lead_stage/ajax_save_lead_sequence',item_sort_order,
			function(data) {
				$('.ls-container').unblock();
				if (data.error) {
					alert(data.errormsg);
				} else {
					//$('.q-save-order').slideUp();
					populateLeadStage();
				}
			},
			'json'
		);
	}


	function checkStatus(id) {
		var params 				= { 'data': id};
		params[csrf_token_name] = csrf_hash_token;
		$.ajax({
			url: site_base_url+'manage_lead_stage/ajax_check_status_lead_stage/',
			data: params,
			type: "POST",
			dataType:"json",                                                                
			cache: false,
			beforeSend:function(){
				$('#dialog-message-'+id).empty();
			},
			success: function(response) {
				if (response.html == 'NO') {
					$('#errmsg-'+id).show();
					$('#errmsg-'+id).append("One or more leads currently assigned for this Lead Stage. This cannot be deleted.");
					setTimeout('timerfadeout()', 4000);
				} else {
					$.blockUI({
						message:'<br /><h5>Are You Sure Want to Delete?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processDelete('+id+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
						css:{width:'440px'}
					});
				}
			}                                                                                       
		});
		return false;
	}
	
	function processDelete(id) {
		window.location.href = 'manage_lead_stage/leadStg_delete/update/'+id;
	}

	function cancelDel() {
		$.unblockUI();
	}

	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
function showMSG(str) {
    $('#user-status .msg-highlight').html(str).effect('highlight', {}, 2500);
} 
function showMSGS(str) {
 $.get('ajax/request/set_flash_data/' + str,{},function(data){
		document.location.href = 'welcome/edit_quote/' + quote_id;
		$.unblockUI();
    });
}
function populateQuote(jobid, nosort) {
    if (typeof(jobid) == 'undefined') {
        return;
    } else {
		$('.q-container').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
        $.get('welcome/ajax_quote_items/'+jobid,{},function(res) {
                if (typeof (res) == 'object') {
                    if (res.error == false) {
                        $('#q-sort-items').empty().append(res.html);
                        $('#sale_amount').html(res.sale_amount);
                        $('#gst_amount').html(res.gst_amount);
                        $('#total_inc_gst').html(res.total_inc_gst);
						$('.q-sub-total #deposit_amount').html(res.deposits);
						$('.q-sub-total #balance_amount').html(res.deposit_balance);
						try {
							$('#new-balance-due, #ex-balance-due, #new-balance-due-log, #ex-balance-due-log').val(res.deposit_balance.replace(/[^0-9\.]+/g, ''));
						} catch (e) {};
						try {
							$('#sp_form_invoice_total').val(res.numeric_total_inc_gst);
						} catch (e) {};
                        if (nosort != true) $('#q-sort-items').sortable('refresh');
                    } else {
                        alert(res.errormsg);
                    }
                } else {
                    alert('Error receiving data!');
                }
                $('.q-container').unblock();
            },
            'json'
        )
    }
}

function scrollElem(parentElem, tragetElem) {
    var divOffset = $(parentElem).offset().top;
    var pOffset = $(tragetElem).offset().top;
    var pScroll = pOffset - divOffset;
    $(parentElem).animate({scrollTop: '+=' + pScroll + 'px'}, 1000);
}

function js_urlencode(str) {
	return escape(str).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
}

function convertQuoteStatus() {
    var qstatus = $('#general_convert_quote_status').val();
	
	$.blockUI({
		message:'<h2>Processing your request...</h2>'
	});
	
	$.getJSON('welcome/ajax_update_quote/' + quote_id + '/' + qstatus,
	function(data) {
		if (typeof(data) == 'object') {
			if (data.error) {
				alert(data.errormsg);
				$.unblockUI();
				window.location.href = "welcome/edit_quote" + "/" + quote_id +"/";
				//alert('status Changed');
			} else {
				reloadWithMessage('Status Changed Successfully', qstatus);
			}
		} else {
			alert('Unexpected response from server!')
			$.unblockUI();
		}
	});
}

function reloadWithMessage(str, statusid) {
	$.get('ajax/request/set_flash_data/' + str,{},function(data){
		document.location.href = 'welcome/view_quote/' + quote_id;
		$.unblockUI();
	});
}
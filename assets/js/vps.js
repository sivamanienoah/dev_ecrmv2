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
/**
 * Quotation overlay
 */
function DP_show(html){
	var oh, st = 0;
	st = getScrollTop();
	$("body").append('<div id="DP_overlay"></div><div id="DP_show"><div id="DP_waiting"><img src="'+ waitImage.src +'" alt=" " /></div></div>');
	oh = parseInt($("body").height()) + 400;
	$("#DP_overlay").css("height", oh + st + "px");
	DP_position(false, oh);
	$("#DP_overlay").fadeIn(500, function(){
		$("#DP_show").fadeIn(500, function(){
			$("#DP_overlay").click(DP_remove);
			DP_data_add(html);
		});
	});
	
	// this is borrowed from thickbox
	document.onkeyup = function(e){   
		if (e == null) { // ie
			keycode = event.keyCode;
		} else { // mozilla
			keycode = e.which;
		}
		if(keycode == 27){ // close
			DP_remove();
		}  
	}
	
	window.onresize = DP_position;
	
	return false;
}

function DP_data_add(data) {
	$("#DP_show").slideUp(300, function(){
		$('#DP_waiting').remove();
		$("#DP_show").append(data);
		$("#DP_show").slideDown(550);
	});
}

function DP_dataload(url){
	var params = {};
	$.get(url, 
		  params, 
		  function(data){
			$("#DP_show").slideUp(300, function(){
						$('#DP_waiting').remove();
						$("#DP_show").append(data);
						$("#DP_show").slideDown(550);
					});
			});
}


function DP_remove(){
	$("#DP_show").fadeOut("fast", function(){
		$(this).remove();
		$("#DP_overlay").fadeOut("fast", function(){
			$(this).remove();
		});
	});
	
	document.onkeyup = "";
	window.onresize = "";
}

function DP_position(animateMove, windowHeight){
	var anim = true;
	if(animateMove == false){
		anim = false;
	}
	var  window_height, window_width, show_width, st;
	if(window.innerWidth){
		window_width = window.innerWidth;
		window_height = window.innerHeight;
	}else if(document.body.offsetWidth){
		window_width = document.body.offsetWidth;
		window_height = document.body.offsetHeight;
	}
	st = getScrollTop();
	if(typeof (windowHeight) != 'undefined' && window_height > windowHeight){
		$("#DP_overlay").animate({height: st + window_height + 'px'}, 'normal');
	}
	show_width = $("#DP_show").width();
	if(anim == true){
		$("#DP_show").animate({marginLeft: parseInt(((window_width-show_width) / 2),10) + 'px', top: st + 20 + "px"}, 'normal');
	}else{
		$("#DP_show").css({marginLeft: parseInt(((window_width-show_width) / 2),10) + 'px', top: st + 20 + "px"});
	}
}

function getScrollTop(){
	var st=0;
	if (document.documentElement && document.documentElement.scrollTop){
	  st = document.documentElement.scrollTop;
	}else if (document.body && document.body.scrollTop){
	  st = document.body.scrollTop;
	}else if (window.pageYOffset) {
	  st = window.pageYOffset;
	}
	return st;
}

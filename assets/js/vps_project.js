function showMSG(str) {
    $('#user-status .msg-highlight').html(str).effect('highlight', {}, 2500);
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
function convertProjectStatus(job_stat) {
	//dynamically bring the hostname and project folder name.
	var hstname = window.location.host;
	var pathname = window.location.pathname;
	var pth=pathname.split("/");
	
	//alert('project status'); return false;
    var qstatus = $('#general_convert_quote_status').val();
	//alert(qstatus); return false;
	$hosting=0;
	$('#hosting option:selected').each(function(){
		$hosting+=','+$(this).val();
	});
	if (window.confirm('Are you sure? You want to Adjust Project Stage?')) {
        var msg;
        if (qstatus == 13) {
            msg = 'The Project moved to In Progress';
        } else if (qstatus == 14) {
			if (job_stat < 89) {
				alert('You cannot change the project stage if the Project is not at least 90% complete!');
				return false;
			}
			else {
				msg = 'The Project moved to Completed';
			}
        } else if (qstatus == 15) {
            msg = 'The Project moved to Cancelled';
		} else if (qstatus == 16) {
			msg = 'The Project moved to On Hold';
        }
		else {
            alert('Invalid Project Status Supplied!');
            return false;
        }
        $.blockUI({
            message:'<h2>Processing your request...</h2>'
        });
		//alert('http://' + hstname + '/' + pth[1] + '/welcome/ajax_update_project/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting);
		//$.getJSON('http://50.63.40.194/esmart/welcome/ajax_update_quote/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting,
		//$.getJSON('http://localhost/ecrmv2/welcome/ajax_update_project/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting,
		//$.getJSON('http://' + hstname + '/' + '/welcome/ajax_update_project/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting,
		$.getJSON('welcome/ajax_update_project/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting,
            function(data){
				if (typeof(data) == 'object'){
                    if (data.error) {
                       $.unblockUI();
                    } else {
                        reloadWithMessage(msg);
                    }
                } else {
                    alert('Unexpected response from server!')
                    $.unblockUI();
                }
            });
    }
    return false;
}

function reloadWithMessage(str) {
	//dynamically bring the hostname and project folder name.
	var hstname = window.location.host;
	var pathname = window.location.pathname;
	var pth=pathname.split("/");
    //$.get('http://50.63.40.194/esmart/ajax/request/set_flash_data/' + str,{},function(data){
    //$.get('http://localhost/ecrmv2/ajax/request/set_flash_data/' + str,{},function(data){
    //$.get('http://' + hstname + '/' + pth[1] + '/ajax/request/set_flash_data/' + str,{},function(data){
    $.get('ajax/request/set_flash_data/' + str,{},function(data){
		//document.location = 'http://50.63.40.194/esmart/welcome/view_quote/' + quote_id;
		//document.location = 'http://localhost/ecrmv2/invoice/view_project/' + quote_id;
		//document.location = 'http://' + hstname + '/' + '/invoice/view_project/' + quote_id;
		document.location = 'invoice/view_project/' + quote_id;
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

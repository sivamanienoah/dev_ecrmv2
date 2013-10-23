function showMSG(str) {
    $('#user-status .msg-highlight').html(str).effect('highlight', {}, 2500);
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
    var qstatus = $('#general_convert_quote_status').val();
	//alert(qstatus); return false;
	$hosting=0;
	$('#hosting option:selected').each(function(){
		$hosting+=','+$(this).val();
	});
	if (window.confirm('Are you sure? You want to Adjust Project Stage?')) {
        var msg = "Status Successfully Changed.";

        $.blockUI({
            message:'<h2>Processing your request...</h2>'
        });

		$.getJSON('welcome/ajax_update_project/' + quote_id + '/' + qstatus + '/' + encodeURI(msg)+'/'+$hosting,
            function(data){
				if (typeof(data) == 'object'){
                    if (data.error) {
                       $.unblockUI();
                    } else {
                        reloadWithMessagePjt(msg);

                    }
                } else {
                    alert('Unexpected response from server!')
                    $.unblockUI();
                }
            });
    }
    return false;
}

function reloadWithMessagePjt(str) {
    $.get('ajax/request/set_flash_data/' + str,{},function(data){
		document.location = 'invoice/view_project/' + quote_id;
		$.unblockUI();
    });
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

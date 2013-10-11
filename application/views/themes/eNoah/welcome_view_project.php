<?php require ('tpl/header.php'); ?>

<?php #echo "<pre>"; print_r($quote_data); exit; ?>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript" src="assets/js/vps_project.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<script type="text/javascript">var this_is_home = true;</script>


<!--Code Added for the Pagination in Comments Section -- Starts Here-->
<script type="text/javascript">
$(document).ready(function() {
	var mySelect = $('#project_lead');
    previousValue = mySelect.val();
	var lead_assign = previousValue; 
	$("#previous-project-manager").val(lead_assign);  //alert($("#previous-project-manager").val());
	$('#project_lead').change( function() {
		//alert( $(this).val() ); // alerts current value
		//alert( previousValue ); // alerts previous value
		//previousValue = mySelect.val(); // save so it can be referenced next time
	});

	$("#lead_log_list")
	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
	
	$("#lead_query_list")
	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager1"),positionFixed: false});
	$("#show-con").hide();
	$("#show-btn").click(function(){
		$("#show-con").slideToggle("slow"); 
		return false;
	});
	$('.payment-profile-button').click(function() {
		$('#rec_paymentfadeout').hide();
		$('.payment-profile-view').slideToggle(); 
		$('.payment-recieved-view').hide(); 
		$('.payment-terms-mini-view1').show(); 
		$('.payment-received-mini-view1').hide(); 
		loadPayment();		
		return false;
	});
	$('.payment-received-button').click(function() {
		$('#rec_paymentfadeout').hide();
		$('.payment-recieved-view').slideToggle();
		$('.payment-profile-view').hide(); 
		$('.payment-received-mini-view1').show(); 
		$('.payment-terms-mini-view1').hide(); 
		loadPaymentTerms(); 
		return false;
	});
	
});

</script>
<div class="comments-log-container" style= "display:none;">
	<?php if ($log_html != "") { ?>
			<table width="100%" class="log-container"> 
				<tbody>
				<?php 
					echo $log_html;
				?>				
				</tbody> 
			</table>
	<?php } else { echo "No Comments Found."; }?>
</div>

<!--Code Added for the Pagination in Comments Section--Ends Here-->

<script type="text/javascript">  
$().ready(function() {  
	$('#add').click(function() {  
		$('#project-member').val($('#select1').val());	
		return !$('#select1 option:selected').remove().appendTo('#select2'); 
	});  
	$('#remove').click(function() {  
		$('#project-member').val($('#select2').val());
		return !$('#select2 option:selected').remove().appendTo('#select1');  
	});  
   /* when double clicking on select1 project member will added to select 2 */
	$('#select1').dblclick(function() {
		$('#project-member').val($('#select1').val());
		return !$('#select1 option:selected').remove().appendTo('#select2');  	
   });
   /* when double clicking on select2 project member will added to select 1 */
	$('#select2').dblclick(function() {
		$('#project-member').val($('#select2').val());
		return !$('#select2 option:selected').remove().appendTo('#select1');  
   });
});
</script>

<script type="text/javascript">

var job_categories = [];
job_categories['not_select'] = '';
<?php foreach ($cfg['job_categories'] as $jck => $jcv) { ?>
job_categories[<?php echo  $jck ?>] = '<?php echo  $jcv ?>';
<?php } ?>

var hourly_rate = <?php echo  $cfg['hourly_rate'] ?>;
var quote_id = <?php echo  isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>;
var ex_cust_id = 0;
var item_sort_order = '';
var curr_job_id = <?php echo  $quote_data['jobid'] ?>;

$(function(){
	<?php if (isset($quote_data) && (isset($edit_quotation) || isset($view_quotation))) { ?>
	populateQuote(<?php echo  $quote_data['jobid'] ?>, true);
	<?php } ?>
});

var userid = <?php echo  isset($userdata['userid']) ? $userdata['userid'] : 0 ?>;

var current_job_status = <?php echo (isset($quote_data['job_status'])) ? $quote_data['job_status'] : 0 ?>;

function addLog() {
	var the_log = $('#job_log').val();
	if ($.trim(the_log) == '') {
		alert('Please enter your post!');
		return false;
	}
	
	var submit_log_minutes = null, log_minutes = $('#log_minutes').val();
	if ($.trim(log_minutes) != '')
	{
		if ( ! /^[0-9]+$/.test(log_minutes))
		{
			alert('Invalid minutes supplied');
			return false;
		}
		else
		{
			submit_log_minutes = log_minutes;
		}
	}

	var client_emails = true;
	if ($('#email_to_customer').is(':checked')) {
		client_emails = false;
		$('#multiple-client-emails').children('input[type=checkbox])').each(function(){
			if ($(this).is(':checked')){
				client_emails = true;
			}
		});
	}
	
	if (!client_emails) {
		alert('If you want to email the client, you must select at least one email address of the client.');
		return false;
	}
	
	if (current_job_status < 2 && $('#email_to_customer').is(':checked') && $('#attach_pdf').is(':checked')) {
		if (!window.confirm('This job is not yet converted to a quotaion.\nAre you sure you want to send this PDF?')) {
			return false;
		}
	}
	
	if ($('#log_stickie').is(':checked')) {
		if (!window.confirm('Are you sure you want to highlight this log as a Stickie?')) {
			return false;
		}
	}
	
	var email_set = '';
	$('.user-addresses input[type="checkbox"]:checked').each(function(){
		email_set += $(this).attr('id') + ':';
	});
	
	
	$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	
	var form_data = {'userid':userid, 'jobid':quote_id, 'log_content':the_log, 'emailto':email_set,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}
	
	if ($('#log_stickie').is(':checked')) {
		form_data.log_stickie = true;
	}
	
	if ($('#attach_pdf').is(':checked')) {
		form_data.attach_pdf = true;
	}
	
	
	if ($('#ignore_content_policy').is(':checked')) {
		form_data.ignore_content_policy = true;
	}
	
	/* add minutes to the log */
	if (submit_log_minutes)
	{
		form_data.time_spent = submit_log_minutes;
	}
	
	form_data.use_custom_date = $('.download-invoice-option-log input[name="use_custom_date"]').val();
	form_data.balance_due = $('.download-invoice-option-log input[name="balance_due"]').val();
	form_data.custom_description = $('.download-invoice-option-log input[name="custom_description"]').val();
	
	
	if ($('#email_to_customer').is(':checked')) {
		form_data.email_to_customer = true;
		form_data.client_email_address = $('#client_email_address').val();
		form_data.client_full_name = $('#client_full_name').val();
		if ($('#client_emails_1').is(':checked')) {
			form_data.client_emails_1 = $('#client_emails_1').val();
		}
		if ($('#client_emails_2').is(':checked')) {
			form_data.client_emails_2 = $('#client_emails_2').val();
		}
		if ($('#client_emails_3').is(':checked')) {
			form_data.client_emails_3 = $('#client_emails_3').val();
		}
		if ($('#client_emails_4').is(':checked')) {
			form_data.client_emails_4 = $('#client_emails_4').val();
		}
		form_data.additional_client_emails = $('#additional_client_emails').val();
	}
	if ($('#requesting_client_approval').val() == 1) {
		form_data.requesting_client_approval = true;
	}
	
	// empty list of emails?
	if (email_set == '' && typeof(form_data.client_emails_1) == 'undefined' && typeof(form_data.client_emails_2) == 'undefined' && typeof(form_data.client_emails_3) == 'undefined' && typeof(form_data.client_emails_4) == 'undefined' && typeof(form_data.additional_client_emails) == 'undefined') {
		if (!window.confirm('You do not have any user selected for emails!\nDo you want to continue?')) {
			$.unblockUI();
			return false;
		}
	}
	
	if ($('#email_to_customer').is(':checked') && the_log.match(/attach|invoice/gi) != null && form_data.attach_pdf != true) {
		if ( ! window.confirm('You have not attached the invoice to the email.\nDo you want to continue without the invoice?')) {
			$.unblockUI();
			return false;
		}
	}
	$.post(
		'welcome/pjt_add_log',
		form_data,
		function(_data){
		//alert(_data);
		try {
				var data;
				eval('data = ' + _data);
				if (typeof(data) == 'object'){
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('#lead_log_list').prepend(data.html).children('.log:first').slideDown(400);
						$('#job_log').val('');
						$('.user-addresses input[type="checkbox"]:checked, #attach_pdf, #email_to_customer, #log_stickie, #ignore_content_policy').each(function(){
							$(this).attr('checked', false);
						});
						$('#log_minutes').val('');
						$('#additional_client_emails').val('');
						$('#multiple-client-emails').children('input[type=checkbox])').attr('checked', false).end()
							.slideUp(400);
						$('.download-invoice-option-log:visible').slideUp(400);
						if (data.status_updated) {
							document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
						}
						if (typeof(this_is_home) != 'undefined')
						{
						   window.location.href = window.location.href;
						}
					}
				} else {
					alert('Unexpected response from server!');
				}
			} catch (e) {
				alert('Unexpected response from server!\nIt is possible that your session timed out!');
			}
			$.unblockUI();
		}
	)
}

function setPaymentRecievedTerms() {
	$('#pr_form_jobid').val(curr_job_id);
	
	var valid_date = true;
	var date_entered = true;
	var errors = [];
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
    var pdate2 = $.trim($('#pr_date_3').val());	
	
	if (($.trim($('#pr_date_1').val()) == '') && ($.trim($('#pr_date_2').val()) == '') && ($.trim($('#pr_date_3').val()) == '')) {
		date_entered = false;
	}
	if (valid_date == false) {
		errors.push('You have selected an invalid date');
	}
    if(($.trim($('#pr_date_1').val()) == '')) {
	    errors.push('<p>Enter Invoice Number.</p>');
	}
	if(($.trim($('#pr_date_2').val()) == '')) {
		errors.push('<p>Enter Amount.</p>');
	}
	if(($.trim($('#pr_date_3').val()) == '')) { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if($('.deposit_map_field').val() == 0) {
	   errors.push('<p>Map payment term.</p>');
	}
	
	if (errors.length > 0) {
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		setTimeout('timerfadeout()', 8000);
		return false;
	} else {
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		
		var form_data = $('#payment-recieved-terms').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
		$.post( 
			'welcome/add_project_received_payments',
			form_data,
			function(data) {
					if (data.error) {
						//alert(data.errormsg);
						setTimeout('timerfadeout()', 8000);
						$('#rec_paymentfadeout').show();
						$('#rec_paymentfadeout').html(data.errormsg);
					} else {
						$('.payment-recieved-view:visible').slideUp(400);
						$('.payment-received-mini-view1').html(data.msg);
						//document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
						$('#payment-recieved-terms')[0].reset();
					}
				$.unblockUI();
				//$('#payment-recieved-terms')[0].reset();
			}
			,'json'
		);
		
	}
	$('.payment-received-mini-view1').css('display', 'block');
}

function updatePaymentRecievedTerms(pdid, eid) {
	$('#pr_form_jobid').val(curr_job_id);
	var valid_date = true;
	var date_entered = true;
	var errors = [];
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
    var pdate2 = $.trim($('#pr_date_3').val());	
	
	if ( ($.trim($('#pr_date_1').val()) == '') && ($.trim($('#pr_date_2').val())  == '') && ($.trim($('#pr_date_3').val()) == '') ) {
		date_entered = false;
	}
	
	if (valid_date == false) {
		errors.push('You have selected an invalid date');
	}
	if(($.trim($('#pr_date_1').val()) == '')) {
	    errors.push('<p>Enter Invoice Number.</p>');
	}
	if(($.trim($('#pr_date_2').val()) == '')) {
		errors.push('<p>Enter Amount.</p>');
	}
	if(($.trim($('#pr_date_3').val()) == '')) { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if($('.deposit_map_field').val() == 0) {
	   errors.push('<p>Map payment term.</p>');
	}
	if (errors.length > 0) {
		//alert(errors.join('\n'));
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		setTimeout('timerfadeout()', 8000);
		return false;
	} else {
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		
		var form_data = $('#update-payment-recieved-terms').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
		$.post( 
			'welcome/add_project_received_payments/'+pdid+'/'+eid,
			form_data,
			function(data) {
					if (data.error) {
						//alert(data.errormsg);
						setTimeout('timerfadeout()', 8000);
						$('#rec_paymentfadeout').show();
						$('#rec_paymentfadeout').html(data.errormsg);
					} else {
						$('.payment-recieved-view:visible').slideUp(400);
						$('.payment-received-mini-view1').html(data.msg);
						$('#update-payment-recieved-terms').remove();
						paymentReceivedView();
					}
				$.unblockUI();
			}
			,'json'
		);
	}
	$('.payment-received-mini-view1').css('display', 'block');
}

function loadPaymentTerms() {
	$.post( 
		'welcome/retrieveRecord/'+curr_job_id,{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('.deposit_map_field').html(data);	
			}
		}
	);
}
//function for load the payment terms every time click the 'Add Payment Terms' button
function loadPayment() {
	$.post( 
		'welcome/payment_terms_delete/'+curr_job_id,{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('.payment-terms-mini-view1').html(data);	
			}
		}
	);
}

function setProjectPaymentTerms() {

	$('#sp_form_jobid').val(curr_job_id);
	$(".payment-terms-mini-view1").css("display","block");
	$(".payment-received-mini-view1").css("display","none");
	//var invoice_total = parseFloat($('#sp_form_invoice_total').val());	
	var valid_date = true;
	var date_entered = true;
	var errors = [];
	
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
    var pdate2 = $.trim($('#sp_date_2').val());	

	if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
		date_entered = false;
	}
	
	if ($('#sp_form_jobid').val() == 0) { 
		errors.push('Invoice not properly loaded!');
	}
	if(($.trim($('#sp_date_1').val()) == '')) {
	    errors.push('<p>Enter Payment Milestone Name.</p>');
	}
	if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if (valid_date == false) {
		errors.push('<p>You have selected an invalid date.</p>');
	}
    if(($.trim($('#sp_date_3').val()) == '')) {
		errors.push('<p>Enter Milestone Value.</p>');
	}
	if (errors.length > 0) {
		//alert(errors.join('\n'));
		setTimeout('timerfadeout()', 8000);
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		return false;
	} else {
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		
		var form_data = $('#set-payment-terms').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
		$.post( 
			'welcome/set_payment_terms',
			form_data,
			function(data) {
				if (data.error) {
					alert(data.errormsg);
				} else {
					$('.payment-profile-view:visible').slideUp(400);
					$('.payment-terms-mini-view1').html(data);
				}
				$.unblockUI();
				$('#set-payment-terms')[0].reset();
			}
		);
	}
	$('.payment-terms-mini-view1').css('display', 'block');
}

//Update functionality for set payment terms Starts here.
function updateProjectPaymentTerms(eid) {
	//alert(eid); return false;
	$('#rec_paymentfadeout').hide();
	$('#sp_form_jobid').val(curr_job_id);
	$(".payment-terms-mini-view1").css("display","block");
	$(".payment-received-mini-view1").css("display","none");
	//var invoice_total = parseFloat($('#sp_form_invoice_total').val());	
	var valid_date = true;
	var date_entered = true;
	var errors = [];
	
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	
	if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
    var pdate2 = $.trim($('#sp_date_2').val());	
	
	if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
		date_entered = false;
	}
	
	if ($('#sp_form_jobid').val() == 0) { 
		errors.push('Invoice not properly loaded!');
	}
	if(($.trim($('#sp_date_1').val()) == '')) {
	    errors.push('<p>Enter Payment Milestone Name.</p>');
	}
	if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if (valid_date == false) {
		errors.push('<p>You have selected an invalid date</p>');
	}
    if(($.trim($('#sp_date_3').val()) == '')) {
		errors.push('<p>Enter Milestone Value.</p>');
	}
	if (errors.length > 0) {
		//alert(errors.join('\n'));
		setTimeout('timerfadeout()', 8000);
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		return false;
		
	} else {
		
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		var form_data = $('#update-payment-terms').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
		$.post( 
			'welcome/set_payment_terms/'+eid,
			form_data,
			function(data) {
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.payment-profile-view:visible').slideUp(400);
						$('.payment-terms-mini-view1').html(data);
					}
				$.unblockUI();
				$('#update-payment-terms').remove();
				paymentProfileView();
			}
		);
		
	}
	$('.payment-terms-mini-view1').css('display', 'block');
}

function addDepositPayment() {
	$('#deposit_form_jobid').val(curr_job_id);
	var dep_amount = $('#deposit_amount_add').val();
	if (!dep_amount.match(/^[0-9]+(\.[0-9]{1,2})?$/))
	{
		alert('Invalid amount supplied!');
		return false;
	} else {
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		
		var form_data = $('#set-deposits').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
		$.post(
			'welcome/add_deposit_payments',
			form_data,
			function(data) {
				if (typeof(data) == 'object') {
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.add-deposit-view:visible').slideUp(400);
					}
				} else {
					alert('Unexpected response from server!')
				}
				$.unblockUI();
			},
			'json'
		);
		
	}
	return false;
}

function fullScreenLogs() {
	var fsl_height = parseInt($(window).height()) - 80;
	fsl_height = fsl_height + 'px';
	$.blockUI({
		message:$('.comments-log-container'),
		css: {background:'#fff', border: '1px solid #999', padding:'4px', height:fsl_height, color:'#000000', width:'600px', overflow:'auto', top:'40px', left:'50%', marginLeft:'-300px'},
		overlayCSS:  {backgroundColor:'#fff', opacity:0.9}
	});
	$('.blockUI:not(.blockMsg)').append('<p onclick="$.unblockUI();$(this).remove();" id="fsl-close">CLOSE</p>');
}

function runAjaxFileUpload() {
	var _uid = new Date().getTime();
	$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
	$.ajaxFileUpload
	(
		{
			url:'ajax/request/file_upload/<?php echo $quote_data['jobid'] ?>',
			secureuri:false,
			fileElementId:'ajax_file_uploader',
			dataType: 'json',
			success: function (data, status)
			{
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						if (window.console)
						{
							console.log(data);
						}
						if (data.msg)
						{
							alert(data.msg);
						}
						else
						{
							alert('File upload failed!');
						}
						$('#'+_uid).hide('slow').remove();
					}
					else
					{	
						if(data.msg == 'File successfully uploaded!') {
							var lead_details = "welcome/lead_fileupload_details/<?php echo $quote_data['jobid'] ?>/"+data.file_name+ "/" +userid;														
							$('#lead_result').load(lead_details);
						}
						//alert(data.msg);
						var _file_link = '<a href="vps_data/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a> <span>'+data.file_size+'</span>';
						var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/vps_data/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>';
						<?php
						if ( $userdata['role_id'] == 1 || $lead_details['belong_to'] == $userdata['userid'] || $lead_details['lead_assign'] == $userdata['userid'] || $lead_details['assigned_to'] == $userdata['userid'] )  {  echo '_del_link;'; } 
						?>
						$('#'+_uid).html(_del_link + _file_link);
					}
				}
			},
			error: function (data, status, e)
			{
				alert('Sorry, the upload failed due to an error!');
				$('#'+_uid).hide('slow').remove();
				if (window.console)
				{
					console.log('ajax error\n' + e + '\n' + data + '\n' + status);
					for (i in e) {
					  console.log(e[i]);
					}
				}
			}
		}
	);
	$('#ajax_file_uploader').val('');
	return false;
}

function ajaxDeleteFile(path, el) {
	if (window.confirm('Are you sure you want to delete this file?')) {
		path = js_urlencode(path);
		$(el).parent().hide('slow');
		$.post(
			'ajax/request/file_delete/',
			{file_path : path,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {
				try {
					var _data;
					eval('_data = ' + data);
					if (!_data.error) {
						$(el).remove();
					} else {
						$(el).parent().show('slow');
						alert('File could not be deleted!');
					}
				} catch (e) {
					alert(e);
					$(el).parent().show('slow');
					alert('File could not be deleted!');
				}
			}
		);
	}
}

function get_silent_logs() {
	var timestamp = $('div.log-container div.log:first p.data span').text();
	var url = 'ajax/request/get_new_logs/' + curr_job_id + '/' + timestamp;
	$.get(
		url,
		{},
		function(_data) {
			try {
				eval ('var data = ' + _data);
			} catch (e) {}
			if (typeof(data) == 'object')
			{
				$('div.log-container').prepend(data.log_html);
				$('div.log-container div.log:first:hidden').slideDown(300);
			}
		}
	)
}

function addURLtoJob() {
	var url = $.trim($('#job-add-url').val());
	var cont = $.trim($('#job-url-content').val());
	if (url == '') {
		alert('Please enter a URL to add');
		return false;
	}
	url = js_urlencode(url);
	$.post(
		'ajax/request/add_url_tojob/',
		{'jobid':curr_job_id, 'url':url, 'content':cont,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function(_data) {
			try {
				eval ('var data = ' + _data);
				if (typeof(data) == 'object') {
					if (data.error == false) {
						$('#job-url-list').append(data.html);
						$('#job-add-url').val('');
						$('#job-url-content').val('');
					} else {
						alert(data.error);
					}
				} else {
					alert('URL addition failed! Please try again.');
				}
			} catch (e) {
				alert('Invalid response, your session may have timed out.');
			}
		}
	);
}

function ajaxDeleteJobURL(id, el) {
	$.get(
		'ajax/request/delete_url/' + id,
		{},
		function (_data) {
			try {
				eval ('var data = ' + _data);
				if (data.error == false) {
					var agree=confirm("Are you sure you want to delete this url?");
						if (agree) {
							$(el).parent().hide('fast', function() { $(this).remove(); });
							}
				} else {
					alert('URL deletion failed! Please try again.');
				}
			} catch (e) {
				alert('URL deletion failed! Please try again.');
			}
		}
	)
}

var job_project_manager = '<?php echo $quote_data['assigned_to'] ?>';

function setProjectLead() {
	$('#pjt_lead_errormsg').hide();
	var pl_user = $('#project_lead').val(); 
	var previous_manager = $("#previous-project-manager").val(); 
	
	if (pl_user == 0) {
		$('#pjt_lead_errormsg').text('Please Select Project Manager!');
		$('#pjt_lead_errormsg').show();
		return false;
	} else {
		$.get(
			'ajax/production/set_project_lead/' + curr_job_id + '/' + pl_user + '/' + previous_manager,
			{},
			
			function(data) {
				try {
				  // return false;
					eval ('var data = ' + data);
					if (typeof(data) == 'object') {
						if (data.error == false) { 
							job_project_manager = pl_user;
							$('h5.project-lead-label span').text('[ ' + $('#project_lead option:selected').text() + ' ]');
							$('.project-lead-change:visible').hide(200);
							if (typeof(this_is_home) != 'undefined')
							{
								window.location.href = window.location.href;
							}
						} else { 
							alert(data.error);
						}
					} else {
						alert('Updating faild, please try again.');
					}
				} catch (e) {
					//alert('Invalid response, your session may have  timed out.');
					$('h5.project-lead-label span').text('[ ' + $('#project_lead option:selected').text() + ' ]');
					$('.project-lead-change:visible').hide(200);
					$("#previous-project-manager").val(pl_user);
				}
				$('h5.project-lead-label span').text('[ ' + $('#project_lead option:selected').text() + ' ]');
				$('.project-lead-change:visible').hide(200);
				if (typeof(this_is_home) != 'undefined')
				{
					window.location.href = window.location.href;
				}
			}
			//'json'
		);
	}
}

//adding the project id.
function setProjectId() {
	$('#pjt_id_errormsg, .checkUser1, .checkUser').hide();
	var pjtId = $('#pjtId').val()
	if (pjtId == 0) {
		//alert('Please Enter Project ID!');
		$('#pjt_id_errormsg').text('Please Enter Project ID!');
		$('#pjt_id_errormsg').show();
		setTimeout('timerfadeout()', 2000);
		return false;
	} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'welcome/getPjtIdFromdb/' + pjtId,
            cache : false,
            success : function(response){
                $('.checkUser').hide();
                if(response == 'userOk') {	
					$('.checkUser').show(); 
					$('.checkUser1').hide();
					setTimeout('timerfadeout()', 2000);
					$.get(
						'ajax/production/set_project_id/' + curr_job_id + '/' + pjtId,
						{},
						function(_data) {
							try {
							eval ('var data = ' + _data);
							if (typeof(data) == 'object') {
								if (data.error == false) {
									$('h5.project-id-label span').text(pjtId);
									$().unblock();
								} else {
									alert(data.error);
								}
							} else {
								alert('Updating faild, please try again.');
								}
							} catch (e) {
								alert('Invalid response, your session may have timed out.');
							}
						}
					);
				} else {
					$().unblock();
					$('.checkUser').hide(); 
					$('.checkUser1').show();
					setTimeout('timerfadeout()', 2000);
				}
            }
        });
	}
}

//updating the project value.
function setProjectVal() {
	$('#pjt_val_errormsg, .checkVal1, .checkVal').hide();
	var pjt_value = $('#pjt_value').val()
	if (pjt_value == 0) {
		$('#pjt_val_errormsg').text('Please Enter Project Value!');
		$('#pjt_val_errormsg').show();
		setTimeout('timerfadeout()', 3000);
		return false;
	} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var baseurl = $('.hiddenUrl').val();
            $.ajax({
            url : baseurl + 'welcome/getPjtValFromdb/' + pjt_value,
            cache : false,
            success : function(response){
                $('#checkVal').hide();
                if(response == 'userOk') {	
					$('#checkVal').show(); 
					$('#checkVal1').hide();
					setTimeout('timerfadeout()', 3000);
					$.get(
						'ajax/production/set_project_value/' + curr_job_id + '/' + pjt_value,
						{},
						function(_data) {
							try {
							eval ('var data = ' + _data);
							if (typeof(data) == 'object') {
								if (data.error == false) {
									$('h5.project-val-label span').text(pjt_value);
									$().unblock();
								} else {
									alert(data.error);
								}
							} else {
								alert('Updating faild, please try again.');
								}
							} catch (e) {
								alert('Invalid response, your session may have timed out.');
							}
						}
					);
				} else {
					$().unblock();
					$('#checkVal').hide(); 
					$('#checkVal1').show();
					setTimeout('timerfadeout()', 3000);
				}
            }
        });
	}
}

//update the project status.
function setProjectStatus() {
	var pjt_stat = $('#pjt_status').val()
	$.blockUI({
		message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
		css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
	});
	var baseurl = $('.hiddenUrl').val();
	$.ajax({
		type: 'POST',
		url: 'ajax/production/set_project_status/',
		dataType: 'json',
		data: 'pjt_stat='+pjt_stat+'&job_id='+curr_job_id+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
		success: function(data) {
			if (data.error == false) {
				$('#resmsg').show();
				$('#resmsg').html("<span class='ajx_success_msg'>Status Updated.</span>");
				$().unblock();
			} else {
				$('#resmsg').show();
				$('#resmsg').html("<span class='ajx_failure_msg'>Error in Updation</span>.");
				$().unblock();
			}
		}
	});
	setTimeout('timerfadeout()', 3000);
}

function setProjectStatusDate(date_type) {
	$("#errmsg, #errmsg_start_dt").hide();
	
	var set_date_type, date_val, d_class;
	
	if (date_type == 'start') {
		set_date_type = 'start';
		date_val = $('#project-start-date').val();
		d_class = 'startdate';
	} else {
		set_date_type = 'end';
		date_val = $('#project-due-date').val();
		d_class = 'deadline';
	}
	
	var pr_date = $('#project_lead').val()
	if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
		//alert('Please enter planned ' + set_date_type + ' date');
		if (set_date_type == 'start') {
			//showing error message As DOM type - Start Date
			$("#errmsg_start_dt").text('Please enter planned ' + set_date_type + ' date');
			$("#errmsg_start_dt").show();
		} else {
			//showing error message As DOM type - End Date
			$("#errmsg").text('Please enter planned ' + set_date_type + ' date');
			$("#errmsg").show();
		}
		return false;
	} else {
		$.get(
			'ajax/production/set_project_status_date/' + curr_job_id + '/' + set_date_type + '/' + date_val,
			{},
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('h6.project-' + d_class + '-label span').text(date_val);
							$('.project-' + d_class + '-change:visible').hide(200);
						} else {
							if (set_date_type == 'start') {
								$("#errmsg_start_dt").text(data.error);
								$("#errmsg_start_dt").show();
							} else {
								$("#errmsg").text(data.error);
								$("#errmsg").show();
							}
						}
					} else {
						if (set_date_type == 'start') {
							$("#errmsg_start_dt").text('Updating faild, please try again.');
							$("#errmsg_start_dt").show();
						} else {
							$("#errmsg").text('Updating faild, please try again.');
							$("#errmsg").show();
						}
					}
				} catch (e) {
					if (set_date_type == 'start') {
						$("#errmsg_start_dt").text('Invalid response, your session may have timed out.');
						$("#errmsg_start_dt").show();
					} else {
						$("#errmsg").text('Invalid response, your session may have timed out.');
						$("#errmsg").show();
					}
				}
			}
		);
	}
}


function actualSetProjectStatusDate(date_type) {	
	var set_date_type, date_val, d_class;
	$("#errmsg_actual_start_dt, #errmsg_actual_end_dt").hide();
	if (date_type == 'start')
	{
		set_date_type = 'start';
		date_val = $('#actual-project-start-date').val();
		d_class = 'startdate';
	}
	else
	{
		set_date_type = 'end';
		date_val = $('#actual-project-due-date').val();
		d_class = 'deadline';
	}
	var pr_date = $('#project_lead').val()
	if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
		if (set_date_type == 'start') {
			$("#errmsg_actual_start_dt").text('Please enter actual ' + set_date_type + ' date');
			$("#errmsg_actual_start_dt").show();
		} else {
			$("#errmsg_actual_end_dt").text('Please enter actual ' + set_date_type + ' date');
			$("#errmsg_actual_end_dt").show();
		}
		return false; 
	} else {
		$.get(
			'ajax/production/actual_set_project_status_date/' + curr_job_id + '/' + set_date_type + '/' + date_val,
			{},
			function(_data) { //alert(_data);
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('h6.actual-project-' + d_class + '-label span').text(date_val);
							$('.actual-project-' + d_class + '-change:visible').hide(200);
						} else {
							if (set_date_type == 'start') {
								$("#errmsg_actual_start_dt").text(data.error);
								$("#errmsg_actual_start_dt").show();
							} else {
								$("#errmsg_actual_end_dt").text(data.error);
								$("#errmsg_actual_end_dt").show();
							}
						}
					} else {
						if (set_date_type == 'start') {
							$("#errmsg_actual_start_dt").text('Updating faild, please try again.');
							$("#errmsg_actual_start_dt").show();
						} else {
							$("#errmsg_actual_end_dt").text('Updating faild, please try again.');
							$("#errmsg_actual_end_dt").show();
						}
					}
				} catch (e) {
					if (set_date_type == 'start') {
						$("#errmsg_actual_start_dt").text('Invalid response, your session may have timed out.');
						$("#errmsg_actual_start_dt").show();
					} else {
						$("#errmsg_actual_end_dt").text('Invalid response, your session may have timed out.');
						$("#errmsg_actual_end_dt").show();
					}
				}
			}
		);
	}
}


function whatAreStickies() {
	var msg = 'Stickies are logs that are important.\nInformation that is vital to the job.\nInformtion that you need to find quickily without reading through all the communication.\nA URL, FTP/MySQL details, Important changes etc.';
	alert(msg);
	return false;
}

function whatIsSignature() {
	var msg = 'This is your signature!\nThis will be attached to any log that you email through.\nGo to "My Account" page to set your signature.';
	alert(msg);
	return false;
}

function downloadCustomPDF()
{
	var errors = [];
	var custom_date = $('.download-invoice-option input[name="use_custom_date"]').val();
	var required_balance = $('#new-balance-due').val();
	var total_balance = $('#ex-balance-due').val();
	
	if ( ! /^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/.test(custom_date))
	{
		errors.push('Invalid date format provided!');
	}
	
	if (/[^0-9\.]/.test(required_balance))
	{
		errors.push('Payment due should be a numeric value.');
	}
	
	if (parseFloat(required_balance) > parseFloat(total_balance))
	{
		errors.push('Requested value should be less than or equal to the payment due.');
	}
	
	if (errors.length > 0)
	{
		alert(errors.join('\n'));
		return false;
	}
	else
	{
		var pdf_url = '<?php echo $this->config->item('base_url') ?>welcome/view_plain_quote/<?php echo $quote_data['jobid'] ?>/TRUE/TRUE/FALSE/output-<?php echo $quote_data['invoice_no'] ?>/template/';
		
		if ($('.download-invoice-option input[name="ignore_content_policy"]').is(':checked'))
		{
			pdf_url = pdf_url + 'FALSE';
		}
		
		$('.download-invoice-option').attr('action', pdf_url);
		$('.download-invoice-option').attr('onsubmit', '');
		$('.download-invoice-option').submit().slideUp('fast').attr('onsubmit', 'return false;');
	}
	
	return false;
}




<?php
if (isset($userdata) && $userdata['level'] < 2)
{
?>
/* function to add the auto log */
function qcOKlog() {
	
	var msg = "VCS QC Officer Log Check - All Appears OK";
	
	if (!window.confirm('Are you sure you want to stamp the OK log?\n"' + msg + '"')) return false;
	
	$('.user .production-manager-user').attr('checked', true);
	$('#job_log').val(msg);
	$('#add-log-submit-button').click();
	
}


<?php
}
?>

$(function(){
	
	//$('#set-payment-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M' });
	$('#set-payment-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	//$('#payment-recieved-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	$('#payment-recieved-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy', maxDate: '0'});
	//$('#update-payment-terms #	').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M' });
	$('#set-deposits .pick-date, .download-invoice-option .pick-date, .download-invoice-option-log .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -30, maxDate: '+1M' });
	//$('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});
	$('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	//$('.milestone-date .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
	
	$('.task-list-item').livequery(function(){
		$(this).hover(
			function() { $('.delete-task', $(this)).css('display', 'block'); },
			function() { $('.delete-task', $(this)).css('display', 'none'); }
		);
	});
	
	$('#email_to_customer').change(function(){
		if ($(this).is(':checked'))	{
			$('#multiple-client-emails').slideDown(400)
				.children('input[type=checkbox]:first').attr('checked', true);
		} else {
			$('#additional_client_emails').val('');
			$('#multiple-client-emails').children('input[type=checkbox])').attr('checked', false).end()
				.slideUp(400);
		}
	});
	
	$('#attach_pdf').change(function(){
		if ($(this).is(':checked'))	{
			$('.download-invoice-option-log:not(:visible)').slideDown(400);
		} else {
			$('.download-invoice-option-log:visible').slideUp(400);
		}
	});
	
	
	$("#job-view-tabs").tabs({
		selected: 1,
		show: function (event, ui) {
			if (ui.index == 3)
			{
				loadExistingTasks();
			}
			else if (ui.index == 4)
			{
				populateJobOverview();
			}
			else if (ui.index == 9)
			{
				populatePackage();
			}
		}
	});
	
	$('#job-url-list li a:not(.file-delete)').livequery(function(){
		$(this).click(function(){
			window.open(this.href);
			return false;
		});
	});
	
	try {
		var sb_ol = $('.status-bar').offset().left;
		$('.status-bar').mousemove(function(e){
			var wd = e.clientX - sb_ol;
			$('.over', $(this)).css({width: wd + 'px', opacity:0.5});
		});
		
		$('.status-bar').bind('mouseleave', function(e){
			$('.over', $(this)).stop().animate({opacity:0}, 600);
		});
		
		$('.status-bar a').click(function(){
			var pos = $(this).attr('rel');
			if (window.confirm('Are you sure that you want to change\nthe status to ' + pos * 10 +'% completion?'))
			{
				updateJobStatus(pos);
			}
			$('.status-bar span.over').fadeOut();
			return false;
		});
	} catch (e) { if (window.console) console.log(e); }
	
	<?php
	if (is_numeric($quote_data['complete_status']))
	{
		echo "updateVisualStatus('" . (int) $quote_data['complete_status'] . "');\n";
	}
	?>
	
	$('#enable_post_profile').click(function(){
		if ($(this).is(':checked'))
		{
			$('.post-profile-select').show();
		}
		else
		{
			$('.post-profile-select').hide();
		}
	});
	
	$('.jump-to-job select').change(function(){
		var _new_location = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{jobid}}', $_SERVER['REQUEST_URI']) ?>';
		document.location = _new_location.replace('{{jobid}}', $(this).val());
	});
	

	$('#job_log').siblings().hide();
	
	$('#job_log').focus(function(){
		$(this).siblings(':hidden').not('#multiple-client-emails').slideDown('fast');
		if ($(this).val() == 'Click to view options') {
			$(this).val('');
			$(this).removeClass('gray-text');
		}
	});
	

	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 240) {
			$(this).focus().val(desc_len.substring(0, 240));
		}
		
		var remain_len = 240 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#task-desc-countdown').text(remain_len);
	});
	
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 240) {
			$(this).focus().val(desc_len.substring(0, 240));
		}
		
		var remain_len = 240 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#edit-task-desc-countdown').text(remain_len);
	});
	
	// Sasha's quick keys
	$('#job_log').keydown(function (e) {

	if (e.ctrlKey && e.keyCode == 13) {

		// Entered values:
		var minutesInput = $('#log_minutes');
		var minutes = minutesInput.val();

		// Check the values that are required (time and recipients)
		//
		// if either are empty, use prompt() dialog boxes to use them.
		// EDIT: In fact, a prompt will use enter, so we can jam it down if needed.

		var newMinutes = prompt('Time in minutes', minutes);
		if(minutes != newMinutes) {
			minutesInput.val(newMinutes);
		}

		var contactsText = prompt('Select contacts (min 3 letters). Seperate with a space.');
		var contacts = contactsText.split(' ');
		for(i in contacts) {
			// Check the ones that match.
			//
			// Modifications needed: this needs to be case insensitive.
			if(contacts[i].length >= 3) {
				contacts[i].replace(/\w+/g, function(a){
					contacts[i]  = a.charAt(0).toUpperCase() + a.substr(1).toLowerCase();
				});
				
				var scope = $('.user label:contains("' + contacts[i] + '")').parent();
				$('input[type=checkbox]', scope).attr('checked', true);
			}
		}
		var recipients = 'Send to the following recipients:\n';
		$('.user input[type=checkbox]:checked').each(
			function () {
				recipients += $('label', $(this).parent()).text() + '\n';
			}
		);
		
		if(confirm(recipients)) {
			addLog();
		}
		return false;
	}
});
	
});

var job_complete_percentage;

function updateJobStatus(status) {
	$('.status-bar').block({
            message:'<img src="assets/img/ajax-loader.gif" />',
			css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
        });
	$.get(
		'ajax/request/update_job_status/',
		{jobid: curr_job_id, job_status: status},
		function(_data) {
			try {
				eval('data = ' + _data);
				if (typeof(data) == 'object') {
					if (data.error == false) {
						pos_just_completed = true;
						status = status * 10;
						updateVisualStatus(status);
						location.reload(); 
					} else {
						alert(data.error);
					}
				} else {
					alert('Your session timed out!');
				}
			} catch (e) {
				alert('Your session timed out!');
			}
			$('.status-bar').unblock();
		}
	);
}

function updateVisualStatus(status) {
	$('h3.status-title .small em strong').html(status);
	$('.status-bar span.bar').animate({width: (status * 3) + 'px'}, 1000);
	job_complete_percentage = status;
}

function setContractorJob()
{
	$("div#errMsgPjtNulMem").hide();
	var contractors = [];
	
	var p = $('#project-member').val()
	
	var arr = new Array;
	$("#select2 option").each ( function() {
		arr.push ( $(this).val() );
	});
	
	if (arr.length === 0) {
		$("div#errMsgPjtNulMem").show();
		//return false;
	}
	//else {
	$('select#select2 option').each(function(){
		contractors.push($(this).val());
	});
	
	//alert(contractors); return false;
	
	var data = {'contractors': contractors.join(','), 'jobid': curr_job_id, 'project-mem': p,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
	$.post(
		'welcome/ajax_set_contractor_for_job',
		data,
		function(xd)
		{
			if (xd.error)
			{
				alert(xd.error);
			}
			else if (xd.status == 'OK')
			{
				document.location.reload();
			}
		},
		'json'
	);

}


</script>

<style type="text/css">
#jv-tab-8 .task-list-item td {
	padding:2px 10px;
}
#jv-tab-8 .task-list-item tr.complete td {
	background:green;
	color:#fff;
}
.email-set-options {
	padding:8px 0 6px;
	margin-bottom:10px;
	border-bottom:1px solid #757575;
	border-top:1px solid #757575;
}
.client-comm-options {
	float:right;
	width:185px;
}
.client-comm-options .action-td {
	padding-right:4px;
	padding-top:2px;
}
</style>

<div id="content">
    <?php
		$date_used = $quote_data['date_created']; 
	?>
    <div class="inner q-view">
		<div class="right-communication">		
			<form id="comm-log-form">
			
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<textarea name="job_log" id="job_log" class="textfield width99pct height100px gray-text">Click to view options</textarea>
				<div style="position:relative;">
					<textarea name="signature" class="textfield width99pct" rows="4" readonly="readonly" style="color:#666;"><?php echo $userdata['signature'] ?></textarea>
					<span style="position:absolute; top:5px; right:18px;"><a href="#comm-log-form" onclick="whatIsSignature(); return false;">What is this?</a></span>
				</div>
				
				<div style="overflow:hidden;">
					
					<p class="right" style="padding-top:5px;">Mark as a <a href="#was" onclick="whatAreStickies(); return false;">stickie</a> <input type="checkbox" name="log_stickie" id="log_stickie" /></p>
					<div class="button-container">
						<div class="buttons">
							<button type="submit" class="positive" onclick="addLog();  return false;" id="add-log-submit-button">Add Post</button>
						</div>
					</div>
				
				</div>
			
			<?php
			if (isset($userdata))
			{
			?>
				<div class="email-set-options" style="overflow:hidden;">
					<table border="0" cellpadding="0" cellspacing="0" class="client-comm-options">
						<tr>
							<td rowspan="2" class="action-td" valign="top" align="right"><a href="#" onclick="addClientCommOptions(); $(this).blur(); return false;">Communicate<br />to Client via</td>
							<td><input type="checkbox" name="client_comm_phone" value="<?php echo (isset($quote_data['phone_1'])) ? $quote_data['phone_1'] : '' ?>"> <span>Phone</span></td>
							<td><input type="checkbox" name="client_comm_sms" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>SMS</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="client_comm_mobile" value="<?php echo (isset($quote_data['phone_3'])) ? $quote_data['phone_3'] : '' ?>"> <span>Mobile</span></td>
							<td><input type="checkbox" name="client_comm_email" value="<?php echo (isset($quote_data['email_1'])) ? $quote_data['email_1'] : '' ?>"> <span>Email</span></td>
						</tr>
					</table>
					
					<script type="text/javascript">
					
					var client_comm_options_order = [];
					
					$(function(){
						$('.client-comm-options input[type="checkbox"]').click(function(){
							var el = $(this);
							setTimeout(function(){
								if (el.is(':checked'))
								{
									if ($.inArray(el.attr('name'), client_comm_options_order) == -1)
									{
										client_comm_options_order.push(el.attr('name'));
									}
								}
								else
								{
									client_comm_options_order = $.grep(client_comm_options_order, function(value){ return value != el.attr('name') });
								}
							}, 80);
						})
					});
					
					function addClientCommOptions()
					{
						if ($('.client-comm-options input[type="checkbox"]:checked').size() < 1)
						{
							alert('Please select at least one option!');
							return false;
						}
						
						if ($('#job_log').val() != '')
						{
							if ( ! window.confirm('This will replace the text on the log window!\nProceed?'))
							{
								return false;
							}
						}
						
						var text_block = '\nYou are required to contact the client via the following means of communication in the following order:';
						for (i in client_comm_options_order)
						{
							var com_item = $('.client-comm-options input[name="' + client_comm_options_order[i] + '"]');
							text_block += '\n' + com_item.siblings('span').text() + ': ' + com_item.val();
						}
						
						$('#job_log').val(text_block);
						
						return false;
					}
					</script>
					
					<?php if (isset($userdata)){ ?>
					
					<table border="0" cellpadding="0" cellspacing="0" style="display:none;" class="download-invoice-option-log">
						<tr>
							<td>
								&nbsp;Use a custom date<br />
								<input type="text" class="textfield width200px pick-date" name="use_custom_date" value="<?php echo date('d-m-Y', strtotime($date_used)) ?>" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;Adjust current payment due<br />
								<input type="text" class="textfield width200px" name="balance_due" value="" id="new-balance-due-log" />
								<input type="hidden" name="ex_balance_due" value="" id="ex-balance-due-log" />
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;<input type="checkbox" name="ignore_content_policy" id="ignore_content_policy" /> <label for="ignore_content_policy" class="normal">Don't attach content policy to PDF</label>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;Add a description - 100 characters<br />
								<input type="text" class="textfield width250px" name="custom_description" value="" maxlength="100" />
							</td>
						</tr>
					</table>
					<br />
					<?php } ?>
					<input type="checkbox" name="email_to_customer" id="email_to_customer" /> <label for="email_to_customer" class="normal">Email Client</label>
					<input type="hidden" name="client_email_address" id="client_email_address" value="<?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?>" />
					<input type="hidden" name="client_full_name" id="client_full_name" value="<?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?>" />
					<input type="hidden" name="requesting_client_approval" id="requesting_client_approval" value="0" />
					
					<p id="multiple-client-emails">
						<input type="checkbox" name="client_emails_1" id="client_emails_1" value="<?php echo $quote_data['email_1'] ?>" /> <?php echo $quote_data['email_1'] ?>
						<?php
						if ($quote_data['email_2'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_2" id="client_emails_2" value="<?php echo $quote_data['email_2'] ?>" /> <?php echo $quote_data['email_2'] ?>
							<?php
						}
						if ($quote_data['email_3'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_3" id="client_emails_3" value="<?php echo $quote_data['email_3'] ?>" /> <?php echo $quote_data['email_3'] ?>
							<?php
						}
						if ($quote_data['email_4'] != '')
						{
							?>
							<br /><input type="checkbox" name="client_emails_4" id="client_emails_4" value="<?php echo $quote_data['email_4'] ?>" /> <?php echo $quote_data['email_4'] ?>
							<?php
						}
						?>
						<br />
						Additional Emails (separate addresses with a comma)<br />
						<input type="text" name="additional_client_emails" id="additional_client_emails" class="textfield width99pct" />
					</p>
					
				</div>
			<?php
			}
			?>
			
				<div class="user-addresses">
					<p><label>Email to:</label></p>
					<?php
				    $restrict1[] = 0;
					if (is_array($contract_users) && count($contract_users) > 0) { 
						foreach ($contract_users as $data) {
							$restrict1[] = $data['userid_fk'];
						}
					}
					//echo "<pre>"; print_r($restrict1);
					
					$r_users = implode(",",$list_users);
					$restrict = explode(",",$r_users);
					//print_r($restrict);
					
					//Merge the contract users, lead owner, lead sssigned_to & project Manager.
					$rest_users = array_merge_recursive($restrict, $restrict1);
					$restrict_users = array_unique($rest_users);
					
					//Re-Assign the Keys in the array.
					$final_restrict_user = array_values($restrict_users);
				
					$cnt = count($user_accounts);
					
					if (count($final_restrict_user)) {
						for($i=0; $i < $cnt; $i++)
						{	
							$usid = $user_accounts[$i]['userid'];

							for($j=0; $j<count($final_restrict_user); $j++) {
							//echo $restrict[$j];

							if($usid == $final_restrict_user[$j]) {
									echo '<span class="user">' .
									'<input type="checkbox" name="email-log-' . $user_accounts[$i]['userid'] . '" id="email-log-' . $user_accounts[$i]['userid'] . '" class="' . $is_pm . '" /> <label for="email-log-' . $user_accounts[$i]['userid'] . '">' . $user_accounts[$i]['first_name'] . ' ' . $user_accounts[$i]['last_name'] . '</label>' .
									'<select name="post_profile_' . $user_accounts[$i]['userid'] . '" class="post-profile-select">' . $post_profile_options . '</select></span>'; 
								}	
							}
							
						}
					}
					else {
						echo "No user found";
					} 
					?>
				</div>
			</form>
			<p>&nbsp;</p>
			<span style="float:right;"> 
				<a href="#" onclick="fullScreenLogs(); return false;">View Full Screen</a>
				|
				<a href="#" onclick="$('.log > :not(.stickie), #pager').toggle(); return false;">View/Hide Stickies</a>
				<?php 
				if (isset($userdata) && $userdata['level']==1 && $userdata['role_id']==1)
				{
				?>
				|
				<a href="#" onclick="qcOKlog(); return false;">All Logs OK?</a>
				<?php 
				}
				?>
			</span>
			<h4>Job History</h4>

			<!--Code Changes for Pagination in Comments Section -- Starts here -->
			<?php if ($log_html != "") { ?>
			<table width="100%" id="lead_log_list" class="log-container"> 
				<thead> 
					<tr> 
						<th></th> 
					</tr> 
				</thead>
				<tbody>
				<?php 
					echo $log_html;
				?>				
				</tbody> 
			</table>
			<div id="pager">
				<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
				<a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
				<span>No. of Records per page:<?php echo '&nbsp;'; ?> </span>
				<select class="pagesize"> 
					<option selected="selected" value="10">10</option> 
					<option value="20">20</option> 
					<option value="30">30</option> 
					<option value="40">40</option> 
				</select> 
			</div>
			<?php }	else {
				echo "No Comments Found."; 
				}
			?>
			<!--Code Changes for Pagination in Comments Section -- Ends here -->
		</div>
		
        <div class="pull-left side1"> 
			<h2 class="job-title">
				<?php
					echo htmlentities($quote_data['job_title'], ENT_QUOTES);
				?>
			</h2>
			<?php
				if (isset($quote_data['pjt_id'])) {
					$varPjtId = $quote_data['pjt_id'];
				}
			?>
			<form>
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<div>
					<div style="float:left;">
						<h5><label class="project-id">Project Id</label>&nbsp;&nbsp;
						<input class="textfield" style="width: 156px;" type="text" name="pjtId" id="pjtId" maxlength="15" value="<?php if (isset($varPjtId)) echo $varPjtId; ?>" <?php if ($chge_access != 1) { ?>readonly<?php } ?> />
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
						<div class="buttons">
							<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 118px;" onclick="setProjectId(); return false;">
								Set Project ID
							</button>
						</div>
						<div class="error-msg">
							<span id="pjt_id_errormsg" style="color:red"></span>
							<span class="checkUser" style="color:green">Project Id Saved.</span>
							<span class="checkUser1" id="id-existsval" style="color:red">Project ID Already Exists.</span>
						</div>
					<?php } ?>
				</div>	
			</form>
			<form>
				<div>
					<div style="float:left;">
						<h5><label class="project-val">Project Value</label>&nbsp;&nbsp;
						<input class="textfield" style="width: 25px; font-weight:bold;" type="text" name="curid" id="curid" value="<?php if (isset($quote_data['expect_worth_name'])) echo $quote_data['expect_worth_name']; ?>" readonly />
						<input class="textfield" style="width: 95px;" type="text" name="pjt_value" id="pjt_value" value="<?php if (isset($quote_data['actual_worth_amount'])) echo $quote_data['actual_worth_amount']; ?>" <?php if ($chge_access != 1) { ?>readonly<?php } ?> onkeypress="return isNumberKey(event)" />
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
					<div class="buttons">
						<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 118px;" onclick="setProjectVal(); return false;">
							Set Project Value
						</button>
					</div>
					<div class="error-msg">
						<span id="pjt_val_errormsg" style="color:red"></span>
						<span id="checkVal" style="color:green">Project Value Updated.</span>
						<span id="checkVal1" id="val-existsval" style="color:red">Project Value Already Exists.</span>
					</div>
					<?php } ?>
				</div>	
			</form>
			<form>
				<div>
					<div style="float:left;">
						<h5><label class="project-val">Project Status</label>&nbsp;&nbsp;
						<select name="pjt_status" id="pjt_status" class="textfield" style="width:138px;">
							<option value="1"  <?php if($quote_data['pjt_status'] == 1) echo 'selected="selected"'; ?>>Project In Progress</option>
							<option value="2"  <?php if($quote_data['pjt_status'] == 2) echo 'selected="selected"'; ?>>Project Completed</option>
							<option value="3"  <?php if($quote_data['pjt_status'] == 3) echo 'selected="selected"'; ?>>Project Onhold</option>
							<option value="4"  <?php if($quote_data['pjt_status'] == 4) echo 'selected="selected"'; ?>>Inactive</option>
                        </select>
						<input type="hidden" class="hiddenUrl"/>
						</h5>
					</div>					
					<?php if ($chge_access == 1) { ?>
					<div class="buttons">
						<button type="submit" class="positive" id="submitid" style="margin:0 0 0 5px; width: 124px;" onclick="setProjectStatus(); return false;">
							Set Project Status
						</button>
						<div id="resmsg" class="error-msg"></div>
					</div>
					<?php } ?>
				</div>	
			</form>
			<div class="action-buttons" style="overflow:hidden;">
				<?php
				require (theme_url().'/tpl/user_accounts_options.php');
				?>
				
				<form name="project_assign" id="project-assign">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0">
						<tr>
							<td valign="top">
								<h5 class="project-lead-label">Project Manager <br /><span class="small">[ 
								<?php if (isset($quote_data['assigned_to']) and is_numeric($quote_data['assigned_to'])) { 
												if(isset($ua_id_name[$quote_data['assigned_to']])) echo $ua_id_name[$quote_data['assigned_to']];
												else echo 'Not Set';
												}
										else echo 'Not Set'; ?> 
                                ]</span>
								</h5>
								<?php
								if ($chge_access == 1) {
								?>
								<p><a href="#" onclick="$('.project-lead-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-lead-change">
									<select name="project_lead" id="project_lead" class="textfield">
										<option value="0">Please Select</option>
										<?php echo $pm_options ?>
									</select>
									<span id="pjt_lead_errormsg" style="color:red; float:left;"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectLead(); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-lead-change:visible, #pjt_lead_errormsg').hide(200); return false;">Cancel</button>
									</div>
									<input type="hidden" value="" id="previous-project-manager"/>
								</div>
								<?php
								}
								?>
							</td>
						</tr>
					</table>
				</form>
				
				<h3 class="status-title">Adjust project status <span class="small">[ current status - <em><strong>0</strong>% Complete</em> ]</span></h3>

				<p class="status-bar">
					<span class="bar"></span>
					<?php if ($chge_access == 1) { ?>
					<span class="over"></span>
					<a href="#" class="p1" rel="1"></a>
					<a href="#" class="p2" rel="2"></a>
					<a href="#" class="p3" rel="3"></a>
					<a href="#" class="p4" rel="4"></a>
					<a href="#" class="p5" rel="5"></a>
					<a href="#" class="p6" rel="6"></a>
					<a href="#" class="p7" rel="7"></a>
					<a href="#" class="p8" rel="8"></a>
					<a href="#" class="p9" rel="9"></a>
					<a href="#" class="p10" rel="10"></a>
					<?php } ?>
				</p>

				<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
					
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />				
				
					<table>
						<tr>
							<td valign="top" width="175">
								<h6 class="project-startdate-label">Planned Project Start Date &raquo; <span><?php if ($quote_data['date_start'] != '') echo date('d-m-Y', strtotime($quote_data['date_start'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1){ ?>
								<p><a href="#" onclick="$('.project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-startdate-change">
									<input type="text" value="" class="textfield pick-date" id="project-start-date" />
									<span id="errmsg_start_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('start'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-startdate-change:visible, #errmsg_start_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
							<td valign="top" width="175">
								<h6 class="project-deadline-label">Planned Project End Date &raquo; <span><?php if ($quote_data['date_due'] != '') echo date('d-m-Y', strtotime($quote_data['date_due'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) {?>
								<p><a href="#" onclick="$('.project-deadline-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-deadline-change">
									<input type="text" value="" class="textfield pick-date" id="project-due-date" />
									<span id="errmsg" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('due'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-deadline-change:visible , #errmsg').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td valign="top" width="175">
								<h6 class="actual-project-startdate-label">Actual Project Start Date &raquo; <span><?php if ($quote_data['actual_date_start'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_start'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) { ?>
								<p><a href="#" onclick="$('.actual-project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								<div class="actual-project-startdate-change">
									<input type="text" value="" class="textfield pick-date" id="actual-project-start-date" />
									<span id="errmsg_actual_start_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="actualSetProjectStatusDate('start'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.actual-project-startdate-change:visible, #errmsg_actual_start_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
							<td valign="top" width="175">
								<h6 class="actual-project-deadline-label">Actual Project End Date &raquo; <span><?php if ($quote_data['actual_date_due'] != '') echo date('d-m-Y', strtotime($quote_data['actual_date_due'])); else echo 'Not Set'; ?></span></h6>
								<?php if ($chge_access == 1) { ?>
								<p><a href="#" onclick="$('.actual-project-deadline-change:hidden').show(200); return false;">Change?</a></p>
								<div class="actual-project-deadline-change">
									<input type="text" value="" class="textfield pick-date" id="actual-project-due-date" />
									<span id="errmsg_actual_end_dt" style="color:red"></span>
									<div class="buttons">
										<button type="submit" class="positive" onclick="actualSetProjectStatusDate('due'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.actual-project-deadline-change:visible, #errmsg_actual_end_dt').hide(200); return false;">Cancel</button>
									</div>
								</div>
								<?php } ?>
							</td>
						</tr>
					</table>
					
				</form>
				
				<form name="contractor-assign">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<h5 class="project-lead-label">Assign Project Team</h5>
					<p><a href="javascript:void(0);" id="show-btn">Show</a></p>
										
					<div id="show-con">
						<?php if ($chge_access == 1) { ?>
							<div class="list-contractors">
								<?php //echo $contractor_list ?>
								<div style="float:left;">
									<span style="padding-left: 55px;">Members</span><br />
									<select multiple="multiple" id="select1"><?php echo $contractor_list_select1 ?></select>
								</div>
								
								<div style="float:left; padding-top: 29px; padding-left: 10px; padding-right: 10px;">
									<input type="button" id="add" class="add-member" value="&gt;&gt;" /><br />
									<input type="button" id="remove" class="remove-member" value="&lt;&lt;" />
									<input type="hidden" value ="" id="project-member" name="project-member"/>
								</div>
								<div style="float:left;">
									<span style="padding-left: 45px;">Project Team</span><br />
									<select multiple="multiple" name="select2" id="select2" ><?php echo $contractor_list_select2 ?></select>
								</div>
							</div>
						<?php 
						} else { 
						?>
								<span style="padding-left: 45px;">Project Team</span><br />
								<select id="select3" multiple="multiple"><?php echo $contractor_list_select2 ?></select>
						<?php		
						}
						?>
						<?php 
							if ($chge_access == 1) { 
						?>
							<div class="buttons" style="clear:both;">
								<button type="submit" class="positive" id="positiveSelectBox" onclick="setContractorJob(); return false;">Set Project Team</button>
								<div id="errMsgPjtNulMem" class="error-msg" style="display:none; color:#FF4400;">Please assign any project member.</div>
							</div>
						<?php 
						} 
						?>
					</div>
				</form>

			</div>

			<div>
				<p id="temp">&nbsp;</p>
				<ul id="job-view-tabs">
					<li><a href="#jv-tab-1">Payment Milestones</a></li>
					<li><a href="#jv-tab-2">Document</a></li>
					<li><a href="#jv-tab-3" >Files</a></li>
					<li><a href="#jv-tab-4" >Tasks</a></li>
					<li><a href="#jv-tab-4-5">Milestones</a></li>
					<li><a href="#jv-tab-5">Customer</a></li>
					<li><a href="#jv-tab-7">URLs</a></li>
				</ul>
			</div>
			<div id="jv-tab-1">
				<div class="q-view-main-top">
					
					<div class="payment-buttons clearfix">
						<div class="buttons">
							<a class="payment-profile-button positive" href="#" onclick="">Payment Terms</a>
						</div>
						<div class="buttons">
						<a class="payment-received-button positive" href="#" onclick="">Payment Received</a>
						</div>
					</div>
				<div style="color:red; margin:7px 0 0;" id="rec_paymentfadeout"></div>
				<?php				
				if ($quote_data['payment_terms'] == 0 || $quote_data['payment_terms'] == 1)
				{
				?>
					<div class="payment-profile-view" id="payment-profile-view" style="float:left;"><br/>
						<form id="set-payment-terms">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<table class="payment-table">
							<tr>
								<td>
									<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" class="textfield width200px" /> </p>
									<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" class="textfield width200px pick-date" /> </p>
									<p>Value *<input onkeypress="return isNumberKey(event)" type="text" name="sp_date_3" id="sp_date_3" class="textfield width200px" /> <span style="color:red;">(Numbers only)</span></p>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button>
									</div>
									<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
									<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
								</td>
							</tr>
						</table>
						</form>
					</div>
					<?php
						//mychanges
						$jid = $this->uri->segment(3); //16 
						$jsql = $this->db->query("select expect_worth_id from crms_jobs where jobid='$jid'");
						$jres = $jsql->result();
						$worthid = $jres[0]->expect_worth_id;
						$expect_worth = $this->db->query("select expect_worth_name from crms_expect_worth where expect_worth_id='$worthid'");
						$eres = $expect_worth->result();
						$symbol = $eres[0]->expect_worth_name;
						
						$output = '';
						$output .= '<div class="payment-terms-mini-view1" style="display:block; float:left; margin-top:5px;">';
					    if(!empty($payment_data))
						{
							$pdi = 1;
							$pt_select_box = '';
							$pt_select_box .= '<option value="0"> &nbsp; </option>';
							$output .= "<table width='100%' class='payment_tbl'>
							<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
							<tr>
							<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
							<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
							<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
							</tr>
							</table>";
							$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
							$output .= "<thead>";
							$output .= "<tr align='left' >";
							$output .= "<th class='header'>Payment Milestone</th>";
							$output .= "<th class='header'>Milestone Date</th>";
							$output .= "<th class='header'>Amount</th>";
							$output .= "<th class='header'>Status</th>";
							$output .= "<th class='header'>Action</th>";
							$output .= "</tr>";
							$output .= "</thead>";
							foreach ($payment_data as $pd)
							{
								$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
								$payment_amount = number_format($pd['amount'], 2, '.', ',');
								$total_amount_recieved += $pd['amount'];
								$payment_received = '';
								if ($pd['received'] == 0)
								{
									$payment_received = '<img src="assets/img/payment-due.jpg" alt="Due" height="10" width="10" />';
								}
								else if ($pd['received'] == 1)
								{
									$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
								}
								else
								{
									$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
								}							
								$output .= "<tr>";
								$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
								$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
								$output .= "<td align='left'> ".$symbol.' '.number_format($pd['amount'], 2, '.', ',')."</td>";
								$output .= "<td align='center'>".$payment_received."</td>";
								$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' >Edit</a> | ";
								$output .= "<a class='edit' onclick='paymentProfileDelete(".$pd['expectid'].");' >Delete</a></td>";
								$output .= "</tr>";
								//echo "<p><strong>Payment #{$pdi}</strong> &raquo; {$pd['percentage']}% by {$expected_date} = \${$payment_amount} {$payment_received}</p>";
								$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . $pd['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
								$pdi ++;
							}
							$output .= "<tr>";
							$output .= "<td></td>";
							$output .= "<td colspan='0'><b>Total Milestone Payment :</b></td><td><b>".$symbol.' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
							$output .= "</tr>";
							$output .= "</table>";
						}
						$output .= '</div>';
					    echo $output;
						?>
						<!--payment received starts here -->

						<div class="payment-recieved-view" id="payment-recieved-view" style="display:none;float:left;"><br/>
						<form id="payment-recieved-terms">
						
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
							<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" class="textfield width200px" /> </p>
							<p>Amount Recieved *<input type="text" name="pr_date_2" onkeypress="return isNumberKey(event)" id="pr_date_2" class="textfield width200px" /><span style="color:red;">(Numbers only)</span></p>
							<p>Date Recieved *<input type="text" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" /> </p>
							
							<?php if (isset($pt_select_box)) { ?>
							<p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } 
							else { ?>
							  <p>Map to a payment term *<select name="deposit_map_field" class="deposit_map_field" style="width:210px;"><?php echo $pt_select_box; ?></select></p>
							<?php } ?>
							
							<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" ></textarea> </p>
							<div class="buttons">
								<button type="submit" class="positive" onclick="setPaymentRecievedTerms(); return false;">Add Payment</button>
							</div>
							<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
							<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
						</form>
					    </div>
						<?php 
						//mychanges
						$jid = $this->uri->segment(3); //16 
						$jsql = $this->db->query("select expect_worth_id from crms_jobs where jobid='$jid'");
						$jres = $jsql->result();
						$worthid = $jres[0]->expect_worth_id;
						$expect_worth = $this->db->query("select expect_worth_name from crms_expect_worth where expect_worth_id='$worthid'");
						$eres = $expect_worth->result();
						$symbol = $eres[0]->expect_worth_name;
			
						$output = '';
						$output .= '<div class="payment-received-mini-view1" style="float:left; display:none; margin-top:5px;">';
						if(!empty($deposits_data))
						{
							//echo "<pre>"; print_r($deposits_data); exit;
							//$output .= '<h3>Payment Recieved</h3>';
							$pdi = 1;
							$output .= '<option value="0"> &nbsp; </option>';
							$output .= "<p><h6>Payment History</h6></p>";
							$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
							$output .= "<thead>";
							$output .= "<tr align='left'>";
							$output .= "<th class='header'>Invoice No</th>";
							$output .= "<th class='header'>Date Received</th>";
							$output .= "<th class='header'>Amt Received</th>";
							$output .= "<th class='header'>Payment Term</th>";
							//$output .= "<th class='header'>Status</th>";
							$output .= "<th class='header'>Action</th>";
							$output .= "</tr>";
							$output .= "</thead>";
							foreach ($deposits_data as $dd)
							{
								$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
								$payment_amount = number_format($dd['amount'], 2, '.', ',');
								$amount_recieved += $dd['amount'];								
								$output .= "<tr align='left'>";
								$output .= "<td>".$dd['invoice_no']."</td>";
								$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
								$output .= "<td> ".$symbol.' '.number_format($dd['amount'], 2, '.', ',')."</td>";
								$output .= "<td>".$dd['payment_term']."</td>";
								$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
								$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
								$output .= "</tr>";
							}
							$output .= "<tr>";
							$output .= "<td></td>";
							$output .= "<td><b>Total Payment: </b></td><td colspan='2'><b>".$symbol.' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
							$output .= "</tr>";
							$output .= "</table>";
						   
						}
						$output .= "</div>";
						echo $output;
						?>
					<!--payment received ends here -->
				<?php
				}
				?>
	
				</div><!-- class:q-view-main-top end -->
			</div><!-- id: jv-tab-1 end -->
			<div id="jv-tab-2"> 
				
				<p style="text-align:right;"><a href="#" onclick="downloadCustomPDF(); return false;">
					<img src="assets/img/download_pdf.gif?q=1" alt="Download PDF" /></a>
				</p>
				<form class="download-invoice-option" style="display:none;" action="welcome/view_plain_quote/<?php echo $quote_data['jobid'] ?>/TRUE" method="post" target="_blank" onsubmit="return false;">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="0">
						
						<tr style="display:none;">
							<td>
								&nbsp;Use a custom date<br />
								<input type="text" class="textfield width200px pick-date" name="use_custom_date" value="<?php echo date('d-m-Y', strtotime($date_used)) ?>" readonly="readonly" />
							</td>
							<td>
								&nbsp;Adjust current payment due<br />
								<input type="text" class="textfield width200px" name="balance_due" value="" id="new-balance-due" />
								<input type="hidden" name="ex_balance_due" value="" id="ex-balance-due" />
							</td>
						</tr>
						<tr>
							<td>
								<div class="buttons">
									<button type="submit" class="positive" onclick="downloadCustomPDF(); return false;">Download PDF</button>
								</div>
							</td>
							<td style="display:none;">
								<input type="checkbox" checked="checked" name="ignore_content_policy" />
								Don't attach content policy
							</td>
						</tr>
					</table>
				</form>

				<div class="q-container">
					<div class="q-details">
						<div class="q-top-head">
							<div class="q-cust">
								<h3 class="q-id"><em>Project</em> &nbsp; <span>#<?php echo  (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?></span></h3>
								<p class="q-date"><em>Date</em> <span><?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?></span></p>
								<p class="q-cust-company"><em>Company</em> <span><?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?></span></p>
								<p class="q-cust-name"><em>Contact</em> <span><?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?></span></p>
								<p class="q-cust-email"><em>Email</em> <span><?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?></span></p>
								<p class="q-service-type"><em>Service</em> <span><?php echo  (isset($quote_data)) ? $cfg['job_categories'][$quote_data['job_category']] : '' ?></span></p>
							</div>
							
							<p><img src="assets/img/qlogo.jpg?q=1" alt="" /></p>
						</div>
						<div class="q-quote-items">
							<h4 class="quote-title">Project Name : <?php echo (isset($quote_data)) ? $quote_data['job_title'] : '' ?></h4>
							<ul id="q-sort-items"></ul>
						</div>
					</div>
				</div>
				<div class="q-sub-total<?php if ( ! $sensitive_information_allowed) echo ' display-none' ?>">
					<table class="width565px" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="160">Sale Amount <span id="sale_amount"></span></td>
							<td width="120" align="right">GST <span id="gst_amount"></span></td>
							<td width="20">&nbsp;</td>
							<td align="right">Total inc GST <span id="total_inc_gst"></span></td>
						</tr>
					</table>
				</div>
				<!--<div class="q-sub-total<?php //if (! in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)) || ! $sensitive_information_allowed) echo ' display-none' ?>">-->
				<div class="q-sub-total<?php if ( ! $sensitive_information_allowed) echo ' display-none' ?>">
					<table class="width565px" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="160">&nbsp;</td>
							<td width="120" align="right">Deposits <span id="deposit_amount"></span></td>
							<td width="20">&nbsp;</td>
							<td align="right">Balance Due <span id="balance_amount"></span></td>
						</tr>
					</table>
				</div>

			</div><!-- id: jv-tab-2 end -->
			
			<div id="jv-tab-3">
				<form name="ajax_file_upload">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<div id="upload-container">
						<img src="assets/img/select_file.jpg" alt="Browse" id="upload-decoy" />
						<input type="file" class="textfield" id="ajax_file_uploader" name="ajax_file_uploader" onchange="return runAjaxFileUpload();" size="1" />
					</div>
					<ul id="job-file-list">
					<?php echo $job_files_html ?>
					</ul>
				</form>
				
			</div><!-- id: jv-tab-3 end -->
			
			<div id="jv-tab-4">
				<form id="set-job-task" onsubmit="return false;">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<h3>Tasks</h3>
					<table border="0" cellpadding="0" cellspacing="0" class="task-add  toggler">
						
						<tr>
							<td colspan="4">
								<strong>All fields are required!</strong>
							</td>
						</tr>
						
						<tr>
							<td valign="top">
								<br /><br />Task
							</td>
							<td colspan="3">
								<strong><span id="task-desc-countdown">240</span></strong> characters left.<br />
								<textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Allocate to
							</td>
							<td>
								<select name="task_user" class="textfield width100px">
								<?php
								//echo $remind_options, $remind_options_all, $contractor_options;
								echo $remind_options, $remind_options_all;
								?>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>
								Planned Start Date
							</td>
							<td>
								<input type="text" name="task_start_date" class="textfield pick-date width100px" />
							</td>
							<td>
								Planned End Date
							</td>
							<td>
								<input type="text" name="task_end_date" class="textfield pick-date width100px" />
							</td>
							
						</tr>
						<tr>
							<td>Remarks</td>
							<td colspan="3"><textarea name="remarks" id="task-remarks" class="task-remarks" width="420px"></textarea></td>
						</tr>
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="addNewTask();">Add</button>
								</div>
								<div class="buttons">
									<button type="submit" class="negative" onclick="$('.toggler').slideToggle();">Cancel</button>
								</div>
							</td>
						</tr>
					</table>
					<div class="buttons task-init  toggler">
						<button type="button" class="positive" onclick="$('.toggler').slideToggle();">Add New</button>
					</div>
					
					<div class="existing-task-list">
						<br /><br />
						<h4>Existing Tasks</h4>
					</div>
				</form>
				
				<form id="edit-job-task" onsubmit="return false;">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<!-- edit task -->
					<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
						
						<tr>
							<td colspan="4">
								<strong>All fields are required!</strong>
							</td>
						</tr>
						
						<tr>
							<td valign="top" width="80">
								<br /><br />Task
							</td>
							<td colspan="3">
								<strong><span id="edit-task-desc-countdown">240</span></strong> characters left.<br />
								<textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Allocate to
							</td>
							<td>
								<select name="task_user" class="edit-task-allocate textfield width100px">
								<?php
								echo $remind_options, $remind_options_all, $contractor_options;
								?>
								</select>
							</td>
							
						</tr>
						
						<tr>
							<td>
								Planned Start Date
							</td>
							<td>
								<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
							</td>
							<td>
								Planned End Date
							</td>
							<td>
								<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />
							</td>
						</tr>
						
						<tr>
							<td>
								Actual Start Date
							</td>
							<td>
								<input type="text" name="edit-actualstart-date" class="edit-actualstart-date textfield pick-date width100px" />
							</td>
							<td>
								Actual End Date
							</td>
							<td>
								<input type="text" name="edit-actualend-date" class="edit-actualend-date textfield pick-date width100px" />
							</td>
						</tr>
						<tr>
							<td>Remarks</td>
							<td colspan="3"><textarea name="remarks" id="edit-task-remarks" class="edit-task-remarks" width="420px"></textarea></td>
						</tr>
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="editTask();">Edit</button>
								</div>
								<div class="buttons">
									<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
								</div>
							</td>
						</tr>
					</table>
				<!-- edit task end -->
				</form>
				
			</div><!-- id: jv-tab-4 end -->
			
			<div id="jv-tab-4-5">
				<form id="milestone-management" onsubmit="return false;">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<h3>Milestones</h3>
					<table id="milestone-clone" style="display:none;">
						<tr>
							<td class="milestone">
								<input type="text" name="milestone[]" class="textfield width250px" />
							</td>
							<td class="milestone-date">
								<input type="text" name="milestone_date[]" class="textfield width80px pick-date" />
							</td>
							<td class="milestone-status">
								<select name="milestone_status[]" class="textfield width80px">
									<option value="0">Scheduled</option>
									<option value="1">In Progress</option>
									<option value="2">Completed</option>
								</select>
							</td>
							<td class="milestone-action" valign="middle">
								&nbsp; <a href="#" onclick="removeMilestoneRow(this); return false;">Remove</a>
							</td>
						</tr>
					</table>
					
					<table id="milestone-data">
						<thead>
							<tr>
								<th align="left">Item</th>
								<th>Date</th>
								<th>Status</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					
					<div class="buttons">
						<button type="submit" class="positive" onclick="addMilestoneField();">Add New</button>
						<button type="submit" class="positive" onclick="saveMilestones();">Save List</button>
						<button type="submit" class="positive" onclick="emailMilestones();">Email Timeline</button>
					</div>
					
				</form>
				
				<script type="text/javascript">
				var milestones_cached_row = false;
				function addMilestoneField()
				{
					if ( ! milestones_cached_row)
					{
						milestones_cached_row = $('#milestone-clone tr:first');
					}
					
					milestones_cached_row.clone().appendTo('#milestone-data tbody');
					$('#milestone-data tr:last .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
				}
				
				function removeMilestoneRow(el)
				{
					var agree=confirm("Are you sure you want to delete this milestone?");
						if (agree) {
							$(el).parent().parent().remove();
						}
						var data = $('#milestone-management').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
					
					$('#jv-tab-4-5').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
					
					$.post(
						'ajax/request/save_job_overview/' + curr_job_id,
						data,
						function(detail)
						{
							if ($.trim(detail) != '')
							{
								$('#milestone-data tbody').html(detail);
								$('#milestone-data tbody tr .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
							}
							$('#jv-tab-4-5').unblock();
						}
					);
					return false;
				}
				
				function saveMilestones()
				{
					var error = false;
					
					$('#milestone-data tbody tr').each(function(){
						if ($('.milestone input', $(this)).val() == '' || $('.milestone-date input', $(this)).val() == '')
						{
							error = 'All milestones and dates are required!';
						}
					});
					
					if (error)
					{
						alert(error);
						return;
					}
					
					var data = $('#milestone-management').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
					
					$('#jv-tab-4-5').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
					
					$.post(
						'ajax/request/save_job_overview/' + curr_job_id,
						data,
						function(detail)
						{
							if ($.trim(detail) != '')
							{
								$('#milestone-data tbody').html(detail);
								$('#milestone-data tbody tr .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
							}
							$('#jv-tab-4-5').unblock();
						}
					);
				}
				
				function emailMilestones()
				{
					var qc_job_title = '<?php echo str_replace("'", "\'", $quote_data['job_title']) ?>';
					var obj = $('#milestone-data tbody tr');
					
					if (obj.length == 0)
					{
						alert('No records are there to email!');
						return false;
					}
					var email_data = '';
					obj.each(function(){
						var ddate = $('.milestone-date input', $(this)).val();
						var mstone = $('.milestone input', $(this)).val();
						var mstat = $('.milestone-status select option:selected', $(this)).val();
						
						email_data += ddate + ' : ' + mstone;
						if (mstat == 2)
						{
							email_data += ' [completed]';
						}
						email_data += '\n';
					});
					
					$('#job_log').focus().val('\nTimeline for the project: ' + qc_job_title + '\n' +  email_data);
					$('html, body').animate({ scrollTop: $('#job_log').offset().top }, 500);
					
					return false;
				}
				
				function populateJobOverview()
				{
					$('#jv-tab-4-5').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
					$.get(
						'ajax/request/get_job_overview/' + curr_job_id,
						{},
						function(detail)
						{
							if ($.trim(detail) != '')
							{
								$('#milestone-data tbody').html(detail);
								$('#milestone-data tbody tr .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
							}
							$('#jv-tab-4-5').unblock();
						}
					);
				}
				</script>
			</div>
			
			<div id="jv-tab-5">
			<?php //echo"<pre>"; print_r($quote_data); exit; ?>
				<form id="customer-detail-read-only" onsubmit="return false;">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<table class="tabbed-cust-layout" cellspacing="0" cellpadding="0">
					<tr>
						<td width="120"><label><b>First Name:</b></label></td>
						<td><b><?php echo $quote_data['first_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Last Name:</b></label></td>
						<td><b><?php echo $quote_data['last_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Position:</b></label></td>
						<td><b><?php echo $quote_data['position_title'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Company:</b></label></td>
						<td><b><?php echo $quote_data['company'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Address Line 1:</b></label></td>
						<td><b><?php echo $quote_data['add1_line1'] ?></b></td>
					</tr>
						
					<tr>
						<td><label><b>Address Line 2:</b></label></td>
						<td><b><?php echo $quote_data['add1_line2'] ?></b></td>
					</tr>
						
					<tr>
						<td><label><b>Suburb:</b></label></td>
						<td><b><?php echo $quote_data['add1_suburb'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Region:</b></label></td>
						<td><b><?php echo $quote_data['region_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Country:</b></label></td>
						<td><b><?php echo $quote_data['country_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>State:</b></label></td>
						<td><b><?php echo $quote_data['state_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Location:</b></label></td>
						<td><b><?php echo $quote_data['location_name'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Post code:</b></label></td>
						<td><b><?php echo $quote_data['add1_postcode'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Direct Phone:</b></label></td>
						<td><b><?php echo $quote_data['phone_1'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Work Phone:</b></label></td>
						<td><b><?php echo $quote_data['phone_2'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Mobile Phone:</b></label></td>
						<td><b><?php echo $quote_data['phone_3'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Fax Line:</b></label></td>
						<td><b><?php echo $quote_data['phone_4'] ?></b></td>
					</tr>

					<tr>
						<td><label><b>Email:</b></label></td>
						<td><b><?php echo $quote_data['email_1'] ?></b></td>
					</tr>

					<tr>
						<td><label><b>Secondary Email:</b></label></td>
						<td><b><?php echo $quote_data['email_2'] ?></b></td>
					</tr>

					<tr>
						<td><label><b>Email 3:</b></label></td>
						<td><b><?php echo $quote_data['email_3'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Email 4:</b></label></td>
						<td><b><?php echo $quote_data['email_4'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Web:</b></label></td>
						<td><b><?php echo $quote_data['www_1'] ?></b></td>
					</tr>
					
					<tr>
						<td><label><b>Secondary Web:</b></label></td>
						<td><b><?php echo $quote_data['www_2'] ?></b></td>
					</tr>
				</table>
				</form>
			</div><!-- id: jv-tab-5 end -->
			
			<div id="jv-tab-7">
				<form id="set-urls" style="overflow:hidden; margin-bottom:15px; zoom:1;">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<p>Add URL to this job (full URL including http://)</p>
					<p><input type="text" class="textfield" id="job-add-url" style="margin:0; width:250px;" /></p>
					<p>Details (optional)</p>
					<p><textarea id="job-url-content" class="textfield" style="margin:0; width:250px;"></textarea></p>
					<div class="buttons">
						<button type="submit" class="positive" onclick="addURLtoJob(); return false;">Add</button>
					</div>
				</form>
				<ul id="job-url-list">
				<?php echo $job_urls_html ?>
				</ul>
			</div><!-- id: jv-tab-7 end -->
			
        </div>
	</div>
</div>

<?php require ('tpl/footer.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('.checkUser').hide();
    $('.checkUser1').hide();
	$('#checkVal').hide();
    $('#checkVal1').hide();
});

<!--Add Payment Terms Edit function Starts here -->
function paymentProfileEdit(eid) {
	//alert(eid);
	$(".payment-profile-view").show();
	var jid = <?php echo  isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>;
	setTimeout('timerfadeout()', 2000);
	var url = "welcome/agreedPaymentEdit/"+eid+"/"+jid;
	$('#payment-profile-view').load(url);
}

function paymentProfileView() {
	setTimeout('timerfadeout()', 2000);
	var url = "welcome/agreedPaymentView";
	$('#payment-profile-view').load(url);
}
<!--Add Payment Terms Edit function Ends here -->

<!--Add Payment Terms Delete function Starts here -->
function paymentProfileDelete(eid) {
	var agree=confirm("Are you sure you want to delete this file?");
	if (agree) {
		var jid = "<?php echo $quote_data['jobid'] ?>";
		setTimeout('timerfadeout()', 2000);
		var url = "welcome/agreedPaymentDelete/"+eid+"/"+jid;
		$('.payment-terms-mini-view1').load(url);
	}
	else {
		return false;
	}
}

function timerfadeout() {
	$('#paymentfadeout').fadeOut();
	$('#rec_paymentfadeout').fadeOut();
	$('#resmsg, #pjt_val_errormsg, #checkVal1, #checkVal').fadeOut();
	$('#pjt_id_errormsg, .checkUser, #id-existsval').fadeOut();
}
<!--Add Payment Terms Delete function Ends here -->

<!--Add Received Payment Terms Edit function Starts here -->
function paymentReceivedEdit(pdid) {
	//alert(pdid); return false;
	$(".payment-recieved-view").show(); 
	var jid = <?php echo $quote_data['jobid'] ?>;
	var pdurl = "welcome/paymentEdit/"+pdid+"/"+jid;
	$('.payment-recieved-view').load(pdurl);
}
<!--Add Received Payment Terms Edit function Ends here.-->

<!--Add Received Payment Terms Delete function Starts here.-->
function paymentReceivedDelete(eid,map) {
	var agree=confirm("Are you sure you want to delete this Payment?");
	if (agree) {
		var jid = "<?php echo $quote_data['jobid'] ?>";
		setTimeout('timerfadeout()', 2000);
		var url = "welcome/receivedPaymentDelete/"+eid+"/"+jid+"/"+map;
		$('.payment-received-mini-view1').load(url);
	}
	else {
		return false;
	}
}


function paymentReceivedView() {
	var url = "welcome/PaymentView";
	$('#payment-recieved-view').load(url);
}

//mychanges
function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}
<!--Add Received Payment Terms Delete function Ends here.-->
</script>
<?php require ('tpl/header.php');?>

<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>

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
$(document).ready(
	function(){
        <?php if (isset($quote_data) && (isset($edit_quotation) || isset($view_quotation))) { ?>
        populatePackage(<?php echo  $quote_data['jobid'] ?>, true);
        <?php } ?>
	}
);
var userid = <?php echo  isset($userdata['userid']) ? $userdata['userid'] : 0 ?>;
var current_job_status = <?php echo (isset($quote_data['job_status'])) ? $quote_data['job_status'] : 0 ?>;
function sendMail() {
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
		
	var form_data = {'userid':userid, 'jobid':quote_id, 'log_content':the_log, 'emailto':email_set}
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
		'welcome/send_mail',
		form_data,
		function(_data){
			try {
				var data;
				eval('data = ' + _data);
				if (typeof(data) == 'object'){
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.log-container').prepend(data.html).children('.log:first').slideDown(400);
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
var client_message = 'Hi <?php echo  (isset($quote_data) && trim($quote_data['first_name']) != '') ? $quote_data['first_name'] : 'There' ?>,\n\nWe are now pleased to forward to you our formal project proposal for:\n\n<?php echo $cfg['job_status'][$quote_data['job_status']] ?> #<?php echo $quote_data['invoice_no'], ', ', str_replace("'", "\'", $quote_data['job_title']) ?>\n\nIf you wish to proceed with our quotation, please reply to this email stating the following: "Quotation Approved - Please Proceed" and one of our friendly staff will be in touch with you to finalise your order.\n\nThank you.';
function prepareForClient() {
	$('#job_log').val(client_message);
	$('#attach_pdf').attr('checked', true);
	$('#email_to_customer').attr('checked', true);
	$('#multiple-client-emails:hidden').slideDown(400)
				.children('input[type=checkbox]:first').attr('checked', true);
	$('#requesting_client_approval').val('1');
	return false;
}
function setPaymentTerms() {
	$('#sp_form_jobid').val(curr_job_id);
	var invoice_total = parseFloat($('#sp_form_invoice_total').val());
	var perc1 = parseInt($('#sp_perc_1').val());
	var perc2 = parseInt($('#sp_perc_2').val());
	var perc3 = parseInt($('#sp_perc_3').val());
	if (isNaN(perc1)) perc1 = 0;
	if (isNaN(perc2)) perc2 = 0;
	if (isNaN(perc3)) perc3 = 0;
	var perc_total = perc1 + perc2 + perc3;
	var valid_date = true;
	var date_entered = true;
	var errors = [];
	if (
			($.trim($('#sp_date_1').val()) != '' && ! $('#sp_date_1').val().match(/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/))
			|| ($.trim($('#sp_date_2').val()) != '' && ! $('#sp_date_2').val().match(/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/))
			|| ($.trim($('#sp_date_3').val()) != '' && ! $('#sp_date_3').val().match(/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/))
	   )
	{
		valid_date = false;
	}
	if (
			($.trim($('#sp_date_1').val()) == '' && perc1 > 0)
			|| ($.trim($('#sp_date_2').val()) == '' && perc2 > 0)
			|| ($.trim($('#sp_date_3').val()) == '' && perc3 > 0)
	   )
	{
		date_entered = false;
	}
	if ($('#sp_form_jobid').val() == 0) {
		errors.push('Invoice not properly loaded!');
	}
	if (isNaN(invoice_total) || invoice_total < 1) {
		errors.push('Invoice total not properly captured!');
	}
	if (perc_total != 100) {
		errors.push('Make sure the percentage values add up to 100%');
	}
	if (valid_date == false) {
		errors.push('You have an invalid date');
	}
	if (date_entered == false) {
		errors.push('You need to enter dates relating to the percentage values');
	}
	if (errors.length > 0) {
		alert(errors.join('\n'));
		return false;
	} else {
		$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
		var form_data = $('#set-payment-terms').serialize();
		$.post(
			'welcome/set_payment_terms',
			form_data,
			function(data) {
				if (typeof(data) == 'object'){
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.payment-profile-view:visible').slideUp(400);
						document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
					}
				} else {
					alert('Unexpected response from server!')
				}
				$.unblockUI();
			},
			'json'
		);
	}
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
		
		var form_data = $('#set-deposits').serialize();
		
		$.post(
			'welcome/add_deposit_payments',
			form_data,
			function(data) {
				if (typeof(data) == 'object'){
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.add-deposit-view:visible').slideUp(400);
						document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
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

var deposit_form_block = true;
<?php
if (isset($userdata) && $userdata['level'] == 5)
{
	?>
	deposit_form_block = true;
	<?php
}
?>
function checkForAccounts() {
	if (!deposit_form_block) {
		$('#set-deposits').block({
            message:'<h4>Accounting Access Only</h4>',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333', width:'200px', top:'10px'}
        });
		deposit_form_block = true;
	}
}

function fullScreenLogs() {
	var fsl_height = parseInt($(window).height()) - 80;
	fsl_height = fsl_height + 'px';
	$.blockUI({
		message:$('.log-container'),
		css: {background:'#444', border: '2px solid #999', padding:'4px', height:fsl_height, color:'#333', width:'600px', overflow:'auto', top:'40px', left:'50%', marginLeft:'-300px'},
		overlayCSS:  {backgroundColor:'#000', opacity:0.9}
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
						//alert(data.msg);
						var _file_link = '<a href="vps_data/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a> <span>'+data.file_size+'</span>';
						var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/vps_data/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>';
						<?php
						if ($userdata['level'] > 1) echo '_del_link = "";';
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
	if (window.confirm('Are you sure?')) {
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
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function (_data) {
			try {
				eval ('var data = ' + _data);
				if (data.error == false) {
					$(el).parent().hide('fast', function() { $(this).remove(); });
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
	var pl_user = $('#project_lead').val()
	if (pl_user == 0) {
		alert('User must be selected!');
		return false;
	} else {
		$.get(
			'ajax/production/set_project_lead/' + curr_job_id + '/' + pl_user,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							job_project_manager = pl_user;
							$('h5.project-lead-label span').text('[ ' + $('#project_lead option:selected').text() + ' ]');
							$('.project-lead-change:visible').hide(200);
							
							// set profile image
							getPMProfileImage();
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
	}
}


function setProjectStatusDate(date_type) {	
	
	<?php
	if (isset($userdata) && $userdata['level'] > 1)
	{
		?>
		alert('Only Administrators can change project dates!');
		return false;
		<?php
	}
	?>
	var set_date_type, date_val, d_class;
	
	if (date_type == 'start')
	{
		set_date_type = 'start';
		date_val = $('#project-start-date').val();
		d_class = 'startdate';
	}
	else
	{
		set_date_type = 'end';
		date_val = $('#project-due-date').val();
		d_class = 'deadline';
	}
	
	var pr_date = $('#project_lead').val()
	if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
		alert('Please insert a valid date!');
		return false;
	} else {
		$.get(
			'ajax/production/set_project_status_date/' + curr_job_id + '/' + set_date_type + '/' + date_val,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('h6.project-' + d_class + '-label span').text(date_val);
							$('.project-' + d_class + '-change:visible').hide(200);
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

function downloadBillingPDF()
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
		var pdf_url = '<?php echo $this->config->item('base_url') ?>welcome/view_plain_billing/<?php echo $quote_data['jobid'] ?>/TRUE/FALSE/TRUE/output-<?php 
		
		echo $quote_data['invoice_no'] ?>/template/';
		
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
		var pdf_url = '<?php echo $this->config->item('base_url') ?>welcome/view_plain_quote/<?php echo $quote_data['jobid'] ?>/TRUE/TRUE/FALSE/output-<?php 
		
		echo $quote_data['invoice_no'] ?>/template/';
		
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
function getPMProfileImage()
{
	var pm_profile = $('#project_lead option:selected').val();
	$.getJSON(
				'ajax/production/get_pm_profile/' + pm_profile,
				{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
				function(data)
				{
					if (typeof(data) == 'object' && data.path != '')
					{
						$('#pm-profile-image').html('<img src="' + data.path + '" />');
					}
					else
					{
						$('#pm-profile-image').html('');
					}
				}
			);
}

function getProjectCSR()
{
	
	$.getJSON(
				'ajax/production/get_csr_status/' + curr_job_id,
				{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
				function(data)
				{
					var csr_status = 0;
					
					if (typeof(data) == 'object')
					{
						csr_status = data.in_csr;
					}
					
					manageCSRStatus(csr_status);
				}
			);
}

function manageCSRStatus(status)
{
	if (status == 1)
	{
		$('#project-in-csr-icon').html('<img src="assets/img/dollar-large.png" alt="In CSR" />');
		$('#project-csr-include-tick').attr('checked', true);
		$('h5.project-csr-label span').text('[ Included in CSR ]');
	}
	else
	{
		$('#project-in-csr-icon').html('');
		$('#project-csr-include-tick').attr('checked', false);
		$('h5.project-csr-label span').text('[ NOT Included in CSR ]');
	}
	$('.project-csr-change:visible').hide(200);
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

function setProjectCSR()
{
	var in_csr = ($('#project-csr-include-tick').is(':checked')) ? 1 : 0;
	$.post(
			'ajax/production/set_csr_status/',
			{'in_csr': in_csr, 'jobid': curr_job_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data)
			{
				var csr_status = 0;
					
				if (typeof(data) == 'object')
				{
					csr_status = data.in_csr;
				}
				
				manageCSRStatus(csr_status);
			},
			'json'
	);
}

<?php
}
?>

$(function(){
	
	$('#set-payment-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M' });
	$('#set-deposits .pick-date, .download-invoice-option .pick-date, .download-invoice-option-log .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -30, maxDate: '+1M' });
	$('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});
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
								}
							});
	
	$('#job-url-list li a:not(.file-delete)').livequery(function(){
		$(this).click(function(){
			window.open(this.href);
			return false;
		});
	});
	
	// get CSR status
	getProjectCSR();
	
	// set profile picture
	getPMProfileImage();
	
	
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
			sendMail();
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
		{jobid: curr_job_id, job_status: status,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function(_data) {
			try {
				eval('data = ' + _data);
				if (typeof(data) == 'object') {
					if (data.error == false) {
						pos_just_completed = true;
						status = status * 10;
						updateVisualStatus(status);
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
	var contractors = [];
	$('.list-contractors input:checked').each(function(){
		contractors.push($(this).val());
	});
	
	var data = {'contractors': contractors.join(','), 'jobid': curr_job_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
	
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
	
	return false;
}

/*
$(window).load(function(){
	setInterval(get_silent_logs, 60 * 2000);
});
*/

</script>

<style type="text/css">
#jv-tab-8 .task-list-item td {
	padding:2px 10px;
}
#jv-tab-8 .task-list-item tr.complete td {
	background:green;
	color:#fff;
}
.project-csr-change {
	display:none;
}
#project-in-csr-icon {
	padding:8px 5px 5px 10px;
}
#pm-profile-image {
	padding:1px 5px 5px;
}
#pm-profile-image img {
	border:1px solid #999;
}
.project-csr-label span {
	font-size:11px;
	font-weight:normal;
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
	if ($this->session->userdata('logged_in') == true)
	{
		if ($this->uri->segment(1) == 'invoice')
		{
			//include ('tpl/invoice_submenu.php');
			$controller_uri = 'invoice';
		}
		else
		{
			include ('tpl/quotation_submenu.php');
			$controller_uri = 'welcome';
		}
	}
	
	/**
	 * this is useful for all the date instances
	 */
	$date_used = $quote_data['date_created'];
	if (in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)) && $quote_data['date_invoiced'] != '')
	{
		$date_used = $quote_data['date_invoiced'];
	}
	?>
    <div class="inner q-view">
		<div class="right-communication">
			
			
			<form action="request" method="post" style="margin-bottom:2px;">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="0" class="search-table">
					<tr>
						<td>
							Quotation / Invoice Search
						</td>
						<td>
							<input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Invoice No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
						</td>
						<td>
							<div class="buttons">
								<button type="submit" class="positive">
									
									Search
								</button>
							</div>
						</td>
					</tr>
				</table>
			</form>
			
			<?php
			if (isset($jobs_under_type))
			{
				?>
			<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="jump-to-job">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<select name="jump_to_job" class="textfield width300px">
					<?php
					foreach($jobs_under_type as $job_ut)
					{
						?>
					<option value="<?php echo $job_ut['jobid'] ?>"<?php if ($job_ut['jobid'] == $quote_data['jobid']) echo ' selected="selected"' ?>><?php echo $job_ut['job_title'], ' - ', $job_ut['company'] ?></option>
						<?php
					}
					?>
				</select>
				<p>Jump to a job</p>
			</form>
				<?php
			}
			?>
			
			
			<form id="comm-log-form">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<textarea name="job_log" id="job_log" class="textfield width99pct height100px gray-text">Click to view options</textarea>
				<div style="position:relative;">
					<textarea name="signature" class="textfield width99pct" rows="4" readonly="readonly" style="color:#666;"><?php echo $userdata['signature'] ?></textarea>
					<span style="position:absolute; top:5px; right:18px;"><a href="#comm-log-form" onclick="whatIsSignature(); return false;">What is this?</a></span>
				</div>
				<?php
				if (in_array($quote_data['job_status'], array(21, 22)))
				{
					?>
				<div class="idle-declined">
					This is a quotation that is either idle or declined.
				</div>
					<?php
				}
				?>
				<div style="overflow:hidden;">
					
					<p class="right" style="padding-left:15px;">Add your time in minutes <input type="text" name="log_minutes" id="log_minutes" class="textfield" style="width:40px;" /></p>
					<p class="right" style="padding-top:5px;">Mark as a <a href="#was" onclick="whatAreStickies(); return false;">stickie</a> <input type="checkbox" name="log_stickie" id="log_stickie" /></p>
					<div class="button-container">
						<div class="buttons">
							<button type="submit" class="positive" onclick="sendMail();  return false;" id="add-log-submit-button">Add Post</button>
						</div>
					</div>
				
				</div>
				<?php
				// you only send this to a client if you are an admin + only if the status is quotation
				if (isset($userdata) && $userdata['level'] < 2 && isset($quote_data) && $quote_data['job_status'] < 3)
				{
					?>
				<p><a href="#" onclick="prepareForClient(); return false;">Send quote to client</a></p>
					<?php
				}
				?>
			
			<?php
			if (isset($userdata) && in_array($userdata['level'], array(0,1,2,4,5)))
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
					
					<?php if (isset($userdata) && in_array($userdata['level'], array(0,1,4,5))) { ?>
					<input type="checkbox" name="attach_pdf" id="attach_pdf" /> <label for="attach_pdf" class="normal">Attach document as PDF</label> &nbsp;
					
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
					<p><label>Email to:</label>
					<?php
						if (isset($userdata) && $userdata['level'] < 3 && 1 == 2)
						{
							?>
							&nbsp; <input type="checkbox" name="enable_post_profile" value="1" id="enable_post_profile" /> <label for="enable_post_profile">Enable "Post to Staff Profile" (does not work yet!)</label>
							<?php
						}
						?>
					</p>
					<?php
					$post_profile_options = '';
					
					if (isset($userdata) && $userdata['level'] < 3)
					{
						$post_profile_record = array(
												"-5" => 'Critical Error',
												"-4" => 'Substantial Error',
												"-3" => 'General Error',
												"-2" => 'Warning',
												"-1" => 'Notice',
												"0" => 'Do not post',
												"1" => 'Good',
												"2" => 'Very Good',
												"3" => 'Great Work',
												"4" => 'Excellent Work',
												"5" => 'Outstanding Work'
											);
					
						foreach ($post_profile_record as $ppk => $ppv)
						{
							$ppk_selected = ($ppk == 0) ? ' selected="selected"' : '';
							$post_profile_options .= "<option value=\"{$ppk}\" class=\"ppcol{$ppk}\"{$ppk_selected}>{$ppv} ({$ppk})</option>";
						}
					}
					
					if (count($user_accounts)) foreach ($user_accounts as $ua)
					{
						if (
							( ($ua['level'] == 4 && $ua['sales_code'] == $quote_data['belong_to']) || $ua['level'] != 4 )
							&&
							( ($ua['level'] == 6 && in_array($ua['userid'], $assigned_contractors)) || $ua['level'] != 6)
							&&
							$ua['inactive'] != 1
						   )
						{
							$is_pm = ($ua['is_pm'] == 1) ? ' production-manager-user' : '';
							echo '<span class="user">' .
									'<input type="checkbox" name="email-log-' . $ua['userid'] . '" id="email-log-' . $ua['userid'] . '" class="' . $is_pm . '" /> <label for="email-log-' . $ua['userid'] . '">' . $ua['first_name'] . ' ' . $ua['last_name'] . '</label>' .
									'<select name="post_profile_' . $ua['userid'] . '" class="post-profile-select">' . $post_profile_options . '</select></span>';
						}
					}
					?>
				</div>
			</form>
			<p>&nbsp;</p>
			<span style="float:right;"> 
				<a href="#" onclick="fullScreenLogs(); return false;">view Full Screen</a>
				|
				<a href="#" onclick="$('.log-container > :not(.stickie)').toggle(); return false;">view/hide stickies</a>
				<?php
				if (isset($userdata) && $userdata['level'] < 2)
				{
				?>
				|
				<a href="#" onclick="qcOKlog(); return false;">All logs OK?</a>
				<?php
				}
				?>
			</span>
			<h4>Job History</h4>
			<div class="log-container">
				<?php echo  $log_html ?>
			</div>
		</div>
		
        <div class="q-main-right">
			<h2 class="job-title">
				<?php
				if (is_file(dirname(FCPATH) . '/assets/img/sales/' . $quote_data['belong_to'] . '.jpg'))
				{
					?>
					<img src="assets/img/sales/<?php echo $quote_data['belong_to'] ?>.jpg" title="<?php echo $quote_data['belong_to'] ?>" />
					<?php
				}
				
				echo htmlentities($quote_data['job_title'], ENT_QUOTES);
				?>
			</h2>
			
			<div class="action-buttons" style="overflow:hidden;">
				<?php
				
				include VIEWPATH . 'tpl/user_accounts_options.php';
				
				// you can only edit this if this is not an invoice
				if (isset($userdata) && in_array($userdata['level'], array(0,1,2,4)) && isset($quote_data) && in_array($quote_data['job_status'], array(4, 5, 6, 7, 8, 15, 25)))
				{
					?>
				
				<form name="project_assign" id="project-assign">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0">
						<tr>
							<td valign="top">
								<h5 class="project-lead-label">Project Manager <br /><span class="small">[ <?php if (is_numeric($quote_data['assigned_to'])) echo $ua_id_name[$quote_data['assigned_to']]; else echo 'Not Set'; ?> ]</span></h5>
								<?php
								if ($userdata['level'] < 2)
								{
									?>
								<p><a href="#" onclick="$('.project-lead-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-lead-change">
									<select name="project_lead" id="project_lead" class="textfield">
										<option value="0">Please Select</option>
										<?php echo $ua_options ?>
									</select>
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectLead(); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-lead-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
									<?php
								}
								?>
							</td>
							<td valign="top">
								<!-- person image -->
								<div id="pm-profile-image"></div>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<h5 class="project-csr-label">Project Priority <br />
								<span class="small">[ Included in CSR ]</span></h5>
								<?php
								if ($userdata['level'] < 2)
								{
									?>
								<p><a href="#" onclick="$('.project-csr-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-csr-change">
									Include in CSR &nbsp; <input type="checkbox" name="project_csr_include" id="project-csr-include-tick" />
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectCSR(); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-csr-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
									<?php
								}
								?>
							</td>
							<td valign="top">
								<!-- dollar!! -->
								<div id="project-in-csr-icon"></div>
							</td>
						</tr>
					</table>
				</form>
				
				<h3 class="status-title">Adjust project status <span class="small">[ current status - <em><strong>0</strong>% Complete</em> ]</span></h3>
				<p class="status-bar">
					<span class="bar"></span>
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
				</p>
				
				<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<table>
						<tr>
							<td valign="top" width="175">
								<h6 class="project-startdate-label">Start Date &raquo; <span><?php if ($quote_data['date_start'] != '') echo date('d-m-Y', strtotime($quote_data['date_start'])); else echo 'Not Set'; ?></span></h6>
								<p><a href="#" onclick="$('.project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-startdate-change">
									<input type="text" value="" class="textfield pick-date" id="project-start-date" />
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('start'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-startdate-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
							</td>
							<td valign="top" width="175">
								<h6 class="project-deadline-label">Due Date &raquo; <span><?php if ($quote_data['date_due'] != '') echo date('d-m-Y', strtotime($quote_data['date_due'])); else echo 'Not Set'; ?></span></h6>
								<p><a href="#" onclick="$('.project-deadline-change:hidden').show(200); return false;">Change?</a></p>
								<div class="project-deadline-change">
									<input type="text" value="" class="textfield pick-date" id="project-due-date" />
									<div class="buttons">
										<button type="submit" class="positive" onclick="setProjectStatusDate('due'); return false;">Set</button>
									</div>
									<div class="buttons">
										<button type="submit" onclick="$('.project-deadline-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
							</td>
						</tr>
					</table>
					
				</form>
				
				<?php
				}
				// you can only edit this if this is not an invoice
				if (isset($userdata) && in_array($userdata['level'], array(0,1,2,4)) && isset($quote_data) && in_array($quote_data['job_status'], array(0, 1, 2, 3, 15, 21, 22)))
				{
					?>
					<div class="buttons" style="overflow:hidden; padding-bottom:10px;">
						<button type="submit" class="positive" onclick="document.location.href = '<?php echo $this->config->item('base_url') ?>welcome/edit_quote/<?php echo $quote_data['jobid'] ?>'">Edit this document</button>
					</div>
					<?php
				}
				
				
				if ( in_array($userdata['level'], array(0,1,2,4)) && in_array($quote_data['job_status'], array(4, 5, 6, 7, 8, 15, 25)) )
				{
				?>
				
				<form name="contractor-assign">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<h5 class="project-lead-label">Assign contractors</h5> &nbsp;&nbsp;
					<a href="#" onclick="$(this).siblings('div:hidden').show(); return false;">Show</a>
					
					<div style="display:none;">
						<div class="list-contractors">
							<?php echo $contractor_list ?>
						</div>
						
						<div class="buttons">
							<button type="submit" class="positive" onclick="setContractorJob(); return false;">Set Contractors</button>
						</div>
					</div>
				</form>
				<?php
				}
				?>
			</div>
					
			<?php
			/**
			 * This will include the select box that changes the status of a job
			 */
			include 'tpl/status_change_menu.php';
			
			?>
			
			<p id="temp">&nbsp;</p>
			<ul id="job-view-tabs">
				<li><a href="#jv-tab-1">Accounts</a></li>
				<li><a href="#jv-tab-2">Document</a></li>
				<li><a href="#jv-tab-3">Files</a></li>
				<li><a href="#jv-tab-4">Tasks</a></li>
				<li><a href="#jv-tab-4-5">Overview</a></li>
				<li><a href="#jv-tab-5">Customer</a></li>
				<li><a href="#jv-tab-6" style="display:none;">Reminders</a></li>
				<li><a href="#jv-tab-7">URLs</a></li>
				<li><a href="#jv-tab-8">QC</a></li>
			</ul>
			<div id="jv-tab-1">
				<div class="q-view-main-top">
					<?php
					if ($quote_data['payment_terms'] == 1 && isset($payment_data) && isset($userdata) && $userdata['level'] < 6)
					{
						
						?>
						<div class="payment-terms-mini-view">
							<h3>Agreed Payment Terms</h3>
							<?php
							$pdi = 1;
							$pt_select_box = '<option value="0"> &nbsp; </option>';
							foreach ($payment_data as $pd)
							{
								$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
								$payment_amount = number_format($pd['amount'], 2, '.', ',');
								$payment_received = '';
								if ($pd['received'] == 1)
								{
									$payment_received = '<img src="assets/img/vcs-payment-received.gif" alt="received" />';
								}
								echo "<p><strong>Payment #{$pdi}</strong> &raquo; {$pd['percentage']}% by {$expected_date} = \${$payment_amount} {$payment_received}</p>";
								$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . "\${$payment_amount} - {$pd['percentage']}% by {$expected_date}" . '</option>';
								$pdi ++;
							}
							?>
						</div>
						<?php
					}
					?>
					
					<p>
					<?php
					if ($quote_data['payment_terms'] == 0 && $quote_data['job_status'] > 3 && $quote_data['job_status'] < 6 && isset($userdata) && $userdata['level'] < 3)
					{
						?>
						<a href="#" onclick="$('.payment-profile-view').slideToggle(); return false;">Set Payment Terms</a>
						<?php
					}
					?>
					<?php
					/*
					if (isset($quote_data) && $quote_data['job_status'] > 3 && isset($userdata) && ($userdata['level'] < 3 || $userdata['level'] == 5))
					{
						?>
						<!-- &nbsp;&nbsp; | &nbsp;&nbsp; <a href="quotation/invoice_data_zip/<?php echo  $quote_data['jobid'] ?>">Get MYOB data</a> -->
						&nbsp;&nbsp; | &nbsp;&nbsp; <a href="#" onclick="$('.add-deposit-view').slideToggle(400, checkForAccounts); return false;">Deposits and Payment Profile</a>
						<?php
					}
					*/
					?>
					</p>
				<?php
				if ($quote_data['payment_terms'] == 0 && $quote_data['job_status'] < 6)
				{
					?>
					<div class="payment-profile-view">
						<form id="set-payment-terms">
						
							<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
							<p>Payment #1 <input type="text" name="sp_date_1" id="sp_date_1" class="textfield pick-date" /> <input type="text" name="sp_perc_1" class="textfield" id="sp_perc_1" size="3" value="" /> % <span id="sp_subt_view1"></span></p>
							<p>Payment #2 <input type="text" name="sp_date_2" id="sp_date_2" class="textfield pick-date" /> <input type="text" name="sp_perc_2" class="textfield" id="sp_perc_2" size="3" value="" /> % <span id="sp_subt_view2"></span></p>
							<p>Payment #3 <input type="text" name="sp_date_3" id="sp_date_3" class="textfield pick-date" /> <input type="text" name="sp_perc_3" class="textfield" id="sp_perc_3" size="3" value="" /> % <span id="sp_subt_view3"></span></p>
							<div class="buttons">
								<button type="submit" class="positive" onclick="setPaymentTerms(); return false;">Set Payment Terms</button>
							</div>
							<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
							<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
						</form>
					</div>
					<?php
				}
				?>
				<?php
				if (isset($quote_data) && in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)))
				{
					?>
				<div class="add-deposit-view">
					<?php
					if (isset($deposits_data) && $sensitive_information_allowed)
					{
						?>
						<div class="payments-history">
							<h3>Payment profile history</h3>
							<?php
							foreach ($deposits_data as $depd)
							{
								?>
								<p><span class="amount">$<?php echo number_format($depd['amount'], 2, '.', ',') ?></span> on <?php echo date('d-m-Y', strtotime($depd['deposit_date'])), '.'; if ($depd['comments'] != '') echo " ({$depd['comments']})"; ?></p>
								<?php
							}
							?>
						</div>
						<?php
					}
					
					if ($sensitive_information_allowed)
					{
					?>
						<form id="set-deposits">
						
							<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
							<p>Deposit amount<br /><input type="text" name="deposit_amount_add" id="deposit_amount_add" class="textfield width200px" /></p>
							<?php
							if (isset($pt_select_box))
							{
								?>
							<p>Map to a payment term<br /><select name="deposit_map_field" style="width:210px;"><?php echo $pt_select_box ?></select></p>
								<?php
							}
							?>
							<p>Deposit Date<br /><input type="text" name="deposit_date" id="deposit_date" class="textfield  width200px pick-date" /></p>
							<p>Comments<br /><input type="text" name="deposit_comments" id="deposit_comments" class="textfield  width200px" /></p>
							<div class="buttons">
								<button type="submit" class="positive" onclick="addDepositPayment(); return false;">Add Deposit</button>
							</div>
							<input type="hidden" name="deposit_form_jobid" id="deposit_form_jobid" value="0" />
							<input type="hidden" name="belong_to" value="<?php echo $quote_data['belong_to'] ?>" />
						</form>
						<?php
					}
					?>
				</div>
					<?php
				}
				?>
					
				</div><!-- class:q-view-main-top end -->
			</div><!-- id: jv-tab-1 end -->
			<div id="jv-tab-2">
				<?php
				if (in_array($userdata['level'], array(0,1,2,4,5)))
				{
					?>
				<p style="text-align:right;"><a href="#" onclick="$('.download-invoice-option').slideToggle('fast'); return false;"><img src="assets/img/download_pdf.gif?q=1" alt="Download PDF" /></a></p>
				<form class="download-invoice-option" style="display:none;" action="welcome/view_plain_quote/<?php echo  $quote_data['jobid'] ?>/TRUE" method="post" target="_blank" onsubmit="return false;">
				
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
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
								&nbsp;Don't attach content policy<br />
								<input type="checkbox" name="ignore_content_policy" />
							</td>
							<td>
								&nbsp;Add a description - 100 characters<br />
								<input type="text" class="textfield width250px" name="custom_description" value="" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="buttons">
									<button type="submit" class="positive" onclick="downloadBillingPDF(); return false;">Download PDF</button>
								</div>
							</td>
						</tr>
					</table>
				</form>
				<?php
				}
				?>
				<div class="q-container">
					<div class="q-details">
						<div class="q-top-head">
							<div class="q-cust<?php if(isset($quote_data) && $quote_data['division'] == 'SYNG') echo ' syng-gray' ?>">
								<h3 class="q-id"><em><?php echo  (isset($quote_data)) ? $cfg['job_status_label'][$quote_data['job_status']] : 'Draft' ?></em> &nbsp; <span>#<?php echo  (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?></span></h3>
								<p class="q-date"><em>Date</em> <span><?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?></span></p>
								<p class="q-cust-company"><em>Company</em> <span><?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?></span></p>
								<p class="q-cust-name"><em>Contact</em> <span><?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?></span></p>
								<p class="q-cust-email"><em>Email</em> <span><?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?></span></p>
								<p class="q-service-type"><em>Service</em> <span><?php echo  (isset($quote_data)) ? $cfg['job_categories'][$quote_data['job_category']] : '' ?></span></p>
							</div>
							<?php //$cfg['quote_header'] ?><!-- end q-self -->
							<p><img src="assets/img/qlogo.jpg?q=1" alt="" /></p>
						</div>
						<div class="q-quote-items">
							<h4 class="quote-title">Project Name : <?php echo (isset($quote_data)) ? $quote_data['job_title'] : '' ?></h4>
							<ul id="q-sort-items"></ul>
						</div>
					</div>
				</div>
			
				<div class="q-sub-total<?php if (! in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)) || ! $sensitive_information_allowed) echo ' display-none' ?>">
					<table class="width565px" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="160">&nbsp;</td>
							<td width="120" align="right"> <span id="deposit_amount"></span></td>
							<td width="20">&nbsp;</td>
							<td align="right">Total amount : $ <span id="balance_amount"></span></td>
						</tr>
					</table>
				</div>
			</div><!-- id: jv-tab-2 end -->
			
			<div id="jv-tab-3">
				<form name="ajax_file_upload">
				
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<div id="upload-container">
						<img src="assets/img/select_file.jpg" alt="Browse" id="upload-decoy" />
						<input type="file" class="textfield" id="ajax_file_uploader" name="ajax_file_uploader" onchange="return runAjaxFileUpload();" size="1" />
						<!-- input type="button" value="Upload File" onclick="runAjaxFileUpload();" / -->
					</div>
					<ul id="job-file-list">
					<?php echo $job_files_html ?>
					</ul>
				</form>
			</div><!-- id: jv-tab-3 end -->
			
			<div id="jv-tab-4">
				<form id="set-job-task" onsubmit="return false;">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<h3>Tasks</h3>
					<table border="0" cellpadding="0" cellspacing="0" class="task-add">
						
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
								echo $remind_options, $remind_options_all, $contractor_options;
								?>
								</select>
							</td>
							<td>
								Hours
							</td>
							<td>
								<input name="task_hours" type="text" class="textfield width100px" /> Hours and
								<select name="task_mins" class="textfield">
									<option value="0">0</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
								Mins
							</td>
						</tr>
						
						<tr>
							<td>
								Start Date
							</td>
							<td>
								<input type="text" name="task_start_date" class="textfield pick-date width100px" />
							</td>
							<td>
								End Date
							</td>
							<td>
								<input type="text" name="task_end_date" class="textfield pick-date width100px" />
								&nbsp;
								<select name="task_end_hour" class="textfield">
								<?php
								$time_range = array(
												'10:00:00'	=> '10:00AM',
												'11:00:00'	=> '11:00AM',
												'12:00:00'	=> '12:00PM',
												'13:00:00'	=> '1:00PM',
												'14:00:00'	=> '2:00PM',
												'15:00:00'	=> '3:00PM',
												'16:00:00'	=> '4:00PM',
												'17:00:00'	=> '5:00PM',
												'18:00:00'	=> '6:00PM',
												'19:00:00'	=> '7:00PM'
											 );
								foreach ($time_range as $k => $v)
								{
									$selected = ($k == '17:00:00') ? ' selected="selected"' : '';
									echo "
									<option value=\"{$k}\"{$selected}>{$v}</option>";
								}
								?>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td colspan="3">
								Require checklist before completion <input type="checkbox" name="require_qc" />
							</td>
						</tr>
						
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="addNewTask();">Add</button>
								</div>
								<div class="buttons">
									<button type="submit" class="negative" onclick="addTaskToggle('off');">Cancel</button>
								</div>
							</td>
						</tr>
					</table>
					<div class="buttons task-init">
						<button type="submit" class="positive" onclick="addTaskToggle();">Add New</button>
					</div>
					
					<div class="existing-task-list">
						<br /><br />
						<h4>Existing Tasks</h4>
					</div>
				</form>
				
				<form id="edit-job-task" onsubmit="return false;">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<!-- edit task -->
					<table border="0" cellpadding="0" cellspacing="0" >
						
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
							<td>
								Hours
							</td>
							<td>
								<input name="task_hours" type="text" class="edit-task-hours textfield width100px" /> Hours and
								<select name="task_mins" class="edit-task-mins textfield">
									<option value="0">0</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
								Mins
							</td>
						</tr>
						
						<tr>
							<td>
								Start Date
							</td>
							<td>
								<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
							</td>
							<td>
								End Date
							</td>
							<td>
								<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />
								&nbsp;
								<select name="task_end_hour" class="textfield edit-end-hour">
								<?php
								foreach ($time_range as $k => $v)
								{
									$selected = '';
									echo "
									<option value=\"{$k}\"{$selected}>{$v}</option>";
								}
								?>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td colspan="3">
								Require checklist before completion <input type="checkbox" name="require_qc" class="task-require-qc" />
							</td>
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
				
				<form onsubmit="return false;" class="display-none" id="task-require-qc-cover">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table border="0" cellpadding="0" cellspacing="0" class="the-task-require-qc">
						<tr>
							<td class="qc-task-item-1" style="color:#ccc;"><input type="checkbox" name="qc_item_id_1" /> &nbsp; <span>Is it working has intended - as per invoice or logged instruction?</span></td>
						</tr>
						<tr>
							<td class="qc-task-item-1" style="color:#ccc;"><input type="checkbox" name="qc_item_id_2" /> &nbsp; <span>Has it been checked for errors - is it working on other browsers/computers?</span></td>
						</tr>
						<tr>
							<td class="qc-task-item-1" style="color:#ccc;"><input type="checkbox" name="qc_item_id_3" /> &nbsp; <span>Does the item visually match the rest of the design/styling?</span></td>
						</tr>
						<tr>
							<td class="qc-task-item-1" style="color:#ccc;"><input type="checkbox" name="qc_item_id_4" /> &nbsp; <span>Is it up to Visiontech Standard - will it get approved in a design critique by the Assistant Art Director?</span>
							<input type="hidden" name="hidden_taskid" />
							</td>
						</tr>
						<tr>
							<td>
								<div class="buttons">
									<button type="submit" class="positive" onclick="submitFullCompleteStatus();">Proceed</button>
								</div>
								<div class="buttons">
									<button type="submit" class="negative" onclick="$.unblockUI(); $('#jv-tab-4').unblock();">Cancel</button>
								</div>
							</td>
						</tr>
					</table>
				</form>
				
			</div><!-- id: jv-tab-4 end -->
			
			<div id="jv-tab-4-5">
				<form id="milestone-management" onsubmit="return false;">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
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
					$(el).parent().parent().remove();
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
					
					var data = $('#milestone-management').serialize();
					
					$('#jv-tab-4-5').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
					
					$.post(
						'ajax/request/save_job_overview/' + curr_job_id,
						data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
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
				<form id="customer-detail-read-only" onsubmit="return false;">
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<table class="tabbed-cust-layout">
					<tr>
						<td width="120">First name:</td>
						<td><input type="text" name="first_name" value="<?php echo $quote_data['first_name'] ?>" readonly="readonly" class="textfield width200px required" /></td>
					</tr>
					<tr>
						<td>Last Name:</td>
						<td><input type="text" name="last_name" value="<?php echo $quote_data['last_name'] ?>" readonly="readonly" class="textfield width200px required" /></td>
					</tr>
					<tr>
						<td>Position:</td>
						<td><input type="text" name="position_title" value="<?php echo $quote_data['position_title'] ?>" readonly="readonly" class="textfield width200px required" /></td>
					</tr>
					<tr>
						<td>Company:</td>
						<td><input type="text" name="company" value="<?php echo $quote_data['company'] ?>" readonly="readonly" class="textfield width200px required" /></td>
					</tr>
					<tr>
						<td>Address Line 1:</td>
						<td><input type="text" name="add1_line1" value="<?php echo $quote_data['add1_line1'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>Address Line 2:</td>
						<td><input type="text" name="add1_line2" value="<?php echo $quote_data['add1_line2'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>Suburb:</td>
						<td><input type="text" name="add1_suburb" value="<?php echo $quote_data['add1_suburb'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>State:</td>
						<td>
							<select name="add1_state" class="textfield width200px" id="userState">
									<option><?php echo $quote_data['add1_state'] ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Post code:</td>
						<td><input type="text" name="add1_postcode" value="<?php echo $quote_data['add1_postcode'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>Country</td>
						<td><input type="text" name="add1_country" value="<?php echo $quote_data['add1_country'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>Direct Phone:</td>
						<td><input type="text" name="phone_1" value="<?php echo $quote_data['phone_1'] ?>" readonly="readonly" class="textfield width200px required" /></td>
					</tr>
					<tr>
						<td>Work Phone:</td>
						<td><input type="text" name="phone_2" value="<?php echo $quote_data['phone_2'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
						<tr>
						<td>Mobile Phone:</td>
						<td><input type="text" name="phone_3" value="<?php echo $quote_data['phone_3'] ?>" readonly="readonly" class="textfield width200px required" />
							</td>
					</tr>
					<tr>
						<td>Fax Line:</td>
						<td><input type="text" name="phone_4" value="<?php echo $quote_data['phone_4'] ?>" readonly="readonly" class="textfield width200px" /></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><input type="text" name="email_1" id="emailval" value="<?php echo $quote_data['email_1'] ?>" readonly="readonly" class="textfield width200px required" />
						</td>
					</tr>
					<tr>
						<td>Secondary Email:</td>
						<td><input type="text" name="email_2" value="<?php echo $quote_data['email_2'] ?>" readonly="readonly" class="textfield width200px required" /> 
						</td>
					</tr>
					<tr>
						<td>Email 3:</td>
						<td><input type="text" name="email_3" value="<?php echo $quote_data['email_3'] ?>" readonly="readonly" class="textfield width200px required" />
						</td>
					</tr>
					<tr>
						<td>Email 4:</td>
						<td><input type="text" name="email_4" value="<?php echo $quote_data['email_4'] ?>" readonly="readonly" class="textfield width200px required" /> 
						</td>
					</tr>
						<tr>
						<td>Web:</td>
						<td><p>&nbsp; <?php echo auto_link($quote_data['www_1']) ?></p>
						</td>
					</tr>
					<tr>
						<td>Secondary Web:</td>
						<td><p>&nbsp; <?php echo auto_link($quote_data['www_2']) ?>
						</td>
					</tr>
					<?php
					if ($sensitive_information_allowed)
					{
						?>
					<tr>
						<td>&nbsp;</td>
						<td><a href="customers/add_customer/update/<?php echo $quote_data['custid'] ?>">More Info</a></td>
					</tr>
						<?php
					}
					?>
				</table>
				</form>
			</div><!-- id: jv-tab-5 end -->
			
			<div id="jv-tab-6">
				<form id="set-reminders">
					Remind
					<select id="remind-user">
						<?php
						//echo $remind_options;
						if ($userdata['level'] < 2)
						{
							//echo $remind_options_all;
						}
						?>
					</select>
					to
					<input type="text" id="remind-about" class="textfield" />
					on
					<input type="text" id="remind-date" class="textfield" />
				</form>
			</div><!-- id: jv-tab-6 end -->
			
			<div id="jv-tab-7">
				<form id="set-urls" style="overflow:hidden; margin-bottom:15px; zoom:1;">
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
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
			<div id="jv-tab-8">
				<form id="qc-checklist-dev" style="overflow:hidden; margin-bottom:15px; zoom:1;" onsubmit="return false">
				
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<div class="dev-checklist-control"<?php if ($is_qc_complete != FALSE) echo ' style="display:none;"' ?>>
						<table border="0" class="task-list-item" id="qc-checklist-dev-items">
							<?php
							foreach ($dev_qc_list as $dqc)
							{
								?>
							<tr>
								<td><?php echo $dqc['question'] ?></td>
								<td><input type="checkbox" name="dev_qc<?php echo $dqc['questionid'] ?>" value="<?php echo $dqc['question'] ?>" /></td>
							</tr>
								<?php
							}
							?>
						</table>
						<div class="buttons">
							<button type="submit" class="positive" onclick="confirmDevQC();">Submit</button>
						</div>
					</div>
					
					<div class="dev-checklist-info"<?php if ($is_qc_complete == FALSE) echo ' style="display:none;"' ?>>
						<p class="qc-complete">This job has been marked as QC completed.</p>
						<div class="buttons">
							<button type="submit" class="negative" onclick="undoDevQC();">Cancel QC confirmation</button>
						</div>
					</div>
					
				</form>
				<script>
				var qc_job_title = '<?php echo str_replace("'", "\'", $quote_data['job_title']) ?>';
				$(function(){
					$('#jv-tab-8 .task-list-item input:checkbox').change(function(){
						if ($(this).is(':checked'))
						{
							$(this).parent().parent().addClass('complete');
						}
						else
						{
							$(this).parent().parent().removeClass('complete');
						}
					});
				});
				
				function confirmDevQC()
				{
					var project_manager = parseInt(job_project_manager);
					
					if (isNaN(project_manager) || parseInt(project_manager) == 0)
					{
						alert('Project manager is not selected for this job!\nPlease organise a project manager before confirming QC for the job.');
						return false;
					}
					
					if (isNaN(parseInt(job_complete_percentage)) || parseInt(job_complete_percentage) < 90)
					{
						alert('You cannot complete QC if the job is not at least 90% complete!');
						return false;
					}
					
					var incomplete = '';
					$('#qc-checklist-dev-items input:not(:checked)').each(function(){
						incomplete += '\n- ' + $(this).val();
					});
					
					if (incomplete == '')
					{
						if (window.confirm('All the items have been checked.\nBy selecting "OK" you are confirming that\nthe Quality Control checklist has been verified.'))
						{
							$('#jv-tab-8').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
							$.post(
									'ajax/request/confirm_qc_check/',
									{jobid: curr_job_id, qc_type: 1, complete: 'yes','<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
									function(data)
									{
										if ( ! data.error)
										{
											$('#qc-checklist-dev .dev-checklist-control').hide();
											$('#qc-checklist-dev .dev-checklist-info').show();
											
											// all good! Place a log
											var msg = "All the quality control items have been checked and verified for this job.\nIt is OK to send a link to the client.";
											// get adrian@ (6)
											$('#job_log').val(msg);
											$('.user .production-manager-user, #email-log-'+project_manager).attr('checked', true);
											sendMail();
										}
										else
										{
											alert(data.error);
										}
										
										// unblock the UI
										$('#jv-tab-8').unblock();
									},
									'json'
							);
						}
					}
					else
					{
						if (window.confirm('There are incomplete items on the list.\n Would you like to notify the staff and place a log?'))
						{
							$('#job_log').focus().val('There are incomplete items on the list.\nJob:' + qc_job_title + '\n' +  incomplete);
							$('html, body').animate({ scrollTop: $('#job_log').offset().top }, 500);
						}
						
						$.post(
								'ajax/request/confirm_qc_check/',
								{jobid: curr_job_id, qc_type: 1, complete: 'no', event_data: incomplete,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
								function(data)
								{
									
								}
						);
					}
					
					return false;
				}
				
				function undoDevQC()
				{
					if (window.confirm('Are you sure?'))
					{
						$('#jv-tab-8').block({
										message:'<img src="assets/img/ajax-loader.gif" />',
										css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
									});
							$.post(
									'ajax/request/undo_qc_check/',
									{jobid: curr_job_id, qc_type: 1,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
									function(data)
									{
										if ( ! data.error)
										{
											$('#qc-checklist-dev .dev-checklist-control').show();
											$('#qc-checklist-dev .dev-checklist-info').hide();
										}
										else
										{
											alert(data.error);
										}
										$('#jv-tab-8').unblock();
									},
									'json'
							);
					}
				}
				</script>
			</div><!-- id: jv-tab-8 end -->
        </div>
	</div>
</div>

<?php require ('tpl/footer.php'); ?>

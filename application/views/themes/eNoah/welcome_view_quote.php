<?php require (theme_url().'/tpl/header.php'); ?>

<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<!--script type="text/javascript" src="assets/js/jquery.blockUI.js"></script-->
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<script type="text/javascript" src="assets/js/easypaginate.js"></script>
<script type="text/javascript">var this_is_home = true;</script>
<!--Code Added for the Pagination in Comments Section -- Starts Here-->
<script type="text/javascript">
$(document).ready(function() {
	$("#lead_log_list")
	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
	
	$("#lead_query_list")
	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager1"),positionFixed: false});
});

function validateRequestForm()
{
var x=document.forms["search_req"]["keyword"].value;
//alert(x); return false;
if (x=='Lead No, Job Title, Name or Company')
  {
  alert("Please provide any values");
  return false;
  }
}
</script>
<div class="comments-log-container" style= "display:none;">
	<?php 
		if ($log_html != "") { 
	?>
			<table width="100%" class="log-container"> 
				<tbody>
				<?php 
					echo $log_html;
				?>				
				</tbody> 
			</table>
	<?php 
		} else { 
			echo "No Comments Found."; 
		}
	?>
</div>

<!--Code Added for the Pagination in Comments Section--Ends Here-->

<script type="text/javascript">
var unid = <?php  echo $userdata['userid'] ; ?>;
var belong_to = <?php echo $quote_data['belong_to'] ; ?>;
var lead_assign = <?php echo $quote_data['lead_assign'] ; ?>;
var role_id = <?php echo $userdata['role_id'] ; ?>;
	
var job_categories = [];
job_categories['not_select'] = '';
<?php foreach ($cfg['job_categories'] as $jck => $jcv) { ?>
job_categories[<?php echo  $jck ?>] = '<?php echo  $jcv ?>';
<?php } ?>

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
		{//alert(log_minutes); return false;
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
	

	
	if ($('#log_stickie').is(':checked')) {
		if (!window.confirm('Are you sure you want to highlight this log as a Stickie?')) {
			return false;
		}
	}
	
	var email_set = 'ssriram@enoahisolution.com';
	// $('.user-addresses input[type="checkbox"]:checked').each(function(){
		// email_set += $(this).attr('id') + ':';
	// });
	
	
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
	
	// form_data.use_custom_date = $('.download-invoice-option-log input[name="use_custom_date"]').val();
	// form_data.balance_due = $('.download-invoice-option-log input[name="balance_due"]').val();
	// form_data.custom_description = $('.download-invoice-option-log input[name="custom_description"]').val();
	
	
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
	
	$.post(
		'welcome/add_log',
		form_data,
		function(_data){
		try {
				var data;
				eval('data = ' + _data);
				if (typeof(data) == 'object'){
					if (data.error) {
						alert(data.errormsg);
					} else {
						//$('.log-container').prepend(data.html).children('.log:first').slideDown(400);
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
// alert("test"); return false;
	var date = '<?php echo date('Y-m-d H:i:s');  ?>';
	var _uid = new Date().getTime();
	$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
	$.ajaxFileUpload
	(
		{
			url:'ajax/request/file_upload/<?php echo $quote_data['jobid'] ?>/'+ date +'/'+ userid + '/',
			secureuri:false,
			fileElementId:'ajax_file_uploader',
			dataType: 'json',
			data:{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			success: function (data, status)
			{
				//alert(data.date);
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
						var _file_link = '<a href="crm_data/files/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a> <span>'+data.file_size+'</span>';
						var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/crm_data/files<?php echo $quote_data['jobid'] ?>/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>';
						if(role_id == 1 || lead_assign == unid || belong_to == unid ) {
						 var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/crm_data/files<?php echo $quote_data['jobid'] ?>/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>'; 
						}
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

function getReplyForm(id) {
	$('#querylead_table').slideToggle();
	document.getElementById('query_form').innerHTML = "<input type='text' value='replay-"+id+"' name='replay-"+id+"' id='replay' />";
}

function QueryAjaxFileUpload() {
	var _uid = new Date().getTime();
	var query = $('#query').val();
	var replay = $('#replay').val();
	 
	//alert(replay);
	//return false;
	var reply = "";
	var fname = "";
	//document.getElementById('querylead_form').style.display = "none";
	if($.trim($('#query').val()) == '') {
		//alert('Please Enter Query');
		return false;						
	}
	if(replay == 'query') {
		reply = "Raised";
		} else {
		reply = "Replied";
		}
	 
	$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#querylist');
	$.ajaxFileUpload
	(
		{
			url:'ajax/request/query_file_upload/<?php echo $quote_data['jobid'] ?>/'+encodeURIComponent(query)+'/'+replay,
			secureuri:false,
			fileElementId:'query_file',
			dataType: 'json',
			data:{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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
						//if(data.msg == 'File successfully uploaded!'){
							//var mail_url = "welcome/send_mail_query/<?php echo $quote_data['jobid'] ?>/"+data.file_name+'/'+encodeURIComponent(query);														
							//$('#mail_results').load(mail_url);						
						//}
						//alert(data.mail_msg);	
						//alert(data.file_name);
						if(typeof(data.file_name) != 'undefined')
						{
						if(data.file_name != 'undefined') {
							fname = '<a href="crm_data/query/<?php echo $quote_data['jobid'] ?>/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a>';
							
						} } else {
							fname = 'File Not Attached';
						}
						
			
var _file_link = '<td><table border="0" cellpadding="5" cellspacing="5" class="task-list-item" id="task-table-15"><tbody><tr><td valign="top" width="80">Query '+reply+'</td><td colspan="3" class="task">'+data.lead_query+'</td></tr>';	
	_file_link += '<tr><td>Date</td><td class="item user-name" rel="59" width="100">'+data.up_date+'</td>';
	_file_link += '<td width="80">'+reply+' By</td><td class="item hours-mins" rel="4:0">'+data.firstname+' '+data.lastname+'</td></tr>';
	_file_link += '<tr><td colspan="1" valign="top">File Name</td><td colspan="3">'+fname+'</td></tr>';
	_file_link += '<tr><td	colspan="4" valign="top"><button class="positive" style="float:right;cursor:pointer;" id="replay" onclick="getReplyForm('+data.replay_id+')">Reply</button></td></tr></table></td>';

						<?php
						if ($userdata['level'] > 1) echo '_del_link = "";';
						?>
						$('#'+_uid).html(_file_link);
					}
				}
			$('#querylead_table').slideToggle();	
			},
			error: function (data, status, e)
			{
				//alert(data);
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
	$('#query').val('');
	$('#query_file').val('');
	return false;
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
			{},
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

/* function to add the auto log */
function qcOKlog() {
	var msg = "eCRM QC Officer Log Check - All Appears OK";
	
	if (!window.confirm('Are you sure you want to stamp the OK log?\n"' + msg + '"')) return false;
	
	$('.user .production-manager-user').attr('checked', true);
	$('#job_log').val(msg);
	$('#add-log-submit-button').click();
}



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
								selected: 0,
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
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#task-desc-countdown').text(remain_len);
	});
	
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
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
</script>

<div id="content">
    <?php
		$date_used = $quote_data['date_created'];
	?>
	
    <div class="inner q-view">
		<div class="right-communication">

			<form action="request" name="search_req" method="post" onsubmit="return validateRequestForm()"  style="margin-bottom:2px;">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="0" class="search-table">
					<tr>
						<td>
							Lead Search
						</td>
						<td>
							<input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Lead No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
							<input type="hidden" name="quoteid" value="<?php echo $quote_data['jobid']; ?>" />
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
		if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) { ?>
			<form id="comm-log-form">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<textarea name="job_log" id="job_log" class="textfield width99pct height100px gray-text">Click to view options</textarea>
				<div style="position:relative;">
					<textarea name="signature" class="textfield width99pct" rows="4" readonly="readonly" style="color:#666;"><?php echo $userdata['signature'] ?></textarea>
					<span style="position:absolute; top:5px; right:18px;"><a href="#comm-log-form" onclick="whatIsSignature(); return false;">What is this?</a></span>
				</div>
				
				<div style="overflow:hidden;">
					
					<!--<p class="right" style="padding-left:15px;">Add your time in minutes <input type="text" name="log_minutes" id="log_minutes" class="textfield" style="width:40px;" /></p>-->
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
					<?php

					/* check the condition if role_id = 1 (admin) and role_id = 2 (management)  and leadowner and lead assigned to  */
					if (count($user_accounts)) foreach ($user_accounts as $ua)
					{
						if ( (($ua['level'] == 1) && ($ua['inactive'] == 0)) || (($ua['role_id'] == 1) && ($ua['inactive'] == 0)) || (($ua['role_id'] == 2) && ($ua['inactive'] == 0)) || (($ua['userid'] == $quote_data['belong_to']) && ($ua['inactive'] == 0)) || (($ua['userid'] == $quote_data['lead_assign']) && ($ua['inactive'] == 0)) ) {
							echo '<span class="user">' .
							'<input type="checkbox" name="email-log-' . $ua['userid'] . '" id="email-log-' . $ua['userid'] . '" /> <label for="email-log-' . $ua['userid'] . '">' . $ua['first_name'] . ' ' . $ua['last_name'] . '</label>
							</span>';
						}
					}
					?>
				</div>
			</form>
			<?php } ?>
			<p>&nbsp;</p>
			<span style="float:right;"> 
				<a href="#" onclick="fullScreenLogs(); return false;">View Full Screen</a>
				|
				<!--<a href="#" onclick="$('.log-container > :not(.stickie)').toggle(); return false;">view/hide stickies</a>-->
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
			<h4>Comments</h4>

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
			<?php } else { echo "No Comments Found."; } ?>
			<!--Code Changes for Pagination in Comments Section -- Ends here -->
		</div>

		<div class="side1">
			<h2 class="job-title">
				<?php
					echo htmlentities($quote_data['job_title'], ENT_QUOTES);
				?>
			</h2>
			
			<div class="action-buttons" style="overflow:hidden;">
				
				<?php if (isset($quote_data)) { ?>
					<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />	
						<table>
							<tr>
								<td valign="top" width="300">
									<h6 class="project-startdate-label">Proposal Expected Date &raquo;<span><?php if ($quote_data['proposal_expected_date'] != '') echo date('d-m-Y', strtotime($quote_data['proposal_expected_date'])); else echo 'Not Set'; ?></span></h6>		
								</td>
							</tr>
						</table>
					</form>
					<?php //} ?>	
					
					<div class="q-init-details">
						<p class="clearfix"><label>Lead Title</label>  <span><?php echo  htmlentities($quote_data['job_title'], ENT_QUOTES) ?></span></p>
						<p class="clearfix"><label>Lead Source </label>  <span><?php echo  $quote_data['lead_source_name'] ?></span></p>
						<p class="clearfix"><label>Service Requirement </label>  <span><?php echo $cfg['job_categories'][$quote_data['job_category']] ?></span></p>
						<p class="clearfix"><label>Expected worth of Deal </label>  <span><?php echo $quote_data['expect_worth_name'] ?><?php echo '&nbsp;' ?><?php echo $quote_data['expect_worth_amount'];?><?php if (is_int($quote_data['expect_worth_amount'])) echo '.00' ?></span></p>
						<p class="clearfix"><label>Actual worth of Deal </label>  <span>
								<?php
									if($quote_data['actual_worth_amount'] == '0.00')
									$amount = '0.00';
									else 
									$amount = $quote_data['actual_worth_amount'];
									echo $quote_data['expect_worth_name'] . ' ' .$amount;
								?>
						</span>
						</p>
						<p class="clearfix"><label>Division </label><span><?php echo $cfg['sales_divisions'][$quote_data['division']] ?></span></p>
						<p class="clearfix"><label>Lead Owner </label> <span><?php echo $quote_data['ownfname'] .' '. $quote_data['ownlname']; ?></span></p>
						<p class="clearfix"><label>Lead Assigned To </label><span><?php echo $quote_data['assfname'] .' '. $quote_data['asslname']; ?></span></p>
						<p class="clearfix"><label>Lead Indicator </label><span><?php echo $quote_data['lead_indicator'] ?></span></p>
						<p class="clearfix"><label>Lead Status </label>
							<span> 
								<?php 
									switch ($quote_data['lead_status'])
									{
										case 1:
											echo $status = 'Active';
										break;
										case 2:
											echo $status = 'On Hold';
										break;
										case 3:
											echo $status = 'Dropped';
										break;
										case 4:
											echo $status = 'Closed';
										break;
									}
								?>
							</span>
						</p>
						<p class="clearfix"><label>Lead Stage </label><span><?php echo $quote_data['lead_stage_name'] ?></span></p>
						<?php if($quote_data['lead_status'] == 2) { ?>
							<p class="clearfix"><label>Reason for OnHold </label><span><?php echo $quote_data['lead_hold_reason'] ?></span></p>
						<?php } ?>
						<input type="hidden" name="jobid_edit" id="jobid_edit" value="<?php echo  $quote_data['jobid'] ?>" />
					</div>
				</form>
				<?php } ?>
				
				<?php
					include theme_url() . '/tpl/user_accounts_options.php';

					if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 ) 
					{
				?>
					<div class="buttons" style="overflow:hidden; padding-bottom:10px; margin:10px 0 0;">
						<button type="submit" class="positive" onclick="document.location.href = '<?php echo $this->config->item('base_url') ?>welcome/edit_quote/<?php echo $quote_data['jobid'] ?>'">Edit this Lead</button>
					</div>
				<?php
					}
				?>
			</div>
			
			<p id="temp">&nbsp;</p>

				<ul id="job-view-tabs">
					<li><a href="#jv-tab-1">Lead History</a></li>
					<li><a href="#jv-tab-2">Estimate</a></li>
					<li><a href="#jv-tab-3">Files</a></li>
					<li><a href="#jv-tab-4">Tasks</a></li>
					<li><a href="#jv-tab-5">Milestones</a></li>
					<li><a href="#jv-tab-6">Customer</a></li>
					<li><a href="#jv-tab-7">Query</a></li>
				</ul>
			<div id="jv-tab-1">
					<table class="data-table">
						<tr ><th>Stage Name</th><th>Modified By</th><th>Modified On</th></tr>
						<?php foreach($lead_stat_history as $ldsh) { ?>
							<tr>
								<td><?php echo $ldsh['lead_stage_name']; ?></td>
								<td><?php echo $ldsh['first_name'] . " " . $ldsh['last_name']; ?></td>
								<td><?php echo date('d-m-Y', strtotime($ldsh['dateofchange'])); ?></td>
							</tr>
						<?php } ?>
					</table>
			</div>
			
			<div id="jv-tab-2">
				<div class="q-container">
					<div class="q-details">
						<div class="q-top-head">
							<div class="q-cust">
								<h3 class="q-id"><em>Lead</em> &nbsp; <span>#<?php echo  (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?></span></h3>
								<p class="q-date"><em>Date</em> <span><?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?></span></p>
								<p class="q-cust-company"><em>Company</em> <span><?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?></span></p>
								<p class="q-cust-name"><em>Contact</em> <span><?php echo  (isset($quote_data)) ? $quote_data['cfn'] . ' ' . $quote_data['cln'] : '' ?></span></p>
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

				<div class="q-sub-total">
					<table class="width565px" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="160">Sale Amount <span id="sale_amount"></span></td>
							<td width="120" align="right">GST <span id="gst_amount"></span></td>
							<td width="20">&nbsp;</td>
							<td align="right">Total inc GST <span id="total_inc_gst"></span></td>
						</tr>
					</table>
				</div>
			</div><!-- id: jv-tab-2 end -->
			
			<div id="jv-tab-3">
				<form name="ajax_file_upload">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<?php if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 ) { ?>
					<div id="upload-container">
						<img src="assets/img/select_file.jpg" alt="Browse" id="upload-decoy" />
						<input type="file" class="textfield" id="ajax_file_uploader" name="ajax_file_uploader" onchange="return runAjaxFileUpload();" size="1" />
					</div>
				<?php } ?>
					<ul id="job-file-list">
					<?php 
						echo $job_files_html;
					?>
					</ul>
				</form>

				<div id="lead_result"></div>
			</div><!-- id: jv-tab-3 end -->
			
			<div id="jv-tab-4">
				<?php 
				if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 ) { 
				?>
					<form id="set-job-task" onsubmit="return false;">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<?php
							$uio = $userdata['userid'];
							foreach($created_by as $value) {
								$b[] = $value[created_by];						
							}
						?>
						<h3>Tasks</h3>
						<table border="0" cellpadding="0" cellspacing="0" class="task-add toggler">
							<tr>
								<td colspan="4"><strong>All fields are required!</strong></td>
							</tr>
							<tr>
								<td valign="top"><br /><br />Task Desc</td>
								<td colspan="3">
									<strong><span id="task-desc-countdown">1000</span></strong> characters left.<br />
									<textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
								</td>
							</tr>
							<tr>
								<td><input type="hidden" class="edit-task-owner textfield"></td>
							</tr>
							<tr>
								<td>Allocate to</td>
								<td>
									<select name="task_user" class="edit-task-allocate textfield width100px">
										<?php
										echo $remind_options, $remind_options_all, $contractor_options;
										?>
									</select>
								</td>
							</tr>				
							<tr>
								<td>Planned Start Date</td>
								<td><input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" /></td>
								<td>Planned End Date</td>
								<td><input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" /></td>
							</tr>						
							<tr>
								<td>Remarks</td>
								<td colspan="3"><textarea name="remarks" id="task-remarks" class="task-remarks" width="420px"></textarea></td>
							</tr>
							<tr>
								<td colspan="4">
									<div class="buttons">
										<button type="submit" class="positive" onclick="addNewTask('','<?php echo $this->security->get_csrf_token_name()?>','<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
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
						
						<br /><br />
					</form>
				<?php 
				} 
				?>
				<div class="existing-task-list">
					<h4>Existing Tasks</h4>
				</div>
					
				<form id="edit-job-task" onsubmit="return false;">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

					<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
						<tr>
							<td colspan="4">
								<?php
								$uio = $userdata['userid'];
								foreach($created_by as $value) {
									$b[] = $value[created_by];						
								}
								?>
								<strong>All fields are required!</strong>
							</td>
						</tr>
						<tr>
							<td valign="top" width="80"><br /><br />Task</td>
							<td colspan="3">
								<strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
								<textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
							</td>
						</tr>
						<tr>
							<td>Task Owner</td>
							<td><input type="text" class="edit-task-owner textfield" readonly></td>
						</tr>

						<tr>
							<td>Allocate to</td>
							<td>
								<select name="task_user" class="edit-task-allocate textfield width100px">
									<?php
										echo $remind_options, $remind_options_all, $contractor_options;
									?>
								</select>
							</td>
						</tr>

						<tr>
							<td>Planned Start Date</td>
							<td><input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" /></td>
							<td>Planned End Date</td>
							<td><input type="text" name="task_end_date" class="edit-end-date textfield width100px" readonly /></td>
						</tr>
						<tr>
							<td>Actual Start Date</td>
							<td><input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px" /></td>
							<td>Actual End Date</td>
							<td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
						</tr>
						<tr>
							<td>Remarks</td>
							<td colspan="3"><textarea name="remarks" class="edit-task-remarks" width="420px"></textarea></td>
						</tr>
						<tr>
							<td colspan="4">
								<div class="buttons">
									<button type="submit" class="positive" onclick="editTask();">Update</button>
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
			
			<div id="jv-tab-5">
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
					<?php if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) { ?>
					<div class="buttons">
						<button type="submit" class="positive" onclick="addMilestoneField();">Add New</button>
						<button type="submit" class="positive" onclick="saveMilestones();">Save List</button>
						<button type="submit" class="positive" onclick="emailMilestones();">Email Timeline</button>
					</div>
					<?php } ?>
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
						var agree=confirm("Are you sure you want to delete this file?");
							if (agree) {
								$(el).parent().parent().remove();
							}
							var data = $('#milestone-management').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
						
						$('#jv-tab-5').block({
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
								$('#jv-tab-5').unblock();
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
						
						$('#jv-tab-5').block({
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
								$('#jv-tab-5').unblock();
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
						$('#jv-tab-5').block({
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
								$('#jv-tab-5').unblock();
							}
						);
					}
				</script>
			</div><!-- id: jv-tab-5 end -->

			<div id="jv-tab-6">
				<form id="customer-detail-read-only" onsubmit="return false;">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<table class="tabbed-cust-layout" cellpadding="0" cellspacing="0">
						<tr>
							<td width="120"><label>First name</label></td>
							<td><b><?php echo $quote_data['cfn'] ?></b></td>
						</tr>
						<tr>
							<td><label>Last Name</label></td>
							<td><b><?php echo $quote_data['cln'] ?></b></td>
						</tr>
						<tr>
							<td><label>Position</label></td>
							<td><b><?php echo $quote_data['position_title'] ?></b></td>
						</tr>
						<tr>
							<td><label>Company</label></td>
							<td><b><?php echo $quote_data['company'] ?></b></td>
						</tr>
						<tr>
							<td><label>Address Line 1</label></td>
							<td><b><?php echo $quote_data['add1_line1'] ?></b></td>
						</tr>
						<tr>
							<td><label>Address Line 2</label></td>
							<td><b><?php echo $quote_data['add1_line2'] ?></b></td>
						</tr>
						<tr>
							<td><label>Suburb</label></td>
							<td><b><?php echo $quote_data['add1_suburb'] ?></b></td>
						</tr>
						<tr>
							<td><label>Region</label></td>
							<td><b><?php echo $quote_data['region_name'] ?></b></td>
						</tr>
						<tr>
							<td><label>Country</label></td>
							<td><b><?php echo $quote_data['country_name'] ?></b></td>
						</tr>
						<tr>
							<td><label>State</label></td>
							<td><b><?php echo $quote_data['state_name'] ?></b></td>
						</tr>
						<tr>
							<td><label>Location</label></td>
							<td><b><?php echo $quote_data['location_name'] ?></b></td>
						</tr>
						<tr>
							<td><label>Post code</label></td>
							<td><b><?php echo $quote_data['add1_postcode'] ?></b></td>
						</tr>
						<tr>
							<td><label>Direct Phone</label></td>
							<td><b><?php echo $quote_data['phone_1'] ?></b></td>
						</tr>
						<tr>
							<td><label>Work Phone</label></td>
							<td><b><?php echo $quote_data['phone_2'] ?></b></td>
						</tr>
							<tr>
							<td><label>Mobile Phone</label></td>
							<td><b><?php echo $quote_data['phone_3'] ?></b></td>
						</tr>
						<tr>
							<td><label>Fax Line</label></td>
							<td><b><?php echo $quote_data['phone_4'] ?></b></td>
						</tr>
						<tr>
							<td><label>Email</label></td>
							<td><b><?php echo $quote_data['email_1'] ?></b></td>
						</tr>
						<tr>
							<td><label>Secondary Email</label></td>
							<td><b><?php echo $quote_data['email_2'] ?></b></td>
						</tr>
						<tr>
							<td><label>Email 3</label></td>
							<td><b><?php echo $quote_data['email_3'] ?></b></td>
						</tr>
						<tr>
							<td><label>Email 4</label></td>
							<td><b><?php echo $customer_tab[0]['email_4'] ?></b></td>
						</tr>
							<tr>
							<td><label>Web</label></td>
							<td><p>&nbsp; <?php echo auto_link($quote_data['www_1']) ?></p>
							</td>
						</tr>
						<tr>
							<td><label>Secondary Web</label></td>
							<td><p>&nbsp; <?php echo auto_link($quote_data['www_2']) ?>
							</td>
						</tr>
					</table>
				</form>
			</div><!-- id: jv-tab-6 end -->
				
			<div id="jv-tab-7"><!-- id: jv-tab-7 start -->
				<div id="querylead_form" style="border:0px solid;" >
					<form id="querylead" name="querylead" method="post" onsubmit="return QueryAjaxFileUpload();">
					
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<h3>Query</h3>
						<table id="querylead_table" class="layout add_query" style="display: none">								
							<tr>
								<td width="120">Query:</td>
								<div id="query_form" style="display:none;" ><input type='text' value='query' name='replay' id='replay' /></div>
								<td width="300"><textarea name="query" id="query" cols="20" rows="3" ></textarea></td>
							</tr>
							<tr>
								<td width="120">Attachment File:</td>
								<td><input type="file" class="textfield" id="query_file" name="query_file" /></td>
							</tr>
							<tr>
								<td>
									<input type="submit" name="query_sub" value="Submit" class="positive submitpositive" />
									<input type="button" name="query_sub" value="Cancel" class="cancel" />
								</td>
							</tr>
						</table>
						<?php if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) { ?>
							<div class="buttons task-init  toggler">
								<button type="button" class="positive" onclick="$('#querylead_table').slideToggle();">Raise Query</button>
							</div>
						<?php } ?>
						
						<table id="lead_query_list" class="existing-query-list">
							<thead> </thead>
							<tbody id="query-file-list"><tr id="querylist"></tr><?php echo $query_files1_html; ?></tbody>
						</table>
						
						<?php if (!empty($query_files1_html)) { ?>
							<div id="pager1">
								<?php echo '&nbsp;';?>
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
						<?php } ?>
					</form>
				</div>
				<script>
					$(function(){
						$(".cancel").click(function(){
							$('#querylead_table').slideToggle();
						})
					})
				</script>
			</div>
		</div>
	</div>
</div>

<?php require (theme_url().'/tpl/footer.php'); ?>
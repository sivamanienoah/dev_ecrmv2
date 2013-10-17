<?php require (theme_url().'/tpl/header.php'); ?>

<style type="text/css">
#lead-init-form {
	display:none;
}
.q-main-left {
	width:100%;
}
#customer-detail-read-only {
	width:500px;
}
#leads-list .item {
	border:1px solid #888;
	padding:5px;
	margin-top:15px;
	background:#4b4b4b;
}
#left-form-content {
	float:left;
}
#leads-list .item.selected {
	background:#f2d8c7;
	color:#555;
	border:2px solid #444;
	margin-top:0;
}
#leads-list .item .lead-links {
	float:right;
	display:block;
	margin-left:5px;
}
.log-container {
	height:auto;
}
.ui-tabs-panel {
	border-top:1px solid #999;
	padding-top:10px;
}
.due-today td {
	background:orange;
	color:#333;
}
.due-today td a span {
	color:#333 !important;
}
</style>

<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>

<script type="text/javascript">

var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

function ndf_cancel() {
    $.unblockUI();
    return false;
}
function ndf_add() {
    $('.new-cust-form-loader .error-handle:visible').slideUp(300);
    var form_data = $('#customer_detail_form').serialize();
    $.post(
        'customers/add_customer/false/false/true',
        form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
        function(res) {
            if (typeof (res) == 'object') {
                if (res.error == false) {
                    $('#hidden_custid_fk').val(res.custid);
                    $("#ex-cust-name").val(res.cust_name);
                    $.unblockUI();
                    $('.notice').slideUp(400);
                } else {
                    $('.error-cont').html(res.ajax_error_str).slideDown(400);
                }
            } else {
                $('.error-cont').html('<p class="form-error">Your session timed out!</p>').slideDown(400);
            }
        },
		"json"
    )
    return false;
}

var lead_id = <?php echo  isset($lead_selected) ? $lead_selected : 0 ?>;
var userid = <?php echo  isset($userdata['userid']) ? $userdata['userid'] : 0 ?>;

function addLog() {
	var the_log = $('#job_log').val();
	if ($.trim(the_log) == '') {
		alert('Please enter your post!');
		return false;
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
	
	
	var email_set = '';
	$('.user-addresses input[type="checkbox"]:checked').each(function(){
		email_set += $(this).attr('id') + ':';
	});
	
	/* update requested on 8/7/2010 - only for Jared */
	if ($('#george_email').is(':checked'))
	{
		email_set += '9898:';
	}
	
	$.blockUI({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#fff', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	
	var form_data = {'userid':userid, 'jobid':lead_id, 'log_content':the_log,'emailto':email_set,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
	
	/* update requested on 8/7/2010 - only for Jared */
	if ($('#george_email').is(':checked'))
	{
		form_data.george_mobile = 1;
	}
	
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
	
	$.post(
		'leads/add_log',
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
						$('.user-addresses input[type="checkbox"]:checked, #attach_pdf, #email_to_customer, #george_email').each(function(){
							$(this).attr('checked', false);
						});
						
						$('#additional_client_emails').val('');
						$('#multiple-client-emails').children('input[type=checkbox])').attr('checked', false).end()
							.slideUp(400);
						
						if (data.status_updated) {
							document.location = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
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
	);
}

function checkDesc() {
	if (isNaN(parseInt($('#hidden_custid_fk').val()))) {
		alert('Customer is not selected properly!\nPlease try again.');
		return false;
	}
	if ($.trim($('#description').val()) == '') {
		return window.confirm("Description area is empty!\nDo you want to continue?");
	} else {
		return true;
	}
}

function confirmDelete() {
	var msg = "Are you sure?\nThis action cannot be undone!\nYou will also delete all the logs and tasks associated with this lead.";
	return window.confirm(msg);
}

function saveAction()
{
	var errors = [],
	action = $('#job-task-desc').val(),
	action_date = $('#next-action input[name="action_date"]').val(),
	action_hours = $('#next-action input[name="action_hours"]').val(),
	action_mins = $('#next-action select[name="action_mins"]').val();
		
	if (action == '')
	{
		errors.push('Action is required!');
	}
	
	if ( ! /^[0-9]+$/.test(action_hours) && action_mins < 15)
	{
		errors.push('Valid hours required!');
	}
	
	if ( ! /^[0-9]{2}-[0-9]{2}-[0-9]{4}$/.test(action_date))
	{
		errors.push('Valid action date is required!');
	}
	
	if (errors.length > 0)
	{
		alert(errors.join('\n'));
		return false;
	}
	
	$('#jv-tab-3').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#fff', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	
	$.post(
		'leads/add_next_action/' + lead_id,
		{'action': action, 'action_date': action_date, 'action_hours': action_hours, 'action_mins':  action_mins,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function (data)
		{
			if (data.error)
			{
				alert(data.errormsg);
			}
			else
			{
				$('#confirm').show('normal', function(){
					setTimeout(function() { $('#confirm').hide('fast'); }, 1500)
				});
			}
			$('#jv-tab-3').unblock();
		},
		'json'
	);
}

<?php
if (isset($quote_data))
{
	?>
function leadConvert() {
	if (window.confirm('Are you sure you want to convert this lead to a quote?')) {
		document.location.href = '<?php echo $this->config->item('base_url') ?>welcome/new_quote/<?php echo "{$quote_data['leadid']}/{$quote_data['custid']}" ?>';
	}
}
	<?php
}
?>

$(function(){
	
	$("#ex-cust-name").autocomplete("hosting/ajax_customer_search/", { minChars:2, width:'308px' }).result(function(event, data, formatted) {
		$('#hidden_custid_fk').val(data[1]);
		$('#hidden_custid_details').val(data[0]);
		$('.notice').slideUp(400);
	});
	
	$('.modal-new-cust').click(function(){
		$.blockUI({
					message:nc_form_msg,
					css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},
					overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
				});
		$.get(
			'ajax/data_forms/new_customer_form',
			{},
			function(data){
				$('.new-cust-form-loader').slideUp(500, function(){
					$(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
					$(this).css('text-align', 'left').html(data).slideDown(500, function(){
						$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					});
				})
			}
		);
		return false;
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
	
	$('#leads-list').children('.selected').clone().prependTo('#leads-list').end().remove();
	
	$('#lsearch').keyup(function(){
        var st = $(this).val().toLowerCase();
        if (st != '') {
			$('td.cust-data').each(function(){
				if ($('a.text', $(this)).text().toLowerCase().indexOf(st) < 0) {
                    $(this).parent().hide();
                }
			})
        } else {
           $('td.cust-data').parent().not(':visible').show();
        }
    });
	
	$('#job-view-tabs').tabs({
							 show: function (event, ui) {
									if (ui.index == 3)
									{	
										loadExistingTasks();
									}
								}
							});
	
	$('.data-table tr').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
	
	$('#jv-tab-3 .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});
	
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
	
	$('#set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});
	
	$('.task-list-item').livequery(function(){
		$(this).hover(
			function() { $('.delete-task', $(this)).css('display', 'block'); },
			function() { $('.delete-task', $(this)).css('display', 'none'); }
		);
	});
	
});

<?php
if (isset($quote_data))
{
	?>
function runAjaxFileUpload() {
	var _uid = new Date().getTime();
	$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
	$.ajaxFileUpload
	(
		{
			url:'ajax/request/file_upload/<?php echo $quote_data['leadid'] ?>/lead',
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
						var _file_link = '<a href="vps_lead_data/<?php echo $quote_data['leadid'] ?>/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a> <span>'+data.file_size+'</span>';
						var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/vps_lead_data/<?php echo $quote_data['leadid'] ?>/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>';
						
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

	<?php
}
?>

</script>

<script type="text/javascript" src="assets/js/lead_tasks.js?q=34"></script>

<div id="content">
	
	<div class="inner">
	<?php if(($this->session->userdata('accesspage')==1 && $this->uri->segment(3)=="") || ($this->session->userdata('edit')==1 && is_numeric($this->uri->segment(3)))) { ?>
		<?php
		if (isset($lead_selected))
		{
			?>
		<div id="left-form-content">
			<form action="<?php $this->uri->uri_string() ?>" id="service-update" method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<table border="0" cellpadding="0" cellspacing="5">
					<tr>
						<td>
							<p><label>Serviced By</label></p>
						</td>
						<td>
							<select name="job_belong_to_edit" id="job_belong_to_edit" class="textfield width200px" style="margin:0;">
								<?php
								/*$arrSalesCode = array('FF','AT68','AT68ENT','DF','NJ','PL','RW','NV',
										'AHS','JG','LV','WH','SP','NH','NT','RE1','ADPT','DWT');*/
								foreach ($cfg['sales_codes'] as $sck => $scv)
								{
										
										if (($userdata['level'] == 4 && $userdata['sales_code'] == $sck) || $userdata['level'] != 4)
										{
										?>
										<option value="<?php echo $sck ?>"<?php echo (isset($quote_data['belong_to']) && $quote_data['belong_to'] == $sck) ? ' selected="selected"' : '' ?>><?php echo $scv ?></option>
										<?php
										}
										
								}
								?>
							</select>
						</td>
						<td>
							<div class="buttons">
								<button type="submit" class="positive">Update</button>
							</div>
						</td>
					</tr>
				</table>
				
			</form>
			
			<form action="<?php $this->uri->uri_string() ?>" id="status-update" method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<table border="0" cellpadding="0" cellspacing="5">
					<tr>
						<td>
							<p><label>Lead Status</label></p>
						</td>
						<td>
							<select name="lead_status" id="lead_status" class="textfield width200px" style="margin:0;">
								<option value="1"<?php if ($quote_data['lead_status'] == 1) echo ' selected="selected"' ?>>Active</option>
								<option value="2"<?php if ($quote_data['lead_status'] == 2) echo ' selected="selected"' ?>>Declined</option>
							</select>
						</td>
						<td>
							<div class="buttons">
								<button type="submit" class="positive">Set Status</button>
							</div>
						</td>
					</tr>
				</table>
				
			</form>
			
			
			
				<p>&nbsp;</p>
				
				<ul id="job-view-tabs">
					<li><a href="#jv-tab-1">Main</a></li>
					<li><a href="#jv-tab-2">Customer</a></li>
					<li><a href="#jv-tab-3" style="display:none;">Next Action</a></li>
					<li><a href="#jv-tab-4">Task</a></li>
					<li><a href="#jv-tab-5">Files</a></li>
				</ul>
				
				<div id="jv-tab-1">
					<div id="leads-list">
						<div class="item selected" style="width:400px;">
							<h6>Customer</h6>
							<?php echo $quote_data['first_name'], ' ', $quote_data['last_name'] ?>
							<?php
							if (trim($quote_data['description']) != '')
							{
								?>
							<br /><br />
							<h6>Description</h6>
							<?php echo nl2br($quote_data['description']) ?>
								<?php
							}
							?>
						</div>
					</div>
				</div>
				
				<div id="jv-tab-2">
					<form action="<?php $this->uri->uri_string() ?>" id="customer-detail-read-only" onsubmit="return false">
					
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
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
						<tr>
							<td>&nbsp;</td>
							<td><a href="customers/add_customer/update/<?php echo $quote_data['custid'] ?>">More Info</a></td>
						</tr>
					</table>
					</form>
				</div>
				
				<div id="jv-tab-3">
					<div id="confirm" style="display:none;">
						<p>Task Saved</p>
					</div>
					<table border="0" cellpadding="0" cellspacing="0" id="next-action">
						<tr>
							<td width="80">Next Action</td>
							<td>
								<strong><span id="task-desc-countdown-na">240</span></strong> characters left.<br />
								<textarea name="job_task" id="job-task-desc-na" class="width420px"><?php echo $quote_data['next_action'] ?></textarea>
							</td>
						</tr>
						<tr>
							<td>Action Date</td>
							<td>
								<input type="text" name="action_date" class="textfield pick-date width100px" value="<?php echo ($quote_data['next_action_date'] == '') ? '' : date('d-m-Y', strtotime($quote_data['next_action_date'])) ?>" />
							</td>
						</tr>
						<tr>
							<td>Time</td>
							<td>
								<input name="action_hours" type="text" class="textfield width100px" value="<?php echo $quote_data['action_hours'] ?>" /> Hours and
								<select name="action_mins" class="textfield">
									<option value="0"<?php if ($quote_data['action_mins'] == 0) echo ' selected="selected"' ?>>0</option>
									<option value="15"<?php if ($quote_data['action_mins'] == 15) echo ' selected="selected"' ?>>15</option>
									<option value="30"<?php if ($quote_data['action_mins'] == 30) echo ' selected="selected"' ?>>30</option>
									<option value="45"<?php if ($quote_data['action_mins'] == 45) echo ' selected="selected"' ?>>45</option>
								</select>
								Mins
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="buttons">
									<button type="submit" class="positive" onclick="saveAction();">Save Action</button>
								</div>
							</td>
						</tr>
					</table>
				</div>
				
				<?php
				$quote_data['assigned_to'] = $quote_data['belong_to'];
				include theme_url() . '/tpl/user_accounts_options.php';
				?>
				<div id="jv-tab-4">
					<form id="set-job-task" onsubmit="return false;">
					
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
						<h3>Tasks</h3>
						<table border="0" cellpadding="0" cellspacing="0" class="task-add toggler">
							
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
								</td>
							</tr>
							<tr>
							<td>&nbsp;</td>
							<td colspan=3>
								Require checklist before completion : <input type="checkbox" name="require_qc" /><br>
								Priority Support : <input type="checkbox" name="priority" />
							</td>
						</tr>
							<tr>
								<td colspan="4">
									<div class="buttons">
										<button type="submit" class="positive" onclick="addNewTask('','<?php echo $this->security->get_csrf_token_name()?>','<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
									</div>
									<div class="buttons">
										<button type="submit" class="negative" onclick="$('.toggler').slideToggle(1000);">Cancel</button>
									</div>
								</td>
							</tr>
						</table>
						<div class="buttons task-init toggler">
							<button type="submit" class="positive" onclick="$('.toggler').slideToggle(1200);">Add New</button>
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
								</td>
							</tr>
							<tr>
							<td>&nbsp;</td>
							<td colspan=3>
								Require checklist before completion : <input type="checkbox" name="require_qc" /><br>
								Priority Support : <input type="checkbox" name="priority" />
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
				</div><!-- id: jv-tab-4 end -->
				
				<div id="jv-tab-5">
					<form name="ajax_file_upload">
					
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
						<div id="upload-container">
							<img src="assets/img/select_file.jpg" alt="Browse" id="upload-decoy" />
							<input type="file" class="textfield" id="ajax_file_uploader" name="ajax_file_uploader" onchange="return runAjaxFileUpload();" size="1" />
							<!-- input type="button" value="Upload File" onclick="runAjaxFileUpload();" / -->
						</div>
						<ul id="job-file-list">
						<?php echo $job_files_html ?>
						</ul>
					</form>
				</div><!-- id: jv-tab-5 end -->
		</div>
		<div class="right-communication">
			
			
				
			<form id="comm-log-form">
			
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<?php
				if ($quote_data['lead_status'] == 1)
				{
					?>
				<div class="button-container">
					<div class="buttons">
						<button type="submit" class="positive" onclick="leadConvert(); return false;">Convert this lead to a quote</button>
					</div>
				</div>
					<?php
				}
				?>
				<textarea name="job_log" id="job_log" class="textfield width99pct height100px"></textarea>
				<div class="button-container">
					<div class="buttons">
						<button type="submit" class="positive" onclick="addLog();  return false;">Add Post</button>
					</div>
				</div>
			
			<?php
			if (isset($userdata) && in_array($userdata['level'], array(0,1,2,4)))
			{
				?>
				<p class="email-set-options">
					<input type="checkbox" name="email_to_customer" id="email_to_customer" /> <label for="email_to_customer" class="normal">Email Client</label>
					<input type="hidden" name="client_email_address" id="client_email_address" value="<?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?>" />
					<input type="hidden" name="client_full_name" id="client_full_name" value="<?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?>" />
					<input type="hidden" name="requesting_client_approval" id="requesting_client_approval" value="0" /></p>
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
				<?php
			}
			?>
			
				<div class="user-addresses">
					<p><label>Email to:</label></p>
					<?php
					if (count($user_accounts)) foreach ($user_accounts as $ua)
					{
						if (
							( ($ua['level'] == 4 && $ua['sales_code'] == $quote_data['belong_to']) || $ua['level'] != 4 )
							&&
							$ua['inactive'] != 1
						   )
						{
							echo '<span class="user"><input type="checkbox" name="email-log-' . $ua['userid'] . '" id="email-log-' . $ua['userid'] . '" /> <label for="email-log-' . $ua['userid'] . '">' . $ua['first_name'] . ' ' . $ua['last_name'] . '</label></span>';
						}
					}
					?>
				</div>
				
			</form>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<h4>Lead History</h4>
			<div class="log-container">
				<?php echo  $log_html ?>
			</div>
			
			
		</div>
		<?php
			/* end lead selected check */
			}
			else
			{
			/* just the list */
		?><!-- -->
		
		<div class="q-main-left">
            
			<form action="leads" method="post" style="float:right;" onsubmit="return false;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				Quick Search <input type="text" name="lsearch" id="lsearch" class="textfield width200px"/>
			</form>
            
			<form action="leads" method="post" id="lead-init-form" onsubmit="return checkDesc();">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
                <input type="hidden" name="custid_fk" id="hidden_custid_fk" />
				<input type="hidden" name="cust_form_details" id="hidden_custid_details" />
             
                <div class="q-init-details">
                    <p><label>Start by typing in <strong>customer name</strong> or <strong>company name</strong>.</label></p>
                    <p><input type="text" name="ex_cust_name" id="ex-cust-name" class="textfield width300px" /></p>
                    <p class="notice width250px">If this is a new customer you need to add the<br /> customer
                    by <a href="#" class="modal-new-cust" onclick="return false;">completing their details</a>.</p>
                    <p><label>Lead Description / Requirements</label><br />
					<span>Please include Lead source or referral source,<br />Initial communication method (email, phone or visit),<br />existing website details (where applicable) and<br /> possibly their budget allocation.</span></p>
                    <p><textarea name="description" id="description" class="textfield width300px height100px"></textarea></p>
					<p><label>Serviced By</label></p>
                    <p><select name="job_belong_to" id="job_belong_to" class="textfield width300px">
							<?php
							$arrSalesCode = array('FF','AT68','AT68ENT','DF','NJ','PL','RW','NV',
										'AHS','JG','LV','WH','SP','NH','NT','RE1','ADPT','DWT');
							foreach ($cfg['sales_codes'] as $sck => $scv)
							{
								if (!in_array($sck,$arrSalesCode))
								{
									
									?>
									<option value="<?php echo $sck ?>"<?php echo (isset($quote_data['belong_to']) && $quote_data['belong_to'] == $sck) ? ' selected="selected"' : '' ?>><?php echo $scv ?></option>
									<?php
									
								}
							}
							?>
                        </select>
					</p>
                    <div class="buttons">
                        <button type="submit" class="positive">Add</button>
                    </div>
					<div class="buttons">
                        <button type="button" onclick="$('#lead-init-form').slideUp(300); return false;">Cancel</button>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </form>
			
			<h3>Current leads</h3>
			<br />
			
			
			<table border="0" cellpadding="0" cellspacing="0" class="data-table" width="99%">
				
				<thead>
					<tr>
						<th>Customer</th>
						<th width="105">Next Task</th>
						<th width="105">Created On</th>
						<th width="100">Actions</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					if (!isset($quote_section))
					{
						$quote_section = '';
					}
					
						if (is_array($leads_list) && count($leads_list) > 0) { ?>
						<?php
						foreach ($leads_list as $record) {
							$action_date_class = '';
							$next_action_date = '';
							if ($record['next_task_date'] != '')
							{
								$next_action_date = date('d-m-Y', strtotime($record['next_task_date']));
								if ($next_action_date == date('d-m-Y'))
								{
									$action_date_class = ' due-today';
								}
							}
							?>
						<tr class="<?php echo $action_date_class ?>">
							<td class="cust-data">
								<?php
								if (is_file(dirname(FCPATH) . '/assets/img/sales/' . $record['belong_to'] . '.jpg'))
								{
									?>
									<img src="assets/img/sales/<?php echo $record['belong_to'] ?>.jpg" title="<?php echo $record['belong_to'] ?>" /> 
									<?php
								}
								?>
								<?php if($this->session->userdata('edit')==1) { ?>
								<a href="leads/index/<?php echo  $record['leadid'] ?>" class="text"><?php echo  $record['first_name'] . ' ' . $record['last_name'] . ' - ' . $record['company'] ?></a><?php } else echo  $record['first_name'] . ' ' . $record['last_name'] . ' - ' . $record['company']; ?> <span style="color:#f70;">( <?php if($this->session->userdata('edit')==1) { ?><a href="customers/add_customer/update/<?php echo  $record['custid'] ?>" style="text-decoration:underline;">client info</a><?php } else echo "client info"; ?> )</span>
							</td>
							<td><?php echo  $next_action_date ?></td>
							<td><?php echo  date('d-m-Y H:i', strtotime($record['date'])) ?></td>
							<td class="actions" align="center"><?php if($this->session->userdata('edit')==1) { ?><a href="leads/index/<?php echo  $record['leadid'] ?>">Edit</a><?php } else echo "Edit"; ?>
							<?php
							echo ($this->session->userdata('delete')==1) ? ' | <a href="leads/delete/' . $record['leadid'] . '" onclick="return confirmDelete();">Delete</a>' : '';
							?>
							</td>
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="4" align="center">No records available to be displayed!</td>
						</tr>
					<?php } ?>
				</tbody>
				
			</table>
			
		</div>
		
		<?php
			}
		/* end lead list */
		}
		else { echo "You have no rights to access this page"; }
		?>
		
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>

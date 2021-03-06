<style>
.hide-calendar .ui-datepicker-calendar { display: block; }
</style>
<?php $attributes = array('id'=>'edit-contract', 'name'=>'edit-contract'); ?>
<?php #echo "<pre>"; print_r($upload_data); echo "</pre>"; ?>
<?php echo form_open_multipart("reseller/editResellerContract", $attributes); ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="contracter_id" id="contracter_id" value="<?php echo $contract_data['contracter_id']; ?>" readonly />
	<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_data['id']; ?>" readonly />
	<input type="hidden" name="hidden_contract_manager" id="hidden_contract_manager" value="<?php echo $contract_data['contract_manager']; ?>" readonly />
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Contract Manager<span class='red'> *</span></td>
			<td>
				<select name='contract_manager' class="textfield width200px" id='contract_manager'>
					<option value=''>Select</option>
					<?php if(!empty($users) && count($users)>0) { ?>
						<?php foreach($users as $user_rec) { ?>
							<?php 
								$username = $user_rec['first_name'];
								if(isset($user_rec['last_name'])){
									$username .= " ".$user_rec['last_name'];
								}
								if(isset($user_rec['emp_id'])){
									$username .= " - ".$user_rec['emp_id'];
								}
							?>
							<option value=<?php echo $user_rec['userid']; ?> <?php if($contract_data['contract_manager'] == $user_rec['userid']) echo "selected='selected'"; ?>><?php echo $username; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='contract_manager_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Title<span class='red'> *</span></td>
			<td>
				<input type="text" name="contract_title" id="contract_title" class="textfield width200px" value="<?php echo $contract_data['contract_title'] ?>" />
				<div class='ajx_failure_msg succ_err_msg' id='contract_title_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Start date<span class='red'> *</span></td>
			<td>
				<?php
					$start_date = (!empty($contract_data['contract_start_date']) && ($contract_data['contract_start_date'] != '0000-00-00 00:00:00')) ? date('d-m-Y', strtotime($contract_data['contract_start_date'])) : '';
				?>
				<input type="text" name="contract_start_date" id="contract_start_date" data-calendar="true" value="<?php echo $start_date; ?>" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_start_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract End date<span class='red'> *</span></td>
			<td>
				<?php
					$end_date = (!empty($contract_data['contract_end_date']) && ($contract_data['contract_end_date'] != '0000-00-00 00:00:00')) ? date('d-m-Y', strtotime($contract_data['contract_end_date'])) : '';
				?>
				<input type="text" name="contract_end_date" id="contract_end_date" data-calendar="true" value="<?php echo $end_date; ?>" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_end_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Renewal Reminder date<span class='red'> *</span></td>
			<td>
				<?php
					$renew_date = (!empty($contract_data['renewal_reminder_date']) && ($contract_data['renewal_reminder_date'] != '0000-00-00 00:00:00')) ? date('d-m-Y', strtotime($contract_data['renewal_reminder_date'])) : '';
				?>
				<input type="text" name="renewal_reminder_date" id="renewal_reminder_date" data-calendar="true" value="<?php echo $renew_date; ?>" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='renewal_reminder_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<textarea name="description" id="description" class="textfield width200px" ><?php echo $contract_data['description']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>Contract Signed Date<span class='red'> *</span></td>
			<td>
				<?php
					$signed_date = (!empty($contract_data['contract_signed_date']) && ($contract_data['contract_signed_date'] != '0000-00-00 00:00:00')) ? date('d-m-Y', strtotime($contract_data['contract_signed_date'])) : '';
				?>
				<input type="text" name="contract_signed_date" id="contract_signed_date" data-calendar="true" value="<?php echo $signed_date; ?>" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_signed_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Status<span class='red'> *</span></td>
			<td>
				<select name='contract_status' class="textfield width200px" id='contract_status'>
					<?php if(is_array($this->contract_status) && !empty($this->contract_status) && count($this->contract_status)>0) { ?>
						<?php foreach($this->contract_status as $sta_key=>$sta_val) { ?>
							<option value=<?php echo $sta_key; ?> <?php if($contract_data['contract_status'] == $sta_key) echo "selected='selected'"; ?>><?php echo $sta_val; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg' id='contract_status_err'></div>
			</td>
		</tr>
		<tr>
			<td>Currency<span class='red'> *</span></td>
			<td>
				<select name='currency' class="textfield width200px" id='currency'>
					<option value=''>Select</option>
					<?php if(!empty($currencies) && count($currencies)>0) { ?>
						<?php foreach($currencies as $cur_rec) { ?>
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($contract_data['currency'] == $cur_rec['expect_worth_id']) echo "selected='selected'"; ?>><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='currency_err'></div>
			</td>
		</tr>
		<tr>
			<td>Tax</td>
			<td>
				<input onkeypress="return isNumberKey(event)" type="text" name="tax" id="tax" maxlength="5" value="<?php echo $contract_data['tax'] ?>" class="textfield width200px"/>
				<span class='red'>(Numbers)</span>
				<div class='ajx_failure_msg succ_err_msg' id='tax_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Document</td>
			<td> <!--multiple--->
				<form name="payment_ajax_file_upload">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div id="upload-container">
						<input type="file" multiple title='upload' class="textfield" id="contract_document" name="contract_document[]" onchange="return runContractAjaxFileUpload();" />
						<input type="hidden" id="exp_type" value="">									
					</div>
				</form>
				<div id="existUploadedFile">
					<?php if(is_array($upload_data) && !empty($upload_data) && count($upload_data)>0) { ?>
						<?php $serial_id = 1; ?>
						<?php foreach($upload_data as $rec_file) { ?>
							<div style="float: left; width: 100%; margin-top: 5px;">
								<span style="float: left;">
									<?php $file_id = base64_encode($rec_file['id']); ?>
									<?php #$file_id = $rec_file['id']; ?>
									<a onclick="download_contract_files('<?php echo $file_id; ?>'); return false;"><?php echo $rec_file['file_name']; ?></a>
								</span>
								<?php if($this->session->userdata('delete')==1) { ?>
								<a class="del_file" serial_id="<?php echo $serial_id; ?>" id="<?php echo $file_id; ?>"> </a>
								<?php } ?>
							</div>
						<?php $serial_id++; ?>
						<?php } ?>
					<?php } ?>
				</div>
				<div id="contractUploadFile"></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php //if ($readonly_status == false) { ?>
				<div class="buttons">
					<?php if($this->session->userdata('edit')==1) { ?>
						<button type="submit" class="positive">Update</button>
					<?php } ?>
					<button onclick="reset_add_form(); return false;" class="negative">Cancel</button>
				</div>
				<?php //} ?>
			</td>
		</tr>
	</table>
<?php form_close(); ?>
<script type="text/javascript">
var contracter_user_id  = '<?php echo $contract_data['contracter_id']; ?>';
var contract_id 		= '<?php echo $contract_data['id']; ?>';
$( document ).ajaxSuccess(function( event, xhr, settings ) {
	if(settings.target=="#output1") {
		if(xhr.responseText=='success') {
			$('#succes_add_contract_data').html("<span class='ajx_success_msg'>Contract Updated Successfully.</span>");
			reset_add_form();
		} else if(xhr.responseText == 'error') {
			$('#succes_add_contract_data').html("<span class='ajx_failure_msg'>Error in updating the contract.</span>");
		}
		$('#succes_add_contract_data').show();
		setTimeout('timerfadeout()', 4000);
	}
});

$(function(){
	var options = {
		target:      '#output1',   // target element(s) to be updated with server response 
		beforeSubmit: validateEditContractForm, // pre-submit callback 
		success:      ''  // post-submit callback 
	}; 
	$('#edit-contract').ajaxForm(options);
	
	/*Date picker*/
	var on_load_start_date = $('#contract_start_date').val();
	var on_load_end_date   = $('#contract_end_date').val();
	
	$('#renewal_reminder_date').datepicker("option", "minDate", on_load_start_date);
	// $('#contract_signed_date').datepicker("option", "minDate", on_load_start_date);
	$('#renewal_reminder_date').datepicker("option", "maxDate", on_load_end_date);
	// $('#contract_signed_date').datepicker("option", "maxDate", on_load_end_date);
	
	$('#contract_start_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		maxDate: on_load_end_date, 
		changeMonth: true, 
		changeYear: true, 
		onSelect: function(date) {
			/* if($('#contract_end_date').val!='')
			{
				$('#contract_end_date').val('');
			} */				
			var return_date = $('#contract_start_date').val();
			$('#contract_end_date').datepicker("option", "minDate", return_date);
		}
	});
	$('#contract_end_date').datepicker({ 
		dateFormat: 'dd-mm-yy',
		minDate: on_load_start_date,
		changeMonth: true, 
		changeYear: true, 
		onSelect: function(dateText, instance) {
			var end_date 	= $('#contract_end_date').val();
			var start_date  = $('#contract_start_date').val();
			/*set one month minus on renewal reminder date*/
			var date = $.datepicker.parseDate(instance.settings.dateFormat, dateText, instance.settings);
			date.setMonth(date.getMonth() - 1);
			$("#renewal_reminder_date").datepicker("setDate", date);
			$('#renewal_reminder_date').datepicker("option", "minDate", start_date);
			// $('#contract_signed_date').datepicker("option", "minDate", start_date);
			$('#renewal_reminder_date').datepicker("option", "maxDate", end_date);
			// $('#contract_signed_date').datepicker("option", "maxDate", end_date);
		}
	});
	
	$("#renewal_reminder_date").datepicker({dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true});
	$("#contract_signed_date").datepicker({dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, maxDate: 0});
});
	
//validate the form
function validateEditContractForm()
{
	// alert('validateEditContractForm');
	var errors = [];
	var validate_form = true;
	if(($.trim($('#contract_manager').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Select Contract Manager.</p>');
		$('#contract_manager_err').html('Select Contract Manager.');
	} else {
		$('#contract_manager_err').html('');
	}
	if(($.trim($('#contract_title').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Contract Title.</p>');
		$('#contract_title_err').html('Enter Contract Title.');
	} else {
		$('#contract_title_err').html('');
	}
	if(($.trim($('#contract_start_date').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Contract Start Date.</p>');
		$('#contract_start_date_err').html('Enter Contract Start Date.');
	} else {
		$('#contract_start_date_err').html('');
	}
	if(($.trim($('#contract_end_date').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Contract End Date.</p>');
		$('#contract_end_date_err').html('Enter Contract End Date.');
	} else {
		$('#contract_end_date_err').html('');
	}
	if(($.trim($('#renewal_reminder_date').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Renewal Reminder Date.</p>');
		$('#renewal_reminder_date_err').html('Enter Renewal Reminder Date.');
	} else {
		$('#renewal_reminder_date_err').html('');
	}
	if(($.trim($('#contract_signed_date').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Contract Signed Date.</p>');
		$('#contract_signed_date_err').html('Enter Contract Signed Date.');
	} else {
		$('#contract_signed_date_err').html('');
	}
	if(($.trim($('#contract_status').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Contract Status.</p>');
		$('#contract_status_err').html('Enter Contract Status.');
	} else {
		$('#contract_status_err').html('');
	}
	if(($.trim($('#currency').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Select Currency.</p>');
		$('#currency_err').html('Select Currency.');
	} else {
		$('#currency_err').html('');
	}
	if(($.trim($('#tax').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter tax.</p>');
		$('#tax_err').html('Enter tax.');
	} else {
		$('#tax_err').html('');
	}
	if (errors.length > 0 && validate_form == false) 
	{
		setTimeout('timerfadeout()', 6000);
		return false;
	}
}

/*file upload by ajax*/
function runContractAjaxFileUpload() 
{
	var _uid				 = new Date().getTime();
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;

	$.ajaxFileUpload({
		url: 'reseller/contractFileUpload/'+contracter_user_id,
		secureuri: false,
		fileElementId: 'contract_document',
		dataType: 'json',
		data: params,
		success: function (data, status) {
			if(typeof(data.error) != 'undefined') {
				if(data.error != '') {
					if (window.console) {
						console.log(data);
					}
					if (data.msg) {
						alert(data.msg);
					} else {
						alert('File upload failed!');
					}
				} else {	
					if(data.msg == 'File successfully uploaded!') {
						// alert(data.msg);				
						$.each(data.res_file, function(i, item) {
							var res = item.split("~",3);
							// alert(res[2]); return false;
							var name = '<div style="float: left; width: 100%; margin-top: 5px;"><input type="hidden" name="file_id[]" value="'+res[0]+'"><span style="float: left;">'+res[1]+'</span><a id="'+res[0]+'" serial_id="'+res[2]+'" class="del_file"> </a></div>';
							$("#contractUploadFile").append(name);
						});
						$.unblockUI();
					}
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
	});
	$('#contract_document').val('');
	return false;
}

$("#existUploadedFile").delegate("a.del_file","click",function() {
	/*delete the file by ajax function*/
	var str_delete 	= $(this).attr("serial_id");
	var result 		= confirm("Are you sure you want to delete this attachment?");
	if (result==true) {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: site_base_url+'reseller/deleteContractUploads/',
			data: '&file_id='+$(this).attr("id")+'&contract_id='+contract_id+'&contracter_user_id='+contracter_user_id+'&'+csrf_token_name+'='+csrf_hash_token,
			cache: false,
			beforeSend:function() {

			},
			success: function(data) {
				console.info(data);
				if(data.res=='success'){
					$('a[serial_id="'+str_delete+'"]').parent("div").remove();
				}
			}                                                                                   
		});
	} else {
		return false;
	}
});
$("#contractUploadFile").delegate("a.del_file","click",function() {
	// var str_delete 	= $(this).attr("id");
	var str_delete 	= $(this).attr("serial_id");
	var result 		= confirm("Are you sure you want to delete this attachment?");
	if (result==true) {
		// $('#'+str_delete).parent("div").remove();
		$('a[serial_id="'+str_delete+'"]').parent("div").remove();
	}
});
</script>
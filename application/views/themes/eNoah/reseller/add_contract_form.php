<?php $attributes = array('id'=>'add-contract', 'name'=>'add-contract'); ?>
<?php echo form_open_multipart("reseller/addResellerContract", $attributes); ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="contracter_id" id="contracter_id" value="<?php echo $reseller_det[0]['userid']; ?>" readonly />
	<input type="hidden" name="hidden_contract_manager" id="hidden_contract_manager" value="<?php echo $reseller_det[0]['contract_manager']; ?>" readonly />
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Contracter Manager<span class='red'> *</span></td>
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
							<option value=<?php echo $user_rec['userid']; ?> <?php if($reseller_det[0]['contract_manager'] == $user_rec['userid']) echo "selected='selected'"; ?>><?php echo $username; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='contract_manager_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Title<span class='red'> *</span></td>
			<td>
				<input type="text" name="contract_title" id="contract_title" class="textfield width200px" />
				<div class='ajx_failure_msg succ_err_msg' id='contract_title_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Start date<span class='red'> *</span></td>
			<td>
				<input type="text" name="contract_start_date" id="contract_start_date" data-calendar="true" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_start_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract End date<span class='red'> *</span></td>
			<td>
				<input type="text" name="contract_end_date" id="contract_end_date" data-calendar="true" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_end_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Renewal Reminder date<span class='red'> *</span></td>
			<td>
				<input type="text" name="renewal_reminder_date" id="renewal_reminder_date" data-calendar="true" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='renewal_reminder_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<textarea name="description" id="description" class="textfield width200px" > </textarea>
			</td>
		</tr>
		<tr>
			<td>Contract Signed Date<span class='red'> *</span></td>
			<td>
				<input type="text" name="contract_signed_date" id="contract_signed_date" data-calendar="true" class="textfield width200px pick-date" readonly />
				<div class='ajx_failure_msg succ_err_msg' id='contract_signed_date_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract Status<span class='red'> *</span></td>
			<td>
				<select name='contract_status' class="textfield width200px" id='contract_status'>
					<?php if(is_array($this->contract_status) && !empty($this->contract_status) && count($this->contract_status)>0) { ?>
						<?php foreach($this->contract_status as $sta_key=>$sta_val) { ?>
							<option value=<?php echo $sta_key; ?> <?php ?>><?php echo $sta_val; ?></option>
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
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php ?>><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='currency_err'></div>
			</td>
		</tr>
		<tr>
			<td>Tax</td>
			<td>
				<input onkeypress="return isNumberKey(event)" type="text" name="tax" id="tax" maxlength="5" class="textfield width200px"/>
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
				<div id="contractUploadFile"></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php //if ($readonly_status == false) { ?>
				<div class="buttons">
					<button type="submit" class="positive">Add</button>
					<button onclick="reset_add_form(); return false;" class="negative">Cancel</button>
				</div>
				<?php //} ?>
			</td>
		</tr>
	</table>
<?php form_close(); ?>
<script type="text/javascript">
	var contracter_user_id = '<?php echo $reseller_det[0]['userid'] ?>';
	$( document ).ajaxSuccess(function( event, xhr, settings ) {
		if(settings.target=="#output1") {
			if(xhr.responseText=='success') {
				$('#succes_add_contract_data').html("<span class='ajx_success_msg'>Contract Added Successfully.</span>");
				reset_add_form();
			} else if(xhr.responseText == 'error') {
				$('#succes_add_contract_data').html("<span class='ajx_failure_msg'>Error in adding contract.</span>");
			}
			$('#succes_add_contract_data').show();
			// loadOtherCostGrid(project_id);
			setTimeout('timerfadeout()', 4000);
		}
	});

	$(function(){
		var options = {
			target:      '#output1',   // target element(s) to be updated with server response 
			beforeSubmit: validateContractForm, // pre-submit callback 
			success:      ''  // post-submit callback 
		}; 
		$('#add-contract').ajaxForm(options);

		$('#contract_start_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
			if($('#contract_end_date').val!='')
			{
				$('#contract_end_date').val('');
			}
			var return_date = $('#contract_start_date').val();
			$('#contract_end_date').datepicker("option", "minDate", return_date);
		}});
		$('#contract_end_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true , onSelect: function(dateText, instance) {
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
		}});
		
		$("#renewal_reminder_date").datepicker({dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true});
		$("#contract_signed_date").datepicker({dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, maxDate: 0});
		
	});
	
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode;
		if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

		return true;
	}
	
//validate the form
function validateContractForm()
{
	// alert('validateContractForm');
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
		// $('#succes_add_contract_data').html(errors.join(''));
		// $('#succes_add_contract_data').show();
		return false;
	}
}

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
							// alert(res[0]+res[1]);	
							var name = '<div style="float: left; width: 100%;"><input type="hidden" name="file_id[]" value="'+res[0]+'"><span style="float: left;">'+res[1]+'</span><a id="'+res[0]+'" serial_id="'+res[2]+'" class="del_file"> </a></div>';
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
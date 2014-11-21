<!--form id="update-payment-terms"-->
<?php $attributes = array('id' => 'update-payment-terms','name' => 'update-payment-terms'); ?>
<?php echo form_open_multipart("project/set_payment_terms/".$expect_id, $attributes); ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Payment Milestone *</td>
			<td><input type="text" name="sp_date_1" id="sp_date_1" value= "<?php echo $project_milestone_name; ?>" class="textfield width200px" /></td>
		</tr>
		<tr>
			<td>Milestone date *</td>
			<td><input type="text" name="sp_date_2" id="sp_date_2" value= "<?php echo $expected_date; ?>" class="textfield width200px pick-date" readonly /></td>
			
		</tr>
		<tr>
			<td>Value *</td>
			<td><input type="text" onkeypress="return isNumberKey(event)" name="sp_date_3" id="sp_date_3" value= "<?php echo $project_milestone_amt; ?>" class="textfield width200px" />
			<span style="color:red;">(Numbers only)</span>
			</td>
		</tr>
		<tr>
			<td>Remarks </td>
			<td><textarea name="payment_remark" id="payment_remark" class="textfield width200px" ><?php echo $payment_remark; ?></textarea></td>
		</tr>
		<tr>
			<td>Attachment File </td>
			<td>
				<a title="Add Folder" href='javascript:void(0)' onclick="open_files(<?php echo $job_id; ?>); return false;"><img src="assets/img/select_file.jpg" alt="Select Files" ></a>
				<div id="show_files">
					<?php 
						foreach($attached_file as $att_file){
					?>
							<div style="float: left; width: 100%;">
								<input type="hidden" value="<?php echo $att_file['file_id']; ?>" name="file_id[]" />
								<span style="float: left;"><?php echo $att_file['lead_files_name']; ?></span>
								<a class="del_file" id="<?php echo $att_file['file_id']; ?>"> </a>
							</div>
					<?php
						}
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php // if ($readonly_status == false) { ?>
				<div class="buttons">
					<!--button type="submit" class="positive" onclick="updateProjectPaymentTerms('<?php echo $expect_id; ?>'); return false;">Update Payment Terms</button-->
					<button type="submit" class="positive">Update Payment Terms</button>
				</div>
				<?php // } ?>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="<?php echo $job_id; ?>" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
			</td>
		</tr>
	</table>
</form>

<script>
$( document ).ajaxSuccess(function( event, xhr, settings ) {
	if(settings.target=="#output2") {
		$('.payment-profile-view:visible').slideUp(400);
		$('.payment-terms-mini-view1').html(xhr.responseText);
		$('#update-payment-terms').remove();
		paymentProfileView();
		$('#show_files').empty();
	}
});
$(function(){
	var options_updt = { 
		target:      '#output2',   // target element(s) to be updated with server response 
		beforeSubmit: showRequest2, // pre-submit callback 
		success:      ''  // post-submit callback 
	}; 
	$('#update-payment-terms').ajaxForm(options_updt);
	
	$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
	
	$("#show_files").delegate("a.del_file","click",function() {
		var str_delete = $(this).attr("id");
		
		var result = confirm("Are you sure you want to delete this attachment?");
		if (result==true) {
			$('#'+str_delete).parent("div").remove();
		}
	});
	
});
function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;

	return true;
}
function showRequest2()
{
	$('#rec_paymentfadeout').hide();
	$('#sp_form_jobid').val(curr_job_id);
	$(".payment-terms-mini-view1").css("display","block");
	$(".payment-received-mini-view1").css("display","none");
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
	}
}
</script>
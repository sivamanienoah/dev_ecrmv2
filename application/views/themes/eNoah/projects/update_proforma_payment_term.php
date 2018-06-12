<!--form id="update-payment-terms"-->

<?php $attributes = array('id' => 'update-payment-terms','name' => 'update-payment-terms'); ?>
<?php echo form_open_multipart("project/set_pr_payment_terms/".$expect_id, $attributes); ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" id="filefolder_id" name="filefolder_id" value="<?php echo $ff_id; ?>">
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Payment Milestone *</td>
			<td>
				<?php if($invoice_status != 1) { ?>
					<input type="text" name="sp_date_1" id="sp_date_1" value= "<?php echo $project_milestone_name; ?>" class="textfield width200px" />
				<?php } else { ?>
					<input type="text" name="sp_date_1" id="sp_date_1" readonly value= "<?php echo $project_milestone_name; ?>" class="textfield width200px" />
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Milestone date *</td>
			<td><input type="text" data-calendar="true" name="pr_sp_date_2" id="pr_sp_date_2" value= "<?php echo $expected_date; ?>" class="textfield width200px pick-date" readonly /></td>
		</tr>
		<tr>
			<td>For the Month & Year *</td>
			<td><input type="text" data-calendar="false" class="textfield width200px" value= "<?php echo $month_year; ?>" name="pr_month_year" id="pr_month_year" readonly /></td>
		</tr>
		<tr>
			<td>Value *</td>
			<td>
				<?php if($invoice_status != 1) { ?>
				<?php ?>
					<input type="text" onkeypress="return isPaymentVal(event)" name="sp_date_3" id="sp_date_3" value= "<?php echo $project_milestone_amt; ?>" class="textfield width200px" />
				<span style="color:red;">(Numbers only)</span>
				<?php } else { ?>
					<input type="text" onkeypress="return isPaymentVal(event)" name="sp_date_3" id="sp_date_3" readonly value= "<?php echo $project_milestone_amt; ?>" class="textfield width200px" />
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Remarks </td>
			<td>
				<?php if($invoice_status != 1) { ?>
					<textarea name="payment_remark" id="payment_remark" class="textfield width200px" ><?php echo $payment_remark; ?></textarea>
				<?php } else { ?>
					<textarea name="payment_remark" readonly id="payment_remark" class="textfield width200px" ><?php echo $payment_remark; ?></textarea>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Attachment File </td>
			<td>
				<?php if($invoice_status!=1) { ?>
				<a title="Add Folder" href='javascript:void(0)' onclick="open_files('<?php echo $job_id; ?>','update'); return false;"><img src="assets/img/select_file.jpg" alt="Select Files" ></a>
				<?php } else { ?>
				<a title="Add Folder" class="readonly-status readonly-status img-opacity" href='javascript:void(0)'><img src="assets/img/select_file.jpg" alt="Select Files" ></a>
				<?php }  ?>
				<div id="show_files">
					<?php
						foreach($attached_file as $att_file){
					?>
							<div style="float: left; width: 100%;">
								<input type="hidden" value="<?php echo $att_file['file_id']; ?>" name="file_id[]" />
								<span style="float: left;"><a onclick="download_files('<?php echo $job_id; ?>','<?php echo $att_file['lead_files_name']; ?>'); return false;"><?php echo $att_file['lead_files_name']; ?></a></span>
								<?php if($invoice_status!=1) { ?>
								<a class="del_file" id="<?php echo $att_file['file_id']; ?>"> </a>
								<?php } ?>
							</div>
					<?php
						}
					?>
				</div>
				<div id="add_newfile"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<div id="uploadFile"></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				
				<div class="buttons">
				<?php if($invoice_status != 1) { ?>
					<button type="submit" class="positive">Update Payment Terms</button>
				<?php  } ?>
					<button onclick="location.href='<?php echo base_url();?>project/view_project/<?php echo $job_id; ?>'; return false;" class="negative">Cancel</button>
				</div>

				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="<?php echo $job_id; ?>" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
			</td>
		</tr>
	</table>
</form>

<script>
var showscript = 1;
<?php if($invoice_status == 1) { ?>
var showscript = 0;
<?php } ?>
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
	if(showscript == 1) {
		$('#pr_sp_date_2').datepicker({
			dateFormat: 'dd-mm-yy', 
			//minDate: '0',
			beforeShow : function(input, inst) {
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
		$("#show_files").delegate("a.del_file","click",function() {
			var str_delete = $(this).attr("id");
			
			var result = confirm("Are you sure you want to delete this attachment?");
			if (result==true) {
				$('#'+str_delete).parent("div").remove();
			}
		});
		$("#show_files").delegate("a.del_file","click",function() {
			var str_delete = $(this).attr("id");
			
			var result = confirm("Are you sure you want to delete this attachment?");
			if (result==true) {
				$('#'+str_delete).parent("div").remove();
			}
		});
		$('#pr_month_year').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'MM yy',
			showButtonPanel: true,
			onClose: function(input, inst) {
				var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			},
			beforeShow: function(input, inst) {
				if ((selDate = $(this).val()).length > 0) 
				{
					iYear = selDate.substring(selDate.length - 4, selDate.length);
					iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
					$(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
					$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
				}
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
	}
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
	var pdate2 = $.trim($('#pr_sp_date_2').val());	

	if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#pr_sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
		date_entered = false;
	}
	if ($('#sp_form_jobid').val() == 0) { 
		errors.push('Invoice not properly loaded!');
	}
	if(($.trim($('#sp_date_1').val()) == '')) {
		errors.push('<p>Enter Payment Milestone Name.</p>');
	}
	if(($.trim($('#pr_sp_date_2').val()) == ''))  { //|| valid_date == false) {
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
function download_files(job_id,f_name){
	window.location.href = site_base_url+'/project/download_file/'+job_id+'/'+f_name;
}
</script>
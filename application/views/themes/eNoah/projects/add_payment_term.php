<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
</style>
<?php $attributes = array('id' => 'set-payment-terms','name' => 'set-payment-terms'); ?>
<?php echo form_open_multipart("project/set_payment_terms", $attributes); ?>			
<input type="hidden" id="filefolder_id" name="filefolder_id" value="<?php echo $ff_id; ?>">
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Payment Milestone *</td><td><input type="text" name="sp_date_1" id="sp_date_1" class="textfield width200px" /> </td>
		</tr>
		<tr>
			<td>Milestone date *</td><td><input type="text" name="sp_date_2" id="sp_date_2" data-calendar="true" class="textfield width200px pick-date" readonly /> </td>
		</tr>
		<tr>
			<td>For the Month & Year *</td>
			<td><input type="text" data-calendar="false" class="textfield width200px" name="month_year" id="month_year" readonly /></td>
		</tr>
		<tr>
			<td>Value *</td><td><input onkeypress="return isNumberKey(event)" type="text" name="sp_date_3" id="sp_date_3" class="textfield width200px" /> <span style="color:red;">(Numbers only)</span></td>
		</tr>
		<tr>
			<td>Remarks </td><td><textarea name="payment_remark" id="payment_remark" class="textfield width200px" ></textarea> </td>
		</tr>
		<tr>
			<td>Attachment File </td>
			<td>
				<a title="Add Folder" href='javascript:void(0)' onclick="open_files(<?php echo $jobid; ?>,'set'); return false;"><img src="assets/img/select_file.jpg" alt="Select Files" ></a>
				<div id="show_files"></div>
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
				<?php //if ($readonly_status == false) { ?>
				<div class="buttons">
					<!--button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button-->
					<button type="submit" class="positive">Add Payment Terms</button>
				</div>
				<?php //} ?>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="<?php echo $jobid; ?>" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
			</td>
		</tr>
	</table>
</form>
<script>

$( document ).ajaxSuccess(function( event, xhr, settings ) {
	if(settings.target=="#output1") {
		$('.payment-profile-view:visible').slideUp(400);
		$('.payment-terms-mini-view1').html(xhr.responseText);
		$('#set-payment-terms')[0].reset();
		$('#show_files').empty();
		$('.payment-terms-mini-view1').css('display', 'block');
	}
});

$(function(){
	var options = { 
		target:      '#output1',   // target element(s) to be updated with server response 
		beforeSubmit: showRequest, // pre-submit callback 
		success:      ''  // post-submit callback 
	}; 
	$('#set-payment-terms').ajaxForm(options);

	$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
	
	$('#month_year').datepicker({
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
function showRequest()
{
	var date_entered = true;
	var errors = [];
	if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
		date_entered = false;
	}
	if(($.trim($('#sp_date_1').val()) == '')) {
		errors.push('<p>Enter Payment Milestone Name.</p>');
	}
	if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if(($.trim($('#sp_date_3').val()) == '')) {
		errors.push('<p>Enter Milestone Value.</p>');
	}
	if (errors.length > 0) {
		setTimeout('timerfadeout()', 8000);
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		return false;
	}
}
function timerfadeout() {
	$('.dialog-err').empty();
}
</script>
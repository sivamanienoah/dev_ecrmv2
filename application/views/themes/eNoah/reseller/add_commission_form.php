<style>
.hide-calendar .ui-datepicker-calendar { display: none !important; }
</style>
<?php $attributes = array('id'=>'add_commission', 'name'=>'add_commission'); ?>
<?php echo form_open_multipart("reseller/addResellerCommission", $attributes); ?>
<?php #echo "<pre>"; print_r($contracts_det['currency']); echo "</pre>"; ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="contracter_id" id="contracter_id" value="<?php echo $reseller_det[0]['userid']; ?>" readonly />
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Title<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_title" id="commission_title" class="textfield width200px" maxlength="50" />
				<div class='ajx_failure_msg succ_err_msg' id='commission_title_err'></div>
			</td>
		</tr>
		<tr>
			<td>Projects<span class='red'> *</span></td>
			<td>
				<select name='job_id' class="textfield width200px" id='job_id'>
					<option value=''>Select</option>
					<?php if(isset($reseller_projects) && !empty($reseller_projects) && count($reseller_projects)>0) { ?>
						<?php foreach($reseller_projects as $job_rec) { ?>
							<option value=<?php echo $job_rec['lead_id']; ?>><?php echo $job_rec['lead_title']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='job_id_err'></div>
			</td>
		</tr>
		<tr>
			<td>Payment Advice Date<span class='red'> *</span></td>
			<td>
				<input type="text" name="payment_advice_date" id="payment_advice_date" data-calendar="true" class="textfield width200px" readonly />
				<div class="ajx_failure_msg succ_err_msg" id="payment_advice_date_err"></div>
			</td>
		</tr>
		<tr>
			<td>Milestone Name<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_milestone_name" id="commission_milestone_name" class="textfield width200px" maxlength="150" />
				<div class="ajx_failure_msg succ_err_msg" id="commission_milestone_name_err"></div>
			</td>
		</tr>
		<tr>
			<td>For The Month & Year<span class='red'> *</span></td>
			<td>
				<input type="text" name="for_the_month_year" id="for_the_month_year" data-calendar="false" class="textfield width200px" readonly />
				<div class="ajx_failure_msg succ_err_msg" id="for_the_month_year_err"></div>
			</td>
		</tr>
		<tr>
			<td>Currency<span class='red'> *</span></td>
			<td>
				<select name='commission_currency' class="textfield width200px" id='commission_currency'>
					<option value=''>Select</option>
					<?php if(!empty($currencies) && count($currencies)>0) { ?>
						<?php foreach($currencies as $cur_rec) { ?>
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($contracts_det['currency']==$cur_rec['expect_worth_id']) { echo "selected='selected'"; }?>><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='commission_currency_err'></div>
			</td>
		</tr>
		<tr>
			<td>Commission Value<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_value" id="commission_value" class="textfield width200px" maxlength="10" />
				<div class="ajx_failure_msg succ_err_msg" id="commission_value_err"></div>
			</td>
		</tr>
		<tr>
			<td>Remarks</td>
			<td>
				<textarea name="remarks" id="remarks" class="textfield width200px"></textarea>
			</td>
		</tr>
		<tr>
			<td>Attachment Document</td>
			<td> <!--multiple--->
				<form name="payment_ajax_file_upload">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div id="upload-container">
						<input type="file" multiple title='upload' class="textfield" id="attachment_document" name="attachment_document[]" onchange="return runCommissionAjaxFileUpload();" />
						<input type="hidden" id="exp_type" value="">									
					</div>
				</form>	
				<div id="commissionUploadFile"></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php //if ($readonly_status == false) { ?>
				<div class="buttons">
					<button type="submit" class="positive">Add</button>
					<button onclick="reset_commission_form(); return false;" class="negative">Cancel</button>
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
				$('#succes_add_commission_data').html("<span class='ajx_success_msg'>Commission Added Successfully.</span>");
				reset_commission_form();
			} else if(xhr.responseText == 'error') {
				$('#succes_add_commission_data').html("<span class='ajx_failure_msg'>Error in Adding Commission.</span>");
			}
			$('#succes_add_commission_data').show();
			setTimeout('timerfadeout()', 4000);
		}
	});

	$(function(){
		var options = {
			target:      '#output1',   // target element(s) to be updated with server response 
			beforeSubmit: validateCommissionForm, // pre-submit callback 
			success:      ''  // post-submit callback 
		}; 
		$('#add_commission').ajaxForm(options);
		
		$('#payment_advice_date').datepicker({
			dateFormat: 'dd-mm-yy', 
			// maxDate: '0',
			beforeShow : function(input, inst) {
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
		
		$('#for_the_month_year').datepicker({
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
		
	});
	
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode;
		if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

		return true;
	}
	
//validate the form
function validateCommissionForm()
{
	// alert('validateCommissionForm');
	var errors = [];
	var validate_form = true;
	
	if(($.trim($('#commission_title').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Title.</p>');
		$('#commission_title_err').html('Enter Title.');
	} else {
		$('#commission_title_err').html('');
	}
	if(($.trim($('#job_id').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Select Project.</p>');
		$('#job_id_err').html('Select Project.');
	} else {
		$('#job_id_err').html('');
	}
	if(($.trim($('#payment_advice_date').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Payment Advice Date.</p>');
		$('#payment_advice_date_err').html('Enter Payment Advice Date.');
	} else {
		$('#payment_advice_date_err').html('');
	}
	if(($.trim($('#commission_milestone_name').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Milestone Name.</p>');
		$('#commission_milestone_name_err').html('Enter Milestone Name.');
	} else {
		$('#commission_milestone_name_err').html('');
	}
	if(($.trim($('#for_the_month_year').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter For the Month & Year.</p>');
		$('#for_the_month_year_err').html('Enter For the Month & Year.');
	} else {
		$('#for_the_month_year_err').html('');
	}
	if(($.trim($('#commission_currency').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Select Currency.</p>');
		$('#commission_currency_err').html('Select Currency.');
	} else {
		$('#commission_currency_err').html('');
	}
	if(($.trim($('#commission_value').val()) == '')) 
	{
		validate_form = false;
		errors.push('<p>Enter Value.</p>');
		$('#commission_value_err').html('Enter Value.');
	} else {
		$('#commission_value_err').html('');
	}
	if (errors.length > 0 && validate_form == false) 
	{
		setTimeout('timerfadeout()', 6000);
		// $('#succes_add_commission_data').html(errors.join(''));
		// $('#succes_add_commission_data').show();
		return false;
	}
}

function runCommissionAjaxFileUpload() 
{
	var _uid				 = new Date().getTime();
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;

	$.ajaxFileUpload({
		url: 'reseller/commissionFileUpload/'+contracter_user_id,
		secureuri: false,
		fileElementId: 'attachment_document',
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
							$("#commissionUploadFile").append(name);
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
	$('#attachment_document').val('');
	return false;
}

$("#commissionUploadFile").delegate("a.del_file","click",function() {
	// var str_delete 	= $(this).attr("id");
	var str_delete 	= $(this).attr("serial_id");
	var result 		= confirm("Are you sure you want to delete this attachment?");
	if (result==true) {
		// $('#'+str_delete).parent("div").remove();
		$('a[serial_id="'+str_delete+'"]').parent("div").remove();
	}
});
</script>
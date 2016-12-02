<?php $attributes = array('id' => 'edit-other-cost','name' => 'edit-other-cost'); ?>
<?php echo form_open_multipart("project/editOtherCost/", $attributes); ?>
	<input type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
	<input type="hidden" id="cost_id" name="cost_id" value="<?php echo $cost_data['id']; ?>">
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Description *</td>
			<td><input type="text" name="description" id="description" maxlength="400" class="textfield width200px" value="<?php echo $cost_data['description']; ?>" /></td>
		</tr>
		<tr>
			<td>Cost Incurred date *</td>
			<?php
			if(!empty($cost_data['cost_incurred_date'])){
				$cost_incurred_date = date('d-m-Y', strtotime($cost_data['cost_incurred_date']));
			}
			?>
			<td><input type="text" name="cost_incurred_date" id="cost_incurred_date" data-calendar="true" class="textfield width200px pick-date" readonly value="<?php echo $cost_incurred_date; ?>" /> </td>
		</tr>
		<tr>
			<td>Currency Type *</td>
			<td>
				<select name='currency_type' class="textfield width200px" id='currency_type'>
					<option value=''>Select</option>
					<?php if(!empty($currencies) && count($currencies)>0) { ?>
						<?php foreach($currencies as $cur_rec) { ?>
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($cost_data['currency_type'] == $cur_rec['expect_worth_id']) echo "selected='selected'"; ?> ><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Value *</td>
			<td>
				<input onkeypress="return isNumberKey(event)" type="text" name="value" id="value" maxlength="7" value="<?php echo $cost_data['value']; ?>" class="textfield width200px"/> 
				<span style="color:red;">(Numbers only)</span>
			</td>
		</tr>
		<tr>
			<td>Attachment File </td>
			<td>
				<a title="Add Files" href='javascript:void(0)' onclick="open_files_othercost(<?php echo $project_id; ?>,'set'); return false;"><img src="assets/img/select_file.jpg" alt="Select Files" ></a>
				<div id="oc_show_files">
				<?php
					foreach($attached_file as $att_file){
				?>
						<div style="float: left; width: 100%;">
							<input type="hidden" value="<?php echo $att_file['file_id']; ?>" name="file_id[]" />
							<span style="float: left;"><a onclick="download_files('<?php echo $project_id; ?>','<?php echo $att_file['lead_files_name']; ?>'); return false;"><?php echo $att_file['lead_files_name']; ?></a></span>
							<?php if($invoice_status!=1) { ?>
							<a class="del_oc_file" id="<?php echo $att_file['file_id']; ?>"> </a>
							<?php } ?>
						</div>
				<?php
					}
				?>
				</div>
				<div id="oc_add_newfile"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><div id="uploadOcFile"></div></td>
		</tr>
		<tr>
			<td colspan='2'>
				<div class="buttons">
					<button type="submit" class="positive">Edit</button>
					<button onclick="reset_editcostdata(); return false;" class="negative">Cancel</button>
				</div>
			</td>
		</tr>
	</table>
</form>
<script>
var project_id = '<?php echo $project_id ?>';
$(function(){
	var options = {
		target:      '#edit_costdata',   // target element(s) to be updated with server response 
		beforeSubmit: editValidateForm, // pre-submit callback 
		success:      ''  // post-submit callback 
	}; 
	$('#edit-other-cost').ajaxForm(options);
	$("#cost_incurred_date").datepicker({dateFormat: "dd-mm-yy"});
});

$( document ).ajaxSuccess(function( event, xhr, settings ) {
	if(settings.target=="#edit_costdata") {
		// console.info(xhr.responseText);
		if(xhr.responseText=='success') {
			$('#succes_other_cost_data').html("<span class='ajx_success_msg'>Other Cost Updated Successfully.</span>");
			reset_editcostdata();
		} else if(xhr.responseText == 'error') {
			$('#succes_other_cost_data').html("<span class='ajx_failure_msg'>Error in updating the other cost.</span>");
		}
		$('#succes_other_cost_data').show();		
		setTimeout('timerfadeout()', 4000);
	}
});
//validate the form
function editValidateForm()
{
	var date_entered = true;
	var errors = [];
	if(($.trim($('#description').val()) == '')) 
	{
		date_entered = false;
		errors.push('<p>Enter Description.</p>');
	}
	if(($.trim($('#cost_incurred_date').val()) == '')) 
	{
		date_entered = false;
		errors.push('<p>Enter Date.</p>');
	}
	if(($.trim($('#currency_type').val()) == '')) 
	{
		date_entered = false;
		errors.push('<p>Select Currency Type.</p>');
	}
	if(($.trim($('#value').val()) == '')) 
	{
		date_entered = false;
		errors.push('<p>Enter Value.</p>');
	}
	if (errors.length > 0) 
	{
		setTimeout('timerfadeout()', 2000);
		$('#err_other_cost_data').show();
		$('#err_other_cost_data').html(errors.join(''));
		return false;
	}
}

function reset_editcostdata()
{
	// alert('tst');
	viewOtherCost(project_id);
}
</script>
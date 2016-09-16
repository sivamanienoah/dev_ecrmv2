<div id="other_cost_form">
	<?php $attributes = array('id' => 'add-other-cost','name' => 'add-other-cost'); ?>
	<?php echo form_open_multipart("project/addOtherCost", $attributes); ?>			
		<input type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
		<table class="payment-table" style="margin: 10px 0px;">
			<tr>
				<td>Description *</td>
				<td><input type="text" name="description" id="description" maxlength="400" class="textfield width200px" /></td>
			</tr>
			<tr>
				<td>Cost Incurred date *</td>
				<td><input type="text" name="cost_incurred_date" id="cost_incurred_date" data-calendar="true" class="textfield width200px pick-date" readonly /> </td>
			</tr>
			<tr>
				<td>Currency Type *</td>
				<td>
					<select name='currency_type' class="textfield width200px" id='currency_type'>
						<option value=''>Select</option>
						<?php if(!empty($currencies) && count($currencies)>0) { ?>
							<?php foreach($currencies as $cur_rec) { ?>
								<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($base_currency == $cur_rec['expect_worth_id']) echo "selected='selected'"; ?>><?php echo $cur_rec['expect_worth_name']; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Value *</td>
				<td>
					<input onkeypress="return isNumberKey(event)" type="text" name="value" id="value" maxlength="7" class="textfield width200px"/> 
					<span style="color:red;">(Numbers only)</span>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<?php //if ($readonly_status == false) { ?>
					<div class="buttons">
						<button type="submit" class="positive">Add</button>
					</div>
					<?php //} ?>
				</td>
			</tr>
		</table>
	</form>
</div>

<div id="list_other_cost">
	<table class="data-table" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr align="left">
				<th class="header">Description</th>
				<th class="header">Cost Incurred Date</th>
				<th class="header">Value</th>
				<th class="header">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($other_cost_data) && count($other_cost_data)>0) { ?>
				<?php foreach($other_cost_data as $row) { ?>
					<tr id="cost_<?php echo $row['id']; ?>">
						<td align="left"><?php echo $row['description']; ?></td>
						<td align="left"><?php echo ($row['cost_incurred_date']!='0000-00-00 00:00:00') ? date('d-m-Y', strtotime($row['cost_incurred_date'])) : '';?></td>
						<td align="right"><?php echo $currency_arr[$row['currency_type']].' '.number_format($row['value'], 2, '.', ',');?></td>
						<td align="left">
							<a title="Edit" onclick="editOtherCostData(<?php echo $row['id']; ?>, <?php echo $project_id?>); return false;"><img src="assets/img/edit.png" alt="edit"> </a>
							<a title="Delete" onclick="deleteOtherCostData(<?php echo $row['id']; ?>, <?php echo $project_id?>); return false;"><img src="assets/img/trash.png" alt="delete"></a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
<script>
var project_id = '<?php echo $project_id ?>';
	$( document ).ajaxSuccess(function( event, xhr, settings ) {
		if(settings.target=="#output1") {
			/* $('.payment-profile-view:visible').slideUp(400);
			$('.payment-terms-mini-view1').html(xhr.responseText);
			$('#set-payment-terms')[0].reset();
			$('#show_files').empty();
			$('.payment-terms-mini-view1').css('display', 'block'); */
			if(xhr.responseText=='success') {
				$('#add-other-cost')[0].reset();
				$('#succes_other_cost_data').html("<span class='ajx_success_msg'>Other Cost Added Successfully.</span>");
			} else if(xhr.responseText == 'error') {
				$('#succes_other_cost_data').html("<span class='ajx_failure_msg'>Error in adding other cost.</span>");
			}
			$('#succes_other_cost_data').show();
			loadOtherCostGrid(project_id);
			setTimeout('timerfadeout()', 4000);
		}
	});

	$(function(){
		var options = {
			target:      '#output1',   // target element(s) to be updated with server response 
			beforeSubmit: validateForm, // pre-submit callback 
			success:      ''  // post-submit callback 
		}; 
		$('#add-other-cost').ajaxForm(options);

		$("#cost_incurred_date").datepicker({dateFormat: "dd-mm-yy"});
	});
	
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode;
		if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

		return true;
	}
	
//validate the form
function validateForm()
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

/*load the other cost grid*/
function loadOtherCostGrid(project_id) 
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'project/getOtherCostData/'+project_id+'/'+true,
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
		},
		success:function(data) {
			// console.info(data);
			$('#list_other_cost').html(data);
		}
	});
}
	
//editing the other cost data
function editOtherCostData(costid, projectid)
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	params['costid'] 		= costid;
	params['projectid'] 	= projectid;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'project/editOtherCostData/',
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
		},
		success:function(data) {
			// console.info(data);
			$('#other_cost_form').html(data);
		}
	});
}

//deleting the other cost data
function deleteOtherCostData(costid, projectid)
{
	
	var agree=confirm("Are you sure you want to delete?");
	if (agree) {
		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		params['costid'] 		= costid;
		params['projectid'] 	= projectid;
		$.ajax({
			type:'POST',
			data:params,
			url:site_base_url+'project/deleteOtherCostData/',
			cache:false,
			dataType:'json',
			beforeSend: function() {
				//show loading symbol
			},
			success:function(data) {
				if(data.res == 'success'){
					$('#succes_other_cost_data').html("<span class='ajx_success_msg'>Deleted Successfully.</span>");
					$('#cost_'+costid).remove();
					setTimeout('timerfadeout()', 4000);
				} else if(data.res == 'failure'){
					$('#succes_other_cost_data').html("<span class='ajx_failure_msg'>Error in deleting other cost.</span>");
				}
			}
		});
	} else {
		return false;
	}
	
	
}
</script>
<?php $attributes = array('id' => 'edit-other-cost','name' => 'edit-other-cost'); ?>
<?php echo form_open_multipart("project/editOtherCost/", $attributes); ?>			
	<input type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Description *</td>
			<td><input type="text" name="description" id="description" maxlength="400" class="textfield width200px" value="" /></td>
		</tr>
		<tr>
			<td>Cost Incurred date *</td>
			<td><input type="text" name="cost_incurred_date" id="cost_incurred_date" data-calendar="true" class="textfield width200px pick-date" readonly value="" /> </td>
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
				<input onkeypress="return isNumberKey(event)" type="text" name="value" id="value" maxlength="7" value="<?php echo ?>" class="textfield width200px"/> 
				<span style="color:red;">(Numbers only)</span>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div class="buttons">
					<button type="submit" class="positive">Edit</button>
					<button class="negative">Cancel</button>
				</div>
			</td>
		</tr>
	</table>
</form>
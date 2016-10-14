<?php $attributes = array('id'=>'view_commission', 'name'=>'view_commission'); ?>
<?php echo form_open_multipart("reseller/editResellerCommission", $attributes); ?>
<?php #echo "<pre>"; print_r($contracts_det['currency']); echo "</pre>"; ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="contracter_id" id="contracter_id" value="<?php echo $commission_data['contracter_id']; ?>" readonly />
	<input type="hidden" name="commission_id" id="commission_id" value="<?php echo $commission_data['id']; ?>" readonly />
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Title<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_title" id="commission_title" class="textfield width200px" value="<?php echo $commission_data['commission_title'] ?>" maxlength="50" />
				<div class='ajx_failure_msg succ_err_msg' id='commission_title_err'></div>
			</td>
		</tr>
		<tr>
			<td>Projects<span class='red'> *</span></td>
			<td>
				<select name='job_id' class="textfield width200px" id='job_id' disabled>
					<option value=''>Select</option>
					<?php if(isset($reseller_projects) && !empty($reseller_projects) && count($reseller_projects)>0) { ?>
						<?php foreach($reseller_projects as $job_rec) { ?>
							<option value=<?php echo $job_rec['lead_id']; ?> <?php if($commission_data['job_id'] == $job_rec['lead_id']) echo "selected='selected'"; ?> ><?php echo $job_rec['lead_title']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='job_id_err'></div>
			</td>
		</tr>
		<tr>
			<td>Contract<span class='red'> *</span></td>
			<td>
				<select name="contract_id" class="textfield width200px" id="contract_id" disabled>
					<option value=''>Select</option>
					<?php if(isset($active_contracts) && !empty($active_contracts) && count($active_contracts)>0) { ?>
						<?php foreach($active_contracts as $con_rec) { ?>
							<option value=<?php echo $con_rec['id']; ?> <?php if($commission_data['contract_id'] == $con_rec['id']) { echo "selected='selected'"; } ?>><?php echo $con_rec['contract_title']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class="ajx_failure_msg succ_err_msg clear" id="contract_id_err"></div>
				<input type="hidden" name="hidden_contract_title" id="hidden_contract_title" value="" readonly />
			</td>
		</tr>
		<tr>
			<td>Payment Advice Date<span class='red'> *</span></td>
			<td>
				<?php
					$payment_advice_date = (!empty($commission_data['payment_advice_date']) && ($commission_data['payment_advice_date'] != '0000-00-00 00:00:00')) ? date('d-m-Y', strtotime($commission_data['payment_advice_date'])) : '';
				?>
				<input type="text" name="payment_advice_date" id="payment_advice_date" data-calendar="true" class="textfield width200px" value="<?php echo $payment_advice_date; ?>" readonly />
				<div class="ajx_failure_msg succ_err_msg" id="payment_advice_date_err"></div>
			</td>
		</tr>
		<tr>
			<td>Milestone Name<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_milestone_name" id="commission_milestone_name" class="textfield width200px" value="<?php echo $commission_data['commission_milestone_name']; ?>" maxlength="150" />
				<div class="ajx_failure_msg succ_err_msg" id="commission_milestone_name_err"></div>
			</td>
		</tr>
		<tr>
			<td>For The Month & Year<span class='red'> *</span></td>
			<td>
				<?php
					$for_the_month_year = (!empty($commission_data['payment_advice_date']) && ($commission_data['payment_advice_date'] != '0000-00-00 00:00:00')) ? date('F Y', strtotime($commission_data['payment_advice_date'])) : '';
				?>
				<input type="text" name="for_the_month_year" id="for_the_month_year" data-calendar="false" class="textfield width200px" value="<?php echo $for_the_month_year; ?>" readonly />
				<div class="ajx_failure_msg succ_err_msg" id="for_the_month_year_err"></div>
			</td>
		</tr>
		<tr class="set_cont">
			<td>Currency<span class='red'> *</span></td>
			<td>
				<select name="hidden_commission_currency" class="textfield width200px" disabled id="hidden_commission_currency">
					<option value=''>Select</option>
					<?php if(!empty($currencies) && count($currencies)>0) { ?>
						<?php foreach($currencies as $cur_rec) { ?>
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($commission_data['commission_currency']==$cur_rec['expect_worth_id']) { echo "selected='selected'"; }?>><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class="ajx_failure_msg succ_err_msg clear" id='hidden_commission_currency_err'></div>
				<input type="hidden" name="commission_currency" id="commission_currency" class="textfield width200px" maxlength="10" value="<?php echo $commission_data['commission_currency']; ?>" readonly />
			</td>
		</tr>
		<tr class="set_cont">
			<td>Tax %<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_tax" id="commission_tax" class="textfield width200px" value="<?php echo $commission_data['commission_tax']; ?>" maxlength="10" readonly />
				<div class="ajx_failure_msg succ_err_msg" id="commission_tax_err"></div>
			</td>
		</tr>
		<tr>
			<td>Currency<span class='red'> *</span></td>
			<td>
				<select name='commission_currency' class="textfield width200px" id='commission_currency' disabled>
					<option value=''>Select</option>
					<?php if(!empty($currencies) && count($currencies)>0) { ?>
						<?php foreach($currencies as $cur_rec) { ?>
							<option value=<?php echo $cur_rec['expect_worth_id']; ?> <?php if($commission_data['commission_currency']==$cur_rec['expect_worth_id']) { echo "selected='selected'"; }?>><?php echo $cur_rec['expect_worth_name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<div class='ajx_failure_msg succ_err_msg clear' id='commission_currency_err'></div>
			</td>
		</tr>
		<tr>
			<td>Commission Value<span class='red'> *</span></td>
			<td>
				<input type="text" name="commission_value" id="commission_value" class="textfield width200px" value="<?php echo $commission_data['commission_value']; ?>" maxlength="10" />
				<div class="ajx_failure_msg succ_err_msg" id="commission_value_err"></div>
			</td>
		</tr>
		<tr>
			<td>Remarks</td>
			<td>
				<textarea name="remarks" id="remarks" class="textfield width200px"><?php echo $commission_data['remarks']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>Attachment Document</td>
			<td> <!--multiple--->
				<form name="payment_ajax_file_upload">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div id="upload-container">
						<input type="hidden" id="exp_type" value="">									
					</div>
				</form>
				<div id='existUploadedFile'>
					<?php if(is_array($upload_data) && !empty($upload_data) && count($upload_data)>0) { ?>
						<?php $serial_id = 1; ?>
						<?php foreach($upload_data as $rec_file) { ?>
							<div style="float: left; width: 100%; margin-top: 5px;">
								<span style="float: left;">
									<?php $file_id = base64_encode($rec_file['id']); ?>
									<?php #$file_id = $rec_file['id']; ?>
									<a onclick="download_commission_files('<?php echo $file_id; ?>'); return false;"><?php echo $rec_file['file_name']; ?></a>
								</span>
							</div>
						<?php $serial_id++; ?>
						<?php } ?>
					<?php } ?>
				</div>
				<div id="commissionUploadFile"></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<?php //if ($readonly_status == false) { ?>
				<div class="buttons">
					<button onclick="reset_commission_form(); return false;" class="negative">Cancel</button>
				</div>
				<?php //} ?>
			</td>
		</tr>
	</table>
<?php form_close(); ?>
<script type="text/javascript">
var contracter_user_id  = '<?php echo $commission_data['contracter_id']; ?>';
var commission_id 		= '<?php echo $commission_data['id']; ?>';
$('#view_commission :input').attr('readonly','readonly');
function download_commission_files(file_id)
{
	var url  = site_base_url+'reseller/downloadCommissionFile';
	var form = $('<form action="' + url + '" method="post">' +
	'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	'<input type="hidden" name="file_id" value="' +file_id+ '" />' +
	'</form>');
	$('body').append(form);
	$(form).submit();
	// window.location.href = site_base_url+'reseller/download_file/'+file_id;
}
</script>
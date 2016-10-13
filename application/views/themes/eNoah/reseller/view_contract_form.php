<?php $attributes = array('id'=>'view-contract', 'name'=>'view-contract'); ?>
<?php #echo "<pre>"; print_r($upload_data); echo "</pre>"; ?>
<?php echo form_open_multipart("reseller/viewResellerContract", $attributes); ?>
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="contracter_id" id="contracter_id" value="<?php echo $contract_data['contracter_id']; ?>" readonly />
	<input type="hidden" name="contract_id" id="contract_id" value="<?php echo $contract_data['id']; ?>" readonly />
	<input type="hidden" name="hidden_contract_manager" id="hidden_contract_manager" value="<?php echo $contract_data['contract_manager']; ?>" readonly />
	<table class="payment-table" style="margin: 10px 0px;">
		<tr>
			<td>Contract Manager<span class='red'> *</span></td>
			<td>
				<select name='contract_manager' class="textfield width200px" id='contract_manager' disabled>
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
				<select name='contract_status' class="textfield width200px" id='contract_status' disabled>
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
				<select name='currency' class="textfield width200px" id='currency' disabled>
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
									<a onclick="download_files('<?php echo $file_id; ?>'); return false;"><?php echo $rec_file['file_name']; ?></a>
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
$('#view-contract :input').attr('readonly','readonly');
function download_files(file_id)
{
	var url  = site_base_url+'reseller/download_file';
	var form = $('<form action="' + url + '" method="post">' +
	'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	'<input type="hidden" name="file_id" value="' +file_id+ '" />' +
	'</form>');
	$('body').append(form);
	$(form).submit();
	// window.location.href = site_base_url+'reseller/download_file/'+file_id;
}
</script>
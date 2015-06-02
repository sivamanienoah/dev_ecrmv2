<?php require (theme_url().'/tpl/header.php'); 
echo '<pre>';print_r($invoice); echo '</pre>';
echo '<pre>';print_r($exp); echo '</pre>';
?>
<div id="content">
	<div class="inner login-inner">
		<div class="login-container" style="width:800px;">
			<div class="login-box">
				<?php if ($this->session->userdata('error_message')) { ?>
					<h2><?php echo $this->session->userdata('error_message');  $this->session->set_userdata('error_message','');?></h2>
				<?php } else { ?>
				<h2>Payment</h2>
				<form action="<?php echo site_url('payment/process_payment');?>" method="post">
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						 <?php if(count($exp_details)>0 && !empty($exp_details)){
								$total = 0;?>
								<table cellspacing="0" cellpadding="0" border="0" class="data-table">
									<tr>
										<th>Project Name</th>
										<th>Milestone Name</th>
										<th>Price</th>
										<th>Tax(%)</th>
										<th>Tax Price</th>
										<th>Total</th>
										<th>Attachment</th>
									</tr>
									<?php foreach($exp_details as $exp){ 
									//echo '<pre>';print_r($exp);
									$total += $exp->total_amount;?>
									<tr>
										<td><?php echo $exp->lead_title; ?></td>
										<td><?php echo $exp->project_milestone_name; ?></td>
										<td><?php echo $exp->expect_worth_name.' '.number_format($exp->amount,2); ?></td>
										<td><?php echo $exp->tax; ?></td>
										<td><?php echo $exp->expect_worth_name.' '.number_format($exp->tax_price,2); ?></td>
										<td><?php echo $exp->expect_worth_name.' '.number_format($exp->total_amount,2); ?></td>
										<td>
										<?php $qry = $this->db->get_where($this->cfg['dbpref']."expected_payments_attachments",array("expectid" => $exp->expectid));
											$res = $qry->result();
											if($qry->num_rows()>0){
												foreach($res as $rs){
													echo anchor(site_url("assets/invoices/".$rs->file_name),$rs->file_name,'target="_blank"').'<br>';
												}
											}
										?>
										</td>
									</tr>
								<?php }?>
								</table>
							<?php  }?>
						<label>Total Amount:</label>
						<?php echo $exp->expect_worth_name.' '.number_format($total,2);?> 
						<input type="hidden" name="total_amount" value="<?php echo $total;?>" />
						<input type="hidden" name="currency_type" value="<?php echo $exp->expect_worth_name;?>" />
						<input type="hidden" name="custid_fk" value="<?php echo $invoice->cust_id;?>" />
						<input type="hidden" name="inv_id" value="<?php echo $invoice->inv_id;?>" />
						<input type="hidden" name="unique_link" value="<?php echo $invoice->unique_link;?>" />
						<br><br>
						<label>Payment By:</label>
						<input type="radio" name="payment_method" value="1"/>&nbsp;Paypal
						<input type="radio" name="payment_method" value="2"/>&nbsp;Authorize.net
						<br><br>

						<label>Card Type:</label>
						<select name="card_type" class="form-control input-md selectpicker show-tick cc_input" for="Card Type" id="card_type" placeholder="Card Type">
								<option value="">Select One</option>
								<?php
								$card_type = array("Visa"=>"Visa","MasterCard"=>"MasterCard");
								//,"Discover"=>"Discover", "Amex"=>"Amex"
								foreach($card_type as $key=>$val)
								{
								?>
								<option value="<?php echo $key; ?>" <?php if(isset($_POST['card_type']) && $_POST['card_type']==$key) echo 'selected="selected"'; ?>><?php echo $val; ?> </option>
								<?php } ?>
						</select>
						<br><br>						
						<label>Card Number:</label>
						<input type="text" placeholder="Card Number" id="card_number" class="form-control number cc_input length_check" name="card_number" value="" maxlength="16" />
						<br><br>						
						<label>Expiry Month & Year:</label>
						<select name="expiry_month" class="form-control input-md selectpicker show-tick cc_input" placeholder="Expiry Month" id="expiry_month">
							<option value="">Month</option>
							<?php
							for ($m=1; $m <= 12; $m++)
							{
								$mnth = ($m>=10) ? $m : '0'.$m;
							?>
							<option value="<?php echo $mnth; ?>" <?php if(isset($_POST['expiry_month']) && $_POST['expiry_month']==$mnth) echo 'selected="selected"'; ?>><?php echo $mnth; ?> </option>
							<?php } ?>
						</select>
						<select name="expiry_year" class="form-control input-md selectpicker show-tick cc_input" placeholder="Expiry Year" id="expiry_year">
							<option value="">Year</option>
							<?php
							$curYear = date('Y');
							for ($yr=0; $yr < 20; $yr++)
							{
								$newEndingDate = date("Y", strtotime(date("Y-m-d") . " + ".$yr." year"));
							?>
							<option value="<?php echo substr($newEndingDate,2,2); ?>" <?php if(isset($_POST['expiry_year']) && $_POST['expiry_year']==$mnth) echo 'selected="selected"'; ?>><?php echo $newEndingDate; ?> </option>
							<?php } ?>
						</select>
						<br><br>	
						<label>CVV:</label>
						<input type="password" placeholder="CVV" id="cvv" class="form-control number cc_input length_check" name="cvv" value="" maxlength="3" /> 
						<br><br>
						<button type="submit" class="positive">Submit</button>
				</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
window.onload = function() {
	document.forms[0].email.focus();
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>
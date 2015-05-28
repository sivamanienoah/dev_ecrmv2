<?php require (theme_url().'/tpl/header.php'); 
//echo '<pre>';print_r($invoice); echo '</pre>';
?>
<div id="content">
	<div class="inner login-inner">
		<div class="login-container" style="width:800px;">
			<div class="login-box">
				<?php if ($this->session->userdata('error_message')) { ?>
					<h2><?php echo $this->session->userdata('error_message');  $this->session->set_userdata('error_message','');?></h2>
				<?php } else { ?>
				<h2>Payment</h2>
				<form action="userlogin/process_login/" method="post">
					<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<label>Project Name:</label>
						<?php echo $invoice->lead_title;?> 
						<br><br>
						<label>Milestone Name:</label>
						<?php echo $invoice->project_milestone_name;?> 
						<br><br>
						<label>For the Month & Year:</label>
						<?php echo date("F Y",strtotime($invoice->month_year));?> 
						<br><br>
 					
						<label>Sub Total:</label>
						<?php echo $invoice->sub_total;?> 
						<br><br>
						<label>Tax (%):</label>
						<?php echo $invoice->tax;?> 
						<br><br>						
						<label>Tax Amount:</label>
						<?php echo $invoice->tax_amount;?> 
						<br><br>
						<label>Total Amount:</label>
						<?php echo $invoice->total_amount;?> 
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
							<option value="<?php echo $newEndingDate; ?>" <?php if(isset($_POST['expiry_year']) && $_POST['expiry_year']==$mnth) echo 'selected="selected"'; ?>><?php echo $newEndingDate; ?> </option>
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

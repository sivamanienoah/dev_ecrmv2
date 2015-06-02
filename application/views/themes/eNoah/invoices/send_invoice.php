<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo count($customers);exit;
//echo '<pre>';print_r($customers);exit;
?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<div id="content">
	<div class="inner">	
		 <div class="pull-left side1 test-block full-div"> 
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<div class="clear"></div>
			<div >
			<form action="<?php echo base_url().'invoice/submit_invoice'?>" method="post" enctype="multipart/form-data">
				<input type="hidden" id="csrf_hash_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type="hidden" name="customer_name" value="" class="customer_name" />
				<div class="pull-left">
					<label class="practices">Select Customer</label>
					<select class="js_customer" name="customer">
						<option value="">Select</option>
						<?php if(count($customers)>0 && !empty($customers)){
									foreach($customers as $customer){?>
										<option <?php echo ($customer->custid== $expresults->custid)?'selected="selected"':'';?> value="<?php echo $customer->custid;?>"><?php echo $customer->first_name.' '.$customer->last_name.' '.$customer->company;?></option>
						<?php }}?>
					</select>
				</div>
				<div class="clear"></div>
				<div class="pull-left js_leads_list" style="display:none;"></div>
				<div class="clear"></div>				
				<div class="pull-left">
					<label class="practices">Payment Options</label>
					 <?php if(count($payment_options)>0){ foreach($payment_options as $payment) { ?>
						 <input class="js_payment_options" checked="checked" type="checkbox" name="payment_options[]" value="<?php echo $payment->ptype_id;?>" />&nbsp;&nbsp;<?php echo $payment->ptype_name;?>&nbsp;&nbsp;
					 <?php }}?>
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Expiry Date</label>
					<input type="text" class="textfield width300px" name="expiry_date" id="expiry_date" value="<?php echo date("d-m-Y",strtotime(EXPIRYDAYS." days"))?>" />
				</div>	 
				 
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Email Address</label>
					<input type="text" name="email_address" class="email_address" value="" />
				</div>
				 
				<div class="clear"></div>				
				<div class="pull-left">
					<label class="practices"></label>
					<input onClick="return startQuote();" type="submit" name="submit" class="js_submit" value="Submit" />
				</div>					
			</form>	
			</div>
		</div>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript">

function startQuote() {
    var err = [];
	
    if ($('.js_customer').val() == '') {
        err.push('A valid customer needs to be selected');
    }
	if(!$('.js_invoice_checkbox').length){
		err.push('A valid customer with invoice needs to be selected');
	}
	
	if(!$('.js_invoice_checkbox').is(":checked")){
		err.push('Please select Invoice to send');
	}
	 
	if ($.trim($('#expiry_date').val()) == '') {
        err.push('Expiry Date is required');
    }
	if(!$('.js_payment_options').is(':checked')){
		err.push('Select any Payment Option');
	}
 
 
	if ($.trim($('.email_address').val()) == '') {
        err.push('Email Address is required');
    }else if($.trim($('.email_address').val())){
		var ems = $('.email_address').val();
		var emails = ems.split(",");
		var userinput = '';
		for(var c=0; c<emails.length; c++){
			userinput = emails[c];
			if(!validateEmail(userinput)){
				err.push('Invalid Email Address');
			}
		}
	}
	
    if (err.length > 0) {
        // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
		$.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">'+err.join('<br />')+'</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
    } else {
		return true;		
    } 
}

function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}

function cancelDel() {
    $.unblockUI();
}

$(document).ready(function(){
	
	/* resetting selected values */
	$(".js_customer").val("");
	$(".email_address").val("");
	
	$('#expiry_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true
	});		
	
	$(".js_customer").change(function(){
		var custid = $(this).val()
		var csrf_hash_token = $("#csrf_hash_token").val();
		if(custid){
			$.ajax({
			  url: '<?php echo base_url()?>invoice/get_customer_invoices/',
			  data: { custid: custid,csrf_token_name : csrf_hash_token},
			  success: function(data){
				if(data=='no_results'){
					alert("Invoice(s) not found for the selected customer!");
				}else{
					  $(".js_leads_list").css("display","block");
					  $(".js_leads_list").append(data);
				}
			  }
			});
			return false;
		}
	});
});
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
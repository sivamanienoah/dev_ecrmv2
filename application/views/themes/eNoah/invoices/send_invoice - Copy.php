<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo count($customers);exit;
//echo '<pre>';print_r($customers);exit;
?>
<div id="content">
	<div class="inner">	
		 <div class="pull-left side1 test-block full-div"> 
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<div class="clear"></div>
			<div >
			<form action="<?php echo base_url().'invoice/submit_invoice'?>" method="post" enctype="multipart/form-data">
				<input type="hidden" id="csrf_hash_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type="hidden" name="customer_name" value="<?php echo $expresults->amount;?>" class="js_sub_total" />
				<input type="hidden" name="customer_name" value="" class="customer_name" />
				<input type="hidden" name="currency_type" value="" class="currency_type" />
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
						 <input checked="checked" type="checkbox" name="payment_options[]" value="<?php echo $payment->ptype_id;?>" />&nbsp;&nbsp;<?php echo $payment->ptype_name;?>&nbsp;&nbsp;
					 <?php }}?>
				</div>
				<div class="clear"></div>	
				<div class="pull-left">
					<label class="practices">Tax</label>
					<input class="js_tax" type="text" name="tax" value="" />%
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Sub Total</label>
					<span class="js_expect_worth"></span><input readonly="readonly" type="text" name="sub_total" class="js_sub_total1" value="<?php echo number_format($expresults->amount,2,'.',',');?>" />
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Tax Price</label>
					<span class="js_expect_worth"></span><input readonly="readonly" type="text" name="tax_price" class="tax_price" value="0" />
				</div>
				<div class="clear"></div>				
				<div class="pull-left">
					<label class="practices">Total</label>
					<span class="js_expect_worth"></span><input type="text" name="total" class="total" readonly="readonly" value="<?php echo number_format($expresults->amount,2,'.',',');?>" />
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Email Address</label>
					<input type="text" name="email_address" class="email_address" value="" />
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Attachment 1</label>
					<input type="file" name="attachment[]" value="" />
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Attachment 2 </label>
					<input type="file" name="attachment[]"  value="" />
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Attachment 3</label>
					<input type="file" name="attachment[]"  value="" />
				</div>
				<div class="clear"></div>				
				<div class="pull-left">
					<label class="practices"></label>
					<input type="submit" name="submit" class="js_submit" value="Submit" />
				</div>					
			</form>	
			</div>
		</div>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<script type="text/javascript">
$(document).ready(function(){
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
					var inv = $.parseJSON(data);
					var html = '<table cellspacing="0" cellpadding="0" border="0" class="data-table"><thead><tr><th>Select Invoice</th><th>Project Name</th><th>Payment Milestone</th><th>Milestone Date</th><th>For the Month & Year</th><th>Amount</th></tr></thead><tbody>';
					
					for(var i=0;i<inv.length;i++){
						html += '<tr><td><input class="js_invoice_checkbox" type="checkbox" name="invoice_id[]" value="'+inv[i].expectid+'" /><input type="hidden" name="" value="'+inv[i].amount+'" /></td>';
						html += '<td>'+inv[i].lead_title+'<input type="hidden" name="project_name[]" value="'+inv[i].lead_title+'" /></td>';
						html += '<td>'+inv[i].project_milestone_name+'<input type="hidden" name="project_milestone_name[]" value="'+inv[i].project_milestone_name+'" /></td>';
						html += '<td>'+inv[i].milestone_date+'</td>';
						html += '<td>'+inv[i].month_year+'<input type="hidden" name="month_year[]" value="'+inv[i].month_year+'" /></td>';
						html += '<td>'+inv[i].expect_worth_name+' '+inv[i].amount+'<input type="hidden" name="amount[]" value="'+inv[i].amount+'" /></td>';
						html += '</tr>';
					}
					var email_address = '';
					if(inv[0].email_1){
						email_address += inv[0].email_1;
					}
					if(inv[0].email_2){
						email_address += ','+inv[0].email_2;
					}
					if(inv[0].email_3){
						email_address += ','+inv[0].email_3;
					}
					if(inv[0].email_4){
						email_address += ','+inv[0].email_4;
					}
					html += '</tbody></table>';
					$(".js_leads_list").html(html);
					$(".email_address").val(email_address);
					$(".customer_name").val(inv[0].first_name+' '+inv[0].last_name);
					$('.js_expect_worth').html(inv[0].expect_worth_name)
					$('.js_expect_worth').html(inv[0].expect_worth_name)
					$('.currency_type').val(inv[0].expect_worth_name)
				}
			  }
			});
			return false;
		}
	});
	
	$("#content").on("click",".js_invoice_checkbox",function(){
		var sub = $(".js_sub_total").val();
		var thisvalue = $(this).next().val();
		if($(this).prop("checked")){			
			var sub_total = parseFloat(sub) + parseFloat(thisvalue);
		}else{
			var sub_total = parseFloat(sub) - parseFloat(thisvalue);
		}
		var sum = sub_total.toFixed(2);
		$(".js_sub_total").val(sum);
		
		if($(".js_tax").val()){
			var tax_value = $(".js_tax").val();
			var tax_addition = (parseFloat(sum)*parseFloat(tax_value))/100;
			var tax_total = parseFloat(sum)+parseFloat(tax_addition);
			tax_total = tax_total.toFixed(2);
			tax_addition = tax_addition.toFixed(2);
			$(".tax_price").val(tax_addition);
			$(".total").val(tax_total);
		}else{
			$(".total").val(sum);
		}
		
		
	});
	
	$(".js_tax").blur(function(){
		var tax_value = $(this).val();
		var sub_total = $(".js_sub_total").val();
		
		if(tax_value){
			var tax_addition = (parseFloat(sub_total)*parseFloat(tax_value))/100;
			tax_addition = tax_addition.toFixed(2);
			var total = parseFloat(sub_total)+parseFloat(tax_addition);
			total = total.toFixed(2);
			$(".total").val(total);
			$(".tax_price").val(tax_addition);
		}
		
		
	})
});
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
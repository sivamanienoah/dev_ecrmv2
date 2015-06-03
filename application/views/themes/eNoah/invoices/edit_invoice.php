<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
//echo count($customers);exit;
//echo '<pre>';print_r($customers);exit;

if(count($expresults)>0 && !empty($expresults)){
	
	$email = $expresults->email_1;
	if($expresults->email_2){
		$email .= ','.$expresults->email_2;
	}
	if($expresults->email_3){
		$email .= ','.$expresults->email_3;
	}
	if($expresults->email_4){
		$email .= ','.$expresults->email_4;
	}
	
 
?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<div id="content">
	<div class="inner">	
	<form onsubmit="return startQuote();" action="<?php echo base_url().'invoice/save_invoice'?>" method="post" enctype="multipart/form-data">
	<div class="q-main-left">	
		 <div class="pull-left side1 test-block full-div"> 
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<div class="clear"></div>
			<div>
				<input type="hidden" id="csrf_hash_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type="hidden" name="sub_total_1" value="<?php echo $expresults->amount;?>" class="js_sub_total" />
				<input type="hidden" name="customer_name" value="" class="customer_name" />
				<input type="hidden" name="currency_type" value="" class="currency_type" />
				<input type="hidden" name="js_current" value="<?php echo date("d-m-Y");?>" class="js_current" />
				<input type="hidden" name="month_year" value="<?php echo date("F Y",strtotime($expresults->month_year));?>" class="js_month_year" />
				<input type="hidden" name="invoice_id" value="<?php echo $expresults->expectid;?>" class="invoice_id" />
				<div class="pull-left">
					<label class="practices">Select Customer</label>
					<select disabled="disabled" class="js_customer" name="customer1">
						<option value="">Select</option>
						<?php if(count($customers)>0 && !empty($customers)){
									foreach($customers as $customer){?>
										<option <?php echo ($customer->custid== $expresults->custid)?'selected="selected"':'';?> value="<?php echo $customer->custid;?>"><?php echo $customer->first_name.' '.$customer->last_name.' '.$customer->company;?></option>
						<?php }}?>
					</select>
				</div>
					<select class="js_customer_name" name="customer" style="display:none;">
						<option value="">Select</option>
						<?php if(count($customers)>0 && !empty($customers)){
									foreach($customers as $customer){?>
										<option <?php echo ($customer->custid== $expresults->custid)?'selected="selected"':'';?> value="<?php echo $customer->custid;?>"><?php echo $customer->first_name.' '.$customer->last_name;?></option>
						<?php }}?>
					</select>				
				<div class="clear"></div>	
				<div class="pull-left">
					<label class="practices">Project Name</label>
					<input type="text" class="textfield width300px" readonly="readonly" id="project_name" name="project_name" value="<?php echo $expresults->lead_title;?>" />
				</div>
				<div class="clear"></div>	
				<div class="pull-left">
					<label class="practices">Project Code</label>
					<input type="text" class="textfield width300px" readonly="readonly" id="project_code" name="project_code" value="<?php echo $expresults->pjt_id;?>" />
				</div>						
				<div class="clear"></div>	
				<div class="pull-left">
					<label class="practices">Project Milestone Name</label>
					<input type="text" class="textfield width300px" name="project_milestone_name" id="project_milestone_name" value="<?php echo $expresults->project_milestone_name;?>" />
				</div>						
 	
				<div class="pull-left">
					<label class="practices">Tax</label>
					<input class="js_tax textfield width300px" type="text" name="tax" value="<?php echo $expresults->tax;?>" />%
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Sub Total</label>
					<input readonly="readonly" type="text" name="sub_total" class="textfield width300px js_sub_total1" value="<?php echo $expresults->amount;?>" /><span class="js_expect_worth"><?php echo $expresults->expect_worth_name;?></span>
				</div>
				<div class="clear"></div>
				<div class="pull-left">
					<label class="practices">Tax Price</label>
					<input readonly="readonly" type="text" name="tax_price" class="tax_price width300px textfield" value="<?php echo $expresults->tax_price;?>" /><span class="js_expect_worth"><?php echo $expresults->expect_worth_name;?></span>
				</div>
				<div class="clear"></div>				
				<div class="pull-left">
					<label class="practices">Total</label>
					<input type="text" name="total" class="total textfield width300px" readonly="readonly" value="<?php echo $expresults->amount+$expresults->tax_price;?>" /><span class="js_expect_worth"><?php echo $expresults->expect_worth_name;?></span>
				</div>
 
				<div class="clear"></div>
				<div id="attachments">
				<?php 
				if(count($attachments)>0 && !empty($attachments)){
					foreach($attachments as $attach){?>
						<a href="javascript:void(0)" class="js_del_attach" rel="<?php echo $attach->id;?>">X</a>
						<?php echo anchor(base_url()."crm_data/invoices/".$attach->file_name,$attach->file_name,'target="_blank"').'<br>';
					}
				}?>	
				<div class="pull-left">
					<label class="practices">Attachment(s)</label>
					<input type="file" name="attachment[]" value="" />
					<a href="javascript:void(0);" class="js_add">Add</a>
				</div>
			
				<div class="clear"></div>
				 </div>		
				<div class="pull-left">
					<label class="practices"></label>
					<input class="positive"  type="submit"  name="submit" class="js_submit" value="Save" />
					<input class="positive"  type="button" onclick="window.location.href='<?php echo base_url().'invoice/';?>'"  name="cancel" class="cancel_1" value="Cancel" />
				</div>					
			
			</div>
		</div>
		</div>
 
		</form>	
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<?php } ?>
<script type="text/javascript">


function startQuote() {
    var err = [];
    if ($('.js_customer').val() == '') {
        err.push('A valid customer needs to be selected');
    }
    if ($.trim($('#project_name').val()) == '') {
        err.push('Project Name is required');
    }
    if ($.trim($('#project_code').val()) == '') {
        err.push('Project Code is required');
    }
 
	if ($.trim($('#project_milestone_name').val()) == '') {
        err.push('Milestone name is required');
    }
	/* if ($.trim($('#expiry_date').val()) == '') {
        err.push('Expiry Date is required');
    }
	if(!$('.js_payment_options').is(':checked')){
		err.push('Select any Payment Option');
	} */
	if ($.trim($('.js_sub_total1').val()) == '') {
        err.push('Sub Total is required');
    }
	if ($.trim($('.total').val()) == '') {
        err.push('Total is required');
    } 
	/* if ($.trim($('.email_address').val()) == '') {
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
	} */
	
    if (err.length > 0) {
        // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
		$.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">'+err.join('<br />')+'</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
    } else {
		return true;
		/* 
		$(".preview_content").css("display","block");
		$('.preview_email_address').html($(".email_address").val());
		$('.preview_cust_name').html($(".js_customer_name :selected").text());
		$('.preview_date').html($(".js_current").val());
		$('.preview_project_name').html($("#project_name").val());
		$('.preview_milestone_name').html($("#project_milestone_name").val());
		$('.preview_for_month').html($(".js_month_year").val());
		$('.preview_amount, .preview_subtotal').html($('.js_expect_worth:first').text()+' '+$(".js_sub_total1").val());
		$('.preview_tax').html($(".js_tax").val());
		$('.preview_tax_amount').html($('.js_expect_worth:first').text()+' '+$(".tax_price").val());
		$('.preview_total').html($('.js_expect_worth:first').text()+' '+$(".total").val());
		$('.preview_expiry_date').html($("#expiry_date").val());

		$('.customer_name').val($(".js_customer_name :selected").text());
		$('.currency_type').val($('.js_expect_worth:first').text());		
		
		return false; */		
    } 
}

$(document).ready(function(){
	$('.js_del_attach').click(function(event){
		event.preventDefault();
		var id = $(this).attr("rel");
		if(!confirm('Are you sure to delete?')){
			return false;
		}
		
		$.ajax({
			  url: '<?php echo base_url()?>invoice/delete_attachment/',
			  data: { id: id,csrf_token_name : csrf_hash_token},
			  success: function(data){
				  window.location.reload();
			  }
			});
			return false;
	})
		
	
	$('#expiry_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true
	});	
	
	$('.js_add').click(function(){
		var html = '<div><div class="pull-left"><label class="practices">&nbsp;</label><input type="file" name="attachment[]" value="" /><a href="javascript:void(0);" class="js_delete">Delete</a></div><div class="clear"></div></div>';
		$("#attachments").append(html)
	});
	
	$("#content").on("click",".js_delete",function(){
		if(confirm("Are you sure to delete?")){
			$(this).parent().parent().remove();	
		}
		
	})	
	
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
		
		if(tax_value)
		{
			var tax_addition = (parseFloat(sub_total)*parseFloat(tax_value))/100;
			tax_addition = tax_addition.toFixed(2);
			var total = parseFloat(sub_total)+parseFloat(tax_addition);
			total = total.toFixed(2);
			$(".total").val(total);
			$(".tax_price").val(tax_addition);
		}else{
			$(this).val(0)
			var tax_addition = parseFloat(sub_total)
			tax_addition = tax_addition.toFixed(2);
			var total = parseFloat(sub_total);
			total = total.toFixed(2);
			$(".total").val(total);
			$(".tax_price").val(0);			
		}
	});
});

function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}


function cancelDel() {
    $.unblockUI();
}
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
</style>
<div id="content">
	<div class="inner">	
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			  
			<div class="section-right">
				<div class="buttons export-to-excel">
					<a href="<?php echo site_url('invoice/send_invoice/');?>">Send Payment Request</a>
				</div>
			</div>			
			<!--<div class="section-right">
				<div class="buttons export-to-excel">
					<button id="inv_excel" onclick="location.href='#'" class="positive" type="button">
						Export to Excel
					</button>
					<input type="hidden" id="val_export" name="val_export" value="<?php echo $val_export ?>" />
				</div>
			</div>-->
			<div class="clearfix"></div>
		</div>
		<?php if($this->session->userdata("success_message")):
				echo '<div id="confirm"><p>'.$this->session->userdata("success_message").'</p></div>';
				$this->session->set_userdata("success_message",'');
		 endif; ?>
		
		<?php 
		if($this->session->userdata('accesspage')==1)
		{
		?>
		<div id="filter_section">
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div id='results'>
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
				<thead>
					<tr>
						<th>Date</th>
						<th>Customer Name</th>
						<th>Paid Amount</th>
						<th>Link</th>
						<th>Expiry Date</th>
						<th>Status</th>
						<th>Payment Status</th>
						<th>Transaction ID</th>
						<th>Transaction Date</th>
						<th>Transaction Method</th>
						<th>Transaction Message</th>
						<th>View Invoice(s)</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$status = array(0 => "Pending",1 => "Completed");
					$paid_status = array(0 => "Failure",1 => "Success");
					$transaction_method = array(1 => "Paypal",2 => "Authorize.net");
					if (is_array($results) && count($results) > 0) { ?>
					<?php foreach($results as $res) { ?>
						<tr>
							<td><?php echo date("d-m-Y",strtotime($res->created_date)); ?></td>
							<td><?php echo $res->customer_name; ?></td>
							<td><?php echo $res->paid_amount;?></td>
							<td><?php echo $res->unique_link;?></td>
							<td><?php echo date("d-m-Y",strtotime($res->expiry_date)); ?></td>
							<td><?php echo $status[$res->status];?></td>
							<td><?php echo $paid_status[$res->paid_status];?></td>
							<td><?php echo $res->transaction_id;?></td>
							<td><?php echo ($res->transaction_date!='')?date("d-m-Y",strtotime($res->transaction_date)):''; ?></td>
							<td><?php echo $transaction_method[$res->transaction_method];?></td>
							<td><?php echo $res->transaction_message;?></td>
							<td><a class="js_view_invoices" rel="<?php echo $res->invoice_id;?>" href="javascript:void(0);">View</a></td>
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<?php 
		}
		else { 
			echo "You have no rights to access this page"; 
		}
		?>
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<div id='popupGetSearchName'></div>
<script type="text/javascript" src="assets/js/invoice/payment_milestones-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/invoice/payment_milestones.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".js_view_invoices").click(function(){
		var inv_id = $(this).prop("rel");
		if(inv_id){
			$.ajax({
			  url: '<?php echo base_url()?>invoice/show_invoice_history/',
			  data: { invoice_id: inv_id,csrf_token_name : csrf_hash_token},
			  success: function(data){
				if(data=='no_results'){
					alert("Invoice(s) not found for the selected customer!");
				}else{
						$.blockUI({
							message:data,
							css:{border: '2px solid #999', color:'#333',padding:'6px',top:'280px',left:($(window).width() - 665) /2+'px',width: '646px', position: 'absolute'},
							onOverlayClick: $.unblockUI 
							// focusInput: false 
						});	
				}
			  }
			});
			return false;			
		}
	})

	$("body").on("click",'.js_close',function(){
		$.unblockUI();
	})
})
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
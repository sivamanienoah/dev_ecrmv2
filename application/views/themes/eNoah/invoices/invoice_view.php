<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<div id="content">
	<div class="inner">	
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<div class="clearfix"></div>
			</div>
		</div>
		
		<?php 
		if($this->session->userdata('accesspage')==1)
		{ 
		?>
		<div id="filter_section">
			<a class="choice-box" onclick="advanced_filter();" >
			Advanced Filters
			<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>
			
			<div class="clear"></div>
			
			<div id="advance_search" style="padding-bottom:15px;">
				<form name="advanceFiltersDash" id="advanceFiltersDash" method="post" style="width:940px;">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">By Projects</td>
								<td class="tblheadbg">By Customers</td>
								<td class="tblheadbg">By Practices</td>
								<td class="tblheadbg">By Date</td>
							</tr>
							<tr>	
								<td>
									<select multiple="multiple" id="project" name="project[]" class="advfilter">
										<?php foreach($projects as $pj) { ?>
											<option value="<?php echo $pj['lead_id']; ?>"><?php echo character_limiter($pj['lead_title'], 30); ?></option>
										<?php } ?>					
									</select> 
								</td>
								<td>
									<select multiple="multiple" id="customer" name="customer[]" class="advfilter">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>
										<?php } ?>
									</select> 
								</td> 
								<td>
									<select multiple="multiple" id="practice" name="practice[]" class="advfilter">
										<?php foreach ($practices as $pr) { ?>
												<option value="<?php echo $pr['id'] ?>"><?php echo $pr['practices']; ?></option>
										<?php
											} 
										?>
									</select> 
								</td>
								<td>
									From <input type="text" name="from_date" id="from_date" class="textfield" style="width:57px;" />
									<br />
									To <input type="text" name="to_date" id="to_date" class="textfield" style="width:57px; margin-left: 13px;" />
								</td>
							</tr>
							<tr align="right" >
								<td colspan="6">
									<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
									<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
		<div class="clear"></div>
		<div id='results'>
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
				<thead>
					<tr>
						<th>Invoice Date</th>
						<th>Customer Name</th>
						<th>Project Title</th>
						<th>Project Code</th>
						<th>Milestone Name</th>
						<th>Actual Value</th>
						<th>Value(<?php echo $default_currency; ?>)</th>
					</tr>
				</thead>
				<tbody>
				<?php if (is_array($invoices) && count($invoices) > 0) { ?>
					<?php foreach($invoices as $inv) { ?>
						<tr>
							<td><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></td>
							<td><?php echo $inv['customer']; ?></td>
							<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
							<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
							<td><?php echo $inv['project_milestone_name']; ?></td>
							<td><?php echo $inv['actual_amt']; ?></td>
							<td><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan='6' align='right'><strong>Total Value</strong></td><td><?php echo sprintf('%0.2f', $total_amt); ?></td>
					</tr>
				</tfoot>
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
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/invoice/invoice_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
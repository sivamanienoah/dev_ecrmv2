<?php
$this->load->helper('custom_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id = '1';
	$default_cur_name = 'USD';
}
?>
<div id="ad_filter" class="clear">
<div style="text-align:right"><a id="excel" class="export-btn">Export to Excel</a></div>	
	        	
	            <table border="0" cellpadding="0" cellspacing="0" class="data-table lead-table">
					<thead>
						<tr>
							<th>Lead No.</th>
							<th>Lead Title</th>
							<th>Customer</th>
							<th>Region</th>
							<th>Lead Owner</th>	
							<th>Lead Assignee</th>	
							<th>Lead Indicator</th>
							<th>Lead Stage</th>
							<th>Status</th>
							<th>Expected Worth (<?php echo $default_cur_name; ?>)</th>
						</tr>
					</thead>
					
						<?php
						if(!empty($res))
						{
							$amt_converted = 0;
							$total = 0;
							foreach ($res as $leads)
							{
						?>
								<tr>
									<td><?php echo empty($leads->invoice_no)?'':$leads->invoice_no; ?></td>
									<td><?php echo empty($leads->lead_title)?'':$leads->lead_title; ?></td>
									<td><?php echo $leads->cust_first_name.''.$leads->cust_last_name; ?></td>
									<td><?php echo empty($leads->region_name)?'':$leads->region_name; ?></td>
									<td><?php echo $leads->ownrfname.' '.$leads->ownrlname; ?></td>
									<td><?php echo $leads->usrfname.' '.$leads->usrlname; ?></td>
									<td><?php echo empty($leads->lead_indicator)?'':$leads->lead_indicator; ?></td>
									<td><?php echo empty($leads->lead_stage_name)?'':$leads->lead_stage_name; ?></td>
									<td>
									<?php
									
										if($leads->lead_status == 1)
											$status = 'Active';
										else if ($leads->lead_status == 2)
											$status = 'On Hold';
										else 
											$status = 'Dropped';
											
										echo $status;
									?>									
									</td>
									<td align = 'right'>
										<?php 
											$amt_converted = conver_currency($leads->expect_worth_amount,$rates[$leads->expect_worth_id][$default_cur_id]);
											$total+=$amt_converted;
											echo empty($leads->expect_worth_amount)?'':$amt_converted;
										?>
									</td>
								</tr>
						<?php 
							}
							?>
							<tfoot>
								<tr>
									<td colspan="9" align="right"><strong>Total (<?php echo $default_cur_name; ?>)</strong></td>
									<td align = 'right'><strong><?php echo $total; ?></strong></td>
								</tr>
							</tfoot>
							<?php
							
						} else{
							?>
							<tr>
								<td colspan = '10' align="center">No result</td>
							</tr>
							<?php 
						}
						?>
						
						
					
				</table>
				<br/>    
	            
				
	
</div>

<?php 
function conver_currency($amount,$val)
{
	return round($amount*$val);
}
?>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/report/active_lead_report_view.js"></script>
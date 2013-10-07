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
							<th>Expected Worth (USD)</th>
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
									<td><?php echo empty($leads->job_title)?'':$leads->job_title; ?></td>
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
									<td colspan="9" align="right"><strong>Total (USD)</strong></td>
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
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$(function(){
	$('#excel').click(function() {
		//mychanges		
		var start_date = $('#task_search_start_date').val();
		var end_date = $('#task_search_end_date').val();
		//var range = $('#range').val();
		var stage = $('#stage').val();
		var customer = $('#customer').val();
		var worth = $('#worth').val();
		var owner = $('#owner').val();		
		var leadassignee = $('#leadassignee').val();

		var regionname = $('#regionname').val();			
		var countryname = $('#countryname').val();		
		var statename = $('#statename').val();		
		var locname = $('#locname').val();
		
		var base_url = "<?php echo site_url(); ?>";

		var url = base_url+"report/report_least_active_lead/excelExport";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />'+
		  '<input type="hidden" name="start_date" value="' +start_date+ '" />' +
		  '<input type="hidden" name="end_date" value="' +end_date+ '" />' +
		  //'<input type="hidden" name="range" value="' +range+ '" />' +
		  '<input type="hidden" name="stage" value="' +stage+ '" />' +
		  '<input type="hidden" name="customer" value="' +customer+ '" />' +
		  '<input type="hidden" name="worth" value="' +worth+ '" />' +
		  '<input type="hidden" name="owner" value="' +owner+ '" />' +
		  '<input type="hidden" name="leadassignee" value="' +leadassignee+ '" />' +

		  '<input type="hidden" name="regionname" value="' +regionname+ '" />' +
		  '<input type="hidden" name="countryname" value="' +countryname+ '" />' +
		  '<input type="hidden" name="statename" value="' +statename+ '" />' +
		  '<input type="hidden" name="locname" value="' +locname+ '" />' +		  
		  '</form>');
		$('body').append(form);
		$(form).submit();
			
		//var sturl = base_url+"report/report_lead_region/excelExport/"+start_date+"/"+end_date+"/"+stage+"/"+customer+"/"+worth+"/"+owner+"/"+leadassignee;
		//document.location.href = sturl;
		
		//$('#advance_search_results').load(sturl);	
		return false;
	});
	
	
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>
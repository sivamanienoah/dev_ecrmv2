<?php $cfg = $this->config->item('crm'); ?>
<?php $userdata = $this->session->userdata('logged_in_user'); 
//echo $this->session->userdata('viewlead');
?>
<script>
$('#excel').click(function() {
	//mychanges
	/*
	var stage = "<?php echo $stage; ?>";
	var customer = "<?php echo $customer; ?>";
	var leadassignee = "<?php echo $leadassignee; ?>";
	var regionname = "<?php echo $regionname; ?>";
	var countryname = "<?php echo $countryname; ?>";
	var statename = "<?php echo $statename; ?>";
	var locname = "<?php echo $locname; ?>";
	var worth = "<?php echo $worth; ?>";
	var owner = "<?php echo $owner; ?>";
	var keyword = "<?php echo $keyword; ?>";
	*/	
	var sturl = "welcome/excelExport/";
	document.location.href = sturl;
	//$('#advance_search_results').load(sturl);	
	return false;
});

</script>
<div style="text-align:right; padding-bottom:5px; padding-right:0px;" >
	<a id="excel" class="export-btn">Export to Excel</a>
</div>

<div id="ad_filter" style="overflow:scroll; height:400px; width:960px;" >
<table border="0" cellpadding="0" cellspacing="0" style="width:1650px !important;" class="data-table lead-table">
<thead>
	<tr>
	<th width="90">Action</th>
	<th width="50">Lead No.</th>
	<th>Lead Title</th>
	<th>Customer</th>
	<th>Region</th>
	<th>Lead Owner</th>
	<th>Lead Assigned To</th>
	<th>Expected Worth</th>
	<!--<th>Lead Creation Date</th>
	<th>Updated On</th>
	<th>Updated By</th>-->
	<th>Lead Stage</th>
	<!--<th>Expected Proposal Date</th>
	<th>Proposal Sent on</th>
	<th>Variance(days)</th>-->
	<th>Lead Indicator</th>
	<th>Status</th>
	
	</tr>
	</thead>
	<tbody>
	<?php //echo "<pre>"; print_r($lead_owner_display); ?>
	<?php 
		if(!empty($filter_results)) {
		foreach($filter_results as $filter_result) {
		//echo "<pre>"; print_r($filter_result['belong_to']);?>
		<tr>
		<td class="actions" align="center"><?php if ($this->session->userdata('viewlead')==1) { ?><a href="<?php echo  base_url(); ?>welcome/view_quote/<?php echo  $filter_result['jobid'], '/', $quote_section ?>">View</a><?php } else echo "View"; ?>
			<?php 
			if ($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $filter_result['lead_assign'] == $userdata['userid']) {
			echo ($filter_result['invoice_downloaded'] != 1) ? ' | <a href="welcome/edit_quote/' . $filter_result['jobid'] . '">Edit</a>' : '' ?>
			<?php } ?> 
			<?php
			if ($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2 ) {
			$list_location = ($this->uri->segment(3)) ? '/' . $this->uri->segment(3) : '';
			echo (($this->session->userdata('deletelead')==1) && $filter_result['invoice_downloaded'] != 1) ? ' | <a href="welcome/delete_quote/' . $filter_result['jobid'] . $list_location . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $filter_result['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this lead.\');">Delete</a>' : ' | Delete';
			} ?>
		</td>
		<td>		
		<a href="<?php echo base_url(); ?>welcome/view_quote/<?php echo  $filter_result['jobid'], '/', 'draft' ?>">		
		<?php echo $filter_result['invoice_no']; ?></a> 
		</td>
		<td> <a href="<?php echo base_url(); ?>welcome/view_quote/<?php echo  $filter_result['jobid'], '/', 'draft' ?>"><?php echo $filter_result['job_title']; ?></a> </td>
		<td><?php echo $filter_result['first_name'].' '.$filter_result['last_name'].' - '.$filter_result['company']; ?></td>
		<td><?php echo $filter_result['region_name']; ?></td>
		<td><?php echo $filter_result['ubfn'].' '.$filter_result['ubln']; ?></td>
		<td><?php echo $filter_result['ufname'].' '.$filter_result['ulname']; ?></td>
		<td style="width:90px;"><?php echo $filter_result['expect_worth_name'].' '.$filter_result['expect_worth_amount']; ?></td>
		<!--<td><?php #echo date('d-m-Y', strtotime($filter_result['date_created'])); ?></td>-->
		<!--<td><?php #echo date('d-m-Y', strtotime($filter_result['date_modified'])); ?></td>-->
		<!--<td><?php #echo $filter_result['usfname'].' '.$filter_result['uslname']; ?></td>-->
		<td><?php echo $filter_result['lead_stage_name']; ?></td>
		<?php if($filter_result['proposal_expected_date'] == NULL) {
				$proposal_exp = '-';
				} else {
				$proposal_exp = date('d-m-Y', strtotime($filter_result['proposal_expected_date']));
				}
		?>
		<!--<td align="center"><?php echo $proposal_exp; ?></td>-->
		<?php if($filter_result['proposal_sent_date'] == NULL) {
				$proposal_sent = '-';
				} else {
				$proposal_sent = date('d-m-Y', strtotime($filter_result['proposal_sent_date']));
				}
		?>
		<!--<td align="center"><?php echo $proposal_sent; ?></td>-->
		<!--<td align="center"><?php 
		$date1 = $filter_result['proposal_sent_date'];
		$date2 = $filter_result['proposal_expected_date'];
		if($date1 != '' && $date2 != '')
		{
		$diff = abs(strtotime($date2) - strtotime($date1));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		#echo $days;
		} else {
		echo '-';
		}?></td>-->
		<td><?php echo $filter_result['lead_indicator']; ?></td>
		<td><?php 
		if($filter_result['lead_status'] == 1)
		$status = 'Active';
		else if ($filter_result['lead_status'] == 2)
		$status = 'On Hold';
		else 
		$status = 'Dropped';
				
		echo $status; ?></td>
		
		</tr> <?php 
		} } else {?>
		<tr align="center" ><td colspan="17"> No Results Found.</td></tr>
		<?php }
	 ?>	
	
</tbody>
</table>
</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$(function(){
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>

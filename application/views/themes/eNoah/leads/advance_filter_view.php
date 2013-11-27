<?php $cfg = $this->config->item('crm'); ?>
<?php $userdata = $this->session->userdata('logged_in_user'); 
?>
<div style="text-align:right; padding-bottom:5px; padding-right:0px;" >
	<a id="excel" class="export-btn">Export to Excel</a>
</div>

<div id="ad_filter" class="custom_dashboardfilter" style="overflow:scroll; width:960px;" >
<table border="0" cellpadding="0" cellspacing="0" style="width:1650px !important;" class="data-tbl dashboard-heads dataTable">
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
	<th>Lead Stage</th>
	<th>Lead Indicator</th>
	<th width="50">Status</th>
	
	</tr>
	</thead>
	<tbody>
	<?php 
		if(!empty($filter_results)) 
		{
			foreach($filter_results as $filter_result) 
			{
	?>
			<tr>
				<td class="actions" align="center"><?php if ($this->session->userdata('viewlead')==1) { ?><a href="<?php echo  base_url(); ?>welcome/view_quote/<?php echo  $filter_result['jobid'] ?>">View</a><?php } else echo "View"; ?>
				<?php 
				if ($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $filter_result['lead_assign'] == $userdata['userid']) {
				echo ' | <a href="welcome/edit_quote/' . $filter_result['jobid'] . '">Edit</a>'; ?>
				<?php } ?> 
				<?php
				if ($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2 ) {
				echo (($this->session->userdata('deletelead')==1)) ? ' | <a href="welcome/delete_quote/' . $filter_result['jobid'] . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $filter_result['job_title']) . '?\n\nThis will delete all the items\nand logs attached to this lead.\');">Delete</a>' : ' | Delete';
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
				<td><?php echo $filter_result['lead_stage_name']; ?></td>
				<td><?php echo $filter_result['lead_indicator']; ?></td>
				<td>		
					<?php 
						switch ($filter_result['lead_status'])
						{
							case 1:
								echo $status = '<span class=label-wip>Active</span>';
							break;
							case 2:
								echo $status = '<span class=label-warning>On Hold</span>';
							break;
							case 3:
								echo $status = '<span class=label-inactive>Dropped</span>';
							break;
							case 4:
								echo $status = '<span class=label-success>Closed</span>';
							break;
						}
					?>
				</td>
			</tr> 
	<?php 
			} 
		} 
		else 
		{
	?>
		<tr align="center" ><td colspan="17"> No Results Found.</td></tr>
	<?php 
	}
	?>
</tbody>
</table>
</div>
<script type="text/javascript" src="assets/js/leads/advance_filter_view.js"></script>
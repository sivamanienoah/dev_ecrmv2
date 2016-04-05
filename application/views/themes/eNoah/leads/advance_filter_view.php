<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user'); 
?>
<div id="ad_filter" class="custom_dashboardfilter" style="overflow-x:scroll; width:100%;" >
<table border="0" cellpadding="0" cellspacing="0" style="width:1650px !important;" class="data-tbl dashboard-heads dataTable">
<thead>
	<tr>
		<th>Action</th>
		<th>Lead No.</th>
		<th>Lead Title</th>
		<th>Customer</th>
		<th>Region</th>
		<th>Lead Owner</th>
		<th>Lead Assigned To</th>
		<th>Expected Worth</th>
		<th>Lead Stage</th>
		<th>Lead Indicator</th>
		<th>Status</th>
	</tr>
	</thead>
	<tbody>
	<?php
		if(!empty($filter_results)) 
		{
			foreach($filter_results as $filter_result) 
			{
	?>
			<tr id='<?php echo $filter_result['lead_id'] ?>'>
				<td class="actions" align="center">
					<?php if ($this->session->userdata('viewlead')==1) { ?>
						<a href="<?php echo base_url(); ?>welcome/view_quote/<?php echo $filter_result['lead_id'] ?>" title='View'>
							<img src="assets/img/view.png" alt='view' >
						</a>
					<?php } ?>
					<?php 
					if ($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $filter_result['lead_assign'] == $userdata['userid']) { ?>					
						<a href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $filter_result['lead_id'] ?>" title='Edit'>
							<img src="assets/img/edit.png" alt='edit' >
						</a>
					<?php } ?> 
					<?php
					if ($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2 ) { ?>
						<a href="javascript:void(0)" onclick="return deleteLeads(<?php echo $filter_result['lead_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
					<?php } ?>
				</td>
				<td>		
				<a href="<?php echo base_url(); ?>welcome/view_quote/<?php echo  $filter_result['lead_id'], '/', 'draft' ?>">		
				<?php echo $filter_result['invoice_no']; ?></a> 
				</td>
				<td> <a href="<?php echo base_url(); ?>welcome/view_quote/<?php echo  $filter_result['lead_id'], '/', 'draft' ?>"><?php echo character_limiter($filter_result['lead_title'], 35) ?></a> </td>
					<td><?php echo $filter_result['first_name'].' '.$filter_result['last_name'].' - '.$filter_result['company']; ?></td>
				<td><?php echo $filter_result['region_name']; ?></td>
				<td><?php echo $filter_result['ubfn'].' '.$filter_result['ubln']; ?></td>
				<td><?php echo $filter_result['ufname'].' '.$filter_result['ulname']; ?></td>
				<td style="width:90px;"><?php echo $filter_result['expect_worth_name'].' '.$filter_result['expect_worth_amount']; ?></td>
				<td><?php echo $filter_result['lead_stage_name']; ?></td>
				<td>
					<?php 
						switch ($filter_result['lead_indicator'])
						{
							case 'HOT':
								echo $status = '<span class=label-hot>Hot</span>';
							break;
							case 'WARM':
								echo $status = '<span class=label-warm>Warm</span>';
							break;
							case 'COLD':
								echo $status = '<span class=label-cold>Cold</span>';
							break;
						}
					?>
				</td>
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
	?>
</tbody>
</table>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/leads/advance_filter_view.js"></script>
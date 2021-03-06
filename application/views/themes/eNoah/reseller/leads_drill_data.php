<?php 
$cfg 		= $this->config->item('crm');
$userdata 	= $this->session->userdata('logged_in_user'); 
?>
<div id="ad_filter" class="" style="overflow-x:scroll; width:100%;" >
<table border="0" cellpadding="0" cellspacing="0" style="width:1650px !important;" class="data-tbl dashboard-heads dataTable">
<thead>
	<tr>
		<th>Action</th>
		<th>Lead No.</th>
		<th>Lead Title</th>
		<th>Customer</th>
		<th>Expected Worth</th>
		<th>Region</th>
		<th>Lead Owner</th>
		<th>Lead Assigned To</th>
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
				if($filter_result['pjt_status']!=0)
				{
					$view_url=base_url().'project/view_project/'.$filter_result['lead_id'];
					$view_lead_url=base_url().'project/view_project/'.$filter_result['lead_id'];
				}else
				{
					$view_url=base_url().'welcome/view_quote/'.$filter_result['lead_id'];
					$view_lead_url=base_url().'welcome/view_quote/'.$filter_result['lead_id'].'/'.'draft';
				}
	?>
			<tr id='<?php echo $filter_result['lead_id'] ?>'>
				<td class="actions" align="center">
					<?php if ($this->session->userdata('viewlead')==1) { ?>
						<a href="<?php echo $view_url;?>" target="_blank" title='View'>
							<img src="assets/img/view.png" alt='view' >
						</a>
					<?php } ?>
					<?php 
					if (($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $filter_result['lead_assign'] == $userdata['userid']) && $filter_result['pjt_status']==0) { ?>					
						<a href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $filter_result['lead_id'] ?>" target="_blank" title='Edit'>
							<img src="assets/img/edit.png" alt='edit' >
						</a>
					<?php } ?> 
					<?php
					//if (($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2) && $filter_result['pjt_status']==0) { ?>
						<!--a href="javascript:void(0)" onclick="return deleteLeads(<?php #echo $filter_result['lead_id']; ?>); return false;" title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a--> 
					<?php //} ?>
				</td>
				<td>		
				<a href="<?php echo $view_lead_url;?>" target="_blank"><?php echo $filter_result['invoice_no']; ?></a> 
				</td>
				<td> <a href="<?php echo $view_lead_url;?>" target="_blank"><?php echo character_limiter($filter_result['lead_title'], 35) ?></a> </td>
					<td><?php echo $filter_result['company'].' - '.$filter_result['customer_name']; ?></td>
				<td style="width:90px;"><?php echo $filter_result['expect_worth_name'].' '.$filter_result['expect_worth_amount']; ?></td>
				<td><?php echo $filter_result['region_name']; ?></td>
				<td><?php echo $filter_result['ubfn'].' '.$filter_result['ubln']; ?></td>
				<td><?php echo $filter_result['ufname'].' '.$filter_result['ulname']; ?></td>
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
						if($filter_result['pjt_status']!=0){
							$filter_result['lead_status']=5;
						}
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
							case 5:
								echo $status = '<span class=label-success>Moved to Project</span>';
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
<script type="text/javascript">
$(function() {
	resellerDataTable();
});
</script>
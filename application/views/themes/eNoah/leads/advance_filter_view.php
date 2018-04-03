<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user');

$td_chk = false;
$td_style = '';
$custom_width = 'width:1650px !important;';

if(!empty($db_fields) && count($db_fields)>0){
	$td_chk = true;
	
	$custom_width = 'width:100% !important;';
	if(count($db_fields) == 8) {
		$custom_width = 'width:1650px !important;';
	}
	$td_cn = $td_ew = $td_reg = $td_lo = $td_lat = $td_stg = $td_ind = $td_stat = 'style="display: none;"';
	if(in_array('CN', $db_fields)) { $td_cn = 'style="display: table-cell;"'; }
	if(in_array('EW', $db_fields)) { $td_ew = 'style="display: table-cell; width:90px;"'; }
	if(in_array('REG', $db_fields)) { $td_reg = 'style="display: table-cell;"'; }
	if(in_array('LO', $db_fields)) { $td_lo = 'style="display: table-cell;"'; }
	if(in_array('LAT', $db_fields)) { $td_lat = 'style="display: table-cell;"'; }
	if(in_array('STG', $db_fields)) { $td_stg = 'style="display: table-cell;"'; }
	if(in_array('IND', $db_fields)) { $td_ind = 'style="display: table-cell;"'; }
	if(in_array('STAT', $db_fields)) { $td_stat = 'style="display: table-cell; width:90px;"'; }
}
?>
<div id="ad_filter" class="custom_dashboardfilter customize-sec" style="overflow-x:scroll; width:100%;" >
	<div class="tbl-field-customize">
		<a href="#" class="modal-custom-fields"><span>Customize Table Fields</span></a>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" style="<?php echo $custom_width; ?>" class="data-tbl dashboard-heads dataTable">
		<thead>
		<tr>
			<th>Action</th>
			<th>Lead No.</th>
			<th>Lead Title</th>
			<?php if($td_chk == false) { ?>
				<th>Customer</th>
				<th>Expected Worth</th>
				<th>Region</th>
				<th>Lead Owner</th>
				<th>Lead Assigned To</th>
				<th>Lead Created Date</th>
				<th>Lead Stage</th>
				<th>Lead Indicator</th>
				<th>Status</th>
			<?php } else { ?>
				<th <?php echo $td_cn; ?> >Customer</th>
				<th <?php echo $td_ew; ?> >Expected Worth</th>
				<th <?php echo $td_reg; ?> >Region</th>			
				<th <?php echo $td_lo; ?> >Lead Owner</th>
				<th <?php echo $td_lat; ?> >Lead Assigned To</th>
				<th <?php echo $td_lat; ?> >Lead Created Date</th>
				<th <?php echo $td_stg; ?> >Lead Stage</th>
				<th <?php echo $td_ind; ?> >Lead Indicator</th>
				<th <?php echo $td_stat; ?> >Status</th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php //echo'<pre>';print_r($filter_results);exit;
			if(!empty($filter_results)) 
			{
				foreach($filter_results as $filter_result) 
				{
					if($filter_result['pjt_status']!=0)
					{
						$view_url=base_url().'project/view_project/'.$filter_result['lead_id'];
						$view_lead_url=base_url().'project/view_project/'.$filter_result['lead_id'];
					}
					else
					{
						$view_url=base_url().'welcome/view_quote/'.$filter_result['lead_id'];
						$view_lead_url=base_url().'welcome/view_quote/'.$filter_result['lead_id'].'/'.'draft';
					}
		?>
				<tr id='<?php echo $filter_result['lead_id'] ?>'>
					<td class="actions" align="center">
						<?php if ($this->session->userdata('viewlead')==1) { ?>
							<a target="_blank" href="<?php echo $view_url;?>" title='View'>
								<img src="assets/img/view.png" alt='view' >
							</a>
						<?php } ?>
						<?php 
						if (($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $filter_result['lead_assign'] == $userdata['userid']) && $filter_result['pjt_status']==0) { ?>					
							<a target="_blank" href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $filter_result['lead_id'] ?>" title='Edit'>
								<img src="assets/img/edit.png" alt='edit' >
							</a>
						<?php } ?> 
						<?php
						if (($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2) && $filter_result['pjt_status']==0) { ?>
							<a href="javascript:void(0)" onclick="return deleteLeads(<?php echo $filter_result['lead_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
						<?php } ?>
					</td>
					<td><a target="_blank" href="<?php echo $view_lead_url;?>"><?php echo $filter_result['invoice_no']; ?></a></td>
					<td><a target="_blank" href="<?php echo $view_lead_url;?>"><?php echo character_limiter($filter_result['lead_title'], 35) ?></a> </td>
					<?php if($td_chk == false) { ?>
					<td><?php echo $filter_result['company'].' - '.$filter_result['customer_name']; ?></td>
					<td style="width:90px;"><?php echo $filter_result['expect_worth_name'].' '.$filter_result['expect_worth_amount']; ?></td>
					<td><?php echo $filter_result['region_name']; ?></td>
					<td><?php echo $filter_result['ubfn'].' '.$filter_result['ubln']; ?></td>
					<td><?php echo $filter_result['ufname'].' '.$filter_result['ulname']; ?></td>
					<td><?php echo date('d-m-Y',strtotime($filter_result['date_created'])); ?></td>
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
					<td style="width:90px;">		
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
					<?php } else { ?>
						<td <?php echo $td_cn; ?>><?php echo $filter_result['company'].' - '.$filter_result['customer_name']; ?></td>
						<td <?php echo $td_ew; ?>><?php echo $filter_result['expect_worth_name'].' '.$filter_result['expect_worth_amount']; ?></td>
						<td <?php echo $td_reg; ?>><?php echo $filter_result['region_name']; ?></td>
						<td <?php echo $td_lo; ?>><?php echo $filter_result['ubfn'].' '.$filter_result['ubln']; ?></td>
						<td <?php echo $td_lat; ?>><?php echo $filter_result['ufname'].' '.$filter_result['ulname']; ?></td>
						<td <?php echo $td_lat; ?>><?php echo date('d-m-Y',strtotime($filter_result['date_created'])); ?></td>
						<td <?php echo $td_stg; ?>><?php echo $filter_result['lead_stage_name']; ?></td>
						<td <?php echo $td_ind; ?>>
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
						<td <?php echo $td_stat; ?>>
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
					<?php } ?>
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
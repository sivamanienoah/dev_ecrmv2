<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user');

$td_chk = false;
$td_style = '';
$custom_width = 'width:1650px !important;';

//if(!empty($db_fields) && count($db_fields)>0){
//	$td_chk = true;
//	
//	$custom_width = 'width:100% !important;';
//	if(count($db_fields) == 8) {
//		$custom_width = 'width:1650px !important;';
//	}
//	$td_cn = $td_ew = $td_reg = $td_lo = $td_lat = $td_stg = $td_ind = $td_stat = 'style="display: none;"';
//	if(in_array('CN', $db_fields)) { $td_cn = 'style="display: table-cell;"'; }
//	if(in_array('EW', $db_fields)) { $td_ew = 'style="display: table-cell; width:90px;"'; }
//	if(in_array('REG', $db_fields)) { $td_reg = 'style="display: table-cell;"'; }
//	if(in_array('LO', $db_fields)) { $td_lo = 'style="display: table-cell;"'; }
//	if(in_array('LAT', $db_fields)) { $td_lat = 'style="display: table-cell;"'; }
//	if(in_array('STG', $db_fields)) { $td_stg = 'style="display: table-cell;"'; }
//	if(in_array('IND', $db_fields)) { $td_ind = 'style="display: table-cell;"'; }
//	if(in_array('STAT', $db_fields)) { $td_stat = 'style="display: table-cell; width:90px;"'; }
//}
?>
<div id="ad_filter" class="custom_dashboardfilter customize-sec" style="overflow-x:scroll; width:100%;" >
	<div class="tbl-field-customize">
		<a href="#" class="modal-custom-fields"><span>Customize Table Fields</span></a>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" style="<?php echo $custom_width; ?>" class="data-tbl dashboard-heads dataTable">
		<thead>
		<tr>
			<th>Action</th>
			<th>S.No.</th>
			<th>Asset Name</th>
                        <th>Asset Type</th>
                        <th>Storage Mode</th>
                        <th>Location</th>
                        <th>Asset Owner</th>
                        <th>Labelling</th>
                        <th>Confidentiality</th>
                        <th>Integrity</th>
                        <th>Availability</th>
                        <th>Lead Indicator</th>
                        <th>Status</th>
			
		</tr>
		</thead>
		<tbody>
		<?php
			if(!empty($filter_results)) 
			{
                           // echo 'hi';exit;
				foreach($filter_results as $filter_result) 
				{
                                  //  print_r($filter_result);exit;
					if($filter_result['pjt_status']!=0) {
						$view_url		= base_url().'project/view_project/'.$filter_result['asset_id'];
					} else {
						$view_url		= base_url().'asset_register/view_quote/'.$filter_result['asset_id'];
					}
					
					//get the lead assign names - changes based on multiple lead assign
				//	$assign_names = get_lead_assigne_names($filter_result['lead_assign']);
		?>
					<tr id='<?php echo $filter_result['asset_id'] ?>'>
						<td class="actions" align="center">
							<?php if ($this->session->userdata('viewlead')==1) { ?>
								<a target="_blank" href="<?php echo $view_url;?>" title='View'>
									<img src="assets/img/view.png" alt='view' >
								</a>
							<?php } ?>
							<?php
								$lead_assign_arr = array(0);
								$lead_assign_arr = @explode(',',$filter_result['lead_assign']);
							if (($this->session->userdata('editlead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || (in_array($userdata['userid'], $lead_assign_arr))) && $filter_result['pjt_status']==0) { ?>				
								<a target="_blank" href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $filter_result['lead_id'] ?>" title='Edit'>
									<img src="assets/img/edit.png" alt='edit' >
								</a>
							<?php } ?> 
							<?php
							if (($this->session->userdata('deletelead')==1 && $filter_result['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2) && $filter_result['pjt_status']==0) { ?>
								<a href="javascript:void(0)" onclick="return deleteLeads(<?php echo $filter_result['lead_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
							<?php } ?>
						</td>
						<td><a target="_blank" href="<?php echo $view_url;?>"><?php echo $filter_result['asset_id']; ?></a></td>
						<td><a target="_blank" href="<?php echo $view_url;?>"><?php echo character_limiter($filter_result['asset_name'], 35) ?></a> </td>
                                                <td><?php echo  $filter_result['asset_type']; ?></td>
                                                <td><?php echo  $filter_result['storage_mode']; ?></td>
                                                <td><?php echo  $filter_result['location']; ?></td>
                                                <td><?php echo  $filter_result['asset_owner']; ?></td>
                                                <td><?php echo  $filter_result['labelling']; ?></td>
                                                <td><?php echo  $filter_result['confidentiality']; ?></td>
                                                <td><?php echo  $filter_result['integrity']; ?></td>
                                                <td><?php echo  $filter_result['availability']; ?></td>
                                                <td><?php echo  $filter_result['integrity']; ?></td>
                                              
                                        </tr> 
		<?php 
				} 
			}
		?>
	</tbody>
	</table>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/asset_register/advance_filter_view.js"></script>
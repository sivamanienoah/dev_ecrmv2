<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user');
//print_r($userdata);exit;
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
<!--	<div class="tbl-field-customize">
		<a href="#" class="modal-custom-fields"><span>Customize Table Fields</span></a>
	</div>-->
	<table border="0" cellpadding="0" cellspacing="0" style="<?php echo $custom_width; ?>" class="data-tbl dashboard-heads dataTable">
		<thead>
		<tr>
		<tr>
			<th>Action</th>
                        <th>Asset Name</th>
			<th>Department Name</th>
			<th>Project Name</th>
                        <th>Asset Type</th>
                        <th>Storage Mode</th>
                        <th>Asset Current Location</th>
                        <th>Asset Owner</th>
                        <th>Labelling</th>
                        <th>Confidentiality</th>
                        <th>Availability</th>
<!--                        <th>Asset Created Date</th>
                        <th>Asset Modified Date</th>
                        <th>Asset Location</th>-->
                </tr>
		</tr>
		</thead>
		<tbody>
		<?php
                
			if(!empty($filter_results)) 
			{
                            
                            
                                    foreach($filter_results as $filter_result) 
				{
                                  //  echo '<pre>'; print_r($filter_result);    exit;
                                    $view_url = base_url().'asset_register/view_asset/'.$filter_result['asset_id'];
					
					$get_user_details = get_lead_assigne_names($filter_result['asset_owner']);
                                      //  print_r($get_user_details);
					//get the lead assign names - changes based on multiple lead assign
				//$assign_names = get_lead_assigne_names($filter_result['lead_assign']);
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
							if (( $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $userdata['role_id'] == 3)) { ?>				
								<a target="_blank" href="<?php echo base_url(); ?>asset_register/edit_asset/<?php echo $filter_result['asset_id'] ?>" title='Edit'>
									<img src="assets/img/edit.png" alt='edit' >
								</a>
							<?php } ?> 
							<?php
							if (( $userdata['role_id'] == 1|| $userdata['role_id'] == 2 || $userdata['role_id'] == 3) ) { ?>
								<a href="javascript:void(0)" onclick="return deleteAsset(<?php echo $filter_result['asset_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
							<?php } ?>
						</td>
                                                <td><a target="_blank" href="<?php echo $view_url;?>"><?php echo $filter_result['asset_name']; ?></a> </td>
                                                  <td><?php  
                                                   $department_name = $this->asset_model->get_department_by_id($filter_result['department_id']);
                                                    echo $department_name[0]['department_name'];
                                                   
                                                 ?></td>
                                                  <td><?php  
                                                   $projects = $this->asset_model->get_project_by_id($filter_result['project_id']);
                                                  // print_r($projectslead_title);exit;
                                                   echo $projects[0]['lead_title'];
                                                  ?></td>
						  <td><?php echo  $filter_result['asset_type']; ?></td>
                                                <td><?php echo  $filter_result['storage_mode']; ?></td>
                                                <td><?php echo  $filter_result['location']; ?></td>
                                                <td><?php 
                                                echo $get_user_details;
                                            //    $get_user_details = $this->asset_model->get_user_name_by_id($filter_result['asset_owner']);
                                          //      foreach ($get_user_details as $user_details){
                                           //         echo $user_details['first_name']. ' ' .$user_details['last_name'];
                                        //        }
                                                 // print_r($get_user_details['first_name'].$get_user_details['last_name']); ?></td>
                                                <td><?php echo  $filter_result['labelling']; ?></td>
                                                <td><?php echo  $filter_result['confidentiality']; ?></td>
                                                <td><?php echo  $filter_result['availability']; ?></td>
<!--                                                <td><?php echo  $filter_result['created_on']; ?></td>
                                                <td><?php echo  $filter_result['modified_on']; ?></td>
                                                <td><?php echo  $filter_result['asset_position']; ?></td>-->
						
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
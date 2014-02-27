<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user'); 
?>

<div id="ad_filter" class="custom_dashboardfilter" style="overflow:scroll; width:960px;" >
<table border="0" cellpadding="0" cellspacing="0" style="width:1650px !important;" class="data-tbl dashboard-heads dataTable">
<thead>
	<tr>
		<th>Action</th>
		<th>Posted By</th>
		<th>Email Address</th>
		<th>Message</th>
		<th>Source</th>
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
				<td class="actions" align="center">
					<?php 

					if ($this->session->userdata('viewenquiry')==1) { ?><a href="<?php echo  base_url(); ?>enquiries/view_enquiries/<?php echo  $filter_result->oppurtunity_id ?>">View</a><?php } else echo "View"; ?>
					<?php 
					if ($this->session->userdata('editenquiry')==1  || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) {
					echo ' | <a href="enquiries/edit_enquiry/' . $filter_result->oppurtunity_id . '">Edit</a>'; ?>
					<?php } ?> 
					<?php
					if ($this->session->userdata('deleteenquiry')==1 || $userdata['role_id'] == 1|| $userdata['role_id'] == 2 ) {
						$lead_tle = str_replace("'", "\'", $filter_result->oppurtunity_name);
					?>
						| <a class="delete" href="javascript:void(0)" onclick="return deleteEnquiry(<?php echo $filter_result->oppurtunity_id; ?>, 'Enquiry'); return false; "> Delete </a> 
					<?php 				
					} 
					?>
				</td>
				<td>		
				  <?php echo stripslashes($filter_result->oppurtunity_name); ?>
				</td>
				<td><?php echo stripslashes($filter_result->oppurtunity_email); ?></td>
				<td><?php echo stripslashes($filter_result->oppurtunity_title); ?></td>
				<td>Website</td>
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
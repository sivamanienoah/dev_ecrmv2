<?php
$userdata = $this->session->userdata('logged_in_user');
?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable">
	<thead>
		<tr>
			<th>Action</th>
			<th>Project No.</th>
			<th>Project Title</th>
			<th>Customer</th>
			<th>Actual Worth</th>
			<th>Region</th>
			<th>Lead Owner</th>
			<th>Lead Assigned To</th>
			<th>Lead Created Date</th>
			<th style="width:100px;">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php //echo'<pre>';print_r($closed_jobs);exit;
			if(!empty($closed_jobs)) 
			{
				foreach($closed_jobs as $jobs) 
				{
					$view_url      = base_url().'project/view_project/'.$jobs['lead_id'];
					$view_lead_url = base_url().'project/view_project/'.$jobs['lead_id'];
		?>
					<tr id='<?php echo $jobs['lead_id'] ?>'>
						<td class="actions" align="center">
							<?php if ($this->session->userdata('viewlead')==1) { ?>
								<a href="<?php echo $view_url;?>" title='View'>
									<img src="assets/img/view.png" alt='view' >
								</a>
							<?php } ?>
							<?php 
							if (($this->session->userdata('editlead')==1 && $jobs['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $jobs['lead_assign'] == $userdata['userid']) && $jobs['pjt_status']==0) { ?>					
								<a href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $jobs['lead_id'] ?>" title='Edit'>
									<img src="assets/img/edit.png" alt='edit' >
								</a>
							<?php } ?> 
							<?php
							if (($this->session->userdata('deletelead')==1 && $jobs['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2) && $jobs['pjt_status']==0) { ?>
								<a href="javascript:void(0)" onclick="return deleteLeads(<?php echo $jobs['lead_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
							<?php } ?>
						</td>
						<td>		
							<a href="<?php echo $view_lead_url;?>"><?php echo $jobs['invoice_no']; ?></a> 
						</td>
						<td> 
							<a href="<?php echo $view_lead_url;?>"><?php echo character_limiter($jobs['lead_title'], 35) ?></a> 
						</td>
						<td><?php echo $jobs['company'].' - '.$jobs['customer_name']; ?></td>
						<td style="width:90px;">
							<?php echo $jobs['expect_worth_name'].' '.$jobs['actual_worth_amount']; ?>
						</td>
						<td><?php echo $jobs['region_name']; ?></td>
						<td><?php echo $jobs['ubfn'].' '.$jobs['ubln']; ?></td>
						<td><?php echo $jobs['ufname'].' '.$jobs['ulname']; ?></td>
						<td><?php echo date('d-m-Y',strtotime($jobs['date_created'])); ?></td>
						<td style="width:100px;">		
							<?php
								switch ($jobs['pjt_status'])
								{
									case 1:
										echo $status = '<span class=label-wip>Project In Progress</span>';
									break;
									case 2:
										echo $status = '<span class=label-success>Project Completed</span>';
									break;
									case 3:
										echo $status = '<span class=label-inactive>Inactive</span>';
									break;
									case 4:
										echo $status = '<span class=label-warning>Project Onhold</span>';
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
<script type="text/javascript">
$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "desc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
		]
	});
});
</script>
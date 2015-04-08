<?php $this->load->helper('text'); ?>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Entity</th>
			<th>Customer</th>
			<th>Lead/Project Name</th>
			<th>Lead/Project</th>
			<th>Milestone Name</th>
			<th>For Month & Year</th>
			<th>Value</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($sales_forecast) && count($sales_forecast) > 0) { ?>
			<?php foreach($sales_forecast as $forecast) { ?>
				<?php $milestone_month_year = date('d-m-Y', strtotime($forecast['for_month_year'])); ?>
				<?php $current_month_year   = date('d-m-Y'); ?>
				<tr>
					<td><?php echo $forecast['division_name']; ?></td>
					<td><?php echo $forecast['company']; ?></td>
					<td><?php echo character_limiter($forecast['lead_title'], 35); ?></td>
					<td><?php if($forecast['forecast_category'] == 1) echo "Lead"; else echo "Project" ?></td>
					<td><?php echo $forecast['milestone_name']; ?></td>
					<td><?php echo date('F y', strtotime($forecast['for_month_year'])); ?></td>
					<td><?php echo $forecast['expect_worth_name']. ' ' .$forecast['milestone_value']; ?></td>
					<td class="actions">
					<?php if(strtotime($milestone_month_year) > strtotime($current_month_year)) { ?>
						<?php if($this->session->userdata('edit')==1) { ?>
							<a href="sales_forecast/add_sale_forecast/update/<?php echo $forecast['forecast_id']; ?>/?ms_id=<?php echo $forecast['milestone_id']; ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'> </a>
						<?php } ?> 
						<?php if($this->session->userdata('delete')==1) { ?>
							<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $forecast['forecast_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
						<?php } ?>
					<?php } ?>
					<a class="delete" href="javascript:void(0)" onclick="return view_logs(<?php echo $forecast['milestone_id']; ?>);" title='View Logs'> <img src="assets/img/log-icon.png" alt='Logs'> </a>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
	</tbody>
</table>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Entity</th>
			<th>Customer Name</th>
			<th>Lead/Project</th>
			<th>Milestone</th>
			<th>For the Month & Year</th>
			<th>Created By</th>
			<th>Created On</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	
	<?php if (is_array($sales_forecast) && count($sales_forecast) > 0) { ?>
		<?php foreach($sales_forecast as $forecast) { ?>
			<tr>
				<td><?php echo $forecast['division_name']; ?></td>
				<td><?php echo $forecast['customer_name']; ?></td>
				<td><?php echo $forecast['lead_name']; ?></td>
				<td><?php echo $forecast['milestone']; ?></td>
				<td><?php echo date('F Y', strtotime($forecast['for_month_year'])); ?></td>
				<td><?php echo $forecast['first_name']. ' ' .$forecast['last_name']; ?></td>
				<td><?php echo date('d-m-Y', strtotime($forecast['created_on'])); ?></td>
				<td class="actions">
					<?php if($this->session->userdata('edit')==1) { ?>
						<a href="sales_forecast/add_sale_forecast/update/<?php echo $forecast['forecast_id']; ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'> </a>
					<?php } ?> 
					<?php if($this->session->userdata('delete')==1) { ?>
						<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $forecast['forecast_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_view.js"></script>
<div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>	
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
	<thead>
		<tr>
			<th>Customer Name</th>
			<th>Company Name</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Position Title</th>
			<th>Skype Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if (is_array($contact) && count($contact) > 0) { ?>
			<?php 
			foreach ($contact as $list) 
			{
			?>
			<tr>
				<td><?php echo $list['customer_name']."".$list['last_name'];?></td>
				<td><?php echo $list['company'];?></td>
				<td><?php echo $list['phone_1']; ?></td>
				<td><?php echo $list['email_1']; ?></td>
				<td><?php echo $list['position_title']; ?></td>
				<td><?php echo $list['skype_name'] ?></td>
				<td>
					<?php if($this->session->userdata('edit')==1) { ?>
						<a target="_blank" href="customers_contact/update_contacts/<?php echo $list['custid']; ?>" title='Edit'><img src="assets/img/edit.png" alt='edit' ></a>
					<?php } ?>
					<?php if($this->session->userdata('delete')==1) { ?>
						<!--a class="delete" href="javascript:void(0)" onclick="return checkStatus_contact(<?php #echo $list['custid']; ?>);" title='Delete'><img src="assets/img/trash.png" alt='delete' ></a-->
					<?php } ?>
					<?php #if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
	</tbody>
</table>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/customer/customer_conatct_view.js"></script>
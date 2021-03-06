<?php 
require (theme_url().'/tpl/header.php'); ?>
<div id="content">	
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
		
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Role Database</h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="section-right">
						<div class="buttons add-new-button">
							<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>role/add_role'">
								Add New Role
							</button>
						</div>
					</div>
				<?php } ?>
			<div class="clearfix"></div>
		</div>
		
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            
            <thead>
                <tr>
                    <th width="20%">Name</th>
                    <th width="10%">Status</th>                 
                    <th width="70%">Action</th>                 
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td>
							<?php if($this->session->userdata('edit')==1){ ?><a href="role/add_role/update/<?php echo $customer['id'] ?>"><?php echo  $customer['name'];?></a><?php } else { echo $customer['name']; }?>
						</td>
                        <td>
							<?php if($customer['inactive'] ==1) echo '<span class=label-warning>Inactive</span>';else echo '<span class=label-success>Active</span>';?>
						</td>
						<td>
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="role/add_role/update/<?php echo  $customer['id'] ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'></a>
							<?php } ?>
							<?php if($customer['id']!=1 && $customer['id'] !=2 && $customer['id'] !=3 && $customer['id'] !=4) { ?>
							<?php if($this->session->userdata('delete')==1) { ?>
								<a class="delete" href="javascript:void(0)" onclick="return role_checkStatus(<?php echo $customer['id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
							<?php } } ?>
							<?php if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
							<div class="dialog-err pull-right" id="dialog-message-<?php echo $customer['id']; ?>" style="display:none"></div>
						</td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
		<?php } else {
	echo "You have no rights to access this page";
	} ?>
	</div>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/role/role_list_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
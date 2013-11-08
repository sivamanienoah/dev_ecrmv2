<?php 
require (theme_url().'/tpl/header.php'); ?>
<div id="content">	
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
		
		<div>
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">Role Database</h2>
			<div class="buttons pull-right">
				<button type="button" class="positive" onclick="location.href='role/add_role'">
					Add New Role
				</button>
			</div>
			<div class="clearfix"></div>
			</div>
		</div>
		
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>                 
                    <th>Action</th>                 
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1){ ?><a href="role/add_role/update/<?php echo  $customer['id'] ?>"><?php echo  $customer['name'];?></a><?php } else { echo $customer['name']; }?></td>
                        <td><?php if($customer['inactive'] ==1) echo '<span class=label-warning>Inactive</span>';else echo '<span class=label-success>Active</span>';?></td>
						<td><?php if($this->session->userdata('edit')==1){ ?><a href="role/add_role/update/<?php echo  $customer['id'] ?>"><?php echo  "Edit";?></a><?php } else { echo "Edit"; }?>
						<?php if($customer['id']!=1 && $customer['id'] !=2 && $customer['id'] !=3) { ?>
                        <?php if($this->session->userdata('delete')==1){ ?> | <a href="role/delete_role/<?php echo $customer['id'] ?>"onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a><?php }  } ?></td>				 
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
<?php require (theme_url(). '/tpl/footer.php'); ?>
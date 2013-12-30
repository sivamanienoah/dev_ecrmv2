<?php require (theme_url().'/tpl/header.php'); ?>
<?php $availed_users = check_max_users(); ?>
<?php  #echo $max_allow_user ." ". $availed_users['avail_users']; ?>
<div id="content">
    <div class="inner">
    <?php if($this->session->userdata('accesspage')==1) { ?>       
        <div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">User Database</h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" <?php if($max_allow_user <= $availed_users['avail_users']) { ?> class="negative_disable" onclick="" <?php } else { ?> class="positive" onclick="location.href='<?php echo base_url(); ?>user/add_user'" <?php } ?> >
							Add New User
						</button>
					</div>
				<?php } ?>
			<div class="clearfix"></div>
			</div>
		</div>
		
		<div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
		
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Roles</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1){ ?><a href="user/add_user/update/<?php echo  $customer['userid'] ?>"><?php echo  $customer['first_name'] . ' ' . $customer['last_name'] ?></a><?php } else { echo $customer['first_name'] . ' ' . $customer['last_name']; } ?></td>
                        <td>
						<?php echo $customer['email'] ?>
						<?php
						if ($userdata['role_id'] == 1)
						{
							echo '<a href="user/log_history/' . $customer['userid'] . '">View Logs</a>';
						}
						?>
						</td>
                        <td><?php echo  $customer['phone'] ?></td>
                        <td><?php echo  $customer['name'] ?></td>
                        <!--<td><?php //echo $customer['level'] ?></td>-->
                        <td><?php echo  $customer['level_name'] ?></td>
                        <td><?php if ($customer['inactive'] == 0) echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?></td>
						<td>
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="user/add_user/update/<?php echo $customer['userid'] ?>">Edit &raquo;</a>
							<?php } else { echo "Edit &raquo;"; } ?>
							<?php if($this->session->userdata('delete')==1) { ?>
								&nbsp;|&nbsp;
								<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $customer['custid']; ?>);"> Delete &raquo; </a> 
							<?php } ?>
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
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/user/list_view.js"></script>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
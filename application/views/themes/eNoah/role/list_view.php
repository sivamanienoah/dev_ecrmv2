<?php 
require (theme_url().'/tpl/header.php'); ?>
<div id="content">	
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1) {?>
        <h2>Role Database</h2>
        <p class="pagination"><?php echo  $pagination ?></p>
        <form action="role/search/" method="post" id="cust_search_form">
			
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Name
                    </td>
                    <td>
                        <input type="text" name="cust_search" value="<?php echo  $this->uri->segment(4) ?>" class="textfield width200px" />
                    </td>
                    <td>
                        <div class="buttons">
                            <button type="submit" class="positive">
                                Search
                            </button>
                        </div>
                    </td>
					<?php if($this->session->userdata('add')==1) { ?>
					<td valign="middle";>
						<div class="buttons">
							<button type="button" class="positive" onclick="location.href='role/add_role'">
								Add New Role
							</button>
						</div>
					</td>
					<?php } ?>
                    <?php if ($this->uri->segment(4)) { ?>
                    <td>
                        <div class="buttons">
                            <button type="submit" name="cancel_submit" class="negative">
                                Cancel
                            </button>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
            </table>
		</form>
        
        <table border="0" cellpadding="0" cellspacing="0" class="tbl-data dashboard-heads dataTable" style="width:100%">
            
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
<script type="text/javascript" src="assets/js/tbl-data.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
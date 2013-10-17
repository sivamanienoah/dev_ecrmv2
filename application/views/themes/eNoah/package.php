<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	<?php //include 'tpl/hosting_submenu.php'; ?>
	<div class="inner hosting-section">
	<?php if($this->session->userdata('accesspage')==1){ ?>
	    <h2>Package Accounts</h2>
        <p class="pagination"><?php echo  $pagination ?></p>
        <form action="package/search/" method="post" id="cust_search_form">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td valign="middle">
                        Search by Package Name
                    </td>
                    <td valign="middle">
                        <input type="text" name="account_search" value="<?php echo  $this->uri->segment(4) ?>" class="textfield width200px" />
                    </td>
                    <td valign="middle">
                        <div class="buttons">
                            <button type="submit" class="positive">
                                Search
                            </button>
                        </div>
                    </td>
					<?php if($this->session->userdata('add')==1) { ?>
					<td valign="middle";>
						<div class="buttons">
							<button type="button" class="positive" onclick="location.href='package/add'">
								Add New Package
							</button>
						</div>
					</td>
					<?php } ?>
                    <?php if ($this->uri->segment(4)) { ?>
                    <td valign="middle">
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
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-table">
            
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Package Price</th>
                    <th>Action</th>
					<th>Package Type</th>
					<th>Duration</th>
					<th>Status</th>
                </tr>
            </thead>
            <tbody>
			<?php
			if (is_array($accounts) && count($accounts) > 0) { 
				foreach ($accounts as $account) { 
				?>
				<tr>
                        <td><?php if($this->session->userdata('edit')==1) {?><a href="package/add/<?php echo  $account['package_id'] ?>"><?php echo $account['package_name'] ?></a><?php } else { echo $account['package_name']; }?></td>
                        <td>$<?php echo  $account['package_price'] ?></td>
						<td><?php if($this->session->userdata('edit')==1) {?><a href="package/add/<?php echo  $account['package_id'] ?>"><?php echo "Edit"; ?></a><?php } else { echo "Edit"; }?> | 
						<?php if($this->session->userdata('edit')==1) {?><a href="package/delete_packagename/<?php echo  $account['package_id'] ?>" onclick="return confirm('Are you sure you want to delete?')" ><?php echo "Delete"; ?></a><?php } else { echo "Delete"; }?>
						</td>
						<td><?php if($this->session->userdata('edit')==1) { ?><a href="package/update/<?php echo  $account['type_id'] ?>"><?php echo $account['PACK_NAME'] ?></a><?php } else { echo $account['PACK_NAME']; }?></td>
						<td><?php echo $account['duration'] ?></td>
						<td><?php if ($account['status'] == 'active') echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
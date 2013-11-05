<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	 
	<div class="inner hosting-section">
	<?php  if($this->session->userdata('accesspage')==1) {   ?>
	<?php
	if(!empty($hosts)) {
	if($hosts=='HOSTS'){ 
		echo '<form action="dns/submit" method="post">';
		echo'<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" />';
		echo '<select name="hostings" class="textfield" style="width:298px;">';
		foreach($hosting as $key=>$val){
			echo '<option value="'.$val['hostingid'].'">'.$val['domain_name'].'</option>';
		}
		echo '</select>';
		
		echo ' <div class="buttons">
			<button type="submit" name="update_dns" class="positive">Edit DNS</button>
			<button type="submit" name="update_hosting" class="positive">Edit Hosting</button></div>
			</form>
			';
		
	}
	}
	else {	
	?>
	    <h2>Hosting Accounts</h2>
        <p class="pagination"><?php //echo $pagination ?></p>
        <form action="hosting/search/" method="post" id="cust_search_form">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td valign="middle">
                        Search by Domain Name
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
							<button type="button" class="positive" onclick="location.href='hosting/add_account'">
								Add New Hosting Account
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
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            
            <thead>
                <tr>
                    <th>Domain Name</th>
                    <th>Customer</th>
                    <th>Domain Status</th>
					<th>DNS</th>
                    <th>Domain Expiry Date</th>
                    <th>Hosting Expiry Date</th>
					<th>SSL Status</th>
					<th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if (is_array($accounts) && count($accounts) > 0) { 
					$thirty_days=(30*86400);
				foreach ($accounts as $account) { 
					$rem=strtotime($account['go_live_date'])-time();
					if($account['login_url']!='' && $account['login']!='' && $account['registrar_password']!='' && $account['email']!='' && $account['cur_smtp_setting']!='' && $account['cur_pop_setting']!='' && $account['cur_dns_primary_url']!='' && $account['cur_dns_primary_ip']!='' && $account['cur_dns_secondary_url']!='' && $account['cur_dns_secondary_ip']!='') $dns='green';
					else if($account['login_url']!='' && $account['login']!='' && $account['registrar_password']!='') $dns='orange';
					else $dns='red';
					//else $dns='yellow';
					//if($rem>0 && $rem<$thirty_days) $dns='red';
				?>
                    <tr>
                        <td><?php if ($this->session->userdata('edit')==1) {?><a href="hosting/add_account/update/<?php echo  $account['hostingid'] ?>"><?php echo  $account['domain_name'] ?></a><?php } else echo "Edit"; ?></td>
                        <td><?php echo  $account['customer'] ?></td>
                        <td><?php echo  $account['domain_status'] ?></td>
						<td><?php if ($this->session->userdata('accesspage')==1) {?><a href="dns/go_live/<?php echo $account['hostingid'];?>" style="color:<?php echo $dns; ?>;">View</a><?php } else echo $dns; ?></td>
                        <td><?php echo  $account['domain_expiry']; ?></td>
                        <td><?php echo  $account['expiry_date']; ?></td>
						<td><?php echo  $account['ssl'] ?></td>
						<td><?php if ($this->session->userdata('edit')==1) {?><a href="hosting/add_account/update/<?php echo  $account['hostingid'] ?>"><?php echo "Edit"; ?></a><?php } else echo "Edit"; ?>
							<?php if ($this->session->userdata('delete')==1) {?> | <a href="hosting/delete_account/<?php echo  $account['hostingid'] ?>" onclick="return confirm('Are you sure you want to delete?')" ><?php echo "Delete"; ?></a><?php } ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            
        </table>
	<?php } ?>
	</div>
	<?php } else{
	echo "You have no rights to access this page";
}?>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
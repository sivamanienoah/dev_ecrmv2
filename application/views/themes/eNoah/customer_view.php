<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
        
        <h2>Customer Database</h2>
        <form action="customers/search/" method="post" id="cust_search_form">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
            <table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
                    <td>
                        Search by Name or Company Name
                    </td>
                    <td>
                        <input type="text" name="cust_search" value="<?php echo $this->uri->segment(4) ?>" class="textfield width200px" />
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
							<button type="button" class="positive" onclick="location.href='customers/add_customer'">
								Add New Customer
							</button>
						</div>
					</td>
					<td valign="middle";>
						<div class="buttons">
							<button type="button" class="positive" onclick="location.href='importcustomers'">
								Import Customer List
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
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
			<thead>
                <tr>
                    <th>Full Name</th>
                    <th>Company</th>
                    <th>Phone</th>
                    <th>Email</th>
					<th>Region</th>
					<th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1){ ?><a href="customers/add_customer/update/<?php echo  $customer['custid'] ?>"><?php echo  $customer['first_name'] . ' ' . $customer['last_name'] ?></a> <?php } else { echo $customer['first_name'] . ' ' . $customer['last_name']; } ?></td>
                        <td><?php echo  $customer['company'] ?></td>
                        <td><?php echo  $customer['phone_1'] ?></td>
                        <td><?php echo  $customer['email_1'] ?></td>
                        <!--<td><?php #echo  auto_link($customer['www_1'], 'both', TRUE) ?></td>-->
                        <td><?php echo $customer['region_name'] ?></td>
                        <td><?php echo $customer['country_name'] ?></td>
                        <td>
							<?php if($this->session->userdata('edit')==1){ ?><a href="customers/add_customer/update/<?php echo  $customer['custid'] ?>"><?php echo "Edit"; ?></a> <?php } else { echo "Edit"; } ?>
							<?php if($this->session->userdata('delete')==1){ ?> | <a href="customers/delete_customer/<?php echo  $customer['custid'] ?>" onclick="return confirm('Are you sure you want to delete?')"><?php echo "Delete"; ?></a> <?php } ?>
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
<?php require (theme_url().'/tpl/footer.php'); ?>

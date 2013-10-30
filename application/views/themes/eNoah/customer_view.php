<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/tablesort.pager.js"></script>
<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
        
        <style type="text/css">
		.data-table th {
			padding:0;
		}
		.data-table th a {
			display:block;
			padding:4px;
			color:#fff;
		}
		</style>
        
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
        
        <table border="0" cellpadding="0" cellspacing="0" class="data-table">
            
            <thead>
                <tr>
                    <th<?php if ($current_sort[0] == 'last_name') echo ' class="selected"' ?>><a href="customers/set_search_order/last_name/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Full Name</a></th>
                    <th<?php if ($current_sort[0] == 'company') echo ' class="selected"' ?>><a href="customers/set_search_order/company/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Company</a></th>
                    <th<?php if ($current_sort[0] == 'phone_1') echo ' class="selected"' ?>><a href="customers/set_search_order/phone_1/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Phone</a></th>
                    <th<?php if ($current_sort[0] == 'email_1') echo ' class="selected"' ?>><a href="customers/set_search_order/email_1/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Email</a></th>
					<th<?php if ($current_sort[0] == 'region_name') echo ' class="selected"' ?>><a href="customers/set_search_order/region_name/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Region</a></th>
					<th<?php if ($current_sort[0] == 'country_name') echo ' class="selected"' ?>><a href="customers/set_search_order/country_name/<?php echo  base64_encode(trim($this->uri->uri_string(), '/')) ?>">Country</a></th>
                    <th> Actions </th>
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
                <?php } else { ?>
                    <tr>
                        <td colspan="7" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
		<p><?php echo '&nbsp;'; ?></p>
		<div id="pager">
		<a class="first"> First </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
		<a class="prev"> &laquo; Prev </a> <?php echo '&nbsp;&nbsp;&nbsp;'; ?>
		<input type="text" size="2" class="pagedisplay"/><?php echo '&nbsp;&nbsp;&nbsp;'; ?> <!-- this can be any element, including an input --> 
		<a class="next"> Next &raquo; </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
		<a class="last"> Last </a><?php echo '&nbsp;&nbsp;&nbsp;'; ?>
		<span>No. of Records per page:<?php echo '&nbsp;'; ?> </span>
		<select class="pagesize"> 
			<option selected="selected" value="10">10</option> 
			<option value="20">20</option> 
			<option value="30">30</option> 
			<option value="40">40</option> 
		</select> 
		</div>
        <?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<script>
$(function() {

	$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager"),positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

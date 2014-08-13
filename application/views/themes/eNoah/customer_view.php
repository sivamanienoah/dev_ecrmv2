<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">Customer Database</h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>customers/add_customer'">
							Add New Customer
						</button>
						
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>importcustomers'">
							Import Customer List
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
                    <th>Company</th>
                    <th>Phone</th>
                    <th>Email</th>
					<th>Region</th>
					<th>Country</th>
					<th>Is Client</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td>
							<?php if($this->session->userdata('edit')==1){ ?><a href="customers/add_customer/update/<?php echo  $customer['custid'] ?>"><?php echo  $customer['first_name'] . ' ' . $customer['last_name'] ?></a> <?php } else { echo $customer['first_name'] . ' ' . $customer['last_name']; } ?>
						</td>
                        <td><?php echo $customer['company'] ?></td>
                        <td><?php echo $customer['phone_1'] ?></td>
                        <td><?php echo $customer['email_1'] ?></td>
                        <td><?php echo $customer['region_name'] ?></td>
                        <td><?php echo $customer['country_name'] ?></td>
                        <td align="center">
							<?php if($customer['is_client'] == 1) { ?>
								<img style="width:14px; height:14px" alt="isClient" src="assets/img/tick.png">
							<?php } else { ?>
								<?php echo "-"; ?>
							<?php } ?>
						</td>
                        <td>
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="customers/add_customer/update/<?php echo $customer['custid']; ?>/">Edit &raquo;</a>
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
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/customer/customer_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

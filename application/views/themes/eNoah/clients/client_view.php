<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('accesspage')==1) { ?>
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">Client Database</h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>clients/add_client'">
							Add New Customer
						</button>
						
						<!--button type="button" class="positive" onclick="location.href='<?php //echo base_url(); ?>importcustomers'">
							Import Customer List
						</button-->
					</div>
				<?php } ?>
			<div class="clearfix"></div>
			</div>
		</div>
		
        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
		
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
			<thead>
                <tr>
                    <th>Client Name</th>
                    <th>Website</th>
                    <th>Region</th>
					<th>Country</th>
					<th>State</th>
					<th>Location</th>
					<th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php //echo "<pre>"; print_r($clients); exit; ?>
            <tbody>
                <?php if (is_array($clients) && count($clients) > 0) { ?>
                    <?php foreach ($clients as $client) { ?>
                    <tr>
                        <td>
							<?php if($this->session->userdata('edit')==1) { ?><a href="clients/add_client/update/<?php echo $client['client_id'] ?>"><?php echo $client['client_name']; ?></a> <?php } else { echo $client['client_name']; } ?>
						</td>
                        <td><?php echo $client['website'] ?></td>
                        <td><?php echo $client['region_name'] ?></td>
                        <td><?php echo $client['country_name'] ?></td>
                        <td><?php echo $client['state_name'] ?></td>
                        <td><?php echo $client['location_name'] ?></td>
                        <td><?php echo $client['first_name'] ?></td>
                        <td>
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="clients/add_client/update/<?php echo $client['client_id']; ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'></a>
							<?php } ?>
							<?php if($this->session->userdata('delete')==1) { ?>
								<a class="delete" href="javascript:void(0)" onclick="return checkStatus(<?php echo $client['client_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a> 
							<?php } ?>
						</td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/client/client_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

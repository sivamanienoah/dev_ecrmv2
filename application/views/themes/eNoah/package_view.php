<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	<?php if($this->session->userdata('accesspage')==1){ ?>
	<div class="inner hosting-section">
	
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">Hosting Accounts</h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>package/update'">
							Add New Package Type
						</button>
					</div>
				<?php } ?>
			<div class="clearfix"></div>
			</div>
		</div>
        
		<div id="dialog-pk-msg" class="dialog-err" style="font-size: 13px; font-weight: bold; padding: 0px 0px 10px; text-align: center;"> </div>
        
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            <thead>
                <tr>
                    <th>Package Type Name</th>
                    <th>Months</th>
                    <th>Status</th>
					<th>Action</th>
                </tr>
            </thead>
            <tbody>
			<?php
			if (is_array($accounts) && count($accounts) > 0) { 
				foreach ($accounts as $account) { 
				?>
				<tr>
					<td><?php if($this->session->userdata('edit')==1){ ?><a href="package/update/<?php echo $account['type_id'] ?>"><?php echo $account['package_name'] ?></a><?php } else echo $account['package_name'] ?></td>
					<td><?php echo  $account['type_months'] ?></td>
					<td>
						<?php if ($account['package_flag'] == 'active') echo "<span class=label-success>Active</span>"; else echo "<span class=label-warning>Inactive</span>"; ?>
					</td>
					<td>
						<?php if($this->session->userdata('edit')==1) { ?><a href="package/update/<?php echo $account['type_id'] ?>" title='Edit' ><img src="assets/img/edit.png" alt='edit'></a> <?php } ?>
						<?php if($this->session->userdata('delete')==1) { ?>
						<a class="delete" href="javascript:void(0)" onclick="return checkStatuspk(<?php echo $account['type_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a>
						<?php } ?>
						<?php if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
            </tbody>
        </table>
	</div>
	<?php } else{
			echo "You have no rights to access this page";
		}?>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/package/package_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
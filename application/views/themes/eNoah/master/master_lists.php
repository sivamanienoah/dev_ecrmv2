<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
 
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1){ ?>
		
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Module Database</h2>
			
			<div class="section-right">
				<!--search-->
				<!--div class="form-cont search-table">
					<form id="cust_search_form" name="cust_search_form" action="master/search/" method="post">
						<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<input type="text" name="cust_search" value="<?php echo  $this->uri->segment(4) ?>" class="textfield width200px" />
						<button type="submit" class="positive">Module Search</button>			
					</form>
				</div-->
				<!--search-->
				<!--add-->
				<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons add-new-button">
					<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>master/add_master'">
						Add New Master
					</button>
				</div>
				<?php } ?>
				<!--add-->
			</div>
			<div class="clearfix"></div>
		</div>
        
        <!--table border="0" cellpadding="0" cellspacing="0" class="data-table"-->
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            
            <thead>
                <tr>
                    <th>Module Name</th>
                    <th>Parent Module Name</th>
                    <th>Created By</th>
                    <th>Modified By</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($customers) && count($customers) > 0) { ?>
                    <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1){ ?><a href="master/add_master/update/<?php echo  $customer['masterid'] ?>"><?php echo $customer['master_name']; ?></a><?php } else { echo $customer['master_name']; } ?></td>
                        <td><?php if(!empty($customer['parentname'])){echo $customer['parentname'];}else { echo 'Parent' ;} ?></td>
                        <td>
						 <?php echo  $customer['cfnam'].$customer['clnam']  ; ?>
						</td>   
						<td>
						 <?php echo $customer['mfnam']. $customer['mlnam'] ; ?>
						</td>
                        <td><?php echo  date('d-m-Y', strtotime($customer['created'])); ?></td>
                        <td><?php echo  date('d-m-Y', strtotime($customer['modified']));?></td>
                        <td><?php if($customer['inactive'] ==1) echo 'Inactive';else echo 'Active';?></td>
						<td><?php if($this->session->userdata('edit')==1){ ?><a href="master/add_master/update/<?php echo  $customer['masterid'] ?>"><?php echo "Edit"; ?></a><?php } else { echo "Edit"; } ?> 
						<?php if ($customer['master_parent_id'] != 0) { ?>
						|
						<?php if($this->session->userdata('delete')==1){ ?><a href="master/delete_master/<?php echo  $customer['masterid'] ?>" onclick="return confirm('Are you sure you want to delete?')" ><?php echo "Delete"; ?></a><?php } else { echo "Delete"; } ?> <?php } ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
	</div>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
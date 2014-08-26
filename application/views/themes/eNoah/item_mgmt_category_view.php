<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/item_mgmt/item_mgmt_category_view.js"></script>
<div id="content">
	<div class="inner">
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" class="positive" style="margin:0px 0px 10px 10px;" onclick="location.href='<?php echo base_url(); ?>item_mgmt/category'">
							Add New Category
						</button>
					</div>
				<?php } ?>
			<div class="clearfix"></div>
			</div>
		</div>
				
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            <thead>
                <tr>
                    <th width="10%">Action</th>
                    <th width="90%">Item Description</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($records) && count($records) > 0) { ?>
                    <?php foreach ($records as $record) { ?>
                    <tr>
						<td class="actions">
							<?php if ($this->session->userdata('edit')==1) { ?>
								<a href="item_mgmt/category/update/<?php echo  $record['cat_id'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'></a>
							<?php } ?>
							<?php if ($this->session->userdata('delete')==1) { ?>
								<a class="delete" href="javascript:void(0)" onclick="return deleteCategory(<?php echo $record['cat_id']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'> </a> 
							<?php } ?>
							<?php if(($this->session->userdata('delete')!=1) && ($this->session->userdata('edit')!=1)) echo '-'; ?>
						</td>
                        <td><?php echo $record['cat_name']; ?></td>
                    </tr>
				<?php 
					} 
				} 
				?>
            </tbody>
        </table>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>

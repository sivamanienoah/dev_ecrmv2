<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<div id="content">
	<?php //include 'tpl/item_mgmt_submenu.php' ?>
	<div class="inner q-view">
	<?php if($this->session->userdata('accesspage')==1){?>
		<table><tr>
		<td><h2><?php echo  $page_heading ?></h2></td>
		<?php if($this->session->userdata('add')==1) { ?>
		<td valign="middle";>
			<div class="buttons">
				<button type="button" class="positive" style="margin:0px 0px 10px 10px;" onclick="location.href='<?php echo base_url(); ?>item_mgmt/add'">
					Add New Item
				</button>
			</div>
		</td>
		<?php } ?>
		</tr>
		</table>
		<?php
		$menu = '<ul id="job-view-tabs">';
		$data = '';

		foreach ($categories as $cat)
		{
			$menu .= "<li><a href=\"#cat_{$cat['cat_id']}\">{$cat['cat_name']}</a></li>";
			$records = $cat['records'];
			ob_start();
			?>
		<div id="cat_<?php echo $cat['cat_id'] ?>">
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
				
				<thead>
					<tr>
						<?php if($this->session->userdata('edit')==1){ ?><th>Action</th><?php } ?>
						<th width="80%">Item Description</th>
						<th width="10%">Item Price</th>
					</tr>
				</thead>
				
				<tbody>
					<?php if (is_array($records) && count($records) > 0) { ?>
						<?php foreach ($records as $record) { ?>
						<tr>
							<td class="actions">
							<?php if($this->session->userdata('edit')==1){ ?>
								<a href="item_mgmt/add/update/<?php echo  $record['itemid'] ?>/<?php echo  $table_in_use ?>">Edit &raquo; </a><?php } else { ?> Edit &raquo; <?php } ?> 
							<?php if($this->session->userdata('delete')==1){ ?>	
							  | <a class="delete" href="item_mgmt/item_delete/update/<?php echo $record['itemid'] ?>/<?php echo  $table_in_use ?>" onclick="return confirm('Are you sure you want to delete?')">Delete &raquo;</a> <?php } else { ?> Delete &raquo; <?php } ?> 
							</td>
							<td><?php echo  nl2br($record['item_desc']) ?></td>
							<td>$<?php echo  $record['item_price'] ?></td>
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3" align="center">No records available to be displayed!</td>
						</tr>
					<?php } ?>
				</tbody>
				
			</table>
		</div>
			<?php
			$data .= ob_get_clean();
		}
		$menu .= '</ul>';
		
		echo $menu, $data;
		?>
		
		<?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$("#job-view-tabs").tabs();
    $(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

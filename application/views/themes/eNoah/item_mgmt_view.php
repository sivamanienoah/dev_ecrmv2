<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
	<div class="inner q-view">
	<?php if($this->session->userdata('accesspage')==1) { ?>
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
				<?php if($this->session->userdata('add')==1) { ?>
					<div class="buttons pull-right">
						<button type="button" class="positive" style="margin:0px 0px 10px 10px;" onclick="location.href='<?php echo base_url(); ?>item_mgmt/add'">
							Add New Item
						</button>
					</div>
				<?php } ?>
			<div class="clearfix"></div>
			</div>
		</div>
		
		<?php
		echo '<div id="quote-tabs"><ul id="job-view-tabs">';
		foreach ($categories as $cat) {
			echo '<li><a href="'.current_url().'#cat_'.$cat['cat_id'].'">'.$cat['cat_name'].'</a></li>';
		}
		echo '</ul>';
		foreach ($categories as $cat) {
			$records = $cat['records'];
		?>
		<div id="cat_<?php echo $cat['cat_id'] ?>">
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
				<thead>
					<tr>
						<th>Action</th>
						<th width="80%">Item Description</th>
						<th width="10%">Item Price</th>
					</tr>
				</thead>
				
				<tbody>
					<?php if (is_array($records) && count($records) > 0) { ?>
						<?php foreach ($records as $record) { ?>
						<tr>
							<td class="actions">
							<?php if($this->session->userdata('edit')==1) { ?>
								<a href="item_mgmt/add/update/<?php echo  $record['itemid'] ?>/<?php echo $table_in_use ?>">Edit &raquo; </a><?php } else { ?> Edit &raquo; <?php } ?> 
							<?php if($this->session->userdata('delete')==1) { ?>	
							  | <a class="delete" href="javascript:void(0)" onclick="return deleteItemMgmt(<?php echo $record['itemid']; ?>); return false; "> Delete &raquo; </a> 
							 <?php } ?> 
							</td>
							<td><?php echo nl2br($record['item_desc']) ?></td>
							<td>$<?php echo $record['item_price'] ?></td>
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
		}
		echo '</div>';
		?>
	<?php } else {
		echo "You have no rights to access this page";
	} ?>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$.fn.__tabs = $.fn.tabs;
	$.fn.tabs = function (a, b, c, d, e, f) {
		var base = location.href.replace(/#.*$/, '');
		$('ul>li>a[href^="#"]', this).each(function () {
			var href = $(this).attr('href');
			alert(href);
			$(this).attr('href', base + href);
		});
		$(this).__tabs(a, b, c, d, e, f);
	};
	$("#quote-tabs").tabs();
});
var tbl_in_use = '<?php echo $table_in_use ?>';
</script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/item_mgmt/item_mgmt_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

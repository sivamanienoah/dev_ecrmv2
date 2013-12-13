<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<div id="content">
	<div class="inner">
		<table>
		<tr>
		<td><h2><?php echo  $page_heading ?></h2></td>
		<?php if($this->session->userdata('add')==1) { ?>
		
		<td valign="middle";>
			<div class="buttons">
				<button type="button" class="positive" style="margin:0px 0px 10px 10px;" onclick="location.href='<?php echo base_url(); ?>item_mgmt/category'">
					Add New Category
				</button>
			</div>
		</td>
		<?php } ?>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" class="data-table">
            
            <thead>
                <tr>
                    <th width="20%">Action</th>
                    <th width="80%">Item Description</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($records) && count($records) > 0) { ?>
                    <?php foreach ($records as $record) { ?>
                    <tr>
						<td class="actions">
						<?php if ($this->session->userdata('edit')==1) { ?>
								<a href="item_mgmt/category/update/<?php echo  $record['cat_id'] ?>">Edit &raquo;</a><?php } else { ?> Edit &raquo; <?php } ?>
							<?php if ($this->session->userdata('delete')==1) { ?>
								| <a href="item_mgmt/delete_category/<?php echo  $record['cat_id'] ?>"onclick="return confirm('Are you sure you want to delete?')"> Delete &raquo;</a><?php } ?> 
						</td>
                        <td><?php echo  $record['cat_name'] ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>

	</div>
</div>
<script type="text/javascript">
$(function(){
    $(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

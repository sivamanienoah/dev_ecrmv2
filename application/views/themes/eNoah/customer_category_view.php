<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div id="left-menu">
		<a href="customers/">Customer List</a>
		<a href="customers/add_category/">Add Category</a>
	</div>
    <div class="inner">
        <?php if($this->session->userdata('accesspage')==1) { ?>
        <table><tr>
		<td><h2>Customer Categories</h2></td>
		<?php if($this->session->userdata('add')==1) { ?>
		<td valign="middle";>
			<div class="buttons">
				<button type="button" class="positive" onclick="location.href='customers/add_category'">
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
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if (is_array($categories) && count($categories) > 0) { ?>
                    <?php foreach ($categories as $category) { ?>
                    <tr>
                        <td><?php if($this->session->userdata('edit')==1) { ?><a href="customers/add_category/update/<?php echo  $category['custcatid'] ?>"><?php echo  $category['category_name'] ?></a><?php } else echo $category['category_name']; ?></td>
                        <td><?php echo  $category['cat_comments'] ?></td>
						<td>
							<?php if($this->session->userdata('edit')==1) { ?><a href="customers/add_category/update/<?php echo  $category['custcatid'] ?>"><?php echo "Edit"; ?></a><?php } else echo "Edit"; ?>
							<?php if($this->session->userdata('delete')==1) { ?> | <a href="customers/delete_category/<?php echo  $category['custcatid'] ?>" onclick="return confirm('Are you sure you want to delete?')" ><?php echo "Delete"; ?></a><?php } ?>
						</td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
        <?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
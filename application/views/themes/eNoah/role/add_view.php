<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/role/add_view.js"></script>
<div id="content">

    <div class="inner"> 
	<?php if(($this->session->userdata('add')==1 && ($this->uri->segment(3) != 'update')) || (($this->uri->segment(3) == 'update') && is_numeric($this->uri->segment(4)) && ($this->session->userdata('edit')==1))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="add_role" onsubmit="return chk_role_name();">
		
		   <input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Role Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td>Role name:</td>
					<td>
						<input type="text" name="name" id="role_name" value="<?php echo $this->validation->name ?>" class="textfield width200px required" /> *
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="role_id_hidden" name="role_id_hidden" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
						
					</td>
					<td><div id="role_msg"> </div></td>					
				</tr>			 
				<tr> 
					<td>Modules:</td>
					<td colspan="2"><?php echo $pageTree; ?></td>
				</tr>			 			   
				<tr>
					<td>Inactive Role:</td>
					<td colspan="2">
						<input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more User currently assigned for this Role. This cannot be made Inactive."; ?>
						<?php if (($this->validation->inactive == 0) && ($cb_status == 0)) echo "Check if the Role need to be Inactive."; ?>
						<?php if ($this->validation->inactive != 0) echo "Uncheck if the Role need to be Active."; ?>
					</td>
				</tr>
				<tr></tr>
				<tr></tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Role
							</button>
						</div>
						<div class="buttons">
							<button type="button" class="negative" onclick="location.href='role'">
								Cancel
							</button>
                        </div>
                    </td>                     
				</tr>
            </table>
		</form>
	</div>
	<?php 
	} else {
		echo "You have no rights to access this page";
	}
	?>
</div>
 
<?php require (theme_url(). '/tpl/footer.php'); ?>


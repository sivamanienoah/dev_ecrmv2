<?php require (theme_url().'/tpl/header.php'); ?>
 
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
 
 
<script type="text/javascript">
$(document).ready(function() {
 $('.check').click(function() { 
        if ($(this).is(':checked')) {
		    $(this).parent().find('input:checkbox').attr('checked', 'checked');
        }else{
		 $(this).parent().find('input:checkbox').attr('checked', '');
		}
    });

});
</script> 	
<div id="content">
 
    <div class="inner"> 
	<?php if(($this->session->userdata('add')==1 && ($this->uri->segment(3) != 'update')) || (($this->uri->segment(3) == 'update') && is_numeric($this->uri->segment(4)) && ($this->session->userdata('edit')==1))) { ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
		   <input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Role Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">Role name:</td>
					<td width="240"><input type="text" name="name" value="<?php echo  $this->validation->name ?>" class="textfield width200px required" /> *</td>					 
				</tr>			 
					<tr> <td>Modules:</td><td><?php	echo $pageTree;	?></td></tr>			 			   
				<tr>
					<td>Inactive Role:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1" <?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the role is inactive .</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active Role cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your Role level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Role
							</button>
						</div>
                    </td>                     
					<?php } ?>
				</tr>
            </table>
		</form>
	</div>
	<?php } else{
	echo "You have no rights to access this page";
}?>
</div>
 

<?php require (theme_url(). '/tpl/footer.php'); ?>


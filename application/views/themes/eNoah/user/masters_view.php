<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
    <div id="left-menu">
       
		<a href="user/<?php echo  $this->session->userdata('customer_list_no') ?>">Back To List</a>
	</div>
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Master Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">First name:</td>
					<td width="240"><input type="text" name="first_name" value="<?php echo  $this->validation->first_name ?>" class="textfield width200px required" /> *</td>
					 
				</tr>
				  
				   
				<tr>
					<td>Status:</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the master is inactive .</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active Master cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your Master level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Master
							</button>
						</div>
                    </td>
                    <td colspan="2">
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && 1 == 1) { # 1 == 2 do not delete users ?>
                        <div class="buttons">
                            <button type="submit" name="delete_user" class="negative" onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Master
                            </button>
                        </div>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>
					<?php } ?>
				</tr>
            </table>
		</form>
	</div>
</div>
<?php require (APPPATH . 'views/tpl/footer.php'); ?>

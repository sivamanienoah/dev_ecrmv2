<?php require (theme_url().'/tpl/header.php'); ?>
 <?php //echo '<pre>'; print_r($masters); echo '</pre>'; ?>
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('add')==1 && $this->uri->segment(3) != 'update') || (($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) && ($this->session->userdata('edit')==1))){ ?>
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Module Details</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
					<td width="100">Parent Module name:</td>
					<td width="240">
				 
					<select id="master_parent_id" name="master_parent_id"><option value="">Select</option><option value="0"<?php if($this->validation->master_parent_id== 0 ) { echo 'selected="selected"'; }  ?>>Parent</option><?php if (is_array($masters) && count($masters) > 0) { ?>
                    <?php 
					
					foreach ($masters as $master) { ?><option value="<?php echo $master['masterid']; ?>"  <?php if($this->validation->master_parent_id==$master['masterid']) { echo 'selected="selected"'; }  ?> ><?php echo $master['master_name']; ?> </option><?php } } ?></select> *
					
					 </td>
				 
				</tr>
				<tr>
					<td width="100">Module name:</td>
					<td width="240"><input type="text" name="master_name" value="<?php echo  $this->validation->master_name ?>" class="textfield width200px required" /> *</td>
				 
				</tr>
				<tr>
					<td width="100">Links To:</td>
					<td width="240"><input type="text" name="links_to" value="<?php echo  $this->validation->links_to ?>" class="textfield width200px required" /> *</td>
				 
				</tr>	
				<tr>
					<td width="100">Label:</td>
					<td width="240"><input type="text" name="controller_name" value="<?php echo  $this->validation->controller_name ?>" class="textfield width200px required" /> *</td>
				 
				</tr>	
				<tr>
					<td width="100">Order:</td>
					<td width="240">
					<select id="order_id" name="order_id"><option value="0">Select</option> 
                    <?php 
					
					for($i=1;$i<100;$i++) { ?><option value="<?php echo $i; ?>" <?php if($i==$this->validation->order_id) { echo 'selected="selected"'; } ?>><?php echo $i; ?> </option><?php }  ?></select> * 
					</td>				 
				</tr>
		 
				<tr>
					<td>Inactive :</td>
					<td colspan="3"><input type="checkbox" name="inactive" value="1"<?php if ($this->validation->inactive == 1) echo ' checked="checked"' ?> /> Check if the module is inactive.</td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<?php if (isset($this_user) && $userdata['userid'] == $this_user) { ?>
					<td colspan="3">
						Active Module cannot be modified! Please use my account to update your details.
					</td>
					<?php } else if (isset($this_user_level) && $userdata['level'] >= $this_user_level && $userdata['level'] != 0) { ?>
					<td colspan="3">
						Your Module level cannot updated similar levels or levels above you.
					</td>
					<?php } else { ?>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive">
								
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Module
							</button>
						</div>
                    </td>
                    <td colspan="2">
                        <?php if ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4)) && 1 == 1) { # 1 == 2 do not delete users ?>
                        <?php if($this->session->userdata('delete')==1){ ?>
						<!--<div class="buttons">
                            <button type="submit" name="delete_user" class="negative"  onclick="if (!confirm('Are you sure?\nThis action cannot be undone!')) { this.blur(); return false; }">
                                Delete Module
                            </button>
                        </div>-->
						<?php } ?>
                        <?php } else { echo "&nbsp;"; } ?>
                    </td>
					<?php } ?>
				</tr>
            </table>
		</form>
		<?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<?php require (theme_url(). '/tpl/footer.php'); ?>
<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/manage_service/manage_service_req_add.js"></script>
<div id="content">
    <div class="inner">

    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="cat_name" onsubmit="return chk_cat_name();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Product / Service </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Product: * </td>
					<td>
						<input type="text" name="services" id="category_name" value="<?php echo $this->validation->services; ?>" class="textfield width200px" />
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="category_hidden" name="category_hidden" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
					</td>
					<td>
						<div id="cat_name_msg"> </div>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td colspan="2">
						<input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more leads currently assigned for this Service. This cannot be made Inactive."; ?>
						<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the Source need to be Inactive."; ?>
						<?php if ($this->validation->status != 1) echo "Check if the Source need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_pdt" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Product
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>manage_service'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<?php require (theme_url(). '/tpl/footer.php'); ?>

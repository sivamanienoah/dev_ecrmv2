<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">

    	<form action="<?php echo $this->uri->uri_string() ?>" method="post" name="add_billing_type" onsubmit="return chk_billing_type();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Billing Type </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Project Billing Type: * </td>
					<td>
						<input type="text" name="project_billing_type" id="project_billing_type" value="<?php echo $this->validation->project_billing_type; ?>" class="textfield width200px" />
						<?php if ($this->uri->segment(3) == 'update') { ?>
							<input type="hidden" id="id" name="id" value="<?php echo $this->uri->segment(4); ?>" />
						<?php } ?>
					</td>
					<td>
						<div id="succes_err_msg"></div>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td colspan="2">
						<input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo ' checked="checked"' ?>
						<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>> 
						<?php if ($cb_status != 0) echo "One or more Projects currently assigned for this type. This cannot be made Inactive."; ?>
						<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the type need to be Inactive."; ?>
						<?php if ($this->validation->status != 1) echo "Check if the type need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons">
							<button type="submit" name="update_project_billing_type" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?>
							</button>
						</div>
						<div class="buttons">
                           <button type="button" class="negative" onclick="location.href='<?php echo base_url(); ?>manage_project_billing_type'">
								Cancel
							</button>
                        </div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script type="text/javascript" src="assets/js/manage_project_billing_type/manage_project_billing_add_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>

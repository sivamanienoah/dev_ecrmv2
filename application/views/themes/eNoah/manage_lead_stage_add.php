<?php require (theme_url() . '/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/manage_lead_stage/manage_lead_stage_add.js"></script>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" name="form" onsubmit="return chk_lead_stg();">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add New' ?> Lead Stage </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
			<?php if($this->session->userdata('add')==1) { ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Lead Stage: * </td>
					<td>
						<input type="text" name="lead_stage_name" id="lead_stage_name" autocomplete="off" value="<?php echo $this->validation->lead_stage_name; ?>" class="textfield width200px" />
					</td>
					<?php if ($this->uri->segment(3) == 'update') { ?>
						<input type="hidden" id="lead_stg_hidden" name="lead_stg_hidden" value="<?php echo $this->uri->segment(4); ?>" />
					<?php } ?>
					<td><div id="lead_stg_msg"></div></td>
				</tr>
				<tr>
					<td>Status</td>
					<td colspan="2">
					<input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo 'checked="checked"' ?>
					<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>>
					<?php if ($cb_status != 0) echo "One or more leads currently assigned for this Lead Stage. This cannot be made Inactive."; ?>
					<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the Lead Stage need to be Inactive."; ?>
					<?php if ($this->validation->status != 1) echo "Check if the Lead Stage need to be Active."; ?>
					</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons" colspan="2">
                        <div class="buttons" style="margin:20px">
							<button type="submit" name="update_pdt" class="positive">
								<?php echo  ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Lead Stage
							</button>
						</div>
                    </td>
				</tr>
				</table>
				
			<?php } else { echo "You have no rights to access this page"; } ?>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->

<?php require (theme_url(). '/tpl/footer.php'); ?>
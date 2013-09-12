<?php require (APPPATH . 'views/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
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
					<td><input type="text" name="lead_stage_name" value="<?php echo $this->validation->lead_stage_name; ?>" class="textfield width200px" /></td>
				</tr>
				<!--<tr>
                    <td>Sequence: * </td>
					<td><select name = "sequence" style="width:70px">
							<option value="">Select </option>
							<?php for($i=1;$i<51;$i++) { ?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select> 
					</td>
				</tr>-->
				<tr>
                    <td>Is Sale: </td>
					<td><input type="checkbox" name="is_sale" value="1"></td>
				</tr>
				<tr>
					<td>Status</td>
					<td>
					<input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo 'checked="checked"' ?>
					<?php if ($cb_status != 0) echo 'disabled="disabled"' ?>>
					<?php if ($cb_status != 0) echo "One or more leads currently assigned for this Lead Stage. This cannot be made Inactive."; ?>
					<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the Lead Stage need to be Inactive."; ?>
					<?php if ($this->validation->status != 1) echo "Check if the Lead Stage need to be Active."; ?>
					</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
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
<?php require (APPPATH . 'views/tpl/footer.php'); ?>

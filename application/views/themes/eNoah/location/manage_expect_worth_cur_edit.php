<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
		<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Currency Type </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>All mandatory fields marked * must be filled in correctly.</p>
			<table class="layout">
				<tr>
                    <td>Currency Type:  </td>
					<td><input type="text" name="expect_worth_name" id="expect_worth_name" value="<?php echo $this->validation->expect_worth_name ?>" class="textfield" readonly></td>
				</tr>
				<tr>
					<td>Status: </td>
					<td><input type="checkbox" name="status" value="1" <?php if ($this->validation->status == 1) echo ' checked="checked"' ?> <?php if (($cb_status != 0) || ($this->validation->is_default == 1)) echo 'disabled="disabled"' ?> onchange="toggleCheckbox(this)" >
					<?php if ($cb_status != 0) echo "One of more leads currently mapped to this Currency. This cannot be made Inactive."; ?>
					<?php if (($this->validation->status == 1) && ($cb_status == 0)) echo "Uncheck if the Source need to be Inactive."; ?>
					<?php if ($this->validation->status != 1) echo "Check if the Source need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>Default Currency: </td>
					<td><input type="checkbox" name="is_default" id="is_default" value="1" <?php if ($this->validation->is_default == 1) { echo ' checked="checked"' ?> onchange="check_is_default(this)" <?php } ?> <?php if ($this->validation->status != 1) echo 'disabled="disabled"' ?> >
					<?php if ($this->validation->is_default == 1) echo "Uncheck if the Currency not need to be Default."; ?>
					<?php if ($this->validation->is_default != 1) echo "Check if the Currency need to be Default."; ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Currency Type
							</button>
						</div>
                    </td>
				</tr>
            </table>
		</form>
	</div><!--Inner div close-->
</div><!--Content div close-->
<?php require (theme_url(). '/tpl/footer.php'); ?>
<script type="text/javascript">
	document.getElementById("is_default").disabled = true;
	function toggleCheckbox(obj) {
		// if(obj.checked) { document.getElementById("is_default").disabled = false; }
		// else { document.getElementById("is_default").disabled = true; }
	}
	
	function check_is_default(obj) {
		if(!obj.checked) {
			alert("You cannot uncheck the default currency.\nAtleast one currency must be Default.");
			$('#is_default').attr('checked','checked');
		}
	}
</script>
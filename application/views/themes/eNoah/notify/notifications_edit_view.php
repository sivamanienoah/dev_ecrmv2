<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Cron </h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p></p>
			<table class="layout">
				<tr>
                    <td>Cron Name:  </td>
					<td><input type="text" name="cron_name" id="cron_name" value="<?php echo $this->validation->cron_name ?>" class="textfield width200px" readonly ></td>
				</tr>
				<tr>
					<td>Onscreen Notify: </td>
					<td><input type="checkbox" name="onscreen_notify_status" value="1" <?php if ($this->validation->onscreen_notify_status == 1) echo ' checked="checked"' ?> >
					<?php if ($this->validation->onscreen_notify_status == 1) echo "Uncheck if the Onscreen Notifications need to be Inactive."; ?>
					<?php if ($this->validation->onscreen_notify_status != 1) echo "Check if the Onscreen Notifications need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>Email Notify: </td>
					<td><input type="checkbox" name="email_notify_status" value="1" <?php if ($this->validation->email_notify_status == 1) echo ' checked="checked"' ?> >
					<?php if ($this->validation->email_notify_status == 1) echo "Uncheck if the Email Notifications need to be Inactive."; ?>
					<?php if ($this->validation->email_notify_status != 1) echo "Check if the Email Notifications need to be Active."; ?>
					</td>
				</tr>
				<tr>
					<td>No of Days: </td>
					<td>
						<!--<select name="no_of_days" id="no_of_days" class="textfield width100px" <?php #if ($this->validation->status != 1) echo 'disabled="disabled"' ?>>-->
						<select name="no_of_days" id="no_of_days" class="textfield width100px" >
							<option value="">--Select--</option>
							<option value="1"<?php if($this->validation->no_of_days == 1) echo 'selected="selected"' ?>>One Day</option>
							<option value="7"<?php if($this->validation->no_of_days == 7) echo 'selected="selected"' ?>>One Week</option>
							<option value="30"<?php if($this->validation->no_of_days == 30) echo 'selected="selected"' ?>>One Month</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="action-buttons">
                        <div class="buttons">
							<button type="submit" name="update_item" class="positive">
								<?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Cron
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
	function toggleCheckbox(obj) {
		if(obj.checked) 
			document.getElementById("no_of_days").disabled = false;
		else 
			document.getElementById("no_of_days").disabled = true;
	}
</script>
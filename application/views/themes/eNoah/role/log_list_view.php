<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	<?php
	if ($userdata['level'] < 2)
	{
		?>
    <div id="left-menu">
		<a href="user">Back To List</a>
	</div>
	<?php
	}
	?>
    <div class="inner">
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="">
		
		   <input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Log Details for <?php echo $log_user_name, ' on ', $current_log_date ?></h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
			<table class="layout">
				<tr>
					<td>
						<input type="text" class="textfield pick-date" name="log_date" style="margin-bottom:0;" value="Select Date" onfocus="if (this.value == this.defaultValue) this.value = '';" onblur="if (this.value == '') this.value = this.defaultValue;" />
					</td>
					<td>
						<div class="buttons">
							<button type="submit" name="new_date" class="positive">
								
								Get Logs &amp; Times
							</button>
						</div>
					</td>
					<td>
						<h4>
						<?php echo $total_time_spent ?>
						</h4>
					</td>
	
				</tr>
            </table>
		</form>
		<div class="log-container">
			<?php echo $log_set ?>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: 0});
});
</script>
<?php require (APPPATH . 'views/tpl/footer.php'); ?>

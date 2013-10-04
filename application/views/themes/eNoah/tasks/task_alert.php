<?php require ( theme_url(). '/tpl/header.php'); ?>

<?php
$id = empty($res->id)?'':$res->id;
$days = empty($res->task_alert_days)?'':$res->task_alert_days;
$days = ($this->input->post('days')!='')?$this->input->post('days'):$days;
?>
<div id="content">
	 
    <div class="inner">
	<?php if($this->session->userdata('edittask')==1){?>
		
    	<form action="<?php echo  $this->uri->uri_string() ?>" method="post" onsubmit="return checkForm();">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
            <h2>Task Alert</h2>
            <?php if ($this->validation->error_string != '') { ?>
            <div class="form_error">
                <?php echo  $this->validation->error_string ?>
            </div>
            <?php } ?>
            <p>Configure the task alerts.</p>
			<table class="layout">
				<tr>
					<td width="100">Day(s)</td>
					<td width="240"><input type="text" name="days" id = 'days' value="<?php echo  $days; ?>" class="textfield width200px required" /> *</td>
					<input type = 'hidden' name = 'hid_days' value = '<?php echo  $id; ?>' />
				</tr>
				
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="update_user" class="positive" value = 'update'>Update</button>
						</div>
                    </td>
                    <td colspan="2">
                        &nbsp;
                    </td>
				</tr>
            </table>
		</form>
		
		<?php } else{
	echo "You have no rights to access this page";
}?>
	</div>
</div>
<script type="text/javascript">
function showPassFields(obj) {
	if (obj.attr('checked')) {
		$('.hide-passwords td:hidden').css('visibility', 'visible');
	} else {
		$('.hide-passwords td:visible').css('visibility', 'hidden');
	}
}
$(function(){
	$('.hide-passwords td').css('visibility', 'hidden');
	
	$('#add_log').click(function(){
		var log_details = $.trim($('#standalone_log').val());
		var log_time = $.trim($('#time_spent').val());
		
		if (log_details == '')
		{
			alert('Please fill content for your log');
			return false;
		}
		
		if (log_time == '' || log_time == 0)
		{
			alert('Please add time');
			return false;
		}
		
		$.post(
			'myaccount/add_log',
			{'log_content': log_details, 'time_spent': log_time},
			function (data)
			{
				if (data.error)
				{
					alert(data.error);
				}
				else
				{
					$('#time_spent').val('');
					$('#standalone_log').val('');
					$('.time-log-form:visible').slideUp('normal');
				}
				return false;
			},
			'json'
		);
	});
});
</script>
<?php require (APPPATH . 'views/tpl/footer.php'); ?>

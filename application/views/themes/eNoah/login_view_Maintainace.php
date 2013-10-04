<?php require ('tpl/header.php'); ?>

<div id="content">
	<div class="inner"> <h1>Site is under Construction.</h1>
	    <?php //if (isset($notallowed) && $notallowed === true) { ?>
			<h2><?php //if ($msg = $this->session->flashdata('access_error')) echo $msg; else echo 'You do not have access to this area of the site!' ?></h2>
		<?php //} else { ?>
		<!--
		<h2>Login</h2>
		
		<form action="userlogin/process_login/" method="post">
			
			<table class="layout">
				<tr>
					<td class="width100px">Username:</td>
					<td><input name="email" type="text" class="textfield width200px" id="email" value="<?php echo $this->input->post('email')?>" /></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input name="password" type="password" class="textfield width200px" id="password" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<div class="buttons">
							<button type="submit" class="positive">
								
								Submit
							</button>
						</div>
						<input type="hidden" name="last_url" value="<?php echo  $this->session->flashdata('last_url') ?>" />
					</td>
				</tr>
			</table>
		</form> -->
		<?php// } ?>
	</div>
</div>
<script type="text/javascript">
window.onload = function() {
	document.forms[0].email.focus();
}
</script>
<?php require ('tpl/footer.php'); ?>

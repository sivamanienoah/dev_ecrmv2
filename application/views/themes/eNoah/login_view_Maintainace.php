<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	<div class="inner"> <h1>Site is under Construction.</h1>
	    <?php //if (isset($notallowed) && $notallowed === true) { ?>
			<h2><?php //if ($msg = $this->session->flashdata('access_error')) echo $msg; else echo 'You do not have access to this area of the site!' ?></h2>
		<?php //} else { ?>
		
		<?php// } ?>
	</div>
</div>
<script type="text/javascript">
window.onload = function() {
	document.forms[0].email.focus();
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

<?php require (theme_url().'/tpl/header.php'); ?>
<?php //echo "<pre>"; $arr = implode(",", $dupsemail);print_r( $arr);exit; ?>
<div id="content">
    <div class="inner">
		<?php echo "$succcount leads(s) Succesfully Uploaded"; ?>
		<br />
		<?php echo "$updated_leads leads(s) Succesfully Updated"; ?>
	</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
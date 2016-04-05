<?php require (theme_url().'/tpl/header.php'); ?>
<?php //echo "<pre>"; $arr = implode(",", $dupsemail);print_r( $arr);exit; ?>
<div id="content">
    <div class="inner">
		<?php echo "$succcount leads(s) Succesfully Uploaded"; ?>
		<br />
		<?php echo "$updated_leads leads(s) Succesfully Updated"; ?>
		
		<?php 
		echo "<br><br>";
		if(!empty($empty_service)){
			$invalid_service = implode('<br/>', $empty_service);
			echo " The following leads having <b>Invalid Service</b> <br /> $invalid_service";
		}
		echo "<br>";
		if(!empty($empty_source)){
			$invalid_source = implode('<br/>', $empty_source);
			echo " The following leads having <b>Invalid Source</b> <br /> $invalid_source";
		}
		echo "<br>";
		if(!empty($empty_currency)){
			$invalid_currency = implode('<br/>', $empty_currency);
			echo " The following leads having <b>Invalid currency type</b> <br /> $invalid_currency";
		}
		echo "<br>";
		if(!empty($empty_entity)){
			$invalid_entity = implode('<br/>', $empty_entity);
			echo " The following leads having <b>Invalid entity</b> <br /> $invalid_entity";
		}
		echo "<br>";
		if(!empty($empty_stages)){
			$invalid_stages = implode('<br/>', $empty_stages);
			echo " The following leads having <b>Invalid stages</b> <br /> $invalid_stages";
		}
		echo "<br>";
		if(!empty($empty_errors)){
			$invalid_errs = implode('<br/>', $empty_errors);
			echo " <b> Mandatory fields are missing </b> for the following leads <br /> $invalid_errs";
		}
		?>
	</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
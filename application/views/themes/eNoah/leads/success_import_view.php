<?php require (theme_url().'/tpl/header.php'); ?>
<?php //echo "<pre>"; $arr = implode(",", $dupsemail);print_r( $arr);exit; ?>
<div id="content">
    <div class="inner">
		<?php echo "$succcount lead(s) Succesfully Uploaded"; ?>
		<br />
		<?php echo "$updated_leads lead(s) Succesfully Updated"; ?>
		
		<?php 
		echo "<br><br>";
		if(!empty($empty_source)){
			$invalid_source = implode('<br/>', $empty_source);
			echo " The following leads having <b>Invalid Source</b> <br /> $invalid_source";
		}
		echo "<br>";
		if(!empty($empty_service)){
			$invalid_service = implode('<br/>', $empty_service);
			echo " The following leads having <b>Invalid Service</b> <br /> $invalid_service";
		}
		echo "<br>";
		if(!empty($empty_industry)){
			$invalid_industry = implode('<br/>', $empty_industry);
			echo " The following leads having <b>Invalid industry</b> <br /> $invalid_industry";
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
		if(!empty($empty_status)){
			$invalid_status = implode('<br/>', $empty_status);
			echo " The following leads having <b>Invalid status</b> <br /> $invalid_status";
		}
		echo "<br>";
		if(!empty($empty_errors)){
			$invalid_errs = implode('<br/>', $empty_errors);
			echo " <b> Mandatory fields are missing </b> for the following leads <br /> $invalid_errs";
		}
		echo "<br>";
		if(!empty($no_access)){
			$no_acces = implode('<br/>', $no_access);
			echo " You have <b>No rights</b> to update the following leads <br /> $no_acces";
		}
		
		?>
	</div>
<?php require (theme_url().'/tpl/footer.php'); ?>
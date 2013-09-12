<?php require ('tpl/header.php'); ?>
<div id="content">
	<div class="inner">
	<?php 
		if ($successmail>0) {
			echo $successmail . " " . $res; 
			echo "<br >";
		} else {
			echo $res;
		}	
		//echo $failmail . "Not sent";
	?>
	</div>
</div>	
<?php require ('tpl/footer.php'); ?>
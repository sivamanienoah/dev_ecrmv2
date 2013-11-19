<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/tablesort.pager.js"></script>

<style>
.ui-tabs-panel {
	border-top:1px solid #999;
	padding-top:10px;
}
</style>
<?php
switch ($tabselected)
{
	case 'region':
		$selected = 0;
	break;
	case 'country':
		$selected = 1;
	break;
	case 'state':
		$selected = 2;
	break;
	case 'location':
		$selected = 3;
	break;

}
?>
<script>
	$(function() {
		$( "#regset-tabs" ).tabs({ active: <?php echo $selected;?> });
	});
</script>							
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('accesspage')==1) || ($this->session->userdata('add')==1) || (($this->session->userdata('edit')==1))) { ?>
			<p id="temp">&nbsp;</p>
			<div id="regset-tabs">
				<ul id="job-view-tabs">
					<li><a href="regionsettings/region">Region</a></li>
					<li><a href="regionsettings/country">Country</a></li>
					<li><a href="regionsettings/state">State</a></li>
					<li><a href="regionsettings/location">Location</a></li>
				</ul>
			</div>
			
	<?php } else {
	 echo "You have no rights to access this page";
	}
	?>
	</div>
	 
</div>

<?php require (theme_url(). '/tpl/footer.php'); ?>

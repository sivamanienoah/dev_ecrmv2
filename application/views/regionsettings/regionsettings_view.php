<?php require (APPPATH . 'views/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>

<style>
.ui-tabs-panel {
	border-top:1px solid #999;
	padding-top:10px;
}
</style>
<?php
if($tabselected == 'region') {
$selected = 0;
}elseif($tabselected == 'country') {
$selected = 1;
}elseif($tabselected == 'state') {
$selected = 2;
}elseif($tabselected == 'location') {
$selected = 3;
} 

?>
<script>
$(function() {
	$("#job-view-tabs").tabs({
	selected: <?php echo $selected;?>,
	show: function (event, ui) {
	    }
	});
							
});
</script>							
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('accesspage')==1) || ($this->session->userdata('add')==1) || (($this->session->userdata('edit')==1))) { ?>
			<p id="temp">&nbsp;</p>
			<ul id="job-view-tabs">
				
				<li><a href="regionsettings/region/">Region</a></li>
				<li><a href="regionsettings/country">Country</a></li>
				<li><a href="regionsettings/state">State</a></li>
				<li><a href="regionsettings/location">Location</a></li>
			</ul>	
				
			
	<?php } else {
	 echo "You have no rights to access this page";
	}
	?>
	</div>
	 
</div>

<?php require (APPPATH . 'views/tpl/footer.php'); ?>

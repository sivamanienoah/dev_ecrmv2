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
/* $(function() {
	$("#job-view-tabs").tabs({
	selected: <?php echo $selected;?>,
	show: function (event, ui) {
	    }
	});
});
*/
$(function() {
$.fn.__tabs = $.fn.tabs;
	$.fn.tabs = function (a, b, c, d, e, f) {
		var base = location.href.replace(/#.*$/, '');
		$('ul>li>a[href^="#"]', this).each(function () {
			var href = $(this).attr('href');
			$(this).attr('href', base + href);
		});
		$(this).__tabs(a, b, c, d, e, f);
	};

	$( "#regset-tabs" ).tabs(('select', 2),{});
});
</script>							
<div id="content">
    <div class="inner">
	<?php if(($this->session->userdata('accesspage')==1) || ($this->session->userdata('add')==1) || (($this->session->userdata('edit')==1))) { ?>
			<p id="temp">&nbsp;</p>
			<div id="regset-tabs">
				<ul id="job-view-tabs">
					<li><a href="regionsettings/region/">Region</a></li>
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

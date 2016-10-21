<script type="text/javascript">
var uc_all_graph_data = <?php echo json_encode($uc_graph_val) ?>;
// var uc_all_graph_data = <?php echo json_encode($uc_graph_val, JSON_PRETTY_PRINT) ?>;
</script>
<div class="pull-left overall_container">
	<h5 class="dash-tlt">Over all - <?php echo $uc_graph_val['total']['ytd_billable'] . "%"; ?></h5>
	<div id="total" class="plot"></div>
</div>
<div class="pull-right chlid_container clearfix">
	<h5 class="dash-tlt">Practice wise</h5>
	<?php
		unset($uc_graph_val['total']);
		$i = 1;
		foreach($uc_graph_val as $key=>$val) {
		?>
		<div class="graph_box">
			<div id="<?php echo $key; ?>" style="height: 150px !important; width: 202px !important;"></div>
		</div>
		<?php if($i == 3 || $i == 6) { ?>
		<div class="clear"></div>
		<?php } ?>
		<?php
		$i++;
		}
	?>
</div>
<script type="text/javascript">
var uc_graph_data = <?php echo json_encode($uc_graph_val) ?>;
// var uc_graph_data = <?php echo json_encode($uc_graph_val, JSON_PRETTY_PRINT) ?>;
</script>
<script type="text/javascript" src="assets/js/projects/graphical_box_uc.js"></script>
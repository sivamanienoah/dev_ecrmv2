<h5 class="dash-tlt">
<span class="forecast-heading">Entitywise Actuals</span>
<div class="forecast-details">
	<span class="from"><strong>Period: </strong> <?php echo date("M Y", strtotime($current_month)); ?>
	<strong> -</strong> <?php echo date("M Y", strtotime($highest_month)); ?></span>
	<a onclick="showFilter('A'); return false;" class="white-filter"></a>
</div>
</h5>
<div id="actual_entity_chart" class="plot" style="width:650px"></div>
<!--div id="forecast_entity_chart_img"><button type="button">PDF</button></div-->
<?php 
	foreach($actual_entity as $a_ent_name=>$a_ent_val){
		$actual_entity_value[] = "['".$a_ent_name.'('.$a_ent_val.' '.$default_currency.')'."'".','.$a_ent_val."]";
	}
	$actual_entity_values = implode(',', $actual_entity_value);
?>
<script type="text/javascript">
	var actual_entity_values 			   = [<?php echo $actual_entity_values ?>];
	var actual_entity_month_year_from_date = "<?php echo $filter['month_year_from_date'] ?>";
	var actual_entity_month_year_to_date   = "<?php echo $filter['month_year_to_date'] ?>";
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_entity_actual.js"></script>
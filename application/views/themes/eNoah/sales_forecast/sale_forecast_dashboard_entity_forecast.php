<h5 class="dash-tlt">
<span class="forecast-heading">Entitywise Forecast</span>
<div class="forecast-details">
	<span class="from"><strong>Period: </strong> <?php echo date("M Y", strtotime($current_month)); ?><strong> -</strong> <?php echo date("M Y", strtotime($highest_month)); ?></span>
	<a onclick="showFilter('F'); return false;" title="Date Filter" class="date-icon-filter"></a></div>
</h5>
<div id="forecast_entity_chart" class="plot" style="width:650px"></div>
<!--div id="forecast_entity_chart_img"><button type="button">PDF</button></div-->
<?php 
	foreach($forecast_entity as $f_ent_name=>$f_ent_val) {
		$forecast_entity_value[] = "['".$f_ent_name.'('.$f_ent_val.' '.$default_currency.')'."'".','.$f_ent_val."]";
	}
	$forecast_entity_values = implode(',', $forecast_entity_value);
?>
<script type="text/javascript">
	var forecast_entity_values 				   = [<?php echo $forecast_entity_values ?>];
	var forecast_entity_month_year_from_date   = "<?php echo $filter['month_year_from_date'] ?>";
	var forecast_entity_month_year_to_date     = "<?php echo $filter['month_year_to_date'] ?>";
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_entity_forecast.js"></script>
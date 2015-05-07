<?php $this->load->helper('text'); ?>
<div class="clearfix">
	<div class="pull-left">
		<h5 class="dash-tlt">Sales Forecast - Entity</h5>
		<div id="forecast-entity-chart" class="plot" style="width:650px"></div>
		<!--div id="forecast-entity-chart-img"><button type="button">PDF</button></div-->
	</div>
	<div class="pull-right">
		<h5 class="dash-tlt">Sales Forecast - Comparison</h5>
		<div id="forecast-compare-chart" class="plot" style="width:450px"></div>
		<!--div id="funnelimg"><button type="button">PDF</button></div-->
	</div>
</div>

<div class="clearfix"></div>

<div id="charts_info_export" class="dash-section dash-section1" style="display:none;">
	<div class="export-to-excel">
		<input type='hidden' id='entity_name' name='entity_name' value=''>
		<a id="entity-export">Export to Excel</a>
	</div>
	<h5 id='entity-tag'>Testing</h5>
	<div class="grid-close"></div>
</div>

<div class="clearfix"></div>	

<div id="charts_info" class="" style="margin: 10px 0px 0px; display:none; width:auto;">

</div>

<?php
	foreach($entity_data as $ent_name=>$ent_val){
		$entity_values[] = "['".$ent_name.'('.$ent_val.' '.$default_currency.')'."'".','.$ent_val."]";
	}
	$entity_values = implode(',', $entity_values);

	$i = date("Y-m", strtotime($current_month)); 
	while($i <= date("Y-m", strtotime($highest_month))) {
		// $month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
		$month_no_arr[] = '"'.date('Y-m', strtotime($i)).'"'; // using for graph dataClick
		
		// $x_axis_values[] = "['".date('M', strtotime($i))."']";
		$x_axis_values[]   = '"'.date('M', strtotime($i)).'"';
		$forecast_values[] = isset($compare_data[date('Y-m', strtotime($i))]['F']) ? $compare_data[date('Y-m', strtotime($i))]['F'] : 0;
		$actual_values[]   = isset($compare_data[date('Y-m', strtotime($i))]['A']) ? $compare_data[date('Y-m', strtotime($i))]['A'] : 0;
		
		if(substr($i, 5, 2) == "12")
		$i = (date("Y", strtotime($i."-01")) + 1)."-01";
		else
		$i++;
		
		if(substr($i, 5, 2) == "9")
		break;
	}

	$x_axis_values   = implode(',', $x_axis_values);
	$forecast_values = implode(',', $forecast_values);
	$actual_values   = implode(',', $actual_values);
	$month_no_arr    = implode(',', $month_no_arr);
	?>
<script type="text/javascript">
	var entity_values   = [<?php echo $entity_values ?>];
	var x_axis_values   = [<?php echo $x_axis_values ?>];
	var forecast_values = [<?php echo $forecast_values ?>];
	var actual_values   = [<?php echo $actual_values ?>];
	var month_no_arr    = [<?php echo $month_no_arr ?>];
	var currency_name   = ['<?php echo $default_currency ?>'];
</script>

<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_graph_js.js"></script>
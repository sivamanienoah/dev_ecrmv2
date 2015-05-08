<h5 class="forecast_chartbar">
	<span class="forecast-heading">Forecast Vs Actuals</span>
	<div class="forecast-details"> 
		Advanced Filter<a onclick="showFilter('FA'); return false;" class="white-filter"></a>
	</div>
</h5>
<div id="forecast_compare_chart" class="plot"></div>
<!--div id="forecast_entity_chart_img"><button type="button">PDF</button></div-->
<?php 
	$i = date("Y-m", strtotime($compare_from_month)); 
	while($i <= date("Y-m", strtotime($compare_to_month))) {
		// $month_arr[date('Y-m', strtotime($i))] = date('Y-M', strtotime($i));
		$month_no_arr[] = '"'.date('Y-m', strtotime($i)).'"'; // using for graph dataClick
		$month_name_arr[] = '"'.date('M Y', strtotime($i)).'"'; // for display
		
		// $x_axis_values[] = "['".date('M', strtotime($i))."']";
		$x_axis_values[]   = '"'.date('M', strtotime($i)).'"';
		$forecast_values[] = isset($compare_data[date('Y-m', strtotime($i))]['F']) ? $compare_data[date('Y-m', strtotime($i))]['F'] : 0;
		$actual_values[]   = isset($compare_data[date('Y-m', strtotime($i))]['A']) ? $compare_data[date('Y-m', strtotime($i))]['A'] : 0;
		
		if(substr($i, 5, 2) == "12")
		$i = (date("Y", strtotime($i."-01")) + 1)."-01";
		else
		$i++;

	}
	// $x_axis = $x_axis_values;
	
	$x_axis_values   = implode(',', $x_axis_values);
	$forecast_values = implode(',', $forecast_values);
	$actual_values   = implode(',', $actual_values);
	$month_no_arr    = implode(',', $month_no_arr);
	$month_name_arr  = implode(',', $month_name_arr);
?>
<script type="text/javascript">
	var x_axis_values  		   		 = [<?php echo $x_axis_values ?>];
	var forecast_values 	   		 = [<?php echo $forecast_values ?>];
	var actual_values  		 		 = [<?php echo $actual_values ?>];
	var compare_entity 			 	 = "<?php echo $filter['entity'] ?>";
	var compare_customer 			 = "<?php echo $filter['customer'] ?>";
	var compare_lead_ids 			 = "<?php echo $filter['lead_ids'] ?>";
	var compare_month_year_from_date = "<?php echo $filter['month_year_from_date'] ?>";
	var compare_month_year_to_date   = "<?php echo $filter['month_year_to_date'] ?>";
	var month_no_arr  		  		 = [<?php echo $month_no_arr ?>];
	var month_name_arr  		  	 = [<?php echo $month_name_arr ?>];
	var currency_name  		  		 = ['<?php echo $default_currency ?>'];
</script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_compare.js"></script>
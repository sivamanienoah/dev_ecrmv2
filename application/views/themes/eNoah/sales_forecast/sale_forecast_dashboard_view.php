<?php require (theme_url().'/tpl/header.php'); ?>
<style>
	.hide-calendar .ui-datepicker-calendar { display: none; }
	button.ui-datepicker-current { display: none; }
</style>

<script type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.logAxisRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.pointLabels.min.js"></script>

<?php $username = $this->session->userdata('logged_in_user'); ?>
<div id="content">
    <div class="inner">
<?php
	if($this->session->userdata('accesspage')==1) { ?>
	
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>			
		</div>
	
		<div class="clear"></div>
		
		<div id='results' style="width:auto;">
			
			<div class="clearfix">
				<div class="pull-left forcast-new left-canvas" id="forecast_pie_container">
					
					<h5 class="dash-tlt">
						<span class="forecast-heading">Entitywise Forecast</span>
						<div class="forecast-details">
							<span class="from"><strong>Period: </strong> <?php echo isset($forecast_from_month) ? date("M Y", strtotime($forecast_from_month)) : '-'; ?><strong> -</strong> <?php echo isset($forecast_to_month) ? date("M Y", strtotime($forecast_to_month)) : '-'; ?></span> 
							<a onclick="showFilter('F'); return false;" title="Date Filter" class="date-icon-filter"></a>
						</div>
					</h5>
					
					
					<div id="forecast_entity_chart" class="plot" style="width:650px"></div>
					<!--div id="forecast_entity_chart_img"><button type="button">PDF</button></div-->
					<?php 
						foreach($forecast_entity as $f_ent_name=>$f_ent_val){
							$forecast_entity_value[] = "['".$f_ent_name.'('.$f_ent_val.' '.$default_currency.')'."'".','.$f_ent_val."]";
						}
						$forecast_entity_values = implode(',', $forecast_entity_value);
					?>
					<script type="text/javascript">
						var forecast_entity_values 				 = [<?php echo $forecast_entity_values ?>];
						var forecast_entity_month_year_from_date = "<?php echo isset($filter['month_year_from_date']) ? $filter['month_year_from_date'] : ''; ?>";
						var forecast_entity_month_year_to_date   = "<?php echo isset($filter['month_year_to_date']) ? $filter['month_year_to_date'] : ''; ?>";
					</script>
					<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_entity_forecast.js"></script>
				</div>
				
				<div class="pull-right right-canvas forcast-new" id="actual_pie_container">
					<h5 class="dash-tlt">
						<span class="forecast-heading">Entitywise Actuals</span>
						<div class="forecast-details">
							<span class="from"><strong>Period: </strong> <?php echo isset($actual_from_month) ? date("M Y", strtotime($actual_from_month)) : '-'; ?><strong>-</strong> <?php echo isset($actual_to_month) ? date("M Y", strtotime($actual_to_month)) : '-'; ?></span>
							<a onclick="showFilter('A'); return false;" title="Date Filter" class="date-icon-filter"></a>
						</div>
					</h5>
					<div id="actual_entity_chart" class="plot canvas100" style="width:450px"></div>
					<!--div id="funnelimg"><button type="button">PDF</button></div-->
					<?php 
						foreach($actual_entity as $a_ent_name=>$a_ent_val){
							$actual_entity_value[] = "['".$a_ent_name.'('.$a_ent_val.' '.$default_currency.')'."'".','.$a_ent_val."]";
						}
						$actual_entity_values = implode(',', $actual_entity_value);
					?>
					<script type="text/javascript">
						var actual_entity_values   			   = [<?php echo $actual_entity_values ?>];
						var actual_entity_month_year_from_date = "<?php echo isset($filter['month_year_from_date']) ? $filter['month_year_from_date'] : ''; ?>";
						var actual_entity_month_year_to_date   = "<?php echo isset($filter['month_year_to_date']) ? $filter['month_year_to_date'] : ''; ?>";
					</script>
					<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_entity_actual.js"></script>
				</div>
			</div>
			
			<div id="entity_charts_info_export" class="dash-section dash-section1" style="display:none;">
				
				<div class="entity-chart-heading-new">
					<h5 id='item_tag_name'></h5>
					<div class="export-to-excel">
						<input type='hidden' id='item_name' name='item_name' value=''>
						<input type='hidden' id='item_category' name='item_category' value=''>
						<input type='hidden' id='item_type' name='item_type' value=''>
						<a id="item_export">Export to Excel</a>
					</div>
					<div class="grid-close" id="grid_close_entity"></div>
				</div>

			</div>
			<div class="clearfix"></div>
			<div id="entity_charts_info" class="" style="margin: 10px 0px 0px; display:none; width:auto;"></div>
			
			<div id="entity_actual_charts_info_export" class="dash-section dash-section1" style="display:none;">
			
				<div class="entity-chart-heading-new">
					<h5 id='actual_item_tag_name'></h5>
					<div class="export-to-excel">
						<input type='hidden' id='actual_item_name' name='actual_item_name' value=''>
						<input type='hidden' id='actual_item_category' name='actual_item_category' value=''>
						<input type='hidden' id='actual_item_type' name='actual_item_type' value=''>
						<a id="actual_item_export">Export to Excel</a>
					</div>
					
					<div class="grid-close" id="grid_close_actual_entity"></div>
				</div>
	
			</div>
			<div class="clearfix"></div>
			<div id="entity_actual_charts_info" class="" style="margin: 10px 0px 0px; display:none; width:auto;"></div>
			
			<div class="clearfix">
				<!--div class="pull-left dash-section forecast-new" id="compare_bar_container"-->
				<div class="pull-left forecast-new dash-section-full" id="compare_bar_container" style="margin-top: 20px;">
					<h5 class="forecast_chartbar">
						<span class="forecast-heading">Forecast Vs Actuals</span>
						<div class="forecast-details"> 
							Advanced Filter<a onclick="showFilter('FA'); return false;" title="Filter" class="white-filter"></a>
						</div>
					</h5>
					<div id="forecast_compare_chart" class="plot" style="width:450px"></div>
					<!--div id="funnelimg"><button type="button">PDF</button></div-->
					
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
						var compare_lead_ids 			 = "<?php echo $filter['lead_ids'] ?>";
						var compare_customer 			 = "<?php echo $filter['customer'] ?>";
						var compare_month_year_from_date = "<?php echo $filter['month_year_from_date'] ?>";
						var compare_month_year_to_date   = "<?php echo $filter['month_year_to_date'] ?>";
						var month_no_arr  		  		 = [<?php echo $month_no_arr ?>];
						var month_name_arr  		  	 = [<?php echo $month_name_arr ?>];
						var currency_name  		  		 = ['<?php echo $default_currency ?>'];
					</script>
					<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_compare.js"></script>
				</div>
			</div>
			<div id="compare_charts_info_export" class="dash-section dash-section1" style="display:none;">
				<div class="entity-chart-heading-new">
					<h5 id='compare_item_tag_name'></h5>
					<div class="export-to-excel">
						<input type='hidden' id='compare_item_name' name='compare_item_name' value=''>
						<input type='hidden' id='compare_item_category' name='compare_item_category' value=''>
						<input type='hidden' id='compare_item_type' name='compare_item_type' value=''>
						<a id="export_compare_data">Export to Excel</a>
					</div>
					
					<div class="grid-close" id="grid_close_compare_entity"></div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			
			<div id="compare_charts_info" class="" style="margin: 10px 0px 0px; display:none; width:auto;"></div>
		
		</div>
		<div id="popup-filter-section"></div>
		
<?php
	} else {
		echo "You have no rights to access this page";
	}
?>
	</div><!--Inner div close-->
</div><!--Content div close-->
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/sale_forecast/sale_forecast_dashboard_view.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
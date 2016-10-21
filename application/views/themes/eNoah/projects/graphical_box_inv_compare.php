<h5 class="revenue_compare_head_bar">
	<span class="forecast-heading">Revenue Comparison with Past Year</span>
	<span class="revenue_filter">
		<input type="radio" name="inv_filter_by" class="inv_filter_by" value="inv_month" <?php if($inv_filter_by == 'inv_month') echo "checked='checked'"; ?> />&nbsp;By Month &nbsp;&nbsp;
		<input type="radio" name="inv_filter_by" class="inv_filter_by" value="inv_year" <?php if($inv_filter_by == 'inv_year') echo "checked='checked'"; ?> />&nbsp;By Year
	</span>
</h5>
<?php
if($inv_filter_by == 'inv_month') {
?>
<div id="revenue_compare_line" class="plot" style="position: relative; height: 320px; padding-bottom:22px;"></div>
<script type="text/javascript" src="assets/js/projects/service_graphical_revenue_compare_line.js"></script>
<?php	
} else if($inv_filter_by == 'inv_year') {
?>
<table class="jqplot-table-legend" style="position: absolute; margin-top: -30px; z-index: 3; right: 115px; background-color: rgba(255, 255, 255, 1) !important;"><tbody><tr class="jqplot-table-legend"><td class="jqplot-table-legend jqplot-table-legend-swatch jqplot-seriesToggle" style="text-align: center; padding-top: 0px;"><div class="jqplot-table-legend-swatch-outline"><div class="jqplot-table-legend-swatch" style="background-color: rgb(0, 167, 229); border-color: rgb(0, 167, 229);"></div></div></td><td class="jqplot-table-legend jqplot-table-legend-label jqplot-seriesToggle" style="padding-top: 0px;">Last Year</td><td class="jqplot-table-legend jqplot-table-legend-swatch jqplot-seriesToggle" style="text-align: center; padding-top: 0px;"><div class="jqplot-table-legend-swatch-outline"><div class="jqplot-table-legend-swatch" style="background-color: rgb(0, 225, 67); border-color: rgb(0, 225, 67);"></div></div></td><td class="jqplot-table-legend jqplot-table-legend-label jqplot-seriesToggle" style="padding-top: 0px;">Current Year</td></tr></tbody></table>
<div id="revenue_compare_bar" class="plot" style="position: relative; height: 320px; padding-bottom:22px;"></div>
<script type="text/javascript" src="assets/js/projects/service_graphical_revenue_compare_bar.js"></script>
<?php
}

?>
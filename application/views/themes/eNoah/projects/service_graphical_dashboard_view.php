<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.meterGaugeRenderer.min.js"></script>
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
<script type="text/javascript">
var uc_all_graph_data = <?php echo json_encode($uc_graph_val) ?>;
// var uc_all_graph_data = <?php echo json_encode($uc_graph_val, JSON_PRETTY_PRINT) ?>;
var curr_fiscal_value = <?php echo json_encode($inv_compare['cur_fiscal_yr_inv_value']) ?>;
var last_fiscal_value = <?php echo json_encode($inv_compare['last_fiscal_yr_inv_value']) ?>;
var line_x_axis_value = <?php echo json_encode($this->fiscal_month_arr); ?>;
</script>
<style>
.jqplot-title { display: none; }
</style>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		
		<!--div class="leadstg_note">
			"Infra Services" Practice Values are Merged With "Others" Practice.
		</div-->

		<?php #echo "<pre>"; print_r($graph_val); echo "</pre>"; ?>
		
		<div class="clearfix">
			<!--Utilization Cost Container-->
			<div class="uc-head">
				<h2 class="pull-left borderBtm"><?php echo $page_heading . " (".date('F Y', strtotime($start_date))." - ".date('F Y', strtotime($end_date)).")" ?></h2>
				<!--a class="choice-box" onclick="advanced_filter();" >
					<img src="assets/img/advanced_filter.png" class="icon leads" />
					<span>Advanced Filters</span>
				</a>
				<div class="buttons export-to-excel">
					<button type="button" class="positive" id="btnExportITServices">
						Export to Excel
					</button>
				</div-->
				<div id="filter_section" class="pull-right">
					<div class="clear"></div>
					<div id="advance_search" style="padding-bottom:5px;">			
						<?php $attributes = array('id'=>'filter_uc_dashboard','name'=>'filter_uc_dashboard','method'=>'post'); ?>
						<?php echo form_open_multipart("projects/service_graphical_dashboard", $attributes); ?>

							<input type="hidden" name="filter" id="filter" value="filter" />
							<div style="width:65% !important;">
								<table style="width:340px;" cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
									<tr>
										<td align="left">
											<input type="radio" name="uc_filter_by" value="hour" <?php if($uc_filter_by == 'hour') { echo 'checked="checked"'; }?> />&nbsp;By Hour &nbsp;&nbsp;
											<input type="radio" name="uc_filter_by" value="cost" <?php if($uc_filter_by == 'cost') { echo 'checked="checked"'; }?> />&nbsp;By Cost
										</td>
										<td align="left">
											<input type="submit" class="positive input-font" name="uc_filter_submit" id="uc_filter_submit" value="Search" />
											<div id="load" style = "float:right;display:none;height:1px;">
												<img src="<?php echo base_url().'assets/images/loading.gif'; ?>" width="54" />
											</div>
										</td>								
									</tr>
								</table>
							</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
			<div class="uc_container_wrap" id="uc_container">
				<?php echo $this->load->view('projects/graphical_box_uc', $uc_graph_val); ?>
			</div>
			<!--Utilization Cost Container-->
			<div class="clear"></div>
			<!--Revenue Share Dashboard Container-->
			<div class="uc-head">
				<h2 class="pull-left borderBtm" style="margin-top: 20px;">Revenue Share Dashboard</h2>
				<div id="filter_section" class="pull-right">
					<div class="clear"></div>
					<div id="advance_search" style="padding-bottom:15px;">			
						
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="revenue_container_wrap" id="revenue_container">
				<?php
					$revenue_arr = array();
					$revenue_values = '';
					foreach($invoice_val as $practice_name=>$practice_value){
						$revenue_arr[] = "['".$practice_name.'('.round($practice_value).')'."'".','.$practice_value."]";
					}
					$revenue_values = implode(',', $revenue_arr);
				?>
				
				<?php #echo $this->load->view('projects/graphical_box_uc', $uc_graph_val); ?>
				<!--For Pie Charts-->
				<div class="pull-left overall_container">
					<h5 class="dash-tlt"><?php echo "Revenue in ". $this->default_cur_name." (".date('F Y', strtotime($start_date))." - ".date('F Y', strtotime($end_date)).")"; ?></h5>
					<div id="revenue_pie" class="plot"></div>
				</div>
				<div class="pull-right revenue_chlid_container clearfix">
					<h5 class="dash-tlt">Revenue Comparison with Past Year</h5>
					<div id="revenue_compare_line" class="plot" style="position: relative; height: 320px; padding-bottom:22px;"></div>
				</div>
			</div>
			<!--Utilization Cost Container-->
        <?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
		</div>
	</div>
</div>
<script type="text/javascript">
	var revenue_values = [<?php echo $revenue_values ?>];
</script>
<script type="text/javascript" src="assets/js/projects/service_graphical_dashboard_view.js"></script>
<script type="text/javascript" src="assets/js/projects/service_graphical_revenue_pie.js"></script>
<script type="text/javascript" src="assets/js/projects/service_graphical_revenue_compare.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
<?php require (theme_url().'/tpl/header.php'); ?>
<script language="javascript" type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script type="text/javascript">
var uc_all_graph_data = <?php echo json_encode($graph_val) ?>;
// var uc_all_graph_data = <?php echo json_encode($graph_val, JSON_PRETTY_PRINT) ?>;
</script>
<style>
.jqplot-title { display: none; }
.plot { -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background: #fff none repeat scroll 0 0; border-color: #cecece; border-image: none; border-style: solid; border-width: 0 1px 1px; box-shadow: 0 1px 3px #c2c2c2; min-height: 342px !important; width: 460px !important; }
.chlid_container { -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background: #fff none repeat scroll 0 0; border-color: #cecece; border-image: none; border-style: solid; border-width: 0 1px 1px; box-shadow: 0 1px 3px #c2c2c2; width: 680px !important; min-height: 343px !important;}
.uc_container_wrap .chlid_container .graph_box { float:left; background: #fff none repeat scroll 0 0; border-color: #cecece; border-image: none; border-style: solid; border-width: 0 1px 1px; box-shadow: 0 1px 3px #c2c2c2; height: 150px !important; width: 204px !important; margin:10px; }
.graph_box .jqplot-event-canvas { left: 0px !important; }
</style>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<div class="page-title-head">
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
		</div>
		<div id="filter_section">
			<div class="clear"></div>
			<div id="advance_search" style="padding-bottom:15px;">			
				<?php $attributes = array('id' => 'filter_uc_dashboard','name' => 'filter_uc_dashboard'); ?>
				<?php echo form_open_multipart("projects/service_graphical_dashboard", $attributes); ?>
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div style="width:65% !important;">
						<table style="width:340px;" cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td align="left">
									<input type="radio" name="uc_filter_by" value="hour" <?php if($uc_filter_by == 'hour') { echo 'checked="checked"'; }?> />&nbsp;By Hour &nbsp;&nbsp;
									<input type="radio" name="uc_filter_by" value="cost" <?php if($uc_filter_by == 'cost') { echo 'checked="checked"'; }?> />&nbsp;By Cost
								</td>
								<td align="left">
									<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
								</td>								
							</tr>
						</table>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		<!--div class="leadstg_note">
			"Infra Services" Practice Values are Merged With "Others" Practice.
		</div-->
		<?php #echo "<pre>"; print_r($graph_val); echo "</pre>"; ?>
		
		<div class="clearfix">
			<div class="uc_container_wrap" id='uc_container'>
				<div class="pull-left" id="overall_container">
					<h5 class="dash-tlt">Over All - <?php echo $graph_val['total']['ytd_billable'] . "%"; ?></h5>
					<div id="total" class="plot"></div>
				</div>
				<div class="pull-right chlid_container clearfix" id="child_container">
					<h5 class="dash-tlt">Practice Wise</h5>
					<?php
						unset($graph_val['total']);
						$i = 1;
						foreach($graph_val as $key=>$val) {
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
			</div>
        <?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
		</div>
	</div>
</div>

<script type="text/javascript">

</script>

<script type="text/javascript">
var uc_graph_data = <?php echo json_encode($graph_val) ?>;
// var uc_graph_data = <?php echo json_encode($graph_val, JSON_PRETTY_PRINT) ?>;
$(document).ready(function(){
// console.info(uc_all_graph_data);
// alert(uc_all_graph_data.total.practice_name);
/*for total utiliztion cost graph*/
var s2 = [uc_all_graph_data.total.ytd_billable];
var plot1 = 'plot_total';
plot1 = $.jqplot('total', [s2],{
	seriesDefaults: {
		renderer: $.jqplot.MeterGaugeRenderer,
		rendererOptions: {
			label: uc_all_graph_data.total.practice_name,
			labelPosition: 'bottom',
			labelHeightAdjust: -5,
			intervalInnerRadius: 148,
			intervalOuterRadius: 120,
			background: "transparent",
			ringColor: "#bbc6d0",
			ringWidth: 1,
			hubRadius: 6,
			needleThickness: 5,
			ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
			min: 0,
			max: 100,
			intervals:[10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
			intervalColors:['#fa0002','#fb3915', '#f95d10', '#f59502', '#f7ac13', '#ffd107','#f3e219', '#d8ed22', '#b0d112', '#9eba1a' ],
			smooth: true,
			animation: { show: true }
		}
	}
});
$.each(uc_graph_data, function (index, value) {
    // alert( index + ' ' + value.practice_name );
	var s1 = [value.ytd_billable];
	var plot = 'plot_'+index;
	plot = $.jqplot(index, [s1],{
		seriesDefaults: {
			renderer: $.jqplot.MeterGaugeRenderer,
			rendererOptions: {
				label: value.practice_name+' - '+value.ytd_billable+' %',
				labelPosition: 'bottom',
				labelHeightAdjust: 5,
				background: "transparent",
				ringColor: "#bbc6d0",
				ringWidth: 1,
				hubRadius: 5,
				needleThickness: 3,
				intervalInnerRadius: 33,
				intervalOuterRadius: 45,
				ticks: [0, 20, 40, 60, 80, 100],
				min: 0,
				max: 100,
				intervals:[10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
				intervalColors:['#fa0002','#fb3915', '#f95d10', '#f59502', '#f7ac13', '#ffd107','#f3e219', '#d8ed22', '#b0d112', '#9eba1a' ],
				smooth: true,
				animation: { show: true }
			}
		}
	});
});

});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

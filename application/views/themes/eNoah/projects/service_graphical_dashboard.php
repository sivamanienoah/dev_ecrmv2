<?php require (theme_url().'/tpl/header.php'); ?>
<script language="javascript" type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="assets/js/plugins/jqplot.meterGaugeRenderer.min.js"></script>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar { display: none; }
.dept_section{ width:100%; float:left; margin:20px 0 0 0; }
.dept_section div{ width:49%; }
.dept_section div:first-child{ margin-right:2% }
table.bu-tbl th{ text-align:center; }
table.bu-tbl{ width:85%; }
table.bu-tbl-inr th{ text-align:center; }
</style>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>

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
				<form name="advanceFilterServiceDashboard" id="advanceFilterServiceDashboard" method="post">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					
					<div style="width:65% !important;">
						<table style="width:340px;" cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td align="left">
									<input <?php echo 'checked="checked"';?> type="radio" name="filter_by" value="1" />&nbsp;By Hour &nbsp;&nbsp;<input type="radio" name="filter_by" value="2" />&nbsp;By Cost
								</td>
								<td align="left">
									<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
								</td>								
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
		<!--div class="leadstg_note">
			"Infra Services" Practice Values are Merged With "Others" Practice.
		</div-->
		<?php echo "<pre>"; print_r($graph_val); echo "</pre>"; ?>
		<script type="text/javascript">
		alert('test'); return false;
		var all_graph_data = <?php echo json_encode($graph_val, JSON_PRETTY_PRINT) ?>;
		</script>
		<div id="default_view">
			<table cellspacing="0" cellpadding="0" border="0" class="proj-dash-table">
				<tr>
					<td align='center' colspan=2>
						<div style="position: relative; height: 300px; width: 400px;" id="total"></div>
					</td>
				</tr>
				<tr>
				<?php 
				unset($graph_val['total']);
				$i = 1;
				foreach($graph_val as $key=>$val) {
				?>
				<td>
					<div style="position: relative; height: 300px; width: 400px;" id="<?php echo $key; ?>"></div>
				</td>
				<?php
				if($i%2==0) {
				?> 
				</tr>
				<?php }
				$i++;
				}
			?>
			</table>
				
			<div class="clearfix"></div>
			<div id="chart4"></div>
						
        <?php 
		} else {
			echo "You have no rights to access this page";
		} 
		?>
		</div>
	</div>
</div>

<script type="text/javascript">
var graph_data = <?php echo json_encode($graph_val, JSON_PRETTY_PRINT) ?>;
</script>

<script type="text/javascript">
/*Test Graph*/

$(document).ready(function(){
console.info(all_graph_data);
// alert(all_graph_data.total.practice_name);

/*for total utiliztion cost graph*/
var s2 = [all_graph_data.total.ytd_billable];
var plot1 = 'plot_total';
plot1 = $.jqplot('total', [s2],{
	seriesDefaults: {
		renderer: $.jqplot.MeterGaugeRenderer,
		rendererOptions: {
			label: all_graph_data.total.practice_name,
			labelPosition: 'bottom',
			labelHeightAdjust: -5,
			intervalOuterRadius: 85,
			min: 0,
			max: 100,
			// ticks: [1000, 2000, 3000, 4000, 5000],
			intervals:[30, 70, 100],
			intervalColors:['#cc6666', '#f79e62', '#66cc66' ],
			smooth: true,
			animation: { show: true }
		}
	}
});
$.each(graph_data, function (index, value) {
    // alert( index + ' ' + value.practice_name );
	var s1 = [value.ytd_billable];
	var plot = 'plot_'+index;
	plot = $.jqplot(index, [s1],{
		seriesDefaults: {
			renderer: $.jqplot.MeterGaugeRenderer,
			rendererOptions: {
				label: value.practice_name,
				labelPosition: 'bottom',
				labelHeightAdjust: -5,
				intervalOuterRadius: 85,
				min: 0,
				max: 100,
				// ticks: [1000, 2000, 3000, 4000, 5000],
				intervals:[30, 70, 100],
				intervalColors:['#cc6666', '#f79e62', '#66cc66' ],
				smooth: true,
				animation: { show: true }
			}
		}
	});
});


   

});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

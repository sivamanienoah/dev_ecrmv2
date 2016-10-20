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
.uc-head { border-bottom: 1px solid #ccc; float: left; margin: 0 0 20px; width: 100%; }
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
					<div id="advance_search" style="padding-bottom:15px;">			
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
				<?php echo $this->load->view('projects/graphical_box_uc', $graph_val); ?>
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
<script type="text/javascript" src="assets/js/projects/service_graphical_dashboard.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
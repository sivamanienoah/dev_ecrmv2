<?php require (theme_url().'/tpl/header.php'); ?>
<script language="javascript" type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
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
<?php
$practice_arr = array();
?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			
			<div id="filter_section">
				<div id="advance_search" class="buttons">
					<form name="advanceFilterServiceDashboard" id="advanceFilterServiceDashboard" method="post">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<input type="hidden" name="filter" value="filter" />
						<div class="pull-left">
							<div class="pull-left">
								<select name='fy_name' id='fy_name'>
									<option value=''>--Select--</option>
									<?php if(!empty($fy_year) && count($fy_year)>0) { ?>
										<?php foreach($fy_year as $fy_rec) { ?>
											<option value='<?php echo $fy_rec['financial_yr']; ?>' <?php echo $yr_select = ($fy_name == $fy_rec['financial_yr']) ? 'selected="selected"' : ''; ?>><?php echo $fy_rec['fy_name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class='pull-left'>
								<select name='start_month' id='start_month'>
									<option value=''>--Select--</option>
									<?php if(!empty($fy_month) && count($fy_month)>0) { ?>
										<?php foreach($fy_month as $fy_mon_key=>$fy_mon_val) { ?>
											<option value='<?php echo $fy_mon_key; ?>' <?php echo $start_sel_month = ($start_month == $fy_mon_key) ? 'selected="selected"' : ''; ?>><?php echo $fy_mon_val; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class='pull-left'>
								<select name='end_month' id='end_month'>
									<option value=''>--Select--</option>
									<?php if(!empty($fy_month) && count($fy_month)>0) { ?>
										<?php foreach($fy_month as $fy_end_mon_key=>$fy_end_mon_val) { ?>
											<option value='<?php echo $fy_end_mon_key; ?>' <?php echo $end_sel_month = ($end_month == $fy_end_mon_key) ? 'selected="selected"' : ''; ?> > <?php echo $fy_end_mon_val; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class='pull-left'>
								<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" style="display:block;"/>
							</div>
						</div>
						
					</form>
				</div>				
			</div>
			
			<div class="buttons pull-right">
				<button type="button" class="positive" id="btnExportITServices">
					Export to Excel
				</button>
			</div>
		</div>
		<?php #echo "<pre>"; print_r($practice_data); echo "</pre>"; ?>
		<div id="default_view">
			<table cellspacing="0" cellpadding="0" border="0" id='it_services_dash' class="data-table proj-dash-table bu-tbl">
				<tr>
					<thead>
						<th>IT Services Dashboard</th>
						<?php if(!empty($practice_data)) { ?>
							<?php foreach($practice_data as $prac) { ?>
								<?php if($prac->id != 7 && $prac->id != 13) { ?>
									<?php $practice_arr[] = $prac->practices; ?>
									<?php $practice_id_arr[$prac->practices] = $prac->id; ?>
									<th><?php echo $prac->practices; ?></th>
								<?php }	?>
							<?php } ?>
						<?php } ?>
						<th>Total</th>
					</thead>
				</tr>
				<?php #echo "<pre>"; print_r($projects['practicewise']); echo "</pre>"; ?>
				<tr>
					<td><b>Number of Projects currently running</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									if($parr == 'Others') {
										$infraProjects  = isset($projects['practicewise']['Infra Services']) ? $projects['practicewise']['Infra Services'] : 0;
										$testinProjects = isset($projects['practicewise']['Testing']) ? $projects['practicewise']['Testing'] : 0;
										$otherProjects  = isset($projects['practicewise']['Others']) ? $projects['practicewise']['Others'] : 0;
										$noProjects = $infraProjects+$otherProjects+$testinProjects;
										$noProjects = isset($noProjects) ? $noProjects : '';
									} else {
										$noProjects = isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : '';
									}
									if($noProjects!='') {
										$total_projects += $noProjects;
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'noprojects'); return false;"><?php echo $noProjects; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>							
						<?php } ?>
					<?php } ?>
					<td align='right'><?php echo ($total_projects!=0) ? $total_projects : '-'; ?></td>
				</tr>
				<tr>
					<td><b>Number of projects in Red</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									if($parr == 'Others') {
										$infraRAG   = isset($projects['rag_status']['Infra Services']) ? $projects['rag_status']['Infra Services'] : 0;
										$testingRAG = isset($projects['rag_status']['Testing']) ? $projects['rag_status']['Testing'] : 0;
										$otherRAG   = isset($projects['rag_status']['Others']) ? $projects['rag_status']['Others'] : 0;
										$ragProjects = $infraRAG+$otherRAG+$testingRAG;
										$rag = isset($ragProjects) ? $ragProjects : '';
									} else {
										$rag = isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : '';
									}
									if($rag!='') {
										$total_rag += $rag;
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'rag'); return false;"><?php echo $rag; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'><?php echo ($total_rag!=0) ? $total_rag : '-'; ?></td>
				</tr>
				<tr>
					<td><b>Billing for the month (USD) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									if($parr == 'Others') {
										$infraCMB   = ($dashboard_det['Infra Services']['billing_month'] != '-') ? $dashboard_det['Infra Services']['billing_month'] : 0;
										$otherCMB   = ($dashboard_det['Others']['billing_month'] != '-') ? $dashboard_det['Others']['billing_month'] : 0;
										$CMBProjects = $infraCMB + $otherCMB;
										$CMBProjects = isset($CMBProjects) ? $CMBProjects : '';
										$cm_billing  = isset($CMBProjects) ? round($CMBProjects) : '-';
									} else {
										$cm_billing = ($dashboard_det[$parr]['billing_month']!='-') ? round($dashboard_det[$parr]['billing_month']) : '-';
									}
									
									if($cm_billing!='-'){
								?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cm_billing'); return false;"><?php echo $cm_billing; ?></a>
								<?php
									} else {
										echo "-";
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'><?php echo ($dashboard_det['Total']['billing_month']!='-') ? round($dashboard_det['Total']['billing_month']) : '-'; ?></td>
				</tr>
				<tr>
					<td><b>YTD Billing (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									if($parr == 'Others') {
										$infra_irval 	= isset($dashboard_det['Infra Services']['ytd_billing']) ? $dashboard_det['Infra Services']['ytd_billing'] : 0;
										$other_irval 	= isset($dashboard_det['Others']['ytd_billing']) ? $dashboard_det['Others']['ytd_billing'] : 0;
										$irvalProjects 	= $infra_irval + $other_irval;
										$irvalProjects 	= isset($irvalProjects) ? $irvalProjects : '';
										$irval 			= isset($irvalProjects) ? round($irvalProjects) : '-';
									} else {
										$irval 			= ($dashboard_det[$parr]['ytd_billing']!='-') ? round($dashboard_det[$parr]['ytd_billing']) : '-';
									}
									if($irval!="-") {
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'irval'); return false;"><?php echo $irval; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'><?php echo ($dashboard_det['Total']['ytd_billing']!='-') ? round($dashboard_det['Total']['ytd_billing']) : '-'; ?></td>
				</tr>
				<tr>
					<td><b>YTD Utilization Cost (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									if($parr == 'Others') {
										$infra_dc_value = isset($dashboard_det['Infra Services']['ytd_utilization_cost']) ? $dashboard_det['Infra Services']['ytd_utilization_cost'] : 0;
										$other_dc_value	= isset($dashboard_det['Others']['ytd_utilization_cost']) ? $dashboard_det['Others']['ytd_utilization_cost'] : 0;
										$dc_value_Projects 	= $infra_dc_value + $other_dc_value;
										$dc_value_Projects 	= isset($dc_value_Projects) ? $dc_value_Projects : '';
										$dc_value 			= isset($dc_value_Projects) ? round($dc_value_Projects) : '-';
									} else {
										$dc_value = ($dashboard_det[$parr]['ytd_utilization_cost']!='-') ? round($dashboard_det[$parr]['ytd_utilization_cost']) : '-';
									}
									
									if($dc_value!="-") {
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'dc_value'); return false;"><?php echo $dc_value; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php
							echo ($dashboard_det['Total']['ytd_utilization_cost']!='-') ? round($dashboard_det['Total']['ytd_utilization_cost']) : '-';
						?>
					</td>
				</tr>
				<tr>
					<td><b>Billable for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$cm_billval = ($dashboard_det[$parr]['billable_month']!='-') ? round($dashboard_det[$parr]['billable_month']) : '-';
									if($cm_billval!="-") {
									?>					
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cm_eff'); return false;"><?php echo $cm_billval; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php echo ($dashboard_det['Total']['billable_month']!='-') ? round($dashboard_det['Total']['billable_month']) : '-'; ?>
					</td>
				</tr>
				<tr>
					<td><b>Billable YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$billval = ($dashboard_det[$parr]['ytd_billable']!='-') ? round($dashboard_det[$parr]['ytd_billable']) : '-';
									if($billval != '-') {
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'ytd_eff'); return false;"><?php echo $billval; ?></a>
									<?php
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php
							echo ($dashboard_det['Total']['ytd_billable']!='-') ? round($dashboard_det['Total']['ytd_billable']) : '-';
						?>
					</td>
				</tr>
				<tr>
					<td><b>Effort Variance (%) - <span class="highlight_info">For Fixed Bid projects</span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php								
								$eff_var = ($dashboard_det[$parr]['effort_variance']!='-') ? round($dashboard_det[$parr]['effort_variance']) : '-';
								if(($eff_var != '-') && (($parr!='Infra Services'))) {
								?>
								<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'fixedbid'); return false;"><?php echo round($eff_var, 0); ?></a>
								<?php
								} else {
									echo '-';
								}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php
							echo ($dashboard_det['Total']['effort_variance']!='-') ? round($dashboard_det['Total']['effort_variance']) : '-';
						?>
					</td>
				</tr>
				<tr>
					<td><b>Contribution for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$cm_dc_val = ($dashboard_det[$parr]['contribution_month']!='-') ? round($dashboard_det[$parr]['contribution_month']) : '-';
									echo ($cm_dc_val!='-') ? $cm_dc_val : '-';
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php
							echo ($dashboard_det['Total']['contribution_month']!='-') ? round($dashboard_det['Total']['contribution_month']) : '-';
						?>
					</td>
				</tr>
				<tr>
					<td><b>Contribution YTD (45 %) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$dc_val = ($dashboard_det[$parr]['ytd_contribution']!='-') ? round($dashboard_det[$parr]['ytd_contribution']) : '-';
									$arrow_val = 'down_arrow';
									if(round($dc_val, 0) >= 45){
										$arrow_val = 'up_arrow';
									}
									if($dc_val!='-'){
									?>
										<?php if($parr=='Infra Services') { 
											echo '-';
										} else {
										?>
										<span class="<?php echo "itser_".$arrow_val;?>">
											<?php echo round($dc_val, 0); ?>
										</span>
										<?php 
										}
									} else {
										echo '-';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
					<td align='right'>
						<?php
							echo ($dashboard_det['Total']['ytd_contribution']!='-') ? round($dashboard_det['Total']['ytd_contribution']) : '-';
						?>
					</td>
				</tr>
			</table>
				
			<div class="clearfix"></div>
			<div class="service_dash_notes">
				<span class="red"> ** </span>Infra Services & Testing Practice Values are Merged With Others Practice.
			</div>
			<div class="clearfix"></div>
			<div id="drilldown_data" class="" style="margin:20px 0;display:none;">
			
			</div>
        <?php 
		} else {
			echo "You have no rights to access this page";
		} 
		?>
		</div>
	</div>

<script type="text/javascript">
$( "#month_year_from_date, #month_year_to_date" ).datepicker({
	changeMonth: true,
	changeYear: true,
	dateFormat: 'MM yy',
	maxDate: 0,
	showButtonPanel: true,	
	onClose: function(dateText, inst) {
		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
		$(this).datepicker('setDate', new Date(year, month, 1));
	},
	beforeShow : function(input, inst) {
		if ((datestr = $(this).val()).length > 0) {
			year = datestr.substring(datestr.length-4, datestr.length);
			month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNames'));
			$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
			$(this).datepicker('setDate', new Date(year, month, 1));    
		}
			var other  = this.id  == "month_year_from_date" ? "#month_year_to_date" : "#month_year_from_date";
			var option = this.id == "month_year_from_date" ? "maxDate" : "minDate";        
		if ((selectedDate = $(other).val()).length > 0) {
			year = selectedDate.substring(selectedDate.length-4, selectedDate.length);
			month = jQuery.inArray(selectedDate.substring(0, selectedDate.length-5), $(this).datepicker('option', 'monthNames'));
			$(this).datepicker( "option", option, new Date(year, month, 1));
		}
		$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
	}
});

//For Advance Filters functionality.
$( "#advance_search" ).on( "click", "#advance", function(e) {
	e.preventDefault();
	var form_data = $('#advanceFilterServiceDashboard').serialize();	
	$.ajax({
		type: "POST",
		url: site_base_url+"projects/it_service_dashboard/",
		dataType: "html",
		data: form_data,
		beforeSend:function() {
			$('#default_view').empty();
			$('#default_view').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			// $('#advance').show();
			$('#default_view').html(res);
			// $('#load').hide();
		}
	});
	return false;  //stop the actual form post !important!
});

function getData(practice, clicktype)
{	
	var month_status   	 = $('input[name=filter_by]:checked', '#advanceFilterServiceDashboard').val()
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/service_dashboard_data_beta/',
		// data: 'filter=filter'+'&entity='+entity+'&project_status='+project_status+'&month_year_from_date='+month_year_from_date+'&month_year_to_date='+month_year_to_date+'&billable_month='+billable_month+'&practice='+practice+'&clicktype='+clicktype+'&'+csrf_token_name+'='+csrf_hash_token,
		data: 'filter=filter'+'&practice='+practice+'&clicktype='+clicktype+'&'+csrf_token_name+'='+csrf_hash_token+'&month_status='+month_status,
		cache: false,
		beforeSend:function() {
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}
$(function() {
	$("#btnExportITServices").click(function () {
		$('#drilldown_data').empty();
		$("#it_services_dash").btechco_excelexport({
			containerid: "it_services_dash"
		   , datatype: $datatype.Table
		   , filename: 'IT Services Data'
		});
	});
});
</script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

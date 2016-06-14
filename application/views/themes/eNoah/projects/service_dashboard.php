<?php require (theme_url().'/tpl/header.php'); ?>
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
$tot_Irval = $totCM_Irval = $totEV = $totDC = $total_projects = $total_rag = 0;
// echo "<pre>"; print_r($projects); echo "</pre>";
?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>

			<!--a class="choice-box" onclick="advanced_filter();" >
				<img src="assets/img/advanced_filter.png" class="icon leads" />
				<span>Advanced Filters</span>
			</a-->
			
			<div class="buttons export-to-excel">
				<button type="button" class="positive" id="btnExportITServices">
					Export to Excel
				</button>
			</div>
		</div>
		
		<!--div id="filter_section">
			<div class="clear"></div>
			<div id="advance_search" style="padding-bottom:15px; display:none;">
				<form name="advanceFilterServiceDashboard" id="advanceFilterServiceDashboard" method="post">
					<input type="hidden" name="<?php #echo $this->security->get_csrf_token_name(); ?>" value="<?php #echo $this->security->get_csrf_hash(); ?>" />
					<div style="border: 1px solid #DCDCDC; width:65% !important;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">MONTH & YEAR</td>
								<td class="tblheadbg">BILLABLE MONTH</td>
								<td class="tblheadbg">PROJECT STATUS</td>
								<td class="tblheadbg">ENTITY</td>
							</tr>
							<tr>
								<td class="month-year">
									<span>From</span> <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="" />
									<br />
									<span>To</span> <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" value="" />
								</td>
								<td class="">
									<span>Billable Month</span> <input type="text" data-calendar="false" name="billable_month" id="billable_month" class="textfield" value="" />
								</td>
								<td class="">
									<select title="Status" id="project_status" name="project_status[]" multiple="multiple">
										<option value="1">Project In Progress</option>
										<option value="2">All Projects</option>
										<option value="3">Project Onhold</option>
										<option value="4">Inactive</option>
									</select>
								</td>								
								<td class="">
									<select title="Entity" id="entity" name="entity[]" multiple="multiple">
										<?php #if(count($entity_data)>0 && !empty($entity_data)) { ?>
										<?php #foreach($entity_data as $entityrec) {	?>
												<option value="<?php #echo $entityrec->div_id; ?>"><?php #echo $entityrec->division_name; ?></option>
										<?php #}
										#}
										?>
									</select>
								</td>
							</tr>
							<tr align="right" >
								<td colspan="2">
									<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
									<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php #echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div-->
		
		<div id="default_view">
			<table cellspacing="0" cellpadding="0" border="0" id='it_services_dash' class="data-table proj-dash-table bu-tbl">
				<tr>
					<thead>
						<th>IT Services Dashboard</th>
						<?php if(!empty($practice_data)) { ?>
							<?php foreach($practice_data as $prac) { ?>
								<th><?php echo $prac->practices; ?></th>
								<?php $practice_arr[] = $prac->practices; ?>
								<?php $practice_id_arr[$prac->practices] = $prac->id; ?>
							<?php } ?>
						<?php } ?>
						<th>Total</th>
					</thead>
				</tr>
				<?php
					// echo "<pre>"; print_r($practice_id_arr); echo "</pre>"; exit;
				?>
				<tr>
					<td><b>Number of Projects currently running</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$noProjects = isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : '';
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
					<td align='right'><?php echo ($total_projects!=0) ? $total_projects : ''; ?></td>
				</tr>
				<tr>
					<td><b>Number of projects in Red</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$rag = isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : '';
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
					<td align='right'><?php echo ($total_rag!=0) ? $total_rag : ''; ?></td>
				</tr>
				<tr>
					<td><b>Billing for the month (USD) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$cm_billing = '';
									$cm_billing = isset($projects['cm_irval'][$parr]) ? round($projects['cm_irval'][$parr]) : '';
									if($cm_billing!=''){
										$totCM_Irval += $cm_billing;
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
					<td align='right'><?php echo ($totCM_Irval!=0) ? $totCM_Irval : ''; ?></td>
				</tr>
				<tr>
					<td><b>YTD Billing (USD) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$irval = isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : '';
									if($irval!="") {
										$tot_Irval += $irval;
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
					<td align='right'><?php echo ($tot_Irval!=0) ? $tot_Irval : ''; ?></td>
				</tr>
				<tr>
					<td><b>Billable for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$cm_billval = '';
									// if(isset($projects['billable_month'][$parr]['Billable']['hour']) && isset($projects['billable_month'][$parr]['totalhour']))
									$cm_billval = ($projects['billable_month'][$parr]['Billable']['hour'])/$projects['billable_month'][$parr]['totalhour'];
									if(isset($cm_billval) && ($cm_billval != 0)) {
									?>					
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'cm_eff'); return false;"><?php echo round(($cm_billval*100), 0); ?></a>
									<?php
									} else {
										echo '';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Billable YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
									$billval = '';
									if(isset($projects['billable_ytd'][$parr]['Billable']['hour']) && isset($projects['billable_ytd'][$parr]['totalhour']))
									$billval = ($projects['billable_ytd'][$parr]['Billable']['hour'])/$projects['billable_ytd'][$parr]['totalhour'];
									if(isset($billval) && ($billval != 0)) {
									?>
									<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'ytd_eff'); return false;"><?php echo round(($billval*100), 0); ?></a>
									<?php
									} else {
										echo '';
									}
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Effort Variance (%) - <span class="highlight_info">For Fixed Bid projects</span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php
								$eff_var = '';
								if(isset($projects['eff_var'][$parr])) {
									$eff_var = (($projects['eff_var'][$parr]['total_actual_hrs'] - $projects['eff_var'][$parr]['tot_estimate_hrs'])/$projects['eff_var'][$parr]['tot_estimate_hrs'])*100;
								}
								if(isset($eff_var) && ($eff_var != 0)) {
								?>
								<a onclick="getData('<?php echo $practice_id_arr[$parr]; ?>', 'fixedbid'); return false;"><?php echo round($eff_var, 0); ?></a>
								<?php
								} else {
									echo '';
								}
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Contribution for the month (%) - <span class="highlight_info"><?=date('M Y', strtotime($bill_month));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php #echo $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'] . "-". $projects['cm_irval'][$parr]; 
									#((total invoice raised - total direct cost)/total invoice raised)*100;
									$cm_dc_val = '';
									if(isset($projects['cm_irval'][$parr]) && isset($projects['cm_direct_cost'][$parr]['total_cm_direct_cost'])) {
										$cm_dc_val = (($projects['cm_irval'][$parr] - $projects['cm_direct_cost'][$parr]['total_cm_direct_cost'])/$projects['cm_irval'][$parr]) * 100;
									}
									echo round($cm_dc_val, 0);
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Contribution YTD (%) - <span class="highlight_info"><?=date('M Y', strtotime($start_date));?> To <?=date('M Y', strtotime($end_date));?></span></b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php 
									// echo $projects['irval'][$parr] . "-" .$projects['direct_cost'][$parr]['total_direct_cost'];
									// ((total invoice raised - total direct cost)/total invoice raised)*100
									$dc_val = '';
									if(isset($projects['irval'][$parr]) && isset($projects['direct_cost'][$parr]['total_direct_cost'])) {
										$dc_val = (($projects['irval'][$parr] - $projects['direct_cost'][$parr]['total_direct_cost'])/$projects['irval'][$parr]) * 100;
									}
									echo round($dc_val, 0);
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
			</table>
				
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
$( "#billable_month" ).datepicker({
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
		$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
	}
});
function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}
//For Advance Filters functionality.
$("#advanceFilterServiceDashboard").submit(function() {
	$('#advance').hide();
	$('#load').show();
	// var entity        		 = $("#entity").val();
	// var project_status 		 = $("#project_status").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();
	var billable_month   	 = $("#billable_month").val();

	// if(entity == null && project_status == null && month_year_from_date == "" && month_year_to_date == "" && billable_month == ""){
	if(month_year_from_date == "" && month_year_to_date == "" && billable_month == ""){
		$('#advance').show();
		$('#load').hide();
		return false;
	}

	$.ajax({
		type: "POST",
		url: site_base_url+"projects/dashboard/service_dashboard/",
		// dataType: "json",
		// data: 'filter=filter'+'&entity='+entity+'&project_status='+project_status+'&month_year_from_date='+month_year_from_date+'&month_year_to_date='+month_year_to_date+'&billable_month='+billable_month+'&'+csrf_token_name+'='+csrf_hash_token,
		data: 'filter=filter'+'&month_year_from_date='+month_year_from_date+'&month_year_to_date='+month_year_to_date+'&billable_month='+billable_month+'&'+csrf_token_name+'='+csrf_hash_token,
		beforeSend:function() {
			$('#default_view').empty();
			$('#default_view').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success: function(res) {
			$('#advance').show();
			$('#default_view').html(res);
			$('#load').hide();
		}
	});
	return false;  //stop the actual form post !important!
});

function getData(practice, clicktype)
{	
	// var entity        		 = $("#entity").val();
	// var project_status 		 = $("#project_status").val();
	// var month_year_from_date = $("#month_year_from_date").val();
	// var month_year_to_date   = $("#month_year_to_date").val();
	// var billable_month   	 = $("#billable_month").val();
	
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/service_dashboard_data/',
		// data: 'filter=filter'+'&entity='+entity+'&project_status='+project_status+'&month_year_from_date='+month_year_from_date+'&month_year_to_date='+month_year_to_date+'&billable_month='+billable_month+'&practice='+practice+'&clicktype='+clicktype+'&'+csrf_token_name+'='+csrf_hash_token,
		data: 'filter=filter'+'&practice='+practice+'&clicktype='+clicktype+'&'+csrf_token_name+'='+csrf_hash_token,
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

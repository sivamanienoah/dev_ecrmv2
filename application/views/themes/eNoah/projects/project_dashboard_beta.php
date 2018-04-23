<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar { display: none; }
.dept_section{ width:100%; float:left; margin:20px 0 0 0; }
.dept_section div{ width:49%; }
.dept_section div:first-child{ margin-right:2% }
table.bu-tbl th{ text-align:center; }
table.bu-tbl{ width:70%; }
table.bu-tbl-inr th{ text-align:center; }
</style>
<script type="text/javascript">var this_is_home = true;</script>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			
			<a class="choice-box" onclick="advanced_filter();" >
				<img src="assets/img/advanced_filter.png" class="icon leads" />
				<span>Advanced Filters</span>
			</a>
			<div class="buttons">
				<form name="fliter_data" id="fliter_data" method="post">
				<!--button  type="submit" id="excel-1" class="positive">
					Export to Excel
				</button-->
				<input type="hidden" name="exclude_leave" value="" id="hexclude_leave" />
				<input type="hidden" name="exclude_holiday" value="" id="hexclude_holiday" />
				<input type="hidden" name="month_year_from_date" value="" id="hmonth_year" />
				<input type="hidden" name="month_year_to_date" value="" id="hmonth_to_year" />
				<input type="hidden" name="department_ids" value="" id="hdept_ids" />
				<input type="hidden" name="practice_ids" value="" id="hprac_ids" />
				<input type="hidden" name="entity_ids" value="" id="henty_ids" />
				<input type="hidden" name="skill_ids" value="" id="hskill_ids" />
				<input type="hidden" name="member_ids" value="" id="hmember_ids" />
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div id="filter_section">
			<div class="clear"></div>
			
			<div id="advance_search" style="padding-bottom:15px; display:none;">
<form action="<?php echo site_url('projects/dashboard/utilization_metrics_beta')?>" name="project_dashboard" id="project_dashboard" method="post">					
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">MONTH & YEAR</td>
								<td class="tblheadbg">EXCLUDE</td>
								<td class="tblheadbg">ENTITY</td>
								<td class="tblheadbg">DEPARTMENT</td>
								<td class="tblheadbg">PRACTICE</td>
								<td class="tblheadbg">SKILL</td>
								<td class="tblheadbg">RESOURCE</td>
							</tr>
							<tr>	
								<td class="month-year">
									<span>From</span> <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($start_date)); ?>" />
									<br />
									<span>To</span> <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" value="<?php echo date('F Y',strtotime($end_date)); ?>" />
								</td>
								<td class="by-exclusion">
									<?php $leaveChecked=''; if($exclude_leave==1) { $leaveChecked ='checked="checked"'; } ?>
									<label><input type="checkbox" id="exclude_leave" name="exclude_leave" <?php echo $leaveChecked; ?> value="1" /><span>Leave</span></label>
									<br />
									<?php $holidayChecked=''; if($exclude_holiday==1) { $holidayChecked ='checked="checked"'; } ?>
									<label><input type="checkbox" id="exclude_holiday" name="exclude_holiday" <?php echo $holidayChecked; ?> value="1" /><span>Holiday</span></label>
								</td>
								<td class="proj-dash-select">
									<select title="Select Entity" id="entity_ids" name="entity_ids[]" multiple="multiple">
									<?php if(count($entitys)>0 && !empty($entitys)) { ?>
										<?php foreach($entitys as $enty) { ?>
											<option <?php echo in_array($enty->div_id, $entity_ids) ? 'selected="selected"' : '';?> value="<?php echo $enty->div_id;?>"><?php echo $enty->division_name; ?></option>
										<?php } ?>
									<?php } ?>
									</select>
								</td>
								<td class="proj-dash-select">
									<select title="Select Department" id="department_ids" name="department_ids[]"	multiple="multiple">
									<?php if(count($departments)>0 && !empty($departments)){?>
											<?php foreach($departments as $depts){?>
												<option <?php echo in_array($depts->department_id,$department_ids)?'selected="selected"':'';?> value="<?php echo $depts->department_id;?>"><?php echo $depts->department_name;?></option>
									<?php } }?>
									</select>
								</td>
								<td class="proj-dash-select">
									<select multiple="multiple" title="Select Practice" id="practice_ids" name="practice_ids[]">
										<?php if(count($practice_ids_selected)>0 && !empty($practice_ids_selected)) { ?>
												<?php foreach($practice_ids_selected as $prac) {?>
													<option <?php echo in_array($prac->practice_id, $practice_ids)?'selected="selected"':'';?> value="<?php echo $prac->practice_id;?>"><?php echo $prac->practice_name;?></option>
										<?php } } ?>
									</select>
								</td>
								<td class="proj-dash-select">
									<select title="Select Skill" id="skill_ids" name="skill_ids[]"	multiple="multiple">
										<?php if(count($skill_ids_selected)>0 && !empty($skill_ids_selected)) { ?>
										<?php foreach($skill_ids_selected as $skills) {
												$skills->name = ($skills->skill_id==0)?'N/A':$skills->name;
												?>
												<option <?php echo in_array($skills->skill_id,$skill_ids)?'selected="selected"':'';?> value="<?php echo $skills->skill_id; ?>"><?php echo $skills->name;?></option>
										<?php } }?>
									</select>
								</td>
								<td class="proj-dash-select">
									<select title="Select Members" id="member_ids" name="member_ids[]" multiple="multiple">
										<?php if(count($member_ids_selected)>0 && !empty($member_ids_selected)){?>
										<?php foreach($member_ids_selected as $members){?>
												<option <?php echo in_array($members->username, $member_ids)?'selected="selected"':'';?> value="<?php echo $members->username;?>" title="<?php echo $members->emp_name.' - '.$members->emp_id; ?>"><?php echo $members->emp_name;?></option>
										<?php } }?>								
									</select>
								</td>
							</tr>
							<tr align="right" >
								<td colspan="7">
									<input type="hidden" id="start_date" name="start_date" value="" />
									<input type="hidden" id="end_date" name="end_date" value="" />
									<input type="hidden" id="filter_area_status" name="filter_area_status" value="" />
									<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
									<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
 
			<div class="clearfix"></div>
			<div id="ajax_loader" style="margin:20px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>
				<?php 
					// echo "<pre>"; print_r($resdata); die;
					
					//Applying max hours calculation//	
					$timesheet_data = array();
					$user_data = array();
					$resource_cost = array();
					$head_count_arr = array();

					if(count($resdata)>0) {
						$rates = $conversion_rates;
						foreach($resdata as $rec) {
							$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
							$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
							
							$user_data[$rec->username]['practice_id'] 	= $rec->practice_id;
							$user_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
							$user_data[$rec->username]['dept_name'] 	= $rec->dept_name;
							$user_data[$rec->username]['prac_id'] 		= $rec->practice_id;
							
							$rateCostPerHr = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
							$directrateCostPerHr = round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
							$timesheet_data[$rec->dept_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
							$timesheet_data[$rec->dept_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
							$timesheet_data[$rec->dept_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
							$timesheet_data[$rec->dept_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
						}
						
						
						if(count($timesheet_data)>0 && !empty($timesheet_data)) {
							foreach($timesheet_data as $dept_key=>$resource_type_arr) {
								if(!empty($resource_type_arr) && count($resource_type_arr)>0) {
									foreach($resource_type_arr as $resource_type_key=>$resource_arr) {
										if(!empty($resource_arr) && count($resource_arr)>0) {
											foreach($resource_arr as $resrc_name=>$resrc_data) {
												$resource_name 	= $resrc_name;
												$max_hours 		= $user_data[$resrc_name]['max_hours'];
												$dept_name 		= $user_data[$resrc_name]['dept_name'];
												$prac_id 		= $user_data[$resrc_name]['prac_id'];
												
												if(count($resrc_data)>0 && !empty($resrc_data)) {
													foreach($resrc_data as $key2=>$value2) {
														$year = $key2;
														if(count($value2)>0 && !empty($value2)) {
															foreach($value2 as $key3=>$value3) {
																$individual_billable_hrs = 0;
																$ts_month		 	  	 = $key3;
																if(count($value3)>0 && !empty($value3)){
																	foreach($value3 as $key4=>$value4) {
																		if($key4 != 'total_hours'){ 
																			$individual_billable_hrs = $value3['total_hours'];
																			$duration_hours			 = $value4['duration_hours'];
																			$rate				 	 = $value4['rateperhr'];
																			$direct_rateperhr	 	 = $value4['direct_rateperhr'];
																			$rate1 					 = $rate;
																			$direct_rateperhr1 		 = $direct_rateperhr;
																			if($individual_billable_hrs>$max_hours) {
																				$percentage 		= ($max_hours/$individual_billable_hrs);
																				$rate1 				= number_format(($percentage*$direct_rateperhr),2);
																				$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
																			}
																			if($prac_id == 0) {
																				$direct_rateperhr1  = $direct_rateperhr;
																			}
																			$resource_cost[$dept_key][$resource_type_key]['duration_hours'] += $duration_hours;
																			$resource_cost[$dept_key][$resource_type_key]['total_cost'] 	+= ($duration_hours*$direct_rateperhr1);
																			$resource_cost[$dept_key][$resource_type_key]['total_dc_cost']  += ($duration_hours*$direct_rateperhr1);
																			//head count
																			if(isset($resource_cost[$dept_key][$resource_type_key]['head_count'])) {
																				$resource_cost[$dept_key][$resource_type_key]['head_count'] += 1; 
																			} else {
																				$resource_cost[$dept_key][$resource_type_key]['head_count'] = 1;
																			}
																			//total_hour,total_cost based on dept
																			$resource_cost['tot'][$dept_key]['total_hour'] += $duration_hours;
																			$resource_cost['tot'][$dept_key]['total_cost'] += ($duration_hours*$direct_rateperhr1);
																			//for overall
																			$resource_cost['over_all'][$resource_type_key]['duration_hours'] += $duration_hours;
																			$resource_cost['over_all'][$resource_type_key]['total_cost'] 	+= ($duration_hours*$direct_rateperhr1);
																			$resource_cost['over_all'][$resource_type_key]['total_dc_cost']  += ($duration_hours*$direct_rateperhr1);
																			//head count
																			if(isset($resource_cost['over_all'][$resource_type_key]['head_count'])) {
																				$resource_cost['over_all'][$resource_type_key]['head_count'] += 1; 
																			} else {
																				$resource_cost['over_all'][$resource_type_key]['head_count'] = 1;
																			}
																			//total_hour,total_cost based on overall
																			$resource_cost['tot']['over_all']['total_hour'] += $duration_hours;
																			$resource_cost['tot']['over_all']['total_cost'] += ($duration_hours*$direct_rateperhr1);
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}	
						}
					}
					//Applying max hours calculation// $other_cost_arr
					// echo "<pre>"; print_r($resource_cost); echo "</pre>";
				?>	
			<div id="default_view">
				<h4>IT</h4>
				<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl">
					<tr>
						<thead>
							<th>Billablity</th>
							<th>Hours</th>
							<th># Head Count *</th>
							<th>Total Cost</th>
							<th>Total Direct Cost</th>
							<th>% of Hours</th>
							<th>% of Cost</th>
							<th>% of Direct Cost</th>
						</thead>
					</tr>
					<?php
						//echo "<pre>"; print_r($max_hours); //die;
						$percent_hour = $percent_cost = $percent_directcost = 0;
						ksort($resource_cost['over_all']);
						if(!empty($resource_cost['over_all']) && count($resource_cost['over_all'])>0) {
							foreach($resource_cost['over_all'] as $resrc_type_name=>$rtval) {
								// if($resrc_type_name = 'Billable') {
									// $rtval['total_cost'] += 
								// }
					?>
								<tr>
									<td><?php echo $resrc_type_name; ?></td>
									<td align="right"><?php echo round($rtval['duration_hours'], 1); ?></td>
									<td align="right"><?php echo round($rtval['head_count'], 2); ?></td>
									<td align="right"><?php echo round($rtval['total_cost'],0); ?></td>
									<td align="right"><?php echo round($rtval['total_dc_cost'],0); ?></td>
									<td align="right"><?php echo round(($rtval['duration_hours']/$resource_cost['tot']['over_all']['total_hour']) * 100, 1) . ' %'; ?></td>
									<td align="right"><?php echo round(($rtval['total_cost']/$resource_cost['tot']['over_all']['total_cost']) * 100, 0) . ' %'; ?></td>
									<td align="right"><?php echo round(($rtval['total_dc_cost']/$resource_cost['tot']['over_all']['total_cost']) * 100, 0) . ' %'; ?></td>
								</tr>
							<?php
								$percent_hour += ($rtval['duration_hours']/$resource_cost['tot']['over_all']['total_hour']) * 100;
								$percent_cost += ($rtval['total_cost']/$resource_cost['tot']['over_all']['total_cost']) * 100;
								$percent_directcost += ($rtval['total_dc_cost']/$resource_cost['tot']['over_all']['total_cost']) * 100;
							}
						}
					?>
							<tr>
								<td align="right"><b>Total:</b></td>
								<td align="right"><?= round($resource_cost['tot']['over_all']['total_hour'],1); ?></td>
								<td align="right"></td>
								<td align="right"><?= round($resource_cost['tot']['over_all']['total_cost'],0); ?></td>
								<td align="right"><?= round($resource_cost['tot']['over_all']['total_cost'],0); ?></td>
								<td align="right"><?= round($percent_hour,1) . ' %'; ?></td>
								<td align="right"><?= round($percent_cost,0) . ' %'; ?></td>
								<td align="right"><?= round($percent_directcost,0) . ' %'; ?></td>
							</tr>
				</table>
				<div class="dept_section">
					<div class="dept_sec_inner pull-left">
						<h4>EADS</h4>
						<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl-inr">
							<tr>
								<thead>
									<th>Billablity</th>
									<th>Hours</th>
									<th># Head Count *</th>
									<th>Total Cost</th>
									<th>Total Direct Cost</th>
									<th>% of Hours</th>
									<th>% of Cost</th>
									<th>% of Direct Cost</th>
								</thead>
							</tr>
							<?php
								$percent_hour = $percent_cost = $percent_directcost = 0;
								ksort($resource_cost['eADS']);
								foreach($resource_cost['eADS'] as $ads_key=>$ads_val) {
							?>
										<tr>
											<td><a onclick="getData(<?php echo "'".$ads_key."'"; ?>,'2');return false;"><?= $ads_key; ?></a></td>
											<td align="right"><?php echo round($ads_val['duration_hours'], 1); ?></td>
											<td align="right"><?php echo round($ads_val['head_count'], 2); ?></td>
											<td align="right"><?php echo round($ads_val['total_cost'],0); ?></td>
											<td align="right"><?php echo round($ads_val['total_dc_cost'],0); ?></td>
											<td align="right"><?php echo round(($ads_val['duration_hours']/$resource_cost['tot']['eADS']['total_hour']) * 100, 1) . ' %'; ?></td>
											<td align="right"><?php echo round(($ads_val['total_cost']/$resource_cost['tot']['eADS']['total_cost']) * 100, 0) . ' %'; ?></td>
											<td align="right"><?php echo round(($ads_val['total_dc_cost']/$resource_cost['tot']['eADS']['total_cost']) * 100, 0) . ' %'; ?></td>
										</tr>
							<?php
									$percent_hour += ($ads_val['duration_hours']/$resource_cost['tot']['eADS']['total_hour']) * 100;
									$percent_cost += ($ads_val['total_cost']/$resource_cost['tot']['eADS']['total_cost']) * 100;
									$percent_directcost += ($ads_val['total_dc_cost']/$resource_cost['tot']['eADS']['total_cost']) * 100;
									}
							?>
									<tr>
										<td align="right"><b>Total:</b></td>
										<td align="right"><?= round($resource_cost['tot']['eADS']['total_hour'],1); ?></td>
										<td align="right"></td>
										<td align="right"><?= round($resource_cost['tot']['eADS']['total_cost'],0); ?></td>
										<td align="right"><?= round($resource_cost['tot']['eADS']['total_cost'],0); ?></td>
										<td align="right"><?= round($percent_hour,1) . ' %'; ?></td>
										<td align="right"><?= round($percent_cost,0) . ' %'; ?></td>
										<td align="right"><?= round($percent_directcost,0) . ' %'; ?></td>
									</tr>
						</table>
					</div>
					<div class="dept_sec_inner pull-left">
						<h4>EQAD</h4>
						<?php #echo '<pre>'; print_r($bu_arr); ?>
						<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl-inr">
							<tr>
								<thead>
									<th>Billablity</th>
									<th>Hours</th>
									<th># Head Count *</th>
									<th>Total Cost</th>
									<th>Total Direct Cost</th>
									<th>% of Hours</th>
									<th>% of Cost</th>
									<th>% of Direct Cost</th>
								</thead>
							</tr>
							<?php
								$percent_hour = $percent_cost = $percent_directcost = 0;
								ksort($resource_cost['eQAD']);
								foreach($resource_cost['eQAD'] as $qadkey=>$qadval) {
							?>
										<tr>
											<td><a onclick="getData(<?php echo "'".$qadkey."'"; ?>,'2');return false;"><?= $qadkey; ?></a></td>
											<td align="right"><?php echo round($qadval['duration_hours'], 1); ?></td>
											<td align="right"><?php echo round($qadval['head_count'], 2); ?></td>
											<td align="right"><?php echo round($qadval['total_cost'],0); ?></td>
											<td align="right"><?php echo round($qadval['total_dc_cost'],0); ?></td>
											<td align="right"><?php echo round(($qadval['duration_hours']/$resource_cost['tot']['eQAD']['total_hour']) * 100, 1) . ' %'; ?></td>
											<td align="right"><?php echo round(($qadval['total_cost']/$resource_cost['tot']['eQAD']['total_cost']) * 100, 0) . ' %'; ?></td>
											<td align="right"><?php echo round(($qadval['total_dc_cost']/$resource_cost['tot']['eQAD']['total_cost']) * 100, 0) . ' %'; ?></td>
										</tr>
							<?php
									$percent_hour += ($qadval['duration_hours']/$resource_cost['tot']['eQAD']['total_hour']) * 100;
									$percent_cost += ($qadval['total_cost']/$resource_cost['tot']['eQAD']['total_cost']) * 100;
									$percent_directcost += ($qadval['total_dc_cost']/$resource_cost['tot']['eQAD']['total_cost']) * 100;
									}
							?>
									<tr>
										<td align="right"><b>Total:</b></td>
										<td align="right"><?= round($resource_cost['tot']['eQAD']['total_hour'],1); ?></td>
										<td align="right"></td>
										<td align="right"><?= round($resource_cost['tot']['eQAD']['total_cost'],0); ?></td>
										<td align="right"><?= round($resource_cost['tot']['eQAD']['total_cost'],0); ?></td>
										<td align="right"><?= round($percent_hour,1) . ' %'; ?></td>
										<td align="right"><?= round($percent_cost,0) . ' %'; ?></td>
										<td align="right"><?= round($percent_directcost,0) . ' %'; ?></td>
									</tr>
						</table>
					</div>
				</div>
								
				<div class="clearfix"></div>
				<div style="margin:20px 0">
					<fieldset>
						<legend>Legend</legend>
						<div align="left" style="background: none repeat scroll 0 0 #3b5998;">
							<!--Legends-->
							<div class="dashboardLegend">
								<div class="pull-left"><strong>#Head Count</strong> - Number of resources booked timesheet in these heads</div>
							</div>
						</div>
					</fieldset>
				</div>
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
var cur_mon = '<?php echo date('F Y') ?>';
var filter_area_status = '<?php echo $filter_area_status; ?>';
if(filter_area_status==1){
	$('#advance_search').show();
}
$(function() {
 	/*Date Picker*/
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
			$("#filter_area_status").val('1');
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
	
	$('#exclude_leave, #exclude_holiday').click(function() {
        $("#filter_area_status").val('1');
    });
	
	$('#entity_ids').change(function(){
		$("#filter_area_status").val('1');
	});
});	
$(document).ready(function(){
	
	$("#department_ids").change(function(){
		var ids = $(this).val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var params = {'dept_ids':ids,'start_date':start_date,'end_date':end_date};
		params[csrf_token_name] = csrf_hash_token;
		$("#filter_area_status").val('1');
		$('#practice_ids').html('');
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_practices',
			data: params,
			success: function(practices) {
				if(practices){
					var prac_html='';
					var prac = $.parseJSON(practices);
					if(prac.length){
						for(var i=0;i<prac.length;i++){
							prac_html +='<option value="'+prac[i].practice_id+'">'+prac[i].practice_name+'</option>';
						}	
					}
					$('#practice_ids').html('');
					$('#practice_ids').append(prac_html);
				}
				//skill
				$('#skill_ids').html('');
				$('#member_ids').html('');
				$.ajax({
					type: 'POST',
					url: site_base_url+'projects/dashboard/get_skills',
					data: params,
					success: function(data) {
						if(data){
							var skills = $.parseJSON(data);
							if(skills.length){
								var html='';
								for(var i=0;i<skills.length;i++){
									if(skills[i].name=='null' || skills[i].skill_id==0) skills[i].name = 'N/A';
										html +='<option value="'+skills[i].skill_id+'">'+skills[i].name+'</option>';
								}
								$('#skill_ids').html('');
								$('#skill_ids').append(html)
								$.ajax({
									type: 'POST',
									url: site_base_url+'projects/dashboard/get_members',
									data: params,
									success: function(members) {
										if(members){
											var mem_html='';
											var users = $.parseJSON(members);
											if(users.length){
												for(var i=0;i<users.length;i++){
													mem_html +='<option value="'+users[i].username+'">'+users[i].emp_name+'</option>';
												}	
											}
											$('#member_ids').html('');
											$('#member_ids').append(mem_html)								
										}
									}
								});
							}
						}
					}
				});
			}
		});
		return false;		
	});
	
	//on change for practice id
	$("#practice_ids").change(function(){
		var ids  = $(this).val();
		var d_ids = $('#department_ids').val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var params = {'dept_ids':d_ids,'prac_id':ids,'start_date':start_date,'end_date':end_date};
		$("#filter_area_status").val('1');
		$('#skill_ids').html('');
		params[csrf_token_name] = csrf_hash_token;
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_skills_by_practice',
			data: params,
			success: function(pdata) {
				if(pdata){
					var skills = $.parseJSON(pdata);
					if(skills.length){
						var html='';
						for(var i=0;i<skills.length;i++){
							if(skills[i].name=='null' || skills[i].skill_id==0) skills[i].name = 'N/A';
								html +='<option value="'+skills[i].skill_id+'">'+skills[i].name+'</option>';
						}
						$('#skill_ids').html('');
						$('#skill_ids').append(html)
						$.ajax({
							type: 'POST',
							url: site_base_url+'projects/dashboard/get_practice_members',
							data: params,
							success: function(members) {
								if(members){
									var mem_html='';
									var users = $.parseJSON(members);
									if(users.length){
										for(var i=0;i<users.length;i++){
											mem_html +='<option value="'+users[i].username+'">'+users[i].emp_name+'</option>';
										}	
									}
									$('#member_ids').html('');
									$('#member_ids').append(mem_html)								
								}
							}
						});
					}
				}
			}
		});
		return false;		
	});
	
	$('body').on('change','#skill_ids',function(){
		var dids       = $('#department_ids').val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var sids = $(this).val();
		$("#filter_area_status").val('1');
		$('#member_ids').html('');
		var params = { 'dept_ids':dids,'skill_ids':sids,'start_date':start_date,'end_date':end_date };
		params[csrf_token_name] = csrf_hash_token;
		
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_skill_members',
			data: params,
			success: function(members) {
				if(members){
					var mem_html;
					var users = $.parseJSON(members);
					if(users.length){
						for(var i=0;i<users.length;i++){
							mem_html +='<option value="'+users[i].username+'" title="'+users[i].emp_name+' - '+users[i].emp_id+'">'+users[i].emp_name+'</option>';
						}	
					}
					$('#member_ids').html('');
					$('#member_ids').append(mem_html);
				}
			}
		});
	});
	
	$("input[name=exclude_leave]").prop("checked",true);
	$("input[name=exclude_holiday]").prop("checked",true);
});

function getData(resource_type, dept_type)
{
	$('#filter_group_by').prop('selectedIndex',0);
	if($('#department_ids').val() == null) {
		$('#hdept_ids').val('');
	} else {
		$('#hdept_ids').val($('#department_ids').val());
	}
	if($('#practice_ids').val() == null) {
		$('#hprac_ids').val('');
	} else {
		$('#hprac_ids').val($('#practice_ids').val());
	}
	if($('#entity_ids').val() == null) {
		$('#henty_ids').val('');
	} else {
		$('#henty_ids').val($('#entity_ids').val());
	}
	$('#hmonth_year').val($('#month_year_from_date').val());
	$('#hmonth_to_year').val($('#month_year_to_date').val());
	$('#hskill_ids').val($('#skill_ids').val())
	$('#hmember_ids').val($('#member_ids').val())
	if($('#exclude_leave').attr('checked'))
	$('#hexclude_leave').val(1);
	if($('#exclude_holiday').attr('checked'))
	$('#hexclude_holiday').val(1)
	
	var formdata = $('#fliter_data').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/get_data_beta/',                                                              
		data: formdata+'&resource_type='+resource_type+'&dept_type='+dept_type+'&filter_group_by=0',
		cache: false,
		beforeSend:function() {
			$('#filter_group_by').prop('selectedIndex',0);
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
function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}
$('#filter_reset').click(function() {
	 $("#project_dashboard").find('input:checkbox').removeAttr('checked').removeAttr('selected');
	 $("#practice_ids").html('');
	 $("#skill_ids").html('');
	 $("#member_ids").html('');
	 // $("#month_year_from_date, #month_year_to_date").val(cur_mon);
	 // $("#entity_ids").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
	 $('select#entity_ids option').removeAttr("selected");
	 $('select#department_ids option').removeAttr("selected");
	 // $("#department_ids").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

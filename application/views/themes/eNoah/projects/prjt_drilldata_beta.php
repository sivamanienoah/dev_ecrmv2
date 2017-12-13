<style>
.prac-dt{ text-align:center !important; }
</style>
<div id="drildown_filter_area">
	<div class="pull-left">
		<label>Group By</label>
		<select name="filter_group_by" id="filter_group_by">
			<option value='0' <?php if($filter_group_by == 0) echo "selected='selected'"; ?>>Practice</option>
			<option value='4' <?php if($filter_group_by == 4) echo "selected='selected'"; ?>>Entity</option>
			<option value='1' <?php if($filter_group_by == 1) echo "selected='selected'"; ?>>Skill</option>
			<option value='2' <?php if($filter_group_by == 2) echo "selected='selected'"; ?>>Project</option>
			<option value='3' <?php if($filter_group_by == 3) echo "selected='selected'"; ?>>Resource</option>
		</select>
	</div>
	<div class="pull-left" style="margin:0 15px;;">
		<label>Sort By</label>
		<select name="filter_sort_by" id="filter_sort_by">
			<option value='desc' <?php if($filter_sort_by == 'desc') echo "selected='selected'"; ?>>DESC</option>
			<option value='asc' <?php if($filter_sort_by == 'asc') echo "selected='selected'"; ?>>ASC</option>
		</select>
	</div>
	<div class="pull-left" style="margin:0 15px;;">
		<label>Sort Value</label>
		<select name="filter_sort_val" id="filter_sort_val">
			<option value='hour' <?php if($filter_sort_val == 'hour') echo "selected='selected'"; ?>>Hour</option>
			<option value='cost' <?php if($filter_sort_val == 'cost') echo "selected='selected'"; ?>>Cost</option>
			<option value='directcost' <?php if($filter_sort_val == 'directcost') echo "selected='selected'"; ?>>Direct Cost</option>
		</select>
	</div>
	<div class="pull-left" style="margin:0 15px;;">
		<input type='hidden' name="dept_type" id="dept_type" value="<?php echo $dept_type; ?>" />
		<input type='hidden' name="resource_type" id="resource_type" value="<?php echo $resource_type; ?>" />
	</div>
	<div class="bttn-area" style="margin:0 15px;">
		<div class="bttons">
			<input style="height:auto;" type="button" class="positive input-font" name="refine_drilldown_data" id="refine_drilldown_data" value="Go" />
			<input style="height:auto;" type="button" class="positive input-font" name="reset_drilldown" id="reset_drilldown" value="Reset" />
		</div>								
	</div>
</div>
<div class="clear"></div>
<?php
function array_sort($array, $on, $order='SORT_ASC')
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }
        switch ($order) {
            case 'SORT_ASC':
                asort($sortable_array);
                break;
            case 'SORT_DESC':
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}

$tbl_data = array();
$sub_tot  = array();
$cost_arr = array();
$directcost_arr= array();
$usercnt  = array();
$prjt_hr  = array();
$prjt_cst = array();
$prjt_directcst = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
$tot_hour = 0;
$tot_cost = 0;
$tot_directcost = 0;
$user_data 		= array();
$timesheet_data = array();
if(!empty($resdata)) {
	/* foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost'])){
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost'] += $rec->resource_duration_cost;
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['directcost'] += $rec->resource_duration_direct_cost;
		} else {
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost'] = $rec->resource_duration_cost;
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['directcost'] = $rec->resource_duration_direct_cost;
		}

		if(isset($sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour']))
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour'] +=  $rec->duration_hours;
		else
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour'] =  $rec->duration_hours;
		
		if(isset($sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost']))
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		else
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost'] =  $rec->resource_duration_cost;
	
		if(isset($sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_directcost']))
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_directcost'] +=  $rec->resource_duration_direct_cost;
		else
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_directcost'] =  $rec->resource_duration_direct_cost;
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		$tot_directcost = $tot_directcost + $rec->resource_duration_direct_cost;
		
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
		$directcost_arr[$rec->empname] = $rec->direct_cost_per_hour;
		
		//head count
		if (!in_array($rec->empname, $usercnt[$rec->dept_name][$rec->project_code]))
		$usercnt[$rec->dept_name][$rec->project_code][] = $rec->empname;
	
		//for project_code - sorting-hour
		if(isset($prjt_hr[$rec->dept_name][$rec->project_code]))
		$prjt_hr[$rec->dept_name][$rec->project_code] += $rec->duration_hours;
		else 
		$prjt_hr[$rec->dept_name][$rec->project_code] = $rec->duration_hours;
		//for project_code - sorting-cost
		if(isset($prjt_cst[$rec->dept_name][$rec->project_code]))
		$prjt_cst[$rec->dept_name][$rec->project_code] += $rec->resource_duration_cost;
		else 
		$prjt_cst[$rec->dept_name][$rec->project_code] = $rec->resource_duration_cost;
	
		if(isset($prjt_directcst[$rec->dept_name][$rec->project_code]))
		$prjt_directcst[$rec->dept_name][$rec->project_code] += $rec->resource_duration_direct_cost;
		else 
		$prjt_directcst[$rec->dept_name][$rec->project_code] = $rec->resource_duration_direct_cost;
	} */
	foreach($resdata as $rec) {
		$rates 				= $conversion_rates;
		$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
		$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
		
		$user_data[$rec->username]['emp_name'] 		= $rec->empname;
		$user_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
		$user_data[$rec->username]['dept_name'] 	= $rec->dept_name;
		$user_data[$rec->username]['prac_id'] 		= $rec->practice_id;
		
		$rateCostPerHr 			= round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
		$directrateCostPerHr 	= round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
		
		$timesheet_data[$rec->dept_name][$rec->project_code][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['duration_hours'] += $rec->duration_hours;
		$timesheet_data[$rec->dept_name][$rec->project_code][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
		$timesheet_data[$rec->dept_name][$rec->project_code][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['direct_rateperhr'] = $directrateCostPerHr;	
		$timesheet_data[$rec->dept_name][$rec->project_code][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['rateperhr']        = $rateCostPerHr;
	}
	
	if(!empty($timesheet_data) && count($timesheet_data)>0) {
		foreach($timesheet_data as $dept_key=>$proj_arr) {
			if(!empty($proj_arr) && count($proj_arr)>0) {
				foreach($proj_arr as $proj_key=>$skill_arr) {
					if(!empty($skill_arr) && count($skill_arr)>0) {
						foreach($skill_arr as $skill_key=>$resrc_data) {
							if(!empty($resrc_data) && count($resrc_data)>0) {
								foreach($resrc_data as $resrc_name=>$recval_data) {
									$resource_name 	= $resrc_name; 
									$emp_name 		= $user_data[$resrc_name]['emp_name'];
									$max_hours 		= $user_data[$resrc_name]['max_hours'];
									$dept_name 		= $user_data[$resrc_name]['dept_name'];
									$prac_id 		= $user_data[$resrc_name]['prac_id'];
									if(count($recval_data)>0 && !empty($recval_data)) {
										foreach($recval_data as $key2=>$value2) {
											$year = $key2;
											if(count($value2)>0 && !empty($value2)) {
												foreach($value2 as $key3=>$value3) {
													$individual_billable_hrs = 0;
													$ts_month 				 = $key3;
													$individual_billable_hrs = $value3['total_hours'];
													$duration_hours			 = $value3['duration_hours'];
													$rate				 	 = $value3['rateperhr'];
													$direct_rateperhr	 	 = $value3['direct_rateperhr'];
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
													$rateHour = $duration_hours * $direct_rateperhr1;
													
													//create array
													if(isset($tbl_data[$dept_key][$proj_key][$emp_name]['hour'])) {
														$tbl_data[$dept_key][$proj_key][$emp_name]['hour'] += $duration_hours;
													} else {
														$tbl_data[$dept_key][$proj_key][$emp_name]['hour'] = $duration_hours;
													}
													if(isset($tbl_data[$dept_key][$proj_key][$emp_name]['cost'])){
														$tbl_data[$dept_key][$proj_key][$emp_name]['cost'] += $rateHour;
														$tbl_data[$dept_key][$proj_key][$emp_name]['directcost'] += $rateHour;
													} else {
														$tbl_data[$dept_key][$proj_key][$emp_name]['cost'] = $rateHour;
														$tbl_data[$dept_key][$proj_key][$emp_name]['directcost'] = $rateHour;
													}

													if(isset($sub_tot[$dept_key][$proj_key]['sub_tot_hour'])) {
														$sub_tot[$dept_key][$proj_key]['sub_tot_hour'] +=  $duration_hours;
													} else {
														$sub_tot[$dept_key][$proj_key]['sub_tot_hour'] =  $duration_hours;
													}
													
													if(isset($sub_tot[$dept_key][$proj_key]['sub_tot_cost'])) {
														$sub_tot[$dept_key][$proj_key]['sub_tot_cost'] +=  $rateHour;
													} else {
														$sub_tot[$dept_key][$proj_key]['sub_tot_cost'] =  $rateHour;
													}
												
													if(isset($sub_tot[$dept_key][$proj_key]['sub_tot_directcost'])) {
														$sub_tot[$dept_key][$proj_key]['sub_tot_directcost'] +=  $rateHour;
													} else {
														$sub_tot[$dept_key][$proj_key]['sub_tot_directcost'] =  $rateHour;
													}
												
													$tot_hour = $tot_hour + $duration_hours;
													$tot_cost = $tot_cost + $rateHour;
													$tot_directcost = $tot_directcost + $rateHour;
													
													$cost_arr[$emp_name] = $direct_rateperhr1;
													$directcost_arr[$emp_name] = $direct_rateperhr1;
													
													//head count
													if (!in_array($emp_name, $usercnt[$dept_key][$proj_key])) {
														$usercnt[$dept_key][$proj_key][] = $emp_name;
													}
												
													//for project_code - sorting-hour
													if(isset($prjt_hr[$dept_key][$proj_key])) {
														$prjt_hr[$dept_key][$proj_key] += $duration_hours;
													} else {
														$prjt_hr[$dept_key][$proj_key] = $duration_hours;
													}
													//for project_code - sorting-cost
													if(isset($prjt_cst[$dept_key][$proj_key])) {
														$prjt_cst[$dept_key][$proj_key] += $rateHour;
													} else {
														$prjt_cst[$dept_key][$proj_key] = $rateHour;
													}
												
													if(isset($prjt_directcst[$dept_key][$proj_key])) {
														$prjt_directcst[$dept_key][$proj_key] += $rateHour;
													} else {
														$prjt_directcst[$dept_key][$proj_key] = $rateHour;
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
	// echo "<pre>"; print_r($timesheet_data);
}
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm"><?php echo $heading; ?> :: Group By - Project</h2>
	<div class="section-right">
		<div class="buttons add-new-button">
			<button id='expand_tr' class="positive" type="button">
				Expand
			</button>
		</div>
		<div class="buttons collapse-button">
			<button id='collapse_tr' class="positive" type="button">
				Collapse
			</button>
		</div>
		<div class="buttons export-to-excel">
			<button type="button" class="positive" id="btnExport">
				Export to Excel
			</button>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div>
<?php
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;
// echo "<pre>"; print_r($prjt_hr); echo "</pre>";
if(!empty($tbl_data)) {
	echo "<table id='project_dash' class='data-table'>
			<tr>
			<th class='prac-dt' width='15%'><b>PROJECT NAME</b></th>
			<th class='prac-dt' width='15%'><b>USER NAME</b></th>
			<th class='prac-dt' width='5%'><b>HOUR</b></th>
			<th class='prac-dt' width='5%'><b>COST</b></th>
			<th class='prac-dt' width='5%'><b>DIRECT COST</b></th>
			<th class='prac-dt' width='5%'><b>% of HOUR</b></th>
			<th class='prac-dt' width='5%'><b>% of COST</b></th>
			<th class='prac-dt' width='5%'><b>% of DIRECT COST</b></th>
			</tr>";
	foreach($tbl_data as $dept=>$proj_ar) {
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($prjt_hr[$dept]);
				$sort_ar = $prjt_hr[$dept];
			} else if($filter_sort_val=='cost') {
				asort($prjt_cst[$dept]);
				$sort_ar = $prjt_cst[$dept];
			} else if($filter_sort_val=='directcost') {
				asort($prjt_directcst[$dept]);
				$sort_ar = $prjt_directcst[$dept];
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($prjt_hr[$dept]);
				$sort_ar = $prjt_hr[$dept];
			} else if($filter_sort_val=='cost') {
				arsort($prjt_cst[$dept]);
				$sort_ar = $prjt_cst[$dept];
			} else if($filter_sort_val=='directcost') {
				arsort($prjt_directcst[$dept]);
				$sort_ar = $prjt_directcst[$dept];
			}
		}
		// foreach($proj_ar as $p_name=>$user_ar) {
		$proj_arr = array();
		foreach($sort_ar as $p_name=>$user_ar) {
			$proj_arr = $proj_ar[$p_name];
			$i       = 0;
			// $res_cnt = 0;
			$pj_tot_cost = $per_sub_hr = $sub_tot_pj_cost = 0;
			$name    = isset($project_master[$p_name]) ? $project_master[$p_name] : $p_name;
			/* $res_cnt = count($usercnt[$dept][$p_name]);
			$per_sub_hr = ($sub_tot[$dept][$p_name]['sub_tot_hour']/(160*$res_cnt))*100;
			foreach($usercnt[$dept][$p_name] as $usr){
				$pj_tot_cost += $cost_arr[$usr]*160;
			}
			$sub_tot_pj_cost = ($sub_tot[$dept][$p_name]['sub_tot_cost']/$pj_tot_cost)*100; */
			$per_sub_hr 	 = ($sub_tot[$dept][$p_name]['sub_tot_hour']/$tot_hour)*100;
			$sub_tot_pj_cost = ($sub_tot[$dept][$p_name]['sub_tot_cost']/$tot_cost)*100;
			$sub_tot_pj_directcost = ($sub_tot[$dept][$p_name]['sub_tot_directcost']/$tot_directcost)*100;
			$perc_tot_hr   += $per_sub_hr;
			$perc_tot_cost += $sub_tot_pj_cost;
			$perc_tot_directcost += $sub_tot_pj_directcost;
			$calc_tot_hour += $sub_tot[$dept][$p_name]['sub_tot_hour'];
			$calc_tot_cost += $sub_tot[$dept][$p_name]['sub_tot_cost'];
			$calc_tot_directcost += $sub_tot[$dept][$p_name]['sub_tot_directcost'];
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='15%' align='left' class='collapse lft-ali'><span class='toggle'> ".strtoupper($name)."</span></th>
				<th width='15%' align='right' class='rt-ali'>SUB TOTAL(PROJECT WISE):</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_hour'], 1)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_cost'], 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_directcost'], 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($per_sub_hr, 1)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pj_cost, 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pj_directcost, 2)."</th>
			</tr>";
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					$prj_arr = array_sort($proj_arr, 'hour', 'SORT_ASC');
				} else if($filter_sort_val=='cost') {
					$prj_arr = array_sort($proj_arr, 'cost', 'SORT_ASC');
				} else if($filter_sort_val=='directcost') {
					$prj_arr = array_sort($proj_arr, 'directcost', 'SORT_ASC');
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					$prj_arr = array_sort($proj_arr, 'hour', 'SORT_DESC');
				} else if($filter_sort_val=='cost') {
					$prj_arr = array_sort($proj_arr, 'cost', 'SORT_DESC');
				} else if($filter_sort_val=='directcost') {
					$prj_arr = array_sort($proj_arr, 'directcost', 'SORT_DESC');
				}
			}
			foreach($prj_arr as $ukey=>$pval) {
				$i=1;
				$per_hr = $per_cost =  $per_directcost = 0;
				/* $rate_pr_hr = isset($cost_arr[$ukey])?$cost_arr[$ukey]:0;
				$per_hr   	= ($pval['hour']/160) * 100;
				$per_cost 	= (($pval['hour']*$rate_pr_hr)/(160*$pval['hour'])) * 100; */
				$per_hr   	= ($pval['hour']/$tot_hour) * 100;
				$per_cost 	= ($pval['cost']/$tot_cost) * 100;
				$per_directcost = ($pval['directcost']/$tot_directcost) * 100;
				echo "<tr data-depth='".$i."' class='collapse'>
					<td width='15%'></td>
					<td width='15%'>".$ukey."</td>
					<td width='5%' align='right'>".round($pval['hour'], 1)."</td>
					<td width='5%' align='right'>".round($pval['cost'], 2)."</td>
					<td width='5%' align='right'>".round($pval['directcost'], 2)."</td>
					<td width='5%' align='right'>".round($per_hr, 1)."</td>
					<td width='5%' align='right'>".round($per_cost, 2)."</td>
					<td width='5%' align='right'>".round($per_directcost, 2)."</td>
				</tr>";
				$per_hr		= '';
				$rate_pr_hr = 0;
				$i++;
				$prj_arr = array();
			}
		}
	}
	/* $perc_tot_hr = ($tot_hour/(160*count($cost_arr)))*100;
	$overall_cost = 0;
	foreach($cost_arr as $cs){
		$overall_cost += $cs * 160;
	}
	$perc_tot_cost = ($tot_cost/$overall_cost)*100; */
	
	echo "<tr data-depth='0'>
		<td width='80%' colspan='2' align='right' class='rt-ali'><b>TOTAL:</b></td>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_hour, 1)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_cost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_directcost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_hr, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_cost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_directcost, 0)."</b></th>
		</tr>";
	echo "</table>";
}
?>
</div>
<script>
//export
$(document).ready(function () {
	$("#btnExport").click(function () {
		$("#project_dash").btechco_excelexport({
			containerid: "project_dash"
		   , datatype: $datatype.Table
		   , filename: 'projectwisedata'
		});
	});
});
</script>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/project_drilldown_data_beta.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
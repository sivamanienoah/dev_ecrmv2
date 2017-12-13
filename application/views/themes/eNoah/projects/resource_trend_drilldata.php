<style>
.prac-dt{ text-align:center !important; }
</style>
<div id="drildown_filter_area">
	<div class="pull-left">
		<label>Group By</label>
		<select name="filter_group_by" id="filter_group_by">
			<option value='0' <?php if($filter_group_by == 0) echo "selected='selected'"; ?>>Practice</option>
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
			<input style="height:auto;" type="button" class="positive input-font" name="refine_trend_drilldown_data" id="refine_trend_drilldown_data" value="Go" />
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
$prac = array();
$dept = array();
$skil = array();
$proj = array();
$emp_hr   = array();
$emp_cst  = array();
$tot_hour = 0;
$tot_cost = 0;
$user_data 		= array();
$timesheet_data = array();
if(!empty($resdata)) {
	/* foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost']))
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost'] = $rec->resource_duration_cost;
	
		if(isset($tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['directcost']))
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['directcost'] += $rec->resource_duration_direct_cost;
		else
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['directcost'] = $rec->resource_duration_direct_cost;
	
		if(isset($sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour']))
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour'] +=  $rec->duration_hours;
		else
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour'] =  $rec->duration_hours;
		
		if(isset($sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost']))
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		else
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost'] =  $rec->resource_duration_cost;
	
		if(isset($sub_tot[$rec->dept_name][$rec->empname]['sub_tot_directcost']))
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_directcost'] +=  $rec->resource_duration_direct_cost;
		else
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_directcost'] =  $rec->resource_duration_direct_cost;
		//total
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		$tot_directcost = $tot_directcost + $rec->resource_duration_direct_cost;
		//user
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
		$directcost_arr[$rec->empname] = $rec->direct_cost_per_hour;
		//for empname - sorting-hour
		if(isset($emp_hr[$rec->dept_name][$rec->empname]))
		$emp_hr[$rec->dept_name][$rec->empname] += $rec->duration_hours;
		else 
		$emp_hr[$rec->dept_name][$rec->empname] = $rec->duration_hours;
		//for empname - sorting-cost
		if(isset($emp_cst[$rec->dept_name][$rec->empname]))
		$emp_cst[$rec->dept_name][$rec->empname] += $rec->resource_duration_cost;
		else 
		$emp_cst[$rec->dept_name][$rec->empname] = $rec->resource_duration_cost;
	
		if(isset($emp_directcst[$rec->dept_name][$rec->empname]))
		$emp_directcst[$rec->dept_name][$rec->empname] += $rec->resource_duration_direct_cost;
		else 
		$emp_directcst[$rec->dept_name][$rec->empname] = $rec->resource_duration_direct_cost;
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
		
		$timesheet_data[$rec->dept_name][$rec->username][$rec->project_code][$rec->yr][$rec->month_name]['duration_hours'] += $rec->duration_hours;
		$timesheet_data[$rec->dept_name][$rec->username][$rec->project_code][$rec->yr][$rec->month_name]['total_hours'] 	= get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
		$timesheet_data[$rec->dept_name][$rec->username][$rec->project_code][$rec->yr][$rec->month_name]['direct_rateperhr'] = $directrateCostPerHr;	
		$timesheet_data[$rec->dept_name][$rec->username][$rec->project_code][$rec->yr][$rec->month_name]['rateperhr']        = $rateCostPerHr;
	}
	
	//create array
	if(!empty($timesheet_data) && count($timesheet_data)>0) {
		foreach($timesheet_data as $dept_key=>$user_arr) {
			if(!empty($user_arr) && count($user_arr)>0) {
				foreach($user_arr as $resrc_name=>$prjt_arr) {
					if(!empty($prjt_arr) && count($prjt_arr)>0) {
						foreach($prjt_arr as $prjt_key=>$recval_data) {
							if(count($recval_data)>0 && !empty($recval_data)) {
								foreach($recval_data as $key2=>$value2) {
									$resource_name 	= $resrc_name;
									$emp_name 		= $user_data[$resrc_name]['emp_name'];
									$max_hours 		= $user_data[$resrc_name]['max_hours'];
									$prac_id 		= $user_data[$resrc_name]['prac_id'];
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
											if(isset($tbl_data[$dept_key][$emp_name][$prjt_key]['hour'])) {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['hour'] += $duration_hours;
											} else {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['hour'] = $duration_hours;
											}
											if(isset($tbl_data[$dept_key][$emp_name][$prjt_key]['cost'])) {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['cost'] += $rateHour;
											} else {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['cost'] = $rateHour;
											}
										
											if(isset($tbl_data[$dept_key][$emp_name][$prjt_key]['directcost'])) {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['directcost'] += $rateHour;
											} else {
												$tbl_data[$dept_key][$emp_name][$prjt_key]['directcost'] = $rateHour;
											}
										
											if(isset($sub_tot[$dept_key][$emp_name]['sub_tot_hour'])) {
												$sub_tot[$dept_key][$emp_name]['sub_tot_hour'] +=  $duration_hours;
											} else {
												$sub_tot[$dept_key][$emp_name]['sub_tot_hour'] =  $duration_hours;
											}
											
											if(isset($sub_tot[$dept_key][$emp_name]['sub_tot_cost'])) {
												$sub_tot[$dept_key][$emp_name]['sub_tot_cost'] +=  $rateHour;
											} else {
												$sub_tot[$dept_key][$emp_name]['sub_tot_cost'] =  $rateHour;
											}
										
											if(isset($sub_tot[$dept_key][$emp_name]['sub_tot_directcost'])) {
												$sub_tot[$dept_key][$emp_name]['sub_tot_directcost'] +=  $rateHour;
											} else {
												$sub_tot[$dept_key][$emp_name]['sub_tot_directcost'] =  $rateHour;
											}
											//total
											$tot_hour = $tot_hour + $duration_hours;
											$tot_cost = $tot_cost + $rateHour;
											$tot_directcost = $tot_directcost + $rateHour;
											//user
											$cost_arr[$emp_name] = $direct_rateperhr1;
											$directcost_arr[$emp_name] = $direct_rateperhr1;
											//for empname - sorting-hour
											if(isset($emp_hr[$dept_key][$emp_name])) {
												$emp_hr[$dept_key][$emp_name] += $duration_hours;
											} else {
												$emp_hr[$dept_key][$emp_name] = $duration_hours;
											}
											//for empname - sorting-cost
											if(isset($emp_cst[$dept_key][$emp_name])) {
												$emp_cst[$dept_key][$emp_name] += $rateHour;
											} else {
												$emp_cst[$dept_key][$emp_name] = $rateHour;
											}
										
											if(isset($emp_directcst[$dept_key][$emp_name])){
												$emp_directcst[$dept_key][$emp_name] += $rateHour;
											} else {
												$emp_directcst[$dept_key][$emp_name] = $rateHour;
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
// echo "<pre>"; print_r($emp_hr); echo "</pre>";
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm"><?php echo $heading; ?> :: Group By - Resource</h2>
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
if(!empty($tbl_data)) {
	echo "<table id='project_dash' class='data-table'>";
	echo "<tr>
			<th class='prac-dt' width='15%'><b>USER NAME</b></th>
			<th class='prac-dt' width='15%'><b>PROJECT NAME</b></th>
			<th class='prac-dt' width='5%'><b>HOUR</b></th>
			<th class='prac-dt' width='5%'><b>COST</b></th>
			<th class='prac-dt' width='5%'><b>DIRECT COST</b></th>
			<th class='prac-dt' width='5%'><b>% of HOUR</b></th>
			<th class='prac-dt' width='5%'><b>% of COST</b></th>
			<th class='prac-dt' width='5%'><b>% of DIRECT COST</b></th>";
	foreach($tbl_data as $dept=>$us_ar) {
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($emp_hr[$dept]);
				$us_sort_ar = $emp_hr[$dept];
			} else if($filter_sort_val=='cost') {
				asort($emp_cst[$dept]);
				$us_sort_ar = $emp_cst[$dept];
			} else if($filter_sort_val=='directcost') {
				asort($emp_directcst[$dept]);
				$us_sort_ar = $emp_directcst[$dept];
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($emp_hr[$dept]);
				$us_sort_ar = $emp_hr[$dept];
			} else if($filter_sort_val=='cost') {
				arsort($emp_cst[$dept]);
				$us_sort_ar = $emp_cst[$dept];
			} else if($filter_sort_val=='directcost') {
				arsort($emp_directcst[$dept]);
				$us_sort_ar = $emp_directcst[$dept];
			}
		}
		// foreach($us_ar as $p_name=>$proj_ar) {
		$user_arr = array();
		foreach($us_sort_ar as $p_name=>$proj_ar) {
			$user_arr = $us_ar[$p_name];
			$i = 0;
			$rs_sub_tot_hr   = 0;
			$rs_sub_tot_cost = 0;
			$rs_sub_tot_directcost = 0;
			$rs_sub_tot_hr   = ($sub_tot[$dept][$p_name]['sub_tot_hour']/$tot_hour)*100;
			$rs_sub_tot_cost = ($sub_tot[$dept][$p_name]['sub_tot_cost']/$tot_cost)*100;
			$rs_sub_tot_directcost = ($sub_tot[$dept][$p_name]['sub_tot_directcost']/$tot_directcost)*100;
			$perc_tot_hr   += $rs_sub_tot_hr;
			$perc_tot_cost += $rs_sub_tot_cost;
			$perc_tot_directcost += $rs_sub_tot_directcost;
			$calc_tot_hour += $sub_tot[$dept][$p_name]['sub_tot_hour'];
			$calc_tot_cost += $sub_tot[$dept][$p_name]['sub_tot_cost'];
			$calc_tot_directcost += $sub_tot[$dept][$p_name]['sub_tot_directcost'];
			echo "<tr data-depth='".$i."' class='collapse'>
					<th align='left' class='collapse lft-ali'><span class='toggle'> ".strtoupper($p_name)."</span></th>
					<th width='15%' align='right' class='rt-ali'>SUB TOTAL:</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_hour'], 1)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_cost'], 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_directcost'], 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($rs_sub_tot_hr, 1)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($rs_sub_tot_cost, 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($rs_sub_tot_directcost, 2)."</th>
				</tr>";
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					$usr_arr = array_sort($user_arr, 'hour', 'SORT_ASC');
				} else if($filter_sort_val=='cost') {
					$usr_arr = array_sort($user_arr, 'cost', 'SORT_ASC');
				} else if($filter_sort_val=='directcost') {
					$usr_arr = array_sort($user_arr, 'directcost', 'SORT_ASC');
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					$usr_arr = array_sort($user_arr, 'hour', 'SORT_DESC');
				} else if($filter_sort_val=='cost') {
					$usr_arr = array_sort($user_arr, 'cost', 'SORT_DESC');
				} else if($filter_sort_val=='directcost') {
					$usr_arr = array_sort($user_arr, 'directcost', 'SORT_DESC');
				}
			}
			// foreach($proj_ar as $pkey=>$pval) {
			foreach($usr_arr as $pkey=>$pval) {
				$i=1;
				$rate_pr_hr = isset($cost_arr[$p_name])?$cost_arr[$p_name]:0;
				$directrate_pr_hr = isset($directcost_arr[$p_name])?$directcost_arr[$p_name]:0;
				$name       = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				$per_hr = $per_cost =  $per_directcost = 0;
				$per_hr   	= ($pval['hour']/$tot_hour) * 100;
				$per_cost 	= ($pval['cost']/$tot_cost) * 100;
				$per_directcost = ($pval['directcost']/$tot_directcost) * 100;
				
				echo "<tr data-depth='".$i."' class='collapse'>
					<td width='15%'></td>
					<td width='15%'>".$name."</td>
					<td width='5%' align='right'>".round($pval['hour'], 1)."</td>
					<td width='5%' align='right'>".round($pval['cost'], 2)."</td>
					<td width='5%' align='right'>".round($pval['directcost'], 2)."</td>
					<td width='5%' align='right'>".round($per_hr, 1)."</td>
					<td width='5%' align='right'>".round($per_cost, 2)."</td>
					<td width='5%' align='right'>".round($per_directcost, 2)."</td>
				</tr>";
				$per_hr = '';
				$i++;
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
		<td width='5%' align='right' class='rt-ali'><b>".round($calc_tot_hour, 1)."</b></td>
		<td width='5%' align='right' class='rt-ali'><b>".round($calc_tot_cost, 0)."</b></td>
		<td width='5%' align='right' class='rt-ali'><b>".round($calc_tot_directcost, 0)."</b></td>
		<td width='5%' align='right' class='rt-ali'><b>".round($perc_tot_hr, 0)."</b></td>
		<td width='5%' align='right' class='rt-ali'><b>".round($perc_tot_cost, 0)."</b></td>
		<td width='5%' align='right' class='rt-ali'><b>".round($perc_tot_directcost, 0)."</b></td>
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
		   , filename: 'resourcewisedata'
		});
	});
	var start_date = '<?php echo $start_date ?>';
	$('#start_date').val(start_date);
});
</script>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/project_drilldown_data.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
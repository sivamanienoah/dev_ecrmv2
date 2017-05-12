<style>
.prac-dt{ text-align:center !important; }
.toggle { display: inline-block; }
</style>
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
$sub_tot_hr    = array();
$sub_tot_cst   = array();
$pr_usercnt    = array();
$sk_usercnt    = array();
$skil_sub_tot  = array();
$skil_sort_hr  = array();
$skil_sort_cst = array();
$user_hr 	   = array();
$user_cst 	   = array();
$cost_arr 	   = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
$user_data 		= array();
$timesheet_data = array();
$sub_tot_entity_hr 		= array();
$sub_tot_entity_cst 	= array();
$sub_tot_entity_dircst 	= array();
$tot_hour = 0;
$tot_cost = 0;
// echo "<pre>"; print_r($resdata); echo "</pre>";
if(!empty($resdata)) {
	foreach($resdata as $rec) {
		$rates 				= $conversion_rates;
		$financialYear      = get_current_financial_year($rec->yr, $rec->month_name);
		$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id,$financialYear);
		
		$user_data[$rec->username]['emp_name'] 		= $rec->empname;
		$user_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
		$user_data[$rec->username]['dept_name'] 	= $rec->dept_name;
		
		$rateCostPerHr 			= round($rec->cost_per_hour * $rates[1][$this->default_cur_id], 2);
		$directrateCostPerHr 	= round($rec->direct_cost_per_hour * $rates[1][$this->default_cur_id], 2);
		
		if(isset($timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'])) {
			$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
		} else {
			$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] = $rec->duration_hours;
		}
		
		$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
		$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->resoursetype][$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr']        = $rateCostPerHr;
		
		$timesheet_data[$rec->entity_name][$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username, $rec->yr, $rec->month_name, array('Leave','Hol'));
	}
	
	// echo "<pre>"; print_r($timesheet_data); echo "</pre>";
	
	if(!empty($timesheet_data) && count($timesheet_data)>0) {
		foreach($timesheet_data as $entity_key=>$entity_arr) {
			if(!empty($entity_arr) && count($entity_arr)>0) {
				foreach($entity_arr as $dept_key=>$prac_arr) {
					if(!empty($prac_arr) && count($prac_arr)>0) {
						foreach($prac_arr as $prac_key=>$skill_arr) { #echo "dept key " .$dept_key . " practice ".$prac_key; print_r($skill_arr); echo "</pre>"; die;
							if(!empty($skill_arr) && count($skill_arr)>0) {
								foreach($skill_arr as $skill_key=>$resrc_type_arr) {
									if(!empty($resrc_type_arr) && count($resrc_type_arr)>0) {
										foreach($resrc_type_arr as $resrc_type_key=>$resrc_data) {
											if(!empty($resrc_data) && count($resrc_data)>0) {
												foreach($resrc_data as $resrc_name=>$recval_data) {
													$resource_name 	= $resrc_name;
													$emp_name 		= $user_data[$resrc_name]['emp_name'];
													$max_hours 		= $user_data[$resrc_name]['max_hours'];
													$dept_name 		= $user_data[$resrc_name]['dept_name'];
													if(count($recval_data)>0 && !empty($recval_data)) {
														foreach($recval_data as $key2=>$value2) {
															$year = $key2;
															if(count($value2)>0 && !empty($value2)) {
																foreach($value2 as $key3=>$value3) {
																	$individual_billable_hrs = 0;
																	$ts_month		 	  	 = $key3;
																	if(count($value3)>0 && !empty($value3)) {
																		foreach($value3 as $pjt_code=>$value4) {
																			if($pjt_code != 'total_hours'){ 
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
																				/*calc*/
																				$rateHour = $duration_hours * $direct_rateperhr1;

																				//hour$email_shortname = ;
																				if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour'])) {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour'] += $duration_hours;
																				} else {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['hour']  = $duration_hours;
																				}
																				//cost
																				if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['cost'])) {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][$pjt_code][$emp_name]['cost'] += $rateHour;
																				} else {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['cost'] = $rateHour;
																				}
																				//direct_cost
																				if(isset($tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'])) {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'] += $rateHour;
																				} else {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$resrc_type_key][substr($ts_month,0,3).' '.$year][$pjt_code][$emp_name]['directcost'] = $rateHour;
																				}
																				
																				
																				//total
																				$tot_hour 		= $tot_hour + $duration_hours;
																				$tot_cost 		= $tot_cost + $rateHour;
																				$tot_directcost = $tot_directcost + $rateHour;
																				
																				//cost
																				$cost_arr[$emp_name] 		= $rateHour;
																				$directcost_arr[$emp_name] 	= $rateHour;
																				
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
					}
				}
			}
		}
	}
}
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
?>
<div>
<?php
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;
if(!empty($tbl_data)) {
	echo "<table id='project_dash' class='proj-dash-table data-table proj-table-odd-even'>
			<tr>
			<th class='prac-dt' width='10%'>ENTITY</th>
			<th class='prac-dt' width='6%'>DEPARTMENT</th>
			<th class='prac-dt' width='10%'>PRACTICE</th>
			<th class='prac-dt' width='12%'>SKILL</th>
			<th class='prac-dt' width='6%'>RESOURCE TYPE</th>
			<th class='prac-dt' width='5%'>Month Year</th>
			<th class='prac-dt' width='15%'>PROJECT</th>
			<th class='prac-dt' width='7%'>USER</th>
			<th class='prac-dt' width='5%'>HOUR</th>
			<th class='prac-dt' width='5%'>COST</th>
			<th class='prac-dt' width='5%'>DIRECT COST</th>
			</tr>";
	foreach($tbl_data as $entiyKey=>$entiyArr) {
		if(!empty($entiyArr) && count($entiyArr)>0) {
			foreach($entiyArr as $deptKey=>$deptArr) {
				if(!empty($deptArr) && count($deptArr)>0) {
					foreach($deptArr as $pracKey=>$pracArr) {
						if(!empty($pracArr) && count($pracArr)>0) {
							foreach($pracArr as $skilKey=>$skilArr) {
								if(!empty($skilArr) && count($skilArr)>0) {
									foreach($skilArr as $resrcTypeKey=>$resrcTypeArr) {
										if(!empty($resrcTypeArr) && count($resrcTypeArr)>0) {
											foreach($resrcTypeArr as $yrMonKey=>$yrMonArr) {
												if(!empty($yrMonArr) && count($yrMonArr)>0) {
													foreach($yrMonArr as $pjtCdeKey=>$pjtCdeArr) {
														if(!empty($pjtCdeArr) && count($pjtCdeArr)>0) {
															foreach($pjtCdeArr as $resrcNmeKey=>$resrcNmeArr) {
																$i=0;
																echo "<tr data-depth='".$i."' class='collapse'>
						<td width='10%' align='left' class='collapse lft-ali'><span class='toggle'>".strtoupper($entiyKey)."</b></span></td>
						<td width='6%' align='left' class='collapse lft-ali'>".$deptKey."</td>
						<td width='10%' align='left' class='collapse lft-ali'>".$pracKey."</td>
						<td width='12%' align='left' class='collapse lft-ali'>".$skilKey."</td>
						<td width='6%' align='left' class='collapse lft-ali'>".$resrcTypeKey."</td>
						<td width='5%' align='right' class='rt-ali'>".$yrMonKey."</td>
						<td width='15%' align='right' class='rt-ali'>".$pjtCdeKey."</td>
						<td width='7%' align='right' class='rt-ali'>".$resrcNmeKey."</td>
						<td width='5%' align='right' class='rt-ali'>".$resrcNmeArr['hour']."</td>
						<td width='5%' align='right' class='rt-ali'>".$resrcNmeArr['cost']."</td>
						<td width='5%' align='right' class='rt-ali'>".$resrcNmeArr['directcost']."</td>
					</tr>"; $i++;
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
	}

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
		   , filename: 'entity_wise_data'
		});
	});
});
</script>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/cost_report_grid.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
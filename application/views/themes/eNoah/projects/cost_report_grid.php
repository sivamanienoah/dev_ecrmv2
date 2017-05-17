<style>
.prac-dt{ text-align:center !important; }
.toggle { display: inline-block; }
.tr_othercost { background-color:#f2dede !important; }
#it_cost_grid{
	border-bottom:0px;
}
.it_cost_sub_grid {
	border:1px solid #ddd;
	border-top:0px;	
	width:99.9%;
}
.it_cost_sub_grid tr td span{	
	float: right;
    padding: 5px;
}
.it_cost_sub_grid td{	
	border-right:1px solid #ddd;
	
}
.it_cost_sub_grid td:last-child{
	border-right:0px;
}
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
	
	// echo "<pre>"; print_r($other_cost_arr); echo "</pre>";
	
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
																	$individual_billable_hrs = $resrc_type_arr[$resrc_name][$year][$ts_month]['total_hours'];
																	if(is_array($value3) && count($value3)>0 && !empty($value3)) {
																		foreach($value3 as $pjt_code=>$value4) {
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
																			
																			//hour;
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
																			
																			//other cost
																			if(is_array($other_cost_arr[$pjt_code][$year]) && !empty($other_cost_arr[$pjt_code][$year])) {
																				//other cost resource type as billable
																				$other_cost_resrc_type = 'Other Cost';
																				foreach($other_cost_arr[$pjt_code][$year] as $ocMonKey=>$ocVal) {
																					$tbl_data[$entity_key][$dept_key][$prac_key][$skill_key][$other_cost_resrc_type][substr(trim($ocMonKey),0,3).' '.$year][$pjt_code][$ocVal['oc_descrptn']]['cost'] = $ocVal['oc_val'];
																				}
																				$otherCostIncludedProjects[] = $pjt_code; 
																			}
																			//other cost
																			
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
// echo "<pre>"; print_r($tbl_data); echo "</pre><br>***************<br>";
// echo "<pre>"; print_r(array_unique($otherCostIncludedProjects)); echo "</pre>";
foreach($other_cost_arr as $ocprjkey=>$va) {
	$pjtOcArr[] = $ocprjkey;
}
// echo "<pre>"; print_r(array_unique($pjtOcArr)); echo "</pre>";
$needAddOtherCostArr = array_diff($pjtOcArr, $otherCostIncludedProjects);

//append other cost projects in tbl_data array
if(!empty($needAddOtherCostArr)) {
	foreach($needAddOtherCostArr as $row) {
		if(!empty($other_cost_arr[$row]) && count($other_cost_arr[$row])>0) {
			foreach($other_cost_arr[$row] as $oc_year=>$oc_yearArr) {
				if(!empty($oc_yearArr) && count($oc_yearArr)>0) {
					foreach($oc_yearArr as $ocMonthKey=>$ocArrRow) {
						$oc_entity_key 	= $ocArrRow['oc_entity'];
						$oc_dept_key 	= $ocArrRow['oc_dept'];
						$oc_prac_key 	= $ocArrRow['oc_practice'];
						$oc_mon_yr 		= substr($ocMonthKey,0,3).' '.$oc_year;
						$oc_other_cost_resrc_type = 'Other Cost';
						$tbl_data[$oc_entity_key][$oc_dept_key][$oc_prac_key]['oc_skill'][$oc_other_cost_resrc_type][$oc_mon_yr][$row][$ocArrRow['oc_descrptn']]['cost'] = $ocArrRow['oc_val'];
					}
				}
			}
		}
	}
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
}

?>
<div>
<div class="tst it_cost_grid_div">
<?php
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;

	echo "<table id='it_cost_grid' class='data-tbl dashboard-heads dataTable it_cost_grid'>
			<thead>
			<tr>
			<th class='prac-dt' width='10%'>ENTITY</th>
			<th class='prac-dt' width='6%'>DEPARTMENT</th>
			<th class='prac-dt' width='10%'>PRACTICE</th>
			<th class='prac-dt' width='12%'>SKILL</th>
			<th class='prac-dt' width='6%'>RESOURCE TYPE</th>
			<th class='prac-dt' width='5%'>Month Year</th>
			<th class='prac-dt' width='15%'>PROJECT</th>
			<th class='prac-dt' width='7%'>RESOURCE</th>
			<th class='prac-dt' width='5%'>HOUR</th>
			<th class='prac-dt' width='5%'>COST</th>
			</tr>";
			echo "</thead><tbody>";
	if(!empty($tbl_data) && count($tbl_data)>0) {
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
																	$i				 = 0;
																	$tempSkilKey 	 = $skilKey;
																	$tempresrcNmeKey = $resrcNmeKey;
																	$tempResrcHour 	 = round($resrcNmeArr['hour'], 1);
																	$tempCls		 = '';
																	if('Other Cost'==$resrcTypeKey) {
																		$tempSkilKey 	 = $tempResrcHour = '-';
																		$tempCls	 	 = 'tr_othercost';
																		$tot_cost	    += $resrcNmeArr['cost'];
																	}
																	$pjt_nme = isset($project_master[$pjtCdeKey]) ? $project_master[$pjtCdeKey] : $pjtCdeKey;
																	echo "<tr class='".$tempCls."' data-depth='".$i."'>
							<td width='10%' align='left' class='collapse lft-ali'><span class='toggle'>".$entiyKey."</b></span></td>
							<td width='6%' align='left' class='collapse lft-ali'>".$deptKey."</td>
							<td width='10%' align='left' class='collapse lft-ali'>".$pracKey."</td>
							<td width='12%' align='left' class='collapse lft-ali'>".$tempSkilKey."</td>
							<td width='6%' align='left' class='collapse lft-ali'>".$resrcTypeKey."</td>
							<td width='5%'>".$yrMonKey."</td>
							<td width='15%'>".$pjt_nme."</td>
							<td width='7%'>".$tempresrcNmeKey."</td>
							<td width='5%' align='right' class='rt-ali'>".$tempResrcHour."</td>
							<td width='5%' align='right' class='rt-ali'>".round($resrcNmeArr['cost'], 2)."</td>
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
	}
	/* echo "<tfoot><tr>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'></td>
			<td align='right' class='rt-ali'><b>Total:</b></td>
			<td width='5%' align='right' class='rt-ali'>".round($tot_hour, 1)."</td>
			<td width='5%' align='right' class='rt-ali'>".round($tot_cost, 2)."</td>
		</tr></tfoot>"; */
	echo "</tbody></table>";
	echo "<table class='data-tbl dashboard-heads dataTable it_cost_grid'><tr>
			<td width='75%' align='right' class=''><span><b>Total:</b></span></td>
			<td width='5%' align='right' class='rt-ali'><span>".round($tot_hour, 1)."</span></td>
			<td width='5%' align='right' class='rt-ali'><span>".round($tot_cost, 2)."</span></td>
		</tr></table>";
?>
</div>
</div>
<script>
var filter_area_status = '<?php echo $filter_area_status; ?>';
</script>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/cost_report_grid.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
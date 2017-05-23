<style>
.prac-dt{ text-align:center !important; }
</style>
<div class="clear"></div>
<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
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
$directcost_arr = array();
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

if(!empty($resdata)) {
$timesheet_data = array();
if(count($resdata)>0) {
	// $rates = $this->report_lead_region_model->get_currency_rates_new();
	foreach($resdata as $rec) {	
		$financialYear 		= get_current_financial_year($rec->yr,$rec->month_name);
		$max_hours_resource = get_practice_max_hour_by_financial_year($rec->practice_id, $financialYear);
		
		$timesheet_data[$rec->username]['practice_id'] 	= $rec->practice_id;
		$timesheet_data[$rec->username]['max_hours'] 	= $max_hours_resource->practice_max_hours;
		$timesheet_data[$rec->username]['dept_name'] 	= $rec->dept_name;
		
		// $rateCostPerHr 		 = round($rec->cost_per_hour*$rates[1][$this->default_cur_id], 2);
		// $directrateCostPerHr = round($rec->direct_cost_per_hour*$rates[1][$this->default_cur_id], 2);
		$rateCostPerHr 		 = round($rec->cost_per_hour, 2);
		$directrateCostPerHr = round($rec->direct_cost_per_hour, 2);
		$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['duration_hours'] += $rec->duration_hours;
		//$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['cost'] = $rec->cost_per_hour;
		$timesheet_data[$rec->username][$rec->yr][$rec->month_name]['total_hours'] = get_timesheet_hours_by_user($rec->username,$rec->yr,$rec->month_name,array('Leave','Hol'));
		$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['direct_rateperhr'] = $directrateCostPerHr;	
		$timesheet_data[$rec->username][$rec->yr][$rec->month_name][$rec->project_code]['rateperhr'] 		= $rateCostPerHr;
		$timesheet_data[$rec->username]['empname'] = $rec->empname;
	}
	$resource_cost = array();	
	if(count($timesheet_data)>0 && !empty($timesheet_data)) {
		foreach($timesheet_data as $key1=>$value1) {
			$resource_name = $key1;
			$max_hours = $value1['max_hours'];
			$dept_name = $value1['dept_name'];
			$resource_cost[$resource_name]['dept_name'] = $dept_name;
			if(count($value1)>0 && !empty($value1)){
				foreach($value1 as $key2=>$value2) {
					$year = $key2;
					if(count($value2)>0 && !empty($value2)){
						foreach($value2 as $key3=>$value3) {
							$individual_billable_hrs = 0;
							$month		 	 		 = $key3;
							if(count($value3)>0 && !empty($value3)){
								foreach($value3 as $key4=>$value4) {
									if($key4 != 'total_hours'){
										$individual_billable_hrs = $value3['total_hours'];
										$duration_hours			= $value4['duration_hours'];
										$rate				 	= $value4['rateperhr'];
										$direct_rateperhr	 	= $value4['direct_rateperhr'];
										$rate1 = $rate;
										$direct_rateperhr1 = $direct_rateperhr;
										if($individual_billable_hrs>$max_hours){
											//echo 'max'.$max_hours.'<br>';
											$percentage 		= ($max_hours/$individual_billable_hrs);
											// $rate1 				= number_format(($percentage*$rate),2);
											$rate1 				= number_format(($percentage*$direct_rateperhr),2);
											$direct_rateperhr1  = number_format(($percentage*$direct_rateperhr),2);
										}
										$resource_cost[$resource_name][$year][$month][$key4]['duration_hours'] 	+= $duration_hours;
										// $resource_cost[$resource_name][$year][$month][$key4]['total_cost'] 		+= ($duration_hours*$rate1);
										$resource_cost[$resource_name][$year][$month][$key4]['total_cost'] 		+= ($duration_hours*$direct_rateperhr1);
										$resource_cost[$resource_name][$year][$month][$key4]['total_dc_cost'] 	+= ($duration_hours*$direct_rateperhr1);
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
// echo '<pre>';print_r($resource_cost); die;
if(count($resource_cost)>0 && !empty($resource_cost)){
	$timesheet_projects = array();
	foreach($resource_cost as $resourceName => $array1){
		$dept_name = $resource_cost[$resourceName]['dept_name'];
		if(count($array1)>0 && !empty($array1)){
			foreach($array1 as $year => $array2){
				if($year !='dept_name'){
					if(count($array2)>0 && !empty($array2)){
						foreach($array2 as $month => $array3){
							$duration_hours = 0;
							$total_cost 	= 0;
							$total_dc_cost 	= 0;
							foreach($array3 as $project_code => $array4){
								$available_projects[] = $project_code;
								if(!in_array($project_code, $timesheet_projects)) {
									$timesheet_projects[] = $project_code;
								}								
								$duration_hours = $array4['duration_hours'];
								$total_cost 	= $array4['total_cost'];
								$total_dc_cost 	= $array4['total_dc_cost'];

								if(isset($sub_tot[$project_code]['sub_tot_hour']))
								$sub_tot[$project_code]['sub_tot_hour'] +=  $duration_hours;
								else
								$sub_tot[$project_code]['sub_tot_hour'] =  $duration_hours;
								
								if(isset($sub_tot[$project_code]['sub_tot_cost']))
								$sub_tot[$project_code]['sub_tot_cost'] +=  $total_cost;
								else
								$sub_tot[$project_code]['sub_tot_cost'] =  $total_cost;
							
								if(isset($sub_tot[$project_code]['sub_tot_directcost']))
								$sub_tot[$project_code]['sub_tot_directcost'] +=  $total_dc_cost;
								else
								$sub_tot[$project_code]['sub_tot_directcost'] =  $total_dc_cost;
								
								if(isset($sub_tot[$project_code][$resourceName]['hour'])) {
									$sub_tot[$project_code][$resourceName]['hour'] += $duration_hours;
								} else {
									$sub_tot[$project_code][$resourceName]['hour'] = $duration_hours;
								}

								if(isset($sub_tot[$project_code][$resourceName]['cost']))
								$sub_tot[$project_code][$resourceName]['cost'] += $total_cost;
								else
								$sub_tot[$project_code][$resourceName]['cost'] = $total_cost;

								if(isset($sub_tot[$project_code][$resourceName]['directcost']))
								$sub_tot[$project_code][$resourceName]['directcost'] += $total_dc_cost;
								else
								$sub_tot[$project_code][$resourceName]['directcost'] = $total_dc_cost;							
							
								$tot_hour 		= $tot_hour + $duration_hours;
								$tot_cost 		= $tot_cost + $total_cost;
								$tot_directcost = $tot_directcost + $total_dc_cost;
								
								$cost_arr[$resourceName] = $rec->cost_per_hour;
								$directcost_arr[$resourceName] = $rec->direct_cost_per_hour;
								
								//for project_code - sorting-directcost
								if(isset($prjt_directcst[$project_code]))
								$prjt_directcst[$project_code] += $total_dc_cost;
								else 
								$prjt_directcst[$project_code] = $total_dc_cost;
							}
						}
					}
				}
			}
		}
	}
}
//**Get the other cost value projects only**//
$resource_cost_not_value_project = array_diff($othercost_projects[$practices_name], $timesheet_projects);
// echo "<pre>"; print_r($othercost_projects[$practices_name]);
// echo "<br />****"; print_r($timesheet_projects);
// echo "<br />****"; print_r($resource_cost_not_value_project); 

/**including the other cost values crm projects only & not in timesheet**/
if(is_array($resource_cost_not_value_project) && !empty($resource_cost_not_value_project) && count($resource_cost_not_value_project)>0) {
	foreach($resource_cost_not_value_project as $crmPjtName) {
		$sub_tot[$crmPjtName]['sub_tot_hour'] 		= 0;
		$sub_tot[$crmPjtName]['sub_tot_cost'] 		= 0;
		$sub_tot[$crmPjtName]['sub_tot_directcost'] = 0;
	}
}

$other_cost_arr = array();
//calculating the other cost
$other_cost_arr['other_cost_total'] = 0;
if(!empty($sub_tot)) {
	foreach($sub_tot as $pname=>$pvals) {
		$other_cost_val 					 = getOtherCostByProjectIdByDateRange($pname, $this->default_cur_id, $start_date, $end_date);
		// $other_cost_val 					 = getOtherCostByProjectIdByDateRange('ITS-SAR-01-0117', $this->default_cur_id, $start_date, $end_date);
		if(isset($other_cost_val['value']) && ($other_cost_val['value'] != 0)) {
			$other_cost_arr[$pname]['detail']  	 = $other_cost_val['det'];
			$other_cost_arr[$pname]['value']   	 = $other_cost_val['value'];
			$other_cost_arr['other_cost_total'] += $other_cost_val['value'];
		}
	}
}

$tot_cost 	 = $tot_cost + $other_cost_arr['other_cost_total']; //merging the other cost values
echo "<pre>"; print_r($other_cost_arr); echo "</pre>"; exit;
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm"><?php echo $practices_name; ?> - Project</h2>
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
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = $calc_tot_directcost =  $calc_tot_othercost = 0;
// echo '<pre>';print_r($sub_tot); exit;
if(!empty($sub_tot)) {
	echo "<table id='project_dash' class='data-table'>
			<tr>
				<th class='prac-dt' width='15%'><b>PROJECT NAME</b></th>
				<th class='prac-dt' width='15%'><b>USER NAME</b></th>
				<th class='prac-dt' width='5%'><b>HOUR</b></th>
				<th class='prac-dt' width='5%'><b>RESOURCE COST</b></th>
				<th class='prac-dt' width='5%'><b>OTHER COST</b></th>
				<th class='prac-dt' width='5%'><b>TOTAL COST</b></th>
				<th class='prac-dt' width='5%'><b>% of HOUR</b></th>
				<th class='prac-dt' width='5%'><b>% of RESOURCE COST</b></th>
				<th class='prac-dt' width='5%'><b>% of TOTAL COST</b></th>
			</tr>";
		//foreach($tbl_data as $projectCode => $proj_ar) {
		//asort($sub_tot);
		function cmp($a, $b) {
			if ($a['sub_tot_hour'] == $b['sub_tot_hour']) {
				return 0;
			}
			return ($a['sub_tot_hour'] > $b['sub_tot_hour']) ? -1 : 1;
		} 
		uasort($sub_tot, 'cmp');
		$sort_ar = $sub_tot;
		$proj_arr = array();
		// echo '<pre>';print_r($sort_ar); exit;
		$other_cost_val = 0;
		foreach($sort_ar as $p_name=>$user_ar) {
			$other_cost_val = $other_cost_arr[$p_name];
			if( ($sub_tot[$p_name]['sub_tot_directcost'] != 0) && ($sub_tot[$p_name]['sub_tot_directcost'] != 0) && ($sub_tot[$p_name]['sub_tot_directcost'] != 0)) {
				
				$i       = 0;
				$pj_tot_cost = $per_sub_hr = $sub_tot_pj_cost = 0;
				$name    				= isset($project_master[$p_name]) ? $project_master[$p_name] : $p_name;
				$per_sub_hr 	 		= ($sub_tot[$p_name]['sub_tot_hour']/$tot_hour)*100;
				$sub_tot_pj_cost 		= (($sub_tot[$p_name]['sub_tot_cost']+$other_cost_val['value'])/$tot_cost)*100;
				$sub_tot_pj_directcost 	= ($sub_tot[$p_name]['sub_tot_directcost']/$tot_directcost)*100;
				// $sub_tot_pj_directcost 	= ($sub_tot[$p_name]['sub_tot_directcost']/$tot_cost)*100;
				$perc_tot_hr   			+= $per_sub_hr;
				$perc_tot_directcost 	+= $sub_tot_pj_directcost;
				$perc_tot_cost 			+= $sub_tot_pj_cost;
				$calc_tot_hour 			+= $sub_tot[$p_name]['sub_tot_hour'];
				$calc_tot_directcost 	+= $sub_tot[$p_name]['sub_tot_directcost'];
				$calc_tot_othercost 	+= $other_cost_val['value'];
				$calc_tot_cost 			+= $sub_tot[$p_name]['sub_tot_cost']+$other_cost_val['value'];
				
				echo "<tr data-depth='".$i."' class='collapse'>
					<th width='15%' align='left' class='collapse lft-ali'><span class='toggle'> ".strtoupper($name)."</span></th>
					<th width='15%' align='right' class='rt-ali'>SUB TOTAL(PROJECT WISE):</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$p_name]['sub_tot_hour'], 1)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$p_name]['sub_tot_directcost'], 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".$other_cost_val['value']."</th>
					<th width='5%' align='right' class='rt-ali'>".round(($sub_tot[$p_name]['sub_tot_cost']+$other_cost_val['value']), 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($per_sub_hr, 1)."</th>
					<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pj_directcost, 2)."</th>
					<th width='5%' align='right' class='rt-ali'>".round((($sub_tot[$p_name]['sub_tot_cost']+$other_cost_val['value'])/$tot_cost)*100, 2)."</th>
				</tr>";
				//echo '<pre>';print_r($user_ar);
				if(count($user_ar)>0 && !empty($user_ar)):
				$i=1;
				$j = 0;
				foreach($user_ar as $ukey=>$pval) {
					
					if(!empty($pval['hour'])):
					$i=1;
					$per_hr = $per_cost = $per_directcost = 0;
					$per_hr   	= ($pval['hour']/$tot_hour) * 100;
					$per_cost 	= ($pval['cost']/$tot_cost) * 100;
					$per_directcost  = ($pval['directcost']/$tot_directcost) * 100;
					
					echo "<tr data-depth='".$i."' class='collapse'>";
						if($j==0){
							echo "<td width='15%' align='right'><b>Resources</b></td>";
						} else {
							echo "<td width='15%'></td>";
						}
						echo "<td width='15%'>".$timesheet_data[$ukey]['empname']."</td>
						<td width='5%' align='right'>".round($pval['hour'], 1)."</td>
						<td width='5%' align='right'>".round($pval['directcost'], 2)."</td>
						<td width='5%' align='right'>-</td>
						<td width='5%' align='right'>".round($pval['cost'], 2)."</td>
						<td width='5%' align='right'>".round($per_hr, 1)."</td>
						<td width='5%' align='right'>".round($per_directcost, 2)."</td>
						<td width='5%' align='right'>".round($per_cost, 2)."</td>
					</tr>";
					$per_hr		= '';
					$rate_pr_hr = 0;
					$i++;
					$j++;
					endif;
					$user_ar = array();
				}
				endif;
				
				//other cost value with description
				if((!empty($other_cost_val['detail'])) && count($other_cost_val['detail'])>0) {
					$e = 0;
					foreach($other_cost_val['detail'] as $oc_key=>$oc_val) {
						$p=1;
						$per_cost = 0;
						if(!empty($oc_val['amt'])){
							$per_cost = ($oc_val['amt']/$tot_cost) * 100;
						}
						echo "<tr data-depth='".$i."' class='collapse'>";
						if($e==0){
							echo "<td width='15%' align='right'><b>Other Cost</b></td>";
						} else {
							echo "<td width='15%'></td>";
						}
						echo "<td width='15%'>".ucfirst($oc_val['desc'])."</td>
							<td width='5%' align='right'>-</td>
							<td width='5%' align='right'>-</td>
							<td width='5%' align='right'>".round($oc_val['amt'], 2)."</td>
							<td width='5%' align='right'>".round($oc_val['amt'], 2)."</td>
							<td width='5%' align='right'>-</td>
							<td width='5%' align='right'>-</td>
							<td width='5%' align='right'>".round($per_cost, 2)."</td>
						</tr>";
						$p++;
						$e++;
					}
				}
			} //if condition
		}
		
	echo "<tr data-depth='0'>
		<td width='80%' colspan='2' align='right' class='rt-ali'><b>TOTAL:</b></td>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_hour, 1)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_directcost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_othercost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($calc_tot_cost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_hr, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_directcost, 0)."</b></th>
		<th width='5%' align='right' class='rt-ali'><b>".round($perc_tot_cost, 0)."</b></th>
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
<script type="text/javascript" src="assets/js/projects/project_drilldown_data.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
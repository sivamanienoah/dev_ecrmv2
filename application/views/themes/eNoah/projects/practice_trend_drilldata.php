<style>
.prac-dt{ text-align:center !important; }
.toggle { display: inline-block; }
</style>
<div id="drildown_filter_area" class="group-section" style="margin: 10px 0px 0px;">
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
	<div class="pull-left" style="margin:0 15px 0 0;">
		<label>Sort Value</label>
		<select name="filter_sort_val" id="filter_sort_val">
			<option value='hour' <?php if($filter_sort_val == 'hour') echo "selected='selected'"; ?>>Hour</option>
			<option value='cost' <?php if($filter_sort_val == 'cost') echo "selected='selected'"; ?>>Cost</option>
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
$tot_hour = 0;
$tot_cost = 0;
// echo "<pre>"; print_r($resdata); exit;
if(!empty($resdata)) {
	foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'])) {
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'])){
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] += $rec->resource_duration_cost;
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['directcost'] += $rec->resource_duration_direct_cost;
		} else {
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] = $rec->resource_duration_cost;
			$tbl_data[$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['directcost'] = $rec->resource_duration_direct_cost;
		}
	
		//for sub total
		if(isset($sub_tot[$rec->practice_name]['sub_tot_hour'])){
			$sub_tot[$rec->practice_name]['sub_tot_hour'] +=  $rec->duration_hours;
		} else {
			$sub_tot[$rec->practice_name]['sub_tot_hour'] =  $rec->duration_hours;
		}
		if(isset($sub_tot[$rec->practice_name]['sub_tot_cost'])){
			$sub_tot[$rec->practice_name]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		} else {
			$sub_tot[$rec->practice_name]['sub_tot_cost'] =  $rec->resource_duration_cost;
		}
		if(isset($sub_tot[$rec->practice_name]['sub_tot_directcost'])){
			$sub_tot[$rec->practice_name]['sub_tot_directcost'] +=  $rec->resource_duration_direct_cost;
		} else {
			$sub_tot[$rec->practice_name]['sub_tot_directcost'] =  $rec->resource_duration_direct_cost;
		}
		if(isset($skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour']))
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour'] += $rec->duration_hours;
		else 
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour'] = $rec->duration_hours;
		
		if(isset($skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost']))
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost'] += $rec->resource_duration_cost;
		else 
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost'] = $rec->resource_duration_cost;
	
		if(isset($skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_directcost']))
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_directcost'] += $rec->resource_duration_direct_cost;
		else 
		$skil_sub_tot[$rec->practice_name][$rec->skill_name]['skil_sub_tot_directcost'] = $rec->resource_duration_direct_cost;
		//for sub total
		
		//for practicewise - sorting-hour
		if(isset($sub_tot_hr[$rec->practice_name]))
		$sub_tot_hr[$rec->practice_name] +=  $rec->duration_hours;
		else
		$sub_tot_hr[$rec->practice_name] =  $rec->duration_hours;
		//for practicewise sorting-cost
		if(isset($sub_tot_cst[$rec->practice_name]))
		$sub_tot_cst[$rec->practice_name] +=  $rec->resource_duration_cost;
		else
		$sub_tot_cst[$rec->practice_name] =  $rec->resource_duration_cost;
		//for practicewise sorting-directcost
		if(isset($sub_tot_directcst[$rec->practice_name]))
		$sub_tot_directcst[$rec->practice_name] +=  $rec->resource_duration_direct_cost;
		else
		$sub_tot_directcst[$rec->practice_name] =  $rec->resource_duration_direct_cost;
		//for skillwise - sorting-hour
		if(isset($skil_sort_hr[$rec->practice_name][$rec->skill_name]))
		$skil_sort_hr[$rec->practice_name][$rec->skill_name] += $rec->duration_hours;
		else 
		$skil_sort_hr[$rec->practice_name][$rec->skill_name] = $rec->duration_hours;
		//for skillwise - sorting-cost
		if(isset($skil_sort_cst[$rec->practice_name][$rec->skill_name]))
		$skil_sort_cst[$rec->practice_name][$rec->skill_name] += $rec->resource_duration_cost;
		else 
		$skil_sort_cst[$rec->practice_name][$rec->skill_name] = $rec->resource_duration_cost;
		//for skillwise - sorting-cost
		if(isset($skil_sort_directcst[$rec->practice_name][$rec->skill_name]))
		$skil_sort_directcst[$rec->practice_name][$rec->skill_name] += $rec->resource_duration_direct_cost;
		else 
		$skil_sort_directcst[$rec->practice_name][$rec->skill_name] = $rec->resource_duration_direct_cost;
		//for userwise - sorting-hour
		if(isset($user_hr[$rec->practice_name][$rec->skill_name][$rec->empname]))
		$user_hr[$rec->practice_name][$rec->skill_name][$rec->empname] += $rec->duration_hours;
		else 
		$user_hr[$rec->practice_name][$rec->skill_name][$rec->empname] = $rec->duration_hours;
		//for userwise - sorting-cost
		if(isset($user_cst[$rec->practice_name][$rec->skill_name][$rec->empname]))
		$user_cst[$rec->practice_name][$rec->skill_name][$rec->empname] += $rec->resource_duration_cost;
		else 
		$user_cst[$rec->practice_name][$rec->skill_name][$rec->empname] = $rec->resource_duration_cost;
		//for userwise - sorting-cost
		if(isset($user_directcst[$rec->practice_name][$rec->skill_name][$rec->empname]))
		$user_directcst[$rec->practice_name][$rec->skill_name][$rec->empname] += $rec->resource_duration_direct_cost;
		else 
		$user_directcst[$rec->practice_name][$rec->skill_name][$rec->empname] = $rec->resource_duration_direct_cost;

		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		$tot_directcost = $tot_directcost + $rec->resource_duration_direct_cost;
		
		//cost
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
		
		//usercount
		if (!in_array($rec->empname, $pr_usercnt[$rec->practice_name]))
		$pr_usercnt[$rec->practice_name][] = $rec->empname;
	
		if (!in_array($rec->empname, $sk_usercnt[$rec->practice_name][$rec->skill_name]))
		$sk_usercnt[$rec->practice_name][$rec->skill_name][] = $rec->empname;
	}
}
// asort($sub_tot_hr);
// echo "<pre>"; print_r($sub_tot_hr); echo "</pre>";
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm"><?php echo $heading; ?> :: Group By - Practice</h2>
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
// echo "<pre>"; print_r($tbl_data); echo "</pre>"; exit;
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;
if(!empty($tbl_data)) {
	echo "<table id='project_dash' class='proj-dash-table data-table proj-table-odd-even'>
			<tr>
			<th class='prac-dt' width='16%'>PRACTICE NAME</th>
			<th class='prac-dt' width='12%'>SKILL NAME</th>
			<th class='prac-dt' width='15%'>USER NAME</th>
			<th class='prac-dt' width='15%'>PROJECT NAME</th>
			<th class='prac-dt' width='5%'>HOUR</th>
			<th class='prac-dt' width='5%'>COST</th>
			<th class='prac-dt' width='5%'>DIRECT COST</th>
			<th class='prac-dt' width='5%'>% of HOUR</th>
			<th class='prac-dt' width='5%'>% of COST</th>
			<th class='prac-dt' width='5%'>% of DIRECT COST</th>
			</tr>";
	// foreach()
	// echo "<pre>"; print_R($sort_ar); exit;
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($sub_tot_hr);
				$sort_ar = $sub_tot_hr;
			} else if($filter_sort_val=='cost') {
				asort($sub_tot_cst);
				$sort_ar = $sub_tot_cst;
			} else if($filter_sort_val=='directcost') {
				asort($sub_tot_directcst);
				$sort_ar = $sub_tot_directcst;
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($sub_tot_hr);
				$sort_ar = $sub_tot_hr;
			} else if($filter_sort_val=='cost') {
				arsort($sub_tot_cst);
				$sort_ar = $sub_tot_cst;
			} else if($filter_sort_val=='directcost') {
				arsort($sub_tot_directcst);
				$sort_ar = $sub_tot_directcst;
			}
		}
		foreach($sort_ar as $pkey=>$sortval) {
			$i = 0;
			$sub_tot_pr_cost = 0;
			$sub_tot_pr_hr    = ($sub_tot[$pkey]['sub_tot_hour']/$tot_hour)*100;
			$sub_tot_pr_cost  = ($sub_tot[$pkey]['sub_tot_cost']/$tot_cost)*100;
			$sub_tot_pr_directcost = ($sub_tot[$pkey]['sub_tot_directcost']/$tot_directcost)*100;
			$calc_tot_hour   += $sub_tot[$pkey]['sub_tot_hour'];
			$calc_tot_cost   += $sub_tot[$pkey]['sub_tot_cost'];
			$calc_tot_directcost += $sub_tot[$pkey]['sub_tot_directcost'];
			$sub_tot_pr_hr    = $sub_tot_pr_hr;
			$sub_tot_pr_cost  = $sub_tot_pr_cost;
			$sub_tot_pr_directcost = $sub_tot_pr_directcost;
			$perc_tot_hr	 += $sub_tot_pr_hr;
			$perc_tot_cost   += $sub_tot_pr_cost;
			$perc_tot_directcost += $sub_tot_pr_directcost;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='16%' align='left' class='collapse lft-ali'><span class='toggle'> ".strtoupper($pkey)."</b></span></th>
				<th width='12%'></th>
				<th width='15%'></th>
				<th width='15%' align='right' class='rt-ali'>SUB TOTAL(PRACTICE WISE):</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$pkey]['sub_tot_hour'], 1)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$pkey]['sub_tot_cost'], 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$pkey]['sub_tot_directcost'], 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pr_hr, 1)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pr_cost, 2)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot_pr_directcost, 2)."</th>
			</tr>";
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					asort($skil_sort_hr[$pkey]);
					$skill_sort_arr = $skil_sort_hr[$pkey];
				} else if($filter_sort_val=='cost') {
					asort($skil_sort_cst[$pkey]);
					$skill_sort_arr = $skil_sort_cst[$pkey];
				} else if($filter_sort_val=='directcost') {
					asort($skil_sort_directcst[$pkey]);
					$skill_sort_arr = $skil_sort_directcst[$pkey];
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					arsort($skil_sort_hr[$pkey]);
					$skill_sort_arr = $skil_sort_hr[$pkey];
				} else if($filter_sort_val=='cost') {
					arsort($skil_sort_cst[$pkey]);
					$skill_sort_arr = $skil_sort_cst[$pkey];
				} else if($filter_sort_val=='directcost') {
					arsort($skil_sort_directcst[$pkey]);
					$skill_sort_arr = $skil_sort_directcst[$pkey];
				}
			}
			$sk_arr = array();
			foreach($skill_sort_arr as $skkey=>$skval) {
				// $sk_arr = $prac_ar[$pkey][$skkey];
				$sk_arr = $tbl_data[$pkey][$skkey];
				$i = 1;

				$sub_tot_sk_hr   = ($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_hour']/$tot_hour)*100;
				$sub_tot_sk_cost = ($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_cost']/$tot_cost)*100;
				$sub_tot_sk_directcost = ($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_directcost']/$tot_directcost)*100;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td align='left' width='12%'><b><span class='toggle'> ".$skkey."</b></span></td>
						<td width='15%'></td>
						<td align='right'><b>SUB TOTAL(SKILL WISE):</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_hour'], 1)."</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_cost'], 2)."</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$pkey][$skkey]['skil_sub_tot_directcost'], 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_hr, 1)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_cost, 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_directcost, 2)."</b></td>
					</tr>";
				$i++;
				
				if($filter_sort_by=='asc') {
					if($filter_sort_val=='hour') {
						asort($user_hr[$pkey][$skkey]);
						$user_sort_arr = $user_hr[$pkey][$skkey];
					} else if($filter_sort_val=='cost') {
						asort($user_cst[$pkey][$skkey]);
						$user_sort_arr = $user_cst[$pkey][$skkey];
					} else if($filter_sort_val=='directcost') {
						asort($user_directcst[$pkey][$skkey]);
						$user_sort_arr = $user_directcst[$pkey][$skkey];
					}
				} else if($filter_sort_by=='desc') {
					if($filter_sort_val=='hour') {
						arsort($user_hr[$pkey][$skkey]);
						$user_sort_arr = $user_hr[$pkey][$skkey];
					} else if($filter_sort_val=='cost') {
						arsort($user_cst[$pkey][$skkey]);
						$user_sort_arr = $user_cst[$pkey][$skkey];
					} else if($filter_sort_val=='directcost') {
						arsort($user_directcst[$pkey][$skkey]);
						$user_sort_arr = $user_directcst[$pkey][$skkey];
					}
				}
				$proj_arr = array();
				foreach($user_sort_arr as $ukey=>$uval){
					$proj_arr = $sk_arr[$ukey];
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td width='12%'></td>
						<td align='left' width='15%'>".$ukey."</td>
						<td width='15%'></td>
						<td width='5%'></td>
						<td width='5%'></td>
						<td width='5%'></td>
						<td width='5%'></td>
					</tr>";
					$i++;
					if($filter_sort_by=='asc') {
						if($filter_sort_val=='hour') {
							$prj_arr = array_sort($proj_arr, 'hour', 'SORT_ASC');
						} else if($filter_sort_val=='cost') {
							$prj_arr = array_sort($proj_arr, 'cost', 'SORT_ASC');
						}
					} else if($filter_sort_by=='desc') {
						if($filter_sort_val=='hour') {
							$prj_arr = array_sort($proj_arr, 'hour', 'SORT_DESC');
						} else if($filter_sort_val=='cost') {
							$prj_arr = array_sort($proj_arr, 'cost', 'SORT_DESC');
						}
					}
					foreach($prj_arr as $p_name=>$pval) {
						$rate_pr_hr = isset($cost_arr[$ukey])?$cost_arr[$ukey]:0;
						$per_hr     = ($pval['hour']/160) * 100;
						$per_cost   = (($pval['hour']*$rate_pr_hr)/(160*$pval['hour'])) * 100;
						echo "<tr data-depth='".$i."' class='collapse '>
							<td width='16%'></td>
							<td width='12%'></td>
							<td width='15%'></td>
							<td width='15%'>".$project_master[$p_name]."</td>
							<td width='5%' align='right' width='5%'>".round($pval['hour'], 1)."</td>
							<td width='5%' align='right' width='5%'>".round($pval['cost'], 2)."</td>
							<td width='5%' align='right' width='5%'>".round($per_hr, 1)."</td>
							<td width='5%' align='right' width='5%'>".round($per_cost, 2)."</td>
						</tr>";
						$per_hr     = '';
						$rate_pr_hr = 0;
						$i++;
						$prj_arr = array();
					}
				}		
			}
		}
	
	echo "<tr data-depth='0' class='project-dash-total' style='text-align:right'>
			<td width='80%' colspan='4' align='right'><b>TOTAL:</b></td>
			<td width='5%' align='right'><b>".round($calc_tot_hour, 1)."</b></td>
			<td width='5%' align='right'><b>".round($calc_tot_cost, 0)."</b></td>
			<td width='5%' align='right'><b>".round($perc_tot_hr, 0)."</b></td>
			<td width='5%' align='right'><b>".round($perc_tot_cost, 0)."</b></td>
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
		   , filename: 'practicewisedata'
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
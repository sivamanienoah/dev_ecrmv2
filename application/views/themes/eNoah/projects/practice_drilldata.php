<style>
.prac-dt{ text-align:center !important; }
.toggle { display: inline-block; }
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
if(!empty($resdata)) {
	foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost']))
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] = $rec->resource_duration_cost;
	
		//for sub total
		if(isset($sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_hour'])){
			$sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_hour'] +=  $rec->duration_hours;
		} else {
			$sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_hour'] =  $rec->duration_hours;
		}
		if(isset($sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_cost'])){
			$sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		} else {
			$sub_tot[$rec->dept_name][$rec->practice_name]['sub_tot_cost'] =  $rec->resource_duration_cost;
		}
		if(isset($skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour']))
		$skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour'] += $rec->duration_hours;
		else 
		$skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_hour'] = $rec->duration_hours;
		
		if(isset($skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost']))
		$skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost'] += $rec->resource_duration_cost;
		else 
		$skil_sub_tot[$rec->dept_name][$rec->practice_name][$rec->skill_name]['skil_sub_tot_cost'] = $rec->resource_duration_cost;
		//for sub total
		
		//for practicewise - sorting-hour
		if(isset($sub_tot_hr[$rec->dept_name][$rec->practice_name]))
		$sub_tot_hr[$rec->dept_name][$rec->practice_name] +=  $rec->duration_hours;
		else
		$sub_tot_hr[$rec->dept_name][$rec->practice_name] =  $rec->duration_hours;
		//for practicewise sorting-cost
		if(isset($sub_tot_cst[$rec->dept_name][$rec->practice_name]))
		$sub_tot_cst[$rec->dept_name][$rec->practice_name] +=  $rec->resource_duration_cost;
		else
		$sub_tot_cst[$rec->dept_name][$rec->practice_name] =  $rec->resource_duration_cost;
		//for skillwise - sorting-hour
		if(isset($skil_sort_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name]))
		$skil_sort_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name] += $rec->duration_hours;
		else 
		$skil_sort_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name] = $rec->duration_hours;
		//for skillwise - sorting-cost
		if(isset($skil_sort_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name]))
		$skil_sort_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name] += $rec->resource_duration_cost;
		else 
		$skil_sort_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name] = $rec->resource_duration_cost;
		//for userwise - sorting-hour
		if(isset($user_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname]))
		$user_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname] += $rec->duration_hours;
		else 
		$user_hr[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname] = $rec->duration_hours;
		//for userwise - sorting-hour
		if(isset($user_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname]))
		$user_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname] += $rec->resource_duration_cost;
		else 
		$user_cst[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname] = $rec->resource_duration_cost;

		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		
		//cost
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
		
		//usercount
		if (!in_array($rec->empname, $pr_usercnt[$rec->dept_name][$rec->practice_name]))
		$pr_usercnt[$rec->dept_name][$rec->practice_name][] = $rec->empname;
	
		if (!in_array($rec->empname, $sk_usercnt[$rec->dept_name][$rec->practice_name][$rec->skill_name]))
		$sk_usercnt[$rec->dept_name][$rec->practice_name][$rec->skill_name][] = $rec->empname;
	}
}
?>
<h2><?php echo $heading; ?> :: Group By - Practice</h2>
<?php
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;
if(!empty($tbl_data)) {
	echo "<table id='project_dash' class='data-table'>
			<tr>
			<th class='prac-dt' width='16%'><b>PRACTICE NAME</b></th>
			<th class='prac-dt' width='12%'><b>SKILL NAME</b></th>
			<th class='prac-dt' width='15%'><b>USER NAME</b></th>
			<th class='prac-dt' width='15%'><b>PROJECT NAME</b></th>
			<th class='prac-dt' width='5%'><b>HOUR</b></th>
			<th class='prac-dt' width='5%'><b>COST</b></th>
			<th class='prac-dt' width='5%'><b>% of HOUR</b></th>
			<th class='prac-dt' width='5%'><b>% of COST</b></th>
			</tr>";
	foreach($tbl_data as $dept=>$prac_ar) {
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($sub_tot_hr[$dept]);
				$sort_ar = $sub_tot_hr[$dept];
			} else if($filter_sort_val=='cost') {
				asort($sub_tot_cst[$dept]);
				$sort_ar = $sub_tot_cst[$dept];
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($sub_tot_hr[$dept]);
				$sort_ar = $sub_tot_hr[$dept];
			} else if($filter_sort_val=='cost') {
				arsort($sub_tot_cst[$dept]);
				$sort_ar = $sub_tot_cst[$dept];
			}
		}
		foreach($sort_ar as $pkey=>$sortval) {
			$i = 0;
			// $pr_cnt = 0;
			// $pr_tot_cost = 0;
			$sub_tot_pr_cost = 0;
			// $pr_cnt = count($pr_usercnt[$dept][$pkey]);
			// $sub_tot_pr_hr   = ($sub_tot[$dept][$pkey]['sub_tot_hour']/(160*$pr_cnt)) * 100;
			// foreach($pr_usercnt[$dept][$pkey] as $mem) {
				// $pr_tot_cost += $cost_arr[$mem]*160;
			// }
			// $sub_tot_pr_cost = ($sub_tot[$dept][$pkey]['sub_tot_cost']/$pr_tot_cost)*100;
			$sub_tot_pr_hr    = ($sub_tot[$dept][$pkey]['sub_tot_hour']/$tot_hour)*100;
			$sub_tot_pr_cost  = ($sub_tot[$dept][$pkey]['sub_tot_cost']/$tot_cost)*100;
			$calc_tot_hour   += $sub_tot[$dept][$pkey]['sub_tot_hour'];
			$calc_tot_cost   += $sub_tot[$dept][$pkey]['sub_tot_cost'];
			$perc_tot_hr	 += $sub_tot_pr_hr;
			$perc_tot_cost   += $sub_tot_pr_cost;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='43%' class='collapse' colspan='3'><span class='toggle'></span> <b>".strtoupper($pkey)."</b></th>
				<th width='15%' class='rt-ali'>SUB TOTAL(PRACTICE WISE):</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_hour'], 0)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_cost'], 0)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot_pr_hr, 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot_pr_cost, 2)."</th>
			</tr>";
			
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					asort($skil_sort_hr[$dept][$pkey]);
					$skill_sort_arr = $skil_sort_hr[$dept][$pkey];
				} else if($filter_sort_val=='cost') {
					asort($skil_sort_cst[$dept][$pkey]);
					$skill_sort_arr = $skil_sort_cst[$dept][$pkey];
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					arsort($skil_sort_hr[$dept][$pkey]);
					$skill_sort_arr = $skil_sort_hr[$dept][$pkey];
				} else if($filter_sort_val=='cost') {
					arsort($skil_sort_cst[$dept][$pkey]);
					$skill_sort_arr = $skil_sort_cst[$dept][$pkey];
				}
			}
			
			$sk_arr = array();
			foreach($skill_sort_arr as $skkey=>$skval) {
				$sk_arr = $prac_ar[$pkey][$skkey];
				$i = 1;
				/* $sk_cnt = 0;
				$sk_tot_cost = 0;
				$sub_tot_sk_cost = 0;
				$sk_cnt = count($sk_usercnt[$dept][$pkey][$skkey]);
				$sub_tot_sk_hr   = ($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_hour']/(160*$sk_cnt)) * 100;
				foreach($sk_usercnt[$dept][$pkey][$skkey] as $usr) {
					$sk_tot_cost += $cost_arr[$usr]*160;
				}
				$sub_tot_sk_cost = ($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_cost']/$sk_tot_cost)*100; */
				$sub_tot_sk_hr   = ($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_hour']/$tot_hour)*100;
				$sub_tot_sk_cost = ($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_cost']/$tot_cost)*100;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td colspan='2'><b><span class='toggle'></span> ".$skkey."</b></td>
						<td class='rt-ali'><b>SUB TOTAL(SKILL WISE):</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_hour'], 0)."</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$dept][$pkey][$skkey]['skil_sub_tot_cost'], 0)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_hr, 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_cost, 2)."</b></td>
					</tr>";
				$i++;
				
				if($filter_sort_by=='asc') {
					if($filter_sort_val=='hour') {
						asort($user_hr[$dept][$pkey][$skkey]);
						$user_sort_arr = $user_hr[$dept][$pkey][$skkey];
					} else if($filter_sort_val=='cost') {
						asort($user_cst[$dept][$pkey][$skkey]);
						$user_sort_arr = $user_cst[$dept][$pkey][$skkey];
					}
				} else if($filter_sort_by=='desc') {
					if($filter_sort_val=='hour') {
						arsort($user_hr[$dept][$pkey][$skkey]);
						$user_sort_arr = $user_hr[$dept][$pkey][$skkey];
					} else if($filter_sort_val=='cost') {
						arsort($user_cst[$dept][$pkey][$skkey]);
						$user_sort_arr = $user_cst[$dept][$pkey][$skkey];
					}
				}
				
				$proj_arr = array();
				foreach($user_sort_arr as $ukey=>$uval){
					$proj_arr = $sk_arr[$ukey];
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td width='12%'></td>
						<td colspan='6'>".$ukey."</td>
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
						echo "<tr data-depth='".$i."' class='collapse'>
							<td width='16%'></td>
							<td width='12%'></td>
							<td width='15%'></td>
							<td width='15%'>".$project_master[$p_name]."</td>
							<td width='5%' align='right' width='5%'>".round($pval['hour'], 2)."</td>
							<td width='5%' align='right' width='5%'>".round($pval['cost'], 2)."</td>
							<td width='5%' align='right' width='5%'>".round($per_hr, 2)."</td>
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
	}
	/* $perc_tot_hr = ($tot_hour/(160*count($cost_arr)))*100;
	$overall_cost = 0;
	foreach($cost_arr as $cs){
		$overall_cost += $cs * 160;
	}
	$perc_tot_cost = ($tot_cost/$overall_cost)*100; */
	echo "<tr data-depth='0'>
			<td width='80%' colspan='4' class='rt-ali'><b>TOTAL:</b></td>
			<th width='5%' class='rt-ali'><b>".round($calc_tot_hour, 0)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($calc_tot_cost, 0)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_hr, 0)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_cost, 0)."</b></th>
			</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/project_drilldown_data.js"></script>

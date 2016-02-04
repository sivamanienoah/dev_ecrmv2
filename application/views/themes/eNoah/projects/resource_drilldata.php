<style>
table.prac-dt th{ text-align:center; }
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
			<option value='asc' <?php if($filter_sort_by == 'asc') echo "selected='selected'"; ?>>ASC</option>
			<option value='desc' <?php if($filter_sort_by == 'desc') echo "selected='selected'"; ?>>DESC</option>
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
			<input style="height:auto;" type="button" class="positive input-font" name="reset_practice" id="reset_practice" value="Reset" />
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
if(!empty($resdata)) {
	foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost']))
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->empname][$rec->project_code]['cost'] = $rec->resource_duration_cost;
	
		if(isset($sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour']))
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour'] +=  $rec->duration_hours;
		else
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_hour'] =  $rec->duration_hours;
		
		if(isset($sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost']))
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		else
		$sub_tot[$rec->dept_name][$rec->empname]['sub_tot_cost'] =  $rec->resource_duration_cost;
		//total
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		//user
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
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
	}
}
// echo "<pre>"; print_r($emp_hr); echo "</pre>";
?>
<h2><?php echo $heading; ?> :: Group By - Resource</h2>
<?php
$perc_tot_hr = $perc_tot_cost = $calc_tot_hour = $calc_tot_cost = 0;
if(!empty($tbl_data)) {
	echo "<table class='data-table prac-dt'>
			<tr>
			<th width='15%'><b>USER NAME</b></th>
			<th width='15%'><b>PROJECT NAME</b></th>
			<th width='5%'><b>HOUR</b></th>
			<th width='5%'><b>COST</b></th>
			<th width='5%'><b>% of HOUR</b></th>
			<th width='5%'><b>% of COST</b></th>
		</table>";
	echo "<table id='project_dash' class='data-table'>";
	foreach($tbl_data as $dept=>$us_ar) {
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($emp_hr[$dept]);
				$us_sort_ar = $emp_hr[$dept];
			} else if($filter_sort_val=='cost') {
				asort($emp_cst[$dept]);
				$us_sort_ar = $emp_cst[$dept];
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($emp_hr[$dept]);
				$us_sort_ar = $emp_hr[$dept];
			} else if($filter_sort_val=='cost') {
				arsort($emp_cst[$dept]);
				$us_sort_ar = $emp_cst[$dept];
			}
		}
		// foreach($us_ar as $p_name=>$proj_ar) {
		$user_arr = array();
		foreach($us_sort_ar as $p_name=>$proj_ar) {
			$user_arr = $us_ar[$p_name];
			$i = 0;
			$rs_sub_tot_hr   = 0;
			$rs_sub_tot_cost = 0;
			$rs_sub_tot_hr   = ($sub_tot[$dept][$p_name]['sub_tot_hour']/$tot_hour)*100;
			$rs_sub_tot_cost = ($sub_tot[$dept][$p_name]['sub_tot_cost']/$tot_cost)*100;
			$perc_tot_hr   += $rs_sub_tot_hr;
			$perc_tot_cost += $rs_sub_tot_cost;
			$calc_tot_hour += $sub_tot[$dept][$p_name]['sub_tot_hour'];
			$calc_tot_cost += $sub_tot[$dept][$p_name]['sub_tot_cost'];
			echo "<tr data-depth='".$i."' class='collapse'>
					<th class='collapse'><span class='toggle'></span> ".strtoupper($p_name)."</th>
					<th width='15%' class='rt-ali'>SUB TOTAL:</th>
					<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_hour'], 0)."</th>
					<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_cost'], 0)."</th>
					<th width='5%' class='rt-ali'>".round($rs_sub_tot_hr, 2)."</th>
					<th width='5%' class='rt-ali'>".round($rs_sub_tot_cost, 2)."</th>
				</tr>";
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					$usr_arr = array_sort($user_arr, 'hour', 'SORT_ASC');
				} else if($filter_sort_val=='cost') {
					$usr_arr = array_sort($user_arr, 'cost', 'SORT_ASC');
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					$usr_arr = array_sort($user_arr, 'hour', 'SORT_DESC');
				} else if($filter_sort_val=='cost') {
					$usr_arr = array_sort($user_arr, 'cost', 'SORT_DESC');
				}
			}
			// foreach($proj_ar as $pkey=>$pval) {
			foreach($usr_arr as $pkey=>$pval) {
				$i=1;
				$rate_pr_hr = isset($cost_arr[$p_name])?$cost_arr[$p_name]:0;
				$name       = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				$per_hr = $per_cost = 0;
				$per_hr   	= ($pval['hour']/$tot_hour) * 100;
				$per_cost 	= ($pval['cost']/$tot_cost) * 100;
				
				echo "<tr data-depth='".$i."' class='collapse'>
					<td width='15%'></td>
					<td width='15%'>".$name."</td>
					<td width='5%' align='right'>".round($pval['hour'], 0)."</td>
					<td width='5%' align='right'>".round($pval['cost'], 0)."</td>
					<td width='5%' align='right'>".round($per_hr, 2)."</td>
					<td width='5%' align='right'>".round($per_cost, 2)."</td>
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
		<td width='80%' colspan='2' class='rt-ali'><b>TOTAL:</b></td>
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
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

$tbl_data   = array();
$sub_tot    = array();
$cost_arr   = array();
$pj_sub_tot = array();
$pj_usercnt = array();
$sk_usercnt = array();
$skil_hr  = array();
$skil_cst = array();
$prjt_hr  = array();
$prjt_cst = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
$tot_hour = 0;
$tot_cost = 0;

if(!empty($resdata)) {
	foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost']))
		$tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost'] = $rec->resource_duration_cost;
	
		//sub total by skillwise
		if(isset($sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_hour']))
		$sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_hour'] +=  $rec->duration_hours;
		else
		$sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_hour'] =  $rec->duration_hours;
		
		if(isset($sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_cost']))
		$sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		else
		$sub_tot[$rec->dept_name][$rec->skill_name]['sub_tot_cost'] =  $rec->resource_duration_cost;
		
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
	
		//sub total by projectwise
		if(isset($pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_hour']))
		$pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_hour'] += $rec->duration_hours;
		else 
		$pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_hour'] = $rec->duration_hours;
	
		if(isset($pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_cost']))
		$pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_cost'] += $rec->resource_duration_cost;
		else 
		$pj_sub_tot[$rec->dept_name][$rec->skill_name][$rec->project_code]['pj_sub_tot_cost'] = $rec->resource_duration_cost;
		
		//usercount
		if (!in_array($rec->empname, $sk_usercnt[$rec->dept_name][$rec->skill_name]))
		$sk_usercnt[$rec->dept_name][$rec->skill_name][] = $rec->empname;
		if (!in_array($rec->empname, $pj_usercnt[$rec->dept_name][$rec->skill_name][$rec->project_code]))
		$pj_usercnt[$rec->dept_name][$rec->skill_name][$rec->project_code][] = $rec->empname;
	
		//for skillwise - sorting-hour
		if(isset($skil_hr[$rec->dept_name][$rec->skill_name]))
		$skil_hr[$rec->dept_name][$rec->skill_name] += $rec->duration_hours;
		else 
		$skil_hr[$rec->dept_name][$rec->skill_name] = $rec->duration_hours;
		//for skillwise - sorting-cost
		if(isset($skil_cst[$rec->dept_name][$rec->skill_name]))
		$skil_cst[$rec->dept_name][$rec->skill_name] += $rec->resource_duration_cost;
		else 
		$skil_cst[$rec->dept_name][$rec->skill_name] = $rec->resource_duration_cost;
		//for project_code - sorting-hour
		if(isset($prjt_hr[$rec->dept_name][$rec->skill_name][$rec->project_code]))
		$prjt_hr[$rec->dept_name][$rec->skill_name][$rec->project_code] += $rec->duration_hours;
		else 
		$prjt_hr[$rec->dept_name][$rec->skill_name][$rec->project_code] = $rec->duration_hours;
		//for project_code - sorting-hour
		if(isset($prjt_cst[$rec->dept_name][$rec->skill_name][$rec->project_code]))
		$prjt_cst[$rec->dept_name][$rec->skill_name][$rec->project_code] += $rec->resource_duration_cost;
		else 
		$prjt_cst[$rec->dept_name][$rec->skill_name][$rec->project_code] = $rec->resource_duration_cost;
	}
}
// echo "<pre>"; print_r($prjt_cst); echo "</pre>";
// echo "<pre>"; print_r($cost_arr); echo "</pre>";
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm"><?php echo $heading; ?> :: Group By - Skill</h2>
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
	echo "<table id='project_dash' class='data-table'>
		<tr>
			<th class='prac-dt' width='15%'><b>SKILL NAME</b></th>
			<th class='prac-dt' width='15%'><b>PROJECT NAME</b></th>
			<th class='prac-dt' width='15%'><b>EMPLOYEE NAME</b></th>
			<th class='prac-dt' width='5%'><b>HOUR</b></th>
			<th class='prac-dt' width='5%'><b>COST</b></th>
			<th class='prac-dt' width='5%'><b>% of HOUR</b></th>
			<th class='prac-dt' width='5%'><b>% of COST</b></th>
		</tr>";
	foreach($tbl_data as $dept=>$skil_ar) {
		if($filter_sort_by=='asc') {
			if($filter_sort_val=='hour') {
				asort($skil_hr[$dept]);
				$sort_ar = $skil_hr[$dept];
			} else if($filter_sort_val=='cost') {
				asort($skil_cst[$dept]);
				$sort_ar = $skil_cst[$dept];
			}
		} else if($filter_sort_by=='desc') {
			if($filter_sort_val=='hour') {
				arsort($skil_hr[$dept]);
				$sort_ar = $skil_hr[$dept];
			} else if($filter_sort_val=='cost') {
				arsort($skil_cst[$dept]);
				$sort_ar = $skil_cst[$dept];
			}
		}
		foreach($sort_ar as $skil_key=>$proj_ar) {
			$i = 0;
			$sk_cnt = 0;
			$sk_tot_cost = 0;
			$sub_tot_sk_cost = 0;
			/* $sk_cnt = count($sk_usercnt[$dept][$skil_key]);
			$sub_tot_sk_hr = ($sub_tot[$dept][$skil_key]['sub_tot_hour']/(160*$sk_cnt)) * 100;
			foreach($sk_usercnt[$dept][$skil_key] as $usr){
				$sk_tot_cost += $cost_arr[$usr]*160;
			}
			$sub_tot_sk_cost = ($sub_tot[$dept][$skil_key]['sub_tot_cost']/$sk_tot_cost)*100; */
			$sub_tot_sk_hr   = ($sub_tot[$dept][$skil_key]['sub_tot_hour']/$tot_hour)*100;
			$sub_tot_sk_cost = ($sub_tot[$dept][$skil_key]['sub_tot_cost']/$tot_cost)*100;
			$perc_tot_hr   += $sub_tot_sk_hr;
			$perc_tot_cost += $sub_tot_sk_cost;
			$calc_tot_hour += $sub_tot[$dept][$skil_key]['sub_tot_hour'];
			$calc_tot_cost += $sub_tot[$dept][$skil_key]['sub_tot_cost'];
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='15%' align='left' class='collapse lft-ali'><span class='toggle'> ".strtoupper($skil_key)."</span></th>
				<th width='15%'></th>
				<th width='15%' align='right' class='rt-ali'>SUB TOTAL(SKILL WISE):</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_hour'], 1)."</th>
				<th width='5%' align='right' class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_cost'], 2)."</th>
				<th width='5%' align='right' class='rt-ali'><b>".round($sub_tot_sk_hr, 1)."</b></th>
				<th width='5%' align='right' class='rt-ali'><b>".round($sub_tot_sk_cost, 2)."</b></th>
			</tr>";
			if($filter_sort_by=='asc') {
				if($filter_sort_val=='hour') {
					asort($prjt_hr[$dept][$skil_key]);
					$proj_sort_arr = $prjt_hr[$dept][$skil_key];
				} else if($filter_sort_val=='cost') {
					asort($prjt_cst[$dept][$skil_key]);
					$proj_sort_arr = $prjt_cst[$dept][$skil_key];
				}
			} else if($filter_sort_by=='desc') {
				if($filter_sort_val=='hour') {
					arsort($prjt_hr[$dept][$skil_key]);
					$proj_sort_arr = $prjt_hr[$dept][$skil_key];
				} else if($filter_sort_val=='cost') {
					arsort($prjt_cst[$dept][$skil_key]);
					$proj_sort_arr = $prjt_cst[$dept][$skil_key];
				}
			}
			$proj_arr = array();
			foreach($proj_sort_arr as $pkey=>$user_ar) {
				$proj_arr = $skil_ar[$skil_key][$pkey];
				$i = 1;
				// $pj_cnt = 0;
				$sub_tot_pj_hr   = 0;
				$sub_tot_pj_cost = 0;
				/* $pj_cnt = count($pj_usercnt[$dept][$skil_key][$pkey]);
				$sub_tot_pj_hr   = ($skil_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_hour']/(160*$pj_cnt)) * 100;
				foreach($pj_usercnt[$dept][$skil_key][$pkey] as $mem){
					$pj_tot_cost += $cost_arr[$mem]*160;
				}
				$sub_tot_pj_cost = ($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_cost']/$pj_tot_cost)*100; */
				$sub_tot_pj_hr 	 = ($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_hour']/$tot_hour)*100;
				$sub_tot_pj_cost = ($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_cost']/$tot_cost)*100;
				
				$name = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='15%'></td>
						<td align='left'><b><span class='toggle'> ".$name."</span></b></td>
						<td align='right' class='rt-ali'><b>SUB TOTAL(PROJECT WISE):</b></td>
						<td align='right' class='rt-ali'><b>".round($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_hour'], 0)."</b></td>
						<td align='right' class='rt-ali'><b>".round($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_cost'], 0)."</b></td>
						<td align='right' class='rt-ali'><b>".round($sub_tot_pj_hr, 2)."</b></td>
						<td align='right' class='rt-ali'><b>".round($sub_tot_pj_cost, 2)."</b></td>
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
				foreach($prj_arr as $ukey=>$uval){
					$rate_pr_hr = isset($cost_arr[$ukey])?$cost_arr[$ukey]:0;
					/* $per_hr 	= ($uval['hour']/160) * 100;
					$per_cost   = (($uval['hour']*$rate_pr_hr)/(160*$uval['hour'])) * 100; */
					$per_hr	  = ($uval['hour']/$tot_hour)*100;
					$per_cost = ($uval['cost']/$tot_cost)*100;
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='15%'></td>
						<td width='15%'></td>
						<td width='15%'>".$ukey."</td>
						<td width='5%' align='right'>".round($uval['hour'], 1)."</td>
						<td width='5%' align='right'>".round($uval['cost'], 2)."</td>
						<td width='5%' align='right'>".round($per_hr, 1)."</td>
						<td width='5%' align='right'>".round($per_cost, 2)."</td>
					</tr>";
					$per_hr 	= '';
					$rate_pr_hr = 0;
					$i++;
					$proj_arr = array();
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
			<td width='80%' colspan='3' align='right' class='rt-ali'><b>TOTAL:</b></td>
			<td width='5%' align='right' class='rt-ali'><b>".round($calc_tot_hour, 1)."</b></td>
			<td width='5%' align='right' class='rt-ali'><b>".round($calc_tot_cost, 0)."</b></td>
			<td width='5%' align='right' class='rt-ali'><b>".round($perc_tot_hr, 0)."</b></td>
			<td width='5%' align='right' class='rt-ali'><b>".round($perc_tot_cost, 0)."</b></td>
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
		   , filename: 'skillwisedata'
		});
	});
});
</script>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
<script type="text/javascript" src="assets/js/projects/project_drilldown_data.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.btechco.excelexport.js"></script>
<script type="text/javascript" src="assets/js/excelexport/jquery.base64.js"></script>
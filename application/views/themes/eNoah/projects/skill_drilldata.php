<style>
table.prac-dt th{ text-align:center; }
</style>
<div class="clear"></div>
<?php
$tbl_data = array();
$sub_tot  = array();
$cost_arr = array();
$pj_sub_tot   = array();
$pj_usercnt   = array();
$sk_usercnt   = array();
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
	}
}
// echo "<pre>"; print_r($pj_usercnt); echo "</pre>";
// echo "<pre>"; print_r($cost_arr); echo "</pre>";
?>
<h2><?php echo $heading; ?> :: Group By - Skill</h2>
<?php
if(!empty($tbl_data)) {
	echo "<table class='data-table prac-dt'>
			<tr>
			<th width='15%'><b>SKILL NAME</b></th>
			<th width='15%'><b>PROJECT NAME</b></th>
			<th width='15%'><b>EMPLOYEE NAME</b></th>
			<th width='5%'><b>HOUR</b></th>
			<th width='5%'><b>COST</b></th>
			<th width='5%'><b>% of HOUR</b></th>
			<th width='5%'><b>% of COST</b></th>
		</table>";
	echo "<table id='project_dash' class='data-table'>";
	foreach($tbl_data as $dept=>$skil_ar) {
		foreach($skil_ar as $skil_key=>$proj_ar) {
			$i = 0;
			$sk_cnt = 0;
			$sk_tot_cost = 0;
			$sub_tot_sk_cost = 0;
			$sk_cnt = count($sk_usercnt[$dept][$skil_key]);
			$sub_tot_sk_hr = ($sub_tot[$dept][$skil_key]['sub_tot_hour']/(160*$sk_cnt)) * 100;
			foreach($sk_usercnt[$dept][$skil_key] as $usr){
				$sk_tot_cost += $cost_arr[$usr]*160;
			}
			$sub_tot_sk_cost = ($sub_tot[$dept][$skil_key]['sub_tot_cost']/$sk_tot_cost)*100;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='30%' class='collapse' colspan='2'><span class='toggle'></span> ".strtoupper($skil_key)."</th>
				<th width='15%' class='rt-ali'>SUB TOTAL(SKILL WISE):</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_hour'], 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_cost'], 2)."</th>
				<th width='5%' class='rt-ali'><b>".round($sub_tot_sk_hr, 2)."</b></th>
				<th width='5%' class='rt-ali'><b>".round($sub_tot_sk_cost, 2)."</b></th>
			</tr>";
			foreach($proj_ar as $pkey=>$user_ar) {
				$i = 1;
				$pj_cnt = 0;
				$pj_tot_cost = 0;
				$sub_tot_pj_cost = 0;
				$pj_cnt = count($pj_usercnt[$dept][$skil_key][$pkey]);
				$sub_tot_pj_hr   = ($skil_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_hour']/(160*$pj_cnt)) * 100;
				foreach($pj_usercnt[$dept][$skil_key][$pkey] as $mem){
					$pj_tot_cost += $cost_arr[$mem]*160;
				}
				$sub_tot_pj_cost = ($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_cost']/$pj_tot_cost)*100;
				$name = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='15%'></td>
						<td><b><span class='toggle'></span> ".$name."</b></td>
						<td class='rt-ali'><b>SUB TOTAL(PROJECT WISE):</b></td>
						<td class='rt-ali'><b>".round($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_hour'], 2)."</b></td>
						<td class='rt-ali'><b>".round($pj_sub_tot[$dept][$skil_key][$pkey]['pj_sub_tot_cost'], 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_pj_hr, 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_pj_cost, 2)."</b></td>
					</tr>";
				$i++;
				foreach($user_ar as $ukey=>$uval){
					$rate_pr_hr = isset($cost_arr[$ukey])?$cost_arr[$ukey]:0;
					$per_hr 	= ($uval['hour']/160) * 100;
					$per_cost   = (($uval['hour']*$rate_pr_hr)/(160*$uval['hour'])) * 100;
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='15%'></td>
						<td width='15%'></td>
						<td width='15%'>".$ukey."</td>
						<td width='5%' align='right'>".round($uval['hour'], 2)."</td>
						<td width='5%' align='right'>".round($uval['cost'], 2)."</td>
						<td width='5%' align='right'>".round($per_hr, 2)."</td>
						<td width='5%' align='right'>".round($per_cost, 2)."</td>
					</tr>";
					$per_hr 	= '';
					$rate_pr_hr = 0;
					$i++;
				}
			}
		}		
	}
	$perc_tot_hr = ($tot_hour/(160*count($cost_arr)))*100;
	$overall_cost = 0;
	foreach($cost_arr as $cs){
		$overall_cost += $cs * 160;
	}
	$perc_tot_cost = ($tot_cost/$overall_cost)*100;
	echo "<tr data-depth='0'>
			<td width='80%' colspan='3' class='rt-ali'><b>TOTAL:</b></td>
			<th width='5%' class='rt-ali'><b>".round($tot_hour, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($tot_cost, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_hr, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_cost, 2)."</b></th>
			</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
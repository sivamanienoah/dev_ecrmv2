<style>
table.prac-dt th { text-align:center; }
</style>
<div class="clear"></div>
<?php
// echo "<pre>"; print_r($resdata); echo "</pre>"; exit;
$tbl_data = array();
$sub_tot  = array();
$cost_arr = array();
$pr_usercnt   = array();
$sk_usercnt   = array();
$skil_sub_tot = array();
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
// echo "<pre>"; print_r($pr_usercnt); echo "</pre>";
// echo "<pre>"; print_r($cost_arr); echo "</pre>";
?>
<h2><?php echo $heading; ?> :: Group By - Practice</h2>
<?php
if(!empty($tbl_data)) {
	echo "<table class='data-table prac-dt'>
			<tr>
			<th width='16%'><b>PRACTICE NAME</b></th>
			<th width='12%'><b>SKILL NAME</b></th>
			<th width='15%'><b>USER NAME</b></th>
			<th width='15%'><b>PROJECT NAME</b></th>
			<th width='5%'><b>HOUR</b></th>
			<th width='5%'><b>COST</b></th>
			<th width='5%'><b>% of HOUR</b></th>
			<th width='5%'><b>% of COST</b></th>
		</table>";
	echo "<table id='project_dash' class='data-table'>";
	foreach($tbl_data as $dept=>$prac_ar) {
		foreach($prac_ar as $pkey=>$skil_ar) {
			$i = 0;
			$pr_cnt = 0;
			$pr_tot_cost = 0;
			$sub_tot_pr_cost = 0;
			$pr_cnt = count($pr_usercnt[$dept][$pkey]);
			$sub_tot_pr_hr   = ($sub_tot[$dept][$pkey]['sub_tot_hour']/(160*$pr_cnt)) * 100;
			foreach($pr_usercnt[$dept][$pkey] as $mem){
				$pr_tot_cost += $cost_arr[$mem]*160;
			}
			$sub_tot_pr_cost = ($sub_tot[$dept][$pkey]['sub_tot_cost']/$pr_tot_cost)*100;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='43%' class='collapse' colspan='3'><span class='toggle'></span> <b>".strtoupper($pkey)."</b></th>
				<th width='15%' class='rt-ali'>SUB TOTAL(PRACTICE WISE):</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_hour'], 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_cost'], 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot_pr_hr, 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot_pr_cost, 2)."</th>
			</tr>";
			foreach($skil_ar as $skey=>$user_ar) {
				$i = 1;
				$sk_cnt = 0;
				$sk_tot_cost = 0;
				$sub_tot_sk_cost = 0;
				$sk_cnt = count($sk_usercnt[$dept][$pkey][$skey]);
				$sub_tot_sk_hr   = ($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_hour']/(160*$sk_cnt)) * 100;
				foreach($sk_usercnt[$dept][$pkey][$skey] as $usr){
					$sk_tot_cost += $cost_arr[$usr]*160;
				}
				$sub_tot_sk_cost = ($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_cost']/$sk_tot_cost)*100;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td colspan='2'><b><span class='toggle'></span> ".$skey."</b></td>
						<td class='rt-ali'><b>SUB TOTAL(SKILL WISE):</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_hour'], 2)."</b></td>
						<td class='rt-ali'><b>".round($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_cost'], 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_hr, 2)."</b></td>
						<td class='rt-ali'><b>".round($sub_tot_sk_cost, 2)."</b></td>
					</tr>";
				$i++;
				foreach($user_ar as $ukey=>$proj_ar) {
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td width='12%'></td>
						<td colspan='6'>".$ukey."</td>
					</tr>";
					$i++;
					foreach($proj_ar as $p_name=>$pval) {
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
					}
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
			<td width='80%' colspan='4' class='rt-ali'><b>TOTAL:</b></td>
			<th width='5%' class='rt-ali'><b>".round($tot_hour, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($tot_cost, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_hr, 2)."</b></th>
			<th width='5%' class='rt-ali'><b>".round($perc_tot_cost, 2)."</b></th>
			</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
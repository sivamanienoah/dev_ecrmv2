<style>
table.prac-dt th { text-align:center; }
</style>
<div class="clear"></div>
<?php
// echo "<pre>"; print_r($resdata); echo "</pre>"; exit;
$tbl_data = array();
$sub_tot  = array();
$cost_arr = array();
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
		
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
	}
}
// $json_data = json_encode($tbl_data); 
// echo $json_data;
// echo "<pre>"; print_r($skil_sub_tot); echo "</pre>";
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
			$i=0;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='43%' class='collapse' colspan='3'><span class='toggle'></span> <b>".strtoupper($pkey)."</b></th>
				<th width='15%' class='rt-ali'>SUB TOTAL(PRACTICE WISE):</th>
				<th class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_hour'], 2)."</th>
				<th class='rt-ali'>".round($sub_tot[$dept][$pkey]['sub_tot_cost'], 2)."</th>
				<th class='rt-ali'></th>
				<th class='rt-ali'></th>
			</tr>";
			foreach($skil_ar as $skey=>$user_ar) {
				$i=1;
				echo "<tr data-depth='".$i."' class='collapse'>
						<td width='16%'></td>
						<td colspan='2'><span class='toggle'></span> ".$skey."</td>
						<td class='rt-ali'>SUB TOTAL(SKILL WISE):</td>
						<td class='rt-ali'>".round($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_hour'], 2)."</td>
						<td class='rt-ali'>".round($skil_sub_tot[$dept][$pkey][$skey]['skil_sub_tot_cost'], 2)."</td>
						<td class='rt-ali'></td>
						<td class='rt-ali'></td>
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
	echo "<tr data-depth='0'>
			<td width='80%' colspan='4' class='rt-ali'><b>TOTAL:</b></td>
			<th width='5%' class='rt-ali'>".round($tot_hour, 2)."</th>
			<th width='5%' class='rt-ali'>".round($tot_cost, 2)."</th>
			<th width='5%'></th>
			<th width='5%'></th>
			</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
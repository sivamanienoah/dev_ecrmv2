<div class="clear"></div>
<?php
$tbl_data  = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
if(!empty($resdata)) {
	foreach($resdata as $rec) {
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost']))
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][$rec->empname]['cost'] = $rec->resource_duration_cost;
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
	
		if(!in_array($rec->empname, $prac[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code])) {
			$prac[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code][] = $rec->empname;
			if(isset($prac[$rec->dept_name]['count']))
			$prac[$rec->dept_name]['count'] = $prac[$rec->dept_name]['count'] + 1;
			else
			$prac[$rec->dept_name]['count'] = 1;
		
			if(isset($dept[$rec->dept_name][$rec->practice_name]['count']))
			$dept[$rec->dept_name][$rec->practice_name]['count'] = $dept[$rec->dept_name][$rec->practice_name]['count'] + 1;
			else
			$dept[$rec->dept_name][$rec->practice_name]['count'] = 1;
		
			if(isset($skil[$rec->dept_name][$rec->practice_name][$rec->skill_name]['count']))
			$skil[$rec->dept_name][$rec->practice_name][$rec->skill_name]['count'] = $skil[$rec->dept_name][$rec->practice_name][$rec->skill_name]['count'] + 1;
			else
			$skil[$rec->dept_name][$rec->practice_name][$rec->skill_name]['count'] = 1;
		
			if(isset($proj[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code]['count']))
			$proj[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code]['count'] = $proj[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code]['count'] + 1;
			else
			$proj[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->project_code]['count'] = 1;
		}
	}
}
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
?>
<h4><?php echo $heading; ?></h4>
<table class="data-table">
	<tr>
		<thead>
			<th>Practice</th><th>Departments</th><th>Skills</th><th>Project Code</th><th>User</th><th>Hour</th><th>Cost</th>
		</thead>
	</tr>
<?php
if(!empty($tbl_data)) {
	foreach($tbl_data as $prac_key=>$prac_ar){
		echo "<tr>"; 
		echo "<td rowspan=".$prac[$prac_key]['count'].">".$prac_key."</td>";
		foreach($prac_ar as $dept_key=>$dept_ar){
			echo "<td rowspan=".$dept[$prac_key][$dept_key]['count'].">".$dept_key."</td>";
			foreach($dept_ar as $skil_key=>$skil_ar){
				echo "<td rowspan=".$skil[$prac_key][$dept_key][$skil_key]['count'].">".$skil_key."</td>";
				foreach($skil_ar as $proj_key=>$proj_ar){
					echo "<td rowspan=".$proj[$prac_key][$dept_key][$skil_key][$proj_key]['count'].">".$project_master[$proj_key]."</td>";
					foreach($proj_ar as $user_name=>$user_val){
						echo "<td>".$user_name."</td>";
						echo "<td align=right>".$user_val['hour']."</td>";
						echo "<td align=right>".$user_val['cost']."</td>";
						echo "</tr>";
					}
				}
			}
		}
	}
}
?>
<tr>
	<td align="right" colspan="5"><b>Total</b></td>
	<td align="right"><?php echo isset($tot_hour) ? round($tot_hour, 2) : '0.00'; ?></td>
	<td align="right"><?php echo isset($tot_cost) ? round($tot_cost, 2) : '0.00'; ?></td>
</tr>
</table>
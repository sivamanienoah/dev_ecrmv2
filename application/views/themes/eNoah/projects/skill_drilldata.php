<style>
table.prac-dt th{
	text-align:center;
}
</style>
<div class="clear"></div>
<?php
$tbl_data  = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
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
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
	}
}
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
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
	foreach($tbl_data as $dept=>$skil_ar) {
		foreach($skil_ar as $skil_key=>$proj_ar) {
			echo "<table class='data-table'>";
			echo "<tr><th colspan='7'>".strtoupper($skil_key)."</th></tr>";
			foreach($proj_ar as $pkey=>$user_ar) {
				$name = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				echo "<tr><td></td><td colspan='6'>".$name."</td></tr>";
				foreach($user_ar as $ukey=>$uval){
					$per_hr = ($uval['hour']/160) * 100;
					$per_cost = ($uval['cost']/160) * 100;
					echo "<tr>
						<td width='15%'></td>
						<td width='15%'></td>
						<td width='15%'>".$ukey."</td>
						<td width='5%' align='right'>".round($uval['hour'], 2)."</td>
						<td width='5%' align='right'>".round($uval['cost'], 2)."</td>
						<td width='5%' align='right'>".round($per_hr, 2)."</td>
						<td width='5%' align='right'>".round($per_cost, 2)."</td>
					</tr>";
					$per_hr = '';
				}
			}
		}
		echo "</table>";
	}
}
?>
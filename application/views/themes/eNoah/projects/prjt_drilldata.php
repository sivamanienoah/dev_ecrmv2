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
		if(isset($tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost']))
		$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->project_code][$rec->empname]['cost'] = $rec->resource_duration_cost;
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
	}
}
?>
<h2><?php echo $heading; ?> :: Group By - Project</h2>
<?php
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
if(!empty($tbl_data)) {
	echo "<table class='data-table prac-dt'>
			<tr>
			<th width='15%'><b>PROJECT NAME</b></th>
			<th width='15%'><b>USER NAME</b></th>
			<th width='5%'><b>HOUR</b></th>
			<th width='5%'><b>COST</b></th>
			<th width='5%'><b>% of HOUR</b></th>
			<th width='5%'><b>% of COST</b></th>
		</table>";
	foreach($tbl_data as $dept=>$proj_ar) {
		foreach($proj_ar as $p_name=>$user_ar) {
			echo "<table class='data-table'>";
			$name = isset($project_master[$p_name]) ? $project_master[$p_name] : $p_name;
			echo "<tr><th colspan='6'>".strtoupper($name)."</th></tr>";
			foreach($user_ar as $ukey=>$pval) {
				$per_hr = ($pval['hour']/160) * 100;
				$per_cost = ($pval['cost']/160) * 100;
				echo "<tr>
					<td></td>
					<td>".$ukey."</td>
					<td align='right'>".round($pval['hour'], 2)."</td>
					<td align='right'>".round($pval['cost'], 2)."</td>
					<td align='right'>".round($per_hr, 2)."</td>
					<td align='right'>".round($per_cost, 2)."</td>
				</tr>";
				$per_hr = '';
			}
		}
		echo "</table>";
	}
}
?>
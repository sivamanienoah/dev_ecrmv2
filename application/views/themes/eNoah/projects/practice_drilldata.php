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
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'])) {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] += $rec->duration_hours;
		} else {
			$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['hour'] = $rec->duration_hours;
		}
		if(isset($tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost']))
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] += $rec->resource_duration_cost;
		else
		$tbl_data[$rec->dept_name][$rec->practice_name][$rec->skill_name][$rec->empname][$rec->project_code]['cost'] = $rec->resource_duration_cost;
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
	}
}
// echo "<pre>"; print_r($tbl_data); echo "</pre>";
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
	foreach($tbl_data as $dept=>$prac_ar) {
		foreach($prac_ar as $pkey=>$skil_ar) {
			echo "<table class='data-table'>";
			echo "<tr><th colspan='8'><b>".strtoupper($pkey)."</b></th></tr>";
			foreach($skil_ar as $skey=>$user_ar) {
				echo "<tr><td width='16%'></td><td colspan='7'>".$skey."</td></tr>";
				foreach($user_ar as $ukey=>$proj_ar) {
					echo "<tr>
						<td></td>
						<td width='12%'></td>
						<td colspan='6'>".$ukey."</td>
					</tr>";
					foreach($proj_ar as $p_name=>$pval) {
						$per_hr = ($pval['hour']/160) * 100;
						$per_cost = ($pval['cost']/160) * 100;
						echo "<tr>
							<td></td>
							<td></td>
							<td width='15%'></td>
							<td width='15%'>".$project_master[$p_name]."</td>
							<td align='right' width='5%'>".round($pval['hour'], 2)."</td>
							<td align='right' width='5%'>".round($pval['cost'], 2)."</td>
							<td align='right' width='5%'>".round($per_hr, 2)."</td>
							<td align='right' width='5%'>".round($per_cost, 2)."</td>
						</tr>";
						$per_hr = '';
					}
				}
			}
			echo "</table>";
		}
	}
}
?>
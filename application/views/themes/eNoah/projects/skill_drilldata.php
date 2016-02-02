<style>
table.prac-dt th{ text-align:center; }
</style>
<div class="clear"></div>
<?php
$tbl_data = array();
$sub_tot = array();
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
	}
}
// echo "<pre>"; print_r($sub_tot); echo "</pre>";
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
			$i=0;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='30%' class='collapse' colspan='2'><span class='toggle'></span> ".strtoupper($skil_key)."</th>
				<th width='15%' class='rt-ali'>SUB TOTAL:</th>
				<th class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_hour'], 2)."</th>
				<th class='rt-ali'>".round($sub_tot[$dept][$skil_key]['sub_tot_cost'], 2)."</th>
				<th></th>
				<th></th>
			</tr>";
			foreach($proj_ar as $pkey=>$user_ar) {
				$i=1;
				$name = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				echo "<tr data-depth='".$i."' class='collapse'><td width='15%'></td><td colspan='6'><span class='toggle'></span> ".$name."</td></tr>";
				$i++;
				foreach($user_ar as $ukey=>$uval){
					$per_hr = ($uval['hour']/160) * 100;
					$per_cost = ($uval['cost']/160) * 100;
					echo "<tr data-depth='".$i."' class='collapse'>
						<td width='15%'></td>
						<td width='15%'></td>
						<td width='15%'>".$ukey."</td>
						<td width='5%' align='right'>".round($uval['hour'], 2)."</td>
						<td width='5%' align='right'>".round($uval['cost'], 2)."</td>
						<td width='5%' align='right'>".round($per_hr, 2)."</td>
						<td width='5%' align='right'>".round($per_cost, 2)."</td>
					</tr>";
					$per_hr = '';
					$i++;
				}
			}
		}		
	}
	echo "<tr data-depth='0'>
			<td width='80%' colspan='3' class='rt-ali'><b>TOTAL:</b></td>
			<th width='5%' class='rt-ali'>".round($tot_hour, 2)."</th>
			<th width='5%' class='rt-ali'>".round($tot_cost, 2)."</th>
			<th width='5%'></th>
			<th width='5%'></th>
			</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
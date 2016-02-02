<style>
table.prac-dt th{
	text-align:center;
}
</style>
<div class="clear"></div>
<?php
$tbl_data = array();
$sub_tot  = array();
$cost_arr = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
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
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
	}
}
// echo "<pre>"; print_r($cost_arr); echo "</pre>";
?>
<h2><?php echo $heading; ?> :: Group By - Resource</h2>
<?php
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
		foreach($us_ar as $p_name=>$proj_ar) {
			$i=0;
			echo "<tr data-depth='".$i."' class='collapse'>
					<th class='collapse'><span class='toggle'></span> ".strtoupper($p_name)."</th>
					<th width='15%' class='rt-ali'>SUB TOTAL:</th>
					<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_hour'], 2)."</th>
					<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_cost'], 2)."</th>
					<th width='5%'></th>
					<th width='5%'></th>
				</tr>";
			foreach($proj_ar as $pkey=>$pval) {
				$i=1;
				$rate_pr_hr = isset($cost_arr[$p_name])?$cost_arr[$p_name]:0;
				$name       = isset($project_master[$pkey]) ? $project_master[$pkey] : $pkey;
				$per_hr   	= ($pval['hour']/160) * 100;
				$per_cost 	= (($pval['hour']*$rate_pr_hr)/(160*$pval['hour'])) * 100;
				
				echo "<tr data-depth='".$i."' class='collapse'>
					<td width='15%'></td>
					<td width='15%'>".$name."</td>
					<td width='5%' align='right'>".round($pval['hour'], 2)."</td>
					<td width='5%' align='right'>".round($pval['cost'], 2)."</td>
					<td width='5%' align='right'>".round($per_hr, 2)."</td>
					<td width='5%' align='right'>".round($per_cost, 2)."</td>
				</tr>";
				$per_hr = '';
				$i++;
			}
		}
	}
	echo "<tr data-depth='0'>
		<td width='80%' colspan='2' class='rt-ali'><b>TOTAL:</b></td>
		<th width='5%' class='rt-ali'>".round($tot_hour, 2)."</th>
		<th width='5%' class='rt-ali'>".round($tot_cost, 2)."</th>
		<th width='5%'></th>
		<th width='5%'></th>
		</tr>";
	echo "</table>";
}
?>
<script type="text/javascript" src="assets/js/projects/table_collapse.js"></script>
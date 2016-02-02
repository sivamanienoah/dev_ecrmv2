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
$usercnt  = array();
$prac = array();
$dept = array();
$skil = array();
$proj = array();
$tot_hour = 0;
$tot_cost = 0;
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

		if(isset($sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour']))
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour'] +=  $rec->duration_hours;
		else
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_hour'] =  $rec->duration_hours;
		
		if(isset($sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost']))
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost'] +=  $rec->resource_duration_cost;
		else
		$sub_tot[$rec->dept_name][$rec->project_code]['sub_tot_cost'] =  $rec->resource_duration_cost;
	
		$tot_hour = $tot_hour + $rec->duration_hours;
		$tot_cost = $tot_cost + $rec->resource_duration_cost;
		
		$cost_arr[$rec->empname] = $rec->cost_per_hour;
		
		//head count
		if (!in_array($rec->username, $usercnt[$rec->dept_name][$rec->project_code]))
		$usercnt[$rec->dept_name][$rec->project_code][] = $rec->username;
	}
}
?>
<h2><?php echo $heading; ?> :: Group By - Project</h2>
<?php
// echo "<pre>"; print_r($usercnt); echo "</pre>";
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
	echo "<table id='project_dash' class='data-table'>";
	foreach($tbl_data as $dept=>$proj_ar) {
		foreach($proj_ar as $p_name=>$user_ar) {
			$i       = 0;
			$res_cnt = 0;
			$name    = isset($project_master[$p_name]) ? $project_master[$p_name] : $p_name;
			$res_cnt = count($usercnt[$dept][$p_name]);
			$per_sub_hr = ($sub_tot[$dept][$p_name]['sub_tot_hour']/(160*$res_cnt))*100;
			echo "<tr data-depth='".$i."' class='collapse'>
				<th width='15%' class='collapse'><span class='toggle'></span> ".strtoupper($name)."</th>
				<th width='15%' class='rt-ali'>SUB TOTAL:</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_hour'], 2)."</th>
				<th width='5%' class='rt-ali'>".round($sub_tot[$dept][$p_name]['sub_tot_cost'], 2)."</th>
				<th width='5%' class='rt-ali'>".round($per_sub_hr, 2)."</th>
				<th width='5%'></th>
			</tr>";
			foreach($user_ar as $ukey=>$pval) {
				$i=1;
				$rate_pr_hr = isset($cost_arr[$ukey])?$cost_arr[$ukey]:0;
				$per_hr   	= ($pval['hour']/160) * 100;
				$per_cost 	= (($pval['hour']*$rate_pr_hr)/(160*$pval['hour'])) * 100;
				echo "<tr data-depth='".$i."' class='collapse'>
					<td width='15%'></td>
					<td width='15%'>".$ukey."</td>
					<td width='5%' align='right'>".round($pval['hour'], 2)."</td>
					<td width='5%' align='right'>".round($pval['cost'], 2)."</td>
					<td width='5%' align='right'>".round($per_hr, 2)."</td>
					<td width='5%' align='right'>".round($per_cost, 2)."</td>
				</tr>";
				$per_hr		= '';
				$rate_pr_hr = 0;
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
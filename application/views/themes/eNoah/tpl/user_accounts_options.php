<?php
$contractor_list = '';
$contractor_list_select1 = '';
$contractor_list_select2 = '';
$contractor_list_selecttemp2 = '';
$assignContractors = '';
$i = 0;
$ua_options = '';
$pm_options = '';
$ua_id_name = array();

$remind_options = ''; // let's use this same loop to set the reminder groups
$remind_options_all = ''; // this is for admins
$contractor_options = '';
if (count($user_accounts)) foreach ($user_accounts as $ua)
{
	$ua_id_name[$ua['userid']] = $ua['first_name'] . ' ' . $ua['last_name'];
	$cl_checked = '';
	$cl_checked1 = '';
		
	if (isset($assigned_contractors) && is_array($assigned_contractors) && in_array($ua['userid'], $assigned_contractors))
	{
		$contractor_name = ($userdata['userid'] == $ua['userid']) ? 'Me' : $ua_id_name[$ua['userid']];
		
		$cl_checked = ' checked="checked"';
		$cl_checked1 = ' selected="selected"';
		
		$contractor_options .= '<option value="' . $ua['userid'] . '">' . $contractor_name . '</option>';
		//For listing the assigned contractors in the 2nd Multiple SELECT BOX in project_view_quote.php
		$contractor_list_selecttemp2 .= '<option value="' . $ua['userid'] . '"' .$cl_checked1.'>' . $ua_id_name[$ua['userid']] . '</option>';
		$assignContractors = $ua['userid'];
	}
	
	$contractor_list .= '<label><input type="checkbox" value="' . $ua['userid'] . '" name="contractor_job[]" ' . $cl_checked . ' /> ' . $ua_id_name[$ua['userid']] . '</label>';
	//1st Multiple Select Box in project_view_quote.php
	if (trim($assignContractors) != trim($ua['userid'])){
		$contractor_list_select1 .= '<option value="' . $ua['userid'] . '">' . $ua_id_name[$ua['userid']] . '</option>';
	}
	$i++;
	//2nd Multiple Select Box in project_view_quote.php
	$contractor_list_select2 = $contractor_list_selecttemp2;

	$pl_sel = '';
	// echo "<pre>"; print_r($quote_data); exit;
	if (isset($quote_data) && $quote_data['assigned_to'] == $ua['userid'])
	{
		$pl_sel = ' selected="selected"';
	}
	$ua_options .= '<option value="' . $ua['userid'] . '"' . $pl_sel . '>' . $ua_id_name[$ua['userid']] . '</option>';
	
	if ($userdata['userid'] == $ua['userid'])
	{
		$remind_options = '<option value="' . $ua['userid'] . '">Me</option>';
	}
	else 
	{
		$remind_options_all .= '<option value="' . $ua['userid'] . '">' . $ua_id_name[$ua['userid']] . '</option>';
	}
	
}

if (isset($pm_accounts) && count($pm_accounts)>0) {
	foreach ($pm_accounts as $pm)
	{
		$pm_id_name[$pm['userid']] = $pm['first_name'] . ' ' . $pm['last_name'];
			
		$pl_sel = '';
		if (isset($quote_data) && $quote_data['assigned_to'] == $pm['userid'])
		{
			$pl_sel = ' selected="selected"';
		}
		$pm_options .= '<option value="' . $pm['userid'] . '"' . $pl_sel . '>' . $pm_id_name[$pm['userid']] . '</option>';
	}
}
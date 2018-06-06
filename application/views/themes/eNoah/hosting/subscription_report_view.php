<?php $cfg = $this->config->item('crm'); ?>
<?php $userdata = $this->session->userdata('logged_in_user'); 
//echo $this->session->userdata('viewlead');
?>
<?php
$this->load->helper('custom_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$GLOBALS['default_cur_ids'] = $default_currency['expect_worth_id'];
	$GLOBALS['default_cur_names'] = $default_currency['expect_worth_name'];
} else {
	$GLOBALS['default_cur_ids'] = '1';
	$GLOBALS['default_cur_names'] = 'USD';
}
?>

<div id="ad_filter" class="clear">
<!-- style="overflow:scroll; height:400px; width:940px;" -->

<?php 
//echo $regionname.','.$countryname.','.$statename.','.$locname."<br/>";
$sort = 'region_name';
if(!empty($locname) && $locname!='null'){
	$sort = 'location_name';
}elseif (!empty($statename) && $statename != 'null'){
	$sort = 'state_name';
}
elseif (!empty($countryname) && $countryname!='null'){
	$sort = 'country_name';
}else{
	$sort = "region_name";
}

if($num>0)
{
	$content = '';
	$res_cnt = 0;
	$amount = 0;
	$gross=0;
	$region = array();
	$total_cnt = count($res);
	//echo "<pre>";print_r($res); exit;
	foreach ($res as $lead)
	{				
			$res_cnt++;			
			$content.= "<tr>";
				/*$content .= "<td>";
				$content .= $lead->region_name;
				$content .= "</td>";*/
				
				$content .= "<td>";
				$content .= $lead->domain_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->subscriptions_type_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->customer_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->owner_first_name.' '.$lead->owner_last_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= get_lead_assigne_names($lead->lead_assign);
				$content .= "</td>";
				
				
				
				$content .= "<td>";
				$content .= $lead->lead_indicator;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->lead_stage_name;
				$content .= "</td>";
				
				$content .= "<td>";	
				switch ($lead->lead_status)
				{
					case 1:
						$status = 'Active';
					break;
					case 2:
						$status = 'On Hold';
					break;
					case 3:
						$status = 'Dropped';
					break;
					case 4:
						$status = 'Closed';
					break;
				}
				$content .= $status;
				$content .= "</td>";
				
				
				$content .= "<td align = 'right'>";
				//$content .= $lead->expect_worth_amount;				 
				$amt_converted = conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$GLOBALS['default_cur_ids']]);
				$content .= $amt_converted;
				$content .= "</td>";
				$amount += $amt_converted;
				
			$content .= "</tr>";
			
			if(empty($res[$res_cnt]->$sort) || $res[$res_cnt]->$sort != $lead->$sort)
			//if(empty($res[$res_cnt]->country_name) || $res[$res_cnt]->country_name != $lead->country_name)
			{
                         //   echo'hi';exit;
				
				
				
				
				//createTable($content);				
				//$content='';
			}
	}
        
	createTable($content);		
	
}else{
	$content .= "<tr>";
		$content .= "<td colspan = '9' align = 'center'><strong>No result</strong></td>";
		//$content .= "<td colspan = '4'><strong>".$amount."</strong></td>";
	$content .= "</tr>";
	createTable($content);
}

function createTable($content,$reg)
{
	if(!empty($reg)){
		echo "<h3 style='border-bottom:1px solid #ccc;'>$reg</h3>";
	}
	$table = '<table border="0" cellpadding="0" cellspacing="0" class="data-table lead-table" width="100%">';
	$table .= "<thead>";
	$table .= "<tr>";
	//$table .= "<th>Region</th>";
	$table .= "<th>Subscription Name</th>";
	$table .= "<th>Subscription type</th>";
	$table .= "<th>Customer</th>";
	$table .= "<th>Subscription Status</th>";
	$table .= "<th>DNS</th>";	
	
	$table .= "<th>Subscription Expiry Date</th>";
	$table .= "<th>Hosting Expiry Date</th>";
	$table .= "<th>SSL Status</th>";
	$table .= "<th>Actions</th>";
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= $content;
	$table .= "</table>";
	echo $table."<br/>";
}

function conver_currency($amount,$val)
{
	return round($amount*$val);
}
?>
	
</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/report/lead_report_region_view.js"></script>
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
<?php $cfg = $this->config->item('crm'); ?>
<?php $userdata = $this->session->userdata('logged_in_user'); 
//echo $this->session->userdata('viewlead');
?>
<div id="ad_filter" class="clear">

<?php 
if($num>0)
{
	$content = '';
	$res_cnt = 0;
	$gross=0;
	$amount = 0;
	$region = array();
	$total_cnt = count($res);
	foreach ($res as $lead)
	{				
			$res_cnt++;			
			$content.= "<tr>";
				
				$content .= "<td>";
				$content .= $lead->invoice_no;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->lead_title;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->cust_first_name.' '.$lead->cust_last_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->region_name;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->assigned_first_name.' '.$lead->assigned_last_name;
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
			
			if(empty($res[$res_cnt]->lead_source) || $res[$res_cnt]->lead_source != $lead->lead_source)
			{
				$gross+=$amount;
				if($total_cnt == $res_cnt)
				{
					$content .= "<tfoot>";
					$content .= "<tr>";
						$content .= "<td colspan = '8' align = 'right'><strong>Gross (".$GLOBALS['default_cur_names'].")</strong></td>";
						$content .= "<td align = 'right'><strong>".$gross."</strong></td>";
					$content .= "</tr>";
					$content .= "</tfoot>";	
				}	
				
				$content .= "<tfoot>";
					$content .= "<tr>";
						$content .= "<td colspan = '8' align = 'right'><strong>Total (".$GLOBALS['default_cur_names'].")</strong></td>";
						$content .= "<td align = 'right'><strong>".$amount."</strong></td>";
					$content .= "</tr>";
				$content .= "</tfoot>";
				
				//$res_cnt = 0;
				$amount=0;
				$region[] = $lead->region_name;
				createTable($content, $lead->lead_source_name);
				$content='';
			}
	}	
	
}else{
	$content .= "<tr>";
		$content .= "<td colspan = '9' align = 'center'><strong>No result</strong></td>";
	$content .= "</tr>";
	createTable($content);
}

function createTable($content,$reg)
{
	if(!empty($reg)){
		echo "<br/><h3 style='border-bottom:1px solid #ccc;'>$reg</h3>";
	}
	$table = '<table border="0" cellpadding="0" cellspacing="0" class="data-table lead-table">';
	$table .= "<thead>";
	$table .= "<tr>";
	//$table .= "<th>Region</th>";
	$table .= "<th>Lead No.</th>";
	$table .= "<th>Lead Title</th>";
	$table .= "<th>Customer</th>";
	$table .= "<th>Region</th>";
	$table .= "<th>Lead Assignee</th>";	
	
	$table .= "<th>Lead Indicator</th>";
	$table .= "<th>Lead Stage</th>";
	$table .= "<th>Status</th>";
	$table .= "<th>Expected Worth (".$GLOBALS['default_cur_names'].")</th>";
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
<script type="text/javascript" src="assets/js/report/lead_report_source_view.js"></script>
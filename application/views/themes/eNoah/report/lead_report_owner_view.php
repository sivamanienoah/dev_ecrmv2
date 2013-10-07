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
<div style="text-align:right"><a id="excel" class="export-btn">Export to Excel</a></div>
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
				/*$content .= "<td>";
				$content .= $lead->region_name;
				$content .= "</td>";*/
				
				$content .= "<td>";
				$content .= $lead->invoice_no;
				$content .= "</td>";
				
				$content .= "<td>";
				$content .= $lead->job_title;
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
				if($lead->lead_status == 1)
					$status = 'Active';
				else if ($lead->lead_status == 2)
					$status = 'On Hold';
				else 
					$status = 'Dropped';
				$content .= $status;
				$content .= "</td>";
				
				
				$content .= "<td align = 'right'>";
				//$content .= $lead->expect_worth_amount;				 
				$amt_converted = conver_currency($lead->expect_worth_amount,$rates[$lead->expect_worth_id][$GLOBALS['default_cur_ids']]);
				$content .= $amt_converted;
				$content .= "</td>";
				$amount += $amt_converted;
				
			$content .= "</tr>";
			
			if(empty($res[$res_cnt]->owner_id) || $res[$res_cnt]->owner_id != $lead->owner_id)
			{
				$gross+=$amount;
				if($total_cnt == $res_cnt)
				{
					$content .= "<tfoot>";
					$content .= "<tr>";
						$content .= "<td colspan = '8' align = 'right'><strong>Gross (USD)</strong></td>";
						$content .= "<td align = 'right'><strong>".$gross."</strong></td>";
					$content .= "</tr>";
					$content .= "</tfoot>";	
				}	
				
				$content .= "<tfoot>";
					$content .= "<tr>";
						$content .= "<td colspan = '8' align = 'right'><strong>Total (USD)</strong></td>";
						$content .= "<td align = 'right'><strong>".$amount."</strong></td>";
					$content .= "</tr>";
				$content .= "</tfoot>";
				
				//$res_cnt = 0;
				$amount=0;
				$region[] = $lead->region_name;
				createTable($content,$lead->owner_first_name.' '.$lead->owner_last_name);
				$content='';
			}
	}	
	
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
	$table .= "<th>Expected Worth (USD)</th>";
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
<!--script type="text/javascript" src="assets/js/tablesort.pager.js"></script-->
<script type="text/javascript">
$(function(){
	$('#excel').click(function() {
		//mychanges		
		var start_date = $('#task_search_start_date').val();
		var end_date = $('#task_search_end_date').val();
		var stage = $('#stage').val();
		var customer = $('#customer').val();
		var worth = $('#worth').val();
		var owner = $('#owner').val();	
		var leadassignee = $('#leadassignee').val();

		var regionname = $('#regionname').val();			
		var countryname = $('#countryname').val();		
		var statename = $('#statename').val();		
		var locname = $('#locname').val();
		
		var base_url = "<?php echo site_url(); ?>";		
		
		var url = base_url+"report/report_lead_owner/excelExport";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />'+
		  '<input type="hidden" name="start_date" value="' +start_date+ '" />' +
		  '<input type="hidden" name="end_date" value="' +end_date+ '" />' +
		  '<input type="hidden" name="stage" value="' +stage+ '" />' +
		  '<input type="hidden" name="customer" value="' +customer+ '" />' +
		  '<input type="hidden" name="worth" value="' +worth+ '" />' +		
		  '<input type="hidden" name="owner" value="' +owner+ '" />' +  
		  '<input type="hidden" name="leadassignee" value="' +leadassignee+ '" />' +

		  '<input type="hidden" name="regionname" value="' +regionname+ '" />' +
		  '<input type="hidden" name="countryname" value="' +countryname+ '" />' +
		  '<input type="hidden" name="statename" value="' +statename+ '" />' +
		  '<input type="hidden" name="locname" value="' +locname+ '" />' +
		  '</form>');
		$('body').append(form);
		$(form).submit();
			
		//var sturl = base_url+"report/report_lead_region/excelExport/"+start_date+"/"+end_date+"/"+stage+"/"+customer+"/"+worth+"/"+owner+"/"+leadassignee;
		//document.location.href = sturl;
		
		//$('#advance_search_results').load(sturl);	
		return false;
	});
	
	
    $(".lead-table").tablesorter({widthFixed: false, widgets: ['zebra']});
	//.tablesorterPager({container: $("#pager"), positionFixed: false});
    $('.data-table tr, .data-table th').hover(
        function() { $(this).addClass('over'); },
        function() { $(this).removeClass('over'); }
    );
});
</script>
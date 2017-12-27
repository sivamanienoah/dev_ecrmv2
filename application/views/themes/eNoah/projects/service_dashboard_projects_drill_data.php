<?php
$this->load->helper('custom_helper');
$this->load->helper('text_helper');
if (get_default_currency()) {
	$default_currency = get_default_currency();
	$default_cur_id   = $default_currency['expect_worth_id'];
	$default_cur_name = $default_currency['expect_worth_name'];
} else {
	$default_cur_id   = '1';
	$default_cur_name = 'USD';
}

$td_chk = false;
$td_style = '';
if(!empty($db_fields) && count($db_fields)>0){
	$td_chk = true;
	$td_cp = $td_pt = $td_rag = $td_ph = $td_bh = $td_ih = $td_nbh = $td_tuh = $td_ev = $td_pv = $td_uc = $td_dc = $td_ir = $td_contrib = $td_pl = $td_plp = 'style="display: none;"';
}
$pt_arr = array();
if(!empty($project_type) && count($project_type)>0){
	foreach($project_type as $prec){
		$pt_arr[$prec->id] = $prec->project_billing_type;
	}
}
?>
<?php
	$milestone_content = '';
	$monthly_content   = '';
	
	// echo "<pre>"; print_r($projects_data); die;
	
	if (is_array($projects_data) && count($projects_data) > 0) {
		$total_pv_amt = 0;
		$total_uc_amt = 0;
		$total_pl_amt = 0;		
		$full_total_amount_inv_raised = 0;
		$total_mile_pv_amt = 0;
		$total_mile_uc_amt = 0;
		$total_mile_pl_amt = 0;	
		foreach($projects_data as $record){
			$total_amount_inv_raised = '';
			$title		   = character_limiter($record['lead_title'], 30);
			$complete_stat = (isset($record['complete_status'])) ? ($record['complete_status']) . ' %' : '-';
 			$project_type  = ($record['project_type']) ? $pt_arr[$record['project_type']] : '-';
			$division  = ($record['division']!=null) ? $record['division'] : '-';
			$estimate_hour = (($record['estimate_hour'])) ? $record['estimate_hour'] : '-';
			$bill_hr 	   = (isset($record['bill_hr'])) ? (round($record['bill_hr'])) : '-';
			$int_hr 	   = (isset($record['int_hr'])) ? (round($record['int_hr'])) : '-';
			$nbil_hr 	   = (isset($record['nbil_hr'])) ? (round($record['nbil_hr'])) : '-';
			$total_hours   = (isset($record['total_hours'])) ? (round($record['total_hours'])) : '-';
			$total_amount_inv_raised = (isset($record['total_amount_inv_raised'])) ? ($record['total_amount_inv_raised']) : '-';
			$eff_variance  = round($total_hours-$estimate_hour);
			$actual_amt    	 = (isset($record['actual_worth_amt'])) ? (round($record['actual_worth_amt'])) : '0';
			$other_cost    	 = (isset($record['other_cost'])) ? (round($record['other_cost'])) : '0';
			$total_temp_cost = $other_cost + $record['total_cost'];
			$total_cost    	 = (isset($total_temp_cost)) ? (round($total_temp_cost)) : '0'; //total cost = utilization cost+other cost
			$total_dc_hours  = (isset($record['total_dc_hours'])) ? (round($record['total_dc_hours'])) : '0';
			$contributePercent = round((($total_amount_inv_raised - $total_cost)/$total_amount_inv_raised)*100);
			// $contributePercent = round($total_dc_hours, 2);
			$profitloss        = round($total_amount_inv_raised - $total_cost);
			$profitlossPercent = round(($profitloss/$total_amount_inv_raised)*100);
			
			switch ($record['rag_status']) {
				case 1:
					$ragStatus = '<span class=label-red></span>';
					$rag_color = '#c0504d';
				break;
				case 2:
					$ragStatus = '<span class=label-amber></span>';
					$rag_color = '#ff7e00';
				break;
				case 3:
					$ragStatus = '<span class=label-green></span>';
					$rag_color = '#468847';
				break;
				default:
					$ragStatus = "-";
					$rag_color = '';
			}
			
				$milestone_content .= '<tr bgcolor='.$rag_color.'>';
				$milestone_content .= "<td class='actions' align='center'>";
				$milestone_content .= "<a title='View' class='view-icon' href='project/view_project/".$record['lead_id']."'><img src='assets/img/view.png' alt='view'></a> ";
				if($this->session->userdata('delete')==1) {
				$milestone_content .= "<a title='Delete' class='delete' href='javascript:void(0)' onclick='return deleteProject(".$record['lead_id']."); return false;'><img src='assets/img/trash.png' alt='delete' ></a>";
				}
				$milestone_content .= "</td>";
				$milestone_content .= "<td><a href='project/view_project/".$record['lead_id']."'>".$title."</a></td>";
				if($td_chk == false) {
					$milestone_content .= "<td>".$complete_stat."</td>";
					$milestone_content .= "<td>".$project_type."</td>";				
					$milestone_content .= "<td>".$ragStatus."</td>";
					$milestone_content .= "<td>".$estimate_hour."</td>";
					$milestone_content .= "<td>".$bill_hr."</td>";
					$milestone_content .= "<td>".$int_hr."</td>";
					$milestone_content .= "<td>".$nbil_hr."</td>";
					$milestone_content .= "<td>".$total_hours."</td>";
					$milestone_content .= "<td>".$eff_variance."</td>";
					$milestone_content .= "<td>".$actual_amt."</td>";
					$milestone_content .= "<td>".$total_dc_hours."</td>";
					$milestone_content .= "<td>".$other_cost."</td>";
					$milestone_content .= "<td>".$total_cost."</td>";
					$milestone_content .= "<td>".$total_amount_inv_raised."</td>";
					$milestone_content .= "<td>".$contributePercent." %</td>";
					$milestone_content .= "<td>".$profitloss."</td>";
					$milestone_content .= "<td>".$profitlossPercent." %</td>";
				} else {
					if(($td_chk == true) && in_array('CP', $db_fields)) { $td_cp = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_cp.">".$complete_stat."</td>";
					if(($td_chk == true) && in_array('PT', $db_fields)) { $td_pt = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_pt.">".$project_type."</td>";
					if(($td_chk == true) && in_array('RAG', $db_fields)) { $td_rag = 'style="display: table-cell;"'; }		
					$milestone_content .= "<td ".$td_rag.">".$ragStatus."</td>";
					if(($td_chk == true) && in_array('PH', $db_fields)) { $td_ph = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_ph.">".$estimate_hour."</td>";
					if(($td_chk == true) && in_array('BH', $db_fields)) { $td_bh = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_bh.">".$bill_hr."</td>";
					if(($td_chk == true) && in_array('IH', $db_fields)) { $td_ih = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_ih.">".$int_hr."</td>";
					if(($td_chk == true) && in_array('NBH', $db_fields)) { $td_nbh = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_nbh.">".$nbil_hr."</td>";
					if(($td_chk == true) && in_array('TUH', $db_fields)) { $td_tuh = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_tuh.">".$total_hours."</td>";
					if(($td_chk == true) && in_array('EV', $db_fields)) { $td_ev = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_ev.">".$eff_variance."</td>";
					if(($td_chk == true) && in_array('PV', $db_fields)) { $td_pv = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_pv.">".$actual_amt."</td>";
					if(($td_chk == true) && in_array('DC', $db_fields)) { $td_dc = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_dc.">".$total_dc_hours."</td>";
					if(($td_chk == true) && in_array('UC', $db_fields)) { $td_uc = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_uc.">".$other_cost."</td>";
					if(($td_chk == true) && in_array('UC', $db_fields)) { $td_uc = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_uc.">".$total_cost."</td>";
					if(($td_chk == true) && in_array('IR', $db_fields)) { $td_ir = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_ir.">".$total_amount_inv_raised."</td>";
					if(($td_chk == true) && in_array('Contribution %', $db_fields)) {$td_contrib = 'style="display: table-cell;"';}
					$milestone_content .= "<td ".$td_contrib.">".$contributePercent." %</td>";
					if(($td_chk == true) && in_array('P&L', $db_fields)) { $td_pl = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_pl.">".$profitloss."</td>";
					if(($td_chk == true) && in_array('P&L %', $db_fields)) { $td_plp = 'style="display: table-cell;"'; }
					$milestone_content .= "<td ".$td_plp.">".$profitlossPercent." %</td>";
				}
				$milestone_content .= "</tr>";
				$full_total_mile_amount_inv_raised += $total_amount_inv_raised;
				$total_mile_pv_amt += $actual_amt;
				$total_mile_uc_amt += $total_cost;
				$total_mile_pl_amt += $profitloss;
			
			$complete_stat = $project_type = $estimate_hour = '';
		}
	}
?>
<div class="page-title-head">
	<h2 class="pull-left borderBtm">Projects</h2>
	<div class="section-right">
		<div class="buttons export-to-excel">
			<button type="button" id='service_dashboard_export_excel' class="positive excel" onclick="location.href='#'">
			Export to Excel
			</button>
			<input type="hidden" name="practices" id="practices" value="<?php echo $practices_id; ?>">
			<input type="hidden" name="excelexporttype" id="excelexporttype" value="<?php echo $excelexporttype; ?>">
		</div>
	</div>
</div>

<div class="customize-sec">
<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" width="100%">
	<thead>
		<tr>
			<th>Action</th>
			<th>Title</th>
			<?php if($td_chk == false) { ?>
				<th title="Completion Percentage">CP % </th>
				<th title="Project Type">PT</th>			
				<th title="RAG Status">RAG</th>
				<th title="Planned Hour">PH</th>
				<th title="Billable Hour">BH</th>
				<th title="Internal Hour">IH</th>
				<th title="Non-Billable Hour">NBH</th>
				<th title="Total Utilized Hours">TUH</th>
				<th title="Effort Variance">EV</th>
				<th title="Project Value">PV(<?php echo $default_cur_name; ?>)</th>
				<th title="Resource Cost">RC(<?php echo $default_cur_name; ?>)</th>
				<th title="Other Cost">OC(<?php echo $default_cur_name; ?>)</th>
				<th title="Total Utilization Cost">TUC(<?php echo $default_cur_name; ?>)</th>
				<th title="Invoice Raised">IR(<?php echo $default_cur_name; ?>)</th>
				<th title="Contribution Percentage">Contribution %</th>
				<th title="P&L">P&L </th>
				<th title="P&L %">P&L % </th>
			<?php } else { ?>
				<th <?php echo $td_cp; ?> title="Completion Percentage">CP % </th>
				<th <?php echo $td_pt; ?> title="Project Type">PT</th>			
				<th <?php echo $td_rag; ?> title="RAG Status">RAG</th>
				<th <?php echo $td_ph; ?> title="Planned Hour">PH</th>
				<th <?php echo $td_bh; ?> title="Billable Hour">BH</th>
				<th <?php echo $td_ih; ?> title="Internal Hour">IH</th>
				<th <?php echo $td_nbh; ?> title="Non-Billable Hour">NBH</th>
				<th <?php echo $td_tuh; ?> title="Total Utilized Hours">TUH</th>
				<th <?php echo $td_ev; ?> title="Effort Variance">EV</th>
				<th <?php echo $td_pv; ?> title="Project Value">PV(<?php echo $default_cur_name; ?>)</th>
				<th <?php echo $td_uc; ?> title="Resource Cost">RC(<?php echo $default_cur_name; ?>)</th>
				<th <?php echo $td_dc; ?> title="Other Cost">OC(<?php echo $default_cur_name; ?>)</th>
				<th <?php echo $td_uc; ?> title="Total Utilization Cost">TUC(<?php echo $default_cur_name; ?>)</th>
				<th <?php echo $td_ir; ?> title="Invoice Raised">IR(<?php echo $default_cur_name; ?>)</th>
				<th <?php echo $td_contrib; ?> title="Contribution Percentage">Contribution %</th>
				<th <?php echo $td_pl; ?> title="P&L">P&L </th>
				<th <?php echo $td_plp; ?> title="P&L %">P&L % </th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php echo $milestone_content; ?>
	</tbody>	
</table>
</div>

<div class='clear'></div>
<fieldset>
	<legend>Legend</legend>
	<div align="left" style="background: none repeat scroll 0 0 #3b5998;">
		<!--Legends-->
		<div class="legend">
			<div class="pull-left"><strong>CP</strong> - Completion Percentage</div>
			<div class="pull-left"><strong>PT</strong> - Project Type</div>
			<div class="pull-left"><strong>PH</strong> - Planned Hours</div>
			<div class="pull-left"><strong>BH</strong> - Billable Hours</div>
			<div class="pull-left"><strong>IH</strong> - Internal Hours</div>
			<div class="pull-left"><strong>NBH</strong> - Non Billable Hours</div>
			<div class="pull-left"><strong>TUH</strong> - Total Utilized Hours</div>
			<div class="pull-left"><strong>PV</strong> - Project Value </div>
			<div class="pull-left"><strong>RC</strong> - Resource Cost</div>
			<div class="pull-left"><strong>OC</strong> - Other Cost</div>
			<div class="pull-left"><strong>TUC</strong> - Total Utilization Cost</div>
			<div class="pull-left"><strong>IR</strong> - Invoice Raised </div>
			<div class="pull-left"><strong>P&L </strong> - Profit & Loss </div>
		</div>
	</div>
</fieldset>
<!--script type="text/javascript" src="assets/js/projects/projects_view_inprogress.js"></script-->
<script type="text/javascript">
$(function() {
	dtPjtTable();
	
		/*for project field customize*/
	var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
	nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';
	  $('.modal-custom-fields').click(function(){
	   $.blockUI({
					message:nc_form_msg,
					css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default', position: 'absolute'},
					overlayCSS: {backgroundColor:'#EAEAEA', opacity: '0.9', cursor: 'wait'}
				});
		$.get(
			'project/set_dashboard_fields',
			{},
			function(data){
				$('.new-cust-form-loader').slideUp(500, function(){
					$(this).parent().css({backgroundColor: '#fff', color: '#333'});
					$(this).css('text-align', 'left').html(data).slideDown(500, function(){
						$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					});
				})
			}
		);
		return false;
	});
	
	//export to excel
	$('#service_dashboard_export_excel').click(function() {
		var practice   		= $('#practices').val();
		var excelexporttype = $('#excelexporttype').val();
		var fy_name   	 	= $('#fy_name').val();
		var start_month 	= $("#start_month").val();
		var end_month   	= $("#end_month").val();
		// var billable_month   	 = $("#billable_month").val();

		var url = site_base_url+"projects/dashboard/service_dashboard_data/";
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input id="practice" type="hidden" name="practice" value="'+practice+'" />'+
		  '<input id="clicktype" type="hidden" name="clicktype" value="'+excelexporttype+'" />'+
		  '<input id="fy_name" type="hidden" name="fy_name" value="'+fy_name+'" />'+
		  '<input id="start_month" type="hidden" name="start_month" value="'+start_month+'" />'+
		  '<input id="end_month" type="hidden" name="end_month" value="'+end_month+'" />'+
		  '</form>');
		$('body').append(form);
		$(form).submit();
		return false;
	});
});	
	
function dtPjtTable() {
	$('.data-tbl').dataTable({
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": false,
		"bDestroy": true
	});
}
</script>


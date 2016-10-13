<?php
ob_start();
require (theme_url().'/tpl/header.php');
?>
<div id="content">
	<div class="inner">	
	<h2><?php echo $page_heading; ?></h2>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
		<thead>
			<tr>
				<th>S.No</th>
				<th>Reseller Name</th>				
				<th>Active Leads</th>
				<th>Active Projects</th>
				<th>Agreement Start Date</th>
				<th>Agreement End Date</th>
				<th>Contract Manager</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; $sno = 1; ?>
			<?php if(is_array($reseller) && !empty($reseller) && count($reseller)>0) { ?>			
				<?php foreach($reseller as $row) {  ?>
					<tr>
						<td class="actions">
							<?php echo $sno; ?>
							<!--a href="reseller/edit_reseller/update/<?php #echo $row['userid'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'> </a--> 
						</td>
						<td>
							<?php 
								$reseller_name = $row['first_name'];
								if(!empty($row['last_name'])){
									$reseller_name .= " ". $row['last_name'];
								}
							?>
							<a href="reseller/view_reseller/update/<?php echo $row['userid'] ?>" title='View Reseller'><?php echo $reseller_name; ?></a>
						</td>
						<td>
							<?php
								$get_resller_leads = getResellerActiveLeads($row['userid']); 
								$active_leads = isset($get_resller_leads) ? $get_resller_leads : "";
								if(isset($active_leads) && !empty($active_leads)) {
							?>
									<a href="javascript:void(0)" onclick="getActiveLeads('<?php echo $row['userid']; ?>'); return false;"><?php echo $active_leads; ?></a>
							<?php
								}
							?>
						</td>
						<td>
							<?php
								$get_resller_projects = getResellerActiveProjects($row['userid']); 
								$active_projects = isset($get_resller_projects) ? $get_resller_projects : "";
								if(isset($active_projects) && !empty($active_projects)) {
							?>
									<a href="javascript:void(0)" onclick="getActiveProjects('<?php echo $row['userid']; ?>'); return false;"><?php echo $active_projects; ?></a>
							<?php
								}
							?>
						</td>
						<td>
							<?php
								$get_dates = getResellerAgreementDate($row['userid']);
								echo (isset($get_dates) && !empty($get_dates['contract_start_date'])) ? date('d-m-Y', strtotime($get_dates['contract_start_date'])) : "";
							?>
						</td>
						<td>
							<?php
								echo (isset($get_dates) && !empty($get_dates['contract_end_date'])) ? date('d-m-Y', strtotime($get_dates['contract_end_date'])) : "";
								if(isset($get_dates) && !empty($get_dates['renewal_reminder_date'])) {
									if(strtotime($get_dates['renewal_reminder_date']) <= strtotime(date('Y-m-d H:i:s'))) {
										echo " <span class='red pull-right'>Renew</span>";
									}
								}
								$get_dates = array();
							?>
						</td>
						<td><?php $get_contract_manager_name = getContractManagerName($row['contract_manager']); echo isset($get_contract_manager_name) ? $get_contract_manager_name : ""; ?></td>
					</tr>
				<?php $i++; $sno++; } ?>
			<?php } ?>
		</tbody>
	</table>
	<div class="clearfix"></div>
	<div id="heading_container" style="margin:20px 0;"></div>
	<div id="drilldown_data" class="" style="margin:5px 0;display:none;"></div>

	<?php } else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--/Inner div -->
</div><!--/Content div -->
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript">
function getActiveProjects(userid)
{
	$.ajax({
		type: "POST",
		url: site_base_url+'reseller/getResellerActiveProjects/',
		data: 'filter=filter'+'&userid='+userid+'&'+csrf_token_name+'='+csrf_hash_token,
		cache: false,
		beforeSend:function() {
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#heading_container').html('<div class="page-title-head"><h2 class="pull-left borderBtm">Active Projects</h2></div>');
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}

function getActiveLeads(userid)
{
	$.ajax({
		type: "POST",
		url: site_base_url+'reseller/getResellerActiveLeads/',
		data: 'filter=filter'+'&userid='+userid+'&'+csrf_token_name+'='+csrf_hash_token,
		cache: false,
		beforeSend:function() {
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#heading_container').html('<div class="page-title-head"><h2 class="pull-left borderBtm">Active Leads</h2></div>');
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}

function resellerDataTable()
{
	$('.data-tbl').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"bDestroy": true
	});
}
</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar { display: none; }
.dept_section{ width:100%; float:left; margin:20px 0 0 0; }
.dept_section div{ width:49%; }
.dept_section div:first-child{ margin-right:2% }
table.bu-tbl th{ text-align:center; }
table.bu-tbl{ width:70%; }
table.bu-tbl-inr th{ text-align:center; }
</style>
<div id="content">
    <div class="inner">
        <?php if($this->session->userdata('viewPjt')==1) { ?>
<?php
$practice_arr = array();
$total_irval = $totCM_Irval = $totEV = $totDC = $totCM_DC =  0;
// echo "<pre>"; print_r($projects); echo "</pre>";
?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
		</div>

		<!--div id="ajax_loader" style="margin:20px;" align="center">
			Loading Content.<br><img alt="wait" src="<?php #echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
		</div-->
		<div id="default_view">
			<table cellspacing="0" cellpadding="0" border="0" class="data-table proj-dash-table bu-tbl">
				<tr>
					<thead>
						<th>IT Services Dashboard</th>
						<?php if(!empty($practice_data)) { ?>
							<?php foreach($practice_data as $prac) { ?>
								<th><?php echo $prac->practices; ?></th>
								<?php $practice_arr[] = $prac->practices; ?>
							<?php } ?>
						<?php } ?>
					</thead>
				</tr>
				<tr>
					<td><b>Number of Projects currently running</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['practicewise'][$parr]) ? $projects['practicewise'][$parr] : ''; ?>
								<?php
									$total_irval += isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : 0;
									$totCM_Irval += isset($projects['cm_irval'][$parr]) ? $projects['cm_irval'][$parr] : '';
									$totEV += isset($projects['eff_var'][$parr]) ? $projects['eff_var'][$parr] : '';
									$totDC += isset($projects['dc'][$parr]) ? $projects['dc'][$parr] : '';
									$totCM_DC += isset($projects['cm_dc'][$parr]) ? $projects['cm_dc'][$parr] : '';
								?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Number of projects in Red</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['rag_status'][$parr]) ? $projects['rag_status'][$parr] : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>YTD Billing (USD)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['irval'][$parr]) ? round($projects['irval'][$parr]) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Billable for the month (%)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['cm_dc'][$parr]) ? round(($projects['cm_dc'][$parr]/$totCM_DC)*100, 2) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Billable YTD (%)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['irval'][$parr]) ? round(($projects['irval'][$parr]/$total_irval)*100, 2) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Effort Variance (%)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['eff_var'][$parr]) ? round(($projects['eff_var'][$parr]/$totEV)*100, 2) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Contribution for the month (%)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['cm_dc'][$parr]) ? round(($projects['cm_dc'][$parr]/$totCM_DC)*100, 2) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
				<tr>
					<td><b>Contribution YTD (%)</b></td>
					<?php if(!empty($practice_arr)) { ?>
						<?php foreach($practice_arr as $parr) { ?>
							<td align='right'>
								<?php echo isset($projects['dc'][$parr]) ? round(($projects['dc'][$parr]/$totDC)*100, 2) : ''; ?>
							</td>
						<?php } ?>
					<?php } ?>
				</tr>
			</table>
				
			<div class="clearfix"></div>
			<div id="drilldown_data" class="" style="margin:20px 0;display:none;">
			
			</div>
        <?php 
		} else {
			echo "You have no rights to access this page";
		} 
		?>
		</div>
	</div>

<script type="text/javascript">
/*
$(function() {

	$('body').on('change','#skill_ids',function(){
		var dids       = $('#department_ids').val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var sids = $(this).val();
		$("#filter_area_status").val('1');
		$('#member_ids').html('');
		var params = { 'dept_ids':dids,'skill_ids':sids,'start_date':start_date,'end_date':end_date };
		params[csrf_token_name] = csrf_hash_token;
		
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_skill_members',
			data: params,
			success: function(members) {
				if(members){
					var mem_html;
					var users = $.parseJSON(members);
					if(users.length){
						for(var i=0;i<users.length;i++){
							mem_html +='<option value="'+users[i].username+'">'+users[i].emp_name+'</option>';
						}	
					}
					$('#member_ids').html('');
					$('#member_ids').append(mem_html);
				}
			}
		});
	});

function getData(resource_type, dept_type)
{
	$('#filter_group_by').prop('selectedIndex',0);
	if($('#department_ids').val() == null) {
		$('#hdept_ids').val('');
	} else {
		$('#hdept_ids').val($('#department_ids').val());
	}
	if($('#practice_ids').val() == null) {
		$('#hprac_ids').val('');
	} else {
		$('#hprac_ids').val($('#practice_ids').val());
	}
	$('#hmonth_year').val($('#month_year_from_date').val());
	$('#hmonth_to_year').val($('#month_year_to_date').val());
	$('#hskill_ids').val($('#skill_ids').val())
	$('#hmember_ids').val($('#member_ids').val())
	if($('#exclude_leave').attr('checked'))
	$('#hexclude_leave').val(1);
	if($('#exclude_holiday').attr('checked'))
	$('#hexclude_holiday').val(1)
	
	var formdata = $('#fliter_data').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/get_data/',                                                              
		data: formdata+'&resource_type='+resource_type+'&dept_type='+dept_type+'&filter_group_by=0',
		cache: false,
		beforeSend:function() {
			$('#filter_group_by').prop('selectedIndex',0);
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}
}); */
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

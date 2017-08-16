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
<script type="text/javascript">var this_is_home = true;</script>
<div id="content">
	<div class="inner">
		<?php 
		if($this->session->userdata('viewPjt')==1) 
		{ ?>
			<div class="page-title-head">
				<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
				<a class="choice-box" onclick="advanced_filter();" >
					<img src="assets/img/advanced_filter.png" class="icon leads" />
					<span>Advanced Filters</span>
				</a>
				<div class="buttons">
					<form name="fliter_data" id="fliter_data" method="post">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					</form>
				</div>
				<div class="clearfix"></div>
			</div>

			<div id="filter_section">
				<div class="clear"></div>
				<div id="advance_search" style="padding-bottom:15px; display:none;">
					<form action="<?php echo site_url('projects/dashboard/utilization_metrics_beta')?>" name="project_dashboard" id="project_dashboard" method="post">					
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">MONTH & YEAR</td>
									<td class="tblheadbg">EXCLUDE</td>
									<td class="tblheadbg">ENTITY</td>
									<td class="tblheadbg">DEPARTMENT</td>
									<td class="tblheadbg">PRACTICE</td>
									<td class="tblheadbg">SKILL</td>
									<td class="tblheadbg">RESOURCE</td>
								</tr>
								<tr>	
									<td class="month-year">
										<span>From</span> <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($start_date)); ?>" />
										<br />
										<span>To</span> <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" value="<?php echo date('F Y',strtotime($end_date)); ?>" />
									</td>
									<td class="by-exclusion">
										<?php $leaveChecked=''; if($exclude_leave==1) { $leaveChecked ='checked="checked"'; } ?>
										<label><input type="checkbox" id="exclude_leave" name="exclude_leave" <?php echo $leaveChecked; ?> value="1" /><span>Leave</span></label>
														
										<br />
										<?php $holidayChecked=''; if($exclude_holiday==1) { $holidayChecked ='checked="checked"'; } ?>
										<label><input type="checkbox" id="exclude_holiday" name="exclude_holiday" <?php echo $holidayChecked; ?> value="1" /><span>Holiday</span></label>
									</td>
									<td class="proj-dash-select">
										<select title="Select Entity" id="entity_ids" name="entity_ids[]" multiple="multiple">
										<?php if(count($entitys)>0 && !empty($entitys)) { ?>
										<?php foreach($entitys as $enty) { ?>
										<option <?php echo in_array($enty->div_id, $entity_ids) ? 'selected="selected"' : '';?> value="<?php echo $enty->div_id;?>"><?php echo $enty->division_name; ?></option>
										<?php } ?>
										<?php } ?>
										</select>
									</td>
									<td class="proj-dash-select">
										<select title="Select Department" id="department_ids" name="department_ids[]"	multiple="multiple">
											<?php if(count($departments)>0 && !empty($departments)){?>
											<?php foreach($departments as $depts){?>
											<option <?php echo in_array($depts->department_id,$department_ids)?'selected="selected"':'';?> value="<?php echo $depts->department_id;?>"><?php echo $depts->department_name;?></option>
											<?php } }?>
										</select>
									</td>
									<td class="proj-dash-select">
										<select multiple="multiple" title="Select Practice" id="practice_ids" name="practice_ids[]">
											<?php if(count($practice_ids_selected)>0 && !empty($practice_ids_selected)) { ?>
											<?php foreach($practice_ids_selected as $prac) {?>
											<option <?php echo in_array($prac->practice_id, $practice_ids)?'selected="selected"':'';?> value="<?php echo $prac->practice_id;?>"><?php echo $prac->practice_name;?></option>
											<?php } } ?>
										</select>
									</td>
									<td class="proj-dash-select">
										<select title="Select Skill" id="skill_ids" name="skill_ids[]"	multiple="multiple">
											<?php if(count($skill_ids_selected)>0 && !empty($skill_ids_selected)) { ?>
											<?php foreach($skill_ids_selected as $skills) {
											$skills->name = ($skills->skill_id==0)?'N/A':$skills->name;
											?>
											<option <?php echo in_array($skills->skill_id,$skill_ids)?'selected="selected"':'';?> value="<?php echo $skills->skill_id; ?>"><?php echo $skills->name;?></option>
											<?php } }?>
										</select>
									</td>
									<td class="proj-dash-select">
										<select title="Select Members" id="member_ids" name="member_ids[]" multiple="multiple">
											<?php if(count($member_ids_selected)>0 && !empty($member_ids_selected)){?>
											<?php foreach($member_ids_selected as $members){?>
											<option <?php echo in_array($members->username, $member_ids)?'selected="selected"':'';?>  value="<?php echo $members->username;?>"><?php echo $members->emp_name;?></option>
											<?php } }?>								
										</select>
									</td>
								</tr>
								<tr align="right" >
									<td colspan="7">
										<input type="hidden" id="start_date" name="start_date" value="" />
										<input type="hidden" id="end_date" name="end_date" value="" />
										<input type="hidden" id="filter_area_status" name="filter_area_status" value="" />
										<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
										<input type="submit" class="positive input-font" name="advance" id="advance" value="Search" />
										<div id = 'load' style = 'float:right;display:none;height:1px;'>
											<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
										</div>
									</td>
								</tr>
							</table>
						</div>
					</form>
				</div>
			</div>

			<div class="clearfix"></div>
			
			<div id="ajax_loader" style="margin:20px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>
			
			<div id="default_view">
				<table cellspacing="0" cellpadding="0" border="0" class="data-table">
					<tr>
						<thead>
							<th></th>
							<th colspan="5" align="center"><h4><?php echo $previous_year;?></h4></th>
							<th colspan="5"><h4><?php echo $current_year;?></h4></th>
							<th></th>
						</thead>
					</tr>
					<tr>
						<thead>
							<th>Month</th>
							<th>Revenue</th>
							<th>Offshore Revenue</th>
							<th>Total Cost</th>
							<th>Offshore cost 2016</th>
							<th>Contribution %age</th>
							<th>Revenue</th>
							<th>Offshore Revenue</th>
							<th>Total Cost</th>
							<th>Offshore cost 2016</th>
							<th>Contribution %age</th>
							<th>Cost Saving @ Offshore</th>
						</thead>
					</tr>
					<?php
					foreach($results as $key => $values)
					{
						$contribution_prev = (($values['revenue_prev']-$values['total_cost_prev'])/$values['revenue_prev'])*100;
						$contribution = (($values['revenue']-$values['total_cost'])/$values['revenue'])*100;
						$saving = 0;
					?>
						<tr>
							<td><?php echo $key;?></td>
							<td align="right"><?php echo $values['revenue_prev'];?></td>
							<td align="right"><?php echo $values['offshore_revenue_prev'];?></td>
							<td align="right"><?php echo $values['total_cost_prev'];?></td>
							<td align="right"><?php echo $values['offshore_cost_prev'];?></td>
							<td align="right"><?php echo round($contribution_prev)."%";?></td>
							<td align="right"><?php echo $values['revenue'];?></td>
							<td align="right"><?php echo $values['offshore_revenue'];?></td>
							<td align="right"><?php echo $values['total_cost'];?></td>
							<td align="right"><?php echo $values['offshore_cost'];?></td>
							<td align="right"><?php echo round($contribution)."%";?></td>
							<td align="right"><?php echo $saving;?></td>
						</tr>
						<?php 
						$overall_revenue+=	$values['revenue'];
						$overall_offshore_revenue+=	$values['offshore_revenue'];
						$overall_total_cost+=	$values['total_cost'];
						$overall_offshore_cost+=	$values['offshore_cost'];
						$overall_contribution+=	$values['contribution'];
						$overall_revnue_prev+=	$values['revenue_prev'];
						$overall_offshore_revenue_prev+=	$values['offshore_revenue_prev'];
						$overall_total_cost_prev+=	$values['total_cost_prev'];
						$overall_offshore_cost_prev+=	$values['offshore_cost_prev'];
						$overall_contribution_prev+=	$values['contribution_prev'];
						$overall_saving+=	$values['saving'];
						?>
					<?php } ?>
					<tr>
						<td align="right"><b>Total:</b></td>
						<td align="right"><?php echo $overall_revnue_prev;?></td>
						<td align="right"><?php echo $overall_offshore_revenue_prev;?></td>
						<td align="right"><?php echo $overall_total_cost_prev;?></td>
						<td align="right"><?php echo $overall_offshore_cost_prev;?></td>
						<td align="right"><?php echo $overall_contribution_prev;?></td>
						<td align="right"><?php echo $overall_revenue;?></td>
						<td align="right"><?php echo $overall_offshore_revenue;?></td>
						<td align="right"><?php echo $overall_total_cost;?></td>
						<td align="right"><?php echo $overall_offshore_cost;?></td>
						<td align="right"><?php echo $overall_contribution;?></td>
						<td align="right"><?php echo $overall_saving;?></td>
					</tr>
				</table>
			</div>
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


<?php require (theme_url().'/tpl/footer.php'); ?>
<script>
function revenue_cost()
{
	var start_month = '01';
	var end_month = '08';
	$.ajax({
			type: "POST",
			url: site_base_url+'cron/service_dashboard_cron_revenue/',                                                              
			data: 'start_month='+start_month+'&end_month='+end_month+'&csrf_token_name='+csrf_hash_token,
			cache: false,
			beforeSend:function() {
			
			},
			success: function(data) {
				
			}                                                                                   
		});
}
revenue_cost();
</script>
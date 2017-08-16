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
				<form action="<?php echo site_url('projects/dashboard/revenue_cost')?>" name="project_dashboard" id="project_dashboard" method="post">					
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">MONTH</td>
									<td class="tblheadbg">DEPARTMENT</td>
									<td class="tblheadbg">PRACTICE</td>
								</tr>
								<tr>	
									<td class="month-year">
										<span>From</span>
										<select id="start_month" name="start_month">
										<option value="1">Jan</option>
										<option value="2">Feb</option>
										<option value="3">March</option>
										<option value="4" selected="selected">Apr</option>
										<option value="5">May</option>
										<option value="6">Jun</option>
										<option value="7">Jul</option>
										<option value="8">Aug</option>
										<option value="9">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dec</option>
										</select>
										<br />
										<span>To</span> 
										<select id="end_month" name="end_month">
										<option value="1">Jan</option>
										<option value="2">Feb</option>
										<option value="3">Mar</option>
										<option value="4">Apr</option>
										<option value="5">May</option>
										<option value="6" selected="selected">Jun</option>
										<option value="7">Jul</option>
										<option value="8">Aug</option>
										<option value="9">Sep</option>
										<option value="10">Oct</option>
										<option value="11">Nov</option>
										<option value="12">Dec</option>
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
											<?php if(count($practice_ids)>0 && !empty($practice_ids)) { ?>
													<?php foreach($practice_ids as $prac) {?>
														<option <?php echo in_array($prac->id, $sel_practice_ids)?'selected="selected"':'';?> value="<?php echo $prac->id;?>"><?php echo $prac->practices;?></option>
											<?php } } ?>
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
						$overall_revnue_prev+=	$values['revenue_prev'];
						$overall_offshore_revenue_prev+=	$values['offshore_revenue_prev'];
						$overall_total_cost_prev+=	$values['total_cost_prev'];
						$overall_offshore_cost_prev+=	$values['offshore_cost_prev'];
						$overall_saving+=	$values['saving'];
						
						$overall_contribution_prev = (($overall_revnue_prev-$overall_total_cost_prev)/$overall_revnue_prev)*100;
						$overall_contribution = (($overall_revenue-$overall_total_cost)/$overall_revenue)*100;

						
						?>
					<?php } ?>
					<tr>
						<td align="right"><b>Total:</b></td>
						<td align="right"><?php echo $overall_revnue_prev;?></td>
						<td align="right"><?php echo $overall_offshore_revenue_prev;?></td>
						<td align="right"><?php echo $overall_total_cost_prev;?></td>
						<td align="right"><?php echo $overall_offshore_cost_prev;?></td>
						<td align="right"><?php echo round($overall_contribution_prev)."%";?></td>
						<td align="right"><?php echo $overall_revenue;?></td>
						<td align="right"><?php echo $overall_offshore_revenue;?></td>
						<td align="right"><?php echo $overall_total_cost;?></td>
						<td align="right"><?php echo $overall_offshore_cost;?></td>
						<td align="right"><?php echo round($overall_contribution)."%";?></td>
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
function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}
$(document).ready(function(){
	$("#department_ids").change(function(){
		var ids = $(this).val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var params = {'dept_ids':ids,'start_date':start_date,'end_date':end_date};
		params[csrf_token_name] = csrf_hash_token;
		$("#filter_area_status").val('1');
		$('#practice_ids').html('');
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_practices',
			data: params,
			success: function(practices) {
				if(practices){
					var prac_html='';
					var prac = $.parseJSON(practices);
					if(prac.length){
						for(var i=0;i<prac.length;i++){
							prac_html +='<option value="'+prac[i].practice_id+'">'+prac[i].practice_name+'</option>';
						}	
					}
					$('#practice_ids').html('');
					$('#practice_ids').append(prac_html);
				}
			}
		});
		return false;		
	});
});

$('#filter_reset').click(function() {
	 $("#project_dashboard").find('input:checkbox').removeAttr('checked').removeAttr('selected');
	 $("#practice_ids").html('');
	 $('select#department_ids option').removeAttr("selected");
});
</script>
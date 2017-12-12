<?php 
// echo $exclude_leave; exit;
require (theme_url().'/tpl/header.php'); ?>
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
		$this->load->helper('custom_helper');
		if (get_default_currency()) {
			$this->default_currency = get_default_currency();
			$this->default_cur_id   = $this->default_currency['expect_worth_id'];
			$this->default_cur_name = $this->default_currency['expect_worth_name'];
		} else {
			$this->default_cur_id   = '1';
			$this->default_cur_name = 'USD';
		}
		$default_currency = $this->default_cur_name;
	?>
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			
			<a class="choice-box" onclick="advanced_filter();" >
				<img src="assets/img/advanced_filter.png" class="icon leads" />
				<span>Advanced Filters</span>
			</a>
			
			<div class="chk-radio-box">	
				<?php 
					$checked_hr = $checked_cost = $checked_directcost = '';
					if($graph_based == 'hour') 
					$checked_hr = 'checked="checked"';
					else if($graph_based == 'cost') 
					$checked_cost = 'checked="checked"';
					else if($graph_based == 'directcost') 
					$checked_directcost = 'checked="checked"';
				?>
				<label><input type='radio' name='graph_based' value='hour' id='rd_grph_hr' <?php echo $checked_hr; ?> /><span>Hour</span></label>
				<label><input type='radio' name='graph_based' value='cost' id='rd_grph_cost' <?php echo $checked_cost; ?> /><span>Cost</span></label>
				<label><input type='radio' name='graph_based' value='directcost' id='rd_grph_directcost' <?php echo $checked_directcost; ?> /><span>Direct Cost</span></label>
			</div>

			<div class="chk-radio-box">	
				<?php 
					$checked_hr_percent = $checked_cost_percent = '';
					if($value_based == 'value') 
					$checked_hr_percent = 'checked="checked"';
					else if($value_based == 'percent') 
					$checked_cost_percent = 'checked="checked"';
				?>
				<label><input type='radio' name='value_based' value='value' id='rd_value' <?php echo $checked_hr_percent; ?> /><span>Value</span></label>
				<label><input type='radio' name='value_based' value='percent' id='rd_percent' <?php echo $checked_cost_percent; ?> /><span>Percentage</span></label>
			</div>
			
			<div class="buttons">
				<form name="fliter_data_trend" id="fliter_data_trend" method="post">
					<!--button  type="submit" id="excel-1" class="positive">
						Export to Excel
					</button-->
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<input type="hidden" name="start_date" value="" id="hmonth_year" />
					<input type="hidden" name="department_ids" value="" id="hdept_ids" />
					<input type="hidden" name="practice_ids" value="" id="hprac_ids" />
					<input type="hidden" name="skill_ids" value="" id="hskill_ids" />
					<input type="hidden" name="member_ids" value="" id="hmember_ids" />
					<input type="hidden" name="exclude_leave" value="" id="hexclude_leave" />
					<input type="hidden" name="exclude_holiday" value="" id="hexclude_holiday" />
					<input type="hidden" name="graph_based" value="" id="hgraph_based" />
					<input type="hidden" name="value_based" value="" id="hvalue_based" />
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div id="filter_section">
			<div class="clear"></div>
			<div id="advance_search" style="padding-bottom:15px; display:none;">
				<form action="<?php echo site_url('projects/dashboard/trend_analysis')?>" name="project_dashboard" id="project_dashboard" method="post">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">MONTH & YEAR</td>
								<td class="tblheadbg">EXCLUDE</td>
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
									<label>
										<input type="checkbox" id="exclude_leave" name="exclude_leave" <?php echo $leaveChecked; ?> value=1 />
										<span>Leave</span>
									</label>
									<br />
									<?php $holidayChecked=''; if($exclude_holiday==1) { $holidayChecked ='checked="checked"'; } ?>
									<label>
										<input type="checkbox" id="exclude_holiday" name="exclude_holiday" <?php echo $holidayChecked; ?> value=1 />
										<span>Holiday</span>
									</label>
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
												<option <?php echo in_array($skills->skill_id,$skill_ids)?'selected="selected"':'';?> value="<?php echo $skills->skill_id;?>"><?php echo $skills->name;?></option>
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
								<td colspan="6">
									<input type="hidden" id="start_date" name="start_date" value="" />
									<input type="hidden" id="end_date" name="end_date" value="" />
									<input type="hidden" id="filter_area_status" name="filter_area_status" value="<?php echo $filter_area_status; ?>" />
									<input type="hidden" name="graph_based" value="<?php echo $graph_based; ?>" id="hidgraph_based" />
									<input type="hidden" name="value_based" value="<?php echo $value_based; ?>" id="hidvalue_based" />
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
			<?php
				$user_arr    = array();
				$project_arr = array();
				$bu_arr      = array();
				$dept_arr    = array();
				$prac_arr    = array();
				$skil_arr    = array();
				$usercnt     = array();
				$deptusercnt = array();
				$business_unit 		    = array();
				$month_no_arr		    = array();
				$month_name_arr 	    = array();
				$billable_value_hr      = array();
				$internal_value_hr      = array();
				$non_billable_value_hr  = array();
				$bu_arr['totalhour'] 	= 0;
				$bu_arr['totalhead'] 	= 0;
				$bu_arr['totalcost'] 	= 0;
				// echo "<pre>"; print_r($resdata); echo "</pre>"; exit;
				if(!empty($resdata)) {
					foreach($resdata as $row){
						$mont_yr = date('Y-m', strtotime($row->start_time));
						// for business unit based
						if (isset($bu_arr['it'][$row->resoursetype][$mont_yr]['hour'])) {
							$bu_arr['it'][$row->resoursetype][$mont_yr]['hour'] = $row->duration_hours + $bu_arr['it'][$row->resoursetype][$mont_yr]['hour'];
							$bu_arr['it'][$row->resoursetype][$mont_yr]['cost'] = $row->resource_duration_cost + $bu_arr['it'][$row->resoursetype][$mont_yr]['cost'];
							$bu_arr['it'][$row->resoursetype][$mont_yr]['direct_cost'] = $row->resource_duration_direct_cost + $bu_arr['it'][$row->resoursetype][$mont_yr]['direct_cost'];
						} else {
							$bu_arr['it'][$row->resoursetype][$mont_yr]['hour'] 		= $row->duration_hours;
							$bu_arr['it'][$row->resoursetype][$mont_yr]['cost'] 		= $row->resource_duration_cost;
							$bu_arr['it'][$row->resoursetype][$mont_yr]['direct_cost'] 	= $row->resource_duration_direct_cost;
						}
						//total calculation by month
						if (isset($bu_arr['it'][$mont_yr]['totalhour'])) {
							$bu_arr['it'][$mont_yr]['totalhour'] += $row->duration_hours;
							$bu_arr['it'][$mont_yr]['totalcost'] += $row->resource_duration_cost;
							$bu_arr['it'][$mont_yr]['totaldirectcost'] += $row->resource_duration_direct_cost;
						} else {
							$bu_arr['it'][$mont_yr]['totalhour'] = $row->duration_hours;
							$bu_arr['it'][$mont_yr]['totalcost'] = $row->resource_duration_cost;
							$bu_arr['it'][$mont_yr]['totaldirectcost'] = $row->resource_duration_direct_cost;
						}
					}
				}
				$business_unit = $bu_arr['it'];
				// echo $end_date; exit;
				
				//creating values
				$i = date("Y-m", strtotime($start_date)); 
				while($i <= date("Y-m", strtotime($end_date))) {
					
					// echo $business_unit[date('Y-m', strtotime($i))]['totalhour']; exit;
					
					$month_no_arr[] = '"'.date('Y-m', strtotime($i)).'"'; // using for graph dataClick
					$month_name_arr[] = '"'.date('M Y', strtotime($i)).'"'; // for display
					$x_axis_values[]   = '"'.date('M', strtotime($i)).'"';
					//for hour
					$billable_value_hr[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['hour']) ? round($business_unit['Billable'][date('Y-m', strtotime($i))]['hour']) : 0;
					$internal_value_hr[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['hour']) ? round($business_unit['Internal'][date('Y-m', strtotime($i))]['hour']) : 0;
					$non_billable_value_hr[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['hour']) ? round($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['hour']) : 0;
					//for cost
					$billable_value_cost[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['cost']) ? round($business_unit['Billable'][date('Y-m', strtotime($i))]['cost']) : 0;
					$internal_value_cost[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['cost']) ? round($business_unit['Internal'][date('Y-m', strtotime($i))]['cost']) : 0;
					$non_billable_value_cost[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['cost']) ? round($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['cost']) : 0;
					//for hour percentage
					$billable_value_hr_percent[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['hour']) ? round(($business_unit['Billable'][date('Y-m', strtotime($i))]['hour']/$business_unit[date('Y-m', strtotime($i))]['totalhour'])*100) : 0;			
					$internal_value_hr_percent[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['hour']) ? round(($business_unit['Internal'][date('Y-m', strtotime($i))]['hour']/$business_unit[date('Y-m', strtotime($i))]['totalhour'])*100) : 0;
					$non_billable_value_hr_percent[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['hour']) ? round(($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['hour']/$business_unit[date('Y-m', strtotime($i))]['totalhour'])*100) : 0;
					//for cost percentage
					$billable_value_cost_percent[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['cost']) ? round(($business_unit['Billable'][date('Y-m', strtotime($i))]['cost']/$business_unit[date('Y-m', strtotime($i))]['totalcost'])*100) : 0;
					$internal_value_cost_percent[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['cost']) ? round(($business_unit['Internal'][date('Y-m', strtotime($i))]['cost']/$business_unit[date('Y-m', strtotime($i))]['totalcost'])*100) : 0;
					$non_billable_value_cost_percent[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['cost']) ? round(($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['cost']/$business_unit[date('Y-m', strtotime($i))]['totalcost'])*100) : 0;
					//for direct cost
					$billable_value_directcost[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['direct_cost']) ? round($business_unit['Billable'][date('Y-m', strtotime($i))]['direct_cost']) : 0;
					$internal_value_directcost[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['direct_cost']) ? round($business_unit['Internal'][date('Y-m', strtotime($i))]['direct_cost']) : 0;
					$non_billable_value_directcost[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['direct_cost']) ? round($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['direct_cost']) : 0;
					//for direct cost percentage
					$billable_value_directcost_percent[] = isset($business_unit['Billable'][date('Y-m', strtotime($i))]['direct_cost']) ? round(($business_unit['Billable'][date('Y-m', strtotime($i))]['direct_cost']/$business_unit[date('Y-m', strtotime($i))]['totaldirectcost'])*100) : 0;
					$internal_value_directcost_percent[]   = isset($business_unit['Internal'][date('Y-m', strtotime($i))]['direct_cost']) ? round(($business_unit['Internal'][date('Y-m', strtotime($i))]['direct_cost']/$business_unit[date('Y-m', strtotime($i))]['totaldirectcost'])*100) : 0;
					$non_billable_value_directcost_percent[]   = isset($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['direct_cost']) ? round(($business_unit['Non-Billable'][date('Y-m', strtotime($i))]['direct_cost']/$business_unit[date('Y-m', strtotime($i))]['totaldirectcost'])*100) : 0;
					
					if(substr($i, 5, 2) == "12")
					$i = (date("Y", strtotime($i."-01")) + 1)."-01";
					else
					$i++;

				}
				// echo "<pre>"; print_r($month_name_arr); echo "</pre>"; exit;
				
				$x_axis_values     = implode(',', $x_axis_values);
				if(($graph_based == 'hour') && ($value_based == 'value')){
					$billable_value 	= implode(',', $billable_value_hr); //billable
					$internal_value 	= implode(',', $internal_value_hr); //internal
					$non_billable_value = implode(',', $non_billable_value_hr); //non-billable
				}
				if(($graph_based == 'cost') && ($value_based == 'value')){
					$billable_value 	= implode(',', $billable_value_cost); //billable
					$internal_value 	= implode(',', $internal_value_cost); //internal
					$non_billable_value = implode(',', $non_billable_value_cost); //non-billable
				}
				if(($graph_based == 'hour') && ($value_based == 'percent')){
					$billable_value 	= implode(',', $billable_value_hr_percent); //billable
					$internal_value 	= implode(',', $internal_value_hr_percent); //internal
					$non_billable_value = implode(',', $non_billable_value_hr_percent); //non-billable
				}
				if(($graph_based == 'cost') && ($value_based == 'percent')){
					$billable_value 	= implode(',', $billable_value_cost_percent); //billable
					$internal_value 	= implode(',', $internal_value_cost_percent); //internal
					$non_billable_value = implode(',', $non_billable_value_cost_percent); //non-billable
				}
				if(($graph_based == 'directcost') && ($value_based == 'value')){
					$billable_value 	= implode(',', $billable_value_directcost); //billable
					$internal_value 	= implode(',', $internal_value_directcost); //internal
					$non_billable_value = implode(',', $non_billable_value_directcost); //non-billable
				}
				if(($graph_based == 'directcost') && ($value_based == 'percent')){
					$billable_value 	= implode(',', $billable_value_directcost_percent); //billable
					$internal_value 	= implode(',', $internal_value_directcost_percent); //internal
					$non_billable_value = implode(',', $non_billable_value_directcost_percent); //non-billable
				}
				
				$month_no_arr      = implode(',', $month_no_arr);
				$month_name_arr    = implode(',', $month_name_arr);
			?>
			
			<div id="default_view">
				<div id='' class='dash-section-full'>
					<h5 class="trend_analysis_bar"><span class="forecast-heading">Trend Analysis</span></h5>
					<div id="trend_analysis_chart" class="plot"></div>	
				</div>
				<!--div id="trend_analysis_chart_img"><button type="button">PDF</button></div-->
				<div class="clearfix"></div>
				<div id="drilldown_data" class="" style="margin: 10px 0px 0px; display:none; width:auto;"></div>
			</div>
			
        <?php 
		} else {
			echo "You have no rights to access this page";
		} 
		?>
	</div>
</div>
<script type="text/javascript">
	var x_axis_values  	   = [<?php echo $x_axis_values ?>];
	var billable_value 	   = [<?php echo $billable_value ?>];
	var internal_value     = [<?php echo $internal_value ?>];
	var non_billable_value = [<?php echo $non_billable_value ?>];
	var start_date		   = "<?php echo $start_date ?>";
	var end_date	       = "<?php echo $end_date ?>";
	var month_no_arr  	   = [<?php echo $month_no_arr ?>];
	var month_name_arr     = [<?php echo $month_name_arr ?>];
	var currency_name  	   = ['<?php echo $default_currency ?>'];
	var value_based		   = ['<?php echo $value_based ?>'];			
	var graph_based		   = ['<?php echo $graph_based ?>'];			
</script>
<script type="text/javascript">
var filter_area_status = '<?php echo $filter_area_status; ?>';
if(filter_area_status==1){ $('#advance_search').show(); }
$(function() {
	/*Date Picker*/
	$( "#month_year_from_date, #month_year_to_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		maxDate: 0,
		showButtonPanel: true,	
		onClose: function(dateText, inst) {
			var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
			$(this).datepicker('setDate', new Date(year, month, 1));
		},
		beforeShow : function(input, inst) {
			$("#filter_area_status").val('1');
			if ((datestr = $(this).val()).length > 0) {
				year = datestr.substring(datestr.length-4, datestr.length);
				month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
				$(this).datepicker('setDate', new Date(year, month, 1));    
			}
				var other  = this.id  == "month_year_from_date" ? "#month_year_to_date" : "#month_year_from_date";
				var option = this.id == "month_year_from_date" ? "maxDate" : "minDate";        
			if ((selectedDate = $(other).val()).length > 0) {
				year = selectedDate.substring(selectedDate.length-4, selectedDate.length);
				month = jQuery.inArray(selectedDate.substring(0, selectedDate.length-5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker( "option", option, new Date(year, month, 1));
			}
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
	
	$('#exclude_leave, #exclude_holiday').click(function() {
        $("#filter_area_status").val('1');
    });
});	
$(document).ready(function(){
	
	$("#department_ids").change(function(){
		var ids = $(this).val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var params = {'dept_ids':ids,'start_date':start_date,'end_date':end_date};
		params[csrf_token_name] = csrf_hash_token;
		$("#filter_area_status").val('1');
		$('#practice_ids').html('');
		$('#skill_ids').html('');
		$('#member_ids').html('');
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
				//skill
				$('#skill_ids').html('');
				$('#member_ids').html('');
				$.ajax({
					type: 'POST',
					url: site_base_url+'projects/dashboard/get_skills',
					data: params,
					success: function(data) {
						if(data){
							var skills = $.parseJSON(data);
							if(skills.length){
								var html='';
								for(var i=0;i<skills.length;i++){
									if(skills[i].name=='null' || skills[i].skill_id==0) skills[i].name = 'N/A';
										html +='<option value="'+skills[i].skill_id+'">'+skills[i].name+'</option>';
								}
								$('#skill_ids').html('');
								$('#skill_ids').append(html)
								$.ajax({
									type: 'POST',
									url: site_base_url+'projects/dashboard/get_members',
									data: params,
									success: function(members) {
										if(members){
											var mem_html='';
											var users = $.parseJSON(members);
											if(users.length){
												for(var i=0;i<users.length;i++){
													mem_html +='<option value="'+users[i].username+'">'+users[i].emp_name+'</option>';
												}	
											}
											$('#member_ids').html('');
											$('#member_ids').append(mem_html)								
										}
									}
								});
							}
						}
					}
				});
			}
		});
		return false;		
	});
	
	//on change for practice id
	$("#practice_ids").change(function(){
		var ids  = $(this).val();
		var d_ids = $('#department_ids').val();
		var start_date = $('#month_year_from_date').val();
		var end_date   = $('#month_year_to_date').val();
		var params = {'dept_ids':d_ids,'prac_id':ids,'start_date':start_date,'end_date':end_date};
		$("#filter_area_status").val('1');
		$('#skill_ids').html('');
		$('#member_ids').html('');
		params[csrf_token_name] = csrf_hash_token;
		$.ajax({
			type: 'POST',
			url: site_base_url+'projects/dashboard/get_skills_by_practice',
			data: params,
			success: function(pdata) {
				if(pdata){
					var skills = $.parseJSON(pdata);
					if(skills.length){
						var html='';
						for(var i=0;i<skills.length;i++){
							if(skills[i].name=='null' || skills[i].skill_id==0) skills[i].name = 'N/A';
								html +='<option value="'+skills[i].skill_id+'">'+skills[i].name+'</option>';
						}
						$('#skill_ids').html('');
						$('#skill_ids').append(html)
						$.ajax({
							type: 'POST',
							url: site_base_url+'projects/dashboard/get_practice_members',
							data: params,
							success: function(members) {
								if(members){
									var mem_html='';
									var users = $.parseJSON(members);
									if(users.length){
										for(var i=0;i<users.length;i++){
											mem_html +='<option value="'+users[i].username+'">'+users[i].emp_name+'</option>';
										}	
									}
									$('#member_ids').html('');
									$('#member_ids').append(mem_html)								
								}
							}
						});
					}
				}
			}
		});
		return false;		
	});
	
	
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
	
});
	$('body').on('click','#filter_reset',function(){
		$('#exclude_leave,#exclude_holiday').removeAttr('checked'); // Unchecks it
		$("#department_ids,#practice_ids,#skill_ids,#member_ids").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
	});
function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}
$('#trend_analysis_grid_close').click(function() {
	$('#trend_analysis_info_export').slideUp('fast', function(){ 
		$('#trend_analysis_info').css('display','none');
	});
})
</script>
<script type="text/javascript" src="assets/js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.logAxisRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="assets/js/projects/trend_analysis_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

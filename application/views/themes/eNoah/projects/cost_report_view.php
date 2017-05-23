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
        <?php if($this->session->userdata('viewPjt')==1) { ?>
			<div class="page-title-head">
				<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
				<a class="choice-box" onclick="advanced_filter();" >
					<img src="assets/img/advanced_filter.png" class="icon leads" />
					<span>Advanced Filters</span>
				</a>
				
				<div class="section-right">
					<!--<div class="buttons add-new-button">
						<button id='expand_tr' class="positive" type="button">
							Expand
						</button>
					</div>
					<div class="buttons collapse-button">
						<button id='collapse_tr' class="positive" type="button">
							Collapse
						</button>
					</div>-->
					<div class="buttons export-to-excel">
						<button type="button" class="positive" id="btnExport">
							Export to Excel
						</button>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="buttons">
					<form name="fliter_data" id="fliter_data" method="post">
					<!--button  type="submit" id="excel-1" class="positive">
						Export to Excel
					</button-->
					<input type="hidden" name="exclude_leave" value="" id="hexclude_leave" />
					<input type="hidden" name="exclude_holiday" value="" id="hexclude_holiday" />
					<input type="hidden" name="month_year_from_date" value="" id="hmonth_year" />
					<input type="hidden" name="month_year_to_date" value="" id="hmonth_to_year" />
					<input type="hidden" name="department_ids" value="" id="hdept_ids" />
					<input type="hidden" name="practice_ids" value="" id="hprac_ids" />
					<input type="hidden" name="entity_ids" value="" id="henty_ids" />
					<input type="hidden" name="skill_ids" value="" id="hskill_ids" />
					<input type="hidden" name="member_ids" value="" id="hmember_ids" />
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					</form>
				</div>
				<div class="clearfix"></div>
			</div>
		
			<div id="filter_section">
				<div class="clear"></div>
				
				<div id="advance_search" style="padding-bottom:15px; display:none;">
				<form action="<?php echo site_url('projects/dashboard/cost_report')?>" name="project_dashboard" id="project_dashboard" method="post">					
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
											<?php if(count($practice_ids)>0 && !empty($practice_ids)) { ?>
													<?php foreach($practice_ids as $prac) {?>
														<option <?php echo in_array($prac->id, $sel_practice_ids)?'selected="selected"':'';?> value="<?php echo $prac->id;?>"><?php echo $prac->practices;?></option>
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
			
			<div id="ajax_loader" style="margin:10px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>
		
			<div id="default_view">
				<?php echo $this->load->view('projects/cost_report_grid', $res_data, true); ?>
			</div>
			<div class="clearfix"></div>
			<div id="drilldown_data" class="" style="margin:10px 0;display:none;">
			
			</div>
        <?php 
		} else {
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>

<script type="text/javascript">
var cur_mon = '<?php echo date('F Y') ?>';
var filter_area_status = '<?php echo $filter_area_status; ?>';
if(filter_area_status==1) {
	$('#advance_search').show();
}
$(function() {
	/*Date Picker*/
	$( "#month_year_from_date, #month_year_to_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		maxDate: 0,
		showButtonPanel: true,	
		onClose: function(dateText, inst) {
			var month 	= $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var year 	= $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
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
	
	$('#entity_ids').change(function(){
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
	
	$("input[name=exclude_leave]").prop("checked",true);
	$("input[name=exclude_holiday]").prop("checked",true);
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
	if($('#entity_ids').val() == null) {
		$('#henty_ids').val('');
	} else {
		$('#henty_ids').val($('#entity_ids').val());
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
		url: site_base_url+'projects/dashboard/get_data_beta/',                                                              
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
function advanced_filter() {
	$('#advance_search').slideToggle('slow');
}
$('#filter_reset').click(function() {
	 $("#project_dashboard").find('input:checkbox').removeAttr('checked').removeAttr('selected');
	 $("#practice_ids").html('');
	 $("#skill_ids").html('');
	 $("#member_ids").html('');
	 $('select#entity_ids option').removeAttr("selected");
	 $('select#department_ids option').removeAttr("selected");
});
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

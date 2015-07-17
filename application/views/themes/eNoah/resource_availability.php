<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar {
    display: none;
    }

</style>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<script>
$(function(){
	var config = {
		'.chzn-select'           : {},
		'.chzn-select-deselect'  : {allow_single_deselect:true},
		'.chzn-select-no-single' : {disable_search_threshold:10},
		'.chzn-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chzn-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
	
	
});  
</script>
<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			<div class="buttons export-to-excel">
				<form onsubmit="return updateFields()" action="<?php echo base_url().'report/resource_availability/excelExport/'?>" name="resource_availability_excel" id="resource_availability_excel"  method="post">
				<button  type="submit" id="excel-1" class="positive">
					Export to Excel
				</button>
				<input type="hidden" name="month_year_from_date" value="" id="excel_date" />
				<input type="hidden" name="department_ids[]" value="" id="excel_departments" />
				<input type="hidden" name="skill_ids[]" value="" id="excel_skills" />
				<input type="hidden" name="member_ids[]" value="" id="excel_members" />
				<input type="hidden" name="resource_type_selection" value="" id="excel_resource_type_selection" />
				<input type="hidden" name="check_condition" value="" id="excel_check_condition" />
				<input type="hidden" name="percentage" value="" id="excel_percentage" />
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				</form>
			</div>
			<div class="clearfix"></div>
		</div>

        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
 
		<div id="advance_filters" style="float:left;width:100%;" >
		
				<form action="<?php echo site_url('report/resource_availability')?>" name="resource_availability" id="resource_availability"  method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div class="filterGrid-area">
						
                            
							<div class="selectOPtshowFilter">
                            	<span> Filter by Type: </span>
								<select id="resource_type_selection" name="resource_type_selection"	>
									<option  value="">All</option>
									<option <?php if($resource_type_selection=='billable_percentage'){ echo 'selected="selected"';}?> value="billable_percentage">Billable</option>
									<option <?php if($resource_type_selection=='non_billable_percentage'){ echo 'selected="selected"';}?>  value="non_billable_percentage">Non Billable</option>
								</select>	
							</div>
							<div class="selectOPtshowFilter1">
								<select id="check_condition" name="check_condition"	>
									<option value="">All</option>
									<option <?php if($check_condition=='greater_than_equal'){ echo 'selected="selected"';}?> value="greater_than_equal">(>=)</option>
									<option <?php if($check_condition=='greater_than'){ echo 'selected="selected"';}?> value="greater_than">(>)</option>
									<option <?php if($check_condition=='less_than_equal'){ echo 'selected="selected"';}?> value="less_than">(<=)</option>
									<option <?php if($check_condition=='less_than'){ echo 'selected="selected"';}?> value="less_than">(<)</option>
									<option <?php if($check_condition=='equal'){ echo 'selected="selected"';}?> value="equal">(=)</option>
								</select>	
							</div>

							<div class="selectOPtshowFilter2">
                            <span>(%)</span>
								<input type="text" id="percentage" maxlength="5" name="percentage" value="<?php echo (!empty($percentage))?(float)$percentage:'';;?>" class="selefilterText" />
                            </div>	
					
						<div class="filterrow-areaYear">
							<span>Month/Year: </span>
							<div class="filtemonYear"><input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($date_filter));?>" /> </div>						
						</div>
						<div class="selectOPtshowFilter2">
                            <span>Width Project</span>
								<input type="checkbox"  />
                            </div>	
						<div class="filterrow-areaD">
							<span> Department: </span>
							<div class="selectOPt"><select class="chzn-select" id="department_ids" name="department_ids[]"	multiple="multiple">
									<?php if(count($departments)>0 && !empty($departments)){?>
											<?php foreach($departments as $depts){?>
												<option <?php echo in_array($depts->department_id,$department_ids)?'selected="selected"':'';?> value="<?php echo $depts->department_id;?>"><?php echo $depts->department_name;?></option>
									<?php } }?>
								</select></div>						
						</div>
						 
						<div class="filterrow-area" id="skill_show_id">
							<span>Select Skill(s): </span>
							<div class="selectOPtshow">
								<select  class="chzn-select" id="skill_ids"  name="skill_ids[]"	multiple="multiple">
								<?php if(count($skill_ids_selected)>0 && !empty($skill_ids_selected)){?>
								<?php 
									foreach($skill_ids_selected as $skills){
										$skills->name = ($skills->skill_id==0)?'N/A':$skills->name;
										//$skills->skill_id = ($skills->skill_id==0)?'N/A':$skills->skill_id;
										?>
										<option <?php echo in_array($skills->skill_id,$skill_ids)?'selected="selected"':'';?> value="<?php echo $skills->skill_id;?>"><?php echo $skills->name;?></option>
								<?php } }?>
								</select>
							</div>						
						</div>
						<div class="filterrow-area" id="member_show_id">
							<span>Select Member(s): </span>
							<div class="selectOPtshow1">
								<select class="chzn-select" id="member_ids" name="member_ids[]"	multiple="multiple">
								<?php if(count($member_ids_selected)>0 && !empty($member_ids_selected)){?>
								<?php foreach($member_ids_selected as $members){?>
										<option <?php echo in_array($members->username,$member_ids)?'selected="selected"':'';?>  value="<?php echo $members->username;?>"><?php echo $members->emp_name;?></option>
								<?php } }?>								
								</select>	
							</div>						
						</div>
						
						<div class="filterrow-area-btn bttn-area">
							<div class="bttons">
								<input style="height:auto;" type="submit" class="positive input-font" name="advance_pjt" id="advance" value="Go" />
								<input style="height:auto;" type="button" class="positive input-font" name="advance_pjt" id="reset" value="Reset" onclick="window.location.href='<?php echo base_url().'report/resource_availability'?>'" />
							</div>								
						</div>
					</div>
					<input type="hidden" id="start_date" name="start_date" value="<?php echo $start_date;?>" />
					<input type="hidden" id="end_date" name="end_date" value="<?php echo $end_date;?>" />
				</form>
				<br />
			</div>	
			<div class="clearfix"></div>
			<div id="ajax_loader" style="margin:20px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>	
			<div id="default_view">
				    <div id="treeGrid"></div>
				 <?php 
					//echo '<pre>';print_r($departments);
					$master = array();
					$skill_arr = array();
					$user_arr = array();
					$project_arr = array();
					$json ='';
					 if(count($results)>0 && !empty($results)){
						 foreach($results as $department_name => $depts){
							$total_availability = $depts['summation_department_based_available_hours'];
							$total_billable_hrs = $depts['departmentwise']['Billable'];
							$total_non_billable_hrs = $depts['departmentwise']['Non-Billable'];
							$dept_skill_count = count($depts['skillwise']);
							$dept_member_count = count($depts['department_based_available_hours']);
							 
							$billable_percentage = (($total_billable_hrs/$total_availability)*100);
							$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
							
							$dept_slug = str_replace(" ","",$department_name);
							
							$master[$department_name]['department_id'] = 'dept'.$dept_slug;
							$master[$department_name]['name'] = $department_name." ($dept_skill_count) ($dept_member_count)";
							$master[$department_name]['availability'] = number_format($total_availability,2);
							$master[$department_name]['billable'] = number_format($total_billable_hrs,2);
							$master[$department_name]['non_billable'] = number_format($total_non_billable_hrs,2);
							$master[$department_name]['billable_percentage'] = number_format($billable_percentage,2).'%';
							$master[$department_name]['non_billable_percentage'] = number_format($non_billable_percentage,2).'%';
							$master[$department_name]['ReportsTo'] = 'null';
							$master[$department_name]['expandRow'] = 'true';
							$json .= json_encode($master[$department_name]).',';								
						

							//echo $department_name.'--'.count($depts['skillwise']);
							
							if(!empty($depts['skillwise']) && count($depts['skillwise'])>0)
							{
								foreach($depts['skillwise'] as $skill_name => $skill){
									//echo "<pre>";print_r($skill);
									$skill_slug = str_replace(" ","",$skill_name);
									
									$total_availability = $depts[$skill_name]['summation_skill_based_available_hours'];
									$total_billable_hrs = $skill['Billable'];
									$total_non_billable_hrs = $skill['Non-Billable'];
									
									$dept_member_count = count($depts['skill_based_available_hours'][$skill_name]);
									
									$billable_percentage = (($total_billable_hrs/$total_availability)*100);
									$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
									
									$skill_arr[$skill_name]['department_id'] = 'skill'.$dept_slug.$skill_slug;
									$skill_arr[$skill_name]['name'] = $skill_name." ($dept_member_count)";
									$skill_arr[$skill_name]['availability'] = number_format($total_availability,2);
									$skill_arr[$skill_name]['billable'] = number_format($total_billable_hrs,2);
									$skill_arr[$skill_name]['non_billable'] = number_format($total_non_billable_hrs,2);
									$skill_arr[$skill_name]['billable_percentage'] = number_format($billable_percentage,2).'%';
									$skill_arr[$skill_name]['non_billable_percentage'] = number_format($non_billable_percentage,2).'%';
									$skill_arr[$skill_name]['ReportsTo'] = 'dept'.$dept_slug;
									$skill_arr[$skill_name]['expandRow'] = 'true';
									$json .= json_encode($skill_arr[$skill_name]).',';
								}
							}
							 
							if(count($depts['userwise'])>0 && !empty($depts['userwise']))
							{
								//sort($depts['userwise']);
								foreach($depts['userwise'] as $skill_name => $user){
									$skill_slug = str_replace(" ","",$skill_name);
									foreach($user as $un => $u){
										$total_availability = $depts['department_based_available_hours'][$un];
										 
										$total_billable_hrs = $u['Billable'];
										$total_non_billable_hrs = $u['Non-Billable'];
										
										$billable_percentage = (($total_billable_hrs/$total_availability)*100);
										$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
										
										$user_arr[$un]['department_id'] = 'un'.$un;
										$user_arr[$un]['name'] = $u[$un];
										$user_arr[$un]['availability'] = number_format($total_availability,2);
										$user_arr[$un]['billable'] = number_format($total_billable_hrs,2);
										$user_arr[$un]['non_billable'] = number_format($total_non_billable_hrs,2);
										$user_arr[$un]['billable_percentage'] = number_format($billable_percentage,2).'%';
										$user_arr[$un]['non_billable_percentage'] = number_format($non_billable_percentage,2).'%';
										//$user_arr[$un]['ReportsTo'] = 'skill'.$dept_slug.$skill_slug;
										$user_arr[$un]['ReportsTo'] = 'skill'.$dept_slug.$skill_slug;
										$user_arr[$un]['expandRow'] = 'true';
										$json .= json_encode($user_arr[$un]).',';
									}
								}
							}
							
							/*
							 $i=0;
							foreach($depts['projectwise'] as $un => $project){
								foreach($project as $key => $proj){
									
									$billable = $depts['projuser'][$un][$proj]['Billable'];
									$nonbillable = $depts['projuser'][$un][$proj]['Non-Billable'];
									
									$billable = ($billable!='')?number_format($billable,2):'0.00';
									$nonbillable = ($nonbillable!='')?number_format($nonbillable,2):'0.00';
									
									$project_arr[$un]['department_id'] = 'project'.$un.$proj;
									$project_arr[$un]['name'] = $proj;
									$project_arr[$un]['availability'] = 'NA';
									$project_arr[$un]['billable'] = $billable;
									$project_arr[$un]['non_billable'] = $nonbillable;
									$project_arr[$un]['billable_percentage'] = '-';
									$project_arr[$un]['non_billable_percentage'] = '-';
									$project_arr[$un]['ReportsTo'] =  'un'.$un;
									$project_arr[$un]['expandRow'] = 'true';
									$json .= json_encode($project_arr[$un]).',';							
								}
							} */
						} 
					}
					?>
				 
					 <script type="text/javascript">
						$(document).ready(function () {
						var employees = <?php echo '['.$json.']';?>
							
							// prepare the data
							var source =
							{
								dataType: "json",
								dataFields: [
									{ name: 'department_id', type: 'number' },
									{ name: 'ReportsTo', type: 'number' },
									{ name: 'name', type: 'string' },
									{ name: 'availability', type: 'string' },
									{ name: 'billable', type: 'string' },
									{ name: 'non_billable', type: 'string' },
									{ name: 'billable_percentage', type: 'string' },
									{ name: 'non_billable_percentage', type: 'string' }
									
								],
								timeout: 1000000,
								hierarchy:
								{
									keyDataField: { name: 'department_id' },
									parentDataField: { name: 'ReportsTo' }
								},
								id: 'department_id',
								localData: employees
							};

							// create Tree Grid
							 var dataAdapter = new $.jqx.dataAdapter(source);
           $("#treeGrid").jqxTreeGrid(
            {
                width: 1125,
                source: dataAdapter,
                sortable: true,
                ready: function()
                {
                    $("#treeGrid").jqxTreeGrid('expandRow', '2');
                },
				columns: [
				  { text: 'Department/Skill/Members',dataField: 'name', width: 450 },
				  { text: 'Available Hours',  dataField: 'availability', width: 125 },
				  { text: 'Billable Hours', dataField: 'billable', width: 125 },
				  { text: 'Non Billable Hours', dataField: 'non_billable',width:135 },
				  { text: 'Billable Hours (%)', dataField: 'billable_percentage', width: 135 },
				  { text: 'Non Billable Hours (%)', dataField: 'non_billable_percentage', width: 155 }
				],
                columnGroups: [
                  { text: 'Name', name: 'Name' }
                ]
            });
						});
$(function() {
    $('#month_year_from_date').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
});					
						
					</script>					
			</div>
        <?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<link rel="stylesheet" href="assets/js/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="assets/js/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="assets/js/jqwidgets/jqxdata.js"></script> 
<script type="text/javascript" src="assets/js/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="assets/js/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="assets/js/jqwidgets/jqxdatatable.js"></script> 
<script type="text/javascript" src="assets/js/jqwidgets/jqxtreegrid.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
	$("#department_ids").change(function(){
		var ids = $(this).val();
		var params = {'dept_ids':ids,'start_date':$('#start_date').val(),'end_date':$('#end_date').val()};
		params[csrf_token_name] = csrf_hash_token;			
		$('#skill_show_id').css('display','none');		
		$('#member_show_id').css('display','none');		
		$.ajax({
			type: 'POST',
			url: site_base_url+'report/resource_availability/get_skills',
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
						$('#skill_show_id').css('display','block');
						$('#skill_ids').html('');
						$('#skill_ids').append(html)
						$("#skill_ids").trigger("liszt:updated");
						$.ajax({
							type: 'POST',
							url: site_base_url+'report/resource_availability/get_members',
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
									$('#member_show_id').css('display','block');
									$('#member_ids').html('');
									$('#member_ids').append(mem_html)
									$("#member_ids").trigger("liszt:updated");									
								}
							}
						});
					}
				}
			}
		});
		return false;		
	})
	
	$('body').on('change','#skill_ids',function(){
		var dids = $('#department_ids').val();
		var sids = $(this).val();
		
		var params = {'dept_ids':dids,'skill_ids':sids,'start_date':$('#start_date').val(),'end_date':$('#end_date').val()};
		params[csrf_token_name] = csrf_hash_token;
		
		$.ajax({
			type: 'POST',
			url: site_base_url+'report/resource_availability/get_skill_members',
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
					$('#member_show_id').css('display','block');
					$('#member_ids').html('');
					$('#member_ids').append(mem_html);
					$("#member_ids").trigger("liszt:updated");
				}
			}
		});
	});
	 $("#skill_ids").chosen({no_results_text: "Please select Department"}); 
	 $("#member_ids").chosen({no_results_text: "Please select Skill"}); 
	 <?php if(count($department_ids)<=0){?>
		$("#skill_show_id").css("display","none")
	 <?php } ?>
	 <?php if(count($skill_ids)<=0){?>
		$("#member_show_id").css("display","none")
	 <?php } ?>
});

function updateFields(){
	$('#excel_date').val($('#month_year_from_date').val())
	$('#excel_departments').val($('#department_ids').val())
	$('#excel_skills').val($('#skill_ids').val())
	$('#excel_members').val($('#member_ids').val())
	
	$('#excel_percentage').val($('#percentage').val())
	$('#excel_resource_type_selection').val($('#resource_type_selection').val())
	$('#excel_check_condition').val($('#check_condition').val())
	
	$("#resource_availability_excel").submit();
	return true;
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

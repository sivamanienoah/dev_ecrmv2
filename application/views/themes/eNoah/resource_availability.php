<?php require (theme_url().'/tpl/header.php'); ?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
.ui-datepicker-calendar {
    display: none;
    }

</style>
<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			<!--<a class="choice-box js_advanced_filter">
				<span>Advanced Filters</span><img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>-->
			<div class="clearfix"></div>
		</div>

        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
 
		<div id="advance_filters" style="float:left;width:100%;" >
		
				<form action="<?php echo site_url('resource_availability')?>" name="resource_availability" id="resource_availability"  method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<table cellspacing="0" cellpadding="0" border="0" class="search-table">
						<tbody><tr>
							<td>
								Search by Month/Year:
							</td>
							<td>
								<input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($date_filter));?>" style="width:78px;" />
							</td>
							<td>
								<div class="buttons">
									<input style="height:auto;" type="submit" class="positive input-font" name="advance_pjt" id="advance" value="Search" />
									 
								</div>
							</td>
							</tr>
					</tbody>
					</table>				
					 				
				 
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
					$master = array();
					$skill_arr = array();
					$user_arr = array();
					$project_arr = array();
					$json ='';
					//echo '<pre>';print_r($departments);exit;
					 foreach($departments as $department_name => $depts){
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
						
						
						foreach($depts['userwise'] as $skill_name => $user){
							$skill_slug = str_replace(" ","",$skill_name);
							foreach($user as $un => $u){
								$total_availability = $depts['department_based_available_hours'][$un];
								 
								$total_billable_hrs = $u['Billable'];
								$total_non_billable_hrs = $u['Non-Billable'];
								
								$billable_percentage = (($total_billable_hrs/$total_availability)*100);
								$non_billable_percentage = (($total_non_billable_hrs/$total_availability)*100);
								
								$user_arr[$un]['department_id'] = 'un'.$un;
								$user_arr[$un]['name'] = $un;
								$user_arr[$un]['availability'] = number_format($total_availability,2);
								$user_arr[$un]['billable'] = number_format($total_billable_hrs,2);
								$user_arr[$un]['non_billable'] = number_format($total_non_billable_hrs,2);
								$user_arr[$un]['billable_percentage'] = number_format($billable_percentage,2).'%';
								$user_arr[$un]['non_billable_percentage'] = number_format($non_billable_percentage,2).'%';
								$user_arr[$un]['ReportsTo'] = 'skill'.$dept_slug.$skill_slug;
								$user_arr[$un]['expandRow'] = 'true';
								$json .= json_encode($user_arr[$un]).',';
							}
						}
						foreach($depts['projectwise'] as $un => $project){
							$unique[$un] = array_unique($project);
						}
						$i=0;
						foreach($unique as $un => $project){
							foreach($project as $key => $proj){
								$project_arr[$un]['department_id'] = 'project'.$un.$proj;
								$project_arr[$un]['name'] = $proj;
								$project_arr[$un]['availability'] = 'NA';
								$project_arr[$un]['billable'] = 'NA';
								$project_arr[$un]['non_billable'] = 'NA';
								$project_arr[$un]['billable_percentage'] = 'NA';
								$project_arr[$un]['non_billable_percentage'] = 'NA';
								$project_arr[$un]['ReportsTo'] =  'un'.$un;
								$project_arr[$un]['expandRow'] = 'true';
								$json .= json_encode($project_arr[$un]).',';							
							}
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
				  { text: 'Department/Skill/Members',dataField: 'name', width: 350 },
				  { text: 'Available Hours',  dataField: 'availability', width: 150 },
				  { text: 'Billable Hours', dataField: 'billable', width: 155 },
				  { text: 'Non Billable Hours', dataField: 'non_billable',width:150 },
				  { text: 'Billable Hours (%)', dataField: 'billable_percentage', width: 155 },
				  { text: 'Non Billable Hours (%)', dataField: 'non_billable_percentage', width: 163 }
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
<!--
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="assets/js/grid.locale-en.js"></script>
-->
<?php require (theme_url().'/tpl/footer.php'); ?>

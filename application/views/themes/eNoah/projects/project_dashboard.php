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
        <?php if($this->session->userdata('viewPjt')==1) { ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			<div class="buttons">
				<form name="fliter_data" id="fliter_data" method="post">
				<!--button  type="submit" id="excel-1" class="positive">
					Export to Excel
				</button-->
				<input type="hidden" name="month_year_from_date" value="" id="hmonth_year" />
				<input type="hidden" name="department_ids" value="" id="hdept_ids" />
				<input type="hidden" name="practice_ids" value="" id="hprac_ids" />
				<input type="hidden" name="skill_ids" value="" id="hskill_ids" />
				<input type="hidden" name="member_ids" value="" id="hmember_ids" />
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				</form>
			</div>
			<div class="clearfix"></div>
		</div>

        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
 
		<div id="advance_filters" style="float:left;width:100%;" >
		
				<form action="<?php echo site_url('projects/dashboard')?>" name="project_dashboard" id="project_dashboard" method="post">
				
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<div class="filterGrid-area">
						<div class="profilterrow-areaYear">
							<span>Month/Year: </span>
							<div class="filtemonYear"><input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" value="<?php echo date('F Y',strtotime($date_filter));?>" /> </div>
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
						<div class="filterrow-areaP" id="practice_show_id">
							<span> Practice: </span>
							<div class="selectOPt"><select class="chzn-select" id="practice_ids" name="practice_ids[]" multiple="multiple">
									<?php if(count($practice_ids_selected)>0 && !empty($practice_ids_selected)){?>
											<?php foreach($practice_ids_selected as $prac){?>
												<option <?php echo in_array($prac->practice_id, $practice_ids)?'selected="selected"':'';?> value="<?php echo $prac->practice_id;?>"><?php echo $prac->practice_name;?></option>
									<?php } }?>
								</select></div>						
						</div>
						<div class="filterrow-area" id="skill_show_id">
							<span>Select Skill(s): </span>
							<div class="selectOPtshow">
								<select class="chzn-select" id="skill_ids"  name="skill_ids[]"	multiple="multiple">
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
				<?php 
					$master      = array();
					$user_arr    = array();
					$project_arr = array();
					$bu_arr      = array();
					$dept_arr    = array();
					$prac_arr    = array();
					$skil_arr    = array();
					$usercnt     = array();
					$deptusercnt = array();
					$bu_arr['totalhour'] = 0;
					$bu_arr['totalhead'] = 0;
					$bu_arr['totalcost'] = 0;
					
					if(!empty($resdata)) {
						foreach($resdata as $row){
							// for business unit based
							if (!in_array($row->username, $usercnt[$row->resoursetype])) {
								$usercnt[$row->resoursetype][] = $row->username;
								$bu_arr['totalhead'] = $bu_arr['totalhead'] + 1;
								if (isset($bu_arr['it'][$row->resoursetype]['headcount'])) {
									$bu_arr['it'][$row->resoursetype]['headcount'] = $bu_arr['it'][$row->resoursetype]['headcount'] + 1;
								} else {
									$bu_arr['it'][$row->resoursetype]['headcount'] = 1;
								}
							}
							if (isset($bu_arr['it'][$row->resoursetype]['hour'])) {
								$bu_arr['it'][$row->resoursetype]['hour'] = $row->duration_hours + $bu_arr['it'][$row->resoursetype]['hour'];
								$bu_arr['it'][$row->resoursetype]['cost'] = $row->resource_duration_cost + $bu_arr['it'][$row->resoursetype]['cost'];
							} else {
								$bu_arr['it'][$row->resoursetype]['hour'] = $row->duration_hours;
								$bu_arr['it'][$row->resoursetype]['cost'] = $row->resource_duration_cost;
							}
							$bu_arr['totalhour'] = $bu_arr['totalhour'] + $row->duration_hours;
							$bu_arr['totalcost'] = $bu_arr['totalcost'] + $row->resource_duration_cost;
							//for dept based
							if (!in_array($row->username, $deptusercnt[$row->dept_name][$row->resoursetype])) {
								$deptusercnt[$row->dept_name][$row->resoursetype][] = $row->username;
								$dept_arr[$row->dept_name]['totalhead'] = $dept_arr[$row->dept_name]['totalhead'] + 1;
								if (isset($dept_arr['dept'][$row->dept_name][$row->resoursetype]['headcount'])) {
									$dept_arr['dept'][$row->dept_name][$row->resoursetype]['headcount'] = $dept_arr['dept'][$row->dept_name][$row->resoursetype]['headcount'] + 1;
								} else {
									$dept_arr['dept'][$row->dept_name][$row->resoursetype]['headcount'] = 1;
								}
							}
							if (isset($dept_arr['dept'][$row->dept_name][$row->resoursetype]['hour'])) {
								$dept_arr['dept'][$row->dept_name][$row->resoursetype]['hour'] = $row->duration_hours + $dept_arr['dept'][$row->dept_name][$row->resoursetype]['hour'];
								$dept_arr['dept'][$row->dept_name][$row->resoursetype]['cost'] = $row->resource_duration_cost + $dept_arr['dept'][$row->dept_name][$row->resoursetype]['cost'];
							} else {
								$dept_arr['dept'][$row->dept_name][$row->resoursetype]['hour'] = $row->duration_hours;
								$dept_arr['dept'][$row->dept_name][$row->resoursetype]['cost'] = $row->resource_duration_cost;
							}
							$dept_arr[$row->dept_name]['totalhour'] = $dept_arr[$row->dept_name]['totalhour'] + $row->duration_hours;
							$dept_arr[$row->dept_name]['totalcost'] = $dept_arr[$row->dept_name]['totalcost'] + $row->resource_duration_cost;
							//for dept based
						}
					}
				?>	
			<div id="default_view">
				<h4>IT</h4>
				<table cellspacing="0" cellpadding="0" border="0" class="data-table bu-tbl">
					<tr>
						<thead>
							<th>Billablity</th>
							<th>Hours</th>
							<th># Head Count</th>
							<th>Total Cost</th>
							<th>% of Hours</th>
							<th>% of Cost</th>
						</thead>
					</tr>
					<?php
						$total_hour   = 0;
						$percent_hour = 0;
						$percent_cost = 0;
						foreach($bu_arr as $bkey=>$bval) {
							ksort($bval);
							foreach($bval as $rt=>$rtval){
					?>
								<tr>
									<!--td><a onclick="getData(<?php #echo "'".$rt."'"; ?>,'1');return false;"><?php #echo $rt; ?></a></td-->
									<td><?= $rt; ?></td>
									<td align="right"><?= round($rtval['hour'],2); ?></td>
									<td align="right"><?= round($rtval['headcount'],2); ?></td>
									<td align="right"><?= round($rtval['cost'],2); ?></td>
									<td align="right"><?php echo round(($rtval['hour']/$bu_arr['totalhour']) * 100, 2) . ' %'; ?></td>
									<td align="right"><?php echo round(($rtval['cost']/$bu_arr['totalcost']) * 100, 2) . ' %'; ?></td>
								</tr>
					<?php
							$percent_hour += ($rtval['hour']/$bu_arr['totalhour']) * 100;
							$percent_cost += ($rtval['cost']/$bu_arr['totalcost']) * 100;
							}
						}
					?>
							<tr>
							<td align="right"><b>Total:</b></td>
							<td align="right"><?= round($bu_arr['totalhour'],2); ?></td>
							<td align="right"></td>
							<td align="right"><?= round($bu_arr['totalcost'],2); ?></td>
							<td align="right"><?= round($percent_hour,2) . ' %'; ?></td>
							<td align="right"><?= round($percent_cost,2) . ' %'; ?></td>
							</tr>
				</table>
				<div class="dept_section">
				<div class="dept_sec_inner pull-left">
				<h4>EADS</h4>
				<?php #echo '<pre>'; print_r($bu_arr); ?>
				<table cellspacing="0" cellpadding="0" border="0" class="data-table bu-tbl-inr">
					<tr>
						<thead>
							<th>Billablity</th>
							<th>Hours</th>
							<th># Head Count</th>
							<th>Total Cost</th>
							<th>% of Hours</th>
							<th>% of Cost</th>
						</thead>
					</tr>
					<?php
						ksort($dept_arr['dept']['eADS']);
						foreach($dept_arr['dept']['eADS'] as $adskey=>$adsval) {
					?>
								<tr>
									<td><a onclick="getData(<?php echo "'".$adskey."'"; ?>,'2');return false;"><?= $adskey; ?></a></td>
									<td align="right"><?= round($adsval['hour'],2); ?></td>
									<td align="right"><?= round($adsval['headcount'],2); ?></td>
									<td align="right"><?= round($adsval['cost'],2); ?></td>
									<td align="right"><?php echo round(($adsval['hour']/$dept_arr['eADS']['totalhour']) * 100, 2) . ' %'; ?></td>
									<td align="right"><?php echo round(($adsval['cost']/$dept_arr['eADS']['totalcost']) * 100, 2) . ' %'; ?></td>
								</tr>
					<?php
							$percent_adshour += ($adsval['hour']/$dept_arr['eADS']['totalhour']) * 100;
							$percent_adscost += ($adsval['cost']/$dept_arr['eADS']['totalcost']) * 100;
							}
					?>
							<tr>
							<td align="right"><b>Total:</b></td>
							<td align="right"><?= round($dept_arr['eADS']['totalhour'],2); ?></td>
							<td align="right"></td>
							<td align="right"><?= round($dept_arr['eADS']['totalcost'],2); ?></td>
							<td align="right"><?= round($percent_adshour, 2) . ' %'; ?></td>
							<td align="right"><?= round($percent_adscost, 2) . ' %'; ?></td>
							</tr>
				</table>
				</div>
				<div class="dept_sec_inner pull-left">
				<h4>EQAD</h4>
				<?php #echo '<pre>'; print_r($bu_arr); ?>
				<table cellspacing="0" cellpadding="0" border="0" class="data-table bu-tbl-inr">
					<tr>
						<thead>
							<th>Billablity</th>
							<th>Hours</th>
							<th># Head Count</th>
							<th>Total Cost</th>
							<th>% of Hours</th>
							<th>% of Cost</th>
						</thead>
					</tr>
					<?php
						ksort($dept_arr['dept']['eQAD']);
						foreach($dept_arr['dept']['eQAD'] as $qadkey=>$qadval) {
					?>
								<tr>
									<td><a onclick="getData(<?php echo "'".$qadkey."'"; ?>,'3');return false;"><?= $qadkey; ?></a></td>
									<td align="right"><?= round($qadval['hour'],2); ?></td>
									<td align="right"><?= round($qadval['headcount'],2); ?></td>
									<td align="right"><?= round($qadval['cost'],2); ?></td>
									<td align="right"><?php echo round(($qadval['hour']/$dept_arr['eQAD']['totalhour']) * 100, 2) . ' %'; ?></td>
									<td align="right"><?php echo round(($qadval['cost']/$dept_arr['eQAD']['totalcost']) * 100, 2) . ' %'; ?></td>
								</tr>
					<?php
							$percent_qadhour += ($qadval['hour']/$dept_arr['eQAD']['totalhour']) * 100;
							$percent_qadcost += ($qadval['cost']/$dept_arr['eQAD']['totalcost']) * 100;
							}
					?>
							<tr>
							<td align="right"><b>Total:</b></td>
							<td align="right"><?= round($dept_arr['eQAD']['totalhour'],2); ?></td>
							<td align="right"></td>
							<td align="right"><?= round($dept_arr['eQAD']['totalcost'],2); ?></td>
							<td align="right"><?= round($percent_qadhour, 2) . ' %'; ?></td>
							<td align="right"><?= round($percent_qadcost, 2) . ' %'; ?></td>
							</tr>
				</table>
				</div>
				</div>
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
<script type="text/javascript">
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
$(document).ready(function(){
	
	$("#department_ids").change(function(){
		var ids = $(this).val();
		var params = {'dept_ids':ids,'start_date':$('#start_date').val(),'end_date':$('#end_date').val()};
		params[csrf_token_name] = csrf_hash_token;
		$('#skill_show_id').css('display','none');
		$('#member_show_id').css('display','none');
		$('#practice_show_id').css('display','none');
		
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
					$('#practice_show_id').css('display','block');
					$('#practice_ids').html('');
					$('#practice_ids').append(prac_html);
					$("#practice_ids").trigger("liszt:updated");									
				}
				//skill
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
								$('#skill_show_id').css('display','block');
								$('#skill_ids').html('');
								$('#skill_ids').append(html)
								$("#skill_ids").trigger("liszt:updated");
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
			}
		});
		return false;		
	});
	
	//on change for practice id
	$("#practice_ids").change(function(){
		var ids  = $(this).val();
		var d_ids = $('#department_ids').val();
		var params = {'dept_ids':d_ids,'prac_id':ids,'start_date':$('#start_date').val(),'end_date':$('#end_date').val()};
		params[csrf_token_name] = csrf_hash_token;
		$('#skill_show_id').css('display','none');
		$('#member_show_id').css('display','none');	
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
						$('#skill_show_id').css('display','block');
						$('#skill_ids').html('');
						$('#skill_ids').append(html)
						$("#skill_ids").trigger("liszt:updated");
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
	});
	
	
	$('body').on('change','#skill_ids',function(){
		var dids = $('#department_ids').val();
		var sids = $(this).val();
		
		var params = {'dept_ids':dids,'skill_ids':sids,'start_date':$('#start_date').val(),'end_date':$('#end_date').val()};
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
					$('#member_show_id').css('display','block');
					$('#member_ids').html('');
					$('#member_ids').append(mem_html);
					$("#member_ids").trigger("liszt:updated");
				}
			}
		});
	});
	$("#practice_ids").chosen({no_results_text: "Please select Practice"});
	$("#skill_ids").chosen({no_results_text: "Please select Department"}); 
	$("#member_ids").chosen({no_results_text: "Please select Skill"}); 
	<?php if(count($department_ids)<=0){?>
		$("#practice_show_id").css("display","none");
	<?php } ?>
	<?php if(count($practice_ids)<=0){?>
		$("#skill_show_id").css("display","none");
	<?php } ?>
	<?php if(count($skill_ids)<=0){?>
		$("#member_show_id").css("display","none")
	<?php } ?>
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
	$('#hskill_ids').val($('#skill_ids').val())
	$('#hmember_ids').val($('#member_ids').val())
	
	var formdata = $('#fliter_data').serialize();
	
	$.ajax({
		type: "POST",
		url: site_base_url+'projects/dashboard/get_data/',                                                              
		data: formdata+'&resource_type='+resource_type+'&dept_type='+dept_type+'&filter_group_by=0',
		cache: false,
		beforeSend:function() {
			$('#filter_group_by').prop('selectedIndex',0);
			$('#drildown_filter_area').hide();
			$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			$('#drilldown_data').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		},
		success: function(data) {
			$('#drilldown_data').html(data);
			$('#drilldown_data').show();
			$('#drildown_filter_area').show();
			$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
		}                                                                                   
	});
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

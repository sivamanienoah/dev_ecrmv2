<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
//echo baseurl();
?>

<script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript">var curr_job_id = 0;</script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<style type="text/css">
@import url(assets/css/tasks.css?q=1);

/* over-ride tasks */
td.task {
	width:557px;
}
.great-task-table, .great-task-table td {
	border-color:#888;
}
.row-header td.user {
	font-size:12px;
}
/* end over-ride */

.choice-box {
    width:260px;
    padding:15px;
    -moz-border-radius:8px;
    -webkit-border-radius:8px;
    background:#a8cb17;
    float:left;
    margin:0 35px 30px 0;
	color:#a8cb17;
	cursor:pointer;
	position:relative;
	color:#fefffd;
	font-weight:bold;
}
.choice-box img {
	position:absolute;
	right:5px;
	top:-20px;
}
.choice-box img.quote, .choice-box img.leads {
	top:-22px;
}
.choice-box:hover {
	background:#888;
	color:#fff;
	text-decoration:none;
}
.right-edge {
    margin-right:0;
}
.bottom-edge {
    margin-bottom:0;
}
</style>

<div id="content">

<div class="inner">
<?php if($this->session->userdata('viewlead')==1) {   ?>
<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<table border="0" cellpadding="0" cellspacing="0" class="search-table">
		<tr>
			<td>
				Lead Search
			</td>
			<td>
				<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Lead No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
			</td>
			<td>
				<div class="buttons">
					<button type="submit" class="positive">
						
						Search
					</button>
				</div>
			</td>
		</tr>
	</table>
</form>



<h2>Lead Dashboard</h2>
	
<div style="margin-top:30px;">

	<div style="float:left; display:none;" >
	<a class="choice-box" href="welcome/new_quote">
		Create a New Lead
		<img src="assets/img/icon_create_quote.png" class="icon quote" />
	</a>
	<a  class="choice-box" href="leads">
		View Sales Leads
		<img src="assets/img/icon_view_leads.png" class="icon leads" />
	</a>
	<a style = "display:none" class="choice-box right-edge" href="invoice">
		View Invoices Pending Deposit
		<img src="assets/img/icon_pending_deposit.png" class="icon pending" />
	</a>
	</div>
<?php } ?>
<?php if($this->session->userdata('viewlead') == 1) { ?>
	<a class="choice-box" onclick="advanced_filter();" >
		Advanced Filters
		<img src="assets/img/icon_view_leads.png" class="icon leads" />
	</a>

	<div id="advance_search" style="float:left;" >
		
		<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:250px; width:960px;">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
			<tr>
				<th>By Lead Stage</th>
				<th>By Customer</th>
				<th>Expected Worth</th>
				<th>By lead Owner</th>
				<th>By Lead Assignee</th>
			</tr>
			</thead>
				<tbody>
			<tr>	
				<td>
					<select style="width:230px;" multiple="multiple" id="stage" name="stage[]">
					<?php foreach($lead_stage as $ls) { ?>
						<?php if($ls['lead_stage_id'] <= 12) { ?>
							<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
						<?php } //for if condition ?>	
					<?php } ?>					
					</select> 
				</td>
				
				
				<td>
					<select style="width:230px;" multiple="multiple" id="customer" name="customer[]">
					<?php foreach($customers as $customer) {?>
					<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
					<?php } ?>
					</select> 
				</td>
				
				<td>
					<select  style="width:120px;" multiple="multiple" id="worth" name="worth[]">
					<option value="0-10000"> <10000 </option>
					<option value="10000-20000"> > 10000 < 20000 </option>
					<option value="20000-50000"> >20000 < 50000 </option>
					<option value="50000-above"> >50000 </option>
					</select> 
				</td>
				
				<td>
					<select  style="width:120px;" multiple="multiple" id="owner" name="owner[]">
					<?php foreach ($lead_owner as $owner){ ?>
					<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
					<?php } ?>
					</select> 
				</td>
				
				<td>
					<select  style="width:120px;" multiple="multiple" id="leadassignee" name="leadassignee[]">
						<?php foreach ($lead_owner as $owner) { ?>
							<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
						<?php } ?>
					</select> 
				</td>
			</tr>	
			<tr>
				<th>By Region Wise</th>
				<th>By Country Wise</th>
				<th>By State Wise</th>
				<th>By Location Wise</th>
			</tr>
			<tr>
				<td>
					<select  style="width:230px;" multiple="multiple" id="regionname" name="regionname[]">
						<?php foreach ($regions as $reg) { ?>
							<option value="<?php echo $reg['regionid'] ?>"><?php echo $reg['region_name'] ?></option>
						<?php } ?>
					</select> 
				</td>
				<td id="country_row">
					<select style="width:230px;" multiple="multiple" id="countryname" name="countryname[]">
						
					</select> 
				</td>
				<td>
					<select  style="width:120px;" multiple="multiple" id="statename" name="statename[]">
						
					</select> 
				</td>
				<td>
					<select  style="width:120px;" multiple="multiple" id="locname" name="locname[]">
						
					</select> 
				</td>
			</tr>
			<tr align="right" >
				<td colspan="6"><input type="reset" class="positive" name="advance" value="Reset" />
				<input type="submit" class="positive" name="advance" value="Search" />
			</tr>
			</tbody>
			</table>
		</form>
	</div>

	<div id="advance_search_results" style="clear:both" ></div>
	
	<p><?php echo '&nbsp;'; ?></p>
	<p><?php echo '&nbsp;'; ?></p>
<?php } ?>
<!--<?php //if($this->session->userdata('viewtask')==1) {  ?>
	<h2 style="padding-bottom:4px; border-bottom:1px solid #ccc; clear:left; margin-bottom:15px;">MY TASKS</h2>
	<div class="my-own-tasks appr" style="margin-bottom:20px;" id="jv-tab-4"></div>
	<div class="my-own-tasks unappr" style="margin-bottom:20px;" id="jv-tab-4"></div>

</div>
<?php //} ?>-->
<!--mychanges -->
<?php if($this->session->userdata('viewtask')==1) { ?>
	<h2 style="padding-bottom:4px; border-bottom:1px solid #ccc; clear:left; margin-bottom:15px;">TASKS</h2>
		
	<p>
			<img src="assets/img/due_today.jpg" width="10" /> Due Today
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed.jpg" width="10" /> Task Delayed
			&nbsp;&nbsp;
			<img src="assets/img/task_delayed_2days.jpg" width="10" /> Task Delayed more than 2 days
			&nbsp;&nbsp;
			<img src="assets/img/task_passed_deadline.jpg" width="10" /> Deadline Passed
			&nbsp;&nbsp;
			<img src="assets/img/task_completed.jpg" width="10" /> Task Completed
		</p>
		<p><?php echo '&nbsp;'; ?></p>
		
		<div class="all-tasks random-task" style="margin-bottom:20px;" id="jv-tab-4"></div>
		<?php
			include theme_url() . '/tpl/user_accounts_options.php';
			$uio = $userdata['userid'];
			foreach($created_by as $value) {
				$b[] = $value[created_by];						
			}
		?>

<form id="edit-job-task" onsubmit="return false;">

	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />


		<!-- edit task -->
			<table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
				<tr>
					<td colspan="4">
						<strong>All fields are required!</strong>
					</td>
				</tr>						
				<tr>				
					<td valign="top" width="80">
						<br /><br />Task Desc
					</td>
					<td colspan="3">
						<strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
						<textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
					</td>
				</tr>
				<tr>
					<td>Task Owner</td>
					<td><input type="text" class="edit-task-owner textfield" readonly ></td>
				</tr>			
				<?php
						
						if(in_array($uio,$b) || $userdata['role_id'] == 1) { ?>
				<tr>
					<td>
						Allocate to
					</td>
					<td>
						<select name="task_user" class="edit-task-allocate textfield width100px">
						<?php
						echo $remind_options, $remind_options_all, $contractor_options;
						?>
						</select>
					</td>
					<!--<td>
						Hours
					</td>
					<td>
						<input name="task_hours" type="text" class="edit-task-hours textfield width100px" /> Hours and
						<select name="task_mins" class="edit-task-mins textfield">
							<option value="0">0</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
						</select>
						Mins
					</td>-->
				</tr>
				<?php } else { ?>
				<tr>
							<td>
								Allocate to
							</td>
							<td>
								<select name="task_user" class="edit-task-allocate textfield width100px" disabled >
								<?php
								echo $remind_options, $remind_options_all, $contractor_options;
								?>
								</select>
							</td>
						</tr>
						<?php } ?>
				<tr>
					<td>
						Planned Start Date
					</td>
					<?php if(in_array($uio,$b) || $userdata['role_id'] == 1) {?>
					<td>
						<input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" />
					</td>
					<?php } else { ?>
					<td>
								<input type="text" name="task_start_date" class="edit-start-date textfield width100px" readonly />
							</td>
							<?php } ?>
					<td>
						Planned End Date
					</td>
					<td>
								<?php if(in_array($uio,$b) || $userdata['role_id'] == 1) { ?>
									<input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" />									
								<?php } else { ?>
									<input type="text" name="task_end_date" class="edit-end-date textfield width100px" readonly />
								<?php } ?>
								&nbsp;
								<!--<select name="task_end_hour" class="textfield edit-end-hour">
								<?php
								/*foreach ($time_range as $k => $v)
								{
									$selected = '';
									echo "
									<option value=\"{$k}\"{$selected}>{$v}</option>";
								}*/
								?>
								</select>-->
							</td>
				</tr>
				<tr>
					<td>Actual Start Date</td>
					<td><input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px" /></td>
					<td>Actual End Date</td>
					<td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="3"><textarea name="remarks" class="edit-task-remarks" width="420px"></textarea></td>
				</tr>
				<!--<tr><td colspan=3>Priority Support : <input type="checkbox" name="priority" class="priority"/></td>-->
				<tr>
					<td colspan="4">
						<div class="buttons">
							<button type="submit" class="positive" onclick="editTask();">Update</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
						</div>
					</td>
				</tr>
			</table>
		<!-- edit task end -->
		</form>
		<form style="display:none;" class="random-task-tables" onsubmit="return false;">
		</form>

<?php } ?>
<!-- ends -->
</div>

<?php if($this->session->userdata('viewPjt') == 1) { ?>
<h2 style="padding-bottom:4px; border-bottom:1px solid #ccc; clear:left; margin-bottom:15px;">PROJECTS</h2>

<form name="pjt_search_form" id="pjt_search_form" action="" method="post" style="float:right;">

			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
					<td>
                        Project Search
                    </td>
					<td>
                        <input type="text" id="keywordpjt" name="keywordpjt" value="<?php if (isset($_POST['keywordpjt'])) echo $_POST['keywordpjt']; else echo 'Project No, Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
                    </td>
                    <td rowspan=2>
                        <div class="buttons">
                            <button type="submit" class="positive">Search</button>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
<a class="choice-box" onclick="advanced_filter_pjt();" >
		Advanced Filters
		<img src="assets/img/icon_view_leads.png" class="icon leads" />
</a>
<div id="advance_search_pjt" style="float:left; width:100%;" >
		
		<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
		
			<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
			<table border="0" cellpadding="0" cellspacing="0" class="data-table">
			<thead>
				<tr>
					<th>By Project Status Wise</th>
					<th>By Project Manager Wise</th>
					<th>By Customer Wise</th>
				</tr>	
			</thead>
			<tbody>
			<tr>	
				<td>
					<select style="width:230px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
					<option value="0">All</option>
					<?php foreach($lead_stage_pjt as $ls) {?>
					<option value="<?php echo $ls['lead_stage_id']; ?>">
					<?php if ($ls['lead_stage_name'] == 'Project Charter Approved. Convert to Projects In Progress') {
						$ls['lead_stage_name'] = 'Project - In Progress'; }
					echo $ls['lead_stage_name']; ?>
					</option>	
					<?php } ?>					
					</select> 
				</td>
				
				
				<td>
					<select style="width:230px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
						<option value="0">All</option>
						<?php foreach($pm_accounts as $pm_acc) {?>
						<option value="<?php echo $pm_acc['userid']; ?>">
						<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
						<?php } ?>
					</select> 
				</td>
				
				<td>
					<select style="width:230px;" multiple="multiple" id="customer1" name="customer1[]">
						<option value="0">All</option>
						<?php foreach($customers as $customer) {?>
						<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr align="right" >
				<td colspan="5"><input type="reset" class="positive" name="advance_pjt" value="Reset" />
				<input type="submit" class="positive" name="advance_pjt" value="Search" /></td>
			
			</tr>
			</tbody>
			</table>
		</form>
	</div>
	
<div id="advance_search_results_pjts" style="clear:both; overflow:scroll; height:400px; width:960px;" ></div>
<?php } ?>

</div>



<script type="text/javascript">
	var owner = $("#owner").val(); 
	var leadassignee = $("#leadassignee").val();
	var regionname = $("#regionname").val();
	var countryname = $("#countryname").val();
	var statename = $("#statename").val();
	var locname = $("#locname").val();
	var stage = $("#stage").val(); 
	var customer = $("#customer").val(); 
	var worth = $("#worth").val();	
	var keyword = $("#keyword").val(); 
	//alert(keyword);
	if(keyword == "Lead No, Job Title, Name or Company")
	keyword = 'null';
	<?php if($this->session->userdata('viewlead')==1) {	?>
		document.getElementById('advance_search').style.display = 'none';	
	<?php } ?>
	var sturl = "welcome/advance_filter_search/";
	$('#advance_search_results').load(sturl);

//For Advance Filters functionality.
$("#advanceFilters").submit(function() {
	var owner = $("#owner").val();
	var leadassignee = $("#leadassignee").val();
	var regionname = $("#regionname").val();
	var countryname = $("#countryname").val();
	var statename = $("#statename").val();
	var locname = $("#locname").val();
	var stage = $("#stage").val(); 
	var customer = $("#customer").val(); 
	var worth = $("#worth").val();	
	var  keyword = $("#keyword").val();
		
	 $.ajax({
	   type: "POST",
	   url: "<?php echo base_url(); ?>welcome/advance_filter_search",
	   data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&keyword="+keyword,

	   success: function(data){
		   $('#advance_search_results').html(data);
	   }
	 });
	return false;  //stop the actual form post !important!
});


//for lead search functionality.
 $(function(){
       $("#lead_search_form").submit(function(){
		var  keyword = $("#keyword").val(); 
		if(keyword == "Lead No, Job Title, Name or Company")
		keyword = 'null';
		var owner = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname = $("#regionname").val();
		var countryname = $("#countryname").val();
		var statename = $("#statename").val();
		var locname = $("#locname").val();
		var stage = $("#stage").val(); 
		var customer = $("#customer").val(); 
		var worth = $("#worth").val();
 
         $.ajax({
           type: "POST",
           url: "<?php echo base_url(); ?>welcome/advance_filter_search",
           data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&keyword="+keyword+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
 
           success: function(data){
			   $('#advance_search_results').html(data);
           }
         });
         return false;  //stop the actual form post !important!
 
      });
   });

function advanced_filter(){
	$('#advance_search').slideToggle('slow');
	var  keyword = $("#keyword").val();
	var status = document.getElementById('advance_search').style.display;
	
	if(status == 'none') {
		var owner = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname = $("#regionname").val();
		var countryname = $("#countryname").val();
		var statename = $("#statename").val();
		var locname = $("#locname").val();
		var stage = $("#stage").val(); 
		var customer = $("#customer").val(); 
		var worth = $("#worth").val();	
		
	}
	else {
		$("#owner").val("");
		$("#leadassignee").val("");
		$("#regionname").val("");
		$("#countryname").val("");
		$("#statename").val("");
		$("#locname").val("");
		$("#stage").val("");
		$("#customer").val("");
		$("#worth").val("");
	}
}

//For Countries
$('#regionname').change(function() {
	$('#statename').html('');
	$('#locname').html('');
	loadCountry();
});

function loadCountry() {
	var region_id = $("#regionname").val(); 
	$.post( 
		'choice/loadCountrys/'+ region_id,
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}
		function(data) {										
			if (data.error) 
			{
				alert(data.errormsg);
			} 
			else 
			{
				$("select#countryname").html(data);
			}
		}
	);
}

//For States
$('#countryname').change(function() {
	$('#locname').html('');
	loadState();
});

function loadState() {
	var coun_id = $("#countryname").val();
	$.post( 
		'choice/loadStates/'+ coun_id,
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}
		function(data) {										
			if (data.error) 
			{
				alert(data.errormsg);
			} 
			else 
			{
				$("select#statename").html(data);
			}
		}
	);
}

//For Locations
$('#statename').change(function() {
		loadLocations();
});

function loadLocations() {
	var st_id = $("#statename").val();
	$.post( 
		'choice/loadLocns/'+ st_id,
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}
		function(data) {										
			if (data.error) 
			{
				alert(data.errormsg);
			} 
			else 
			{
				$("select#locname").html(data);
			}
		}
	);
}


//For Projects
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var keyword = $("#keywordpjt").val(); 
	//alert(keyword);
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	<?php if($this->session->userdata('viewPjt')==1) { 	?>
	document.getElementById('advance_search_pjt').style.display = 'none';	
	<?php } ?>
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);	
	$('#advance_search_results_pjts').load(sturl);
	
function advanced_filter_pjt(){
	$('#advance_search_pjt').slideToggle('slow');
	var  keyword = $("#keywordpjt").val();
	var status = document.getElementById('advance_search_pjt').style.display;
	
	if(status == 'none') {
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
	}
	else   {
			
		$("#pjt_stage").val("");
		$("#pm_acc").val("");
		$("#customer1").val("");

	}
}


$('#advanceFilters_pjt').submit(function() {	
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var  keyword = $("#keywordpjt").val(); 
	if(keyword == "Project No, Project Title, Name or Company")
	keyword = 'null';
	document.getElementById('advance_search_results_pjts').style.display = 'block';	
	var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
	//alert(sturl);
	$('#advance_search_results_pjts').load(sturl);	
	return false;
});

$('#pjt_search_form').submit(function() {	
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val();  
		//document.getElementById('ad_filter').style.display = 'block';
		var sturl = "welcome/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		$('#advance_search_results_pjts').load(sturl);
		return false;
});

/*mychanges*/
$(function(){
	$('.all-tasks').load('tasks/index/extend #task-page .task-contents', {}, loadEditTables);
	$('#set-job-task .pick-date, #search-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -1, maxDate: '+6M'});
	
	$('#task_search_user').val('<?php echo $userdata['userid']; ?>');
	/* job tasks character limit */
	$('#job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#task-desc-countdown').text(remain_len);
	});
	$('#edit-job-task-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 1000) {
			$(this).focus().val(desc_len.substring(0, 1000));
		}
		
		var remain_len = 1000 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#edit-task-desc-countdown').text(remain_len);
	});
});
function searchTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	var data = $('#search-job-task').serialize();
	$.post('tasks/search',data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function searchtodayTasks(){
	$('.tasks-search .search-results').empty().html('Loading...');
	$.post('tasks/search','task_search_user='+$('#task_search_user').val()+'&task_search_start_date='+$('#hided').val()+'&task_search_end_date='+$('#hided').val()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function(data) {
		$('.tasks-search').find('.search-results').remove();
		$('.all-tasks').hide();
		$('.tasks-search').append(data);
	});
	return false;
}
function loadEditTables(){
	$('#jv-tab-4').block({
            message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
        });
	var taskids = [];
	$('td.task.random-task').each(function(){
		taskids.push($(this).attr('rel'));
		
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit |</button> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'complete\'); return false;">Approve |</button> \
								<button type="submit" onclick="setTaskStatus(\'' + $(this).attr('rel') + '\', \'delete\'); return false;">Delete</button> \
							</div>');	
		
	});
	$('td.task.newrandom-task').each(function(){
		taskids.push($(this).attr('rel'));
			$(this).append('<div class="buttons" style="display:none"> \
								<button style="margin:0!important;background: none repeat scroll 0 0 transparent;" type="submit" onclick="openEditTask(\'' + $(this).attr('rel') + '\', \'random\'); return false;\">Edit</button> \
							</div>');
	});
	if (taskids.length < 1)	{
		$('#jv-tab-4').unblock();
		return;
	}
	$.post('ajax/request/get_random_tasks',{'id_set': taskids.join(','),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
		if (data != '')	{
			$('form.random-task-tables').html(data);
		} 
		$('#jv-tab-4').unblock();
	});
}
/*ends*/
</script>
<!--<script type="text/javascript">

$(function(){
	$i=0;
	$('.my-own-tasks.appr').load('tasks/index/extend #user-<?php echo $userdata['userid'] ?>', {}, function(){
		$i++;LoadCheck($i);
	});
	$('.my-own-tasks.unappr').load('tasks/index/extend #unapprove-user-<?php echo $userdata['userid'] ?>', {}, function(){
		$i++;LoadCheck($i);
	});
});

function LoadCheck($v){
	if($v==2) {
		
		if($('.my-own-tasks table').size()<1) $('.my-own-tasks.appr').html('<p>You have no more outstanding tasks. Please request a new task from the production manager.</p>');
	}

}
</script> -->
<?php
require (theme_url().'/tpl/footer.php');
ob_end_flush();
?>
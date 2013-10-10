<?php
ob_start();
require ('tpl/header.php'); 
//echo baseurl();
?>

<script type="text/javascript">var this_is_home = true;</script>
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
<?php //echo '<pre>'; print_r($customers); echo '</pre>'; ?>
<?php
	if($this->uri->segment(1)=='production') include 'tpl/production_submenu.php'; 
	
?>
<div class="inner">
<?php // if($this->session->userdata('accesspage')==1) {   ?>
<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
	
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	
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
<?php
$hostingid=array();
if(!empty($hosting)){
foreach($hosting as $val){
	$v=$val['hostingid_fk'];$k=$val['jobid_fk'];
	$hostingid[$k]=$v;
}
}
?>

<h2><?php if ($this->uri->segment(1) == 'production') echo 'Production ' ?>Lead Dashboard</h2>
	
<div style="margin-top:30px;">
<?php

//if (in_array($userdata['level'], array(0,1,2,3,4,6)))
//{
	//if ($this->uri->segment(1) != 'production' && in_array($userdata['level'], array(0,1,4)))
	//{
	?>
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
	<a class="choice-box" onclick="advanced_filter();" >
		Advanced Filters
		<img src="assets/img/icon_view_leads.png" class="icon leads" />
	</a>
	
	
	<div id="advance_search" style="float:left;">
		<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:280px; width:940px;">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<div style="border: 1px solid #DCDCDC;">
			<table cellpadding="0" cellspacing="0" class="data-table" >
			<thead><tr>
				<th>By Lead Stage</th>
				<th>By Customer</th>
				<th>Expected Worth</th>
				<th>By lead Owner</th>
				<th>Lead Assignee</th>
			</tr>	
				</thead>
				<tbody>
			<tr>	
				<td>
					<select style="width:230px;" multiple="multiple" id="stage" name="stage[]">
					<?php foreach($lead_stage as $ls) { ?>
						<?php if($ls['lead_stage_id'] <= 12) { ?>
							<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
						<?php } //if condition- end here. ?>
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
					<?php foreach ($lead_owner as $owner){ 
						if(!empty($owner['first_name'])) {?>
					<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
					<?php } } ?>
					</select> 
				</td>
				
				<td>
					<select  style="width:120px;" multiple="multiple" id="leadassignee" name="leadassignee[]">
						<?php foreach ($lead_owner as $owner) { 
							if(!empty($owner['first_name'])) {?>
							<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
						<?php } } ?>
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
						<?php foreach ($regions as $reg) { 
							if(!empty($reg['region_name'])) {?>
							<option value="<?php echo $reg['regionid'] ?>"><?php echo $reg['region_name'] ?></option>
						<?php } } ?>
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
			</tbody></table></div>
		</form>
	</div>
	<div id="advance_search_results" style="clear:both" ></div>
<?php
	//}
?>  
	
</div>
<?php //} } else{
	//echo "You have no rights to access this page";
//}?>
</div>

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
	   data: "stage="+stage+"&customer="+customer+"&worth="+worth+"&owner="+owner+"&leadassignee="+leadassignee+"&regionname="+regionname+"&countryname="+countryname+"&statename="+statename+"&locname="+locname+"&keyword="+keyword+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',

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
	} else {
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
		{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		function(data) {										
			if (data.error) 
			alert(data.errormsg);
			else 
			$("select#countryname").html(data);
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
	if(coun_id != '') {
		$.post( 
			'choice/loadStates/'+ coun_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {										
				if (data.error) 
				alert(data.errormsg);
				else 
				$("select#statename").html(data);
			}
		);
	}
}

//For Locations
$('#statename').change(function() {
		loadLocations();
});

function loadLocations() {
	var st_id = $("#statename").val();
	if(st_id != '') {
		$.post( 
			'choice/loadLocns/'+ st_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {										
				if (data.error) 
				alert(data.errormsg);
				else 
				$("select#locname").html(data);
			}
		);
	}
}

</script>
<script type="text/javascript">

$(function(){
	$i=0;
	//alert(<?php echo $userdata['userid'] ?>);
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
</script>
<?php
require ('tpl/footer.php');
ob_end_flush();
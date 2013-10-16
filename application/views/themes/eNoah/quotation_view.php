<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
// echo base_url();
?>
<!--script type="text/javascript">var this_is_home = true;</script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script-->
<div id="content">
	<div class="inner">
		<?php if($this->session->userdata('accesspage')==1) {   ?>
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
								<button type="submit" class="positive">Search</button>
							</div>
						</td>
					</tr>
				</table>
			</form>

			<h2>Lead Dashboard</h2>
		
			<div style="margin-top:30px;">
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
										
												<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
												
										<?php } ?>					
									</select> 
								</td>
								<td>
									<select style="width:230px;" multiple="multiple" id="customer" name="customer[]">
									<?php foreach($customers as $customer) { ?>
										<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
									<?php } ?>
									</select> 
								</td>  
								<td>
									<select  style="width:120px;" multiple="multiple" id="worth" name="worth[]">
										<option value="0-10000"> < 10000 </option>
										<option value="10000-20000"> > 10000 < 20000 </option>
										<option value="20000-50000"> > 20000 < 50000 </option>
										<option value="50000-above"> > 50000 </option>
									</select> 
								</td>
								<td>
									<select  style="width:120px;" multiple="multiple" id="owner" name="owner[]">
									<?php foreach ($lead_owner as $owner){ 
										if(!empty($owner['first_name'])) { ?>
										<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
									<?php } 
									} ?>
									</select> 
								</td>
								<td>
									<select  style="width:120px;" multiple="multiple" id="leadassignee" name="leadassignee[]">
										<?php foreach ($lead_owner as $owner) { 
												if(!empty($owner['first_name'])) { ?>		
													<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
										<?php 	} 
											  } 
										?>
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
							</tbody>
							</table>
						</div>
					</form>
				</div>
				<div id="advance_search_results" style="clear:both" ></div>
			</div>
	<?php 
		} else {
				echo "You have no rights to access this page";
		}
	?>
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
			if (data.error) {
				alert(data.errormsg);
			} else {
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
	if(coun_id != '') {
		$.post( 
			'choice/loadStates/'+ coun_id,
			{'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(data) {										
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#statename").html(data);
				}
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
				if (data.error) {
					alert(data.errormsg);
				} else {
					$("select#locname").html(data);
				}
			}
		);
	}
}

</script>

<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
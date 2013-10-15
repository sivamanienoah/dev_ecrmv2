<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	 
    <div class="inner">
		<?php if($this->session->userdata('viewReport')==1){?>
			
	    	<form name = 'report_lead_frm' id = 'report_lead_frm' action="<?php echo  $this->uri->uri_string() ?>" method="post" >
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
	            <h2>Leads Report By Owner</h2>
	            <?php if ($this->validation->error_string != '') { ?>
	            <div class="form_error">
	                <?php echo  $this->validation->error_string ?>
	            </div>
	            <?php } ?>
	            <!-- <p>Configure the task alerts.</p> -->
	        	
	        	
	        	<a class="choice-box advanced_filter">
					Advanced Filters
					<img class="icon leads" src="assets/img/icon_view_leads.png">
				</a>
				
	        	<div class="clear"><div>
	            <div id="advance_search" style="display:none;">
				
				<table class="layout">
					<tr>
						<td>
							From Date
						</td>
						<td>
							<input type="text" name="task_search_start_date" id ="task_search_start_date" class="textfield pick-date width100px" autocomplete = 'off' />
						</td>
						<td>
							To Date
						</td>	
						<td>
							<input type="text" name="task_search_end_date" id ="task_search_end_date"class="textfield pick-date width100px" autocomplete = 'off' />
						</td>				
					</tr>
	            </table>
	                
	            <div style="border: 1px solid #DCDCDC;">    
				<table cellpadding="0" cellspacing="0" class="data-table" >
					<thead>
						<tr>
							<th>By Lead Stage</th>
							<th>By Customer</th>
							<th>Expected Worth</th>
							
							<th>By lead Owner</th>
							<th>Lead Assignee</th>
							
						</tr>	
					</thead>				
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
							<?php foreach ($user as $owner){ ?>
							<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
							<?php } ?>
							</select> 
						</td>
						
						<td>
							<select  style="width:120px;" multiple="multiple" id="leadassignee" name="leadassignee[]">
								<?php foreach ($user as $owner) { ?>
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
							<input type="submit" class="positive" name="advance" id="advance" value="Search" />
							<div id = 'load' style = 'float:right;display:none;height:1px;'>
								<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
							</div>
						</td>
					</tr>
				</table></div>
			</div>
			
				
			<div id = 'report_grid'>
	        	<?php echo $report; ?>
			</div>
		</form>
			
		<?php } else{
			echo "You have no rights to access this page";
		}?>
	</div>
</div>
<script type="text/javascript">

$(function(){

	$('.advanced_filter').click(function(){
			$('#advance_search').slideToggle('slow');
	});	
	//$('.pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	$('#task_search_start_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
		 		var date2 = $('#task_search_start_date').datepicker('getDate');
        		$("#task_search_end_date").datepicker("option","minDate", date2);
     		 }
	});

	$('#task_search_end_date').datepicker({dateFormat: 'dd-mm-yy',  onSelect: function(selected) {
			var date1 = $('#task_search_end_date').datepicker('getDate');							
			$("#task_search_start_date").datepicker("option","maxDate", date1);
		 }
	});
	 
	
	$('#report_lead_frm').submit(function(e){
		e.preventDefault();		
		$('#advance').hide();
		$('#load').show();		
		var base_url = "<?php echo site_url(); ?>";
		var start_date = $('#task_search_start_date').val();
		var end_date = $('#task_search_end_date').val();
		var stage = $('#stage').val();		
		stage = stage + "";			
		var customer = $('#customer').val();
		customer = customer + "";
		var worth = $('#worth').val();
		worth = worth+"";
		var owner = $('#owner').val();
		owner = owner+"";
		var leadassignee = $('#leadassignee').val();		
		leadassignee = leadassignee+"";
		
		var regionname = $('#regionname').val();	
		regionname = regionname+"";	
		var countryname = $('#countryname').val();
		countryname = countryname+"";
		var statename = $('#statename').val();
		statename = statename+"";
		var locname = $('#locname').val();
		locname = locname+"";
		
		$('#report_grid').load(base_url+'report/report_lead_owner/get_lead_report',{start_date:start_date,end_date:end_date,stage:stage,customer:customer,worth:worth,owner:owner,leadassignee:leadassignee,regionname:regionname,countryname:countryname,statename:statename,locname:locname},function(){
			$('#advance').show();
			$('#load').hide();	
		});
		
	});

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

		
});
</script>
<?php require (theme_url(). '/tpl/footer.php'); ?>

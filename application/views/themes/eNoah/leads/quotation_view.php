<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>
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
					<img src="assets/img/advanced_filter.png" class="icon leads" />
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
								<th>By Status</th>
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
								<td>
									<select  style="width:120px;" multiple="multiple" id="lead_status" name="lead_status[]">
										<option value="1">Active</option>
										<option value="2">OnHold</option>
										<option value="3">Dropped</option>
										<option value="4">Closed</option>
									</select> 
								</td>
							</tr>
							<tr align="right" >
								<td colspan="6"><input type="reset" class="positive" name="advance" value="Reset" />
								<input type="submit" class="positive" name="advance" id="advance" value="Search" />
								<div id = 'load' style = 'float:right;display:none;height:1px;'>
									<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
								</div>
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
<script type="text/javascript" src="assets/js/leads/quotation_view.js"></script>
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
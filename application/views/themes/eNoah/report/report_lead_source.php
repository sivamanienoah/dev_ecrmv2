<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	 
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
			
	    	<form name = 'report_lead_frm' id = 'report_lead_frm' action="<?php echo  $this->uri->uri_string() ?>" method="post" >
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
				<div class="page-title-head">
					<h2 class="pull-left borderBtm">Leads Report By Lead Source</h2>
					<div class="buttons export-to-excel">
						<button type="button" id="excel" class="positive" onclick="location.href='#'">
							Export to Excel
						</button>
					</div>
					<a class="choice-box advanced_filter">
						<span>Advanced Filters</span>
						<img class="icon leads" src="assets/img/advanced_filter.png">
					</a>
				</div>

	            <?php if ($this->validation->error_string != '') { ?>
	            <div class="form_error">
	                <?php echo $this->validation->error_string; ?>
	            </div>
	            <?php } ?>       	

				
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
									<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
								<?php } ?>					
							</select> 
						</td>
						<td>
							<select style="width:230px;" multiple="multiple" id="customer" name="customer[]">
								<?php foreach($customers as $customer) { ?>
									<option value="<?php echo $customer['companyid']; ?>"><?php echo $customer['company']; ?></option>	
								<?php } ?>
							</select> 
						</td>
						<td>
							<select style="width:120px;" multiple="multiple" id="worth" name="worth[]">
								<option value="0-10000"> <10000 </option>
								<option value="10000-20000"> > 10000 < 20000 </option>
								<option value="20000-50000"> >20000 < 50000 </option>
								<option value="50000-above"> >50000 </option>
							</select> 
						</td>
						<td>
							<select  style="width:120px;" multiple="multiple" id="owner" name="owner[]">
								<?php foreach ($user as $owner) { ?>
									<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name']; ?>"><?php echo $owner['first_name'] ?></option>
								<?php } ?>
							</select> 
						</td>
						<td>
							<select style="width:120px;" multiple="multiple" id="leadassignee" name="leadassignee[]">
								<?php foreach ($user as $owner) { ?>
									<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name']; ?>"><?php echo $owner['first_name'] ?></option>
								<?php } ?>
							</select> 
						</td>
					</tr>
					<tr>
						<th>By Region Wise</th>
						<th>By Country Wise</th>
						<th>By State Wise</th>
						<th>By Location Wise</th>				
						<th>By Lead Source</th>			
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
						<td>
							<select  style="width:120px;" multiple="multiple" id="lead_src" name="lead_src[]">
								<?php if (count($lead_sourc)>0) { ?>
									<?php foreach ($lead_sourc as $src) { ?>
										<option value="<?php echo $src['lead_source_id'] ?>"><?php echo $src['lead_source_name'] ?></option>
									<?php } ?>
								<?php } ?>
							</select> 
						</td>
					</tr> 
					<tr align="right" >
						<td colspan="6">
							<input type="reset" class="positive" name="advance" value="Reset" />
							<input type="submit" class="positive" name="advance" id="advance" value="Search" />
							<div id = 'load' style = 'float:right;display:none;height:1px;'>
								<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
							</div>
						</td>
					</tr>
				</table>
				</div>
			</div>
			
			<div id = 'report_grid'>
	        	<?php echo $report; ?>
			</div>
		</form>
		<?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<script type="text/javascript" src="assets/js/report/report_lead_source.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>

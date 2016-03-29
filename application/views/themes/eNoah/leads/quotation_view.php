<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>

<div id="content">
	<div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Lead Dashboard</h2>
			
			<a class="choice-box" onclick="advanced_filter();" >
				<span>Advanced Filters</span>
				<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>

			<div class="search-dropdown">
				<a class="saved-search-head">
					<p>Saved Search</p>
				</a>
				<div class="saved-search-criteria" style="display: none; ">
					<img class="dpwn-arw" src="assets/img/drop-down-arrow.png" title="" alt="" />
					<ul class="search-root">
					<li class="save-search-heading"><span>Search Name</span><span>Set Default</span><span>Action</span></li>
					<?php 
					if(sizeof($saved_search)>0) {
						foreach($saved_search as $searc) { 
					?>
							<li class="saved-search-res" id="item_<?php echo $searc['search_id']; ?>">
								<span><a href="javascript:void(0)" onclick="show_search_results('<?php echo $searc['search_id'] ?>')"><?php echo $searc['search_name'] ?></a></span>
								<span class='rd-set-default'><input type="radio" value="<?php echo $searc['search_id'] ?>" <?php if ($searc['is_default']==1) { echo "checked"; } ?> name="set_default_search" class="set_default_search" /></span>
								<span><a title="Delete" href="javascript:void(0)" onclick="delete_save_search('<?php echo $searc['search_id'] ?>')"><img alt="delete" src="assets/img/trash.png"></a></span>
							</li>
					<?php 
						}
					} else {
					?>
						<li id="no_record" style="text-align: center; margin: 5px;">No Save & search found</li>
					<?php
					}
					?>
					</ul>
				</div>
			</div>

			<div class="section-right">
				<!--search-->
				<div class="form-cont search-table">
					<form id="lead_search_form" name="lead_search_form" method="post">
						<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Lead No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
						<button type="submit" class="positive">Lead Search</button>			
					</form>
				</div>
				<!--search-->
				<!--add-->
				<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons add-new-button">
					<button onclick="location.href='<?php echo base_url(); ?>welcome/new_quote'" class="positive" type="button">
						Add New Lead
					</button>
				</div>
				<?php } ?>
				<!--add-->
				<!--export-->
				<div class="buttons export-to-excel">
					<!--a class="export-btn">Export to Excel</a-->
					<button id="excel_lead" class="positive" type="button" >
						Export to Excel
					</button>
					<input type="hidden" name="search_type" value="" id="search_type" />
				</div>
				<!--export-->
			</div>
			
			
		</div>
	
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			</form>
		
			<div>			
				<div id="advance_search" style="float:left;width:100%;">
					<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:280px; width:100%;">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">By Lead Stage</td>
									<td class="tblheadbg">By Customer</td>
									<td class="tblheadbg">Expected Worth</td>
									<td class="tblheadbg">By lead Owner</td>
									<td class="tblheadbg">By Lead Assignee</td>
									<td class="tblheadbg" colspan='2'>By Service</td>
								</tr>
								<tr>	
									<td>
										<select style="width:210px" multiple="multiple" id="stage" name="stage[]">
											<?php foreach($lead_stage as $ls) { ?>
													<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
											<?php } ?>					
										</select> 
									</td>
									<td>
										<select style="width:210px" multiple="multiple" id="customer" name="customer[]">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
										<?php } ?>
										</select> 
									</td>  
									<td>
										<select style="width:120px" multiple="multiple" id="worth" name="worth[]">
											<option value="0-10000"> < 10000 </option>
											<option value="10000-20000"> > 10000 < 20000 </option>
											<option value="20000-50000"> > 20000 < 50000 </option>
											<option value="50000-above"> > 50000 </option>
										</select> 
									</td>
									<td>
										<select style="width:110px" multiple="multiple" id="owner" name="owner[]">
											<?php foreach ($lead_owner as $owner) { 
													if(!empty($owner['first_name'])) { ?>
														<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
											<?php 	} 
												} 
											?>
										</select> 
									</td>
									<td>
										<select style="width:130px" multiple="multiple" id="leadassignee" name="leadassignee[]">
											<?php foreach ($lead_owner as $owner) {
													if(!empty($owner['first_name'])) { ?>		
														<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
											<?php 	} 
												}
											?>
										</select> 
									</td>
									<td colspan='2'>
										<select multiple="multiple" id="service" name="service[]" >
											<?php
												if(isset($services) && count($services)>0){
													foreach($services as $se){ ?>
														<option value="<?php echo $se['sid'] ?>"><?php echo $se['services'] ?></option>
													<?php }
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="tblheadbg">By Source</td>
									<td class="tblheadbg">By Region Wise</td>
									<td class="tblheadbg">By Country Wise</td>
									<td class="tblheadbg">By State Wise</td>
									<td class="tblheadbg">By Location Wise</td>
									<td class="tblheadbg">By Status</td>
									<td class="tblheadbg">By Lead Indicator</td>
								</tr>
								<tr>
									<td>
										<select multiple="multiple" id="lead_src" name="lead_src[]" >
											<?php
												if(isset($sources) && count($sources)>0){
													foreach($sources as $srcs){ ?>
														<option value="<?php echo $srcs['lead_source_id'] ?>"><?php echo $srcs['lead_source_name'] ?></option>
													<?php }
												}
											?>
										</select> 
									</td>
									<td>
										<select style="width:210px" multiple="multiple" id="regionname" name="regionname[]">
											<?php foreach ($regions as $reg) {
													if(!empty($reg['region_name'])) { ?>
														<option value="<?php echo $reg['regionid'] ?>"><?php echo $reg['region_name'] ?></option>
											<?php 	}
												}
											?>
										</select> 
									</td>
									<td id="country_row">
										<select style="width:170px" multiple="multiple" id="countryname" name="countryname[]">
											
										</select> 
									</td>
									<td>
										<select style="width:120px" multiple="multiple" id="statename" name="statename[]">
											
										</select> 
									</td>
									<td>
										<select style="width:120px" multiple="multiple" id="locname" name="locname[]">
											
										</select> 
									</td>
									<td>
										<select style="width:70px" multiple="multiple" id="lead_status" name="lead_status[]">
											<option value="1">Active</option>
											<option value="2">OnHold</option>
											<option value="3">Dropped</option>
											<option value="4">Closed</option>
										</select> 
									</td>
									<td>
										<select style="width:85px" multiple="multiple" id="lead_indi" name="lead_indi[]">
											<option value="HOT">Hot</option>
											<option value="WARM">Warm</option>
											<option value="COLD">Cold</option>
										</select> 
									</td>
								</tr>
								<tr align="right" >
									<td colspan="7"><input type="reset" class="positive input-font" name="advance" value="Reset" />
									<input type="button" class="positive input-font show-ajax-loader" name="advance" id="search_advance" value="Search" />
									<input type="button" class="positive input-font show-ajax-loader" name="advance" id="save_advance" value="Save & Search" />
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
									</td>
								</tr>
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
<div id='popupGetSearchName'></div>
<script type="text/javascript" src="assets/js/leads/quotation_view.js"></script>
<script>

$('#excel_lead').click(function() {

	var search_type = $('#search_type').val();	
	var url = site_base_url+"welcome/excelExport/";	
	if(search_type == 'search'){
		var stage        = $("#stage").val();
		var customer     = $("#customer").val();
		var service      = $("#service").val();
		var lead_src     = $("#lead_src").val();
		var worth	     = $("#worth").val();
		var owner 	     = $("#owner").val();
		var leadassignee = $("#leadassignee").val();
		var regionname   = $("#regionname").val();
		var countryname  = $("#countryname").val();
		var statename    = $("#statename").val();
		var locname      = $("#locname").val();
		var lead_status  = $("#lead_status").val();
		var lead_indi    = $("#lead_indi").val();
		var keyword      = $("#keyword").val();
		
		
		var form = $('<form action="' + url + '" method="post">' +
		  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
		  '<input id="project" type="hidden" name="stage" value="'+stage+'" />'+
		  '<input id="customer" type="hidden" name="customer" value="'+customer+'" />'+
		  '<input id="service" type="hidden" name="service" value="'+service+'" />'+
		  '<input id="lead_src" type="hidden" name="lead_src" value="'+lead_src+'" />'+
		  '<input id="worth" type="hidden" name="worth" value="'+worth+'" />'+
		  '<input id="owner" type="hidden" name="owner" value="'+owner+'" />'+
		  '<input id="leadassignee" type="hidden" name="leadassignee" value="'+leadassignee+'" />'+
		  '<input id="regionname" type="hidden" name="regionname" value="'+regionname+'" />'+
		  '<input type="hidden" name="countryname" id="countryname" value="'+countryname+ '" />' +
		  '<input type="hidden" name="locname" id="locname" value="'+locname+ '" />' +
		  '<input type="hidden" name="lead_status" id="lead_status" value="'+lead_status+ '" />' +
		  '<input type="hidden" name="lead_indi" id="lead_indi" value="'+lead_indi+ '" />' +
		  '<input type="hidden" name="keyword" id="keyword" value="'+keyword+ '" />' +
		  '<input type="hidden" name="statename" id="statename" value="'+statename+ '" /></form>');
		$('body').append(form);
		$(form).submit(); 
		return false;		
	}else{
		var form = $('<form action="' + url + '" method="post">' +
			  '<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" /></form>');
			$('body').append(form);
			$(form).submit(); 
			return false;
	}


});

</script>
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
?>
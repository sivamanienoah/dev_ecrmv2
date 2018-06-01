<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
?>

<div id="content">
	<div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Asset Dashboard</h2>
			
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
						<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo '' ?>" class="textfield width200px g-search" />
						<button type="submit" class="positive">Asset Search</button>			
					</form>
				</div>
				<!--search-->
				<!--add-->
				<?php if($this->session->userdata('add')==1) { ?>
				<div class="buttons add-new-button">
					<button onclick="location.href='<?php echo base_url(); ?>asset_register/new_asset'" class="positive" type="button">
						Add New Asset
					</button>
				</div>
				<?php } ?>
				<!--add-->
				<!--export-->
<!--				<div class="buttons export-to-excel">
					a class="export-btn">Export to Excel</a
					<button id="excel_lead" class="positive" type="button" >
						Export to Excel
					</button>
					<input type="hidden" name="search_type" value="" id="search_type" />
				</div>-->
				<!--export-->
			</div>
		</div>
	
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			</form>
		
			<div>			
				<div id="advance_search" style="float:left;width:100%;">
					<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:314px; width:100%;">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">By Created / Modified Date</td>
									<td class="tblheadbg">By Lead Stage</td>
									<td class="tblheadbg">By Customer</td>
									<td class="tblheadbg">Expected Worth</td>
									<td class="tblheadbg">By lead Owner</td>
									<td class="tblheadbg">By Lead Assignee</td>
									<td class="tblheadbg" colspan="2">By Service</td>
									
								</tr>
								<tr>	
									<td>
										From <input type="text" data-calendar="true" name="from_date" id="from_date" class="textfield" style="width:57px;" />
										<br />
										To <input type="text" data-calendar="true" name="to_date" id="to_date" class="textfield" style="width:57px; margin-left: 13px;" />
									</td>
									<td>
										<select style="width:148px" multiple="multiple" id="stage" name="stage[]">
											<?php foreach($lead_stage as $ls) { ?>
													<option value="<?php echo $ls['lead_stage_id']; ?>"><?php echo $ls['lead_stage_name']; ?></option>
											<?php } ?>					
										</select> 
									</td>
									<td>
										<select style="width:180px" multiple="multiple" id="customer" name="customer[]">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['companyid']; ?>"><?php echo $customer['company']; ?></option>	
										<?php } ?>
										</select> 
									</td>  
									<td>
										<select style="width:110px" multiple="multiple" id="worth" name="worth[]">
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
										<select style="width:110px" multiple="multiple" id="leadassignee" name="leadassignee[]">
											<?php foreach ($lead_owner as $owner) {
													if(!empty($owner['first_name'])) { ?>		
														<option value="<?php echo $owner['userid'] ?>"><?php echo $owner['first_name'] ?></option>
											<?php 	} 
												}
											?>
										</select> 
									</td>
									<td colspan="2">
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
									<td class="tblheadbg">By Industry</td>
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
										<select multiple="multiple" id="industry" name="industry[]" >
											<?php
												if(isset($industry) && count($industry)>0){
													foreach($industry as $ind){ ?>
														<option value="<?php echo $ind['id'] ?>"><?php echo $ind['industry'] ?></option>
													<?php }
												}
											?>
										</select>
									</td>
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
										<select style="width:180px" multiple="multiple" id="regionname" name="regionname[]">
											<?php foreach ($regions as $reg) {
													if(!empty($reg['region_name'])) { ?>
														<option value="<?php echo $reg['regionid'] ?>"><?php echo $reg['region_name'] ?></option>
											<?php 	}
												}
											?>
										</select> 
									</td>
									<td id="country_row">
										<select style="width:110px" multiple="multiple" id="countryname" name="countryname[]">
											
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
										<select style="width:120px" multiple="multiple" id="lead_status" name="lead_status[]">
											<option value="1">Active</option>
											<option value="2">OnHold</option>
											<option value="3">Dropped</option>
											<option value="4">Closed</option>
											<option value="5">Moved to Project</option>
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
									
									<td colspan="8"><input type="reset" class="positive input-font" name="advance" value="Reset" />
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
<script>
var query_type = '<?php echo isset($load_proposal_expect_end) ? $load_proposal_expect_end : '' ?>';
$(function() {
	$('#from_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true, 
		onSelect: function(date) {
			if($('#to_date').val!='')
			{
				$('#to_date').val('');
			}
			var return_date = $('#from_date').val();
			$('#to_date').datepicker("option", "minDate", return_date);
		},
		beforeShow: function(input, inst) {
			/* if ((selDate = $(this).val()).length > 0) 
			{
				iYear = selDate.substring(selDate.length - 4, selDate.length);
				iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			} */
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
	$('#to_date').datepicker({ 
		dateFormat: 'dd-mm-yy', 
		changeMonth: true, 
		changeYear: true,
		beforeShow: function(input, inst) { 
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});
</script>
    <script type="text/javascript" src="assets/js/asset_register/quotation_view.js"></script>
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
?>
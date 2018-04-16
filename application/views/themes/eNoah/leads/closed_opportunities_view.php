<?php
ob_start();
require (theme_url().'/tpl/header.php'); 
$userdata = $this->session->userdata('logged_in_user');
?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
</style>
<div id="content">
	<div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Lead Dashboard</h2>
			
			<a class="choice-box" onclick="advanced_filter();" >
				<span>Advanced Filters</span>
				<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>

			<!--div class="search-dropdown">
				<a class="saved-search-head">
					<p>Saved Search</p>
				</a>
				<div class="saved-search-criteria" style="display: none; ">
					<img class="dpwn-arw" src="assets/img/drop-down-arrow.png" title="" alt="" />
					<ul class="search-root">
					<li class="save-search-heading"><span>Search Name</span><span>Set Default</span><span>Action</span></li>
					<?php 
					// if(sizeof($saved_search)>0) {
						// foreach($saved_search as $searc) { 
					?>
							<li class="saved-search-res" id="item_<?php #echo $searc['search_id']; ?>">
								<span><a href="javascript:void(0)" onclick="show_search_results('<?php #echo $searc['search_id'] ?>')"><?php #echo $searc['search_name'] ?></a></span>
								<span class='rd-set-default'><input type="radio" value="<?php #echo $searc['search_id'] ?>" <?php #if ($searc['is_default']==1) { echo "checked"; } ?> name="set_default_search" class="set_default_search" /></span>
								<span><a title="Delete" href="javascript:void(0)" onclick="delete_save_search('<?php #echo $searc['search_id'] ?>')"><img alt="delete" src="assets/img/trash.png"></a></span>
							</li>
					<?php 
						// }
					// } else {
					?>
						<li id="no_record" style="text-align: center; margin: 5px;">No Save & search found</li>
					<?php
					// }
					?>
					</ul>
				</div>
			</div-->

			<div class="section-right">
				<!--search-->
				<!--div class="form-cont search-table">
					<form id="lead_search_form" name="lead_search_form" method="post">
						<input type="text" name="keyword" id="keyword" value="<?php #if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Lead No, Job Title, Name or Company' ?>" class="textfield width200px g-search" />
						<button type="submit" class="positive">Lead Search</button>			
					</form>
				</div-->
				<div class="buttons export-to-excel">
					<!--a class="export-btn">Export to Excel</a-->
					<button id="excel_lead" class="positive" type="button" >
						Export to Excel
					</button>
					<!--input type="hidden" name="search_type" value="" id="search_type" /-->
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
						<input type="hidden" name="filter" id="filter" value="filter" />
						
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">By Customer</td>
									<td class="tblheadbg">By lead Owner</td>
									<td class="tblheadbg">By Lead Assignee</td>
									<td class="tblheadbg">By Service</td>
									<td class="tblheadbg">By Industry</td>
									<td class="tblheadbg">By Lead Created Date</td>
								</tr>
								<tr>
									<td>
										<select style="width:180px" multiple="multiple" id="customer" name="customer[]">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['companyid'];?>" title="<?php echo $customer['company']; ?>"><?php echo $customer['company']; ?></option>	
										<?php } ?>
										</select> 
									</td>
									<td>
										<select style="width:180px" multiple="multiple" id="owner" name="owner[]">
											<?php foreach ($lead_owner as $owner) { 
													if(!empty($owner['first_name'])) { ?>
														<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name'].' '.$owner['last_name']; ?>"><?php echo $owner['first_name'].' '.$owner['last_name'] ?></option>
											<?php 	} 
												} 
											?>
										</select> 
									</td>
									<td>
										<select style="width:180px" multiple="multiple" id="leadassignee" name="leadassignee[]">
											<?php foreach ($lead_owner as $owner) {
													if(!empty($owner['first_name'])) { ?>		
														<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name'].' '.$owner['last_name']; ?>"><?php echo $owner['first_name'].' '.$owner['last_name']; ?></option>
											<?php 	} 
												}
											?>
										</select> 
									</td>
									<td>
										<select multiple="multiple" id="service" name="service[]" >
											<?php
												if(isset($services) && count($services)>0){
													foreach($services as $se){ ?>
														<option value="<?php echo $se['sid'] ?>" title="<?php echo $se['services']; ?>"><?php echo $se['services'] ?></option>
													<?php }
												}
											?>
										</select>
									</td>
									<td>
										<select multiple="multiple" id="industry" name="industry[]" style="width:140px" >
											<?php
												if(isset($industry) && count($industry)>0){
													foreach($industry as $ind){ ?>
														<option value="<?php echo $ind['id'] ?>" title="<?php echo $ind['industry']; ?>"><?php echo $ind['industry'] ?></option>
													<?php }
												}
											?>
										</select>
									</td>
									<td>
										From <input type="text" data-calendar="true" name="from_date" id="from_date" class="textfield" style="width:67px; margin-left: 8px;" />
										<br />
										To <input type="text" data-calendar="true" name="to_date" id="to_date" class="textfield" style="width:67px; margin-left: 22px;" />
									</td>
								</tr>
								<tr>
									<td class="tblheadbg">By Source</td>
									<td class="tblheadbg">By Region Wise</td>
									<td class="tblheadbg">By Country Wise</td>
									<td class="tblheadbg">By State Wise</td>
									<td class="tblheadbg">By Location Wise</td>
									<td class="tblheadbg">By Date Range</td>
								</tr>
								<tr>
									<td>
										<select multiple="multiple" id="lead_src" name="lead_src[]" >
											<?php
												if(isset($sources) && count($sources)>0){
													foreach($sources as $srcs){ ?>
														<option value="<?php echo $srcs['lead_source_id'] ?>" title="<?php echo $srcs['lead_source_name']; ?>"><?php echo $srcs['lead_source_name'] ?></option>
													<?php }
												}
											?>
										</select> 
									</td>
									<td>
										<select style="width:180px" multiple="multiple" id="regionname" name="regionname[]">
											<?php foreach ($regions as $reg) {
													if(!empty($reg['region_name'])) { ?>
														<option value="<?php echo $reg['regionid'] ?>" title="<?php echo $reg['region_name']; ?>"><?php echo $reg['region_name'] ?></option>
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
										From <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" style="width:78px;" />
										<br />
										To <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" style="width:78px; margin-left: 13px;" />
									</td>
								</tr>
								<tr align="right" >
									<td colspan="7"><input type="reset" class="positive input-font" name="advance" value="Reset" />
									<input type="button" class="positive input-font show-ajax-loader" name="advance" id="search_advance" value="Search" />
									<!--input type="button" class="positive input-font show-ajax-loader" name="advance" id="save_advance" value="Save & Search" /-->
									<div id = 'load' style = 'float:right;display:none;height:1px;'>
										<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
									</div>
									</td>
								</tr>
							</table>
						</div>
					</form>
				</div>
				<div id="advance_search_results" style="clear:both" >				
					<table border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable">
						<thead>
							<tr>
								<th>Action</th>
								<th>Project No.</th>
								<th>Project Title</th>
								<th>Customer</th>
								<th>Actual Worth</th>
								<th>Region</th>
								<th>Lead Owner</th>
								<th>Lead Assigned To</th>
								<th>Lead Created Date</th>
								<th style="width:100px;">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if(!empty($closed_jobs)) 
								{
									foreach($closed_jobs as $jobs) 
									{
										$view_url      = base_url().'project/view_project/'.$jobs['lead_id'];
										$view_lead_url = base_url().'project/view_project/'.$jobs['lead_id'];
							?>
										<tr id='<?php echo $jobs['lead_id'] ?>'>
											<td class="actions" align="center">
												<?php if ($this->session->userdata('viewlead')==1) { ?>
													<a href="<?php echo $view_url;?>" title='View'>
														<img src="assets/img/view.png" alt='view' >
													</a>
												<?php } ?>
												<?php 
												if (($this->session->userdata('editlead')==1 && $jobs['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2 || $jobs['lead_assign'] == $userdata['userid']) && $jobs['pjt_status']==0) { ?>					
													<a href="<?php echo base_url(); ?>welcome/edit_quote/<?php echo $jobs['lead_id'] ?>" title='Edit'>
														<img src="assets/img/edit.png" alt='edit' >
													</a>
												<?php } ?> 
												<?php
												if (($this->session->userdata('deletelead')==1 && $jobs['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1|| $userdata['role_id'] == 2) && $jobs['pjt_status']==0) { ?>
													<a href="javascript:void(0)" onclick="return deleteLeads(<?php echo $jobs['lead_id']; ?>); return false; " title="Delete" ><img src="assets/img/trash.png" alt='delete' ></a> 
												<?php } ?>
											</td>
											<td>		
												<a href="<?php echo $view_lead_url;?>"><?php echo $jobs['invoice_no']; ?></a> 
											</td>
											<td> 
												<a href="<?php echo $view_lead_url;?>"><?php echo character_limiter($jobs['lead_title'], 35) ?></a> 
											</td>
											<td><?php echo $jobs['company'].' - '.$jobs['customer_name']; ?></td>
											<td style="width:90px;">
												<?php echo $jobs['expect_worth_name'].' '.$jobs['actual_worth_amount']; ?>
											</td>
											<td><?php echo $jobs['region_name']; ?></td>
											<td><?php echo $jobs['ubfn'].' '.$jobs['ubln']; ?></td>
											<td><?php echo $jobs['ufname'].' '.$jobs['ulname']; ?></td>
											<td><?php echo date('d-m-Y',strtotime($jobs['date_created'])); ?></td>
											<td style="width:100px;">		
												<?php
													switch ($jobs['pjt_status'])
													{
														case 1:
															echo $status = '<span class=label-wip>Project In Progress</span>';
														break;
														case 2:
															echo $status = '<span class=label-success>Project Completed</span>';
														break;
														case 3:
															echo $status = '<span class=label-inactive>Inactive</span>';
														break;
														case 4:
															echo $status = '<span class=label-warning>Project Onhold</span>';
														break;
													}
												?>
											</td>
										</tr> 
							<?php 
									} 
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
	<?php 
		} else {
			echo "You have no rights to access this page";
		}
	?>
	</div>
</div>
<div id='popupGetSearchName'></div>
<script type="text/javascript">
$(function() {
	$('.data-tbl').dataTable({
		"aaSorting": [[ 1, "desc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": true,
		"bAutoWidth": false,
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
		]
	});
});
</script>
<script type="text/javascript" src="assets/js/leads/closed_opportunities_view.js"></script>
<script>

$('#excel_lead').click(function() {

	var url = site_base_url+"welcome/excelExportClosedLeads/";

	var from_date     = $("#from_date").val();
	var to_date     = $("#to_date").val();
	var customer     = $("#customer").val();
	var service      = $("#service").val();
	var lead_src     = $("#lead_src").val();
	var industry     = $("#industry").val();
	var worth	     = $("#worth").val();
	var owner 	     = $("#owner").val();
	var leadassignee = $("#leadassignee").val();
	var regionname   = $("#regionname").val();
	var countryname  = $("#countryname").val();
	var statename    = $("#statename").val();
	var locname      = $("#locname").val();
	var month_year_from_date = $("#month_year_from_date").val();
	var month_year_to_date   = $("#month_year_to_date").val();
	
	var form = $('<form action="' + url + '" method="post">' +
	  '<input type="hidden" id="token" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
	  '<input id="from_date" type="hidden" name="from_date" value="'+from_date+'" />'+
	  '<input id="to_date" type="hidden" name="to_date" value="'+to_date+'" />'+	  
	  '<input type="hidden" id="customer" name="customer" value="'+customer+'" />'+
	  '<input type="hidden" id="service" name="service" value="'+service+'" />'+
	  '<input type="hidden" id="lead_src" name="lead_src" value="'+lead_src+'" />'+
	  '<input type="hidden" id="industry" name="industry" value="'+industry+'" />'+
	  '<input type="hidden" id="owner" name="owner" value="'+owner+'" />'+
	  '<input type="hidden" id="leadassignee" name="leadassignee" value="'+leadassignee+'" />'+
	  '<input type="hidden" id="regionname" name="regionname" value="'+regionname+'" />'+
	  '<input type="hidden" name="countryname" id="countryname" value="'+countryname+ '" />' +
	  '<input type="hidden" name="locname" id="locname" value="'+locname+ '" />' +
	  '<input type="hidden" name="statename" id="statename" value="'+statename+ '" />'+
	  '<input type="hidden" name="month_year_from_date" id="month_year_from_date" value="'+month_year_from_date+ '" />'+
	  '<input type="hidden" name="month_year_to_date" id="month_year_to_date" value="'+month_year_to_date+ '" />'+
	  '</form>');
	$('body').append(form);
	$(form).submit();
	return false;

});

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
<?php
require (theme_url().'/tpl/footer.php'); 
ob_end_flush();
?>
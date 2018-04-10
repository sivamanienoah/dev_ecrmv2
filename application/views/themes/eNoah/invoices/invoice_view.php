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
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<a class="choice-box" onclick="advanced_filter();" >
				<span>Advanced Filters</span>
				<img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>
			
			<div class="search-dropdown">
				<a class="saved-search-head" >
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
				<div class="buttons export-to-excel">
					<button id="inv_excel" onclick="location.href='#'" class="positive" type="button">
						Export to Excel
					</button>
					<input type="hidden" id="val_export" name="val_export" value="<?php echo $val_export ?>" />
				</div>
			</div>
			
			<div class="clearfix"></div>
			
		</div>
		
		<?php if($this->session->userdata("success_message")):
				echo '<div id="confirm"><p>'.$this->session->userdata("success_message").'</p></div>';
				$this->session->set_userdata("success_message",'');
		 endif; ?>		
		<?php if($this->session->userdata("error_message")):
				echo '<div id="error"><p>'.$this->session->userdata("error_message").'</p></div>';
				$this->session->set_userdata("error_message",'');
		 endif; ?>		
		<?php 
		if($this->session->userdata('accesspage')==1)
		{
		?>
		<div id="filter_section">

			<div class="clear"></div>
			
			<div id="advance_search" style="padding-bottom:15px; display:none;">
				<form name="advanceFiltersDash" id="advanceFiltersDash" method="post">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
					<?php //echo '<pre>'; print_r($sales_divisions);?>
					<div style="border: 1px solid #DCDCDC;">
						<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
							<tr>
								<td class="tblheadbg">By Projects</td>
								<td class="tblheadbg">By Customers</td>
								<td class="tblheadbg">By Entity</td>
								<td class="tblheadbg">By Practices</td>								
								<td class="tblheadbg">By Date</td>
								<td class="tblheadbg">Month & Year</td>
							</tr>
							<tr>	
								<td>
									<select multiple="multiple" id="project" name="project[]" class="advfilter" style="width:200px;">
										<?php foreach($projects as $pj) { ?>
											<option value="<?php echo $pj['lead_id']; ?>"><?php echo character_limiter($pj['lead_title'], 30); ?></option>
										<?php } ?>					
									</select> 
								</td>
								<td>
									<select multiple="multiple" id="customer" name="customer[]" class="advfilter" style="width:200px;">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['companyid']; ?>"><?php echo $customer['company']; ?></option>
										<?php } ?>
									</select> 
								</td> 
								<td>
									<select multiple="multiple" id="divisions" name="divisions[]" class="advfilter" style="width: 135px;">
										<?php foreach ($sales_divisions as $division) { ?>
												<option value="<?php echo $division['div_id'] ?>"><?php echo $division['division_name']; ?></option>
										<?php } ?>
									</select> 
								</td>
								<td>
									<select multiple="multiple" id="practice" name="practice[]" class="advfilter" style="width: 99px;">
										<?php foreach ($practices as $pr) { ?>
												<option value="<?php echo $pr['id'] ?>"><?php echo $pr['practices']; ?></option>
										<?php } ?>
									</select> 
								</td>								
								<td>
									From <input type="text" data-calendar="true" name="from_date" id="from_date" class="textfield" style="width:57px;" />
									<br />
									To <input type="text" data-calendar="true" name="to_date" id="to_date" class="textfield" style="width:57px; margin-left: 13px;" />
								</td>
								<td>
									From <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" style="width:78px;" />
									<br />
									To <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" style="width:78px; margin-left: 13px;" />
								</td>
							</tr>
							<tr align="right" >
								<td colspan="6">
									<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
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
		</div>
		<div class="clear"></div>
		<div id='results'>
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
				<thead>
					<tr>
						<th>Invoice Date</th>
						<th>Month & Year</th>
						<th>Entity</th>
						<th>Practice</th>
						<th>Customer Name</th>
						<th>Project Title</th>
						<th>Project Code</th>
						<th>Milestone Name</th>
						<th>Actual Value</th>
						<th>Entity Book Value</th>
						<th>Value(<?php echo $default_currency; ?>)</th>
						<!--th>Status</th>
						<th>Action</th-->
					</tr>
				</thead>
				<tbody>
				<?php 
					$st_array = array(0=>"Pending",1=>"Payment Completed",2=>"Payment Partially Completed");
					if (is_array($invoices) && count($invoices) > 0) { ?>
					<?php foreach($invoices as $inv) { ?>
						<tr>
							<!--td><a href="<?php echo base_url().'invoice/edit_invoice/'.$inv['expectid'];?>"><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></a></td-->
							<td><?php echo date('d-m-Y', strtotime($inv['invoice_generate_notify_date'])); ?></td>
							<td><?php echo ($inv['month_year']!='0000-00-00 00:00:00') ? date('M Y', strtotime($inv['month_year'])) : ''; ?></td>
							<td><?php echo $inv['division_name']; ?></td>
							<td><?php echo $inv['practices']; ?></td>
							<td><?php echo $inv['customer']; ?></td>
							<td><a title='View' href="project/view_project/<?php echo $inv['lead_id'] ?>"><?php echo character_limiter($inv['lead_title'], 30); ?></a></td>
							<td><?php echo isset($inv['pjt_id']) ? $inv['pjt_id'] : '-'; ?></td>
							<td><?php echo $inv['project_milestone_name']; ?></td>
							<td align="right"><?php echo $inv['actual_amt']; ?></td>
							<td align="right"><?php echo $currency_names[$inv['entity_conversion_name']] .' '. sprintf('%0.2f', $inv['entity_conversion_value']); ?></td>
							<td align="right"><?php echo sprintf('%0.2f', $inv['coverted_amt']); ?></td>
							<!--td><?php #echo $st_array[$inv['received']]; ?></td>
							<td><a class="js_view_payment" rel="<?php #echo $inv['expectid'];?>" href="javascript:void(0);">View</a></td-->
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan='10' align='right'><strong>Total Value</strong></td><td align='right'><?php echo sprintf('%0.2f', $total_amt); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php 
		}
		else { 
			echo "You have no rights to access this page"; 
		}
		?>
		
	</div><!--Inner div-close here-->
</div><!--Content div-close here-->
<div id='popupGetSearchName'></div>
<script type="text/javascript" src="assets/js/invoice/invoice_data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/invoice/invoice_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
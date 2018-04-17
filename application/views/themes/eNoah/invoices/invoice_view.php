<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
?>
<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }

#content .inner {
	padding: 15px 24px;
	overflow: auto;
	clear: left;
}
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
								<?php if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) { ?>
									<td class="tblheadbg">By Project Manager</td>
								<?php } ?>
							</tr>
							<tr>	
								<td>
									<select multiple="multiple" id="project" name="project[]" class="advfilter" style="width:200px;">
										<?php foreach($projects as $pj) { ?>
											<option value="<?php echo $pj['lead_id']; ?>" title="<?php echo character_limiter($pj['lead_title'], 30); ?>"><?php echo character_limiter($pj['lead_title'], 30); ?></option>
										<?php } ?>					
									</select> 
								</td>
								<td>
									<select multiple="multiple" id="customer" name="customer[]" class="advfilter" style="width:200px;">
										<?php foreach($customers as $customer) { ?>
											<option value="<?php echo $customer['companyid']; ?>" title="<?php echo $customer['company']; ?>"><?php echo $customer['company']; ?></option>
										<?php } ?>
									</select> 
								</td> 
								<td>
									<select multiple="multiple" id="divisions" name="divisions[]" class="advfilter" style="width: 135px;">
										<?php foreach ($sales_divisions as $division) { ?>
												<option value="<?php echo $division['div_id'] ?>" title="<?php echo $division['division_name']; ?>"><?php echo $division['division_name']; ?></option>
										<?php } ?>
									</select> 
								</td>
								<td>
									<select multiple="multiple" id="practice" name="practice[]" class="advfilter" style="width: 99px;">
										<?php foreach ($practices as $pr) { ?>
												<option value="<?php echo $pr['id'] ?>" title="<?php echo $pr['practices']; ?>"><?php echo $pr['practices']; ?></option>
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
								<?php if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) { ?>
									<td colspan="4">
										<select style="width:195px;" multiple="multiple" id="pm" name="pm[]">
											<?php if(!empty($all_pm)) {
												foreach($all_pm as $pm) {												
													$pm_name = $pm['first_name'].(($pm['last_name']!='') ? ' '.$pm['last_name'].' ' : ' ').(($pm['emp_id']!='') ? '- '.$pm['emp_id'].' ' : ' ');
												?>
												<option value="<?php echo $pm['userid']; ?>" title="<?php echo $pm_name; ?>"><?php echo $pm_name; ?></option>	
												<?php } ?>
											<?php } ?>
										</select>
									</td>
								<?php } ?>
							</tr>
							<tr align="right" >
								<td colspan="7">
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
			<?php echo $this->load->view('invoices/invoice_view_grid', $invoices, true); ?>
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
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/invoice/invoice_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
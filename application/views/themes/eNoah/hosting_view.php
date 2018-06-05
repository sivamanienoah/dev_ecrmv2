<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
  
	<div class="inner hosting-section">
	<?php 
	if($this->session->userdata('accesspage')==1) {
	if(!empty($hosts)) {
		if($hosts=='HOSTS') { 
			echo '<form action="dns/submit" method="post">';
			echo'<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" />';
			echo '<select name="hostings" class="textfield" style="width:298px;">';
			foreach($hosting as $key=>$val){
				echo '<option value="'.$val['hostingid'].'">'.$val['domain_name'].'</option>';
			}
			echo '</select>';
			
			echo '<div class="buttons">
				  <button type="submit" name="update_dns" class="positive">Edit DNS</button>
				  <button type="submit" name="update_hosting" class="positive">Edit Hosting</button></div>
				  </form>';
		}
	}
	else 
	{	
	?>      <div class="inner">
		<div class="page-title-head">
			<h2 class="pull-left borderBtm">Subscription Accounts</h2>
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
<!--				<div class="form-cont search-table">
					<form id="lead_search_form" name="lead_search_form" method="post">
						<input type="text" name="keyword" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Subscription Name, Subscription Type' ?>" class="textfield width200px g-search" />
						<button type="submit" class="positive">Subscription Search</button>			
					</form>
				</div>
				search-->
                                <?php if($this->session->userdata('add')==1) { ?>
				<div class="section-right">
					<div class="buttons add-new-button">
						<button type="button" class="positive" onclick="location.href='<?php echo base_url(); ?>hosting/add_account'">
							Add New Subscription
						</button>
					</div>
				</div>
                                
			<?php } ?>
			<div class="clearfix"></div>
				
			</div>
		
	
		<?php if($this->session->userdata('accesspage')==1) { ?>
			<form id="lead_search_form" name="lead_search_form" action="" method="post" style="float:right; margin:0;">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			</form>
		
			<div>			
				<div id="advance_search" style="float:left;width:100%;">
					<form name="advanceFilters" id="advanceFilters" method="post" style="overflow:auto; height:157  px; width:100%;">
						
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
						
						<div style="border: 1px solid #DCDCDC;">
							<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
								<tr>
									<td class="tblheadbg">By Created / Modified Date</td>
									<td class="tblheadbg">By Subscription Name</td>
									<td class="tblheadbg">By Subscription Type</td>
									<td class="tblheadbg">By Customer</td>
									<td class="tblheadbg">By Subscription Status</td>
									
									
								</tr>
								<tr>	
									<td>
										From <input type="text" data-calendar="true" name="from_date" id="from_date" class="textfield" style="width:57px;" />
										<br />
										To <input type="text" data-calendar="true" name="to_date" id="to_date" class="textfield" style="width:57px; margin-left: 13px;" />
									</td>
									<td>
										<select style="width:148px" multiple="multiple" id="sub_name" name="sub_name[]">
											<?php foreach($sub_names as $sub_name) { ?>
													<option value="<?php echo $sub_name['hostingid']; ?>" title="<?php echo $sub_name['domain_name']; ?>"><?php echo $sub_name['domain_name']; ?></option>
											<?php } ?>
                                                                                                       
										</select> 
									</td>
									<td>
										<select style="width:180px" multiple="multiple" id="sub_type_name" name="sub_type_name[]">
										<?php foreach($accounts as $sub_type) { ?>
											<option value="<?php echo $sub_type['subscriptions_type_name']; ?>" title="<?php echo $sub_type['subscriptions_type_name']; ?>"><?php echo $sub_type['subscriptions_type_name']; ?></option>	
										<?php } ?>
										</select> 
									</td>  
									<td>
										<select style="width:110px" multiple="multiple" id="customer" name="customer[]">
											<?php foreach($accounts as $cus) { ?>
											<option value="<?php echo $cus['customer']; ?>" title="<?php echo $cus['customer']; ?>"><?php echo $cus['customer']; ?></option>	
										<?php } ?>
										</select> 
									</td>
									<td>
										<select style="width:110px" multiple="multiple" id="owner" name="owner[]">
											<?php foreach ($lead_owner as $owner) { 
													if(!empty($owner['first_name'])) { ?>
														<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name'].' - '.$owner['emp_id'] ?>"><?php echo $owner['first_name'] ?></option>
											<?php 	}
												} 
											?>
										</select> 
									</td>
<!--									<td>
										<select style="width:110px" multiple="multiple" id="leadassignee" name="leadassignee[]">
											<?php foreach ($lead_owner as $owner) {
													if(!empty($owner['first_name'])) { ?>		
														<option value="<?php echo $owner['userid'] ?>" title="<?php echo $owner['first_name'].' - '. $owner['emp_id'] ?>"><?php echo $owner['first_name'] ?></option>
											<?php 	}
												}
											?>
										</select> 
									</td>-->
<!--									<td colspan="2">
										<select multiple="multiple" id="service" name="service[]" >
											<?php
												if(isset($services) && count($services)>0){
													foreach($services as $se){ ?>
														<option value="<?php echo $se['sid'] ?>" title="<?php echo $se['services'] ?>"><?php echo $se['services'] ?></option>
													<?php }
												}
											?>
										</select>
									</td>-->
									
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
		
        <table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
            <thead>
                <tr>
                    <th>Subscription Name</th>
                    <th>Subscription type</th>
                    <th>Customer</th>
                    <th>Subscription Status</th>
					<th>DNS</th>
                    <th>Subscription Expiry Date</th>
                    <th>Hosting Expiry Date</th>
					<th>SSL Status</th>
					<th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if (is_array($accounts) && count($accounts) > 0) { 
				  foreach ($accounts as $account) { 
					$rem=strtotime($account['go_live_date'])-time();
					if($account['login_url']!='' && $account['login']!='' && $account['registrar_password']!='' && $account['email']!='' && $account['cur_smtp_setting']!='' && $account['cur_pop_setting']!='' && $account['cur_dns_primary_url']!='' && $account['cur_dns_primary_ip']!='' && $account['cur_dns_secondary_url']!='' && $account['cur_dns_secondary_ip']!='') 
						$dns='green';
					else if($account['login_url']!='' && $account['login']!='' && $account['registrar_password']!='') 
						$dns='orange';
					else 
						$dns='red';
				?>
                    <tr>
                        <td>
							<?php if ($this->session->userdata('edit')==1) {?><a href="hosting/add_account/update/<?php echo  $account['hostingid'] ?>"><?php echo  $account['domain_name'] ?></a><?php } else echo "Edit"; ?>
						</td>
						<td>
							<?php echo  ($account['subscriptions_type_name'])?$account['subscriptions_type_name']:'---'; ?>
						</td>
						
                        <td>
							<?php echo  $account['customer'] ?>
						</td>
                        <td>
							<?php echo  $account['domain_status'] ?>
						</td>
						<td>
							<?php if ($this->session->userdata('accesspage')==1) { ?>
								<a href="dns/go_live/<?php echo $account['hostingid'];?>" style="color:<?php echo $dns; ?>;">View</a>
							<?php } else echo $dns; ?>
						</td>
                        <td>
							<?php echo $account['domain_expiry']; ?>
						</td>
                        <td>
							<?php echo $account['expiry_date']; ?>
						</td>
						<td>
							<?php echo $account['ssl']; ?>
						</td>
						<td>
							<?php if ($this->session->userdata('edit')==1) { ?>
								<a href="hosting/add_account/update/<?php echo $account['hostingid'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'></a>
							<?php } ?>
							<?php if ($this->session->userdata('delete')==1) { ?>
								<a class="delete" href="javascript:void(0)" onclick="return delHosting(<?php echo $account['hostingid']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'></a> 
							<?php } ?>
						</td>
                    </tr>
				<?php 
				  } 
				} 
				?>
            </tbody>
        </table>
	<?php 
	} 
	?>
	</div>
	<?php 
	} 
	else 
	{
		echo "You have no rights to access this page";
	}
	?>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/hosting/hosting_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>
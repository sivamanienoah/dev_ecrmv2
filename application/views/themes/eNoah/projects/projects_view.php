<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	<div class="inner">
		<?php
		if($this->session->userdata('accesspage')==1) 
		{
		?>
		<form action="" id="pjt_search_form" name="pjt_search_form" method="post" style="float:right;">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
			<table border="0" cellpadding="0" cellspacing="0" class="search-table">
                <tr>
					<td>
                        Project Search
                    </td>
					<td>
                        <input type="text" name="keyword" id="keywordpjt" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; else echo 'Project No, Project Title, Name or Company' ?>" class="textfield width210px pjt-search" />
                    </td>
                    <td rowspan=2>
                        <div class="buttons">
                            <button type="submit" class="positive">Search</button>
                        </div>
                    </td>
                </tr>
            </table>
		</form>
		
	    <h2><?php echo $page_heading ?></h2>
		
		<a class="choice-box" onclick="advanced_filter_pjt();" style="top:10px;">
			Advanced Filters
			<img src="assets/img/advanced_filter.png" class="icon leads" />
		</a>
		
		<div id="advance_search_pjt" style="float:left; width:100%;" >
		
			<form name="advanceFilters_pjt" id="advanceFilters_pjt"  method="post">
			
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
				<table border="0" cellpadding="0" cellspacing="0" class="data-table">
				<thead>
					<tr>
						<th>By Project Status Wise</th>
						<th>By Project Manager Wise</th>
						<th>By Customer Wise</th>
					</tr>	
				</thead>
				<tbody>
				<tr>	
					<td>
						<select style="width:230px;" multiple="multiple" id="pjt_stage" name="pjt_stage[]">
							<option value="1">Project In Progress</option>
							<option value="2">Project Completed</option>
							<option value="3">Project Onhold</option>
							<option value="4">Inactive</option>
						</select> 
					</td>
					
					<td>
						<select style="width:230px;" multiple="multiple" id="pm_acc" name="pm_acc[]">
							<?php foreach($pm_accounts as $pm_acc) {?>
							<option value="<?php echo $pm_acc['userid']; ?>">
							<?php echo $pm_acc['first_name'].' '.$pm_acc['last_name']?></option>	
							<?php } ?>
						</select> 
					</td>
					
					<td>
						<select style="width:230px;" multiple="multiple" id="customer1" name="customer1[]">
							<?php foreach($customers as $customer) {?>
							<option value="<?php echo $customer['custid']; ?>"><?php echo $customer['first_name'].' '.$customer['last_name'].' - '.$customer['company']; ?></option>	
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr align="right" >
					<td colspan="5"><input type="reset" class="positive" name="advance_pjt" value="Reset" />
					<input type="submit" class="positive" name="advance_pjt" id="advance" value="Search" />
					<div id = 'load' style = 'float:right;display:none;height:1px;'>
						<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
					</div>
					</td>
				</tr>
				</tbody>
				</table>
			</form>
		</div>
		<div class="clearfix"></div>
		
		<form name="project-total-form" onsubmit="return false;" style="clear:right; overflow:visible;">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<div id="ad_filter" class="custom_dashboardfilter" style="overflow:scroll; margin-top:15px;" >
		<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:1200px !important;">
            
            <thead>
				<th>Action</th>
				<th>Project No.</th>
				<th>Project ID</th>
				<th>Project Title</th>
				<th>Customer</th>
				<th>Project Manager</th>
				<th>Planned Start Date</th>
				<th>Planned End Date</th>
				<th>Project Completion</th>
				<th>Project Status</th>
            </thead>
            
            <tbody>
				<?php
				if (is_array($records) && count($records) > 0) { 
				?>
                    <?php
					foreach ($records as $record) {
					?>
                    <tr>
						<td class="actions" align="center">
							<a href="project/view_project/<?php echo  $record['lead_id'] ?>">
								View
							</a>
							<?php
								echo ($this->session->userdata('deletePjt') == 1) ? ' | <a href="project/delete_quote/' . $record['lead_id'] . '" onclick="return window.confirm(\'Are you sure you want to delete\n' . str_replace("'", "\'", $record['lead_title']) . '?\n\nThis will delete all the items\nand logs attached to this job.\');">Delete</a>' : '';
							?>
						</td>
						
                        <td class="actions">
							<div>
								<a style="color:#A51E04; text-decoration:none;" href="project/view_project/<?php echo $record['lead_id'] ?>"><?php echo  $record['invoice_no'] ?></a> &nbsp;
							</div>
						</td>
						
						<td class="actions">
							<?php if (isset ($record['pjt_id'])) { echo $record['pjt_id']; } else { echo "-"; } ?>
						</td>
						
                        <td class="actions">
							<?php echo character_limiter($record['lead_title'], 35) ?>
						</td>
						
                        <td class="cust-data">
							<span style="color:none"><?php echo $record['cfname'] . ' ' . $record['clname'] ?></span> - <?php echo $record['company'] ?>
						</td>
						
						<td class="cust-data">
							<?php echo $record['fnm'] . ' ' . $record['lnm']; ?>
						</td>
			
						<td><?php if ($record['date_start'] == "") { echo "-"; } else { echo  date('d-m-Y', strtotime($record['date_start'])); } ?></td>
						
						<td><?php if ($record['date_due'] == "") echo "-"; else echo  date('d-m-Y', strtotime($record['date_due'])) ?></td>
						
						<td class="actions" align="center"><?php if (isset($record['complete_status'])) echo ($record['complete_status']) . " %"; else echo "-"; ?></td>
						
						<td class="actions" align="center">
							<?php
							switch ($record['pjt_status'])
								{
									case 1:
										$pjtstat = '<span class=label-wip>Project In Progress</span>';
									break;
									case 2:
										$pjtstat = '<span class=label-success>Project Completed</span>';
									break;
									case 3:
										$pjtstat = '<span class=label-warning>Project Onhold</span>';
									break;
									case 4:
										$pjtstat = '<span class=label-inactive>Inactive</span>';
									break;
								}
							 echo $pjtstat;
							 ?>
						</td>
					</tr>
					<?php
					} 
					?>
                <?php 
				} 
				else 
				{ ?>
                <tr>
                    <td align="center" colspan="9">No records available to be displayed!</td>
                </tr>
                <?php 
				} 
				?>
            </tbody>
        </table>
		</div>
		</form>
		<?php 
		} 
		else 
		{ 
			echo "You have no rights to access this page";
		}
		?>
	</div>
</div>
<script type="text/javascript" src="assets/js/tablesort.min.js"></script>
<script type="text/javascript" src="assets/js/projects/projects_view.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

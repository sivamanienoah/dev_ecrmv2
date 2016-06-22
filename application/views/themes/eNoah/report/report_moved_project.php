<?php require (theme_url().'/tpl/header.php'); ?>

<div id="content">
	 
    <div class="inner">
		<?php if($this->session->userdata('accesspage')==1) { ?>
			
	    	<form name="report_moved_project_frm" id="report_moved_project_frm" action="<?php echo $this->uri->uri_string()?>" method="post">
				
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo 	$this->security->get_csrf_hash(); ?>" />
				<div class="page-title-head">
					<h2 class="pull-left borderBtm">Project Created</h2>
					<?php if ($this->validation->error_string != '') { ?>
					<div class="form_error">
						<?php echo  $this->validation->error_string ?>
					</div>
					<?php } ?>
					
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

	            <div class="clear"><div> 
	            <div id="advance_search" style="display:none;">
				
				<table class="layout">
					<tr>
						<td>
							* From Date
						</td>
						<td>
							<input type="text" name="project_search_start_date" id ="project_search_start_date" class="textfield pick-date width100px" autocomplete = 'off' />
						</td>
						<td>
							* To Date
						</td>	
						<td>
							<input type="text" name="project_search_end_date" id ="project_search_end_date"class="textfield pick-date width100px" autocomplete = 'off' />
						</td>						
					</tr>
					<tr>
						<td colspan="2">
								<div id="report_error_msg" style="color:#cc0000;"></div>
						</td>
					</tr>
	            </table>
	            <div style="border: 1px solid #DCDCDC;">
				<table cellpadding="0" cellspacing="0" class="data-table" >
					<thead>
						<tr>
							<th>By Practices</th>
							<th>By Entity</th>
						</tr>	
					</thead>				
					<tr>	
						<td>
							<select style="width:250px;" multiple="multiple" id="practices" name="practices[]">
								<?php foreach($practices as $pract) {?>
								<option value="<?php echo $pract['id']; ?>"><?php echo $pract['practices'];?></option>	
								<?php } ?>
							</select>
						</td>
						<td>
							<select multiple="multiple" id="divisions" name="divisions[]" class="advfilter" style="width:250px;">
								<?php foreach ($sales_divisions as $division) { ?>
									<option value="<?php echo $division['div_id'] ?>"><?php echo $division['division_name']; ?></option>
								<?php } ?>
							</select> 
						</td>
					</tr>
					<tr align="right" >
						<td colspan="6"><input type="reset" class="positive" name="advance" value="Reset" />
							<input type="submit" class="positive" name="advance" id = 'advance' value="Search" />
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
<script type="text/javascript" src="assets/js/report/report_moved_project.js"></script>
<?php require (theme_url(). '/tpl/footer.php'); ?>
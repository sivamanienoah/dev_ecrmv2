<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading ?></h2>
			<a class="choice-box js_advanced_filter">
				<span>Advanced Filters</span><img src="assets/img/advanced_filter.png" class="icon leads" />
			</a>
			<div class="clearfix"></div>
		</div>

        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
 
		<div id="advance_filters" style="float:left; display:none;width:100%;" >
		
				<form name="dmssearch" id="dmssearch"  method="post">
				
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				
					<table cellspacing="0" cellpadding="0" border="0" class="search-table">
						<tbody><tr>
							<td>
								Document Name:
							</td>
							<td>
								<input type="text" class="textfield width200px" value="<?php echo $keyword;?>" id="keyword" name="keyword" style="color: rgb(119, 119, 119);">
							</td>
							
							<td>
								Tag Name:
							</td>
							<td>
								<input type="text" class="textfield width200px" value="<?php echo $keyword;?>" id="tag_keyword" name="tag_keyword" style="color: rgb(119, 119, 119);">
							</td>
							<!--<td>
								<div class="buttons">
									<button class="positive" type="submit">Search</button>
									<button onclick="window.location='<?php echo base_url().'dms_search';?>'" class="positive" type="reset">Reset</button>
								</div>
							</td> -->
						</tr>
					</tbody>
					</table>				
				
					<table border="0" cellpadding="0" cellspacing="0" class="data-table">
					<thead>
						<tr>							
							<th>By Customer Wise</th>
							<th>By Lead/Project Wise</th>							
							<th>By File Type Wise</th>
							<th>By Created Date</th>
						</tr>	
					</thead>
					<tbody>
					<tr>
						<td>
							<select style="width:210px;" multiple="multiple" id="customers" name="customers[]">
								<?php if(count($customers)>0):foreach($customers as $customer) {?>
								<option value="<?php echo $customer['companyid']; ?>"><?php echo $customer['company']; ?></option>	
								<?php } endif;?>
							</select>
						</td>
						<td>
							<select style="width:210px;" multiple="multiple" id="projects" name="projects[]">
								<?php if(count($projects)>0):foreach($projects as $project) {?>
								<option value="<?php echo $project->lead_id; ?>"><?php echo $project->lead_title; ?></option>	
								<?php } endif;?>
							</select>
						</td>
						<td>
							<select style="width:210px;" multiple="multiple" id="extension" name="extension[]">
								<?php if(count($extension)>0):foreach($extension as $ext) {?>
								<option value="<?php echo $ext['extension']; ?>"><?php echo $ext['extension']; ?></option>	
								<?php } endif;?>
							</select>
						</td>
						<td>
							 
							From <input type="text" name="from_date" id="from_date" class="pick-date textfield" style="width:57px;" />
							To <input type="text" name="to_date" id="to_date" class="pick-date textfield" style="width:57px;" />
						</td>
					</tr>
					</tbody>						 
					<tbody>						 
						<tr align="right" >
							<td colspan="5"><input type="reset" class="positive js_reset input-font" name="advance_pjt" value="Reset" />
							<input type="submit" class="positive input-font" name="advance_pjt" id="advance" value="Search" />
							<div id = 'load' style = 'float:right;display:none;height:1px;'>
								<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
							</div>
							</td>
						</tr>
					</tbody>
					</table>
				</form>
				<br />
			</div>	
			<div class="clearfix"></div>
			<div id="ajax_loader" style="margin:20px;display:none" align="center">
				Loading Content.<br><img alt="wait" src="<?php echo base_url().'assets/images/ajax_loader.gif'; ?>"><br>Thank you for your patience!
			</div>	
			<div id="default_view">
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%;">
            <thead>
                <tr>
					<th>Created On</th>
                    <th>File Name</th>
                    <th>Tags</th>
                    <th>Client</th>
                    <th>Lead/Project</th>
					<th>Folder</th>					
					<th>Type</th>
					<th>Size</th>
					<th>Created By</th>
                </tr>
            </thead>
            <tbody>
			<?php
			// echo '<pre>';print_r($files);exit;
			if (is_array($files) && count(files) > 0) { 
				foreach ($files as $file) {?>
				 
					<?php
					$file_ext  = end(explode('.',$file['lead_files_name']));
					$file_full_path = UPLOAD_PATH.'files/'.$file['lead_id'].'/'.$file['lead_files_name'];
					$filesize = filesize($file_full_path);
					$filesize_bytes = formatSizeUnits($filesize);?>			
					<tr>
						<td><?php echo date("d-M-Y",strtotime($file['lead_files_created_on']));?></td>
						<td>
							<?php if(file_exists($file_full_path)){ ?>
									<a onclick="download_files('<?php echo $file['lead_id']; ?>','<?php echo $file['lead_files_name']; ?>'); return false;"><?php echo $file['lead_files_name'];?></a>
									
								<?php }else{ 
									echo $file['lead_files_name'];
								} ?>
						</td>
						<td><?php echo $file['tag_names'];?></td>
						<td><?php echo $file['company'].' - '.$file['cust_firstname'].' '.$file['cust_lastname'];?></td>
						<td><?php echo $file['lead_title'];?></td>
						<td><?php echo is_numeric($file['folder_name'])?"Root":$file['folder_name'];?></td>
						<td><?php echo $file_ext;?></td>
						<td><?php echo $filesize_bytes;?></td>
						<td><?php echo $file['first_name'].' '.$file['last_name'];?></td>
					</tr>	
			<?php } }?>
			</tbody>
			</table>
			</div>
        <?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/dms_search.js"></script>
<?php require (theme_url().'/tpl/footer.php'); ?>

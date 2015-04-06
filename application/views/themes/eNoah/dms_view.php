<?php require (theme_url().'/tpl/header.php'); ?>
<div id="content">
    <div class="inner">
        <?php  	if($this->session->userdata('accesspage')==1) {   ?>
		<div style="padding-bottom: 10px;">
			<div style="width:100%; border-bottom:1px solid #ccc;"><h2 class="pull-left borderBtm">DMS Search</h2>
			<div class="clearfix"></div>
			</div>
		</div>
        <div class="dialog-err" id="dialog-err-msg" style="font-size:13px; font-weight:bold; padding: 0 0 10px; text-align:center;"></div>
		<form style="float:left; margin:0;" method="post" action="<?php echo base_url().'dms_search/search/';?>" name="dms_search_form" onsubmit="return check_search_form();" id="dms_search_form">
			<table cellspacing="0" cellpadding="0" border="0" class="search-table">
				<tbody><tr>
					<td>
						Document Name:
					</td>
					<td>
						<input type="text" class="textfield width200px" value="<?php echo $keyword;?>" id="keyword" name="keyword" style="color: rgb(119, 119, 119);">
					</td>
					<td>
						<div class="buttons">
							<button class="positive" type="submit">Search</button>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
			<input type="hidden" name="ci_csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		</form>
		<?php if($keyword){?>
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl1 dashboard-heads dataTable" style="width:100%">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Client</th>
                    <th>Project</th>
					<th>Folder</th>
					<th>Created On</th>
					<th>Type</th>
					<th>Size</th>
					<th>Created By</th>
                </tr>
            </thead>
            <tbody>
			<?php
			if (is_array($files) && count(files) > 0) { 
				foreach ($files as $file) { 				 
					$file_ext  = end(explode('.',$file['lead_files_name']));
					$file_full_path = UPLOAD_PATH.'files/'.$file['lead_id'].'/'.$file['lead_files_name'];
					$filesize = filesize($file_full_path);
					$filesize_bytes = formatSizeUnits($filesize);?>			
					<tr>
						<td>
							<?php if(file_exists($file_full_path)){								
									echo anchor(site_url('project/download_file/'.$file['lead_id'].'/'.$file['lead_files_name']),$file['lead_files_name']);
								}else{ 
									echo $file['lead_files_name'];
								} ?>
						</td>
						<td><?php echo $file['company'];?></td>
						<td><?php echo $file['lead_title'];?></td>
						<td><?php echo $file['folder_name'];?></td>
						<td><?php echo date("d-m-Y",strtotime($file['lead_files_created_on']));?></td>
						<td><?php echo $file_ext;?></td>
						<td><?php echo $filesize_bytes;?></td>
						<td><?php echo $file['first_name'].' '.$file['last_name'];?></td>
					</tr>	
			<?php } }?>
			</tbody>
			</table>
		<?php } ?>
        <?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript">
	function check_search_form()
	{
		var keyword = $.trim($("#keyword").val());
		if(keyword ==''){
			alert("Please enter any keyword to search!")
			return false;
		}else if(keyword.length<=3){
			alert("Please enter atleat 3 letters to search!")
			return false;
		}
	}
$(function() {
	$('.data-tbl1').dataTable({
		"aaSorting": [[ 0, "asc" ]],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"bInfo": true,
		"bPaginate": true,
		"bProcessing": true,
		"bServerSide": false,
		"bLengthChange": true,
		"bSort": true,
		"bFilter": false,
		"bAutoWidth": true,	
	});
});	
	
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

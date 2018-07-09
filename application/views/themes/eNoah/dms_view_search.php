<div id="default_view">
			<table border="0" cellpadding="0" cellspacing="0" class="data-tbl1 dashboard-heads dataTable" style="width:100%">
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
			if (is_array($files) && count(files) > 0) { 
				foreach ($files as $file) {
					//echo '<pre>';print_r($file);
					$file_ext  = end(explode('.',$file['lead_files_name']));
					$file_full_path = UPLOAD_PATH.'files/'.$file['lead_id'].'/'.$file['lead_files_name'];
					$filesize = filesize($file_full_path);
					$filesize_bytes = formatSizeUnits($filesize);?>			
					<tr>
						<td><?php echo date("d-M-Y",strtotime($file['lead_files_created_on']));?></td>
						<td>
							<?php if(file_exists($file_full_path)){		?>
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
<script type="text/javascript">
function download_files(job_id,f_name){
	window.location.href = site_base_url+'project/download_file/'+job_id+'/'+f_name;
}
	
$(function() {	
	$('.data-tbl1').dataTable({
		"aaSorting": [[ 2, "asc" ]],
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
		"aoColumns": [{"sWidth":"5%"},{"sWidth":"15%"},{"sWidth":"25%"},{"sWidth":"25%"},{"sWidth":"10%"},{"sWidth":"5%"},{"sWidth":"6%"},{"sWidth":"10%"}]
	});
});	
</script>
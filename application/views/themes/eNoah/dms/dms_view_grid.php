<?php 
$this->load->helper('text'); 
$this->load->helper('file');
?>
<table id="list_file_tbl-no-need" border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl-no-need dashboard-heads dataTable"><thead><tr><th><input type="checkbox" id="file_chkall" value="checkall"></th><th>File Name</th><th>Created On</th><th>Type</th><th>Size</th><th>Created By</th></tr></thead>
<?php
if(!empty($file_array)) {
?>
	<tbody>
<?php
	foreach($file_array as $rec) {
		list($fname, $fcreatedon, $ftype, $fcreatedby, $file_id) = explode('<=>',$rec);
		?>
			<tr>
			<?php
			if($ftype == 'File') {
				$file_sz = '';
				$file_info = get_file_info($f_dir.$fname);
				$kb = 1024;
				$mb = 1024 * $kb;
				if ($file_info['size'] > $mb) {
				  $file_sz = round($file_info['size']/$mb, 2);
				  $file_sz .= ' Mb';
				} else if ($file_info['size'] > $kb) {
				  $file_sz = round($file_info['size']/$kb, 2);
				  $file_sz .= ' Kb';
				} else {
				  $file_sz = $file_info['size'] . ' Bytes';
				}
				$file_ext  = end(explode('.',$fname));
			?>
				<td class='td_filechk'>
					<input type='hidden' value='file'>
					<input type='checkbox' class='file_chk' file-type='file' value=<?php echo $file_id; ?> > </td>
				<td>
					<input type="hidden" id="<?php echo "file_".$file_id ?>" value=<?php echo $fname; ?> />
					<a onclick="download_files_id('<?php echo $file_id; ?>'); return false;"><?php echo $fname ?></a>
				</td>
				<td><?php echo date('d-m-Y',strtotime($fcreatedon)); ?> </td>
				<td><?php echo $file_ext; ?></td>
				<td><?php echo $file_sz; ?></td>
				<td><?php echo $fcreatedby; ?></td>
				<?php 
			} else {
			?>
				<td>
					<input type='hidden' value='folder'>
					<input type='checkbox' file-type='folder' class='file_chk' value=<?php echo $file_id; ?> >
				</td>
				<td>
					<a class=edit onclick="getDmsData('<?php echo $file_id ?>'); return false;" ><img src="assets/img/directory.png" alt=directory>&nbsp;<?php echo $fname; ?></a>
				</td>
				<td><?php echo date('d-m-Y',strtotime($fcreatedon)); ?></td>
				<td><?php echo $ftype; ?></td>
				<td></td>
				<td><?php echo $fcreatedby; ?></td>
			<?php	
			}
			?>
			</tr>
<?php
	}
} else {
?>
	<tr><td colspan=6 align='center'><b>No Records.</b></td></tr>
<?php
}
?>
</tbody></table>
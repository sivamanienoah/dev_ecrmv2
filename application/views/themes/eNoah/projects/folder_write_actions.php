<?php 
$this->load->helper('lead_helper');
$folder_rights = get_folder_access($lead_id, $folder_id, $user_id);
?>
<?php if (($chge_access == 1 && $pjt_status != 2) || ($folder_rights == 2 && $pjt_status != 2)) { ?>
	<form name="ajax_file_upload" class="pull-left pad-right">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<div id="upload-container">
				<img src="assets/img/uploads.png" alt="Browse" title="Browse" class="icon-width" id="upload-decoy" />
				<input type="file" title='Upload' class="textfield" multiple id="ajax_file_uploader" name="ajax_file_uploader[]" onchange="return runAjaxFileUpload();" />
			</div>
	</form>
	<div class="pull-left pad-right">
		<a title="Add Folder" href='javascript:void(0)'  onclick="create_folder(<?php echo $lead_id; ?>,<?php echo $folder_id; ?>); return false;"><img src="assets/img/add_folders.png" class="icon-width" alt="Add Folder" ></a>
	</div>
	<div class="pull-left pad-right">
		<a title="Move All" onclick="moveAllFiles(); return false;" ><img src="assets/img/document_move.png" class="icon-width" alt="Move All"></a>
	</div>
	<div class="pull-left pad-right">
		<a title="Delete All" onclick="deleteAllFiles(); return false;"  ><img src="assets/img/delete_new.png" class="icon-width" alt="Delete"></a>
	</div>
<?php } ?>
<?php if(($chge_access == 1 && $pjt_status != 2)) { ?>
<div class="pull-left pad-right">
	<a title="Edit Folder Permissions" onclick="editFolderPermissions(<?php echo $lead_id; ?>); return false;"  ><img src="assets/img/permissions.png" class="icon-width" alt="Edit Folder Permissions"></a>
</div>
<?php } ?>
<?php 
$this->load->helper('custom_helper');
$dmsAdminAccess = get_dms_access($type=0);
$userdata = $this->session->userdata('logged_in_user');

if(($dmsAdminAccess == 1) || ($userdata['userid']==59))
$write_access = 2;
else
$write_access = $dmsFolderAccess;

?>
<?php if(($folder_id != 0) && ($write_access==2)) { ?>
	<form name="ajax_file_upload" class="pull-left pad-right">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<div id="upload-container">
				<img src="assets/img/uploads.png" alt="Browse" title="Browse" class="icon-width" id="upload-decoy" />
				<input type="file" title='Upload' class="textfield" multiple id="ajax_file_uploader" name="ajax_file_uploader[]" onchange="return runAjaxFileUpload();" />
			</div>
	</form>
<?php } ?>
<?php if(($dmsAdminAccess == 1) || ($userdata['userid']==59)) { ?>
	<div class="pull-left pad-right">
		<a title="Add Folder" href='javascript:void(0)'  onclick="create_dms_folder(<?php echo $folder_id; ?>); return false;"><img src="assets/img/add_folders.png" class="icon-width" alt="Add Folder" ></a>
	</div>
	<div class="pull-left pad-right">
		<a title="Move All" onclick="moveAllFiles(); return false;" ><img src="assets/img/document_move.png" class="icon-width" alt="Move All"></a>
	</div>
<?php } ?>
<?php if($write_access==2) { ?>	
	<div class="pull-left pad-right">
		<a title="Delete All" onclick="deleteAllFiles(); return false;"  ><img src="assets/img/delete_new.png" class="icon-width" alt="Delete"></a>
	</div>
<?php } ?>
<?php if(($folder_id == 0) && ($write_access==2)) { ?>
<div class="pull-left pad-right">
	<a title="Edit Folder Permissions" onclick="editFolderPermissions(); return false;"  ><img src="assets/img/permissions.png" class="icon-width" alt="Edit Folder Permissions"></a>
</div>
<?php } ?>
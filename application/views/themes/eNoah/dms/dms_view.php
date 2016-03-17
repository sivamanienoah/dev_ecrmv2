<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata  = $this->session->userdata('logged_in_user');
$this->load->helper('custom_helper');
$dmsAccess = get_dms_access($type=1);
$dmsAdminAccess = get_dms_access($type=0);
?>
<div id="content">
	<div class="inner">
	<?php #if(($dmsAccess==1) || ($dmsAdminAccess==1) || ($userdata['role_id']==1)) { ?>
	<?php if($this->session->userdata('accesspage')==1) { ?>
		<div class="page-title-head">
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
		</div>
		<div class="clear"></div>
		<input type="hidden" name="hfolder_id" id="hfolder_id" value="<?php echo $hfolder_id; ?>">
		<div id='results'>
			<div id="file_breadcrumb"></div>
			<div class="pull-left pad-right">
				<form id="file_search">
					<span class="pull-left">
						<label>Search File or Folder</label> <input type="text" class="textfield" id="search_input" value="" />
						<button class="positive" onclick="searchFileFolder(); return false;" style="margin:0 0 0 5px;" type="submit">Search</button>
					</span>
					<span class="pull-left">
						<button class="resetBtn" onclick="load_root(); return false;" style="margin:0 0 0 5px;" type="reset">Reset</button>
					</span>
				</form>
			</div>
			<div class="pull-left pad-right" id="files_actions"></div>
			<div id="list_file"></div>
		</div>
	<?php 
	} else { 
		echo "You have not mapped to collateral folders.";
	}
	?>
	</div><!--Inner div-close here -->
</div><!--Content div-close here -->
<form id="create-folder" onsubmit="return false;">
	<div id='af_successerrmsg' class='succ_err_msg'></div>
	<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td colspan="2"><div id='af_name'><strong><h3>Create Folder</h3></strong></div></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type='hidden' name='afparent_id' id='afparent_id' value=''>
			</td>
		</tr>
		<tr>
			<td valign="top" width="80"><label>Parent</label></td>
			<td>
				<select name='add_destiny' id="add_file_tree">
					<option value=''>Select</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" width="80"><label>New Folder</label></td>
			<td><input type="text" name="new_folder" id="new_folder" value="" class="textfield"></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="buttons"><button type="submit" class="positive" onclick="add_folder();">Add</button></div>
				<div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
			</td>
		</tr>
	</table>
</form>
<form id="moveallfile" onsubmit="return false;">
	<div id='all_mf_successerrmsg' class='succ_err_msg'></div>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="4"><strong><h3>Move</h3></strong></td>
		</tr>
		<tr>
			<td colspan="4">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<input type='hidden' name='mov_folder' id='mov_folder' value=''>
				<input type='hidden' name='mov_file' id='mov_file' value=''>
			</td>
		</tr>
		<tr>
			<td valign="top" width="80">Move to</td>
			<td colspan="3">
				<select name='move_destiny' id="file_tree_all">
					<option value=''>Select</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="buttons"><button type="submit" class="positive" onclick="move_all_files();">Move</button></div>
				<div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
			</td>
		</tr>
	</table>
</form>
<!-- Edit folder permissions start. -->
<form id="edit-folder-permissions" onsubmit="return false;" style="display: none; width: 900px; height: 600px; overflow: scroll; top: 4%; left: 4%;"></form>
<!-- Edit folder permissions end. -->
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/dms/dms_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
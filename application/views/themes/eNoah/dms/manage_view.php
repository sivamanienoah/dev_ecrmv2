<?php
require (theme_url().'/tpl/header.php');
?>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<?php
$userdata 		= $this->session->userdata('logged_in_user');
$this->load->helper('custom');
$dmsAccess 		= get_dms_access($type=0);
$show_disable 	= true;
?>
<div id="content">
	<div class="inner inner-manageView">
	<?php if(($dmsAccess==1) || ($userdata['role_id']==1)) { $show_disable = false; } ?>
	<?php if($this->session->userdata('accesspage')==1) { ?>
	<div class="clearfix">
		<div class="title_managview">
			<h2 class="pull-left borderBtm"><?php echo $page_heading; ?></h2>
			<div class="clearfix"></div>
		</div>
	</div>
	<?php
		$dms_admin_array = array();
		if(count($dms_admin) > 0 && !empty($dms_admin)){
			foreach($dms_admin as $row)
			$dms_admin_array[] = $row['user_id'];
		}
		$dms_users_array = array();
		if(count($dms_users) > 0 && !empty($dms_users)){
			foreach($dms_users as $urow)
			$dms_users_array[] = $urow['user_id'];
		}
	?>
	
	<div class="pull-left mange-colteralrow">
		<label class="dms_admin">Collateral Admin</label>
		<div class="manage-texaarea">
			<div class="manage-selectarea">
				<select <?php if($show_disable) { echo 'disabled="disabled"'; } ?> multiple="multiple" class="chzn-select"  id="dms_admin" name="dms_admin">
					<?php if(!empty($all_users)) { ?>
						<option value="">Select</option>
						<?php foreach($all_users as $rec) {
								$selected = (in_array($rec['userid'], $dms_admin_array))?'selected="selected"':''; ?>
								<option <?php echo $selected; ?> value="<?php echo $rec['userid']?>"><?php echo $rec['first_name'].' '.$rec['last_name'];?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
			<?php if(($dmsAccess==1) || ($userdata['role_id']==1)) { ?>
				<div class="buttons">
					<button onclick="setDmsMembers('dms_admin'); return false;" style="margin:0 0 0 5px;" id="dms_admin_members" class="positive" type="submit">Set</button>
					<div class="error-msg" id="dms_admin_msg"></div>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="pull-left mange-colteralrow">
		<label class="dms_users">Collateral Users</label>
		<div class="manage-texaarea">
			<div class="manage-selectarea">
				<select <?php if($show_disable) { echo 'disabled="disabled"';} ?> multiple="multiple" class="chzn-select"  id="dms_users" name="dms_users">
					<?php if(!empty($all_users)) { ?>
						<option value="">Select</option>
						<?php foreach($all_users as $rec) {
								$selected = (in_array($rec['userid'], $dms_users_array))?'selected="selected"':'';?>
								<option <?php echo $selected; ?> value="<?php echo $rec['userid']?>"><?php echo $rec['first_name'].' '.$rec['last_name'];?></option>
							<?php } ?>
					<?php } ?>
				</select>
			</div>
			<?php if(($dmsAccess==1) || ($userdata['role_id']==1)) { ?>
			<div class="buttons">
				<button onclick="setDmsMembers('dms_users'); return false;" style="margin:0 0 0 5px;" id="dms_admin_members" class="positive" type="submit">Set</button>
				<div class="error-msg" id="dms_users_msg"></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php 
	} else { 
		echo "You have no rights to access this page"; 
	} 
	?>
	</div><!--Inner div-close here -->
</div><!--Content div-close here -->
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script>
$(function(){
	var config = {
		'.chzn-select'           : {},
		'.chzn-select-deselect'  : {allow_single_deselect:true},
		'.chzn-select-no-single' : {disable_search_threshold:10},
		'.chzn-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chzn-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
});
</script>
<script type="text/javascript" src="assets/js/dms/manage_view.js"></script>
<?php
require (theme_url(). '/tpl/footer.php');
?>
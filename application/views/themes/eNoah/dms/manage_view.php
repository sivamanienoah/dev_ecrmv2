<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<?php
ob_start();
require (theme_url().'/tpl/header.php');
$userdata = $this->session->userdata('logged_in_user');
$this->load->helper('custom_helper');
$dmsAccess = get_dms_access($type=0);
?>
<div id="content">
	<div class="inner">
	<?php if(($dmsAccess==1) || ($userdata['userid']==59)) { ?>
	<div style="padding-bottom: 10px;">
		<div style="width:100%; border-bottom:1px solid #ccc;">
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
	
	<div class="pull-left">
		<label class="dms_admin">Collateral Admin</label>
		<select multiple="multiple" class="chzn-select"  id="dms_admin" name="dms_admin">
			<?php if(!empty($all_users)):?>
					<option value="">Select</option>
					<?php foreach($all_users as $rec):
							$selected = (in_array($rec['userid'], $dms_admin_array))?'selected="selected"':'';?>
							<option <?php echo $selected; ?> value="<?php echo $rec['userid']?>"><?php echo $rec['first_name'].' '.$rec['last_name'];?></option>
					<?php endforeach;?>
			<?php endif; ?>
		</select>
		<div class="">
			<div class="buttons">
				<button onclick="setDmsMembers('dms_admin'); return false;" style="margin:0 0 0 5px;" id="dms_admin_members" class="positive" type="submit">Set</button>
				<div class="error-msg" id="dms_admin"></div>
			</div>
		</div>
	</div>
	<div class="pull-left">
		<label class="dms_users">Collateral Users</label>
		<select multiple="multiple" class="chzn-select"  id="dms_users" name="dms_users">
			<?php if(!empty($all_users)):?>
					<option value="">Select</option>
					<?php foreach($all_users as $rec):
							$selected = (in_array($rec['userid'], $dms_users_array))?'selected="selected"':'';?>
							<option <?php echo $selected; ?> value="<?php echo $rec['userid']?>"><?php echo $rec['first_name'].' '.$rec['last_name'];?></option>
					<?php endforeach;?>
			<?php endif; ?>
		</select>
		<div class="">
			<div class="buttons">
				<button onclick="setDmsMembers('dms_users'); return false;" style="margin:0 0 0 5px;" id="dms_admin_members" class="positive" type="submit">Set</button>
				<div class="error-msg" id="dms_users"></div>
			</div>
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
<script type="text/javascript" src="assets/js/dms/manage_view.js"></script>
<script>

</script>
<?php
require (theme_url(). '/tpl/footer.php');
ob_end_flush();
?>
<?php require (theme_url().'/tpl/header.php'); ?>
<style type="text/css">
#domain-expiry-date {
		display:none;
}
</style>
<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript">
$(document).ready(function(){
		$('input.pick-date').datepicker({dateFormat: 'dd-mm-yy'});
	});
</script>
<div id="content">
    <div id="left-menu">
		<a href="hosting/">Back To Hosting</a><a  class="active" href="hosting/due_date/<?php echo $hostingid; ?>">Package Due Date</a>
	</div>
    <div class="inner">
	<?php if ($this->session->userdata('accesspage==1')) { ?>
    	<form action="<?php echo base_url(); ?>hosting/due_date/<?php echo $hostingid; ?>" method="post">
		
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		
		<h4>Package Due Date<?php if(!empty($pack[0]['domain_name'])) echo '  for '.$pack[0]['domain_name']; ?></h4>
		<p>&nbsp;</p>
  		<table class="layout">
				<tr>
					<td>Package:</td>
					<td>
						<select name="packageid" class="textfield width200px" onchange="self.location='<?php echo base_url(); ?>hosting/due_date/<?php echo $hostingid; ?>/'+this.value">
						<option value="" disabled="disabled" selected="selected">Select</option>
						<?php
							foreach ($pack as $key => $value) {
								($value['packageid_fk']==$packageid?$s=' selected="selected"':$s='');
								echo '<option value="'.$value['packageid_fk'].'"'.$s.'>'.$value['package_name'].'</option>';
							} ?>
						</select> *
					</td>
                </tr>
				<tr>
					<td>Hosting Due Date:</td>
					<?php
					$t='';
					foreach ($pack as $key => $value) {
						if($value['packageid_fk']!=$packageid) continue;
						if($value['due_date']=='0000-00-00') continue;
						if(strtotime($value['due_date'])==0) $t='';
						else $t=date('d-m-Y',strtotime($value['due_date']));
						
					}
					?>
					<td><input type="text" id="due_date" name="due_date" class="textfield width200px pick-date" value="<?php echo $t; ?>" autocomplete="off"/> </td>
				</tr>
                <tr>
					<td>&nbsp;</td>
					<td>
                        <div class="buttons">
							<button type="submit" name="Add_duedate" class="positive" value="edit">
								Update
							</button>
						</div>
                    </td>
    			</tr>
            </table>
		</form>
		
		 
        <table border="0" cellpadding="0" cellspacing="0" class="data-table">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
			<?php
			if (!empty($pack)) { 
				foreach ($pack as $v) { 
				?>
				<tr>
					<td><a href="hosting/due_date/<?php echo  $v['hostingid'].'/'.$v['package_id'] ?>"><?php echo  $v['package_name'] ?></a></td>
					<td><?php echo ($v['due_date']!='0000-00-00'?date('d-m-Y',strtotime($v['due_date'])):''); ?></td>
				</tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" align="center">No records available to be displayed!</td>
                    </tr>
                <?php } ?>
            </tbody>
            
        </table>
	<?php } else echo "You have no rights to access this page"; ?>	
	</div>
</div>
<?php require (theme_url().'/tpl/footer.php'); ?>

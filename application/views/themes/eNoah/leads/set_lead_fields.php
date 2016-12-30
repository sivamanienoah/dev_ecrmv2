<?php 
$usernme = $this->session->userdata('logged_in_user'); 
?>
<div id="msg"></div>

	<div class="field-swap">
	<form name="set_dashboard_fields" id="set_dashboard_fields" method="post" onsubmit="return false;">
		<input id="user_id" type="hidden" name="user_id" value="<?php echo $usernme['userid']; ?>" />
		<input id="oldfields" type="hidden" name="oldfields" value="<?php echo $oldfields; ?>" />
		
		<div class="field-swap-left">
			<span>Available Fields</span><br />
			<select multiple="multiple" name="base_select" id="base_select" style="width: 230px;">
				<?php echo $base_select; ?>
			</select>
		</div>
		<div style="float:left;padding:0 10px;margin:60px 0 0 0;">
		
			<input type="button" id="add" class="add-member" value="&gt;&gt;" /><br />
			<input type="button" id="remove" class="remove-member" value="&lt;&lt;" />
			<input type="hidden" value ="" id="newfields" name="newfields"/>
		
		</div>
		<div class="field-swap-right">
			<span>Dashboard Fields</span><br />
			<select multiple="multiple" name="new_select" id="new_select" style="width: 230px;" >
				<?php echo $old_select; ?>
			</select>
		</div>
		
		
		<div class="buttons" style="clear:both;">
			<button type="submit" class="positive" id="positiveSelectBox" onclick="setLeadFields(); return false;">Set </button>
			<a href="javascript:void(0)" onclick="form_cancel()">Cancel</a>
		</div>
		
</form>
</div>

<script>
/*Select boxs*/
$(function() {
	$('#add').click(function() { 
		$('#newfields').val($('#base_select').val());	
		return !$('#base_select option:selected').remove().appendTo('#new_select'); 
	});  
	$('#remove').click(function() { 
		$('#newfields').val($('#new_select').val());
		return !$('#new_select option:selected').remove().appendTo('#base_select');
	});
	$('#base_select').dblclick(function() {
		$('#newfields').val($('#base_select').val());
		return !$('#base_select option:selected').remove().appendTo('#new_select');
	});
	$('#new_select').dblclick(function() {
		$('#newfields').val($('#new_select').val());
		return !$('#new_select option:selected').remove().appendTo('#base_select');
	});
});

function form_cancel() {
    $.unblockUI();
    return false;
}

function setLeadFields() {
	$('#new_select option').each(function(i) {
		$(this).prop("selected", true);
	});
 
	var newselect = $('#new_select').val();
	// alert(newselect)
	if(newselect == null){
		return false;
	}
	// alert(site_base_url);
	var params = {'user_id':$("#user_id").val(),'new_select':newselect};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type: 'POST',
		url: site_base_url+'welcome/save_lead_fields/',
		data: params,
		dataType: 'json',
		beforeSend: function(){
			$('#positiveSelectBox').text('Saving..');
			$('#positiveSelectBox').prop('disabled', true);
		},
		success: function(data) {
			if(data.result=='success'){
				$('#msg').html('<span class="ajx_success_msg">Saved Successfully</span>');
				// setTimeout('timerfadeout()', 2000);
				setTimeout(function () { location.reload(true) }, 2000);
			}
		}
	});
	
}
</script>
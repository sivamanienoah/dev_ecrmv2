<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab" id="add_container_close"></div>
	<form name="bk_add_currency_form" id="bk_add_currency_form" method="post" onsubmit="return false;" class="layout">
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<?php #echo "<pre>"; print_r($curr_data); exit; ?>
		<table>
			<tr>
				<td><label>Financial Year:</label></td>
				<td>
					<select name="financial_year" id="financial_year" class="textfield" onchange="getCurFinanYrExist(this.value)" style="width: 90px;">
					<option value ="">Select</option>
					<?php for($i=date("Y")+2;$i>=2010;$i--) { ?>
						<option value="<?php echo $i ?>"><?php echo ($i-1).' - '.$i ?></option>
					<?php } ?>
					</select>
				</td>
				<td><label>Base Currency:</label></td>
				<td>
					<select name="expect_worth_id_to" id="expect_worth_id_to" class="textfield" onchange="getCurIdExist(this.value)" style="width: 80px;">
					<option value ="">Select</option>
					<?php foreach($currencies as $cur_id) { ?>
						<option value="<?php echo $cur_id['expect_worth_id'] ?>"><?php echo $cur_id['expect_worth_name'] ?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<table id="new_curr_container">
			<?php foreach($currencies as $cr) { ?>
			<tr>
				<td style="padding:0 10px 0"><label><?php echo $cr['expect_worth_name'] ?>:</label></td>
				<td><input type="text" name="expect_worth_id_from[<?php echo $cr['expect_worth_id'] ?>]" onkeypress="return isNumberKey(event)" value="" class="textfield width100px ip_curr" /></td>
			</tr>
			<?php } ?>
		</table>
		<table>
			<tr>
				<td style="padding:0 50px 0">
					<div id="subBtn" class="buttons pull-right" style="padding-right: 30px;">
						<button type="submit" class="positive" id="positiveBtn" onclick="save_bk_data(); return false;">Save</button>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
$(function() {
	$( "#add_container_close" ).on( "click", function() {
		$.unblockUI();
		$('#add_currency_container').empty();
		return false;
	});	
});

function getCurIdExist(curr_id) {
	if($('#financial_year').val() == '')
	return false;

	var form_data = {};	
	form_data[csrf_token_name] 	    = csrf_hash_token;
	form_data['financial_year'] 	= $('#financial_year').val();
	// form_data['expect_worth_id_to'] = $('#expect_worth_id_to').val();
	form_data['expect_worth_id_to'] = curr_id;
	
	$.ajax({
		url: site_base_url + 'manage_service/check_exist_currency_value/',
		cache: false,
		type: "POST",
		dataType: 'json',
		data: form_data,
		beforeSend:function(){
			$('#new_curr_container').block();
		},
		success: function(response){
			if(response.result=='success') {
				$('#new_curr_container').html(response.message);
			} else {
				$('#expect_worth_id_to').val("");
				alert(response.message);
				// $.unblockUI();
			}
			$('#new_curr_container').unblock();
		}
	});
}

function getCurFinanYrExist(finan_yr) {
	if($('#expect_worth_id_to').val() == '')
	return false;

	var form_data = {};	
	form_data[csrf_token_name] 	    = csrf_hash_token;
	form_data['financial_year'] 	= finan_yr;
	form_data['expect_worth_id_to'] = $('#expect_worth_id_to').val();
	
	$.ajax({
		url: site_base_url + 'manage_service/check_exist_currency_value/',
		cache: false,
		type: "POST",
		dataType: 'json',
		data: form_data,
		beforeSend:function(){
			$('#new_curr_container').block();
		},
		success: function(response){
			if(response.result=='success') {
				$('#new_curr_container').html(response.message);
			} else {
				$('#financial_year').val("");
				alert(response.message);
				// $.unblockUI();
			}
			$('#new_curr_container').unblock();
		}
	});
}

function save_bk_data() {
	var error = 0;
	if($('#financial_year').val()=='') {
		$('#financial_year').css("border-color", "red");
		error = 1;
	} else {
		$('#financial_year').css("border-color", "");
	}
	if($('#expect_worth_id_to').val()=='') {
		$('#expect_worth_id_to').css("border-color", "red");
		error = 1;
	} else {
		$('#expect_worth_id_to').css("border-color", "");
	}
	
	$('.ip_curr').each(function(){
		if($(this).val() == '') {
			$(this).css("border-color", "red");
			error = 1;
		} else {
			$(this).css("border-color", "");
		}
	});
	
	$( '#financial_year,#expect_worth_id_to' ).change(function() {
		$(this).css("border-color", "");
	});
	
	$( '.ip_curr' ).blur(function() {
		$(this).css("border-color", "");
	});
	
	if(error == 1)
	return false;

	var form_data = $('#bk_add_currency_form').serialize();
	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url: site_base_url + 'manage_service/add_bk_values/',
		cache: false,
		type: "POST",
		dataType: 'json',
		data: form_data,
		success: function(response){
			if(response.result=='ok') {
				setTimeout(function(){
					$.blockUI({
						message:'<h4>Updating...</h4><img src="'+site_base_url+'assets/img/ajax-loader.gif" />',
						css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
					});
					// window.location.href = site_base_url + "sales_forecast/add_sale_forecast/update/"+url_segment[4];
					$( ".blockUI.blockMsg.blockPage" ).addClass( "no-scroll" );
					window.location.reload(true);
				},500);
			} else {
				alert(response.msg);
				$.unblockUI();
			}
		}
	});
}

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	else
	return true;
}

</script>
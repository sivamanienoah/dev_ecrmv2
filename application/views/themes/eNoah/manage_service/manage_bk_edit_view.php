<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab" id="edit_container_close"></div>
	<form name="bk_currency_form" id="bk_currency_form" method="post" onsubmit="return false;">
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<?php #echo "<pre>"; print_r($curr_data); exit; ?>
		<table class="layout">
			<tr>
				<td>Financial Year:</td>
				<td>
				<input type="text" name="show_financial_year" readonly value="<?php echo ($financial_year-1).' - '.$financial_year ?>" class="textfield" style="width: 70px;" />
				<input type="hidden" name="financial_year" readonly value="<?php echo $financial_year ?>" class="textfield" style="width: 70px;" />
				</td>
				<td>Base Currency:</td>
				<td>
					<input type="text" name="to_currency" id="to_currency" readonly value="<?php echo $convert_to['expect_worth_name']; ?>" class="textfield" style="width: 70px;" />
					<input type="hidden" name="expect_worth_id_to" id="expect_worth_id_to" value="<?php echo $convert_to['expect_worth_id']; ?>" />
				</td>
			</tr>
			<?php #echo "<pre>"; print_r($curr_data); exit; ?>
			<?php
				$curr = array();
				foreach($curr_data as $curre) {
					$curr[$curre['expect_worth_id_from']] = $curre['currency_value'];
				}
			?>
			<?php foreach($currencies as $cr) { ?>
			<tr>
				<?php
					$readonly_status = '';
					$curr_value = $curr[$cr['expect_worth_id']];
					if($cr['expect_worth_id']==$convert_to['expect_worth_id']) {
						$readonly_status = 'readonly';
						$curr_value = 1;
					}
				?>
				<td style="padding:0 10px 0"><?php echo $cr['expect_worth_name'] ?>:</td>
				<td><input type="text" name="expect_worth_id_from[<?php echo $cr['expect_worth_id'] ?>]" onkeypress="return isNumberKey(event)" value="<?php echo $curr_value ?>" class="textfield width100px ip_curr" <?php echo $readonly_status ?> /></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan=2>
					<div id="subBtn" class="buttons pull-right" style="padding-right: 30px;">
						<button type="submit" class="positive" id="positiveBtn" onclick="update_bk_data(); return false;">Save</button>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
$(function() {

	$('#ui-datepicker-div').addClass('blockMsg');

	$( "#edit_container_close" ).on( "click", function() {
		$.unblockUI();
		$('#edit_currency_container').empty();
		return false;
	});
	
});

function update_bk_data() {
	
	var error = 0;
		
	$('.ip_curr').each(function(){
		if($(this).val() == '') {
			$(this).css("border-color", "red");
			error = 1;
		}
	});
	$( '.ip_curr' ).blur(function() {
		$(this).css("border-color", "");
	});
	
	if(error == 1)
	return false;
	
	var form_data = $('#bk_currency_form').serialize();	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url: site_base_url + 'manage_service/save_bk_value/',
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
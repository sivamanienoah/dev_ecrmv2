<style>
.hide-calendar .ui-datepicker-calendar { display: none; }
button.ui-datepicker-current { display: none; }
</style>

<div style="width:100%;">
	<div class="file-tabs-close-confirm-tab"></div>
	<form name="sales_forecast_edit_form" id="sales_forecast_edit_form" method="post" onsubmit="return false;">
	<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
		<table class="layout">
			<tr>
				<td>Milestone Name:</td>
				<td><input type="text" name="milestone_name" value="<?php echo $sf_data['milestone_name']; ?>" class="textfield width200px" /></td>
			</tr>
			<tr>
				<td>Month & Year:</td>
				<td><input type="text" name="for_month_year" data-calendar="false" id="for_month_year" readonly value="<?php echo date('F Y', strtotime($sf_data['for_month_year'])); ?>" class="textfield width200px" /></td>
			</tr>
			<tr>
				<td>Milestone Value:</td>
				<td><input type="text" name="milestone_value" value="<?php echo $sf_data['milestone_value']; ?>" class="textfield width200px" /></td>
			</tr>
			<tr>
				<td>
					<div id="subBtn" class="buttons pull-right" style="padding-right: 30px;">
						<button type="submit" class="positive" id="positiveBtn" onclick="update_sf_data('<?php echo $sf_data['milestone_id'] ?>'); return false;">Save</button>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
$(function() {

	$('#ui-datepicker-div').addClass('blockMsg');

	$( ".file-tabs-close-confirm-tab" ).on( "click", function() {
		$.unblockUI();
		return false;
	});
	
	$('#for_month_year').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'MM yy',
		showButtonPanel: true,
		minDate: new Date(<?php echo date('Y') ?>, <?php echo date('m') ?>, 1),
		onClose: function(input, inst) {
			var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
		},
		beforeShow: function(input, inst) {
			if ((selDate = $(this).val()).length > 0) 
			{
				iYear = selDate.substring(selDate.length - 4, selDate.length);
				iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
				$(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			}
			$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
		}
	});
});

function update_sf_data(id) {
	var form_data = $('#sales_forecast_edit_form').serialize();	
	$('.blockUI .layout').block({
		message:'<h3>Processing</h3>',
		css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
	});
	
	$.ajax({
		url: site_base_url + 'sales_forecast/save_sale_forecast_milestone/'+id,
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
					window.location.reload(true);
				},500);
			} else {
				alert("Update Failed");
				$.unblockUI();
			}
		}
	});
}

</script>
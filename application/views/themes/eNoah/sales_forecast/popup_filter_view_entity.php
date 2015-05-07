<style>
	.hide-calendar .ui-datepicker-calendar { display: none; }
	button.ui-datepicker-current { display: none; }
</style>
<div class="file-tabs-close-confirm-tab"></div>

<div style="border: 1px solid #DCDCDC;">
	<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
		<tr>
			<td class="tblheadbg">For the Month & Year</td>
		</tr>
		<tr>
			<td>
				From <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" style="width:78px;" />
				<br />
				To <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" style="width:78px; margin-left: 13px;" />
			</td>
		</tr>
		<tr align="right" >
			<td colspan="4">
				<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
				<!--input type="button" class="positive input-font" id="advance_filter" value="Search" /-->
				<a class='link-btn' id="filtersForecastEntity" href="javascript:void(0);" onclick="advanceFiltersEntity('<?php echo $forecast_type ?>')">Search</a>
				<div id = 'load' style = 'float:right;display:none;height:1px;'>
					<img src = '<?php echo base_url().'assets/images/loading.gif'; ?>' width="54" />
				</div>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
	var params  = {};
	params[csrf_token_name] = csrf_hash_token;

	$(function() {

		$('#ui-datepicker-div').addClass('blockMsg');

		$( ".file-tabs-close-confirm-tab" ).on( "click", function() {
			$.unblockUI();
			return false;
		});
		
		$( "#month_year_from_date, #month_year_to_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'MM yy',
			showButtonPanel: true,	
			onClose: function(dateText, inst) {
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();         
				$(this).datepicker('setDate', new Date(year, month, 1));
			},
			beforeShow : function(input, inst) {
				if ((datestr = $(this).val()).length > 0) {
					year = datestr.substring(datestr.length-4, datestr.length);
					month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNames'));
					$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
					$(this).datepicker('setDate', new Date(year, month, 1));    
				}
					var other  = this.id  == "month_year_from_date" ? "#month_year_to_date" : "#month_year_from_date";
					var option = this.id == "month_year_from_date" ? "maxDate" : "minDate";        
				if ((selectedDate = $(other).val()).length > 0) {
					year = selectedDate.substring(selectedDate.length-4, selectedDate.length);
					month = jQuery.inArray(selectedDate.substring(0, selectedDate.length-5), $(this).datepicker('option', 'monthNames'));
					$(this).datepicker( "option", option, new Date(year, month, 1));
				}
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
	});
	
	// $("#advanceFiltersForecastEntity").click(function() {
	function advanceFiltersEntity(forecast_type) {
		$('#filtersForecastEntity').hide();
		$('#load').show();

		var month_year_from_date = $("#month_year_from_date").val();
		var month_year_to_date   = $("#month_year_to_date").val();
		
		// alert(customer);
		
		$.ajax({
			type: "POST",
			url: site_base_url+"sales_forecast/get_chart_value/"+forecast_type,
			async: false,
			// dataType: "json",
			data: "filter=filter"+'&month_year_from_date='+month_year_from_date+"&month_year_to_date="+month_year_to_date+"&"+csrf_token_name+'='+csrf_hash_token,
			beforeSend:function(){
				$('.leadAdvancedfiltertbl').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(res) {
				$.unblockUI();
				// $('#filtersForecastEntity').show();
				if(forecast_type == 'F')
				$('#forecast_pie_container').html(res);
				if(forecast_type == 'A')
				$('#actual_pie_container').html(res);
			}
		});
		return false;  //stop the actual form post !important!
	}

	
</script>
<style>
	.hide-calendar .ui-datepicker-calendar { display: none; }
	button.ui-datepicker-current { display: none; }
</style>
<div class="file-tabs-close-confirm-tab"></div>
<div class="popup-forecast-head">Filter for Forecast Vs Actuals</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

<div style="border: 1px solid #DCDCDC;">
	<table cellpadding="0" cellspacing="0" class="data-table leadAdvancedfiltertbl" >
		<tr>
			<td class="tblheadbg">By Entity</td>
			<td class="tblheadbg">By Service</td>
			<td class="tblheadbg">By Practice</td>
			<td class="tblheadbg">By Industry</td>
			<td class="tblheadbg">By Customers</td>
			<td class="tblheadbg">By Leads/Projects</td>
			<td class="tblheadbg">For the Month & Year</td>
		</tr>
		<tr>	
			<td>
				<select multiple="multiple" id="entity" name="entity[]" class="advfilter" style="width:150px;">
					<?php foreach($entity as $ent) { ?>
						<option value="<?php echo $ent['div_id']; ?>"><?php echo $ent['division_name']; ?></option>
					<?php } ?>					
				</select> 
			</td>
			<td>
				<select multiple="multiple" id="services" name="services[]" class="advfilter" style="width:100px;">
					<?php foreach($services as $srv) { ?>
						<option value="<?php echo $srv['sid']; ?>"><?php echo $srv['services']; ?></option>
					<?php } ?>					
				</select>
			</td>
			<td>
				<select multiple="multiple" id="practices" name="practices[]" class="advfilter" style="width:100px;">
					<?php foreach($practices as $pr) { ?>
						<option value="<?php echo $pr['id']; ?>"><?php echo $pr['practices']; ?></option>
					<?php } ?>					
				</select>
			</td>
			<td>
				<select multiple="multiple" id="industries" name="industries[]" class="advfilter" style="width:100px;">
					<?php foreach($industries as $ind) { ?>
						<option value="<?php echo $ind['id']; ?>"><?php echo $ind['industry']; ?></option>
					<?php } ?>					
				</select>
			</td>
			<td>
				<select multiple="multiple" id="customer" name="customer[]" class="advfilter" style="width:195px;">
					<?php 
						if(!empty($customers)) {
						foreach($customers as $cust) {
					?>
							<option value="<?php echo $cust['companyid']; ?>"><?php echo $cust['company'] ?></option>
					<?php
						}
					}
					?>
				</select> 
			</td> 
			<td>
				<select multiple="multiple" id="lead_ids" name="lead_ids[]" class="advfilter" style="width: 200px;">
					<?php 
						if(!empty($leads)) {
						foreach($leads as $ld) {
					?>
							<option value="<?php echo $ld['lead_id']; ?>"><?php echo $ld['lead_title']; ?></option>
					<?php
						}
					}
					?>
				</select> 
			</td>
			<td>
				From <input type="text" data-calendar="false" name="month_year_from_date" id="month_year_from_date" class="textfield" style="width:78px;" />
				<br />
				To <input type="text" data-calendar="false" name="month_year_to_date" id="month_year_to_date" class="textfield" style="width:78px;" />
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<a class='link-btn' id="filtersForecastEntity" href="javascript:void(0);" onclick="advanceFiltersCompare('<?php echo $forecast_type ?>')">Search</a>
				<input type="reset" class="positive input-font" name="advance" id="filter_reset" value="Reset" />
				<!--input type="button" class="positive input-font" id="advance_filter" value="Search" /-->
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
	function advanceFiltersCompare(forecast_type) {
		$('#filtersForecastEntity').hide();
		$('#load').show();

		var entity      = $("#entity").val();
		var services    = $("#services").val();
		var practices   = $("#practices").val();
		var industries  = $("#industries").val();
		var lead_ids    = $("#lead_ids").val();
		var customer    = $("#customer").val();
		var month_year_from_date = $("#month_year_from_date").val();
		var month_year_to_date   = $("#month_year_to_date").val();
		
		// alert(entity);
		
		$.ajax({
			type: "POST",
			url: site_base_url+"sales_forecast/get_chart_value/"+forecast_type,
			async: false,
			// dataType: "json",
			data: "filter=filter"+"&entity="+entity+"&services="+services+"&practices="+practices+"&industries="+industries+"&lead_ids="+lead_ids+"&customer="+customer+'&month_year_from_date='+month_year_from_date+"&month_year_to_date="+month_year_to_date+"&"+csrf_token_name+'='+csrf_hash_token,
			beforeSend:function(){
				$('.leadAdvancedfiltertbl').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
			},
			success: function(res) {
				$.unblockUI();
				// $('#filtersForecastEntity').show();
				if(forecast_type == 'FA')
				$('#compare_bar_container').html(res);
			}
		});
		return false;  //stop the actual form post !important!
	}

	
</script>
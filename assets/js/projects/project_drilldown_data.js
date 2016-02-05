$(function() {
    $("#refine_drilldown_data").click(function(){
		if($('#department_ids').val() == null) {
			$('#hdept_ids').val('');
		} else {
			$('#hdept_ids').val($('#department_ids').val());
		}
		if($('#practice_ids').val() == null) {
			$('#hprac_ids').val('');
		} else {
			$('#hprac_ids').val($('#practice_ids').val());
		}
		$('#hmonth_year').val($('#month_year_from_date').val());
		$('#hskill_ids').val($('#skill_ids').val())
		$('#hmember_ids').val($('#member_ids').val())
		
		var formdata = $('#fliter_data').serialize();
		
		$.ajax({
			type: "POST",
			url: site_base_url+'projects/dashboard/get_data/',                                                              
			data: formdata+'&resource_type='+$('#resource_type').val()+'&dept_type='+$('#dept_type').val()+'&filter_group_by='+$('#filter_group_by').val()+'&filter_sort_by='+$('#filter_sort_by').val()+'&filter_sort_val='+$('#filter_sort_val').val(),
			cache: false,
			beforeSend:function() {
				$('#filter_group_by').prop('selectedIndex',0);
				$('#drilldown_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
				$('#drilldown_data').show();
				$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
			},
			success: function(data) {
				$('#drilldown_data').html(data);
				$('#drilldown_data').show();
				$('html, body').animate({ scrollTop: $("#drilldown_data").offset().top }, 1000);
			}                                                                                   
		});
	});
	
	$("#reset_drilldown").click(function(){
		$('#filter_group_by').prop('selectedIndex',0);
		$('#filter_sort_by').prop('selectedIndex','desc');
		$('#filter_sort_val').prop('selectedIndex','hour');
	});
	
});
/*
 *@Account View
 *@Role Controller
*/

function showPassFields(obj) {
	if (obj.attr('checked')) {
		$('.hide-passwords td:hidden').css('visibility', 'visible');
	} else {
		$('.hide-passwords td:visible').css('visibility', 'hidden');
	}
}

$(function(){
	$('.hide-passwords td').css('visibility', 'hidden');
	
	$('#add_log').click(function(){
		var log_details = $.trim($('#standalone_log').val());
		var log_time = $.trim($('#time_spent').val());
		
		if (log_details == '')
		{
			alert('Please fill content for your log');
			return false;
		}
		
		if (log_time == '' || log_time == 0)
		{
			alert('Please add time');
			return false;
		}
		
		$.post(
			'myaccount/add_log',
			{'log_content': log_details, 'time_spent': log_time},
			function (data)
			{
				if (data.error)
				{
					alert(data.error);
				}
				else
				{
					$('#time_spent').val('');
					$('#standalone_log').val('');
					$('.time-log-form:visible').slideUp('normal');
				}
				return false;
			},
			'json'
		);
	});
});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
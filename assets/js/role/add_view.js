/*
 *@Add View
 *@Role Controller
*/

$(document).ready(function() {
 $('.check').click(function() { 
        if ($(this).is(':checked')) {
		    $(this).parent().find('input:checkbox').attr('checked', 'checked');
        }else{
		 $(this).parent().find('input:checkbox').attr('checked', '');
		}
    });

});
	
//////////////////////////////////////////////////////////////////// end ///////////////////////////////////////////////////
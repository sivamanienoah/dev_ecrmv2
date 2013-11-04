/*
 *@Item Management Add Jquery
*/

$(function(){
	$('#add-item-desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 600) {
			$(this).val(desc_len.substring(0, 600));
		}
		
		var remain_len = 600 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		$('#desc-countdown').text(remain_len);
	});
	$('#add-item-desc').keyup();
	if (parseInt($('#desc-countdown').text()) == 0) {
		//$('td.action-buttons .buttons').remove();
		//$('td.action-buttons:first').html('<p>Update buttons removed due to extended text being trimmed. Contact the developer if you need to edit this item.</p>');
	}
});
function isNumberKey(evt)
{
  var charCode = (evt.which) ? evt.which : event.keyCode;
  if (charCode != 46 && charCode > 31 
	&& (charCode < 48 || charCode > 57))
	 return false;

  return true;
}


/////////////////
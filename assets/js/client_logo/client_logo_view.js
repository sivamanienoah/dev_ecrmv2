/*
 *@Client Logo View Jquery
*/

function clientLogoAjaxFileUpload() {
	$('<li>Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#proces_img');
	var client_url = $('#client_url').val();
	var client_url=client_url.replace(/\//g, "-");
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	$.ajaxFileUpload
	(
		{
			url:'client_logo/cliLogoUp/'+encodeURIComponent(client_url),
			secureuri:true,
			fileElementId:'logo_file',
			dataType: 'json',
			data:params,
			success: function (data, status)
			{
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						if (window.console)
						{
							console.log(data);
						}
						if (data.msg)
						{
							alert(data.msg);
							$('#proces_img').hide('slow').remove();
						}
						else
						{
							alert('File upload failed!');
							$('#proces_img').hide('slow').remove();
						}
						
					}
					else
					{
						if(typeof(data.file_name) != 'undefined')
						{
							if(data.file_name != 'undefined') {
								fname = '<img src=crm_data/client_logo/'+data.file_name+' alt="Smiley face" >';
							}
						} else {
							fname = 'File Not Attached';
						}
						$('#files').html(fname);
						$('#proces_img').hide('slow').remove();
					}
				}
			},
			error: function (data, status, e)
			{
				// alert(status);
				alert('Sorry, the upload failed due to an error!');
				$('#proces_img').hide('slow').remove();
				if (window.console)
				{
					console.log('ajax error\n' + e + '\n' + data + '\n' + status);
					for (i in e) {
					  console.log(e[i]);
					}
				}
			}
		}
	);
	$('#logo_file').val('');
	$('#client_url').val('');
	return false;
}

function reset_logo_confirm() {
	$.blockUI({
		message:'<br /><h5>Are You Sure Want to Reset the Logo?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="del_client_logo(); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDel(); return false;">No</button></div></div>',
		css:{width:'440px'}
	});
}

function cancelDel() {
    $.unblockUI();
}

function del_client_logo() {
	$.ajax({
		type: "GET",
		url: site_base_url+'client_logo/del_client_logo/',
		dataType:"json",                                                                
		cache: false,
		beforeSend:function(){
			
		},
		success: function(response) {
			if (response == true) {
				$('#files').empty();
				$('#files').html('<span class="ajx_failure_msg">Logo Deleted.</span>');
			}
			$.unblockUI();
		}                                                                                       
	});
	
	return false;
}

/////////////////
/*
*@Welcome View Project
*@
*/
	var metrics_reload = false;
	$(document).ready(function() {
		
		var mySelect = $('#project_lead');
		previousValue = mySelect.val();
		var lead_assign = previousValue; 
		$("#previous-project-manager").val(lead_assign); 
		$('#project_lead').change( function() {
		});

		$('.payment-profile-button').click(function() {
			$('#rec_paymentfadeout').hide();
			$('.payment-profile-view').slideToggle(); 
			$('.payment-recieved-view').hide(); 
			$('.payment-terms-mini-view1').show(); 
			$('.payment-received-mini-view1').hide(); 
			loadPayment();	
			return false;
		});
		$('.payment-received-button').click(function() {
			$('#rec_paymentfadeout').hide();
			$('.payment-recieved-view').slideToggle();
			$('.payment-profile-view').hide(); 
			$('.payment-received-mini-view1').show(); 
			$('.payment-terms-mini-view1').hide(); 
			loadPaymentTerms(); 
			return false;
		});

		$(document).on('change', '.all-chk', function(event){
			var type = $(this).attr('id');
			var uid = $(this).val();
			if($(this).is(':checked')) {
				$('.'+type+'-'+uid).prop('checked',true);
				switch(type){
					case 'rd-read':
						$('#rd-write, #rd-none').prop('checked',false);
					break;
					case 'rd-write':
						$('#rd-read, #rd-none').prop('checked',false);
					break;
					case 'rd-none':
						$('#rd-write, #rd-read').prop('checked',false);
					break;
				}
			} else {
				$('.rd-none-'+uid).prop('checked',true);
			}
		});

	});

	$().ready(function() {
		$('#add').click(function() {  
			$('#project-member').val($('#select1').val());	
			return !$('#select1 option:selected').remove().appendTo('#select2'); 
		});  
		$('#remove').click(function() {  
			$('#project-member').val($('#select2').val());
			return !$('#select2 option:selected').remove().appendTo('#select1');  
		});
	   /* when double clicking on select1 project member will added to select 2 */
		$('#select1').dblclick(function() {
			$('#project-member').val($('#select1').val());
			return !$('#select1 option:selected').remove().appendTo('#select2');  	
	   });
	   /* when double clicking on select2 project member will added to select 1 */
		$('#select2').dblclick(function() {
			$('#project-member').val($('#select2').val());
			return !$('#select2 option:selected').remove().appendTo('#select1');  
	   });
	});
	
	function logsDataTable(){
		$('.logstbl').dataTable( {
			"iDisplayLength": 10,
			"sPaginationType": "full_numbers",
			"bInfo": false,
			"bPaginate": true,
			"bProcessing": true,
			"bServerSide": false,
			"bLengthChange": false,
			"bSort": false,
			"bFilter": false,
			"bAutoWidth": false,
			"oLanguage": {
			  "sEmptyTable": "No Comments Found..."
			}
		});
	}

 ////////////////////////----------------------------X---------------------////////////////////////////

	var quote_id        = project_jobid;
	var ex_cust_id      = 0;
	var item_sort_order = '';
	var curr_job_id     = project_jobid;

	$(function(){
	    if ( ( project_view_quotation!='' ) ) {
			populateQuote(project_jobid, true);
		} 
	});

	var userid 				= project_user_id;
	var current_job_status  = project_job_status;

	function addLog() {

		var the_log = tinyMCE.get('job_log').getContent();
	    var the_sign = tinyMCE.get('signature').getContent();
		
		if ($.trim(the_log) == '') {
			alert('Please enter your post!');
			return false;
		}

		var submit_log_minutes = null, log_minutes = $('#log_minutes').val();
		if ($.trim(log_minutes) != '')
		{
			if ( ! /^[0-9]+$/.test(log_minutes))
			{
				alert('Invalid minutes supplied');
				return false;
			}
			else
			{
				submit_log_minutes = log_minutes;
			}
		}

		var client_emails = true;
		if ($('#email_to_customer').is(':checked')) {
			client_emails = false;
			$('#multiple-client-emails').children('input[type=checkbox]').each(function(){
				if ($(this).is(':checked')){
					client_emails = true;
				}
			});
		}

		if (!client_emails) {
			alert('If you want to email the client, you must select at least one email address of the client.');
			return false;
		}

		if ($('#log_stickie').is(':checked')) {
			if (!window.confirm('Are you sure you want to highlight this log as a Stickie?')) {
				return false;
			}
		}

		var email_set = '';
		/* $('.email-list input[type="checkbox"]:checked').each(function(){
			email_set += $(this).attr('id') + ':';
		}); */
		$('.email-list input[type="hidden"]').each(function(){
			email_set += $(this).attr('value') + ':';
		});

		$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});


		var form_data 			   = {'userid':userid, 'lead_id':quote_id, 'log_content':the_log, 'emailto':email_set,'sign_content':the_sign,'client_emails':$('#email_to_customer').is(':checked')}
		form_data[csrf_token_name] = csrf_hash_token;
		
		if ($('#log_stickie').is(':checked')) {
			form_data.log_stickie = true;
		}


		/* add minutes to the log */
		if (submit_log_minutes)
		{
			form_data.time_spent = submit_log_minutes;
		}


		if ($('#email_to_customer').is(':checked')) {
			form_data.email_to_customer = true;
			form_data.client_email_address = $('#client_email_address').val();
			form_data.client_full_name = $('#client_full_name').val();
			if ($('#client_emails_1').is(':checked')) {
				form_data.client_emails_1 = $('#client_emails_1').val();
			}
			if ($('#client_emails_2').is(':checked')) {
				form_data.client_emails_2 = $('#client_emails_2').val();
			}
			if ($('#client_emails_3').is(':checked')) {
				form_data.client_emails_3 = $('#client_emails_3').val();
			}
			if ($('#client_emails_4').is(':checked')) {
				form_data.client_emails_4 = $('#client_emails_4').val();
			}
			form_data.additional_client_emails = $('#additional_client_emails').val();
		}
		if ($('#requesting_client_approval').val() == 1) {
			form_data.requesting_client_approval = true;
		}

		// empty list of emails?
		if (email_set == '' && typeof(form_data.client_emails_1) == 'undefined' && typeof(form_data.client_emails_2) == 'undefined' && typeof(form_data.client_emails_3) == 'undefined' && typeof(form_data.client_emails_4) == 'undefined' && typeof(form_data.additional_client_emails) == 'undefined') {
			if (!window.confirm('You do not have any user selected for emails!\nDo you want to continue?')) {
				$.unblockUI();
				return false;
			}
		}

		if ($('#email_to_customer').is(':checked') && the_log.match(/attach|invoice/gi) != null) {
			if ( ! window.confirm('You have not attached the invoice to the email.\nDo you want to continue without the invoice?')) {
				$.unblockUI();
				return false;
			}
		}
		$.post(
			site_base_url+'project/pjt_add_log',
			form_data,
			function(data)
			{
				if (typeof(data) == 'object')
				{
					if (data.error) 
					{
						alert(data.errormsg);
					} 
					else 
					{
						$('#lead_log_list').prepend(data.html).children('.log:first').slideDown(400);
						$('#job_log').val('');
						$('#log_minutes').val('');
						$('#additional_client_emails').val('');
						if (data.status_updated) {
							document.location.href = project_request_url;
						}
						if (typeof(this_is_home) != 'undefined')
						{
						   window.location.href = window.location.href;
						}
					}
				} 
				else 
				{
					alert('Unexpected response from server!');
				}
				$.unblockUI();
			},"json"
		)
	}

	function setPaymentRecievedTerms() 
	{
		$('#pr_form_jobid').val(curr_job_id);
		var valid_date = true;
		var date_entered = true;
		var errors = [];
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
		var pdate2 = $.trim($('#pr_date_3').val());	

		if (($.trim($('#pr_date_1').val()) == '') && ($.trim($('#pr_date_2').val()) == '') && ($.trim($('#pr_date_3').val()) == '')) {
			date_entered = false;
		}
		if (valid_date == false) {
			errors.push('You have selected an invalid date');
		}
		if(($.trim($('#pr_date_1').val()) == '')) {
			errors.push('<p>Enter Invoice Number.</p>');
		}
		if(($.trim($('#pr_date_2').val()) == '')) {
			errors.push('<p>Enter Amount.</p>');
		}
		if(($.trim($('#pr_date_3').val()) == '')) { //|| valid_date == false) {
			errors.push('<p>Enter valid Date.</p>');
		}
		if($('.deposit_map_field').val() == 0) {
		   errors.push('<p>Map payment term.</p>');
		}

		if (errors.length > 0) {
			$('#rec_paymentfadeout').show();
			$('#rec_paymentfadeout').html(errors.join(''));
			setTimeout('timerfadeout()', 8000);
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var form_data = $('#payment-recieved-terms').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;		
			$.post( 
				site_base_url+'project/add_project_received_payments',
				form_data,
				function(data) {
						if (data.error) {
							setTimeout('timerfadeout()', 8000);
							$('#rec_paymentfadeout').show();
							$('#rec_paymentfadeout').html(data.errormsg);
						} else {
							$('.payment-recieved-view:visible').slideUp(400);
							$('.payment-received-mini-view1').html(data.msg);
							$('#payment-recieved-terms')[0].reset();
						}
					$.unblockUI();
				}
				,'json'
			);
			
		}
		
		$('.payment-received-mini-view1').css('display', 'block');
	
	}

	function updatePaymentRecievedTerms(pdid, eid) 
	{
		$('#pr_form_jobid').val(curr_job_id);
		var valid_date = true;
		var date_entered = true;
		var errors = [];
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
		var pdate2 = $.trim($('#pr_date_3').val());	

		if ( ($.trim($('#pr_date_1').val()) == '') && ($.trim($('#pr_date_2').val())  == '') && ($.trim($('#pr_date_3').val()) == '') ) {
			date_entered = false;
		}

		if (valid_date == false) {
			errors.push('You have selected an invalid date');
		}
		if(($.trim($('#pr_date_1').val()) == '')) {
			errors.push('<p>Enter Invoice Number.</p>');
		}
		if(($.trim($('#pr_date_2').val()) == '')) {
			errors.push('<p>Enter Amount.</p>');
		}
		if(($.trim($('#pr_date_3').val()) == '')) { //|| valid_date == false) {
			errors.push('<p>Enter valid Date.</p>');
		}
		if($('.deposit_map_field').val() == 0) {
		   errors.push('<p>Map payment term.</p>');
		}
		if (errors.length > 0) {
			//alert(errors.join('\n'));
			$('#rec_paymentfadeout').show();
			$('#rec_paymentfadeout').html(errors.join(''));
			setTimeout('timerfadeout()', 8000);
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			var form_data = $('#update-payment-recieved-terms').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+'project/add_project_received_payments/'+pdid+'/'+eid,
				form_data,
				function(data) {
						if (data.error) {
							setTimeout('timerfadeout()', 8000);
							$('#rec_paymentfadeout').show();
							$('#rec_paymentfadeout').html(data.errormsg);
						} else {
							$('.payment-recieved-view:visible').slideUp(400);
							$('.payment-received-mini-view1').html(data.msg);
							$('#update-payment-recieved-terms').remove();
							paymentReceivedView();
						}
					$.unblockUI();
				}
				,'json'
			);
		}
		$('.payment-received-mini-view1').css('display', 'block');
	}

	function loadPaymentTerms() 
	{	
		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		$.post( 
			site_base_url+'project/retrieve_record/'+curr_job_id,params,
			function(data) {
				if (data.error) {
					alert(data.errormsg);
				} else {
					$('.deposit_map_field').html(data);	
				}
			}
		);
	}

	//function for load the payment terms every time click the 'Add Payment Terms' button
	function loadPayment() 
	{
		$("#uploadFile").empty();
		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		$.post( 
			site_base_url+'project/retrieve_payment_terms/'+curr_job_id,params,
			function(data) {
				if (data.error) {
					alert(data.errormsg);
				} else {
					$('.payment-terms-mini-view1').html(data);
				}
			}
		);
	}

	function fullScreenLogs()
	{
		var fsl_height = parseInt($(window).height()) - 80;
		fsl_height = fsl_height + 'px';

		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		$.post( 
			site_base_url+'project/getLogs/'+project_jobid,params,
			function(data) {
				if (data.error) {
					alert(data.errormsg);
				} else {
					$('.comments-log-container').html(data);
				}
			}
		);
		
		$.blockUI({
			message:$('.comments-log-container'),
			css: {background:'#fff', border: '1px solid #999', padding:'4px', height:fsl_height, color:'#000000', width:'600px', overflow:'auto', top:'40px', left:'50%', marginLeft:'-300px'},
			overlayCSS:  {backgroundColor:'#fff', opacity:0.9}
		});
		$('.blockUI:not(.blockMsg)').append('<p onclick="$.unblockUI();$(this).remove();" id="fsl-close">CLOSE</p>');
	}

	function checkAccessPermissions(lead_id, filed_column, filed_id, checking_column)
	{
	
	var date_return = '';
	
	$.get(site_base_url+'ajax/request/check_access_permissions/', {lead_id:lead_id,filed_column:filed_column,filed_id:filed_id,checking_column:checking_column},
			function(data) {
				date_return  = data;
			}
	);
			return date_return;
	}
	
	function runAjaxFileUpload()
	{
		var _uid = new Date().getTime();
		$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
		var params 				 = {};
		params[csrf_token_name]  = csrf_hash_token;
		var ffid = $('#filefolder_id').val();

		if(ffid == 'Files') 
		{ 
		alert('You have no permissions to upload files to current locations. Please contact to administrators!.'); 
		return false;
		}

		$.ajaxFileUpload({
			url: 'ajax/request/file_upload/'+project_jobid+'/'+ffid,
			secureuri: false,
			fileElementId: 'ajax_file_uploader',
			dataType: 'json',
			data: params,
			success: function (data, status) {
			
				if(typeof(data.error) != 'undefined') {
					if(data.error != '') {
						if (window.console) {
							console.log(data);
						}
						if (data.msg) {
							alert(data.msg);
						} else {
							alert('File upload failed!');
						}
						// $('#'+_uid).hide('slow').remove();
					} else {	
						if(data.msg == 'File successfully uploaded!') {
							// alert(data.msg);
							/*Showing successfull message.*/
							$('#fileupload_msg').html('<span class=ajx_success_msg>'+data.msg+'</span>');
							setTimeout('timerfadeout()', 3000);
							// Again loading existing files with new files
							$('#jv-tab-3').block({
								message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
								css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
							});
							$.get(
								site_base_url+'ajax/request/get_project_files/' + curr_job_id +'/'+ ffid,
								{},
								function(data) {
									$('#list_file').html(data);
									$('#jv-tab-3').unblock();
									$('#list_file_tbl').dataTable({
										"iDisplayLength": 10,
										"sPaginationType": "full_numbers",
										"bInfo": true,
										"bPaginate": true,
										"bProcessing": true,
										"bServerSide": false,
										"bLengthChange": true,
										"bSort": true,
										"bFilter": false,
										"bAutoWidth": false,
										"bDestroy": true,
										"aoColumnDefs": [
											{ 'bSortable': false, 'aTargets': [ 0 ] }
										]
									});
									$.unblockUI();
								}
							);
							return false;
						}
					}
				}
			},
			error: function (data, status, e)
			{
				alert('Sorry, the upload failed due to an error!');
				$('#'+_uid).hide('slow').remove();
				if (window.console)
				{
					console.log('ajax error\n' + e + '\n' + data + '\n' + status);
					for (i in e) {
					  console.log(e[i]);
					}
				}
			}
		});
		$('#ajax_file_uploader').val('');
		return false;
	}


	
function runPaymentAjaxFileUpload() {
	var _uid				 = new Date().getTime();
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;
	var ffid				 = $('#filefolder_id').val();

	$.ajaxFileUpload({
		url: 'project/payment_file_upload/'+curr_job_id+'/'+ffid,
		secureuri: false,
		fileElementId: 'payment_ajax_file_uploader',
		dataType: 'json',
		data: params,
		success: function (data, status) {
			if(typeof(data.error) != 'undefined') {
				if(data.error != '') {
					if (window.console) {
						console.log(data);
					}
					if (data.msg) {
						alert(data.msg);
					} else {
						alert('File upload failed!');
					}
				} else {	
					if(data.msg == 'File successfully uploaded!') {
						// alert(data.msg);				
						$.each(data.res_file, function(i, item) {
							var res = item.split("~",2);
							// alert(res[0]+res[1]);	
							var name = '<div style="float: left; width: 100%;"><input type="hidden" name="file_id[]" value="'+res[0]+'"><span style="float: left;">'+res[1]+'</span><a id="'+res[0]+'" class="del_file"> </a></div>';
							$("#uploadFile").append(name);
						});
						$.unblockUI();
					}
				}
			}
		},
		error: function (data, status, e)
		{
			alert('Sorry, the upload failed due to an error!');
			$('#'+_uid).hide('slow').remove();
			if (window.console)
			{
				console.log('ajax error\n' + e + '\n' + data + '\n' + status);
				for (i in e) {
				  console.log(e[i]);
				}
			}
		}
	});
	$('#payment_ajax_file_uploader').val('');
	return false;
}

function runOtherCostAjaxFileUpload()
{
	var _uid				 = new Date().getTime();
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;
	var ffid				 = $('#filefolder_id').val();

	$.ajaxFileUpload({
		url: 'project/othercost_file_upload/'+curr_job_id+'/'+ffid,
		secureuri: false,
		fileElementId: 'othercost_ajax_file_uploader',
		dataType: 'json',
		data: params,
		success: function (data, status) {
			if(typeof(data.error) != 'undefined') {
				if(data.error != '') {
					if (window.console) {
						console.log(data);
					}
					if (data.msg) {
						alert(data.msg);
					} else {
						alert('File upload failed!');
					}
				} else {	
					if(data.msg == 'File successfully uploaded!') {
						// alert(data.msg);				
						$.each(data.res_file, function(i, item) {
							var res = item.split("~",2);
							// alert(res[0]+res[1]);	
							var name = '<div style="float: left; width: 100%;"><input type="hidden" name="file_id[]" value="'+res[0]+'"><span style="float: left;">'+res[1]+'</span><a id="'+res[0]+'" class="del_oc_file"> </a></div>';
							$("#uploadOcFile").append(name);
						});
						$.unblockUI();
					}
				}
			}
		},
		error: function (data, status, e)
		{
			alert('Sorry, the upload failed due to an error!');
			$('#'+_uid).hide('slow').remove();
			if (window.console)
			{
				console.log('ajax error\n' + e + '\n' + data + '\n' + status);
				for (i in e) {
				  console.log(e[i]);
				}
			}
		}
	});
	$('#othercost_ajax_file_uploader').val('');
	return false;
}

function addURLtoJob() 
{
	var url = $.trim($('#job-add-url').val());
	var cont = $.trim($('#job-url-content').val());
	if (url == '') {
		alert('Please enter a URL to add');
		return false;
	}
	url 					  = js_urlencode(url);
	var params 				  = {'lead_id':curr_job_id, 'url':url, 'content':cont};
	params[csrf_token_name]   = csrf_hash_token;
	
	$.post(
		site_base_url+'ajax/request/add_url_tojob/',
		params,
		function(_data) {
			try {
				eval ('var data = ' + _data);
				if (typeof(data) == 'object') {
					if (data.error == false) {
						$('#job-url-list').append(data.html);
						$('#job-add-url').val('');
						$('#job-url-content').val('');
					} else {
						alert(data.error);
					}
				} else {
					alert('URL addition failed! Please try again.');
				}
			} catch (e) {
				alert('Invalid response, your session may have timed out.');
			}
		}
	);
}

	function ajaxDeleteJobURL(id, el) {
		$.get(
			'ajax/request/delete_url/' + id,
			{},
			function (_data) {
				try {
					eval ('var data = ' + _data);
					if (data.error == false) {
						var agree=confirm("Are you sure you want to delete this url?");
							if (agree) {
								$(el).parent().hide('fast', function() { $(this).remove(); });
								}
					} else {
						alert('URL deletion failed! Please try again.');
					}
				} catch (e) {
					alert('URL deletion failed! Please try again.');
				}
			}
		)
	}

	var job_project_manager = project_assigned_to;

	function setProjectLead() {
		$('#pjt_lead_errormsg').hide();
		var pl_user				= $('#project_lead').val(); 
		var previous_manager	= $("#previous-project-manager").val();
		var params 				= {'lead_id':curr_job_id, 'new_pm':pl_user, 'previous_pm':previous_manager};
		params[csrf_token_name] = csrf_hash_token;
		
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		
		if (pl_user == 0) {
			$('#pjt_lead_errormsg').html("<span class='ajx_failure_msg'>Please Select Project Manager!</span>");
			$('#pjt_lead_errormsg').show();
			$.unblockUI();
			setTimeout('timerfadeout()', 3000);
			return false;
		} else {
			$.post(
				site_base_url + 'project/set_project_lead',
				params,
				function(data)
				{
					if (data.error == false) {
						job_project_manager = pl_user;
						$('#pjt_lead_errormsg').show();
						$('#pjt_lead_errormsg').html("<span class='ajx_success_msg'>Project Manager Saved.</span>");
						$("#previous-project-manager").val(pl_user);
					} else {
						alert(data.msg);
						// window.location.href = window.location.href;
					}
				},"json"
			);
			$.unblockUI();
			setTimeout('timerfadeout()', 3000);
		}
	}	

	//adding the project id.
	function setProjectId() {
		$('#pjt_id_errormsg, .checkUser1, .checkUser').hide();
		var pjtId = $('#pjtId').val()
		if (pjtId == 0) {
			//alert('Please Enter Project ID!');
			$('#pjt_id_errormsg').text('Please Enter Project ID!');
			$('#pjt_id_errormsg').show();
			setTimeout('timerfadeout()', 2000);
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});

			var baseurl 			= $('.hiddenUrl').val();
			var params  			= { pjt_id: pjtId};
			params[csrf_token_name] = csrf_hash_token;
				
			$.ajax({
				type: "POST",
				url : baseurl + 'project/chkPjtIdFromdb/',
				cache : false,
				data: params,
				success : function(response){
					$('.checkUser').hide();
					
					var set_params 				= {pjt_id: pjtId, lead_id: curr_job_id};
					set_params[csrf_token_name] = csrf_hash_token;
					
					if(response == 'Ok') {				
						$('.checkUser').show();
						$('.checkUser1').hide();
						setTimeout('timerfadeout()', 2000);
						$.post(
							site_base_url+'project/set_project_id/',
							set_params,
							function(data) 
							{
								if (data.error == false) {
									$('h5.project-id-label span').text(pjtId);
									setTimeout(function(){
										$.blockUI({
											message:'<h4>Fetching Timesheet Information...</h4><img src="assets/img/ajax-loader.gif" />',
											css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
										});
										window.location.reload(true);
									},2000);
								} else {
									alert(data.error);
								}

							},"json"
						);
					} else {
						$.unblockUI();
						$('.checkUser').hide(); 
						$('.checkUser1').show();
						setTimeout('timerfadeout()', 2000);
					}
				}
			});
		}
	}

	//updating the project value.
	function setProjectVal() 
	{
		$('#msg_project_efforts').empty();
		var pjtValue = $('#pjt_value').val()
		if (pjtValue == 0) {
			$('#msg_project_efforts').html("<span class='ajx_failure_msg'>Please Enter Project Value!.</span>");
			$('#msg_project_efforts').show();
			setTimeout('timerfadeout()', 3000);
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var baseurl = $('.hiddenUrl').val();
			var params      			 = { pjt_val: pjtValue, lead_id: curr_job_id };
			params[csrf_token_name]      = csrf_hash_token;
			$.ajax({
				type: "POST",
				url : baseurl + 'project/chkPjtValFromdb/',
				cache : false,
				data: params,
				success : function(response) {
					var params 				= { pjt_val: pjtValue, lead_id: curr_job_id };
					params[csrf_token_name] = csrf_hash_token;
					
					if(response == 'Ok') {
						setTimeout('timerfadeout()', 3000);
						$.post(
							site_base_url+'project/set_project_value/',
							params,
							function(_data) {
								try {
								eval ('var data = ' + _data);
								if (typeof(data) == 'object') {
									if (data.error == false) {
										$('#msg_project_efforts').show();
										$('#msg_project_efforts').html("<span class='ajx_success_msg'>Project Value Updated.</span>");
										variancePjtValue = pjtValue - $("#actualValue").val();
										$("#varianceValue").val(variancePjtValue);
										$.unblockUI();
									} else {
										alert(data.error);
									}
								} else {
									alert('Updating faild, please try again.');
									}
								} catch (e) {
									alert('Invalid response, your session may have timed out.');
								}
							}
						);
					} else {
						$.unblockUI();
						$('#msg_project_efforts').html("<span class='ajx_failure_msg'>Project Value Already Exists.</span>");
						setTimeout('timerfadeout()', 2000);
					}
				}
			});
		}
	}
	
	//Edit the Project Title//
	function updateTitle() {
		$('#resmsg_projecttitle').empty();
		var lead_title = $('#lead_title').val();
		
		if(lead_title == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/update_title/',
			dataType: 'json',
			data: 'lead_title='+lead_title+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_projecttitle').html("<span class='ajx_success_msg'>Title Updated</span>");
					$('.job-title').html(lead_title);
				} else {
					$('#resmsg_projecttitle').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		setTimeout('timerfadeout()', 2000);
	}
	
	/*
	*@Method setServiceRequirement
	*@parameters department_id_fk, lead_id
	*@Use update department for particular leads
	*@Author eNoah - Sriram.S
	*/
	function setServiceRequirement() {
		$('#resmsg_serv_req').empty();
		var lead_service = $('#lead_service').val();
		
		if(lead_service == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_service_req/',
			dataType: 'json',
			data: 'lead_service='+lead_service+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				
				if (data.error == false) {
					$('#resmsg_serv_req').html("<span class='ajx_success_msg'>Updated.</span>");
				} else {
					$('#resmsg_serv_req').show();
					$('#resmsg_serv_req').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$("#resmsg_serv_req").delay(3000).fadeOut("slow");
				$.unblockUI();
			}
		});
	}
	
	function setCustomer() {
		$('#resmsg_customer').empty();
		var customer_company_name = $('#customer_company_name').val();
		var customer_id = $('#customer_id').val();
		var customer_id_old = $('#customer_id_old').val();
		var customer_company_name_old = $('#customer_company_name_old').val();
		
		if(customer_id == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/update_customer/',
			dataType: 'json',
			data: 'customer_company_name='+customer_company_name+'&customer_id='+customer_id+'&customer_id_old='+customer_id_old+'&customer_company_name_old='+customer_company_name_old+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_customer').html("<span class='ajx_success_msg'>Customer Updated</span>");
					// $('.job-title').html(lead_title);
				} else {
					$('#resmsg_customer').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				loadCustomer(curr_job_id);
				$.unblockUI();
			}
		});
		setTimeout('timerfadeout()', 2000);
	}
	
	//Set the Project Practices//
	function setPractices()
	{
		$('#resmsg_practice').empty();
		var practice = $('#practice').val();
		
		if(practice == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_practices/',
			dataType: 'json',
			data: 'practice='+practice+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_practice').html("<span class='ajx_success_msg'>Status Updated</span>");
				} else {
					$('#resmsg_practice').show();
					$('#resmsg_practice').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		$('#remsg_practice').fadeOut(2000);
	}
	
	//Set the Project Currency//
	function setProjectCurny()
	{
		$('#resmsg_currency').empty();
		var currency = $('#currency').val();
		
		if(currency == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_currency/',
			dataType: 'json',
			data: 'currency='+currency+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_currency').html("<span class='ajx_success_msg'>Currency Updated</span>");
				} else {
					$('#resmsg_currency').show();
					$('#resmsg_currency').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		$('#resmsg_currency').fadeOut(4000);
	}
	/*
	*@Method setDepartments
	*@parameters department_id_fk, lead_id
	*@Use update department for particular leads
	*@Author eNoah - Mani.S
	*/
	function setDepartments() {
		$('#resmsg_departments').empty();
		var department_id_fk = $('#department_id_fk').val();
		
		if(department_id_fk == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_departments/',
			dataType: 'json',
			data: 'department_id_fk='+department_id_fk+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_departments').html("<span class='ajx_success_msg'>Status Updated</span>");
				} else {
					$('#resmsg_departments').show();
					$('#resmsg_departments').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
				setTimeout('timerfadeout()', 2000);
			}
		});
	}
	
	/*
	*@Method setProjectTypes
	*@parameters project_types, lead_id
	*@Use update project types for particular leads
	*@Author eNoah - Mani.S
	*/
	function setProjectTypes() {
		$('#resmsg_project_types').empty();
		var project_type = $('#project_type').val();
		
		if(project_type == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_project_types/',
			dataType: 'json',
			data: 'project_types='+project_type+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_project_types').html("<span class='ajx_success_msg'>Status Updated</span>");
				} else {
					$('#resmsg_project_types').show();
					$('#resmsg_project_types').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		setTimeout('timerfadeout()', 2000);
	}
	
	/*
	*@Method setProjectTypes
	*@parameters project_types, lead_id
	*@Use update project types for particular leads
	*@Author eNoah - Mani.S
	*/
	function setEconProjectTypes() {
		$('#resmsg_econ_project_types').empty();
		var project_types = $('#project_types').val();
		
		if(project_types == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_econ_project_types/',
			dataType: 'json',
			data: 'project_types='+project_types+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_econ_project_types').html("<span class='ajx_success_msg'>Status Updated</span>");
				} else {
					$('#resmsg_econ_project_types').show();
					$('#resmsg_econ_project_types').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		setTimeout('timerfadeout()', 2000);
	}
	
	/*
	*@Method setDepartments
	*@parameters department_id_fk, lead_id
	*@Use update department for particular leads
	*@Author eNoah - Mani.S
	*/
	function setResourceType() {
		$('#resmsg_resource_type').empty();
		var resource_type = $('#resource_type').val();
		
		if(resource_type == '') {
			return false;
		}

		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_resource_type/',
			dataType: 'json',
			data: 'resource_type='+resource_type+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg_resource_type').html("<span class='ajx_success_msg'>Status Updated</span>");
				} else {
					$('#resmsg_resource_type').show();
					$('#resmsg_resource_type').html("<span class='ajx_failure_msg'>"+data.error+"</span>");
				}
				$.unblockUI();
			}
		});
		setTimeout('timerfadeout()', 2000);
	}
	

	//update the project status.
	function setProjectStatus() {
		
		if($('#pjt_status').val() == $('#pjt_status_hidden').val()) {
			return false;
		}
		
		var pjt_stat = $('#pjt_status').val();
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_project_status/',
			dataType: 'json',
			data: 'pjt_stat='+pjt_stat+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg').show();
					$('#resmsg').html("<span class='ajx_success_msg'>Status Updated.</span>");
					setTimeout(function(){
						$.blockUI({
							message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
							css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
						});
						window.location.reload(true);
					},500);
					$.unblockUI();
				} else {
					$('#resmsg').show();
					$('#resmsg').html("<span class='ajx_failure_msg'>"+data.error+".</span>");
					$.unblockUI();
				}
			}
		});
		setTimeout('timerfadeout()', 3000);
		return false;
	}
	
	function show_processing(){
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});		
	}
	
	function show_updating(){
		setTimeout(function(){
			$.blockUI({
				message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			window.location.reload(true);
		},500);
		$.unblockUI();
	}
	
	//update the project status.
	function setProjectManager() {
		
		$('#resmsg1').hide();
		var project_manager = $('#project_manager').val();
		
		if(project_manager == '') {
			$('#resmsg1').show();
			$('#resmsg1').html("<span class='ajx_failure_msg'>Please select Project Manager!.</span>");
			return false;
		}
		
		show_processing();
		 
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_project_manager/',
			dataType: 'json',
			data: 'project_manager='+project_manager+'&project_code='+project_code+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg1').show();
					$('#resmsg1').html("<span class='ajx_success_msg'>Project Manager Updated.</span>");
					setTimeout(function(){
						$.blockUI({
							message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
							css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
						});
						window.location.reload(true);
					},500);
					$.unblockUI();
				} else {
					$('#resmsg1').show();
					$('#resmsg1').html("<span class='ajx_failure_msg'>"+data.error+".</span>");
					$.unblockUI();
				}
			}
		});
		setTimeout('timerfadeout()', 3000);
		return false;
	}
	
	function setProjectMembers() {
		
		$('#resmsg2').hide();
		var project_team_members = $('#project_team_members').val();
		if(!project_team_members) {
			$('#resmsg2').show();
			$('#resmsg2').html("<span class='ajx_failure_msg'>Please select Project Team Members!.</span>");
			return false;
		}
		
		show_processing();
		 
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_project_members/',
			dataType: 'json',
			data: 'project_team_members='+project_team_members+'&project_code='+project_code+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg2').show();
					$('#resmsg2').html("<span class='ajx_success_msg'>Project Team Members Updated.</span>");
					show_updating();
				} else {
					$('#resmsg2').show();
					$('#resmsg2').html("<span class='ajx_failure_msg'>"+data.error+".</span>");
					$.unblockUI();
				}
			}
		});
		setTimeout('timerfadeout()', 3000);
		return false;
	}
	

	function setStakeMembers() {
		
		$('#resmsg3').hide();
		var stake_members = $('#stake_members').val();
		if(!confirm("Are you sure to update Stake Holders?")) {
			$('#resmsg3').show();
			$('#resmsg3').html("<span class='ajx_failure_msg'>Please select Stake Holders!.</span>");
			return false;
		}
		
		show_processing();
		 
		$.ajax({
			type: 'POST',
			url: site_base_url+'project/set_stake_holders/',
			dataType: 'json',
			data: 'stake_members='+stake_members+'&project_code='+project_code+'&lead_id='+curr_job_id+'&'+csrf_token_name+'='+csrf_hash_token,
			success: function(data) {
				if (data.error == false) {
					$('#resmsg3').show();
					$('#resmsg3').html("<span class='ajx_success_msg'>Project Stake Holders Updated.</span>");
					show_updating();
				} else {
					$('#resmsg3').show();
					$('#resmsg3').html("<span class='ajx_failure_msg'>"+data.error+".</span>");
					$.unblockUI();
				}
			}
		});
		setTimeout('timerfadeout()', 3000);
		return false;
	}
	
	function setProjectStatusDate(date_type) {
		
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		
		$("#dates_errmsg").empty();
		var set_date_type, date_val, d_class;
		if (date_type == 'start') {
			set_date_type = 'start';
			date_val = $('#project-start-date').val();
			d_class = 'startdate';
		} else {
			set_date_type = 'end';
			date_val = $('#project-due-date').val();
			d_class = 'deadline';
		}

		var pr_date = $('#project_lead').val()
		if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
			//alert('Please enter planned ' + set_date_type + ' date');
			if (set_date_type == 'start') {
				//showing error message As DOM type - Start Date
				$("#dates_errmsg").text('Please enter planned ' + set_date_type + ' date');
			} else {
				//showing error message As DOM type - End Date
				$("#dates_errmsg").text('Please enter planned ' + set_date_type + ' date');
			}
			$.unblockUI();
			$("#dates_errmsg").show();
			return false;
		} else {
			var params 				= {'lead_id':curr_job_id, 'date_type':set_date_type, 'date':date_val};
			params[csrf_token_name] = csrf_hash_token;
		
			$.post(
				site_base_url+'project/set_project_status_date/',
				params,
				function(_data) {
					try {
						eval ('var data = ' + _data);
						if (typeof(data) == 'object') {
							if (data.error == false) {
								$('#dates_errmsg').html("<span class='ajx_success_msg'>Saved Successfully...</span>");
							} else {
								$("#dates_errmsg").text(data.error);
							}
						} else {
							$("#dates_errmsg").text('Updating faild, please try again.');
						}
					} catch (e) {
						$("#dates_errmsg").text('Invalid response, your session may have timed out.');
					}
					$.unblockUI();
					$("#dates_errmsg").show();
					setTimeout('timerfadeout()', 3000);
				}
			);
		}
	}
	
	//Removing the Project Dates
	function rmProjectStatusDate(date_type) {
		switch(date_type) {
			case 'start':
				var txtbx = 'project-start-date';
			break;
			case 'due':
				var txtbx = 'project-due-date';
			break;
			case 'act-start':
				var txtbx = 'actual-project-start-date';
			break;
			default:
				var txtbx = 'actual-project-due-date';
		}
		if($('#'+txtbx).val() == '') {
			return false;
		}
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		
		$("#dates_errmsg").empty();

		var params 				= {'lead_id':curr_job_id, 'date_type':date_type};
		params[csrf_token_name] = csrf_hash_token;
	
		$.post(
			site_base_url+'project/rm_project_status_date/',
			params,
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('#dates_errmsg').html("<span class='ajx_success_msg'>Updated Successfully...</span>");
							$("#"+txtbx).val("");
						} else {
							$("#dates_errmsg").text(data.error);
						}
					} else {
						$("#dates_errmsg").text('Updating faild, please try again.');
					}
				} catch (e) {
					$("#dates_errmsg").text('Invalid response, your session may have timed out.');
				}
				$.unblockUI();
				$("#dates_errmsg").show();
				setTimeout('timerfadeout()', 3000);
			}
		);
	}


	function actualSetProjectStatusDate(date_type) {
		
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		
		var set_date_type, date_val, d_class;
		
		$("#dates_errmsg").empty();
		if (date_type == 'start')
		{
			set_date_type = 'start';
			type = 'actual-project-start-date';
			date_val = $('#actual-project-start-date').val();
			d_class = 'startdate';
		}
		else
		{
			set_date_type = 'end';
			type = 'actual-project-due-date';
			date_val = $('#actual-project-due-date').val();
			d_class = 'deadline';
		}
		var pr_date = $('#project_lead').val()
		if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
			if (set_date_type == 'start') {
				$("#dates_errmsg").text('Please enter actual ' + set_date_type + ' date');
			} else {
				$("#dates_errmsg").text('Please enter actual ' + set_date_type + ' date');
			}
			$.unblockUI();
			$("#dates_errmsg").show();
			return false; 
		} else {
		
			var params 					 = {'lead_id':curr_job_id, 'date_type':set_date_type, 'date':date_val};
			params[csrf_token_name]      = csrf_hash_token;
			
			$.post(
				site_base_url+'project/actual_set_project_status_date/',
				params,
				function(_data) { //alert(_data);
					try {
						eval ('var data = ' + _data);
						if (typeof(data) == 'object') {
							if (data.error == false) {
								// $('h6.actual-project-' + d_class + '-label span').text(date_val);
								// $('.actual-project-' + d_class + '-change:visible').hide(200);
								$('#dates_errmsg').html("<span class='ajx_success_msg'>Saved Successfully...</span>");
							} else {
								$("#dates_errmsg").text(data.error);
								$('#' + type).val("");
							}
						} else {
							$("#dates_errmsg").text('Updating faild, please try again.');
						}
					} catch (e) {
						$("#dates_errmsg").text('Invalid response, your session may have timed out.');
					}
					$.unblockUI();
					$("#dates_errmsg").show();
					setTimeout('timerfadeout()', 3000);
				}
			);
		}
	}


	function whatAreStickies() {
		var msg = 'Stickies are logs that are important.\nInformation that is vital to the job.\nInformtion that you need to find quickily without reading through all the communication.\nA URL, FTP/MySQL details, Important changes etc.';
		alert(msg);
		return false;
	}

	function whatIsSignature() {
		var msg = 'This is your signature!\nThis will be attached to any log that you email through.\nGo to "Manage Signature" page to set your signature.';
		alert(msg);
		return false;
	}

	
	if(project_userdata!=''){
		/* function to add the auto log */
		function qcOKlog() {
			var msg = "eSmart QC Officer Log Check - All Appears OK";
			if (!window.confirm('Are you sure you want to stamp the OK log?\n"' + msg + '"')) return false;

			$('.user .production-manager-user').attr('checked', true);
			$('#job_log').val(msg);
			$('#add-log-submit-button').click();
		}
	}

	$(function() {
		$('#set-payment-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy'});
		$('#payment-recieved-terms .pick-date, #pr_date_3').datepicker({
			dateFormat: 'dd-mm-yy', 
			maxDate: '0',
			beforeShow : function(input, inst) {
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
		$('.milestone_date .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -30, maxDate: '+1M' });
		$('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date, #sp_date_2').datepicker({
			dateFormat: 'dd-mm-yy', 
			//minDate: '0',
			beforeShow : function(input, inst) {
				$('#ui-datepicker-div')[ $(input).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
			}
		});
		$('#month_year').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'MM yy',
			showButtonPanel: true,
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
		$('.task-list-item').livequery(function(){
			$(this).hover(
				function() { $('.delete-task', $(this)).css('display', 'block'); },
				function() { $('.delete-task', $(this)).css('display', 'none'); }
			);
		});

		$('#email_to_customer').change(function(){
			if ($(this).is(':checked'))	{
				$('#multiple-client-emails').slideDown(400)
					.children('input[type=checkbox]:first').attr('checked', true);
			} else {
				$('#additional_client_emails').val('');
				$('#multiple-client-emails').children('input[type=checkbox]').attr('checked', false).end()
					.slideUp(400);
			}
		});

		$.fn.__tabs = $.fn.tabs;
		$.fn.tabs = function (a, b, c, d, e, f) {
			var base = location.href.replace(/#.*$/, '');
			$('ul>li>a[href^="#"]', this).each(function () {
				var href = $(this).attr('href');
				$(this).attr('href', base + href);
			});
			$(this).__tabs(a, b, c, d, e, f);
		};

		$( "#project-tabs" ).tabs({
			beforeActivate: function( event, ui ) {
				
				var evnt_id = ui.newPanel[0].id;
				
				switch(evnt_id){
					case 'jv-tab-z': alert(evnt_id+'-'+project_jobid);
						updtActualProjectValue(project_jobid);
					break;
					case 'jv-tab-z-a':
						viewOtherCost(project_jobid);
					break;
					case 'jv-tab-1':
						$('.payment-terms-mini-view1').html('');
						loadPayment();
					break;
					case 'jv-tab-3':
						loadExistingFiles($('#filefolder_id').val());
						showBreadCrumbs($('#filefolder_id').val());
					break;
					case 'jv-tab-4':
						loadExistingTasks();
					break;
					case 'jv-tab-4-5':
						$('.payment-received-mini-view1').hide();
					break;
					case 'jv-tab-5':
						loadCustomer(quote_id);
					break;
					case 'jv-tab-9':
						loadLogs(project_jobid);
					break;
				}
				/* if (ui.newPanel[0].id=='jv-tab-z') {
					updtActualProjectValue(project_jobid);
				}
				if (ui.newPanel[0].id=='jv-tab-z-a') {
					viewOtherCost(project_jobid);
				}
				if (ui.newPanel[0].id=='jv-tab-1') {
					$('.payment-terms-mini-view1').html('');
					loadPayment();
				}
				if (ui.newPanel[0].id=='jv-tab-3') {				
					loadExistingFiles($('#filefolder_id').val());
					showBreadCrumbs($('#filefolder_id').val());
				}
				if (ui.newPanel[0].id=='jv-tab-4') {
					loadExistingTasks();
				}
				if (ui.newPanel[0].id=='jv-tab-4-5') {
					$('.payment-received-mini-view1').hide();
				}
				if (ui.newPanel[0].id=='jv-tab-5') {
					loadCustomer(quote_id);
				}
				if (ui.newPanel[0].id=='jv-tab-9') {
					loadLogs(project_jobid);
				} */
				
			}
		});
		
		$( "#map_add_file" ).tabs();
		$( "#oc_map_add_file" ).tabs();

		$('#job-url-list li a:not(.file-delete)').livequery(function(){
			$(this).click(function(){
				window.open(this.href);
				return false;
			});
		});
	

		/* try {
			var sb_ol = $('.status-bar').offset().left;
			$('.status-bar').mousemove(function(e){
				var wd = e.clientX - sb_ol;
				$('.over', $(this)).css({width: wd + 'px', opacity:0.5});
			});
			
			$('.status-bar').bind('mouseleave', function(e){
				$('.over', $(this)).stop().animate({opacity:0}, 600);
			});
			
			$('.status-bar a').click(function(){
				var pos = $(this).attr('rel');
			
				if (window.confirm('Are you sure that you want to change\nthe status to ' + pos * 10 +'% completion?'))
				{
					updateJobStatus(pos);
				}
				$('.status-bar span.over').fadeOut();
				return false;
			});
		} catch (e) { if (window.console) console.log(e); } */


		if (project_complete_status!='') {
			updateVisualStatus(project_complete_status);
		}

		
		$('.jump-to-job select').change(function(){
			var _new_location = proj_location;
			document.location = _new_location.replace('{{lead_id}}', $(this).val());
		});


		/* $('#job_log').siblings().hide();

		$('#job_log').focus(function(){
			$(this).siblings(':hidden').not('#multiple-client-emails').slideDown('fast');
			if ($(this).val() == 'Click to view options') {
				$(this).val('');
				$(this).removeClass('gray-text');
			}
		}); */


		/* job tasks character limit */
		$('#job-task-desc').keyup(function(){
			var desc_len = $(this).val();
			
			if (desc_len.length > 240) {
				$(this).focus().val(desc_len.substring(0, 240));
			}
			
			var remain_len = 240 - desc_len.length;
			if (remain_len < 0) remain_len = 0;
			
			$('#task-desc-countdown').text(remain_len);
		});

		$('#edit-job-task-desc').keyup(function(){
			var desc_len = $(this).val();
			
			if (desc_len.length > 240) {
				$(this).focus().val(desc_len.substring(0, 240));
			}
			
			var remain_len = 240 - desc_len.length;
			if (remain_len < 0) remain_len = 0;
			
			$('#edit-task-desc-countdown').text(remain_len);
		});

		// Sasha's quick keys
		$('#job_log').keydown(function (e) {

			if (e.ctrlKey && e.keyCode == 13) {

				// Entered values:
				var minutesInput = $('#log_minutes');
				var minutes = minutesInput.val();

				// Check the values that are required (time and recipients)
				//
				// if either are empty, use prompt() dialog boxes to use them.
				// EDIT: In fact, a prompt will use enter, so we can jam it down if needed.

				var newMinutes = prompt('Time in minutes', minutes);
				if(minutes != newMinutes) {
					minutesInput.val(newMinutes);
				}

				var contactsText = prompt('Select contacts (min 3 letters). Seperate with a space.');
				var contacts = contactsText.split(' ');
				for(i in contacts) {
					// Check the ones that match.
					//
					// Modifications needed: this needs to be case insensitive.
					if(contacts[i].length >= 3) {
						contacts[i].replace(/\w+/g, function(a){
							contacts[i]  = a.charAt(0).toUpperCase() + a.substr(1).toLowerCase();
						});
						
						var scope = $('.user label:contains("' + contacts[i] + '")').parent();
						$('input[type=checkbox]', scope).attr('checked', true);
					}
				}
				var recipients = 'Send to the following recipients:\n';
				$('.user input[type=checkbox]:checked').each(
					function () {
						recipients += $('label', $(this).parent()).text() + '\n';
					}
				);
				
				if(confirm(recipients)) {
					addLog();
				}
				return false;
			}
		});
		
		
	});
	

	function checkRootReadAccess()
	{
		$.get(site_base_url+'ajax/request/check_root_folder_read_access/'+curr_job_id,{},function(data) {
		
			if(data !=1) {
				$('#files_actions').hide();
			}
		
		});
	}
	
	//function for getting the files based on ID
	function loadExistingFiles(ffolder_id) {
		$('#jv-tab-3').block({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		$.get(
			site_base_url+'ajax/request/get_project_files/'+curr_job_id+'/'+ffolder_id,
			{},
			function(data) {
				$('#list_file').html(data);
				$('#jv-tab-3').unblock();
				dataTable();
				$.unblockUI();
				
				var parent_folder_id = $('#filefolder_id').val();
				var current_folder_parent_id = $('#current_folder_parent_id').val();
				if(parent_folder_id == 'Files') {
				//alert(current_folder_parent_id);
				if(current_folder_parent_id != undefined) {
				
				showBreadCrumbs(current_folder_parent_id);
				loadExistingFiles(current_folder_parent_id);				
				$('#filefolder_id').val(current_folder_parent_id);		
				}
				}				
				$('#parent_folder_id').val(parent_folder_id);				
				
			}
		);
		return false;
	}
	
	
	function dataTable()
	{
		$('#list_file_tbl').dataTable({
			"iDisplayLength": 15,
			"sPaginationType": "full_numbers",
			"bInfo": true,
			"bPaginate": true,
			"bProcessing": true,
			"bServerSide": false,
			"bLengthChange": true,
			"bSort": true,
			"bFilter": false,
			"bAutoWidth": false,
			"bDestroy": true,
			"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ 0 ] }
			]
		});
	}

	var job_complete_percentage;

	function updateJobStatus(status) {
		$('.status-bar').block({
				message:'<img src="assets/img/ajax-loader.gif" />',
				css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
		});
		var params 				= {lead_id: curr_job_id, thermometer_val: status};
		params[csrf_token_name] = csrf_hash_token;
		
		$.post(
			site_base_url+'project/update_job_status/',
			params,
			function(_data) {
				try {
					eval('data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							pos_just_completed = true;
							updateVisualStatus(status);
							// location.reload();
						} else {
							alert(data.error);
						}
					} else {
						alert('Your session timed out!');
					}
				} catch (e) {
					alert('Your session timed out!');
				}
				$('.status-bar').unblock();
			}
		);
	}

	function updateVisualStatus(status)
	{
		// $('h3.status-title .small em strong').html(status);
		// $('.status-bar span.bar').animate({width: (status * 3) + 'px'}, 1000);
		// job_complete_percentage = status;
		$('h6.status-title .small em strong').html(status);
		$('.track-progress').css({'width':status +'%'});
		if(status==100){
			$(".progress-cont").css("width",100+"%")
		}else if(status==0){
			$('.track-progress-left').hide();
		}else{
			$(".progress-cont").removeAttr("style");
			$('.track-progress-left').show();
		}
		job_complete_percentage = status;
	}

	function setContractorJob()
	{
		$("div#errMsgPjtNulMem").hide();
		var contractors = [];

		var p = $('#project-member').val()

		var arr = new Array;
		$("#select2 option").each ( function() {
			arr.push ( $(this).val() );
		});

		if (arr.length === 0) {
			$("div#errMsgPjtNulMem").show();
		}

		$('select#select2 option').each(function(){
			contractors.push($(this).val());
		});

		var params = {'contractors': contractors.join(','), 'lead_id': curr_job_id, 'project-mem': p};
		params[csrf_token_name] = csrf_hash_token;
	
		var baseurl = $('.hiddenUrl').val();
		$.ajax({
			type: "POST",
			dataType: 'json',
			url : baseurl + 'project/ajax_set_contractor_for_job/',
			cache : false,
			data: params,
			success : function(response)
			{
				if(response.status == 'OK')
				{					
					document.location.reload();
				} 
				else 
				{
					alert(response.error);
				}
			}
		});
	}
	
	
	//////////////////////////----------------------X-----------------////////////////////////////
	//////////////////////////----------------------X-----------------////////////////////////////
	
	function populateJobOverview()
	{
		$('#jv-tab-4-5').block({
							message:'<img src="assets/img/ajax-loader.gif" />',
							css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
						});
		$.get(
			'ajax/request/get_job_overview/' + curr_job_id,
			{},
			function(detail)
			{
				if ($.trim(detail) != '')
				{
					$('#milestone-data tbody').html(detail);
					$('#milestone-data tbody tr .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
				}
				$('#jv-tab-4-5').unblock();
			}
		);
	}
			
			
		/////////////////////------------------X-------------------//////////////////////////////////////
			
		if(project_userdata!=''){
		
				var client_comm_options_order = [];
				
				$(function(){
					$('.client-comm-options input[type="checkbox"]').click(function(){
						var el = $(this);
						setTimeout(function(){
							if (el.is(':checked'))
							{
								if ($.inArray(el.attr('name'), client_comm_options_order) == -1)
								{
									client_comm_options_order.push(el.attr('name'));
								}
							}
							else
							{
								client_comm_options_order = $.grep(client_comm_options_order, function(value){ return value != el.attr('name') });
							}
						}, 80);
					})
				});
				
				function addClientCommOptions()
				{
					if ($('.client-comm-options input[type="checkbox"]:checked').size() < 1)
					{
						alert('Please select at least one option!');
						return false;
					}
					
					if ($('#job_log').val() != '')
					{
						if ( ! window.confirm('This will replace the text on the log window!\nProceed?'))
						{
							return false;
						}
					}
					
					var text_block = '\nYou are required to contact the client via the following means of communication in the following order:';
					for (i in client_comm_options_order)
					{
						var com_item = $('.client-comm-options input[name="' + client_comm_options_order[i] + '"]');
						text_block += '\n' + com_item.siblings('span').text() + ': ' + com_item.val();
					}
					
					$('#job_log').val(text_block);
					
					return false;
				}
		
		}
			
		///////////////////------------------------XXXXXXXXXXXX--------------////////////////////////////////


		$(document).ready(function() 
		{
			$('.checkUser').hide();
			$('.checkUser1').hide();
		});

		//Add Payment Terms Edit function Starts here 
		function paymentProfileEdit(eid) 
		{
			$(".payment-profile-view").show();
			var jid = project_jobid;
			setTimeout('timerfadeout()', 2000);
			var url = site_base_url+"project/payment_term_edit/"+eid+"/"+jid;
			$('#payment-profile-view').load(url);
		}

		function paymentProfileView() 
		{
			setTimeout('timerfadeout()', 2000);
			var url = site_base_url+"project/agreedPaymentView/"+project_jobid;
			$('#payment-profile-view').load(url);
		}
		function paymentProfileDelete(eid) 
		{
			var agree=confirm("Are you sure you want to delete this file?");
			if (agree) 
			{
				var jid = project_jobid;
				setTimeout('timerfadeout()', 2000);
				var url = site_base_url+"project/agreedPaymentDelete/"+eid+"/"+jid;
				$('.payment-terms-mini-view1').load(url);
			}
			else 
			{
				return false;
			}
		}

		function timerfadeout()
		{alert(123)
			$('#paymentfadeout').fadeOut();
			$('#rec_paymentfadeout').fadeOut();
			$('#pjt_lead_errormsg').fadeOut();
			$('#dates_errmsg').fadeOut();
			$('#msg_project_efforts').fadeOut();
			$('#errmsg_project_type').fadeOut();
			$('#pjt_id_errormsg, .checkUser, #id-existsval').fadeOut();
			$('#msErrNotifyFadeout').fadeOut();
			$('#errmsg_rag_status').fadeOut();
			$('#errmsg_bill_type').fadeOut();
			$('#resmsg_practice').empty();
			$('.succ_err_msg').empty();
			$('.ajx_success_msg').empty();
			$('#resmsg').empty();
		}

		function paymentReceivedEdit(pdid) 
		{
			$(".payment-recieved-view").show(); 
			var jid = project_jobid;
			var pdurl = site_base_url+"project/paymentEdit/"+pdid+"/"+jid;
			$('.payment-recieved-view').load(pdurl);
		}

		//<!--Add Received Payment Terms Delete function Starts here.-->
		function paymentReceivedDelete(eid,map) {
			var agree=confirm("Are you sure you want to delete this Payment?");
			if (agree) {
				var jid = project_jobid;
				setTimeout('timerfadeout()', 2000);
				var url = site_base_url+"project/receivedPaymentDelete/"+eid+"/"+jid+"/"+map;
				$('.payment-received-mini-view1').load(url);
			}
			else {
				return false;
			}
		}


		function paymentReceivedView() 
		{
			var url = site_base_url+"project/PaymentView";
			$('#payment-recieved-view').load(url);
		}

		function isNumberKey(evt) {
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
			else
			return true;
		}
		
		function isPaymentVal(evt) {
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if (charCode != 45 && charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
			else
			return true;
		}
		
		function setProjectEstimateHour() {
			$("#dates_errmsg").hide();
			var hour_val, h_class;
			hour_val=$('#project-estimate-hour').val();
			h_class = 'estimate-hour';

			if (hour_val=='') {
				$("#msg_project_efforts").html('<span class="ajx_failure_msg">Please enter project estimate hour.</span>');
				$("#msg_project_efforts").show();
				return false;
			}else {
				if(filterFloat(hour_val) == false){
					$("#msg_project_efforts").html('<span class="ajx_failure_msg">Please enter valid estimate hour.</span>');
					$("#msg_project_efforts").show();
					return false;
				}
				var params 				= {'lead_id':curr_job_id,'esthr':hour_val};
				params[csrf_token_name] = csrf_hash_token;
				
				$.blockUI({
					message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
					css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
				});
			
				$.post(
					site_base_url+'project/set_project_estimate_hour/',
					params,
					function(_data) {
						try {
							eval ('var data = ' + _data);
							if (typeof(data) == 'object') {
								if (data.error == false) {
									$('#msg_project_efforts').html("<span class='ajx_success_msg'>Saved Successfully...</span>");		
									varianceeff = $("#actualEff").val() - hour_val;
									$("#varianceEff").val(varianceeff);
								} else {
									$("#msg_project_efforts").html("<span class='ajx_failure_msg'>"+data.error+"</span>");
								}
							} else {
								$("#msg_project_efforts").html("<span class='ajx_failure_msg'>Updating faild, please try again.</span>");
							}
						} catch (e) {
							$("#msg_project_efforts").html("<span class='ajx_failure_msg'>Invalid response, your session may have timed out.</span>");
						}
						$("#msg_project_efforts").show();
						$.unblockUI();
						setTimeout('timerfadeout()', 2000);
					}
				);
			}
		}
		
		function setProjectType() {
			$("#errmsg_project_type").hide();
			
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var project_type_val, p_class,project_val;
			project_type_val=$('#project_type').val();
			p_class = 'type';

			if (project_type_val=='') {
				$("#errmsg_project_type").html("<span class='ajx_failure_msg'>Please select project type.</span>");
				$("#errmsg_project_type").show();
				$.unblockUI();
				setTimeout('timerfadeout()', 3000);
				return false;
			}else {
				var params 				= {'lead_id':curr_job_id,'project_type':project_type_val};
				params[csrf_token_name] = csrf_hash_token;
			
				$.post(
					site_base_url+'project/set_project_type/',
					params,
					function(_data) {
						try {
							eval ('var data = ' + _data);
							if (typeof(data) == 'object') {
								if (data.error == false) {
									if(project_type_val =='1'){
										project_val='Fixed';
									}else if(project_type_val =='2'){
										project_val='Internal';
									}else if(project_type_val =='3'){
										project_val='T&M';
									}									
									$('#errmsg_project_type').show();
									$('#errmsg_project_type').html("<span class='ajx_success_msg'>Status Updated.</span>");
								} else {
									$("#errmsg_project_type").text(data.error);
									$("#errmsg_project_type").show();
								}
								$.unblockUI();
							} else {
								$("#errmsg_project_type").show();
								$("#errmsg_project_type").html("<span class='ajx_failure_msg'>Updating faild, please try again.</span>.");
								$.unblockUI();
							}
						} catch (e) {
							$("#errmsg_project_type").html("<span class='ajx_failure_msg'>Invalid response, your session may have timed out.</span>");
							$("#errmsg_project_type").show();
							$.unblockUI();
						}
					}
				);
				setTimeout('timerfadeout()', 3000);
			}
		}
		
		
		var filterFloat = function (value) {
		    if(/^\-?([0-9]+(\.[0-9]+)?|Infinity)$/
		      .test(value))
		      return Number(value);
		  return false;
		}
		
	////////////////////------------------------XXXXXXXXXXX--------------------------//////////////////////////
	//////Add Milestones////////Start here//////
	function addMilestoneTerms()
	{
		$('#jobid_fk').val(curr_job_id);
		var errors = [];
		
		if ($('#jobid_fk').val() == 0) {
			errors.push('Milestone not properly loaded!');
		}
		if ( ($.trim($('#milestone_name').val()) == '') ) {
			errors.push('<p>Enter Milestone Name.</p>');
		}
		if ( ($.trim($('#ms_plan_st_date').val()) == '') ) {
			errors.push('<p>Please Select Planned Start Date.</p>');
		}
		if ( ($.trim($('#ms_plan_end_date').val())  == '') ) { 
			errors.push('<p>Please Select Planned End Date.</p>');
		}
		if ($.trim($('#ms_effort').val())  == '') {
			errors.push('<p>Enter Milestone Effort.</p>');
		}
		if (errors.length > 0) {
			//alert(errors.join('\n'));
			setTimeout('timerfadeout()', 8000);
			$('#msErrNotifyFadeout').show();
			$('#msErrNotifyFadeout').html(errors.join(''));
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var form_data = $('#milestone-management').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+'project/addMilestones',
				form_data,
				function(data) {
					
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.ms-toggler').slideToggle();
						$('#milestone_view_det').empty();
						var data = data.split('#');
						$('#milestone_view_det').html(data[0]);
						updateJobStatus(data[1]);
						
					}
					$.unblockUI();
					$('#milestone-management')[0].reset();
				}
			);
		}
	}
	//////Add Milestones////////End here//////

	//////Updating Milestones////////Start here//////
	function updateMilestoneTerms(ms_id)
	{
		$('#jobid_fk').val(curr_job_id);
		var errors = [];
		
		if ($('#jobid_fk').val() == 0) {
			errors.push('Milestone not properly loaded!');
		}
		if ( ($.trim($('#milestone_name').val()) == '') ) {
			errors.push('<p>Enter Milestone Name.</p>');
		}
		if ( ($.trim($('#ms_plan_st_date').val()) == '') ) {
			errors.push('<p>Please Select Planned Start Date.</p>');
		}
		if ( ($.trim($('#ms_plan_end_date').val())  == '') ) { 
			errors.push('<p>Please Select Planned End Date.</p>');
		}
		if ($.trim($('#ms_effort').val())  == '') {
			errors.push('<p>Enter Milestone Effort.</p>');
		}
		if (errors.length > 0) {
			//alert(errors.join('\n'));
			setTimeout('timerfadeout()', 8000);
			$('#msErrNotifyFadeout').show();
			$('#msErrNotifyFadeout').html(errors.join(''));
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var form_data = $('#milestone-management').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+'project/addMilestones/'+ms_id,
				form_data,
				function(data) {
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.ms-toggler').slideToggle();
						$('#milestone_view_det').empty();
						var data = data.split('#');
						$('#milestone_view_det').html(data[0]);
						updateJobStatus(data[1]);
					}
					$.unblockUI();
					$('#milestone-add-view').empty();
					// $('#addNew-ms').hide();
					addMilestoneForm();
					
				}
			);
		}
	}
	//////Update Milestones////////End here//////
	
	function addMilestoneForm() {
		setTimeout('timerfadeout()', 2000);
		var url = site_base_url+"project/addMilestoneFormView";
		$('#milestone-add-view').load(url);
		$('.ms-toggler').slideToggle();
	}
	
	//Milestone Edit Terms function Starts here
	function milestoneEditTerm(ms_id)
	{ 
		$(".milestone-table").show();
		var jobid = project_jobid;
		setTimeout('timerfadeout()', 2000);
		var url = site_base_url+"project/milestone_edit_term/"+ms_id+"/"+jobid;
		$('#milestone-add-view').load(url);
		$('#addNew-ms').hide();
	}
	
	//Milestone Delete Terms function Starts here
	function milestoneDeleteTerm(ms_id)
	{
		var agree=confirm("Are you sure you want to delete this file?");
		var pjtid = project_jobid;
		if (agree) {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var form_data = $('#milestone-management').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+"project/deleteMilestoneTerm/"+ms_id+"/"+pjtid,
				form_data,
				function(data) {
					if (data.error) {
						alert(data.errormsg);
					} else {
						setTimeout('timerfadeout()', 2000);
						var data = data.split('#');
						$('#milestone_view_det').html(data[0]);
						updateJobStatus(data[1]);
					}
					$.unblockUI();
				}
			);
		} else {
			return false;
		}
	}
	
	$(document).ready(function() {
		/*Export Milestones*/
		$('#milestone-export').on('click',function(e) {
			var obj = $('#milestone-data tbody tr');
			if (obj.length == 0) {
				alert('No records are there to Export!');
				return false;
			}
			e.preventDefault();
			var baseurl = site_base_url;
			var url 	= baseurl+"project/exportMilestoneTerms";
			
			var form = $('<form action="' + url + '" method="post">' +
			'<input id="token" type="hidden" name="'+csrf_token_name+'" value="'+csrf_hash_token+'" />'+
			'<input type="hidden" name="lead_id" value="' +curr_job_id+ '" />' +
			'</form>');
			$('body').append(form);
			$(form).submit();
			return false;
		});
		/*Email Milestones*/
		$('#milestone-email').on('click',function(e) {
			e.preventDefault();
			emailMilestones();
		});
	});
	
	/*Email Milestones*/
	function emailMilestones()
	{
		var qc_job_title = project_job_title;
		var obj = $('#milestone-data tbody tr');
		if (obj.length == 0)
		{
			alert('No records are there to email!');
			return false;
		}
		var email_data = '';
		var table = $('#milestone-data tbody');
		obj.each(function(){
			var $tds = $(this).find('td'),
				msname	 = $tds.eq(0).text(),
				msplst	 = $tds.eq(1).text(),
				msplend	 = $tds.eq(2).text();
				msactst	 = $tds.eq(3).text();
				msactend = $tds.eq(4).text();
				mseff	 = $tds.eq(5).text();
				mscomp	 = $tds.eq(6).text();
				msstaus	 = $tds.eq(7).text();
			
			email_data += 'MileStone Name: ' + msname + 
						'\nPlanned Start Date: ' + msplst + 
						'\nPlanned End Date: ' + msplend + 
						'\nActual Start Date: ' + msactst + 
						'\nActual End Date: ' + msactend +
						'\nEffort: ' + mseff +
						'\nCompletion: ' + mscomp + '%' +
						'\nCompletion: ' + msstaus+'\n\n';
			
		});
		$('#job_log').focus().val('\nTimeline for the project: ' + qc_job_title + '\n' +  email_data);
		$('html, body').animate({ scrollTop: $('#job_log').offset().top }, 500);
		return false;
	}
	
	//Milestones dateformats conditions -milestone-management
	
	$(document).ready(function() {
		$('#ms_plan_st_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
			if($('#ms_plan_end_date').val!='')
			{
				$('#ms_plan_end_date').val('');
			}
			var return_date = $('#ms_plan_st_date').val();
			$('#ms_plan_end_date').datepicker("option", "minDate", return_date);
		}});
		$('#ms_plan_end_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
		
		$('#ms_act_st_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, onSelect: function(date) {
			if($('#ms_act_end_date').val!='')
			{
				$('#ms_act_end_date').val('');
			}
			var return_date=$('#ms_act_st_date').val();
			$('#ms_act_end_date').datepicker("option", "minDate", return_date);
		}});
		
		$('#ms_act_end_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	});
	
	//Set the RAG Status
	$(document).ready(function() {
		
		$( ".rag_stat" ).change(function() {
			$("#errmsg_rag_status").empty();
			$("#errmsg_rag_status").hide();
			var rag_status_val = $(this).val();
			if (rag_status_val=='') {
				$("#errmsg_rag_status").text('Please check RAG status');
				$("#errmsg_rag_status").show();
				return false;
			} else {
				var params 				= {'lead_id':curr_job_id, 'rag_status':rag_status_val};
				params[csrf_token_name] = csrf_hash_token;
			
				$.post(
					site_base_url+'project/set_rag_status/',
					params,
					function(_data) {
						if (typeof(_data) == 'object') {
							if (_data.error == false) {
								if(rag_status_val =='1'){
									rag_val='Red';
								}else if(rag_status_val =='2'){
									rag_val='Amber';
								}else if(rag_status_val =='3'){
									rag_val='Green';
								}
								// $('h6.rag-' + r_class + '-label span').text(rag_val);
								// $('.rag-status-change').hide(200);
								$('#errmsg_rag_status').html("<span class='ajx_success_msg'>Status Updated.</span>");
								$("#errmsg_rag_status").show();
							} else {
								$("#errmsg_rag_status").text(data.error);
								$("#errmsg_rag_status").show();
							}
						} else {
							$("#errmsg_rag_status").text('Updating faild, please try again.');
							$("#errmsg_rag_status").show();
						}
					},"json"
				);
			}
			setTimeout('timerfadeout()', 2000);
		});
		
		$( ".sow_stat" ).change(function() {
			$("#errmsg_sow_status").empty();
			$("#errmsg_sow_status").hide();
			var sow_status = $(this).val();
			if (sow_status=='') {
				$("#errmsg_sow_status").text('Please Check SOW Status');
				$("#errmsg_sow_status").show();
				return false;
			} else {
				$.blockUI({
					message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
					css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
				});
				var params 				= {'lead_id':curr_job_id, 'sow_status':sow_status};
				params[csrf_token_name] = csrf_hash_token;
			
				$.post(
					site_base_url+'project/set_sow_status/',
					params,
					function(_data) {
						if (typeof(_data) == 'object') {
							if (_data.error == false) {
								$('#errmsg_sow_status').html("<span class='ajx_success_msg'>Status Updated.</span>");
								setTimeout(function(){
									$.blockUI({
										message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
										css: {background:'#666', border: '2px solid #999', padding:'2px', height:'35px', color:'#333'}
									});
									window.location.reload(true);
								},500);
							} else {
								$("#errmsg_sow_status").text(data.error);
							}
						} else {
							$("#errmsg_sow_status").text('Updating faild, please try again.');
						}
					},"json"
				);
			}
			$("#errmsg_sow_status").show();
		});

		$( ".bill_type" ).change(function() {
			$("#errmsg_bill_type").empty();
			$("#errmsg_bill_type").hide();
			var billing_type_val = $(this).val();
			if (billing_type_val=='') {
				$("#errmsg_bill_type").text('Please Check Bill Type');
				$("#errmsg_bill_type").show();
				return false;
			} else {
				$.blockUI({
					message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
					css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
				});
				var params 				= {'lead_id':curr_job_id, 'billing_type':billing_type_val};
				params[csrf_token_name] = csrf_hash_token;
			
				$.post(
					site_base_url+'project/set_bill_type/',
					params,
					function(_data) {
						if (typeof(_data) == 'object') {
							if (_data.error == false) {
								$('#errmsg_bill_type').html("<span class='ajx_success_msg'>Status Updated.</span>");
								setTimeout(function(){
									$.blockUI({
										message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
										css: {background:'#666', border: '2px solid #999', padding:'2px', height:'35px', color:'#333'}
									});
									window.location.reload(true);
								},500);
							} else {
								$("#errmsg_bill_type").text(data.error);
							}
						} else {
							$("#errmsg_bill_type").text('Updating faild, please try again.');
						}
					},"json"
				);
			}
			$("#errmsg_bill_type").show();
		});
		
		//set customer type
		$( ".customer_type" ).change(function() {
			$("#errmsg_customer_type").empty();
			$("#errmsg_customer_type").hide();
			var customer_type_val = $(this).val();
			if (customer_type_val=='') {
				$("#errmsg_customer_type").text('Please Check Customer Type');
				$("#errmsg_customer_type").show();
				return false;
			} else {
				$.blockUI({
					message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
					css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
				});
				var params 				= {'lead_id':curr_job_id, 'customer_type':customer_type_val};
				params[csrf_token_name] = csrf_hash_token;
			
				$.post(
					site_base_url+'project/set_customer_type/',
					params,
					function(_data) {
						if (typeof(_data) == 'object') {
							if (_data.error == false) {
								$('#errmsg_customer_type').html("<span class='ajx_success_msg'>Customer Type Updated.</span>");
								setTimeout(function(){
									$.blockUI({
										message:'<h4>Status Updating...</h4><img src="assets/img/ajax-loader.gif" />',
										css: {background:'#666', border: '2px solid #999', padding:'2px', height:'35px', color:'#333'}
									});
									window.location.reload(true);
								},500);
							} else {
								$("#errmsg_customer_type").text(data.error);
							}
						} else {
							$("#errmsg_bill_type").text('Updating faild, please try again.');
						}
					},"json"
				);
			}
			$("#errmsg_customer_type").show();
		});
	});

/*RAG Status Script - Start*/
$(function(){

	$('.rag-status input:radio').screwDefaultButtons({
		image: '.',
		width: 25,
		height: 25
	});
	
	$('input#red').parent().addClass('styleradio-1');
	$('input#amber').parent().addClass('styleradio-2');
	$('input#green').parent().addClass('styleradio-3');
	
	if(rag_stat_id == 1) {
		$(".rag-status").children("div").eq(0).attr("id","red-radio");
	}
	if(rag_stat_id == 2) {
		$(".rag-status").children("div").eq(1).attr("id","amber-radio");
	}
	if(rag_stat_id == 3) {
		$(".rag-status").children("div").eq(2).attr("id","green-radio");
	}

});
/*RAG Status Script - End*/

/*For Timesheet Metrics Data - Start*/
	$(function() {
		$('#filter_metrics').submit(function() {
			var start_date = $("#metrics_month").val()+'-'+$("#metrics_year").val();
			var cur_name   = $("#expect_worth_name").val();
			
			if(start_date == '')
			return false;
			
			var params = {'start_date':start_date, 'project_id':project_jobid, 'project_code':project_code, 'expect_worth_id':expect_worth_id, 'cur_name':cur_name};
			params[csrf_token_name] = csrf_hash_token; 
			if($(this).attr("id") == 'filter_metrics'){
				$('#metrics_data').hide();
				$('#load').show();
			}
			$.ajax({
				type: 'POST',
				url: site_base_url+'project/filterTimesheetMetricsData',
				data: params,
				success: function(data) {
					$(".inner_timesheet" ).html(data);
					$('#metrics_data').show();
					$('#load').hide();
				}
			});
			return false;
		});
	});
/*For Timesheet Metrics Data - End*/

/*Project module Invoice genration*/
function generate_inv(eid) {
	// window.location.href = site_base_url+'project/generateInvoice/'+eid+"/"+pjtid;
	$('#rec_paymentfadeout').empty();
	var agree = confirm("Are you sure you want to generate invoice?\nIt will send an email to accounts department.");
	var pjtid = project_jobid;
	if (agree) {
		$.blockUI({
			message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
			css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
		});
		
		var form_data = csrf_token_name+'='+csrf_hash_token;
		$.post( 
			site_base_url+"project/generateInvoice/"+eid+"/"+pjtid,
			form_data,
			function(data) {
				if (data.error) {
					// alert(data.errormsg);
					$('#rec_paymentfadeout').html(data.errormsg);
				} else {
					$('#rec_paymentfadeout').html('<span class=ajx_success_msg>Status Updated</span>');
					loadPayment();
					reset_paymentdata();
				}
				$.unblockUI();
			}
			,'json'
		);
		setTimeout('timerfadeout()', 4000);
	} else {
		return false;
	}
	
}
/*
*FOR PAYMENT MILESTONES 
*/
function open_files(leadid,type) {
	var params				= {'leadid':leadid};
	params[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_files_tree_structure',
		// dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#all_file_list').html(data);
			$('#exp_type').val(type);
			$.blockUI({
				message: $('#map_add_file'),
				css: { border: '2px solid #999',color:'#333',padding:'8px',top: ($(window).height() + 400) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '400px',position: 'absolute', maxHeight: '450px', 'overflow-y':'auto', 'overflow-x':'hidden'}			
			});
			$( "#map_add_file" ).parent().addClass( "no-scroll" );
		}
	});
	return false;
}

function open_files_othercost(leadid,type) {
	var params				= {'leadid':leadid};
	params[csrf_token_name] = csrf_hash_token;
	$.ajax({
		type: 'POST',
		url: site_base_url+'ajax/request/get_files_tree_structure_for_other_cost',
		// dataType: 'json',
		data: params,
		success: function(data) {
			// console.info(data);
			$('#oc_all_file_list').html(data);
			$('#oc_exp_type').val(type);
			$.blockUI({
				message: $('#oc_map_add_file'),
				css: { border: '2px solid #999',color:'#333',padding:'8px',top: ($(window).height() + 400) /2 + 'px',left: ($(window).width() - 400) /2 + 'px',width: '400px',position: 'absolute', maxHeight: '450px', 'overflow-y':'auto', 'overflow-x':'hidden'}			
			});
			$( "#oc_map_add_file" ).parent().addClass( "no-scroll" );
		}
	});
	return false;
}

function select_files() {
	var data = '';
	var id = '';
	jQuery.each( $(".attach_file:checked"), function( i, val ) {
		filename = $(this).val().split('~');
		id += filename[0] + ',';
		data += '<div style="float: left; width: 100%;"><input type="hidden" name="file_id[]" value="'+filename[0]+'"><span style="float: left;">'+filename[1]+'</span> <a id="'+filename[0]+'" class="del_file"> </a></div>';
	});

	$('#show_files').append(data);
	$.unblockUI();
	return false;
}
function select_othercost_files() {
	var data = '';
	var id   = '';
	jQuery.each( $(".oc_attach_file:checked"), function( i, val ) {
		filename = $(this).val().split('~');
		id += filename[0] + ',';
		data += '<div style="float: left; width: 100%;"><input type="hidden" name="file_id[]" value="'+filename[0]+'"><span style="float: left;">'+filename[1]+'</span> <a id="'+filename[0]+'" class="del_oc_file"> </a></div>';
	});

	$('#oc_show_files').append(data);
	$.unblockUI();
	return false;
}

$(function() {
	$(document).delegate("a.del_oc_file","click",function() {
		var str_delete = $(this).attr("id");
		var result = confirm("Are you sure you want to delete this attachment?");
		if (result==true) {
			$('#'+str_delete).parent("div").remove();
		}
	});
	$("#show_files").delegate("a.del_file","click",function() {
		var str_delete = $(this).attr("id");
		var result = confirm("Are you sure you want to delete this attachment?");
		if (result==true) {
			$('#'+str_delete).parent("div").remove();
		}
	});
	
	$("#uploadFile").delegate("a.del_file","click",function() {
		var str_delete = $(this).attr("id");
		var result = confirm("Are you sure you want to delete this attachment?");
		if (result==true) {
			$('#'+str_delete).parent("div").remove();
		}
	});
	$(".file-tabs-close-project").click(function() {
		$.unblockUI();
		return false;
	});
});

/*
*Adding Payment terms
*/
$( document ).ajaxSuccess(function( event, xhr, settings ) {
	if(settings.target=="#output1") {
		$('.payment-profile-view:visible').slideUp(400);
		$('.payment-terms-mini-view1').html(xhr.responseText);
		$('#set-payment-terms')[0].reset();
		$('#show_files').empty();
		$('.payment-terms-mini-view1').css('display', 'block');
	}
});

$(function(){
	var options = { 
		target:      '#output1',   // target element(s) to be updated with server response 
		beforeSubmit: showRequest, // pre-submit callback 
		success:      ''  // post-submit callback 
	}; 
	$('#set-payment-terms').ajaxForm(options);
});

function showRequest()
{
	var date_entered = true;
	var errors = [];
	if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
		date_entered = false;
	}
	/* if ($('#sp_form_jobid').val() == 0) { 
		errors.push('Project Id missing!');
	} */
	if(($.trim($('#sp_date_1').val()) == '')) {
		errors.push('<p>Enter Payment Milestone Name.</p>');
	}
	if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
		errors.push('<p>Enter valid Date.</p>');
	}
	if(($.trim($('#sp_date_3').val()) == '')) {
		errors.push('<p>Enter Milestone Value.</p>');
	}
	if(($.trim($('#month_year').val()) == '')) {
		errors.push('<p>Enter Month & Year Value.</p>');
	}
	if (errors.length > 0) {
		//alert(errors.join('\n'));
		setTimeout('timerfadeout()', 8000);
		$('#rec_paymentfadeout').show();
		$('#rec_paymentfadeout').html(errors.join(''));
		return false;
	}
}
function download_files(job_id,f_name){
	window.location.href = site_base_url+'project/download_file/'+job_id+'/'+f_name;
}

function loadLogs(id) 
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	$.post( 
		site_base_url+'project/getLogs/'+id,params,
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('#load-log').html(data);
				logsDataTable();
			}
		}
	);
}

function viewOtherCost(project_id) 
{
	metrics_reload = true;
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'project/getOtherCostData/'+project_id,
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
			$('#other_cost_data').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// console.info(data);
			$('#other_cost_data').html(data);
		}
	});
}

function reset_paymentdata() 
{
	$("#uploadFile").empty();
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	$.post( 
		site_base_url+'project/agreedPaymentView/'+curr_job_id,params,
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('.payment-profile-view').html(data);
			}
		}
	);
}

function loadCustomer(id) 
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.post( 
		site_base_url+'project/getCustomers/'+id, params,
		function(data) {
			if (data.error) {
				alert(data.errormsg);
			} else {
				$('#load-customer').html(data);
			}
		}
	);
}

// Edit folder permissions start.
function editFolderPermissions(lead_id)
{
	var ht = $('#edit-folder-permissions').height();
	$('#edit-folder-permissions').text('Loading, please wait..');
	$.blockUI({
		message: $('#edit-folder-permissions'),
		css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht) /2 + 'px',left: ($(window).width() - 900) /2 + 'px',width: '900px',height: ht+'px'}
	});
	
	$.get(site_base_url+'project/get_folder_permissions_ui_for_a_project', {'lead_id':lead_id}, function(data)
	{
		$("#edit-folder-permissions").html(data);
		$.blockUI({
			message: $('#edit-folder-permissions'), 
			css: { border: '2px solid #999',color:'#333',padding:'8px',top:  ($(window).height() - ht)/2 + 'px',left: ($(window).width() - 900)/2 + 'px',width: '900px',height: ht+'px'} 
		});
		$( "#edit-folder-permissions" ).parent().addClass( "no-scroll" );
	});	
}
// Edit folder permission end.

//UPLOAD XML FILES FOR GANTT CHART 

// Setup form validation on the #register-form element
$(function() {
	// Setup form validation on the #register-form element
	$("#upload-form").validate({
		// Specify the validation rules
		rules: {
			xmlfile:{
                    required: true,
                    accept:"xml"
                }  
		},
		// Specify the validation error messages
		messages: {
			 xmlfile:{
                    required: "Choose file",
                    accept: "Only xml file type is allowed"
                } 
		},
		submitHandler: function(form) {
			if(confirm("Uploaded file will replace the data in chart. Are you sure you want to proceed?"))
			{
			var formData = new FormData($("#upload-form")[0]);
			var project_id=jQuery("#project_id").val();
			$.ajax({
				type:'POST',
				data:formData,
				url:site_base_url+'projects/upload/do_upload?project_id='+project_id,
				cache:false,
				dataType:'json',
				processData:false, // Don't process the files
				contentType:false,
				beforeSend: function() {
					jQuery("#upload_loading").show();
				},
				success:function(data){
					jQuery("#upload_loading").hide();
					if(data.result=='success')
					{
						jQuery("#success_msg").html('File Uploaded Successfully');
						 setTimeout(function(){// wait for 5 secs(2)
							   location.reload(true); // then reload the page.(3)
						  }, 3000); 
					}
					else if(data.result=='error')
					{
						jQuery("#success_msg").html('Please fill out all required fields');
					}
					else
					{
						jQuery("#success_msg").html('There was an error while uploading.Please try again..');
					}
				}
			});
			return false;
			}
		}
	});
});


/*for other cost inclusion*/
/*load the other cost grid*/
function loadOtherCostGrid(project_id) 
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	
	$.ajax({
		type:'POST',
		data:params,
		url:site_base_url+'project/getOtherCostData/'+project_id+'/'+true,
		cache:false,
		dataType:'html',
		beforeSend: function() {
			//show loading symbol
			$('#list_other_cost').html('<div style="margin:20px;" align="center">Loading Content.<br><img alt="wait" src="'+site_base_url+'assets/images/ajax_loader.gif"><br>Thank you for your patience!</div>');
		},
		success:function(data) {
			// console.info(data);
			$('#list_other_cost').html(data);
		}
	});
}

//deleting the other cost data
function deleteOtherCostData(costid, projectid)
{
	var agree=confirm("Are you sure you want to delete?");
	if (agree) {
		var params = {};
		params[csrf_token_name] = csrf_hash_token;
		params['costid'] 		= costid;
		params['projectid'] 	= projectid;
		$.ajax({
			type:'POST',
			data:params,
			url:site_base_url+'project/deleteOtherCostData/',
			cache:false,
			dataType:'json',
			beforeSend: function() {
				//show loading symbol
			},
			success:function(data) {
				if(data.res == 'success'){
					$('#succes_other_cost_data').html("<span class='ajx_success_msg'>Deleted Successfully.</span>");
					$('#cost_'+costid).remove();
					setTimeout('timerfadeout()', 4000);
				} else if(data.res == 'failure'){
					$('#succes_other_cost_data').html("<span class='ajx_failure_msg'>Error in deleting other cost.</span>");
				}
			}
		});
	} else {
		return false;
	}
}
/*for updating the actual cost inclusion*/
function updtActualProjectValue(projectid)
{
	var params = {};
	params[csrf_token_name] = csrf_hash_token;
	params['project_id'] 	= project_id;
	
	if(metrics_reload == true) {
		$.ajax({
			type:'POST',
			data:params,
			url:site_base_url+'project/getAcutalCostDataForProject/',
			cache:false,
			dataType:'json',
			beforeSend: function() {
				//show loading symbol or overlay
				$('.metrics_overlay').block({
					message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
					css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
				});
			},
			success:function(data) {
				$('.metrics_overlay').unblock();
				// $('.blockUI').css('display', 'none');
				$('#actualValue').val(data.project_cost);
				$('#varianceValue').val(data.varianceProjectVal);
			}
		});
	}
}

/* To get email template by id */
function getTemplate(temp_id)
{
	       params ={'temp_id':temp_id};
		   params[csrf_token_name] = csrf_hash_token;
			$.ajax({
			async: false,
			type: "POST",
			url : site_base_url + 'project/get_template_content/',
			cache : false,
			data :params,
			success : function(response){
				response = JSON.parse(response);
				
				if(response != null && response.temp_content !=null) {
					
					tinymce.get('job_log').setContent(response.temp_content);
					//tinymce.triggerSave();
               } else {
					tinymce.get('job_log').setContent('');
				}
			}
		});
}
/* To get email signature by id */
function getSignature(sign_id)
{
	       params ={'sign_id':sign_id};
		   params[csrf_token_name] = csrf_hash_token;
			$.ajax({
			async: false,
			type: "POST",
			url : site_base_url + 'project/get_signature_content/',
			cache : false,
			data :params,
			success : function(response){
				response = JSON.parse(response);
				
				if(response != null && response.sign_content !=null) {
					
					tinymce.get('signature').setContent(response.sign_content);
					//tinymce.triggerSave();
                } else {
					tinymce.get('signature').setContent('');
				}
			}
		});
}
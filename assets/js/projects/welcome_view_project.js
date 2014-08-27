/*
*@Welcome View Project
*@
*/
	$(document).ready(function() {
		
		var mySelect = $('#project_lead');
		previousValue = mySelect.val();
		var lead_assign = previousValue; 
		$("#previous-project-manager").val(lead_assign); 
		$('#project_lead').change( function() {
		});

		// $("#show-con").hide();
		// $("#show-btn").click(function(){
			// $("#show-con").slideToggle("slow"); 
			// return false;
		// });
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
	});

 ////////////////////////----------------------------X---------------------////////////////////////////
 
	var lead_services = [];
	lead_services['not_select'] = '';

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

		var the_log = $('#job_log').val();
		
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
		$('.user-addresses input[type="checkbox"]:checked').each(function(){
			email_set += $(this).attr('id') + ':';
		});


		$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});


		var form_data 			   = {'userid':userid, 'lead_id':quote_id, 'log_content':the_log, 'emailto':email_set}
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

	function setProjectPaymentTerms() 
	{
		$('#sp_form_jobid').val(curr_job_id);
		$(".payment-terms-mini-view1").css("display","block");
		$(".payment-received-mini-view1").css("display","none");	
		var valid_date = true;
		var date_entered = true;
		var errors = [];

		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
		var pdate2 = $.trim($('#sp_date_2').val());	

		if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
			date_entered = false;
		}

		if ($('#sp_form_jobid').val() == 0) { 
			errors.push('Invoice not properly loaded!');
		}
		if(($.trim($('#sp_date_1').val()) == '')) {
			errors.push('<p>Enter Payment Milestone Name.</p>');
		}
		if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
			errors.push('<p>Enter valid Date.</p>');
		}
		if (valid_date == false) {
			errors.push('<p>You have selected an invalid date.</p>');
		}
		if(($.trim($('#sp_date_3').val()) == '')) {
			errors.push('<p>Enter Milestone Value.</p>');
		}
		if (errors.length > 0) {
			//alert(errors.join('\n'));
			setTimeout('timerfadeout()', 8000);
			$('#rec_paymentfadeout').show();
			$('#rec_paymentfadeout').html(errors.join(''));
			return false;
		} else {
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			
			var form_data = $('#set-payment-terms').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+'project/set_payment_terms',
				form_data,
				function(data) {
					if (data.error) {
						alert(data.errormsg);
					} else {
						$('.payment-profile-view:visible').slideUp(400);
						$('.payment-terms-mini-view1').html(data);
					}
					$.unblockUI();
					$('#set-payment-terms')[0].reset();
				}
			);
		}
		
		$('.payment-terms-mini-view1').css('display', 'block');
	}

	//Update functionality for set payment terms Starts here.
	function updateProjectPaymentTerms(eid) 
	{
		$('#rec_paymentfadeout').hide();
		$('#sp_form_jobid').val(curr_job_id);
		$(".payment-terms-mini-view1").css("display","block");
		$(".payment-received-mini-view1").css("display","none");
		var valid_date = true;
		var date_entered = true;
		var errors = [];

		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();

		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'-'+mm+'-'+yyyy;
		var pdate2 = $.trim($('#sp_date_2').val());	

		if ( ($.trim($('#sp_date_1').val()) == '') && ($.trim($('#sp_date_2').val()) == '') && ($.trim($('#sp_date_3').val()) == '') ) {
			date_entered = false;
		}

		if ($('#sp_form_jobid').val() == 0) { 
			errors.push('Invoice not properly loaded!');
		}
		if(($.trim($('#sp_date_1').val()) == '')) {
			errors.push('<p>Enter Payment Milestone Name.</p>');
		}
		if(($.trim($('#sp_date_2').val()) == ''))  { //|| valid_date == false) {
			errors.push('<p>Enter valid Date.</p>');
		}
		if (valid_date == false) {
			errors.push('<p>You have selected an invalid date</p>');
		}
		if(($.trim($('#sp_date_3').val()) == '')) {
			errors.push('<p>Enter Milestone Value.</p>');
		}
		if (errors.length > 0) {
			//alert(errors.join('\n'));
			setTimeout('timerfadeout()', 8000);
			$('#rec_paymentfadeout').show();
			$('#rec_paymentfadeout').html(errors.join(''));
			return false;
			
		} else {
			
			$.blockUI({
				message:'<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
				css: {background:'#666', border: '2px solid #999', padding:'4px', height:'35px', color:'#333'}
			});
			var form_data = $('#update-payment-terms').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
			$.post( 
				site_base_url+'project/set_payment_terms/'+eid,
				form_data,
				function(data) {
						if (data.error) {
							alert(data.errormsg);
						} else {
							$('.payment-profile-view:visible').slideUp(400);
							$('.payment-terms-mini-view1').html(data);
						}
					$.unblockUI();
					$('#update-payment-terms').remove();
					paymentProfileView();
				}
			);
			
		}
		$('.payment-terms-mini-view1').css('display', 'block');
	}

	function fullScreenLogs() 
	{
		var fsl_height = parseInt($(window).height()) - 80;
		fsl_height = fsl_height + 'px';
		$.blockUI({
			message:$('.comments-log-container'),
			css: {background:'#fff', border: '1px solid #999', padding:'4px', height:fsl_height, color:'#000000', width:'600px', overflow:'auto', top:'40px', left:'50%', marginLeft:'-300px'},
			overlayCSS:  {backgroundColor:'#fff', opacity:0.9}
		});
		$('.blockUI:not(.blockMsg)').append('<p onclick="$.unblockUI();$(this).remove();" id="fsl-close">CLOSE</p>');
	}

	function runAjaxFileUpload() 
	{
		var _uid = new Date().getTime();
		$('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
		var params 				= {};
		params[csrf_token_name] = csrf_hash_token;
		
		$.ajaxFileUpload
		(
			{
				url:'ajax/request/file_upload/'+project_jobid,
				secureuri:false,
				fileElementId:'ajax_file_uploader',
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
							}
							else
							{
								alert('File upload failed!');
							}
							$('#'+_uid).hide('slow').remove();
						}
						else
						{	
							if(data.msg == 'File successfully uploaded!') {
								var lead_details = "project/lead_fileupload_details/"+project_jobid+"/"+data.file_name+ "/" +userid;														
								$('#lead_result').load(lead_details);
							}
							//alert(data.msg);
							var _file_link = '<a href="crm_data/'+project_jobid+'/'+data.file_name+'" onclick="window.open(this.href); return false;">'+data.file_name+'</a> <span>'+data.file_size+'</span>';
							var _del_link = '<a href="#" onclick="ajaxDeleteFile(\'/crm_data/'+project_jobid+'/'+data.file_name+'\', this); return false;" class="file-delete">delete file</a>';
							$('#'+_uid).html(_del_link + _file_link);
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
			}
		);
		$('#ajax_file_uploader').val('');
		return false;
	}

	function ajaxDeleteFile(path, el) 
	{
		if (window.confirm('Are you sure you want to delete this file?')) 
		{
			path = js_urlencode(path);
			$(el).parent().hide('slow');
			
			var params 				= {file_path : path};
			params[csrf_token_name] = csrf_hash_token;
			
			$.post(
				site_base_url+'ajax/request/file_delete/',
				params,
				function(data) {
					try {
						var _data;
						eval('_data = ' + data);
						if (!_data.error) {
							$(el).remove();
						} else {
							$(el).parent().show('slow');
							alert('File could not be deleted!');
						}
					} catch (e) {
						alert(e);
						$(el).parent().show('slow');
						alert('File could not be deleted!');
					}
				}
			);
		}
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
	
	//Set the Project Practices//
	function setPractices() {
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
		setTimeout('timerfadeout()', 2000);
	}

	//update the project status.
	function setProjectStatus() {
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
					// $('#resmsg').html("<span class='ajx_success_msg'>Status Updated.</span>");
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
		var msg = 'This is your signature!\nThis will be attached to any log that you email through.\nGo to "My Account" page to set your signature.';
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
		$('#payment-recieved-terms .pick-date').datepicker({dateFormat: 'dd-mm-yy', maxDate: '0'});
		$('.milestone_date .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -30, maxDate: '+1M' });
		$('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({
			dateFormat: 'dd-mm-yy', 
			minDate: '0',
			beforeShow : function(input, inst) {
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
				if (ui.newPanel[0].id=='jv-tab-4')
					loadExistingTasks();
				if (ui.newPanel[0].id=='jv-tab-4-5') {
					// populateJobOverview();
					$('.payment-received-mini-view1').hide();
				}
			}
		});

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

		
		$(document).ready(function() {
			if (project_complete_status!='') {
				updateVisualStatus(project_complete_status);
			}
		});
		
		$('.jump-to-job select').change(function(){
			var _new_location = proj_location;
			document.location = _new_location.replace('{{lead_id}}', $(this).val());
		});


		$('#job_log').siblings().hide();

		$('#job_log').focus(function(){
			$(this).siblings(':hidden').not('#multiple-client-emails').slideDown('fast');
			if ($(this).val() == 'Click to view options') {
				$(this).val('');
				$(this).removeClass('gray-text');
			}
		});


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
	
	/* var milestones_cached_row = false;
	function addMilestoneField()
	{
		if ( ! milestones_cached_row)
		{
			milestones_cached_row = $('#milestone-clone tr:first');
		}
		
		milestones_cached_row.clone().appendTo('#milestone-data tbody');
		$('#milestone-data tr:last .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: '-6M', maxDate: '+24M'});
	} */
	
	/* function removeMilestoneRow(el)
	{
		var agree=confirm("Are you sure you want to delete this milestone?");
			if (agree) {
				$(el).parent().parent().remove();
			}
			var data = $('#milestone-management').serialize()+'&'+csrf_token_name+'='+csrf_hash_token;
		
		$('#jv-tab-4-5').block({
							message:'<img src="assets/img/ajax-loader.gif" />',
							css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
						});
		
		$.post(
			'ajax/request/save_job_overview/' + curr_job_id,
			data,
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
		return false;
	} */
	
	/* 
	function saveMilestones()
	{
		var error = false;
		
		$('#milestone-data tbody tr').each(function(){
			if ($('.milestone input', $(this)).val() == '' || $('.milestone-date input', $(this)).val() == '')
			{
				error = 'All milestones and dates are required!';
			}
		});
		
		if (error)
		{
			alert(error);
			return;
		}
		
		var data = $('#milestone-management').serialize()+'&csrf_token_name'+'='+csrf_hash_token;
		
		$('#jv-tab-4-5').block({
							message:'<img src="assets/img/ajax-loader.gif" />',
							css: {background:'transparent', border: 'none', padding:'4px', height:'12px', color:'#333', top:'4px'}
						});
		
		$.post(
			'ajax/request/save_job_overview/' + curr_job_id,
			data,
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
	} */
	
	/* function emailMilestones()
	{
		var qc_job_title = project_job_title;
		var obj = $('#milestone-data tbody tr');
		
		if (obj.length == 0)
		{
			alert('No records are there to email!');
			return false;
		}
		var email_data = '';
		obj.each(function(){
			var ddate = $('.milestone-date input', $(this)).val();
			var mstone = $('.milestone input', $(this)).val();
			var mstat = $('.milestone-status select option:selected', $(this)).val();
			
			email_data += ddate + ' : ' + mstone;
			if (mstat == 0)
			{
				email_data += ' [Scheduled]';
			}
			if (mstat == 1)
			{
				email_data += ' [In Progress]';
			}
			if (mstat == 2)
			{
				email_data += ' [completed]';
			}
			email_data += '\n';
		});
		
		$('#job_log').focus().val('\nTimeline for the project: ' + qc_job_title + '\n' +  email_data);
		$('html, body').animate({ scrollTop: $('#job_log').offset().top }, 500);
		
		return false;
	} */
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
			var url = site_base_url+"project/agreedPaymentView";
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
		{
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
			$('#resmsg_practice').fadeOut();
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

		//mychanges
		function isNumberKey(evt) {
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
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
				$("#msg_project_efforts").text('Please enter project estimate hour');
				$("#msg_project_efforts").show();
				return false;
			}else {
				if(filterFloat(hour_val) == false){
					$("#msg_project_efforts").text('Please enter valid estimate hour');
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
					alert(data.errormsg);
				} else {
					loadPayment();
				}
				$.unblockUI();
			}
			,'json'
		);
	} else {
		return false;
	}
	
}
function raise_inv() {
	alert('raiseInvoice');
	return false;
}
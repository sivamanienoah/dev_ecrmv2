/*
 *@Asset Add View Jquery
*/

	var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
	nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';
	$(function() {
            
		$( "#project_name" ).autocomplete({
                    
			minLength: 2, 
			source: function(request, response) {
			
				var params = {'project_name': $("#project_name").val()};
                               console.log(params);
				params[csrf_token_name] = csrf_hash_token;
				
				$.ajax({ 
					url: "asset_register/ajax_project_search",
					data: params,
					type: "POST",
					dataType: 'json',
					async: false,
					success: function(data) {
						response( data );
					}
				});
			},
			select: function(event, ui) {
				$('#cust_id').val(ui.item.id);
			} 
		});
	});

	$(document).ready(function() {

			$('input.pick-date').datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
			
			$('input[name="domain_mgmt"]').change(function(){
					if ($('input[name="domain_mgmt"]:checked').val() == 'ENOAH') {
							$('#domain-expiry-date:hidden').show();
					} else {
							$('#domain-expiry-date:visible').hide();
					}
			});
			
			if ($('input[name="domain_mgmt"]:checked').val() == 'ENOAH') {
					$('#domain-expiry-date:hidden').show();
			} else {
					$('#domain-expiry-date:visible').hide();
			}
			
			$('.modal-new-cust').click(function(){
					$.blockUI({message: nc_form_msg, css: {width: '690px', marginLeft: '50%', left: '-345px', position: 'absolute', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},overlayCSS: {backgroundColor:'#EAEAEA', opacity: '0.9', cursor: 'wait'}});
					$.get(
						'ajax/data_forms/new_customer_form',
						{},
						function(data){
							$('.new-cust-form-loader').slideUp(500, function(){
								$(this).parent().css({backgroundColor: '#fff', color: '#333'});
								$(this).css('text-align', 'left').html(data).slideDown(500, function(){
									$('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
								});
							})
						}
					);
				return false;
			});
		}
	);

	///////-----------------------------------X---------------------------------------X---------------

	function ndf_cancel() 
	{
		$.unblockUI();
		return false;
	}

	function ndf_add() {
	
		$('.new-cust-form-loader .error-handle:visible').slideUp(300);
		var form_data = $('#customer_detail_form').serialize();
		
		$('.blockUI .layout').block({
			message:'<h3>Processing</h3>',
			css: {background:'#666', border: '2px solid #999', padding:'8px', color:'#333'}
		});
		
		$.post(
			'customers/add_customer/false/false/true',
			form_data,
			function(res) {
				if (typeof (res) == 'object') {
					if (res.error == false) {
						ex_cust_id = res.custid;
						// $("#ex-cust-name").val(res.cust_name1);
						$("#cust_name").val(res.cust_name1);
						$("#cust_id").val(res.custid);
						$.unblockUI();	
						$('.notice').slideUp(400);
						showMSG('<div id=confirm>New Customer Added!</div>');
					} else {
						$('.blockUI .layout').unblock();
						$('.error-cont').html(res.ajax_error_str).slideDown(400);
						
					}
				} else {
					$('.error-cont').html('<p class="form-error">Your session timed out!</p>').slideDown(400);
				}
			},
			"json"
		)
		return false;
	}

	/*
	 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Starts Here
	 */
	function ajxCty()
	{
		$("#addcountry").slideToggle("slow");
	}

	function ajxSaveCty()
	{
		$(document).ready(function() {
			if ($('#newcountry').val() == "") {
				alert("Country Required.");
			}
			else {
				var regionId = $("#add1_region").val();
				var newCty = $('#newcountry').val();
				getCty(newCty, regionId);
			}
			
		function getCty(newCty, regionId){
				var baseurl = $('.hiddenUrl').val();
				var params  = {regionid: $("#add1_region").val(),country_name:$("#newcountry").val(),created_by:hosting_userid}; // hosting_userid is hosting page variable 
				params[csrf_token_name] = csrf_hash_token;
				
				$.ajax({
				url : baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
				cache : false,
				success : function(response){
					if(response == 'userOk') 
						{ 
							$.post("regionsettings/country_add_ajax",params, 
							function(info){$("#country_row").html(info);});
							$("#addcountry").hide();
							$("#state_row").load("regionsettings/getState");
						}
					else
						{ 
							alert('Country Exists.'); 
						}
				}
			});
		}
		});	
	}

	function ajxSt() 
	{
		$("#addstate").slideToggle("slow");
	}

	function ajxSaveSt() 
	{
		$(document).ready(function() {
			if ($('#newstate').val() == "") {
				alert("State Required.");
			}
			else {
				var cntyId = $("#add1_country").val()
				var newSte = $('#newstate').val();
				getSte(newSte,cntyId);

			}
			
		function getSte(newSte,cntyId) {
				var baseurl = $('.hiddenUrl').val();
				var params  = {countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:hosting_userid}
				params[csrf_token_name] = csrf_hash_token;
				$.ajax({
				url : baseurl + 'customers/getSteRes/' + newSte + "/" + cntyId,
				cache : false,
				success : function(response){
					if(response == 'userOk') 
						{
							$.post("regionsettings/state_add_ajax",params, 
							function(info){ $("#state_row").html(info); });
							$("#addstate").hide();

							$("#location_row").load("regionsettings/getLocation");
						}
					else
						{ 
							alert('State Exists.');
						}
				}
			});
		}
		});	
	}

	function ajxLoc()
	{
		$("#addLocation").slideToggle("slow");
	}

	function ajxSaveLoc() 
	{
		$(document).ready(function() {
			if ($('#newlocation').val() == "") {
				alert("Location Required.");
			}
			else {
				var stId = $("#add1_state").val();
				var newLoc = $('#newlocation').val();
				getLoc(newLoc,stId);
			}
		function getLoc(newLoc, stId) {
				var baseurl = $('.hiddenUrl').val();
				var params  = {stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:hosting_userid};
				params[csrf_token_name] = csrf_hash_token;
				$.ajax({
				url : baseurl + 'customers/getLocRes/' + newLoc + '/' +stId,
				cache : false,
				success : function(response){
					if(response == 'userOk') 
						{
							$.post("regionsettings/location_add_ajax",params, 
							function(info){ $("#location_row").html(info); });
							$("#addstate").hide();
							//var steId = $("#add1_state").val();
							//$("#location_row").load("regionsettings/getLocation/" +steId);
						}
					else
						{ 
							alert('Location Exists.');
						}
				}
			});
		}
		});	
	}
//Multiple customer add
//for document format form
$('#document_tbl').delegate( '#addRow', 'click', function () {
	var thisRow = $(this).closest('tr');
	$(this).hide();
	$("#document_tbl tbody tr").find('.del_file').show();	
	var obj = $(thisRow).clone().insertAfter(thisRow);
		obj.find(".contact_id").val("");
		obj.find(".first_name").val("");
		obj.find(".last_name").val("");
		obj.find(".position_title").val("");
		obj.find(".phone").val("");
		obj.find(".email").val("");
		obj.find(".skype").val("");
		obj.find(".hyperfields").css('border','');
		obj.find(".first_name_err_msg").text('');
		obj.find(".last_name_err_msg").text('');
		obj.find(".position_title_err_msg").text('');
		obj.find(".phone_err_msg").text('');
		obj.find(".email_err_msg").text('');
		obj.find(".skype_err_msg").text('');
		obj.find("#deleteRow").attr('hyperid','0');
		obj.find('.createBtn').show();
});

$('#document_tbl').delegate( '.del_file', 'click', function () {
	var thisRow = $(this).parent('td').parent('tr');
	if( $(this).attr('hyperid') !=0 ) {
		var hyperid = $(this).attr('hyperid');
		var x = confirm("Are you Sure want to remove?");
		if(x==true)
		{
			/* $.post(site_base_url+'customers/delete_contact?id='+hyperid,function( data ) {
				$(thisRow).remove();
				if($('#document_tbl tbody tr').length<=1){
					$('#document_tbl .del_file').hide();
					$('#document_tbl .createBtn').show();
				}
			}); */
			var formdata = { 'id':hyperid }
			formdata[csrf_token_name] = csrf_hash_token;
			$.ajax({
				async: false,
				type: "POST",
				url: site_base_url+'customers/delete_contact/',
				dataType:"json",                                                                
				data: formdata,
				cache: false,
				beforeSend:function() {
				},
				success: function(response) {
					if (response.html == 'NO') {
						alert('One or more Leads currently mapped to this customer. This cannot be deleted.');
					} else {
						$(thisRow).remove();
						$("#document_tbl tbody tr:last").find('.createBtn').show();
						// $("#document_tbl tbody tr:last").find('.del_file').hide();
						if($('#document_tbl tbody tr').length<=1){
							$('#document_tbl .del_file').hide();
							$('#document_tbl .createBtn').show();
						}
						alert('Contact Deleted.');
					}
				}          
			});
		}
	} else {
		$(thisRow).remove();
		
		if($('#document_tbl tbody tr').length<=1){
			$('#document_tbl .del_file').hide();
			$('#document_tbl .createBtn').show();
		}
	}
	$("#document_tbl tbody tr").each(function(){
		$("#document_tbl tbody tr:last").find('.createBtn').show();
	})
});


$("#document_tbl tbody tr").each(function(){
	$("#document_tbl tbody tr:last").find('.createBtn').show();
});
if($('#document_tbl tbody tr').length<=1){
	$('#document_tbl .del_file').hide();
	$('#document_tbl .createBtn').show();
}

/////////////////
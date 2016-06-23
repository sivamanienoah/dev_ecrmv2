<?php require (theme_url().'/tpl/header.php'); ?>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<input type="hidden" class="hiddenUrl"/>
<script type="text/javascript">
<?php
$userdata = $this->session->userdata('logged_in_user');
$usernme = $this->session->userdata('logged_in_user');
?>
var curr_job_id = <?php echo isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0 ?>;
var lead_services = [];
lead_services['not_select'] = '';

<?php foreach ($job_cate as $job) { ?>
lead_services[<?php echo $job["sid"] ?>] = '<?php echo $job["services"] ?>';
<?php } ?>

var hourly_rate = '';
var quote_id = <?php echo isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0 ?>;
var ex_cust_id = 0;
var item_sort_order = '';
var regId = '';
var cntryId = '';
var stId = '';
var locId = '';

// converting a lead to a quote 
var existing_lead = 0;
var existing_lead_service;

var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

function disableEnquiry()
{
     $(".convert_to_lead").slideUp();
}

$(document).ready(function(){
	$("#convert_to_lead").click(function()
	{
	  $(".convert_to_lead").slideToggle();
	});
	
		$( "#ex-cust-name" ).autocomplete({
			minLength: 2,
			source: function(request, response) {
				$.ajax({ 
					url: "hosting/ajax_customer_search",
					data: { 'cust_name': $("#ex-cust-name").val(),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
					type: "POST",
					dataType: 'json',
					async: false,
					success: function(data) {
						response( data );
					}
				});
			},
			select: function(event, ui) {
				// $('#cust_id').val(ui.item.id);
				ex_cust_id = ui.item.id;
				regId = ui.item.regId;
				cntryId = ui.item.cntryId;
				stId = ui.item.stId;
				locId = ui.item.locId;
				prepareQuoteForClient(ex_cust_id);
				getUserForLeadAssign(regId,cntryId,stId,locId);
			} 
		});
		
		<?php
		if (isset($existing_lead) && isset($lead_customer))
		{
			echo 'ex_cust_id = ', $lead_customer, ";\n";
			echo 'existing_lead = ', $existing_lead, ";\n";
			echo 'existing_lead_service = "', $existing_lead_service, '";', "\n";
			echo "prepareQuoteForClient(ex_cust_id);\n";
		}
		?>
        
      $('.modal-new-cust').click(function(){
           $.blockUI({
                        message:nc_form_msg,
                        css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default', position: 'absolute'},
                        overlayCSS: {backgroundColor:'#EAEAEA', opacity: '0.9', cursor: 'wait'}
                    });
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
	        
        $('.q-item-details div').css('display', 'none')
                                .siblings('a:first').next().show().end()
                                .parent().children('a').click(function() {
                                    $(this).next().slideToggle(500);
                                    return false;
                                });
        
        <?php
		/*
		 * This is applicable when not viewing a quote
		 * on edit or create mode
		 */
		if (!isset($view_quotation))
		{
			?>
        
        $('#q-sort-items').sortable({axis:'y', cursor:'move', update:prepareSortedItems});
        
		$('#q-sort-items li').livequery(function(){ 
		// use the helper function hover to bind a mouseover and mouseout event 
			$(this) 
				.hover(function() { 
					quoteItemOver($(this)); 
				}, function() { 
					quoteItemOut($(this)); 
				}); 
		}, function() { 
			// unbind the mouseover and mouseout events 
			$(this) 
				.unbind('mouseover') 
				.unbind('mouseout'); 
		});
        
        $('#q-sort-items li .ip-delete').livequery(function(){
            $(this).click(function(){
                quoteItemDelete($(this));
            });
        });
        $('#q-sort-items li .ip-edit').livequery(function(){
            $(this).click(function(){
                quoteItemEdit($(this));
            });
        });
        
        <?php
		} // end edit mode
		?>
        <?php if (isset($quote_data) && (isset($edit_quotation) || isset($view_quotation))) { ?>
        
        populateQuote(<?php echo $quote_data['lead_id'] ?>);
        
        <?php } ?>
        <?php if (isset($edit_quotation)) { ?>
        $('#item-submit').css('display', 'block');
        <?php } ?>
	}
);

function prepareQuoteForClient(custID) {
	$('.notice').slideUp(400);
	$.getJSON(
		'welcome/ajax_customer_details/' + custID,
		{},
		function (details) {
			$('.q-cust-company span').html(details.company);
			$('.q-cust-name span').html(details.customer_name);
			$('.q-cust-email span').html(details.email_1);
			if (existing_lead > 0) {
				$('#ex-cust-name').val(details.customer_name);
			}
		}
	);
	if (existing_lead > 0) {
		if (existing_lead_service) {
			$('#job_belong_to').val(existing_lead_service);
		}
		$('#job_belong_to').parent().after('<p class="notice width250px">This is an existing lead. Upon starting, the logs belong to this lead will be transferred to this job.</p>');
		$('#quote-init-form').append('<input type="hidden" name="transfer_lead" value="' + existing_lead + '" />')
	}
}

function getUserForLeadAssign(regId,cntryId,stId,locId) {

	$('.notice').slideUp(400);
	$.getJSON(
		'welcome/user_level_details/' + regId + '/' + cntryId + '/' + stId + '/' + locId,
		{},
		function (userdetails) 
		{
			get_user_infm(userdetails);
		}
	);
}

function get_user_infm(users) {
	var baseurl = $('.hiddenUrl').val();
	var user = users.toString();
	$.ajax({
		type: "POST",
		url : baseurl + 'user/getUserDetFromDb/',
		cache : false,
		data: { user: user,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' },
		success : function(response){
		//alert(response);
			if(response != '') {
				$("#lead_assign").html(response);
			}
		}
	});
}

function ndf_cancel() {
    $.unblockUI();
    return false;
}

function ndf_add() {
    $('.new-cust-form-loader .error-handle:visible').slideUp(300);
    var form_data = $('#customer_detail_form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
	
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
                    $("#ex-cust-name").val(res.cust_name1);
                    $.unblockUI();	
                    $('.notice').slideUp(400);
                    showMSG('<div id=confirm>New Customer Added!</div>');
                    $('.q-cust-name span').html(res.cust_name);
                    $('.q-cust-email span').html(res.cust_email);
					$('.q-cust-company span').html(res.cust_company);
					getUserForLeadAssign(res.cust_reg,res.cust_cntry,res.cust_ste,res.cust_locn);
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

function setPrice(num) {
    num = $.trim(num);
    if (num != '') {
        if (isNaN(num)) {
            alert('Hours must be a number');
            $('#hours').val('').focus();
            return false;
        } else {
            //var n = Math.round((hourly_rate * parseFloat(num)) * 100) / 100;
            //$('#item_price').val(n);
        }
    }   
}


function startQuote() {
    var err = [];
    if (ex_cust_id == 0) {
        err.push('A valid customer needs to be selected');
    }
    if ($.trim($('#lead_title').val()) == '') {
        err.push('Job title is required');
    }
    if ($('#lead_service').val() == 'not_select') {
        err.push('Service type must be selected');
    }
	 if ($('#lead_source').val() == 'not_select') {
        err.push('Lead Source type must be selected');
    }
	 if ($('#lead_assign').val() == 'not_select') {
        err.push('Lead Assigned to type must be selected');
    }
	 if ($('#job_division').val() == 'not_select') {
        err.push('Job Division type must be selected');
    }
	 if ($('#lead_indicator').val() == 'not_select') {
        err.push('Lead Indicator type must be selected');
    }
	if ($('#expect_worth').val() == 'not_select') {
        err.push('Expected Worth Amount Curreny type must be selected');
    }
	if ($.trim($('#expect_worth_amount').val()) == '') {
        err.push('Expected Worth Amount is required');
    }
	if ($.trim($('#proposal_expected_date').val()) == '') {
        err.push('Proposal Expected Date is required');
    }
    if (err.length > 0) {
        // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
		$.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">'+err.join('<br />')+'</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});
        return false;
    } else {
        // block
        $('#content').block({
            message:'<h4>Processing...</h4>'
        });
        // add id to form
        $('#hidden_custid_fk').val(ex_cust_id);
        // get form data
        var form_data = $('#quote-init-form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
		
        $.post('enquiries/ajax_enquiry_to_lead',form_data,function (res) {
			if (typeof (res) == 'object') {
				if (res.error == false) {
					// good to go
					$('.q-id span').html(res.fancy_insert_id);
					quote_id = res.insert_id;
					$('#quote-init-form').slideUp();
					$('#content').block({
						message:'<h4>Processing...</h4>'
					});
					
					$('#item-submit, #change-quote-status').slideDown();
					populateQuote(res.insert_id);
					window.location.href = "<?php echo $this->config->item('base_url') ?>"+"welcome/view_quote/"+res.insert_id;
					$('#item-submit').append('<br /><br /><div class="action-buttons" style="clear:left; overflow:hidden; margin-top:20px;">' +
												'<div class="buttons clearfix">' +
												'<button type="button" class="positive" onclick="document.location.href = \'<?php echo $this->config->item('base_url') ?>welcome/view_quote/' + res.insert_id + '\'; return false;">View Lead</button>' +
												'</div>' +
											'</div>');
				} else {
				   // alert(res.errormsg);
				}
			} else {
				alert('Your session timed out!');
			}
			$('#content').unblock();
		},
		"json"
        );
    }
}

function cancelDel() {
    $.unblockUI();
}

function addItem() {
    if ($.trim($('#item_desc').val()) == '') {
        alert('You must provide a description!');
        return false;
    } else if ($('#item_price').val().match(/^[0-9]+(\.[0-9]{1,2})?$/) == false) {
        alert('Price field must be a valid numeric!');
        return false;
    } else {
        // block
        $('#content').block({
            message:'<h4>Processing</h4>'
        });
        // add lead_id to form
        $('#hidden_jobid').val(quote_id);
        // get form data
        var form_data = $('#item-submit').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        $.post(
            'welcome/ajax_add_item',
            form_data,
            function (res) {
                if (typeof (res) == 'object') {
                    if (res.error == false) {
                        // good to go
                        $('#q-sort-items').empty().append(res.html);
                        $('#sale_amount').html(res.sale_amount);
                        $('#gst_amount').html(res.gst_amount);
                        $('#total_inc_gst').html(res.total_inc_gst);
                        $('#q-sort-items').sortable('refresh');
                        scrollElem('.q-container', '#q-sort-items li#qi-'+res.itemid);
                        $('#item_title, #item_price, #item_desc, #hours').val('');
                    } else {
                        alert(res.errormsg);
                    }
                } else {
                    alert('Your session timedout!');
                }
                $('#content').unblock();
            },
            "json"
        );
    }
}


function prepareSortedItems() {
	var item_sort_order = $('#q-sort-items').sortable('serialize')+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
	
	$('.q-main-right').block({message:'<h5>Processing</h5>'});

    $.post(
        'welcome/ajax_save_item_order',
        item_sort_order,
        function(data) {
			$('.q-main-right').unblock();
            if (data.error) {
                alert(data.errormsg);
            } else {
                //$('.q-save-order').slideUp();
            }
        },
        'json'
    );
}


function quoteItemOver (obj) {
    obj.append('<a class="ip-edit">edit</a><a class="ip-delete">delete</a>');
}

function quoteItemOut (obj) {
    $('.ip-edit, .ip-delete', obj).remove();
}

var qe_item = '<div class="q-modal-item-edit">Loading Content.<br />';
qe_item += '<img src="assets/img/indicator.gif" alt="wait" /></div>';

function quoteItemEdit(obj) {
    var itemid = obj.parent().attr('id').replace(/^qi\-/, '');
    $.blockUI({
        message:qe_item,
        css: {width: '500px', marginLeft: '50%', left: '-250px', padding: '20px 0 20px 20px', top: '25%', border: 'none', cursor: 'default'},
        overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
    });
    $.get(
        'ajax/data_forms/quote_item_form/'+itemid,
        {},
        function(data){
            $('.q-modal-item-edit').slideUp(500, function(){
                $(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
                $(this).css('text-align', 'left').html(data).slideDown(500, function(){
                    $('.error-cont').css({margin:'10px 25px 10px 0', padding:'10px 10px 0 10px', backgroundColor:'#CEB1B0'});
					$('#quote_item_edit_form textarea').keyup();
                });
            })
        }
    );
    return false;
}

function processItemEdit() {
    $('.q-modal-item-edit').parent().block({message:'<p>Processing...</p>'});
	var form_data = $('#quote_item_edit_form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
    $.post(
        'welcome/ajax_edit_item',
        form_data,
        function(data){
            if (typeof(data) == 'object') {
                if (!data.error) {
                    $('.q-modal-item-edit').unblock();
                    populateQuote(quote_id);
                    cancelDelEdit();
                } else if(data.error='undefined'){
					$('.q-modal-item-edit').unblock();
                    populateQuote(quote_id);
                    cancelDelEdit();
				} else {
                    $('.q-modal-item-edit').unblock();
                    alert(data.errormsg);
                    $('.q-modal-item-edit').parent().unblock();
                }
            } else {
                alert('Database error!');
                cancelDelEdit();
            }
            
        },
        'json'
    );
}

function quoteItemDelete(obj) {
    var itemid = obj.parent().attr('id').replace(/^qi\-/, '');
    $.blockUI({
        message:'<br /><h5>Are you sure?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processItemDelete('+itemid+'); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDelEdit(); return false;">No</button></div></div>',
		css:{width:'440px'}
    });
}

function processItemDelete(itemid) {
    var params = {'itemid':itemid,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'};
    $.post(
        'welcome/ajax_delete_item',
        params,
        function(res){
            if (typeof (res) == 'object') {
                if (res.error) {
                    alert(res.errormsg);
                } else {
                    $('#q-sort-items').empty().append(res.html);
                    $('#sale_amount').html(res.sale_amount);
                    $('#gst_amount').html(res.gst_amount);
                    $('#total_inc_gst').html(res.total_inc_gst);
                    $('#q-sort-items').sortable('refresh');
                }
            } else {
                alert('Your session timedout!');
            }
            cancelDelEdit();
        },
        'json'
    );
}

function cancelDelEdit() {
    $.unblockUI();
}

function editQuoteDetails() {
    var err = [];
    if ($.trim($('#job_title_edit').val()) == '') {
        err.push('Job title is required');
    }
    if ($('#job_category_edit').val() == 'not_select') {
        err.push('Service type must be selected');
    }
	 if ($('#lead_source_edit').val() == 'not_select') {
        err.push('Lead Source type must be selected');
    }
	 if ($('#lead_assign_edit').val() == 'not_select') {
        err.push('Lead Assigned to type must be selected');
    }
	if ($('#job_division_edit').val() == 'not_select') {
        err.push('Division type must be selected');
    }

	if ($('#expect_worth_edit').val() == 'not_select') {
        err.push('Expected worth amount Currency type must be selected');
    }

	var act_worth = $.trim($('#actual_worth').val());

	if ((act_worth == 0.00) || (act_worth == '') || (act_worth == 0.0)) {
		act_worth = 0;
	}

	if ( ($.trim($('#lead_status').val()) == 4) && (act_worth <= 0) ) {
		err.push('Actual worth amount must not be empty or greater than zero');
    }

    if (err.length > 0) {
        // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
		
		$.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">'+err.join('<br />')+'</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});

        return false;
    } else {
        // block
        $('#content').block({
            message:'<h4>Processing</h4>'
        });
        // get form data
        var form_data = $('#quote-edit-form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        // add cutomer id
		var csrf_token = '<?php echo $this->security->get_csrf_token_name() ?>';
		var csrf_hasf = '<?php echo $this->security->get_csrf_hash() ?>';
        $.post(
            'welcome/ajax_edit_quote',
            form_data,
            function (res) {
				if (typeof (res) == 'object') {				
                    if (res.error == false) {
						showMSGS('Details Successfully Updated!', csrf_token, csrf_hasf);
                        // good to go
                        $('.q-title').html(res.lead_title);
                        $('.q-service-type span').html(lead_services[res.lead_service]);
                        
                    } else {
                        alert(res.errormsg);
                    }
                } else {
                    alert('Your session timedout!');
                }
				
            },
            "json"
        );
		
    }
}


/*
 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Starts Here
 */
 function ajxCty(){
	$("#addcountry").slideToggle("slow");
}

function ajxSaveCty(){
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
            $.ajax({
            url : baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{ 
						$.post("regionsettings/country_add_ajax",{regionid: $("#add1_region").val(),country_name:$("#newcountry").val(),created_by:(<?php echo $usernme['userid']?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
						function(info){$("#country_row").html(info);});
						$("#addcountry").hide();

						//var regId = $("#add1_region").val();
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

function ajxSt() {
	$("#addstate").slideToggle("slow");
}

function ajxSaveSt() {
	$(document).ready(function() {
        /*if( $('#newstate').val().length > 2 )
            {
              var newSte = $('#newstate').val();
              getSte(newSte);
            }
        return false;
		*/
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
            $.ajax({
            url : baseurl + 'customers/getSteRes/' + newSte + "/" + cntyId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/state_add_ajax",{countryid: $("#add1_country").val(),state_name:$("#newstate").val(),created_by:(<?php echo $usernme['userid'] ?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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

function ajxLoc() {
	$("#addLocation").slideToggle("slow");
}

function ajxSaveLoc() {
	$(document).ready(function() {
        /*if ($('#newlocation').val().length > 2)
            {
              var newLoc = $('#newlocation').val();
              getLoc(newLoc);
            }
        return false;
		*/
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
            $.ajax({
            url : baseurl + 'customers/getLocRes/' + newLoc + '/' +stId,
            cache : false,
            success : function(response){
                if(response == 'userOk') 
					{
						$.post("regionsettings/location_add_ajax",{stateid: $("#add1_state").val(),location_name:$("#newlocation").val(),created_by:(<?php echo $usernme['userid']?>),'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'}, 
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

/*
 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Ends Here.
 */

</script>
<style type="text/css">
h3 .small {
	font-weight:normal;
	font-size:14px;
	display:block;
}
</style>
<div id="content">
    <div class="inner">
					<form method="post" action="myaccount">
			
				<input type="hidden" value="0e821d87866043e648af4d4ba94676ad" name="ci_csrf_token" id="token">
			
				<h2>Enquiry</h2>
				<table class="layout">
					<tbody><tr>
						<td width="100">Enquiry Posted By: </td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_name'] ?></td>
						<td width="100">Email Address:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_email'] ?></td>
					</tr>
					<tr>
						<td width="100">Phone Number:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_phone'] ?></td>
						<td width="100">Message:</td>
						<td width="240"><?php echo $get_enquiry_detail['oppurtunity_title'] ?></td>
					</tr>
					<tr>
						<td width="100">Date Created:</td>
						<td width="240"><?php ?></td>
						<td width="100">Source:</td>
						<td width="240">Website</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td colspan="4">
										
								<div class="buttons">
								 <button class="positive" id="convert_to_lead" type="button">Convert To Leads</button>
								   <button onclick="location.href='<?php echo base_url().'enquiries/enquirieslist' ?>'" class="negative" type="button">Back</button>
								   
								</div>
													</td>
					</tr>
				</tbody></table>
			</form>
			</div>
</div>
<div id="content" style="display:none;" class="convert_to_lead">
    <div class="inner">
						<?php  if (($this->session->userdata('add')==1 && $this->uri->segment(2) != 'edit_enquiry') || ($this->session->userdata('edit')==1) && ($this->uri->segment(2) == 'edit_enquiry' && is_numeric($this->uri->segment(3)))) { ?>
            <form action="" method="post" id="quote-init-form" class="<?php echo  (isset($view_enquiry) || isset($edit_enquiry)) ? 'display-none' : '' ?>" onsubmit="return false;">
                <input type="hidden" name="custid_fk" id="hidden_custid_fk" />
			
				<input type="hidden" value="" name="ci_csrf_token" id="token">
				
				<input type="hidden" value="<?php echo $get_enquiry_detail['oppurtunity_id'] ?>" name="enquiry_id" id="enquiry_id">
				<h2>Convert this enquiry to Lead</h2>
				<table class="layout">
					<tbody>
					<tr>
					   <td colspan="2"> <p><label>Start by typing in <strong>customer name</strong> or <strong>company name</strong>.</label></p>
                    <p><input type="text" name="ex_cust_name" id="ex-cust-name" class="textfield width200px" /></p>
                    <!--p class="notice width250px">If this is a new customer you need to add the<br /> customer
                    by <a href="#" class="modal-new-cust">completing their details</a>.</p-->
					</td>
					</tr>
					<tr>
						<td width="100">Lead Title:*</td>
						<td width="340"><input type="text" name="lead_title" id="lead_title" class="textfield width200px" onkeyup="$('.q-quote-items .quote-title span').html(this.value);" /> </td>
						<td width="100">Lead Source:</td>
						<td width="340"><select name="lead_source" id="lead_source" class="textfield width200px">
                            <option value="not_select">Please Select</option>
                        <?php
						if (!empty($lead_source)) {
							foreach ($lead_source as $leads) {
							?>
								<option value="<?php echo $leads['lead_source_id'] ?>"><?php echo  $leads['lead_source_name'] ?></option>
							<?php
							}
						}
						?>
                        </select></td>
					</tr>
					<tr>
						<td>Service Requirement:</td>
						<td><select name="lead_service" id="lead_service" class="textfield width200px" onchange="$('.q-service-type span').html(lead_services[$(this).val()]);">
                            <option value="not_select">Please Select</option>
                        <?php 
						foreach ($job_cate as $job) { 
						?>
                            <option value="<?php echo $job['sid'] ?>"><?php echo $job['services'] ?></option>
                        <?php
						}
						?>
                        </select></td>
						<td>Expected worth of Deal:</td>
						<td><select name="expect_worth" id="expect_worth" class="textfield width100px">
                            <option value="not_select">Please Select</option>
                        <?php 
						foreach ($expect_worth as $expect) {
						?>
                            <option value="<?php echo  $expect['expect_worth_id'] ?>"><?php echo  $expect['expect_worth_name'] ?></option>
                        <?php
						}
						?>
                        </select> <?php '/t';?><label> Amount</label> <input type="text" name="expect_worth_amount" id="expect_worth_amount" class="textfield" style=" width:140px" /></td>
					</tr>
					<tr>
						<td>Division:
							<input name="job_belong_to" id="job_belong_to" type="hidden"  value="<?php echo $userdata['userid'] ?>" class="textfield width300px">
						</td>
						<td>
							<select name="job_division" id="job_division" class="textfield width200px">
							<option value="not_select">Please Select</option>
                            <?php
							foreach ($sales_divisions as $sa_div)
							{
							?>
								<option value="<?php echo $sa_div['div_id'] ?>"><?php echo $sa_div['division_name'] ?></option>
							<?php
							}
							?>
                        </select>
						</td>
						<td>Lead Assigned To:</td>
						<td>
							<select name="lead_assign" id="lead_assign" class="textfield width300px">
						    <option value="not_select">Please Select</option>
                            <?php
							if (!empty($lead_assign)) {
								foreach ($lead_assign as $leada) {
							?>
								<option value="<?php echo $leada['userid'] ?>"><?php echo $leada['first_name'] ?></option>
							<?php
								}
							}
							?>
                        </select>
						</td>
					</tr>
					<tr>
						<td>Proposal Expected Date:</td>
						<td>
							<input type="text" name="proposal_expected_date" id="proposal_expected_date" value="" class="textfield width200px" />
						</td>
						<td>Lead Indicator:</td>
						<td>
							<select name="lead_indicator" id="lead_indicator" class="textfield width200px">
							<option value="not_select">Please Select</option>
							<option value="HOT">HOT</option>
							<option value="WARM">WARM</option>
							<option value="COLD">COLD</option>
                        </select>
						</td>
					</tr>
					
		
					<tr>
						<td>
							&nbsp;
						</td>
						<td colspan="4">
															<div class="buttons">
									 <button type="submit" class="positive" onclick="startQuote(); return false;">Start</button>
								</div>
								<div class="buttons">
								    <button type="submit" class="negative" onclick="disableEnquiry(); return false;">Cancel</button>
								</div>
													</td>
					</tr>
				</tbody></table>
			</form>
			<?php } ?>
			</div>
</div>
<script type="text/javascript">
$(function(){

	$('#drag-item-list').draggable({handle:$('.handle', $(this))});
    
	$('#drag-item-list .close').click(function(){
        $(this).parent().hide(400);
        $('#drag-item-list-opener').show();
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
	
	$('#drag-item-list .item-inventory').tabs();

	$('#drag-item-list .item-inventory div ul li').hover(
        function(){
            $(this).addClass('over');
        },
        function(){
            $(this).removeClass('over');
        }
    );
    
	$('#drag-item-list .item-inventory div ul li').click(function(){
        var the_text = '\n';
        the_text += $('.desc', $(this)).text();
        $('#item_desc').val(the_text);
        $('#item_price').val($('.hidden', $(this)).text());
        addItem();
        return false;
    });
	
	$('#item_desc').keyup(function(){
		var desc_len = $(this).val();
		
		if (desc_len.length > 600) {
			$(this).focus().val(desc_len.substring(0, 600));
		}
		
		var remain_len = 600 - desc_len.length;
		if (remain_len < 0) remain_len = 0;
		
		$('#desc-countdown').text(remain_len);
	});

	$('#quote_item_edit_form textarea').livequery(function(){
		$(this).keyup(function(){
			var desc_len = $(this).val();
			
			if (desc_len.length > 600) {
				$(this).val(desc_len.substring(0, 600));
			}
			
			var remain_len = 600 - desc_len.length;
			if (remain_len < 0) remain_len = 0;
			
			$('#desc-edit-countdown').text(remain_len);
		});
	});
	
});

//code added for when the customer name field is empty it will show the notice div.
$("#ex-cust-name").keyup(function(){
//alert('test');
var mylength = $(this).val();
    if(mylength == '') {
		$('.notice').slideDown(400);
		location.reload();
    }
});

function resetCounter() {
$('#desc-countdown').text(600);
}

function openDragItems() {
	
    var topv = parseInt($(window).scrollTop()) + 40;
    var leftv = $('#content').offset().left + 20;
    $('#drag-item-list').show(700).animate({top:topv+'px',left:leftv+'px'}, 1000);
    $('#drag-item-list-opener').hide();
    
}

function setProjectStatusDate(date_type) {
	var set_date_type, date_val, d_class;
	
	if (date_type == 'start')
	{
		set_date_type = 'start';
		date_val = $('#project-start-date').val();
		d_class = 'startdate';
	}

	if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
		alert('Please insert a valid date!');
		return false;
	} else {
		$.post(
			'welcome/set_proposal_date/',
			{'lead_id':curr_job_id, 'date_type':set_date_type, 'date':date_val, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('h6.project-' + d_class + '-label span').text(date_val);
							$('.project-' + d_class + '-change:visible').hide(200);
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
	}
}

function setLeadCreationDate() {
	var	date_val = $('#lead_creation_date').val();
	
	if (! /^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
		alert('Please insert a valid date!');
		return false;
	} else {
		$.post(
			'welcome/set_lead_creation_date/',
			{'lead_id':curr_job_id, 'date':date_val, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			function(_data) {
				try {
					eval ('var data = ' + _data);
					if (typeof(data) == 'object') {
						if (data.error == false) {
							$('h6.lead-created-label span').text(date_val);
							$('.lead-created-change:visible').hide(200);
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
	}
}

$(function(){
	
	$('#lead_creation_date').datepicker({dateFormat: 'dd-mm-yy', maxDate: 0});
	$('#project-date-assign, #proposal_expected_date, .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});
	
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

	
	$('#job-url-list li a:not(.file-delete)').livequery(function(){
		$(this).click(function(){
			window.open(this.href);
			return false;
		});
	});
	
	$('.jump-to-job select').change(function(){
		var _new_location = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{lead_id}}', $_SERVER['REQUEST_URI']) ?>';
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
function is_project() {
	// alert(curr_job_id); return false;
    
	$.blockUI({
		message:'<h2>Processing your request...</h2>'
	});
	$.getJSON('welcome/ajax_update_lead_status/' + curr_job_id,
		function(data){
			if (typeof(data) == 'object') {
				if (data.error) {
					alert(data.errormsg);
					$.unblockUI();
					window.location.href = site_base_url+"welcome/edit_quote" + "/" + curr_job_id +"/";
					//alert('status Changed');
				} else {
					//alert(qstatus);
					reloadWithMessagePjt('Lead Successfully moved to Project', curr_job_id);
				}
			} else {
				alert('Unexpected response from server!')
				$.unblockUI();
			}
		});
   
}

function reloadWithMessagePjt(str, statusid) {
	$.get('ajax/request/set_flash_data/' + str,{},function(data){
		document.location.href = site_base_url+'project/view_project/' + curr_job_id;
		$.unblockUI();
	});
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

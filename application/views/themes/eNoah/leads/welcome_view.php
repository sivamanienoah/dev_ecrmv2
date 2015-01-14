<?php require (theme_url().'/tpl/header.php'); ?>

<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<input type="hidden" class="hiddenUrl"/>
<script type="text/javascript">
<?php 
$userdata = $this->session->userdata('logged_in_user');
$usernme = $this->session->userdata('logged_in_user');
?>
var curr_job_id = <?php echo isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0 ?>;
var customer_id = <?php echo isset($quote_data['custid_fk']) ? $quote_data['custid_fk'] : 0 ?>;
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

$(document).ready(function(){
	
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
			$('.q-cust-name span').html(details.first_name + ' ' + details.last_name);
			$('.q-cust-email span').html(details.email_1);
			if (existing_lead > 0) {
				$('#ex-cust-name').val(details.first_name + ' ' + details.last_name);
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
        err.push('Lead title is required');
    }
    if ($('#lead_service').val() == 'not_select') {
        err.push('Service Requirement must be selected');
    }
	 if ($('#lead_source').val() == 'not_select') {
        err.push('Lead Source must be selected');
    }
	 if ($('#lead_assign').val() == 'not_select') {
        err.push('Lead Assigned to must be selected');
    }
	 if ($('#job_division').val() == 'not_select') {
        err.push('Entity must be selected');
    }
	if ($('#expect_worth').val() == 'not_select') {
        err.push('Expected Worth Curreny must be selected');
    }
	if ($.trim($('#expect_worth_amount').val()) == '') {
        err.push('Expected Worth Amount is required');
    }
	if ($.trim($('#proposal_expected_date').val()) == '') {
        err.push('Proposal Expected Date is required');
    }
	if ($('#lead_indicator').val() == 'not_select') {
        err.push('Lead Indicator must be selected');
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
		
        $.post('welcome/ajax_create_quote',form_data,function (res) {
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

function editQuoteDetails(arg) {
    var err = [];
    if ($.trim($('#job_title_edit').val()) == '') {
        err.push('Lead title is required');
    }
    if ($('#job_category_edit').val() == 'not_select') {
        err.push('Service Requirement must be selected');
    }
	 if ($('#lead_source_edit').val() == 'not_select') {
        err.push('Lead Source must be selected');
    }
	 if ($('#lead_assign_edit').val() == 'not_select') {
        err.push('Lead Assigned to must be selected');
    }
	if ($('#job_division_edit').val() == 'not_select') {
        err.push('Entity must be selected');
    }
	if ($('#expect_worth_edit').val() == 'not_select') {
        err.push('Expected Worth Currency must be selected');
    }

	var act_worth = $.trim($('#actual_worth').val());

	if ((act_worth == 0.00) || (act_worth == '') || (act_worth == 0.0)) {
		act_worth = 0;
	}

	if ( ($.trim($('#lead_status').val()) == 4) && (act_worth <= 0) ) {
		err.push('Actual worth amount must not be empty and it must be greater than zero');
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
                        if(arg == 'view') {
							window.location.href = site_base_url+"welcome/view_quote" + "/" + curr_job_id;
						}
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
<?php //echo '<pre>'; print_r($quote_data); echo '</pre>'; ?>
    <div class="inner">
	<?php  if (($this->session->userdata('add')==1 && $this->uri->segment(2) != 'edit_quote') || ($this->session->userdata('edit')==1) && ($this->uri->segment(2) == 'edit_quote' && is_numeric($this->uri->segment(3)))) { ?>
    	<div class="q-main-left">
            <form action="" method="post" id="quote-init-form" class="<?php echo  (isset($view_quotation) || isset($edit_quotation)) ? 'display-none' : '' ?>" onsubmit="return false;">
				
                <input type="hidden" name="custid_fk" id="hidden_custid_fk" />
                <h2>Create a Lead</h2>
             
                <div>
                    <p><label>Start by typing in <strong>customer name</strong> or <strong>company name</strong>.</label></p>
                    <p><input type="text" name="ex_cust_name" id="ex-cust-name" class="textfield width300px" /></p>
                    <p class="notice width250px">If this is a new customer you need to add the<br /> customer
                    by <a href="#" class="modal-new-cust">completing their details</a>.</p>
                    <p><label>Lead Title</label></p>
                    <p><input type="text" name="lead_title" id="lead_title" class="textfield width300px" onkeyup="$('.q-quote-items .quote-title span').html(this.value);" /></p>
					<p><label>Lead Source</label></p>
                    
					<p><select name="lead_source" id="lead_source" class="textfield width300px">
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
                        </select>
                    </p>
                    <p><label>Service Requirement</label></p>
					<p><select name="lead_service" id="lead_service" class="textfield width300px" onchange="$('.q-service-type span').html(lead_services[$(this).val()]);">
                            <option value="not_select">Please Select</option>
                        <?php 
						foreach ($job_cate as $job) { 
						?>
                            <option value="<?php echo $job['sid'] ?>"><?php echo $job['services'] ?></option>
                        <?php
						}
						?>
                        </select>
                    </p>
					
					<p><label>Expected worth of Deal</label></p>
                    
					<p><select name="expect_worth" id="expect_worth" class="textfield width100px">
                            <option value="not_select">Please Select</option>
                        <?php 
						foreach ($expect_worth as $expect) {
						?>
                            <option value="<?php echo  $expect['expect_worth_id'] ?>"><?php echo  $expect['expect_worth_name'] ?></option>
                        <?php
						}
						?>
                        </select> <?php '/t';?><label> Amount</label> <input type="text" name="expect_worth_amount" id="expect_worth_amount" class="textfield" style=" width:140px" />
                    </p>
					
					
				
					<input name="job_belong_to" id="job_belong_to" type="hidden"  value="<?php echo $userdata['userid'] ?>" class="textfield width300px">
					
					<p><label>Entity</label></p>
					<p>
						<select name="job_division" id="job_division" class="textfield width300px">
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
                    </p>
					<p><label>Lead Assigned To</label></p>
					<p>
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
                    </p>
					<p><label>Proposal Expected Date</label></p>
					<p>
						<input type="text" name="proposal_expected_date" id="proposal_expected_date" value="" class="textfield width300px" />
                    </p>
					<p><label>Lead Indicator</label></p>
					<p>
						<select name="lead_indicator" id="lead_indicator" class="textfield width300px">
						<option value="not_select">Please Select</option>
						<option value="HOT">HOT</option>
						<option value="WARM">WARM</option>
						<option value="COLD">COLD</option>
                        </select>
                    </p>
					
                    <div class="buttons clearfix">
                        <button type="submit" class="positive" onclick="startQuote(); return false;">Start</button>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </form>
            
            <?php if (isset($edit_quotation) && isset($quote_data)) { ?>
				<h2> Edit Lead </h2>
				
				<form name="lead_dates" id="lead-change-date" style="padding:0 5px 0 0; margin:0 !important;">
					<table>
						<tr>
							<td valign="top" width="300">
								<h6 class="lead-created-label">Lead Creation Date &raquo;<span><?php if ($quote_data['date_created'] != '') echo date('d-m-Y', strtotime($quote_data['date_created'])); else echo 'Not Set'; ?></span></h6>
								<p><a href="#" onclick="$('.lead-created-change:hidden').show(200); return false;">Change?</a></p>
								
								<div class="lead-created-change">
									<input type="text" value="" class="textfield pick-date" name="lead_creation_date" id="lead_creation_date" />
									<div class="buttons clearfix">
										<button type="submit" class="positive" onclick="setLeadCreationDate(); return false;">Set</button>
										<button type="submit" onclick="$('.lead-created-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</form>
			
				<form name="project_dates" id="project-date-assign" style="padding:5px 0 5px 0;">
					<table>
						<tr>
							<td valign="top" width="300">
								<h6 class="project-startdate-label">Proposal Expected Date &raquo;<span><?php if ($quote_data['proposal_expected_date'] != '') echo date('d-m-Y', strtotime($quote_data['proposal_expected_date'])); else echo 'Not Set'; ?></span></h6>
								<p><a href="#" onclick="$('.project-startdate-change:hidden').show(200); return false;">Change?</a></p>
								
								<div class="project-startdate-change">
									<input type="text" value="" class="textfield pick-date" name="proposal_expected_date" id="project-start-date" />
									<div class="buttons clearfix">
										<button type="submit" class="positive" onclick="setProjectStatusDate('start'); return false;">Set</button>
										<button type="submit" onclick="$('.project-startdate-change:visible').hide(200); return false;">Cancel</button>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</form>

				<form action="" method="post" id="quote-edit-form" onsubmit="return false;">
					<div>
						<p><label>Lead No</label></p>
						<p><input type="text" name="lead_no" id="lead_no" class="textfield width300px" value="<?php echo $quote_data['invoice_no'] ?>" disabled /></p>
						<p><label>Lead Title</label></p>
						<p><input type="text" name="lead_title" id="job_title_edit" class="textfield width300px" value="<?php echo  htmlentities($quote_data['lead_title'], ENT_QUOTES) ?>" /></p>
						<p><label>Lead Source</label></p>
						<p><select name="lead_source_edit" id="lead_source_edit" class="textfield width300px">
								<option value="not_select">Please Select</option>
							<?php 
							foreach ($lead_source_edit as $leadedit) 
							{
							?>
								<option value="<?php echo $leadedit['lead_source_id'] ?>"<?php echo ($quote_data['lead_source'] == $leadedit['lead_source_id']) ? ' selected="selected"' : '' ?>><?php echo  $leadedit['lead_source_name'] ?></option>
							<?php
							}
							?>
							</select>
						</p>
						<p><label>Service Requirement</label></p>
						<p><select name="lead_service" id="job_category_edit" class="textfield width300px">
								<option value="not_select">Please Select</option>
							<?php 
								foreach ($job_cate as $job) 
								{ 
							?>
								<option value="<?php echo $job['sid'] ?>"<?php echo ($quote_data['lead_service'] == $job['sid']) ? ' selected="selected"' : '' ?>><?php echo $job['services'] ?></option>
							<?php
								}
							?>
							</select>
						</p>
						<p><label>Expected worth of Deal</label></p>
						<p><select name="expect_worth_edit" id="expect_worth_edit" class="textfield" style="width:100px">
								<option value="not_select">Please Select</option>
							<?php foreach ($expect_worth as $worth) {							
								?>
								<option value="<?php echo $worth['expect_worth_id'] ?>"<?php echo  ($quote_data['expect_worth_id'] == $worth['expect_worth_id']) ? ' selected="selected"' : '' ?>><?php echo  $worth['expect_worth_name'] ?></option>
							<?php
								
							}
							?>
							</select><?php echo'&nbsp;&nbsp;&nbsp;' ?>
							<label> Amount</label> <?php echo'&nbsp;&nbsp;&nbsp;' ?><input type="text" name="expect_worth_amount" value="<?php echo $quote_data['expect_worth_amount'];?>" id="expect_worth_amount" class="textfield" style=" width:132px" />
						</p>
						
						<p><label>Actual worth of Project</label><?php echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ?><input type="text" name="actual_worth" value="<?php
							if($quote_data['actual_worth_amount'] != '0.00') 
							$amount = $quote_data['actual_worth_amount'];
							else 
							$amount = $quote_data['actual_worth_amount'];
							echo $amount;
						?>" id="actual_worth" class="textfield" style=" width:155px" /></p>
						<input type="hidden" name="expect_worth_amount_dup" value="<?php echo $quote_data['actual_worth_amount'];?>" id="expect_worth_amount_dup" class="textfield" style=" width:85px" />
						<!--<p><label>Lead Owner</label></p>
						<p>
							<input name="job_belong_to" id="job_belong_to_edit"  class="textfield width300px">
								
						</p> -->
						<p><label>Entity</label></p>
						<p>
							<select name="job_division" id="job_division_edit" class="textfield width300px">
								<option value="not_select">Please Select</option>
								<?php
								
								foreach ($sales_divisions as $sa_div)
								{								
								?>
									<option value="<?php echo $sa_div['div_id'] ?>"<?php echo ($quote_data['division'] == $sa_div['div_id']) ? ' selected="selected"' : '' ?>><?php echo $sa_div['division_name'] ?></option>
								<?php								
								}
								?>
							</select>
							
						</p>
						<!-- lead owner edit owner starts -->
						<p><label>Lead Owner </label></p>
						<p>
						<?php if(($quote_data['belong_to'] ==  $userdata['userid']) || ($userdata['role_id'] == 1 || $userdata['role_id'] == 2)) { ?>
							
							
							<select name="lead_owner_edit" id="lead_owner_edit" class="textfield width300px">
							
							<?php foreach ($lead_assign_edit as $leadassignedit) { ?>
								
								<option value="<?php echo  $leadassignedit['userid'] ?>"<?php echo  ($quote_data['belong_to'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] . " " . $leadassignedit['last_name'] ?></option>
								
							<?php
								
							}
							?>
							</select>
							<script>
							$('#lead_owner_edit').change(function() {
								var assign_mail = $('#lead_owner_edit').val();
								//alert(assign_mail);
								$('#lead_owner_edit_hidden').val(assign_mail);
							});
							</script>
						<?php } else { ?>
										<select name="lead_owner_edit" id="lead_owner_edit" class="textfield width300px" disabled=true>
							<?php foreach ($lead_assign_edit as $leadassignedit) {
								
								?>
								<option value="<?php echo  $leadassignedit['userid'] ?>"<?php echo  ($quote_data['belong_to'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo  $leadassignedit['first_name'] ?></option>
								
							<?php
								
							}
							?>
							</select>
							<script>
								$(document).ready(function() {
								var assign_hidden = $('#lead_owner_edit').val();
								//alert(assign_hidden);
								$('#lead_owner_edit_hidden').val(assign_hidden);
								});
							</script>
						<?php } ?>
						<input type="hidden" value="0" id="lead_owner_edit_hidden" name="lead_owner_edit_hidden"/>
						
						</p>
						<!-- lead edit owner ends here -->
						<p><label>Lead Assigned To</label></p>
						<p>
						<?php if($quote_data['belong_to'] ==  $userdata['userid'] || $userdata['role_id'] == 1) { ?>
						
							<select name="lead_assign_edit" id="lead_assign_edit" class="textfield width300px">
							<?php foreach ($lead_assign_edit as $leadassignedit) {
								
								?>
								<option value="<?php echo  $leadassignedit['userid'] ?>"<?php echo  ($quote_data['lead_assign'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] . " " . $leadassignedit['last_name'] ?></option>
							<?php
								
							}
							?>
							</select>
							<script>
							$('#lead_assign_edit').change(function() {
								var assign_mail = $('#lead_assign_edit').val();
								//alert(assign_mail);
								$('#lead_assign_edit_hidden').val(assign_mail);
							});
							</script>
						<?php } else {	?>
										<select name="lead_assign_edit" id="lead_assign_edit" class="textfield width300px" disabled=true>
							<?php foreach ($lead_assign_edit as $leadassignedit) {
								
								?>
								<option value="<?php echo  $leadassignedit['userid'] ?>"<?php echo  ($quote_data['lead_assign'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo  $leadassignedit['first_name'] ?></option>
								
							<?php 
								
							}
							?>
							</select>
							<script>
								$(document).ready(function() {
								var assign_hidden = $('#lead_assign_edit').val();
								//alert(assign_hidden);
								$('#lead_assign_edit_hidden').val(assign_hidden);
								});
							</script>
						<?php } ?>
						<input type="hidden" value="0" id="lead_assign_edit_hidden" name="lead_assign_edit_hidden"/>
						
						</p>
						<p><label>Lead Indicator</label></p>
						<p>
							<select name="lead_indicator" id="lead_indicator" class="textfield width300px">
							<!--<option value="<?php #echo $quote_data['lead_indicator'] ?>"><?php #echo $quote_data['lead_indicator'] ?></option>-->
							<option value="HOT" <?php if($quote_data['lead_indicator'] == "HOT") echo "selected"; ?>>HOT</option>
							<option value="WARM" <?php if($quote_data['lead_indicator'] == "WARM") echo "selected"; ?>>WARM</option>
							<option value="COLD" <?php if($quote_data['lead_indicator'] == "COLD") echo "selected"; ?>>COLD</option>
							</select>
						</p>
						<p><label>Lead Status</label></p>
						
						<p>
							<select name="lead_status" id="lead_status" class="textfield width300px" onchange="getReason(this.value);">
							<option value="1"  <?php if($quote_data['lead_status'] == 1) echo 'selected="selected"'; ?>>Active</option>
							<option value="2"  <?php if($quote_data['lead_status'] == 2) echo 'selected="selected"'; ?>>OnHold</option>
							<option value="3"  <?php if($quote_data['lead_status'] == 3) echo 'selected="selected"'; ?>>Dropped</option>
							<option value="4"  <?php if($quote_data['lead_status'] == 4) echo 'selected="selected"'; ?>>Closed</option>
							</select>
							<input type="hidden" value="<?php echo $quote_data['lead_status']; ?>" id="lead_status_hidden" name="lead_status_hidden" />
							<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $quote_data['custid_fk'] ?>" />
						</p>
						<script>
						//if(document.getElementById('lead_status').value == 2)
						//document.getElementById('lead-reason').style.display = "block";
						function getReason(val){
							if(val == 2) {							
								document.getElementById('lead-reason').style.display = "block";
							} else {
								document.getElementById('lead-reason').style.display = "none";
							}					
						}
						</script>
						<?php if(($quote_data['lead_status'] == 2) && isset($quote_data['lead_hold_reason'])) { ?>
								<div id="lead-reason" class="lead-reason" style="display:block;">
								<p><label>Reasons:</label></p>
								<textarea class="textfield" style="width:300px; height:80px;" id="reason" name="reason"><?php echo $quote_data['lead_hold_reason']?></textarea>
								</div>
							<?php }
							else { ?>
								<div id="lead-reason" class="lead-reason" style="display:none;">
								<p><label>Reasons:</label></p>
								<textarea class="textfield" style="width:300px; height:80px;" id="reason" name="reason"></textarea>
								</div>
							<?php } ?>
						
						<input type="hidden" name="jobid_edit" id="jobid_edit" value="<?php echo  $quote_data['lead_id'] ?>" />
						<div style="width:300px;">
						<div class="buttons clearfix pull-left">
							<button type="submit" class="positive" onclick="editQuoteDetails('save'); return false;">Save</button>
						</div>
						<div class="buttons clearfix pull-left" style="padding: 0px 10px;">
							<button type="submit" class="positive" onclick="editQuoteDetails('view'); return false;">Save & View</button>
						</div>
						<?php if($quote_data['lead_status'] == 4) { ?>
							<div class="buttons clearfix pull-left">
								<button type="submit" class="positive" onclick="confirmaMoveLeadsToProject(); return false;">Move To Project</button>
								
								<!-- <button type="submit" class="positive" onclick="is_project(); return false;">Move To Project</button> -->
							</div>
						<?php } ?>
						</div>
						<!--div class="buttons">
							<button type="submit" onclick="$('#quote-edit-form').slideUp(400); return false;">Done</button>
						</div-->
						<p style="clear:left;">&nbsp;</p>
					</div>
				</form>
            <?php } ?>
            
            <form id="item-submit">
                <input type="hidden" name="lead_id" id="hidden_jobid" />
                <h3>Add items to Lead</h3>
                <div class="item-container">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="3">
                                <p><label>Item description, <strong><span id="desc-countdown">600</span></strong> characters left.</label></p>
                                <p><textarea name="item_desc" id="item_desc" class="textfield width99pct height120px"></textarea></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="width100px">
                                <p><label>Hours</label></p>
                                <p><input type="text" name="hours" id="hours" class="textfield width80px" onblur="setPrice(this.value);" /></p>
                            </td>
                            <td class="width100px">
                                <p><label>Item price</label></p>
                                <p><input type="text" name="item_price" id="item_price" class="textfield width80px" /></p>
                            </td>
                        </tr>
                    </table>
					
					<div class="clearfix">
						<div class="buttons pull-left">
							<button type="submit" onclick="addItem();  return false;">Add Item</button>
						</div>
						<div class="buttons pull-left" id="drag-item-list-opener">
							<button type="submit" class="" onclick="openDragItems();  return false;">Additional Features</button>
						</div>
					</div>
                </div>
                
            </form>
			
			<?php
			/**
			 * This will include the select box that changes the status of a job
			 */
			if (isset($edit_quotation))
				// include '../tpl/status_change_menu.php';
				require (theme_url().'/tpl/status_change_menu.php');
			?>
           
			
        </div>
        <div class="q-main-right">
			<?php
			if (isset($quote_data))
			{
				?>
			<div class="leadstg_note_top">
				Please be careful when editing previous documents, the new textfield only allow 600 characters to be inserted. If you are editing items of a previous document, please note that you will only see 600 characters.
			</div>
				<?php
			}
			?>
            <div class="q-container">
                <div class="q-details">
					
                    
                    <div class="q-quote-items" style="position: relative;">
						<h4 class="quote-title">Project Name : <span><?php echo (isset($quote_data)) ? $quote_data['lead_title'] : '' ?></span></h4>
                        <ul id="q-sort-items"></ul>
                    </div>
                </div>
            </div>
            <div class="q-save-order">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="120">Save new item order?</td>
						<td width="300">
						<div class="buttons clearfix">
							<button type="submit" class="positive" onclick="saveSortedItems();  return false;">Save</button>
						</div>
						<div class="buttons">
							<button type="submit" class="negative" onclick="$('.q-save-order').slideUp('fast');  return false;">Cancel</button>
						</div>
						</td>
					</tr>
				</table>
            </div>
            <div class="q-sub-total">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td>Sale Amount <span id="sale_amount"></span></td>
                        <td>GST <span id="gst_amount"></span></td>
                        <td>&nbsp;</td>
                        <td align="right">Total inc GST <span id="total_inc_gst"></span></td>
                    </tr>
                </table>
            </div>
        </div>
		<?php } else {
			echo "You have no rights to access this page";
		} ?>
	</div>
</div>
<div id="drag-item-list">
    <div class="close">X</div>
    <div class="handle">||||</div>
    <div class="item-inventory">
		<?php
		$menu = '<ul class="tabs-nav">';
		$data = '';
		
		
		foreach ($categories as $cat)
		{
			$menu .= '<li><a href="'.current_url().'#cat_'.$cat["cat_id"].'">'.$cat["cat_name"].'</a></li>';
			$records = $cat['records'];
			ob_start();
			?>
			<div id="cat_<?php echo $cat['cat_id'] ?>">
				<ul>
					<?php foreach ($records as $record) { ?>
					<li>
						<span onclick="resetCounter();" class="desc"><?php echo  nl2br($record['item_desc']) ?></span><br />
						<span class="hidden"><?php echo  $record['item_price'] ?></span>
						<strong>$<?php echo  number_format($record['item_price'], 2, '.', ',') ?></strong>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php
			$data .= ob_get_clean();
		}
		$menu .= '</ul>';
		
		echo $menu, $data;
		?>
		
    </div>
</div>
<div class="comments-log-container"></div>
<script type="text/javascript">
/*
*@Method runSOWAjaxFileUpload
*@Use Upload SOW Files
*File projects/leads_confirm_view.php
*Author eNoah - Mani.S
*/
function runSOWAjaxFileUpload() {
	var _uid				 = new Date().getTime();
	var params 				 = {};
	params[csrf_token_name]  = csrf_hash_token;	

	$.ajaxFileUpload({
		url: 'project/sow_file_upload/'+curr_job_id,
		secureuri: false,
		fileElementId: 'sow_ajax_file_uploader',
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
							var name = '<div style="float: left; width: 100%;"><span style="float: left;">'+res[1]+'</span></div>';
							$("#sowUploadFile").append(name);
						});
						//$.unblockUI();
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
	$('#sow_ajax_file_uploader').val('');
	return false;
}

/*
*@Method change_project_category
*@Use Show and hide the project and cost center tr
*Author eNoah - Mani.S
*/
function change_project_category(val)
{

	if(val == 1) {
		$('#project_center_tr').show();
		$('#cost_center_tr').hide();
	}else if(val == 2) {
		$('#cost_center_tr').show();
		$('#project_center_tr').hide();
	}else {
		$('#cost_center_tr').hide();
		$('#project_center_tr').hide();
	}

}

function confirmaMoveLeadsToProject()
	{
		var fsl_height = parseInt($(window).height()) - 80;
		fsl_height = fsl_height + 'px';
		var params = {};
		$.post( 
			site_base_url+'project/getCurentLeadsDetails/', {job_id:curr_job_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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
			css: {
					border: '2px solid #999',
					color:'#333',
					padding:'8px',
					top:  ($(window).height() - 450) /2 + 'px', 
					left: ($(window).width() - 800) /2 + 'px', 
					width: '800px' 
				} 
		});
		
	}

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

/*
*@ Move lead to projects
*@ Last Changed on 26/12/2014 By Mani.S
*/
function is_project() {
	// alert(curr_job_id); return false;	
	var err = [];
  
    if ($.trim($('#department_id_fk').val()) == 'not_select') {
        err.push('Project department must be selected');
    }
	if ($.trim($('#resource_type').val()) == 'not_select') {
        err.push('Resource type must be selected');
    }
    if ($('#project_name').val() == '') {
        err.push('Project name is required');
    }
	/*if ($('#project_types').val() == 'not_select') {
        err.push('Project types must be selected');
    }*/
	if ($('#timesheet_project_types').val() == 'not_select') {
        err.push('Timesheet project types must be selected');
    }
   if ($("input[name=project_category]").is(":checked") == false) {
        err.push('Project category must be selected');
    }else if ($("input[name=project_category]").is(":checked") == true && $("input[name=project_category]:checked").val() == 1 && $('#project_center_value').val() == 'not_select') {
	
		 err.push('Project center must be selected');
	
	}else if ($("input[name=project_category]").is(":checked") == true && $("input[name=project_category]:checked").val() == 2 && $('#cost_center_value').val() == 'not_select') {
	
		 err.push('Cost center must be selected');
	
	}	
	if ($("input[name=sow_status]").is(":checked") == false) {
        err.push('SOW status must be selected');
    }
	 
    if (err.length > 0) {
         alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
		/*$.blockUI({
			message:'<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">'+err.join('<br />')+'</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
			css:{width:'440px'}
		});*/
        return false;
    }else {
    
	$.blockUI({
		message:'<h2>Processing your request...</h2>'
	});	
	
	 // get form data
        var form_data = $('#project-confirm-form').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        $.post(
            'welcome/ajax_update_lead_status/' + curr_job_id,
            form_data,
            function (res) {
                if (typeof (res) == 'object') {
                    if (res.error == true) {
                        // good to go
                        alert(data.errormsg);						
						window.location.href = site_base_url+"welcome/edit_quote" + "/" + curr_job_id +"/";
						$.unblockUI();
                    } else {
                       reloadWithMessagePjt('Lead Successfully moved to Project', curr_job_id);
                    }
                } else {
                    alert('Unexpected response from server!')
					$.unblockUI();
                }              
				$.unblockUI();
            },
            "json"
        );
		

	}
}

function reloadWithMessagePjt(str, statusid) {
	$.get('ajax/request/set_flash_data/' + str,{},function(data){
		document.location.href = site_base_url+'project/view_project/' + curr_job_id;
		$.unblockUI();
	});
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>
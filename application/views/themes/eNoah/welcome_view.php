<?php require (theme_url().'/tpl/header.php'); ?>

<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<input type="hidden" class="hiddenUrl"/>
<script type="text/javascript">
<?php 
$userdata = $this->session->userdata('logged_in_user');
$usernme = $this->session->userdata('logged_in_user');
?>
var curr_job_id = <?php echo  isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>;
var job_categories = [];
job_categories['not_select'] = '';
<?php foreach ($cfg['job_categories'] as $jck => $jcv) { ?>
job_categories[<?php echo  $jck ?>] = '<?php echo  $jcv ?>';
<?php } ?>


var item_inventory = [];
<?php foreach ($cfg['item_inventory'] as $iv_key => $iv_val) { ?>
item_inventory[<?php echo  $iv_key ?>] = ['<?php echo  str_replace("'", "\'", $iv_val['desc']) ?>', '<?php echo  str_replace("'", "\'", $iv_val['hours']) ?>'];
<?php } ?>

var hourly_rate = '';
var quote_id = <?php echo isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>;
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

$(document).ready(
	function(){
		$("#ex-cust-name").autocomplete("hosting/ajax_customer_search/", { minChars:2, width:'308px' }).result(function(event, data, formatted) {
			ex_cust_id = data[1];
			regId = data[2];
			cntryId = data[3];
			stId = data[4];
			locId = data[5];
            prepareQuoteForClient(ex_cust_id);
			getUserForLeadAssign(regId,cntryId,stId,locId);
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
                        css: {width: '690px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default'},
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
        
        populateQuote(<?php echo $quote_data['jobid'] ?>);
        
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
		function (userdetails) {
			//$('.q-cust-company span').html(userdetails.company);
			//alert(userdetails);
			get_user_infm(userdetails);
						
			if (existing_lead > 0) {
				//$('#ex-cust-name').val(userdetails.first_name + ' ' + userdetails.last_name);
			}
		}
	);
	/*if (existing_lead > 0) {
		if (existing_lead_service) {
			$('#job_belong_to').val(existing_lead_service);
		}
		$('#job_belong_to').parent().after('<p class="notice width250px">This is an existing lead. Upon starting, the logs belong to this lead will be transferred to this job.</p>');
		$('#quote-init-form').append('<input type="hidden" name="transfer_lead" value="' + existing_lead + '" />')
	}*/
}


function get_user_infm(users){
	// alert(users); return false;
	var baseurl = $('.hiddenUrl').val();
	var data = users
	$.ajax({
		type: "POST",
		url : baseurl + 'user/getUserDetFromDb/',
		cache : false,
		data: { user: users },
		success : function(response){
		//alert(response);
			if(response != '') {
				$("#lead_assign").html(response);
			}
		}
	});
}

function cleanupLead() {
	 $.getJSON(
		'leads/delete/' + existing_lead + '/TRUE',
		{},
		function (data) {
			
		}
	);
	if (window.console)
	{
		console.log('Clean up called');
	}
	return false;
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
    if ($.trim($('#job_title').val()) == '') {
        err.push('Job title is required');
    }
    if ($('#job_category').val() == 'not_select') {
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
        alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
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
						
						// if this was a lead, clear the lead
						if (res.lead_converted)
						{
							cleanupLead();
						}
						
                        // good to go
                        $('.q-id span').html(res.fancy_insert_id);
                        quote_id = res.insert_id;
                        $('#quote-init-form').slideUp();
						$('#content').block({
						message:'<h4>Processing...</h4>'
						});
                        
						/*
						if (res.job_category == 1) { // web development quote
                            $('#web-dev-prep').slideDown();
                        } else if (res.job_category == 16) { // 16 => 'Premium Real Estate Package'
                            CreateRealestateWebQuote(880);
						} else if (res.job_category == 17) { // 17 => 'Unlimited Real Estate Package'
                            CreateRealestateWebQuote(879);
                        } else if (res.job_category == 18) { // 18 => 'Property Developers Package'
                            CreateRealestateWebQuote(877);
						} else if (res.job_category == 19) { // 19 => 'Commercial Real Estate Package'
                            CreateRealestateWebQuote(878);
						} else if (res.job_category == 20) { // 20 => 'Premium Business Website'
                            CreateRealestateWebQuote(984);
						} else if (res.job_category == 21) { // 21 => 'Unlimited Business Package'
                            CreateRealestateWebQuote(1060);
						} else if (res.job_category == 22) { // 22 => 'One Page Websites (Real Tools)'
                            CreateRealestateWebQuote(1312);
						} else if (res.job_category == 30) { // 30 => 'V-Series Website Packages | V1 Unlimited'
                            CreateRealestateWebQuote(1318);
                        } else if (res.job_category == 5) { // 5 => 'Web Hosting'
                            CreateRealestateWebQuote(985);
                        } else { // normal
                            $('#item-submit, #change-quote-status').slideDown();
							populateQuote(res.insert_id);
                        }
						*/
						
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
        // add jobid to form
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
                        $('#item_title, #item_price, #item_desc, #hours, #item_section').val('');
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
    item_sort_order = $('#q-sort-items').sortable('serialize')+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
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
    /*if ($.trim($('#job_desc_edit').val()) == '') {
        err.push('Job description is required');
    }*/
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

	
	
    if (err.length > 0) {
        alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
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
                        $('.q-title').html(res.job_title);
                        $('.q-service-type span').html(job_categories[res.job_category]);
                        
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

function selectItemSection() {
    var si = $('#item_section').val();
    $('#item_desc').val(item_inventory[si][0]);
    $('#hours').val(item_inventory[si][1]).focus().blur();
}


/*
 *Functions for adding New Country, New State & New Location in the New Lead Creation page -- Starts Here
 */
 function ajxCty(){
	$("#addcountry").slideToggle("slow");
}

function ajxSaveCty(){
	$(document).ready(function() {
        /*if( $('#newcountry').val().length > 2 )
            {
              var newCty = $('#newcountry').val();
              getCty(newCty);
            }
        return false;
		*/
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

/*
 * Web development quotes
 */

var web_dev_data = {
                    num_pages : 0,
                    domain : 0
                };


function startWebDevQuote() {
    
    var errors = [];
    var num_pages = $('#web-dev-prep select[@name="prep_pages"]').val();
    var page_html = '';
    /*
     * set default value
     */
    web_dev_data.num_pages = 0;
    $('#web_number_of_pages').val(0);
    
    if (!$('#web-dev-prep input[@name="prep_gui"]').is(':checked')) {
        errors.push('Does this quote need GUI design?');
    }
    if (!$('#web-dev-prep input[@name="prep_np"]').is(':checked')) {
        errors.push('Does this quote need NewsletterPRO?');
    }
    if (!$('#web-dev-prep input[@name="prep_vs"]').is(':checked')) {
        errors.push('Does this quote need V-Shop?');
    }
    if (!$('#web-dev-prep input[@name="prep_domain"]').is(':checked')) {
        errors.push('Does this quote need domain registration?');
    }
    if (!$('#web-dev-prep input[@name="prep_hosting"]').is(':checked')) {
        errors.push('Does this quote need hosting?');
    }
    if (num_pages == 0) {
        errors.push('How many pages does this quote need?');
    }
    
    if (errors.length > 0) {
        alert(errors.join('\n'));
        return false;
    } else {
        
        page_html += '<br /><p>Please enter page details below.</p>';
        
        for(var j = 0; j < num_pages; j++){
            page_html += '<p><input type="text" name="web_pages_'+ j +'" class="dynamic-page-name textfield width180px" /> &nbsp; &nbsp; <input type="checkbox" name="editablepage_'+ j +'" value="1" /> editable &nbsp; <input type="checkbox" name="formpage_'+ j +'" value="1" /> form </p>';
        }
        
        if ($('#web-dev-prep input[@name="prep_domain"]:checked').val() == 1) {
            page_html += '<br /><p>Domain Name <input type="text" name="prep_domain_name" value="" class="dynamic-domain-name textfield width180px" />';
            web_dev_data.domain = 1;
        } else {
            web_dev_data.domain = 0;
        }
        
        web_dev_data.num_pages = num_pages;
        $('#web_number_of_pages').val(num_pages);
        $('#web_hidden_jobid').val(quote_id);
        
        $('#web-dev-prep .prep-init').slideUp();
        $('#web-dev-prep .web-dev-prep-data').html(page_html).slideDown();
        $('#web-dev-prep .web-dev-prep-submit').slideDown();
        
    }
    
}

function submitWebDevQuote() {
    
    var errors = [];
    var empty_page_names = true;
    
    if (web_dev_data.num_pages > 0) {
        $('.dynamic-page-name').each(function(){
            if ($.trim(this.value) != '') {
                empty_page_names = false;
            }
        });
    }
    
    if (empty_page_names) {
        errors.push('You should not leave all pages empty!');
    }
    
    if (web_dev_data.domain == 1 && $.trim($('.dynamic-domain-name').val()) == '') {
        errors.push('You should specify the domain name you want!');
    }
    
    if (errors.length > 0) {
        alert(errors.join('\n'));
        return false;
    } else {
        
        $.blockUI({
            message:'<h2>Preparing your quote...</h2>'
        });
        
        var fd = $('#web-dev-prep').serialize()+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        $.post(
            'welcome/ajax_webdev_quote',
            fd,
            function(data){
                if (typeof(data) == 'object') {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        populateQuote(quote_id);
                        $('#web-dev-prep').slideUp(400);
                        $('#item-submit, #change-quote-status').slideDown(400);
                    }
                } else {
                    alert('Error creating the records!');
                }
                $.unblockUI();
            },
            'json'
        );
        
    }
}


function CreateRealestateWebQuote(duplicate_id) {
    $.blockUI({
        message:'<h2>Preparing your quote...</h2>'
    });
	
    $.getJSON(
        'welcome/ajax_duplicate_quote/' + quote_id + '/' + duplicate_id,
        function(data){
            if (typeof(data) == 'object'){
                if (data.error) {
                    alert(data.error);
                } else {
                    populateQuote(quote_id);
                    $('#item-submit, #change-quote-status').slideDown();
                }
            } else {
                alert('Unexpected response from server!')
            }
            $.unblockUI();
        }
        
    );
}
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
                    <p><input type="text" name="job_title" id="job_title" class="textfield width300px" onkeyup="$('.q-quote-items .quote-title span').html(this.value);" /></p>
                    <!--p><label>Description</label></p>
                    <p><textarea name="job_desc" id="job_desc" class="textfield width300px height100px"></textarea></p-->
					<p><label>Lead Source</label></p>
                    
					<p><select name="lead_source" id="lead_source" class="textfield width300px">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($lead_source as $leads) {
							
							?>
                            <option value="<?php echo  $leads['lead_source_id'] ?>"><?php echo  $leads['lead_source_name'] ?></option>
                        <?php
							
						}
						?>
                        </select>
                    </p>
                    <p><label>Service Requirement</label></p>
                    
					<p><select name="job_category" id="job_category" class="textfield width300px" onchange="$('.q-service-type span').html(job_categories[$(this).val()]);">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($cfg['job_categories'] as $jck => $jcv) {
							if (! in_array($jck, $cfg['inactive_job_categories'])) {
							?>
                            <option value="<?php echo  $jck ?>"><?php echo  $jcv ?></option>
                        <?
							}
						}
						?>
                        </select>
                    </p>
					
					<p><label>Expected worth of Deal</label></p>
                    
					<p><select name="expect_worth" id="expect_worth" class="textfield width100px">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($expect_worth as $expect) {
							
							?>
                            <option value="<?php echo  $expect['expect_worth_id'] ?>"><?php echo  $expect['expect_worth_name'] ?></option>
                        <?php
							
						}
						?>
                        </select> <?php '/t';?><label> Amount</label> <input type="text" name="expect_worth_amount" id="expect_worth_amount" class="textfield" style=" width:140px" />
                    </p>
					
					
				
					<input name="job_belong_to" id="job_belong_to" type="hidden"  value="<?php echo $userdata['userid'] ?>" class="textfield width300px">
					
					<p><label>Division</label></p>
					<p>
						<select name="job_division" id="job_division" class="textfield width300px">
							<option value="not_select">Please Select</option>
                            <?php
							foreach ($cfg['sales_divisions'] as $sck => $scv)
							{
								?>
								<option value="<?php echo $sck ?>"><?php echo $scv ?></option>
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
							foreach ($lead_assign as $leada)
							{
								?>
								<option value="<?php echo $leada['userid'] ?>"><?php echo $leada['first_name'] ?></option>
								<?php
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
			
				<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
					
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
                    <p><label>Lead Title</label></p>
                    <p><input type="text" name="job_title" id="job_title_edit" class="textfield width300px" value="<?php echo  htmlentities($quote_data['job_title'], ENT_QUOTES) ?>" /></p>
                    <!--p><label>Description</label></p>
                    <p><textarea name="job_desc" id="job_desc_edit" class="textfield width300px height100px"><?php echo  htmlentities($quote_data['job_desc'], ENT_QUOTES) ?></textarea></p-->
					<p><label>Lead Source</label></p>
                    <p><select name="lead_source_edit" id="lead_source_edit" class="textfield width300px">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($lead_source_edit as $leadedit) {
							
							?>
                            <option value="<?php echo  $leadedit['lead_source_id'] ?>"<?php echo  ($quote_data['lead_source'] == $leadedit['lead_source_id']) ? ' selected="selected"' : '' ?>><?php echo  $leadedit['lead_source_name'] ?></option>
                        <?php
							
						}
						?>
                        </select>
                    </p>
                    <p><label>Service Requirement</label></p>
                    <p><select name="job_category" id="job_category_edit" class="textfield width300px">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($cfg['job_categories'] as $jck => $jcv) {
							if (! in_array($jck, $cfg['inactive_job_categories'])) {
							?>
                            <option value="<?php echo  $jck ?>"<?php echo  ($quote_data['job_category'] == $jck) ? ' selected="selected"' : '' ?>><?php echo  $jcv ?></option>
                        <?php
							}
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
							$amount = $actual_worth[0]['project_cost']; 
							echo $amount; ?>" id="actual_worth" class="textfield" style=" width:155px" /></p>
					<input type="hidden" name="expect_worth_amount_dup" value="<?php echo $quote_data['actual_worth_amount'];?>" id="expect_worth_amount_dup" class="textfield" style=" width:85px" />
					<!--<p><label>Lead Owner</label></p>
					<p>
						<input name="job_belong_to" id="job_belong_to_edit"  class="textfield width300px">
							
                    </p> -->
					<p><label>Division</label></p>
					<p>
						<select name="job_division" id="job_division_edit" class="textfield width300px">
							<option value="not_select">Please Select</option>
                            <?php
							
							foreach ($cfg['sales_divisions'] as $sck => $scv)
							{								
								?>
								<option value="<?php echo $sck ?>"<?php echo ($quote_data['division'] == $sck) ? ' selected="selected"' : '' ?>><?php echo $scv ?></option>
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
							
                        <?
							
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
							
                        <?
							
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
					
                    <input type="hidden" name="jobid_edit" id="jobid_edit" value="<?php echo  $quote_data['jobid'] ?>" />
                    <div style="width:170px;">
                    <div class="buttons clearfix pull-left">
                        <button type="submit" class="positive" onclick="editQuoteDetails(); return false;">Save</button>
                    </div>
					<?php if($quote_data['lead_status'] == 4) { ?>
						<div class="buttons clearfix pull-right">
							<button type="submit" class="positive" onclick="is_project(); return false;">Move To Project</button>
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
                <input type="hidden" name="jobid" id="hidden_jobid" />
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

            <form id="web-dev-prep" class="display-none">
                <h3>Web Development Quotation</h3>
                <table cellpadding="0" cellspacing="0" class="prep-init">
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>GUI design</td>
                        <td valign="middle">&nbsp; <input type="radio" name="prep_gui" value="1" /> Yes &nbsp; <input type="radio" name="prep_gui" value="0" />No</td>
                    </tr>
                    <tr>
                        <td>Number of web pages<br />(Including editable pages)</td>
                        <td><select name="prep_pages" id="web-dev-prep-pages" class="textfield width80px">
                            <?php
                            for ($i = 0; $i < 41; $i++)
                            {
                                echo "<option value=\"{$i}\">{$i}</option>\n";
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Does this quote require NewsletterPRO?</td>
                        <td valign="middle">&nbsp; <input type="radio" name="prep_np" value="1" /> Yes &nbsp; <input type="radio" name="prep_np" value="0" />No</td>
                    </tr>
                    <tr>
                        <td>Does this quote require V-Shop (e-commerce) ?</td>
                        <td valign="middle">&nbsp; <input type="radio" name="prep_vs" value="1" /> Yes &nbsp; <input type="radio" name="prep_vs" value="0" />No</td>
                    </tr>
                    <tr>
                        <td>Does this quote require domain registration?</td>
                        <td valign="middle">&nbsp; <input type="radio" name="prep_domain" value="1" /> Yes &nbsp; <input type="radio" name="prep_domain" value="0" />No</td>
                    </tr>
                    <tr>
                        <td>Does this quote require web hosting?</td>
                        <td valign="middle">&nbsp; <input type="radio" name="prep_hosting" value="1" /> Yes &nbsp; <input type="radio" name="prep_hosting" value="0" />No</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="buttons">
                                <button type="submit" class="" onclick="startWebDevQuote(); return false;">Next</button>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="web-dev-prep-data display-none">
                    
                </div>
                <input type="hidden" name="web_number_of_pages" id="web_number_of_pages" />
                <input type="hidden" name="jobid" id="web_hidden_jobid" />
                <div class="web-dev-prep-submit display-none">
                    <div class="buttons clearfix">
                        <button type="submit" class="positive" onclick="submitWebDevQuote(); return false;">Start</button>
                    </div>
                </div>
            </form>
			
			<?php
			/**
			 * This will include the select box that changes the status of a job
			 */
			if (isset($edit_quotation))
			include 'tpl/status_change_menu.php';
			?>
           
			
        </div>
        <div class="q-main-right">
			<?php
			if (isset($quote_data))
			{
				?>
			<div class="dev-notice">
				Please be careful when editing previous documents, the new textfield only allow 600 characters to be inserted. If you are editing items of a previous document, please note that you will only see 600 characters.
			</div>
				<?php
			}
			?>
            <div class="q-container">
                <div class="q-details">
					<div class="q-top-head">
						<div class="q-cust<?php if(isset($quote_data) && $quote_data['belong_to'] == 'SYNG') echo ' syng-gray' ?>">
							<h3 class="q-id"><em><?php //echo (isset($quote_data)) ? $cfg['job_status_label'][$quote_data['job_status']] : 'Draft' ?>Lead</em> &nbsp; <span>#<?php echo (isset($quote_data)) ? $quote_data['invoice_no'] : '' ?></span></h3>
							<?php
							$date_used = '';
							if (isset($quote_data))
							{
								$date_used = $quote_data['date_created'];
								if (in_array($quote_data['job_status'], array(4, 5, 6, 7, 8)) && $quote_data['date_invoiced'] != '')
								{
									$date_used = $quote_data['date_invoiced'];
								}
							}
								
							?>
							<p class="q-date"><em>Date</em> <span><?php echo  (isset($quote_data)) ? date('d-m-Y', strtotime($date_used)) : date('d-m-Y') ?></span></p>
							<p class="q-cust-company"><em>Company</em> <span><?php echo  (isset($quote_data)) ? $quote_data['company'] : '' ?></span></p>
							<p class="q-cust-name"><em>Contact</em> <span><?php echo  (isset($quote_data)) ? $quote_data['first_name'] . ' ' . $quote_data['last_name'] : '' ?></span></p>
							<p class="q-cust-email"><em>Email</em> <span><?php echo  (isset($quote_data)) ? $quote_data['email_1'] : '' ?></span></p>
							<p class="q-service-type"><em>Service</em> <span><?php echo  (isset($quote_data)) ? $cfg['job_categories'][$quote_data['job_category']] : '' ?></span></p>
						</div>
						<!-- end q-self -->
						<?php 
							if (getClientLogo()) {
							$cilentLogo = getClientLogo();
						?>
							<p><img src="assets/img/client_logo/<?php echo $cilentLogo['filename']; ?>" alt="client-logo" width="155" height="50"/></p>
						<?php 
							}
							else {
						?>
							<p><img src="" alt="" /></p>
						<?php
							}
						?>
					</div><!-- q-top-head -->
                    
                    <div class="q-quote-items">
						<h4 class="quote-title">Project Name : <span><?php echo (isset($quote_data)) ? $quote_data['job_title'] : '' ?></span></h4>
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
			$menu .= "<li><a href=\"#cat_{$cat['cat_id']}\">{$cat['cat_name']}</a></li>";
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
<script type="text/javascript">
$(function(){

	$('#drag-item-list').draggable({handle:$('.handle', $(this))});
    
	$('#drag-item-list .close').click(function(){
        $(this).parent().hide(400);
        $('#drag-item-list-opener').show();
    });
	
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
			{'jobid':curr_job_id, 'date_type':set_date_type, 'date':date_val, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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

$(function(){
	
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
			$('#multiple-client-emails').children('input[type=checkbox])').attr('checked', false).end()
				.slideUp(400);
		}
	});
	
	$('#attach_pdf').change(function(){
		if ($(this).is(':checked'))	{
			$('.download-invoice-option-log:not(:visible)').slideDown(400);
		} else {
			$('.download-invoice-option-log:visible').slideUp(400);
		}
	});
	
	
	$("#job-view-tabs").tabs({
								selected: 1,
								show: function (event, ui) {
									if (ui.index == 3)
									{
										loadExistingTasks();
									}
									else if (ui.index == 4)
									{
										populateJobOverview();
									}
									else if (ui.index == 9)
									{
										populatePackage();
									}
								}
							});
	
	$('#job-url-list li a:not(.file-delete)').livequery(function(){
		$(this).click(function(){
			window.open(this.href);
			return false;
		});
	});

	
	<?php
	if (is_numeric($quote_data['complete_status']))
	{
		echo "updateVisualStatus('" . (int) $quote_data['complete_status'] . "');\n";
	}
	?>
	
	$('#enable_post_profile').click(function(){
		if ($(this).is(':checked'))
		{
			$('.post-profile-select').show();
		}
		else
		{
			$('.post-profile-select').hide();
		}
	});
	
	$('.jump-to-job select').change(function(){
		var _new_location = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{jobid}}', $_SERVER['REQUEST_URI']) ?>';
		document.location = _new_location.replace('{{jobid}}', $(this).val());
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
					window.location.href = "welcome/edit_quote" + "/" + curr_job_id +"/";
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
		document.location.href = 'invoice/view_project/' + curr_job_id;
		$.unblockUI();
	});
}
</script>
<?php require (theme_url().'/tpl/footer.php'); ?>

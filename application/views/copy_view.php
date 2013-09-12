<?php require ('tpl/header.php'); ?>

<link rel="stylesheet" href="assets/css/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="assets/js/blockui.v2.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/vps.js?q=13"></script>
<script type="text/javascript">

var job_categories = [];
job_categories['not_select'] = '';
<?php foreach ($cfg['job_categories'] as $jck => $jcv) { ?>
job_categories[<?php echo  $jck ?>] = '<?php echo  $jcv ?>';
<?php } ?>

var item_inventory = [];
<?php foreach ($cfg['item_inventory'] as $iv_key => $iv_val) { ?>
item_inventory[<?php echo  $iv_key ?>] = ['<?php echo  str_replace("'", "\'", $iv_val['desc']) ?>', '<?php echo  str_replace("'", "\'", $iv_val['hours']) ?>'];
<?php } ?>

var hourly_rate = <?php echo  $cfg['hourly_rate'] ?>;
var quote_id = <?php echo  isset($quote_data['jobid']) ? $quote_data['jobid'] : 0 ?>;
var ex_cust_id = 0;
var item_sort_order = '';

// converting a lead to a quote 
var existing_lead = 0;
var existing_lead_service;

var nc_form_msg = '<div class="new-cust-form-loader">Loading Content.<br />';
nc_form_msg += '<img src="assets/img/indicator.gif" alt="wait" /><br /> Thank you for your patience!</div>';

$(document).ready(
	function(){
		$("#ex-cust-name").autocomplete("hosting/ajax_customer_search/", { minChars:2, width:'308px' }).result(function(event, data, formatted) {
			ex_cust_id = data[1];
            prepareQuoteForClient(ex_cust_id);
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
                        overlayCSS: {backgroundColor:'#000000', opacity: '0.9', cursor: 'wait'}
                    });
            $.get(
                'ajax/data_forms/new_customer_form',
                {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
                function(data){
                    $('.new-cust-form-loader').slideUp(500, function(){
                        $(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
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
        
        <?
		} // end edit mode
		?>
        <?php if (isset($quote_data) && (isset($edit_quotation) || isset($view_quotation))) { ?>
        
        populateQuote(<?php echo  $quote_data['jobid'] ?>);
        
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
    var form_data = $('#customer_detail_form').serialize();
    $.post(
        'customers/add_customer/false/false/true',
        form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
        function(res) {
            if (typeof (res) == 'object') {
                if (res.error == false) {
                    ex_cust_id = res.custid;
                    $("#ex-cust-name").val(res.cust_name);
                    $.unblockUI();
                    $('.notice').slideUp(400);
                    showMSG('New Customer Added!');
                    $('.q-cust-name span').html(res.cust_name);
                    $('.q-cust-email span').html(res.cust_email);
                } else {
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
            var n = Math.round((hourly_rate * parseFloat(num)) * 100) / 100;
            $('#item_price').val(n);
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
        var form_data = $('#quote-init-form').serialize()
		
        $.post('welcome/ajax_create_quote',form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',function (res) {
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
						
						$('#item-submit').append('<br /><br /><div class="action-buttons" style="clear:left; overflow:hidden; margin-top:20px;">' +
													'<div class="buttons">' +
													'<button type="button" class="positive" onclick="document.location.href = \'<?php echo $this->config->item('base_url') ?>welcome/view_quote/' + res.insert_id + '\'; return false;">View, Email or Add Logs to this Job</button>' +
													'</div>' +
												'</div>');
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
        var form_data = $('#item-submit').serialize();
        $.post(
           'welcome/ajax_add_item',
           form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
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
                        $('#keep_item').attr('checked', false);
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
    item_sort_order = $('#q-sort-items').sortable('serialize');
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
        {'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
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
	var form_data = $('#quote_item_edit_form').serialize();
    $.post(
        'welcome/ajax_edit_item',
        form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
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
    if (err.length > 0) {
        alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
        return false;
    } else {
        // block
        $('#content').block({
            message:'<h4>Processing</h4>'
        });
        // get form data
        var form_data = $('#quote-edit-form').serialize()
        // add cutomer id
        $.post(
           'welcome/ajax_edit_quote',
           form_data+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
           function (res) {
                if (typeof (res) == 'object') {
                    if (res.error == false) {
                        // good to go
                        //$('.q-id span').html(res.fancy_jobid);
                        $('.q-title').html(res.job_title);
                        //$('.q-desc').html(res.job_desc);
                        $('.q-service-type span').html(job_categories[res.job_category]);
                        showMSG('Details successfully updated!');
                    } else {
                        alert(res.errormsg);
                    }
                } else {
                    alert('Your session timedout!');
                }
            },
            "json"
        );
        $('#content').unblock();
    }
}

function selectItemSection() {
    var si = $('#item_section').val();
    $('#item_desc').val(item_inventory[si][0]);
    $('#hours').val(item_inventory[si][1]).focus().blur();
}


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
        
        var fd = $('#web-dev-prep').serialize();
        $.post(
            'welcome/ajax_webdev_quote',
            fd+'&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
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

function copyQuote()
{
	// add id to form
    $('#hidden_custid_fk').val(ex_cust_id);
	$('#quote-init-form').submit();
	//document.quote-init-form.Submit();
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
    <?php //include ('tpl/quotation_submenu.php') ?>
    <div class="inner">
	<?php  if (($this->session->userdata('add')==1 && $this->uri->segment(2) != 'edit_quote') || ($this->session->userdata('edit')==1) && ($this->uri->segment(2) == 'copy_quote' && is_numeric($this->uri->segment(3)))) { ?>
    	<div class="q-main-left">
            
            <form action="/welcome/copy_quote/<?php echo $qid; ?>/add/" method="post" id="quote-init-form" name="quote-init-form" 
			class="<?php echo  (isset($view_quotation) || isset($edit_quotation)) ? 'display-none' : '' ?>" >
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
                <input type="hidden" name="custid_fk" id="hidden_custid_fk" />
                <h2>Copy a quotation</h2>
             
                <div class="q-init-details">
                    <p><label>Start by typing in <strong>customer name</strong> or <strong>company name</strong>.</label></p>
                    <p><input type="text" name="ex_cust_name" id="ex-cust-name" class="textfield width300px" /></p>
                    <p class="notice width250px">If this is a new customer you need to add the<br /> customer
                    by <a href="#" class="modal-new-cust">completing their details</a>.</p>
                    <p><label>Quotation Title</label></p>
                    <p><input type="text" name="job_title" id="job_title" class="textfield width300px" onkeyup="$('.q-quote-items .quote-title span').html(this.value);" /></p>
                    <!--p><label>Description</label></p>
                    <p><textarea name="job_desc" id="job_desc" class="textfield width300px height100px"></textarea></p-->
                    <!--<p><label>Main Category</label></p>
                    
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
                    </p> -->
					
					<!--<p><label>Serviced By</label></p>
					<p>
						<select name="job_belong_to" id="job_belong_to" class="textfield width300px">
                            <?php
							foreach ($cfg['sales_codes'] as $sck => $scv)
							{
								if (($userdata['level'] == 4 && $userdata['sales_code'] == $sck) || $userdata['level'] != 4)
								{
								?>
								<option value="<?php echo $sck ?>"><?php echo $scv ?></option>
								<?php
								}
							}
							?>
                        </select>
                    </p>
					<p><label>Division</label></p>
					<p>
						<select name="job_division" id="job_division" class="textfield width300px">
                            <?php
							foreach ($cfg['sales_divisions'] as $sck => $scv)
							{
								?>
								<option value="<?php echo $sck ?>"><?php echo $scv ?></option>
								<?php
							}
							?>
                        </select>
                    </p> -->
                    <div class="buttons">
                        <button type="submit" class="positive" onclick="copyQuote();">Copy</button>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </form>
            
            <?php if (isset($edit_quotation) && isset($quote_data)) { ?>
            <form action="" method="post" id="quote-edit-form" onsubmit="return false;">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
                <h2>Edit Title and Description</h2>
                <div class="q-init-details">
                    <p><label>Quotation Title</label></p>
                    <p><input type="text" name="job_title" id="job_title_edit" class="textfield width300px" value="<?php echo  htmlentities($quote_data['job_title'], ENT_QUOTES) ?>" /></p>
                    <!--p><label>Description</label></p>
                    <p><textarea name="job_desc" id="job_desc_edit" class="textfield width300px height100px"><?php echo  htmlentities($quote_data['job_desc'], ENT_QUOTES) ?></textarea></p-->
                    <p><label>Main Category</label></p>
                    <p><select name="job_category" id="job_category_edit" class="textfield width300px">
                            <option value="not_select">Please Select</option>
                        <?php foreach ($cfg['job_categories'] as $jck => $jcv) {
							if (! in_array($jck, $cfg['inactive_job_categories'])) {
							?>
                            <option value="<?php echo  $jck ?>"<?php echo  ($quote_data['job_category'] == $jck) ? ' selected="selected"' : '' ?>><?php echo  $jcv ?></option>
                        <?
							}
						}
						?>
                        </select>
                    </p>
					<p><label>Serviced By</label></p>
					<p>
						<select name="job_belong_to" id="job_belong_to_edit" class="textfield width300px">
							<?php
							foreach ($cfg['sales_codes'] as $sck => $scv)
							{
								if (($userdata['level'] == 4 && $userdata['sales_code'] == $sck) || $userdata['level'] != 4)
								{
								?>
								<option value="<?php echo $sck ?>"<?php echo ($quote_data['belong_to'] == $sck) ? ' selected="selected"' : '' ?>><?php echo $scv ?></option>
								<?php
								}
							}
							?>
                        </select>
                    </p>
					<p><label>Division</label></p>
					<p>
						<select name="job_division" id="job_division_edit" class="textfield width300px">
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
                    <input type="hidden" name="jobid_edit" id="jobid_edit" value="<?php echo  $quote_data['jobid'] ?>" />
                    <div class="buttons">
                        <button type="submit" class="positive" onclick="editQuoteDetails(); return false;">Save</button>
                    </div>
                    <div class="buttons">
                        <button type="submit" onclick="$('#quote-edit-form').slideUp(400); return false;">Done</button>
                    </div>
                    <p style="clear:left;">&nbsp;</p>
                </div>
            </form>
            <?php } ?>
            
            <form id="item-submit">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
                <input type="hidden" name="jobid" id="hidden_jobid" />
                <h3>Add items to quotation</h3>
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
                            <td>
                                <p><label>Add to additional items?</label></p>
                                <p><input type="checkbox" name="keep_item" id="keep_item" /></p>
                            </td>
                        </tr>
                    </table>
                    <div class="buttons">
                        <button type="submit" class="positive" onclick="addItem();  return false;">Add Item</button>
                    </div><br />
                    <p>&nbsp;</p>
                    <div class="buttons" id="drag-item-list-opener">
                        <button type="submit" class="" onclick="openDragItems();  return false;">Additional Features</button>
                    </div>
                </div>
                
            </form>

            <form id="web-dev-prep" class="display-none">
			
				<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			
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
                    <div class="buttons">
                        <button type="submit" class="positive" onclick="submitWebDevQuote(); return false;">Start</button>
                    </div>
                </div>
            </form>
			
			<?php
			/**
			 * This will include the select box that changes the status of a job
			 */
			include 'tpl/status_change_menu.php';
			?>
            
			<div class="action-buttons" style="overflow:hidden; margin-top:20px;">
				<?php
				if (isset($quote_data) && isset($userdata) && in_array($userdata['level'], array(0,1,2,4)))
				{
					?>
				<div class="buttons">
						<button type="submit" class="positive" onclick="document.location.href = '<?php echo $this->config->item('base_url') ?>welcome/view_quote/<?php echo $quote_data['jobid'] ?>'">View, Email or Add Logs to this Job</button>
					</div>
					<?php
				}
				?>
			</div>
			
        </div>
		<!-- q main right removal  --->
       <!-- end -->
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
						<span class="desc"><?php echo  nl2br($record['item_desc']) ?></span><br />
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
function openDragItems() {
    var topv = parseInt($(window).scrollTop()) + 40;
    var leftv = $('#content').offset().left + 20;
    $('#drag-item-list').show(700).animate({top:topv+'px',left:leftv+'px'}, 1000);
    $('#drag-item-list-opener').hide();
    
}
</script>
<?php require ('tpl/footer.php'); ?>

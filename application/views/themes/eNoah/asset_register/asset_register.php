<?php require (theme_url() . '/tpl/header.php'); ?>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<style>
    .ui-autocomplete { max-height:200px; overflow-y:auto; overflow-x: hidden; }
    #ui-datepicker-div {z-index: 999 !important;}
</style>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<input type="hidden" class="hiddenUrl"/>
<script type="text/javascript">
<?php
$userdata = $this->session->userdata('logged_in_user');
//print_r($userdata['userid']);exit;
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

    $(document).ready(function () {
        $("#ex-cust-name").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.ajax({
                    url: "hosting/ajax_customer_search",
                    data: {'cust_name': $("#ex-cust-name").val(), '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                    type: "POST",
                    dataType: 'json',
                    async: false,
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                // $('#cust_id').val(ui.item.id);
                ex_cust_id = ui.item.id;
                regId = ui.item.regId;
                cntryId = ui.item.cntryId;
                stId = ui.item.stId;
                locId = ui.item.locId;
                prepareQuoteForClient(ex_cust_id);
                getUserForLeadAssign(regId, cntryId, stId, locId);
            }
        });

        $("#customer_company_name").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.ajax({
                    url: "hosting/ajax_customer_search",
                    data: {'cust_name': $("#customer_company_name").val(), '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                    type: "POST",
                    dataType: 'json',
                    async: false,
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $('#customer_id').val(ui.item.id);
                // ex_cust_id = ui.item.id;
                // regId = ui.item.regId;
                // cntryId = ui.item.cntryId;
                // stId = ui.item.stId;
                // locId = ui.item.locId;
                // prepareQuoteForClient(ex_cust_id);
                // getUserForLeadAssign(regId,cntryId,stId,locId);
            }
        });

<?php
if (isset($existing_lead) && isset($lead_customer)) {
    echo 'ex_cust_id = ', $lead_customer, ";\n";
    echo 'existing_lead = ', $existing_lead, ";\n";
    echo 'existing_lead_service = "', $existing_lead_service, '";', "\n";
    echo "prepareQuoteForClient(ex_cust_id);\n";
}
?>

        $('.modal-new-cust').click(function () {
            $.blockUI({
                message: nc_form_msg,
                css: {width: '820px', marginLeft: '50%', left: '-345px', padding: '20px 0 20px 20px', top: '10%', border: 'none', cursor: 'default', position: 'absolute'},
                overlayCSS: {backgroundColor: '#EAEAEA', opacity: '0.9', cursor: 'wait'}
            });
            $.get(
                    'ajax/data_forms/new_customer_form',
                    {},
                    function (data) {
                        $('.new-cust-form-loader').slideUp(500, function () {
                            $(this).parent().css({backgroundColor: '#fff', color: '#333'});
                            $(this).css('text-align', 'left').html(data).slideDown(500, function () {
                                $('.error-cont').css({margin: '10px 25px 10px 0', padding: '10px 10px 0 10px', backgroundColor: '#CEB1B0'});
                            });
                        })
                    }
            );
            return false;
        });

        $('.q-item-details div').css('display', 'none')
                .siblings('a:first').next().show().end()
                .parent().children('a').click(function () {
            $(this).next().slideToggle(500);
            return false;
        });

<?php
/*
 * This is applicable when not viewing a quote
 * on edit or create mode
 */
if (!isset($view_quotation)) {
    ?>

            $('#q-sort-items').sortable({axis: 'y', cursor: 'move', update: prepareSortedItems});

            $('#q-sort-items li').livequery(function () {
                // use the helper function hover to bind a mouseover and mouseout event 
                $(this)
                        .hover(function () {
                            quoteItemOver($(this));
                        }, function () {
                            quoteItemOut($(this));
                        });
            }, function () {
                // unbind the mouseover and mouseout events 
                $(this)
                        .unbind('mouseover')
                        .unbind('mouseout');
            });

            $('#q-sort-items li .ip-delete').livequery(function () {
                $(this).click(function () {
                    quoteItemDelete($(this));
                });
            });
            $('#q-sort-items li .ip-edit').livequery(function () {
                $(this).click(function () {
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

    function getUserForLeadAssign(regId, cntryId, stId, locId) {

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
            url: baseurl + 'user/getRestrictedUsers/',
            cache: false,
            data: {user: user, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
            success: function (response) {
                //alert(response);
                if (response != '') {
                    $("#lead_assign").html(response);
                    $("#lead_assign").trigger("liszt:updated");
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
        var form_data = $('#customer_detail_form').serialize() + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';

        $('.blockUI .layout').block({
            message: '<h3>Processing</h3>',
            css: {background: '#666', border: '2px solid #999', padding: '8px', color: '#333'}
        });

        $.post(
                'customers/add_customer/false/false/true',
                form_data,
                function (res) {
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
                            getUserForLeadAssign(res.cust_reg, res.cust_cntry, res.cust_ste, res.cust_locn);
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
       // alert('hi');return false;
        var err = [];
        
        if ($.trim($('#department').val()) == '') {
            err.push('department is required');
        }
        if ($('#Project').val() == 'not_select') {
            err.push('Project must be selected');
        }
        if ($('#asset_type').val() == 'not_select') {
            err.push('Asset type must be selected');
        }
//        if ($('#storage_mode').val() == 'not_select') {
//            err.push('Storage mode must be selected');
//        }
        if ($('#confidentiality').val() == 'not_select') {
            err.push('Confidentiality must be selected');
        }
        if ($('#Availability').val() == 'not_select') {
            err.push('Availability must be selected');
        }
        if ($('#asset_name').val() == '') {
            err.push('asset_name is required');
        }
//         if ($('#Integrity').val() == 'not_select') {
//            err.push('Integrity must be selected');
//        }
//        if ($.trim($('#location').val()) == '') {
//            err.push('location is required');
//        }
//        if ($.trim($('#labelling').val()) == '') {
//            err.push('labelling is required');
//        }
//        if ($.trim($('#backupLocation').val()) == '') {
//            err.push('backupLocation is required');
//        }
//        if ($.trim($('#archivalLocation').val()) == '') {
//            err.push('archivalLocation is required');
//        }
        
//     
        
        if (err.length > 0) {
            // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));
            $.blockUI({
                message: '<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">' + err.join('<br />') + '</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
                css: {width: '440px'}
            });
            return false;
        } else {
            // block
            $('#content').block({
                message: '<h4>Processing...</h4>'
            });
            // add id to form
         //   $('#hidden_custid_fk').val(ex_cust_id);
            // get form data
            var form_data = $('#quote-init-form').serialize() + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
           // alert(form_data);return false;
            $.post('asset_register/ajax_create_quote', form_data, function (res) {
              // alert(res);return false;
               console.log(res);
                if (typeof (res) == 'object') {
                    if (res.error == true) {
                        // good to go
                        window.location = '<?php echo $this->config->item('base_url') ?>asset_register/quotation/';
                    } else {
                        // alert(res.errormsg);
                         alert('Asset already registered!');                    
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
                message: '<h4>Processing</h4>'
            });
            // add lead_id to form
            $('#hidden_jobid').val(quote_id);
            // get form data
            var form_data = $('#item-submit').serialize() + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
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
                                scrollElem('.q-container', '#q-sort-items li#qi-' + res.itemid);
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
        var item_sort_order = $('#q-sort-items').sortable('serialize') + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';

        $('.q-main-right').block({message: '<h5>Processing</h5>'});

        $.post(
                'welcome/ajax_save_item_order',
                item_sort_order,
                function (data) {
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


    function quoteItemOver(obj) {
        obj.append('<a class="ip-edit">edit</a><a class="ip-delete">delete</a>');
    }

    function quoteItemOut(obj) {
        $('.ip-edit, .ip-delete', obj).remove();
    }

    var qe_item = '<div class="q-modal-item-edit">Loading Content.<br />';
    qe_item += '<img src="assets/img/indicator.gif" alt="wait" /></div>';

    function quoteItemEdit(obj) {
        var itemid = obj.parent().attr('id').replace(/^qi\-/, '');
        $.blockUI({
            message: qe_item,
            css: {width: '500px', marginLeft: '50%', left: '-250px', padding: '20px 0 20px 20px', top: '25%', border: 'none', cursor: 'default'},
            overlayCSS: {backgroundColor: '#000000', opacity: '0.9', cursor: 'wait'}
        });
        $.get(
                'ajax/data_forms/quote_item_form/' + itemid,
                {},
                function (data) {
                    $('.q-modal-item-edit').slideUp(500, function () {
                        $(this).parent().css({backgroundColor: '#535353', color: '#cccccc'});
                        $(this).css('text-align', 'left').html(data).slideDown(500, function () {
                            $('.error-cont').css({margin: '10px 25px 10px 0', padding: '10px 10px 0 10px', backgroundColor: '#CEB1B0'});
                            $('#quote_item_edit_form textarea').keyup();
                        });
                    })
                }
        );
        return false;
    }

    function processItemEdit() {
        $('.q-modal-item-edit').parent().block({message: '<p>Processing...</p>'});
        var form_data = $('#quote_item_edit_form').serialize() + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        $.post(
                'welcome/ajax_edit_item',
                form_data,
                function (data) {
                    if (typeof (data) == 'object') {
                        if (!data.error) {
                            $('.q-modal-item-edit').unblock();
                            populateQuote(quote_id);
                            cancelDelEdit();
                        } else if (data.error = 'undefined') {
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
            message: '<br /><h5>Are you sure?</h5><div class="modal-confirmation overflow-hidden"><div class="buttons"><button type="submit" class="positive" onclick="processItemDelete(' + itemid + '); return false;">Yes</button></div><div class="buttons"><button type="submit" class="negative" onclick="cancelDelEdit(); return false;">No</button></div></div>',
            css: {width: '440px'}
        });
    }

    function processItemDelete(itemid) {
        var params = {'itemid': itemid, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'};
        $.post(
                'welcome/ajax_delete_item',
                params,
                function (res) {
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
        if ($('#lead_assign_edit').val() == null) {
            err.push('Lead Assigned to must be selected');
        }
        if ($('#job_division_edit').val() == 'not_select') {
            err.push('Entity must be selected');
        }
        if ($('#industry_edit').val() == '') {
            err.push('Industry must be selected');
        }
        if ($('#expect_worth_edit').val() == 'not_select') {
            err.push('Expected Worth Currency must be selected');
        }

        var act_worth = $.trim($('#actual_worth').val());

        if ((act_worth == 0.00) || (act_worth == '') || (act_worth == 0.0)) {
            act_worth = 0;
        }

        if (($.trim($('#lead_status').val()) == 4) && (act_worth <= 0)) {
            err.push('Actual worth amount must not be empty and it must be greater than zero');
        }
        if (($.trim($('#lead_stage').val()) == '')) {
            err.push('Lead Stage must be selected');
        }

        if (err.length > 0) {
            // alert('Few errors occured! Please correct them and submit again!\n\n' + err.join('\n'));

            $.blockUI({
                message: '<br /><h5>Few errors occured! Please correct them and submit again!\n\n</h5><div class="modal-errmsg overflow-hidden"><div class="buttons">' + err.join('<br />') + '</div><div class="buttons pull-right"><button type="submit" class="positive" onclick="cancelDel(); return false;">Ok</button></div></div>',
                css: {width: '440px'}
            });

            return false;
        } else {
            // block
            $('#content').block({
                message: '<h4>Processing</h4>'
            });
            // get form data
            var form_data = $('#quote-edit-form').serialize() + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
            // add cutomer id
            var csrf_token = '<?php echo $this->security->get_csrf_token_name() ?>';
            var csrf_hasf = '<?php echo $this->security->get_csrf_hash() ?>';
            $.post(
                    'welcome/ajax_edit_quote',
                    form_data,
                    function (res) {
                        if (typeof (res) == 'object') {
                            if (res.error == false) {
                                var params = {str: 'Details Successfully Updated!'};

                                params[csrf_token_name] = csrf_hash_token;

                                $.post("ajax/request/set_flash_data", params,
                                        function (info) {
                                            // good to go
                                            if (arg == 'view') {
                                                window.location.href = site_base_url + "welcome/view_quote/" + curr_job_id;
                                            } else {
                                                document.location.href = site_base_url + 'welcome/edit_quote/' + curr_job_id;
                                                $('.q-title').html(res.lead_title);
                                                $('.q-service-type span').html(lead_services[res.lead_service]);
                                            }
                                        }
                                );

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
    function ajxCty() {
        $("#addcountry").slideToggle("slow");
    }

    function ajxSaveCty() {
        $(document).ready(function () {
            if ($('#newcountry').val() == "") {
                alert("Country Required.");
            } else {
                var regionId = $("#add1_region").val();
                var newCty = $('#newcountry').val();
                getCty(newCty, regionId);
            }

            function getCty(newCty, regionId) {
                var baseurl = $('.hiddenUrl').val();
                $.ajax({
                    url: baseurl + 'customers/getCtyRes/' + newCty + "/" + regionId,
                    cache: false,
                    success: function (response) {
                        if (response == 'userOk')
                        {
                            $.post("regionsettings/country_add_ajax", {regionid: $("#add1_region").val(), country_name: $("#newcountry").val(), created_by: (<?php echo $usernme['userid'] ?>), '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                                    function (info) {
                                        $("#country_row").html(info);
                                    });
                            $("#addcountry").hide();

                            //var regId = $("#add1_region").val();
                            $("#state_row").load("regionsettings/getState");
                        } else
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
        $(document).ready(function () {
            /*if( $('#newstate').val().length > 2 )
             {
             var newSte = $('#newstate').val();
             getSte(newSte);
             }
             return false;
             */
            if ($('#newstate').val() == "") {
                alert("State Required.");
            } else {
                var cntyId = $("#add1_country").val()
                var newSte = $('#newstate').val();
                getSte(newSte, cntyId);

            }

            function getSte(newSte, cntyId) {
                var baseurl = $('.hiddenUrl').val();
                $.ajax({
                    url: baseurl + 'customers/getSteRes/' + newSte + "/" + cntyId,
                    cache: false,
                    success: function (response) {
                        if (response == 'userOk')
                        {
                            $.post("regionsettings/state_add_ajax", {countryid: $("#add1_country").val(), state_name: $("#newstate").val(), created_by: (<?php echo $usernme['userid'] ?>), '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                                    function (info) {
                                        $("#state_row").html(info);
                                    });
                            $("#addstate").hide();

                            $("#location_row").load("regionsettings/getLocation");
                        } else
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
        $(document).ready(function () {
            /*if ($('#newlocation').val().length > 2)
             {
             var newLoc = $('#newlocation').val();
             getLoc(newLoc);
             }
             return false;
             */
            if ($('#newlocation').val() == "") {
                alert("Location Required.");
            } else {
                var stId = $("#add1_state").val();
                var newLoc = $('#newlocation').val();
                getLoc(newLoc, stId);
            }
            function getLoc(newLoc, stId) {
                var baseurl = $('.hiddenUrl').val();
                $.ajax({
                    url: baseurl + 'customers/getLocRes/' + newLoc + '/' + stId,
                    cache: false,
                    success: function (response) {
                        if (response == 'userOk')
                        {
                            $.post("regionsettings/location_add_ajax", {stateid: $("#add1_state").val(), location_name: $("#newlocation").val(), created_by: (<?php echo $usernme['userid'] ?>), '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                                    function (info) {
                                        $("#location_row").html(info);
                                    });
                            $("#addstate").hide();
                            //var steId = $("#add1_state").val();
                            //$("#location_row").load("regionsettings/getLocation/" +steId);
                        } else
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
    <?php //echo '<pre>'; print_r($quote_data); echo '</pre>';  ?>
    <div class="inner">
        <?php if (($this->session->userdata('add') == 1 && $this->uri->segment(2) != 'edit_quote') || ($this->session->userdata('edit') == 1) && ($this->uri->segment(2) == 'edit_quote' && is_numeric($this->uri->segment(3)))) { ?>
            <div class="q-main-left">
                <form action="" method="post" id="quote-init-form" class="<?php echo (isset($view_quotation) || isset($edit_quotation)) ? 'display-none' : '' ?>" onsubmit="return false;">

                    <input type="hidden" name="username" id="username" value= "<?php echo $userdata['userid'] ?>" />
                    <h2>Create a Asset</h2>

                    <div>
                        <p><label>Department</label></p>
                        <p><select name="department" id="department" class="textfield width300px" ;">
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
                        <p><label>Project</label></p>
                        <p><select name="Project" id="Project" class="textfield width300px";">
                                <option value="not_select">Please Select</option>
                                <?php
                                foreach ($project_listing_ls as $job) {
                                    ?>
                                    <option value="<?php echo $job['lead_id'] ?>"><?php echo $job['lead_title'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </p>
                        <p><label>Asset Type</label></p>
                        <p>
                            <select name="asset_type" id="asset_type" class="textfield width300px">
                                <option value="not_select">Please Select</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Information">Information</option>
                            </select>
                        </p>
                        <p><label>Storage Mode</label></p>
                        <p>
                            <select name="storage_mode" id="storage_mode" class="textfield width300px">
                                <option value="not_select">Please Select</option>
                                <option value="Hardcopy">Hardcopy</option>
                                <option value="Softcopy">Softcopy</option>
                            </select>
                        </p>
                        <p><label>Confidentiality</label></p>
                        <p>
                            <select name="confidentiality" id="confidentiality" class="textfield width300px">
                                <option value="not_select">Please Select</option>
                                <option value="Highly confidential">Highly confidential</option>
                                <option value="Confidential">Confidential</option>
                                <option value="Internal">Internal</option>
                                <option value="Public">Public</option>
                            </select>
                        </p>
                        <p><label>Integrity</label></p>
                        <p>
                            <select name="integrity" id="integrity" class="textfield width300px">
                                <option value="not_select">Please Select</option>
                                <option value="High">High</option>
                            </select>
                        </p>
                        <p><label>Availability</label></p>
                        <p>
                            <select name="availability" id="availability" class="textfield width300px">
                                <option value="not_select">Please Select</option>
                                <option value="Low">Low</option>
                                <option value="High">High</option>
                                <option value="Severe">Severe</option>
                            </select>
                        </p>
                        <p><label>Asset Name</label></p>
                        <p><input type="text" name="asset_name" id="asset_name" class="textfield width300px" /></p>
                        <p><label>Location</label></p>
                        <p><textarea name="location" id="location" class="textfield width300px"></textarea></p>
                        <p><label>Labelling</label></p>
                        <p><input type="text" name="labelling" id="labelling" class="textfield width300px" /></p>
                        <p><label>Select Location</label></p>
                        <p><select name="location" id="location" class="textfield width300px";">
                                <option value="not_select">Please Select</option>
                                <?php
                                foreach ($location as $loc) {
                                    ?>
                                    <option value="<?php echo $loc['loc_id'] ?>"><?php echo $loc['asset_location'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </p>
                       
                        
                        <div class="buttons clearfix">
                            <button type="submit" class="positive" onclick="startQuote(); return false;">save asset</button>
                        </div>
                        <p>&nbsp;</p>
                    </div>
                </form>

                <?php if (isset($edit_quotation) && isset($quote_data)) { ?>
                    <h2> Edit Lead  
                        <div style="overflow:hidden; padding-bottom:10px;" class="buttons pull-right">
                            <button onclick="document.location.href = '<?php echo base_url(); ?>welcome/view_quote/<?php echo $quote_data['lead_id']; ?>'" class="positive" type="submit">Back to View</button>
                        </div>

                    </h2>

                    <form name="lead_dates" id="lead-change-date" style="padding:0 5px 0 0; margin:0 !important;">
                        <table>
                            <tr>
                                <td valign="top" width="300">
                                    <h6 class="lead-created-label">Lead Creation Date &raquo;<span><?php if ($quote_data['date_created'] != '')
                echo date('d-m-Y', strtotime($quote_data['date_created']));
            else
                echo 'Not Set';
            ?></span></h6>
                                    <p><a href="#" onclick="$('.lead-created-change:hidden').show(200);
                                            return false;">Change?</a></p>

                                    <div class="lead-created-change">
                                        <input type="text" value="" class="textfield pick-date" name="lead_creation_date" id="lead_creation_date" />
                                        <div class="buttons clearfix">
                                            <button type="submit" class="positive" onclick="setLeadCreationDate();
                                                    return false;">Set</button>
                                            <button type="submit" onclick="$('.lead-created-change:visible').hide(200);
                                                    return false;">Cancel</button>
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
                                    <h6 class="project-startdate-label">Proposal Expected Date &raquo;<span><?php if ($quote_data['proposal_expected_date'] != '')
                echo date('d-m-Y', strtotime($quote_data['proposal_expected_date']));
            else
                echo 'Not Set';
            ?></span></h6>
                                    <p><a href="#" onclick="$('.project-startdate-change:hidden').show(200);
                                            return false;">Change?</a></p>

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
                            <p><label>Customer</label></p>
                            <p><input type="text" name="customer_company_name" id="customer_company_name" class="textfield width300px" value="<?php echo $quote_data['company'] . ' - ' . $quote_data['customer_name'] ?>" /></p>
                            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $quote_data['custid_fk'] ?>" />
                            <input type="hidden" name="customer_id_old" id="customer_id_old" value="<?php echo $quote_data['custid_fk'] ?>" />
                            <input type="hidden" name="customer_company_name_old" id="customer_company_name_old" class="textfield width300px" value="<?php echo $quote_data['company'] . ' - ' . $quote_data['customer_name'] ?>" />
                            <p><label>Lead No</label></p>
                            <p><input type="text" name="lead_no" id="lead_no" class="textfield width300px" value="<?php echo $quote_data['invoice_no'] ?>" disabled /></p>
                            <p><label>Lead Title</label></p>
                            <p><input type="text" name="lead_title" id="job_title_edit" class="textfield width300px" value="<?php echo htmlentities($quote_data['lead_title'], ENT_QUOTES) ?>" /></p>
                            <p><label>Lead Source</label></p>
                            <p><select name="lead_source_edit" id="lead_source_edit" class="textfield width300px">
                                    <option value="not_select">Please Select</option>
        <?php
        foreach ($lead_source_edit as $leadedit) {
            ?>
                                        <option value="<?php echo $leadedit['lead_source_id'] ?>"<?php echo ($quote_data['lead_source'] == $leadedit['lead_source_id']) ? ' selected="selected"' : '' ?>><?php echo $leadedit['lead_source_name'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </p>
                            <p><label>Service Requirement</label></p>
                            <p><select name="lead_service" id="job_category_edit" class="textfield width300px">
                                    <option value="not_select">Please Select</option>
        <?php
        foreach ($job_cate as $job) {
            ?>
                                        <option value="<?php echo $job['sid'] ?>"<?php echo ($quote_data['lead_service'] == $job['sid']) ? ' selected="selected"' : '' ?>><?php echo $job['services'] ?></option>
            <?php
        }
        ?>
                                </select>
                            </p>
                            <p><label>Industry</label></p>
                            <p>
                                <select name="industry" id="industry_edit" class="textfield width300px" >
                                    <option value="">Please Select</option>
        <?php
        foreach ($industry as $ind) {
            ?>
                                        <option value="<?php echo $ind['id'] ?>"<?php echo ($quote_data['industry'] == $ind['id']) ? ' selected="selected"' : '' ?>><?php echo $ind['industry'] ?></option>
            <?php
        }
        ?>
                                </select>
                            </p>
                            <p><label>Entity</label></p>
                            <p>
                                <!--select name="job_division" id="job_division_edit" class="textfield width300px" onchange="getBaseCurrencyEdit(this.value);"-->
                                <select name="job_division" id="job_division_edit" class="textfield width300px">
                                    <option value="not_select">Please Select</option>
        <?php
        foreach ($sales_divisions as $sa_div) {
            ?>
                                        <option value="<?php echo $sa_div['div_id'] ?>"<?php echo ($quote_data['division'] == $sa_div['div_id']) ? ' selected="selected"' : '' ?>><?php echo $sa_div['division_name'] ?></option>
            <?php
        }
        ?>
                                </select>
                            </p>
                            <p><label>Expected worth of Deal</label></p>
                            <!--input name="expect_worth_edit_readonly" id="expect_worth_edit_readonly" type="hidden" value="<?php #echo $quote_data['expect_worth_id']; ?>" class="textfield width300px"-->
                            <p><select name="expect_worth_edit" id="expect_worth_edit" class="textfield" style="width:100px">
                                    <option value="not_select">Please Select</option>
        <?php foreach ($expect_worth as $worth) { ?>
                                        <option value="<?php echo $worth['expect_worth_id'] ?>"<?php echo ($quote_data['expect_worth_id'] == $worth['expect_worth_id']) ? ' selected="selected"' : '' ?>><?php echo $worth['expect_worth_name'] ?></option>
            <?php
        }
        ?>
                                </select><?php echo'&nbsp;&nbsp;&nbsp;' ?>
                                <label> Amount</label> <?php echo'&nbsp;&nbsp;&nbsp;' ?><input type="text" name="expect_worth_amount" value="<?php echo $quote_data['expect_worth_amount']; ?>" id="expect_worth_amount" class="textfield" style=" width:132px" />
                                <input type="hidden" name="hidden_expect_worth_amount" value="<?php echo $quote_data['expect_worth_amount']; ?>" id="hidden_expect_worth_amount" />
                            </p>

                            <p><label>Actual worth of Project</label><?php echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ?><input type="text" name="actual_worth" value="<?php
                        if ($quote_data['actual_worth_amount'] != '0.00')
                            $amount = $quote_data['actual_worth_amount'];
                        else
                            $amount = $quote_data['actual_worth_amount'];
                        echo $amount;
                        ?>" id="actual_worth" class="textfield" style=" width:155px" /></p>
                            <input type="hidden" name="expect_worth_amount_dup" value="<?php echo $quote_data['actual_worth_amount']; ?>" id="expect_worth_amount_dup" class="textfield" style=" width:85px" />
                            <!--<p><label>Lead Owner</label></p>
                            <p>
                                    <input name="job_belong_to" id="job_belong_to_edit"  class="textfield width300px">
                                            
                            </p> -->
                            <!-- lead owner edit owner starts -->
                            <p><label>Lead Owner </label></p>
                            <p>
        <?php if (($quote_data['belong_to'] == $userdata['userid']) || ($userdata['role_id'] == 1 || $userdata['role_id'] == 2)) { ?>
                                    <select name="lead_owner_edit" id="lead_owner_edit" class="textfield width300px">
            <?php foreach ($lead_assign_edit as $leadassignedit) { ?>
                                            <option value="<?php echo $leadassignedit['userid'] ?>"<?php echo ($quote_data['belong_to'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] . " " . $leadassignedit['last_name'] ?></option>
            <?php } ?>
                                    </select>
                                    <script>
                                        $('#lead_owner_edit').change(function () {
                                            var assign_mail = $('#lead_owner_edit').val();
                                            //alert(assign_mail);
                                            $('#lead_owner_edit_hidden').val(assign_mail);
                                        });
                                    </script>
        <?php } else { ?>
                                    <select name="lead_owner_edit" id="lead_owner_edit" class="textfield width300px" disabled=true>
            <?php foreach ($lead_assign_edit as $leadassignedit) { ?>
                                            <option value="<?php echo $leadassignedit['userid'] ?>"<?php echo ($quote_data['belong_to'] == $leadassignedit['userid']) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] ?></option>
            <?php } ?>
                                    </select>
                                    <script>
                                        $(document).ready(function () {
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
                                    <?php
                                    $lead_assign_arr = array(0);
                                    $lead_assign_arr = @explode(',', $quote_data['lead_assign']);
                                    ?>
                            <p>
        <?php if ($quote_data['belong_to'] == $userdata['userid'] || $userdata['role_id'] == 1) { ?>
                                    <select data-placeholder="Choose Assignee..." name="lead_assign_edit[]" multiple id="lead_assign_edit" class="chzn-select width300px">
            <?php foreach ($lead_assign_edit as $leadassignedit) { ?>
                                            <option value="<?php echo $leadassignedit['userid'] ?>"<?php echo (in_array($leadassignedit['userid'], $lead_assign_arr) ) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] . " " . $leadassignedit['last_name'] . " - " . $leadassignedit['emp_id']; ?></option>
            <?php } ?>
                                    </select>
                                    <script>
                                        $('#lead_assign_edit').change(function () {
                                            var assign_mail = $('#lead_assign_edit').val();
                                            //alert(assign_mail);
                                            $('#lead_assign_edit_hidden').val(assign_mail);
                                        });
                                    </script>
        <?php } else { ?>

                                    <select data-placeholder="Choose Assignee..." name="lead_assign_edit[]" multiple id="lead_assign_edit" disabled=true class="chzn-select width300px">
            <?php foreach ($lead_assign_edit as $leadassignedit) { ?>
                                            <option value="<?php echo $leadassignedit['userid'] ?>"<?php echo (in_array($leadassignedit['userid'], $lead_assign_arr) ) ? ' selected="selected"' : '' ?>><?php echo $leadassignedit['first_name'] . " " . $leadassignedit['last_name'] . " - " . $leadassignedit['emp_id'] ?></option>
            <?php } ?>
                                    </select>
                                    <script>
                                        $(document).ready(function () {
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
                                <!--<option value="<?php #echo $quote_data['lead_indicator']   ?>"><?php #echo $quote_data['lead_indicator']   ?></option>-->
                                    <option value="HOT" <?php if ($quote_data['lead_indicator'] == "HOT") echo "selected"; ?>>HOT</option>
                                    <option value="WARM" <?php if ($quote_data['lead_indicator'] == "WARM") echo "selected"; ?>>WARM</option>
                                    <option value="COLD" <?php if ($quote_data['lead_indicator'] == "COLD") echo "selected"; ?>>COLD</option>
                                </select>
                            </p>
                            <p><label>Lead Status</label></p>

                            <p>
                                <select name="lead_status" id="lead_status" class="textfield width300px" onchange="getReason(this.value);">
                                    <option value="1"  <?php if ($quote_data['lead_status'] == 1) echo 'selected="selected"'; ?>>Active</option>
                                    <option value="2"  <?php if ($quote_data['lead_status'] == 2) echo 'selected="selected"'; ?>>OnHold</option>
                                    <option value="3"  <?php if ($quote_data['lead_status'] == 3) echo 'selected="selected"'; ?>>Dropped</option>
                                    <option value="4"  <?php if ($quote_data['lead_status'] == 4) echo 'selected="selected"'; ?>>Closed</option>
                                </select>
                                <input type="hidden" value="<?php echo $quote_data['lead_status']; ?>" id="lead_status_hidden" name="lead_status_hidden" />

                            </p>

                            <script>
                                //if(document.getElementById('lead_status').value == 2)
                                //document.getElementById('lead-reason').style.display = "block";
                                function getReason(val) {
                                    if (val == 2) {
                                        document.getElementById('lead-reason').style.display = "block";
                                    } else {
                                        document.getElementById('lead-reason').style.display = "none";
                                    }
                                }
                            </script>
        <?php if (($quote_data['lead_status'] == 2) && isset($quote_data['lead_hold_reason'])) { ?>
                                <div id="lead-reason" class="lead-reason" style="display:block;">
                                    <p><label>Reasons:</label></p>
                                    <textarea class="textfield" style="width:300px; height:80px;" id="reason" name="reason"><?php echo $quote_data['lead_hold_reason'] ?></textarea>
                                </div>
        <?php } else {
            ?>
                                <div id="lead-reason" class="lead-reason" style="display:none;">
                                    <p><label>Reasons:</label></p>
                                    <textarea class="textfield" style="width:300px; height:80px;" id="reason" name="reason"></textarea>
                                </div>
                                    <?php } ?>

                            <p><label>Lead Stage</label></p>
                            <p>
                                <select name="lead_stage" id="lead_stage" class="textfield width300px">
        <?php foreach ($lead_stage as $stage) { ?>
                                        <option value="<?php echo $stage['lead_stage_id'] ?>" <?php if ($quote_data['lead_stage'] == $stage['lead_stage_id']) echo 'selected="selected"'; ?> ><?php echo $stage['lead_stage_name'] ?></option>
        <?php } ?>
                                </select>
                                <input type="hidden" value="<?php echo $quote_data['lead_stage']; ?>" id="lead_stage_hidden" name="lead_stage_hidden" />
                            </p>

                            <input type="hidden" name="jobid_edit" id="jobid_edit" value="<?php echo $quote_data['lead_id'] ?>" />
                            <div style="width:300px;">
                                <div class="buttons clearfix pull-left">
                                    <button type="submit" class="positive" onclick="editQuoteDetails('save'); return false;">Save</button>
                                </div>
                                <div class="buttons clearfix pull-left" style="padding: 0px 10px;">
                                    <button type="submit" class="positive" onclick="editQuoteDetails('view');
                                            return false;">Save & View</button>
                                </div>
                                <?php if ($quote_data['lead_status'] == 4) { ?>
                                    <div class="buttons clearfix pull-left">
                                        <button type="submit" class="positive" onclick="confirmaMoveLeadsToProject();
                                                return false;">Move To Project</button>

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
                                <button type="submit" onclick="addItem();
                                        return false;">Add Item</button>
                            </div>
                            <div class="buttons pull-left" id="drag-item-list-opener">
                                <button type="submit" class="" onclick="openDragItems();
                                        return false;">Additional Features</button>
                            </div>
                        </div>
                    </div>

                </form>

                <?php
                /**
                 * This will include the select box that changes the status of a job
                 */
                // if (isset($edit_quotation))
                // include '../tpl/status_change_menu.php';
                // require (theme_url().'/tpl/status_change_menu.php');
                ?>


            </div>
           
    <?php
} else {
    echo "You have no rights to access this page";
}
?>
    </div>
</div>
<div id="drag-item-list">
    <div class="close">X</div>
    <div class="handle">||||</div>
    <div class="item-inventory">
        <?php
        $menu = '<ul class="tabs-nav">';
        $data = '';


        foreach ($categories as $cat) {
            $menu .= '<li><a href="' . current_url() . '#cat_' . $cat["cat_id"] . '">' . $cat["cat_name"] . '</a></li>';
            $records = $cat['records'];
            ob_start();
            ?>
            <div id="cat_<?php echo $cat['cat_id'] ?>">
                <ul>
            <?php foreach ($records as $record) { ?>
                        <li>
                            <span onclick="resetCounter();" class="desc"><?php echo nl2br($record['item_desc']) ?></span><br />
                            <span class="hidden"><?php echo $record['item_price'] ?></span>
                            <strong>$<?php echo number_format($record['item_price'], 2, '.', ',') ?></strong>
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
        var _uid = new Date().getTime();
        var params = {};
        params[csrf_token_name] = csrf_hash_token;

        $.ajaxFileUpload({
            url: 'project/sow_file_upload/' + curr_job_id,
            secureuri: false,
            fileElementId: 'sow_ajax_file_uploader',
            dataType: 'json',
            data: params,
            success: function (data, status) {

                if (typeof (data.error) != 'undefined') {
                    if (data.error != '') {
                        if (window.console) {
                            console.log(data);
                        }
                        if (data.msg) {
                            alert(data.msg);
                        } else {
                            alert('File upload failed!');
                        }
                    } else {
                        if (data.msg == 'File successfully uploaded!') {
                            // alert(data.msg);				
                            $.each(data.res_file, function (i, item) {
                                var res = item.split("~", 2);
                                // alert(res[0]+res[1]);	
                                var name = '<div style="float: left; width: 100%;"><span style="float: left;">' + res[1] + '</span></div>';
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
                $('#' + _uid).hide('slow').remove();
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

    function confirmaMoveLeadsToProject()
    {
        var fsl_height = parseInt($(window).height()) - 80;
        fsl_height = fsl_height + 'px';
        var params = {};
        $.post(
                site_base_url + 'project/getCurentLeadsDetails/', {job_id: curr_job_id, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                function (data) {
                    if (data.error) {
                        alert(data.errormsg);
                    } else {
                        $('.comments-log-container').html(data);
                    }
                }
        );

        $.blockUI({
            message: $('.comments-log-container'),
            css: {
                border: '2px solid #999',
                color: '#333',
                padding: '8px',
                top: '400px',
                left: ($(window).width() - 820) / 2 + 'px',
                width: '820px',
                position: 'absolute',
                'overflow-y': 'auto',
                'overflow-x': 'hidden'
            }
        });
        $('html, body').animate({scrollTop: $(".comments-log-container").offset().top}, 1000);
        $(".comments-log-container").parent().addClass("assign-user");
    }

    $(function () {

        $('#drag-item-list').draggable({handle: $('.handle', $(this))});

        $('#drag-item-list .close').click(function () {
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
                function () {
                    $(this).addClass('over');
                },
                function () {
                    $(this).removeClass('over');
                }
        );

        $('#drag-item-list .item-inventory div ul li').click(function () {
            var the_text = '\n';
            the_text += $('.desc', $(this)).text();
            $('#item_desc').val(the_text);
            $('#item_price').val($('.hidden', $(this)).text());
            addItem();
            return false;
        });

        $('#item_desc').keyup(function () {
            var desc_len = $(this).val();

            if (desc_len.length > 600) {
                $(this).focus().val(desc_len.substring(0, 600));
            }

            var remain_len = 600 - desc_len.length;
            if (remain_len < 0)
                remain_len = 0;

            $('#desc-countdown').text(remain_len);
        });

        $('#quote_item_edit_form textarea').livequery(function () {
            $(this).keyup(function () {
                var desc_len = $(this).val();

                if (desc_len.length > 600) {
                    $(this).val(desc_len.substring(0, 600));
                }

                var remain_len = 600 - desc_len.length;
                if (remain_len < 0)
                    remain_len = 0;

                $('#desc-edit-countdown').text(remain_len);
            });
        });

        //for lead assign
        var config = {
            '.chzn-select': {},
            '.chzn-select-deselect': {allow_single_deselect: true},
            '.chzn-select-no-single': {disable_search_threshold: 10},
            '.chzn-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chzn-select-width': {width: "95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }

    });

//code added for when the customer name field is empty it will show the notice div.
    $("#ex-cust-name").keyup(function () {
//alert('test');
        var mylength = $(this).val();
        if (mylength == '') {
            $('.notice').slideDown(400);
            // location.reload();
        }
    });

    function resetCounter() {
        $('#desc-countdown').text(600);
    }

    function openDragItems() {

        var topv = parseInt($(window).scrollTop()) + 40;
        var leftv = $('#content').offset().left + 20;
        $('#drag-item-list').show(700).animate({top: topv + 'px', left: leftv + 'px'}, 1000);
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

        if (!/^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
            alert('Please insert a valid date!');
            return false;
        } else {
            $.post(
                    'welcome/set_proposal_date/',
                    {'lead_id': curr_job_id, 'date_type': set_date_type, 'date': date_val, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                    function (_data) {
                        try {
                            eval('var data = ' + _data);
                            if (typeof (data) == 'object') {
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
        var date_val = $('#lead_creation_date').val();

        if (!/^\d{2}-\d{2}-\d{4}$/.test(date_val)) {
            alert('Please insert a valid date!');
            return false;
        } else {
            $.post(
                    'welcome/set_lead_creation_date/',
                    {'lead_id': curr_job_id, 'date': date_val, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                    function (_data) {
                        try {
                            eval('var data = ' + _data);
                            if (typeof (data) == 'object') {
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

    $(function () {

        $('#lead_creation_date').datepicker({dateFormat: 'dd-mm-yy', maxDate: 0});
        $('#project-date-assign, #proposal_expected_date, .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: -7, maxDate: '+12M'});

        $('.task-list-item').livequery(function () {
            $(this).hover(
                    function () {
                        $('.delete-task', $(this)).css('display', 'block');
                    },
                    function () {
                        $('.delete-task', $(this)).css('display', 'none');
                    }
            );
        });

        $('#email_to_customer').change(function () {
            if ($(this).is(':checked')) {
                $('#multiple-client-emails').slideDown(400)
                        .children('input[type=checkbox]:first').attr('checked', true);
            } else {
                $('#additional_client_emails').val('');
                $('#multiple-client-emails').children('input[type=checkbox]').attr('checked', false).end()
                        .slideUp(400);
            }
        });


        $('#job-url-list li a:not(.file-delete)').livequery(function () {
            $(this).click(function () {
                window.open(this.href);
                return false;
            });
        });

        $('.jump-to-job select').change(function () {
            var _new_location = 'http://<?php echo $_SERVER['HTTP_HOST'], preg_replace('/[0-9]+/', '{{lead_id}}', $_SERVER['REQUEST_URI']) ?>';
            document.location = _new_location.replace('{{lead_id}}', $(this).val());
        });

        $('#job_log').siblings().hide();

        $('#job_log').focus(function () {
            $(this).siblings(':hidden').not('#multiple-client-emails').slideDown('fast');
            if ($(this).val() == 'Click to view options') {
                $(this).val('');
                $(this).removeClass('gray-text');
            }
        });



        /* job tasks character limit */
        $('#job-task-desc').keyup(function () {
            var desc_len = $(this).val();

            if (desc_len.length > 240) {
                $(this).focus().val(desc_len.substring(0, 240));
            }

            var remain_len = 240 - desc_len.length;
            if (remain_len < 0)
                remain_len = 0;

            $('#task-desc-countdown').text(remain_len);
        });

        $('#edit-job-task-desc').keyup(function () {
            var desc_len = $(this).val();

            if (desc_len.length > 240) {
                $(this).focus().val(desc_len.substring(0, 240));
            }

            var remain_len = 240 - desc_len.length;
            if (remain_len < 0)
                remain_len = 0;

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
                if (minutes != newMinutes) {
                    minutesInput.val(newMinutes);
                }

                var contactsText = prompt('Select contacts (min 3 letters). Seperate with a space.');
                var contacts = contactsText.split(' ');
                for (i in contacts) {
                    // Check the ones that match.
                    //
                    // Modifications needed: this needs to be case insensitive.
                    if (contacts[i].length >= 3) {
                        contacts[i].replace(/\w+/g, function (a) {
                            contacts[i] = a.charAt(0).toUpperCase() + a.substr(1).toLowerCase();
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

                if (confirm(recipients)) {
                    addLog();
                }
                return false;
            }
        });
    });

    function reloadWithMessagePjt(str, statusid) {
        $.get('ajax/request/set_flash_data/' + str, {}, function (data) {
            document.location.href = site_base_url + 'project/view_project/' + curr_job_id;
            $.unblockUI();
        });
    }

    function getBaseCurrency(val) {
        $.ajax({
            url: "welcome/get_base_currency",
            data: {'division': val, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
            type: "POST",
            dataType: 'json',
            async: false,
            success: function (data) {
                $('#expect_worth').val(data.base_cur);
                $('#expect_worth_readonly').val(data.base_cur);
            }
        });
    }

    function getBaseCurrencyEdit(val) {
        $.ajax({
            url: "welcome/get_base_currency",
            data: {'division': val, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
            type: "POST",
            dataType: 'json',
            async: false,
            success: function (data) {
                $('#expect_worth_edit').val(data.base_cur);
                $('#expect_worth_edit_readonly').val(data.base_cur);
            }
        });
    }
</script>
<?php require (theme_url() . '/tpl/footer.php'); ?>
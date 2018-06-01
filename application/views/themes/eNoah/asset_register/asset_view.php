<?php require (theme_url() . '/tpl/header.php'); ?>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/jq.livequery.min.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="assets/js/tasks.js?q=34"></script>
<script type="text/javascript">var this_is_home = true;</script>
<script src="assets/js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
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
</script>

<div class="comments-log-container"></div>

<script type="text/javascript">
    var unid = <?php echo $userdata['userid'] ?>;
    var belong_to = <?php echo $quote_data['belong_to'] ?>;
    var lead_assign = '<?php echo $quote_data['lead_assign'] ?>';
    var role_id = <?php echo $userdata['role_id'] ?>;

    var lead_services = [];
    lead_services['not_select'] = '';

<?php foreach ($job_cate as $job) { ?>
        lead_services[<?php echo $job["sid"] ?>] = '<?php echo $job["services"] ?>';
<?php } ?>

    var quote_id = <?php echo isset($quote_data['lead_id']) ? $quote_data['lead_id'] : 0 ?>;
    var ex_cust_id = 0;
    var item_sort_order = '';
    var curr_job_id = <?php echo $quote_data['lead_id'] ?>;

    $(function () {
<?php if (isset($quote_data) && (isset($edit_quotation) || isset($view_quotation))) { ?>
            populateQuote(<?php echo $quote_data['lead_id'] ?>, true);
<?php } ?>
    });

    var userid = <?php echo isset($userdata['userid']) ? $userdata['userid'] : 0 ?>;

    var current_job_status = <?php echo (isset($quote_data['lead_stage'])) ? $quote_data['lead_stage'] : 0 ?>;

    function addLog() {
        var the_log = $('#job_log').val();

        if ($.trim(the_log) == '') {
            alert('Please enter your post!');
            return false;
        }

        var submit_log_minutes = null, log_minutes = $('#log_minutes').val();
        if ($.trim(log_minutes) != '')
        {
            if (!/^[0-9]+$/.test(log_minutes))
            {
                alert('Invalid minutes supplied');
                return false;
            } else

            {//alert(log_minutes); return false;
                submit_log_minutes = log_minutes;
            }
        }


        var client_emails = true;
        if ($('#email_to_customer').is(':checked')) {
            client_emails = false;
            $('#multiple-client-emails').children('input[type=checkbox]').each(function () {
                if ($(this).is(':checked')) {
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
        /* $('.user-addresses input[type="checkbox"]:checked').each(function(){
         email_set += $(this).attr('id') + ':';
         }); */
        $('.user-addresses input[type="hidden"]').each(function () {
            email_set += $(this).attr('value') + ':';
        });

        $.blockUI({
            message: '<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
            css: {background: '#666', border: '2px solid #999', padding: '4px', height: '35px', color: '#333'}
        });


        var form_data = {'userid': userid, 'lead_id': quote_id, 'log_content': the_log, 'emailto': email_set, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}


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
        if (email_set == '' && typeof (form_data.client_emails_1) == 'undefined' && typeof (form_data.client_emails_2) == 'undefined' && typeof (form_data.client_emails_3) == 'undefined' && typeof (form_data.client_emails_4) == 'undefined' && typeof (form_data.additional_client_emails) == 'undefined') {
            if (!window.confirm('You do not have any user selected for emails!\nDo you want to continue?')) {
                $.unblockUI();
                return false;
            }
        }

        if ($('#email_to_customer').is(':checked') && the_log.match(/attach|invoice/gi) != null) {
            if (!window.confirm('You have not attached the invoice to the email.\nDo you want to continue without the invoice?')) {
                $.unblockUI();
                return false;
            }
        }
        $.post(
                'welcome/add_log',
                form_data,
                function (data) {
                    if (data.error == false)
                    {
                        $('#lead_log_list').prepend(data.html).children('.log:first').slideDown(400);
                        $('#job_log').val('');
                        $('.user-addresses input[type="checkbox"]:checked, #email_to_customer, #log_stickie').each(function () {
                            $(this).attr('checked', false);
                        });
                        $('#log_minutes').val('');
                        $('#additional_client_emails').val('');
                        $('#multiple-client-emails').children('input[type=checkbox]').attr('checked', false).end()
                                .slideUp(400);
                        if (data.status_updated) {
                            document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>';
                        }
                        if (typeof (this_is_home) != 'undefined')
                        {
                            window.location.href = window.location.href;
                        }
                        $.unblockUI();
                    } else
                    {
                        alert(data.errormsg);
                    }
                }, "json"
                )
    }


    function fullScreenLogs()
    {
        var fsl_height = parseInt($(window).height()) - 80;
        fsl_height = fsl_height + 'px';

        var params = {};
        params[csrf_token_name] = csrf_hash_token;
        $.post(
                site_base_url + 'welcome/getLogs/' + curr_job_id, params,
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
            css: {background: '#fff', border: '1px solid #999', padding: '4px', height: fsl_height, color: '#000000', width: '600px', overflow: 'auto', top: '40px', left: '50%', marginLeft: '-300px'},
            overlayCSS: {backgroundColor: '#fff', opacity: 0.9}
        });
        $('.blockUI:not(.blockMsg)').append('<p onclick="$.unblockUI();$(this).remove();" id="fsl-close">CLOSE</p>');
    }

    function runAjaxFileUpload() {
        var _uid = new Date().getTime();
        $('<li id="' + _uid + '">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#job-file-list');
        var params = {};
        params[csrf_token_name] = csrf_hash_token;
        var ffid = $('#filefolder_id').val();

        $.ajaxFileUpload({
            url: 'ajax/request/file_upload/' + curr_job_id + '/' + ffid,
            secureuri: false,
            fileElementId: 'ajax_file_uploader',
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
                        // $('#'+_uid).hide('slow').remove();
                    } else {
                        if (data.msg == 'File successfully uploaded!') {
                            // alert(data.msg);
                            /*Showing successfull message.*/
                            $('#fileupload_msg').html('<span class=ajx_success_msg>' + data.msg + '</span>');
                            setTimeout('timerfadeout()', 3000);
                            // Again loading existing files with new files
                            $('#jv-tab-3').block({
                                message: '<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
                                css: {background: '#666', border: '2px solid #999', padding: '4px', height: '35px', color: '#333'}
                            });
                            $.get(
                                    site_base_url + 'ajax/request/get_project_files/' + curr_job_id + '/' + ffid,
                                    {},
                                    function (data) {
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
                                                {'bSortable': false, 'aTargets': [0]}
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
        $('#ajax_file_uploader').val('');
        return false;
    }

    function getReplyForm(id) {
        $('#querylead_table').slideToggle();
        document.getElementById('query_form').innerHTML = "<input type='text' value='replay-" + id + "' name='replay-" + id + "' id='replay' />";
    }

    function QueryAjaxFileUpload() {
        var _uid = new Date().getTime();
        var query = $('#query').val();
        var replay = $('#replay').val();

        var reply = "";
        var fname = "";
        if ($.trim($('#query').val()) == '') {
            return false;
        }
        if (replay == 'query') {
            reply = "Raised";
        } else {
            reply = "Replied";
        }

        // $('<li id="' + _uid +'">Processing <img src="assets/img/ajax-loader.gif" /></li>').appendTo('#querylist');
        $('#querylist').empty();
        $('<div id="' + _uid + '">Processing <img src="assets/img/ajax-loader.gif" /></div>').appendTo('#querylist');
        $.ajaxFileUpload
                (
                        {
                            url: 'ajax/request/query_file_upload/<?php echo $quote_data['lead_id'] ?>/' + encodeURIComponent(query) + '/' + replay,
                            secureuri: false,
                            fileElementId: 'query_file',
                            dataType: 'json',
                            data: {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                            success: function (data, status)
                            {
                                if (typeof (data.error) != 'undefined')
                                {
                                    if (data.error != '')
                                    {
                                        if (window.console)
                                        {
                                            console.log(data);
                                        }
                                        if (data.msg)
                                        {
                                            alert(data.msg);
                                        } else
                                        {
                                            alert('File upload failed!');
                                        }
                                        $('#' + _uid).hide('slow').remove();
                                    } else
                                    {
                                        if (typeof (data.file_name) != 'undefined')
                                        {
                                            if (data.file_name != 'undefined') {
                                                fname = '<a href="crm_data/query/<?php echo $quote_data['lead_id'] ?>/' + data.file_name + '" onclick="window.open(this.href); return false;">' + data.file_name + '</a>';

                                            }
                                        } else {
                                            fname = 'File Not Attached';
                                        }


                                        var _file_link = '<table border="0" cellpadding="5" cellspacing="5" class="task-list-item" id="task-table-15"><tbody><tr><td valign="top" width="80">Query ' + reply + '</td><td colspan="3" class="task">' + decodeURIComponent(data.lead_query) + '</td></tr>';
                                        _file_link += '<tr><td>Date</td><td class="item user-name task" rel="59" width="100">' + data.up_date + '</td>';
                                        _file_link += '<td width="80">' + reply + ' By</td><td class="item hours-mins task" rel="4:0">' + data.firstname + ' ' + data.lastname + '</td></tr>';
                                        _file_link += '<tr><td colspan="1" valign="top">File Name</td><td colspan="3">' + fname + '</td></tr>';
                                        _file_link += '<tr><td class="task" colspan="4" valign="top"><button class="positive" style="float:right;cursor:pointer;" id="replay" onclick="getReplyForm(' + data.replay_id + ')">Reply</button></td></tr></table>';

<?php
if ($userdata['level'] > 1)
    echo '_del_link = "";';
?>
                                        $('#' + _uid).html(_file_link);
                                    }
                                }
                                $('#querylead_table').slideToggle();
                            },
                            error: function (data, status, e)
                            {
                                //alert(data);
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
                        }
                );
        $('#query').val('');
        $('#query_file').val('');
        return false;
    }

    function addURLtoJob() {
        var url = $.trim($('#job-add-url').val());
        var cont = $.trim($('#job-url-content').val());
        if (url == '') {
            alert('Please enter a URL to add');
            return false;
        }
        url = js_urlencode(url);
        $.post(
                'ajax/request/add_url_tojob/',
                {'lead_id': curr_job_id, 'url': url, 'content': cont, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'},
                function (_data) {
                    try {
                        eval('var data = ' + _data);
                        if (typeof (data) == 'object') {
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
                        eval('var data = ' + _data);
                        if (data.error == false) {
                            $(el).parent().hide('fast', function () {
                                $(this).remove();
                            });
                        } else {
                            alert('URL deletion failed! Please try again.');
                        }
                    } catch (e) {
                        alert('URL deletion failed! Please try again.');
                    }
                }
        )
    }

    function whatAreStickies()
    {
        var msg = 'Stickies are logs that are important.\nInformation that is vital to the job.\nInformtion that you need to find quickily without reading through all the communication.\nA URL, FTP/MySQL details, Important changes etc.';
        alert(msg);
        return false;
    }

    function whatIsSignature()
    {
        var msg = 'This is your signature!\nThis will be attached to any log that you email through.\nGo to "My Account" page to set your signature.';
        alert(msg);
        return false;
    }

    /* function to add the auto log */
    function qcOKlog() {
        var msg = "eSmart QC Officer Log Check - All Appears OK";

        if (!window.confirm('Are you sure you want to stamp the OK log?\n"' + msg + '"'))
            return false;

        $('.user .production-manager-user').attr('checked', true);
        $('#job_log').val(msg);
        $('#add-log-submit-button').click();
    }



    $(function () {

        $('#project-date-assign .pick-date, #set-job-task .pick-date, #edit-job-task .pick-date').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, maxDate: '+12M'});

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
                $('#multiple-client-emails').children('input[type=checkbox]').attr('checked', false).end().slideUp(400);
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
        $("#lead_tab").tabs({
            beforeActivate: function (event, ui) {
                if (ui.newPanel[0].id == 'jv-tab-4')
                    loadExistingTasks();
                if (ui.newPanel[0].id == 'jv-tab-3') {
                    getFolderdata($('#filefolder_id').val());
                    // showBreadCrumbs($('#filefolder_id').val());
                }
                if (ui.newPanel[0].id == 'jv-tab-8') {
                    loadLogs(quote_id);
                }
                if (ui.newPanel[0].id == 'jv-tab-6') {
                    loadCustomer(quote_id);
                }
            }
        });


        $('#job-url-list li a:not(.file-delete)').livequery(function () {
            $(this).click(function () {
                window.open(this.href);
                return false;
            });
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

            if (desc_len.length > 1000) {
                $(this).focus().val(desc_len.substring(0, 1000));
            }

            var remain_len = 1000 - desc_len.length;
            if (remain_len < 0)
                remain_len = 0;

            $('#task-desc-countdown').text(remain_len);
        });

        $('#edit-job-task-desc').keyup(function () {
            var desc_len = $(this).val();

            if (desc_len.length > 1000) {
                $(this).focus().val(desc_len.substring(0, 1000));
            }

            var remain_len = 1000 - desc_len.length;
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
</script>

<div id="content">
    <?php
    $date_used = $quote_data['date_created'];
    ?>

    <div class="inner q-view">
        <div class="right-communication">

            <?php
            $lead_assign_arr = array(0);
            $lead_assign_arr = @explode(',', $quote_data['lead_assign']);

            if ($quote_data['belong_to'] == $userdata['userid'] || (in_array($userdata['userid'], $lead_assign_arr) ) || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) {
                ?>
                <form id="comm-log-form">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                    <textarea name="job_log" id="job_log" class="textfield width99pct height100px gray-text">Click to view options</textarea>
                    <div style="position:relative;">
                        <textarea name="signature" class="textfield width99pct" rows="4" readonly="readonly" style="color:#666;"><?php echo $userdata['signature'] ?></textarea>
                        <span style="position:absolute; top:5px; right:18px;"><a href="#comm-log-form" onclick="whatIsSignature();
                                                    return false;">What is this?</a></span>
                    </div>

                    <div style="overflow:hidden;">

                        <p class="right" style="padding-top:5px;">Mark as a <a href="#was" onclick="whatAreStickies();
                                                    return false;">stickie</a> <input type="checkbox" name="log_stickie" id="log_stickie" /></p>
                        <div class="button-container">
                            <div class="buttons">
                                <button type="submit" class="positive" onclick="addLog();
                                                                    return false;" id="add-log-submit-button">Add Post</button>
                            </div>
                        </div>

                    </div>

                    <?php
                    if (isset($userdata)) {
                        ?>
                        <div class="email-set-options" style="overflow:hidden;">

                            <script type="text/javascript">

                                var client_comm_options_order = [];

                                $(function () {
                                    $('.client-comm-options input[type="checkbox"]').click(function () {
                                        var el = $(this);
                                        setTimeout(function () {
                                            if (el.is(':checked'))
                                            {
                                                if ($.inArray(el.attr('name'), client_comm_options_order) == -1)
                                                {
                                                    client_comm_options_order.push(el.attr('name'));
                                                }
                                            } else
                                            {
                                                client_comm_options_order = $.grep(client_comm_options_order, function (value) {
                                                    return value != el.attr('name')
                                                });
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
                                        if (!window.confirm('This will replace the text on the log window!\nProceed?'))
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
                            </script>

                            <input type="checkbox" name="email_to_customer" id="email_to_customer" /> <label for="email_to_customer" class="normal">Email Client</label>
                            <input type="hidden" name="client_email_address" id="client_email_address" value="<?php echo (isset($quote_data)) ? $quote_data['email_1'] : '' ?>" />
                            <input type="hidden" name="client_full_name" id="client_full_name" value="<?php echo (isset($quote_data)) ? $quote_data['customer_name'] : '' ?>" />
                            <input type="hidden" name="requesting_client_approval" id="requesting_client_approval" value="0" />

                            <p id="multiple-client-emails">
                                <input type="checkbox" name="client_emails_1" id="client_emails_1" value="<?php echo $quote_data['email_1'] ?>" /> <?php echo $quote_data['email_1'] ?>
                                <?php
                                if ($quote_data['email_2'] != '') {
                                    ?>
                                    <br /><input type="checkbox" name="client_emails_2" id="client_emails_2" value="<?php echo $quote_data['email_2'] ?>" /> <?php echo $quote_data['email_2'] ?>
                                    <?php
                                }
                                if ($quote_data['email_3'] != '') {
                                    ?>
                                    <br /><input type="checkbox" name="client_emails_3" id="client_emails_3" value="<?php echo $quote_data['email_3'] ?>" /> <?php echo $quote_data['email_3'] ?>
                                    <?php
                                }
                                if ($quote_data['email_4'] != '') {
                                    ?>
                                    <br /><input type="checkbox" name="client_emails_4" id="client_emails_4" value="<?php echo $quote_data['email_4'] ?>" /> <?php echo $quote_data['email_4'] ?>
                                    <?php
                                }
                                ?>
                                <br />
                                Additional Emails (separate addresses with a comma)<br />
                                <input type="text" name="additional_client_emails" id="additional_client_emails" class="textfield width99pct" />
                            </p>

                        </div>
        <?php
    }
    ?>

                    <div class="user-addresses">
                    <?php
                    /* check the condition if role_id = 1 (admin) and role_id = 2 (management)  and leadowner and lead assigned to  */
                    ?>
                        <?php if (count($user_accounts) > 0) { ?>
                            <label>Email To:</label>
                            <br/>
                            <select data-placeholder="Choose Users..." name="email_users" multiple id="email_users" class="chzn-select" style="width:400px;">
        <?php
        foreach ($user_accounts as $ua) {
            // if ((($ua['role_id'] == 1) && ($ua['inactive'] == 0)) || (($ua['role_id'] == 2) && ($ua['inactive'] == 0)) || (($ua['userid'] == $quote_data['belong_to']) && ($ua['inactive'] == 0)) || (($ua['userid'] == $quote_data['lead_assign']) && ($ua['inactive'] == 0)) ) {
            ?>
                                    <option value="<?php echo 'email-log-' . $ua['userid']; ?>"><?php echo $ua['first_name'] . ' ' . $ua['last_name']; ?></option>
                                    <?php
                                    // }
                                }
                                ?>
                            </select>
                                <?php
                            }
                            ?>
                    </div>
                </form>
<?php } ?>
        </div>

        <div class="side1">
            <h2 class="job-title">
<?php
echo htmlentities($quote_data['lead_title'], ENT_QUOTES);
?>
            </h2>

            <div class="action-buttons" style="overflow:hidden;">

<?php
if (isset($quote_data)) {
    foreach ($quote_data as $asset) {

 print_r($asset);exit;
        ?>

                        <!--					<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
                                                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />	
                                                                        <table>
                                                                                <tr>
                                                                                        <td valign="top" width="300">
                                                                                                <h6 class="project-startdate-label">Proposal Expected Date &raquo;<span><?php if ($quote_data['proposal_expected_date'] != '') echo date('d-m-Y', strtotime($quote_data['proposal_expected_date']));
                else echo 'Not Set'; ?></span></h6>		
                                                                                        </td>
                                                                                </tr>
                                                                        </table>
                                                                </form>-->


                        <div class="q-init-details">
                            <p class="clearfix"><label>Asset No</label>  <span><?php echo $asset['asset_id'] ?></span></p>
                            <p class="clearfix"><label>Department Name</label>  <span><?php echo htmlentities($quote_data['department_id'], ENT_QUOTES) ?></span></p>
                            <p class="clearfix"><label>Project Name</label>  <span><?php echo $quote_data['project_id'] ?></span></p>
                            <p class="clearfix"><label>Asset Name</label>  <span><?php echo $quote_data['asset_name'] ?></span></p>
                            <p class="clearfix"><label>Asset Type</label><span><?php echo $quote_data['asset_type'] ?></span></p>
                            <p class="clearfix"><label>Storage Mode</label>  <span><?php echo $quote_data['storage_mode'] ?><?php echo '&nbsp;' ?><?php echo $quote_data['expect_worth_amount']; ?><?php if (is_int($quote_data['expect_worth_amount'])) echo '.00' ?></span></p>

                            <p class="clearfix"><label>Location</label><span><?php echo $quote_data['location'] ?></span></p>
                            <p class="clearfix"><label>Asset Owner</label> <span><?php echo $quote_data['asset_owner']; ?></span></p>
                            <p class="clearfix"><label>Labelling</label><span><?php echo $quote_data['labelling']; ?></span></p>
                            <p class="clearfix"><label>Confidentiality</label><span><?php echo $quote_data['confidentiality'] ?></span></p>
                            <p class="clearfix"><label>Integrity</label><span><?php echo $quote_data['integrity'] ?></span></p>
                            <p class="clearfix"><label>Availability</label><span><?php echo $quote_data['availability'] ?></span></p>
                           
                           
                        </div>
    <?php }
}
?>

                <?php
                include theme_url() . '/tpl/user_accounts_options.php';

                if ($quote_data['belong_to'] == $userdata['userid'] || (in_array($userdata['userid'], $lead_assign_arr) ) || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) {
                    ?>

                    <?php
                }
                ?>
            </div>
        </div>
        <div id="lead_tab">
            <ul id="job-view-tabs">
                <li><a href="<?php echo current_url() ?>#jv-tab-1">Lead History</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-2">Estimate</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-3">Files</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-4">Tasks</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-6">Customer</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-7">Query</a></li>
                <li><a href="<?php echo current_url() ?>#jv-tab-8">History</a></li>
            </ul>
            <div id="jv-tab-1">
                <table class="data-table">
                    <tr ><th>Stage Name</th><th>Modified By</th><th>Modified On</th></tr>
<?php foreach ($lead_stat_history as $ldsh) { ?>
                        <tr>
                            <td><?php echo $ldsh['lead_stage_name']; ?></td>
                            <td><?php echo $ldsh['first_name'] . " " . $ldsh['last_name']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($ldsh['dateofchange'])); ?></td>
                        </tr>
<?php } ?>
                </table>
            </div>

            <div id="jv-tab-2">
                <div class="q-container">
                    <div>
                        <div class="q-quote-items" style="position: relative;">
                            <h4 class="quote-title">Project Name : <?php echo (isset($quote_data)) ? $quote_data['lead_title'] : '' ?></h4>
                            <ul id="q-sort-items"></ul>
                        </div>
                    </div>
                </div>

                <div class="q-sub-total">
                    <table class="width565px" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="160">Sale Amount <span id="sale_amount"></span></td>
                            <td width="120" align="right">GST <span id="gst_amount"></span></td>
                            <td width="20">&nbsp;</td>
                            <td align="right">Total inc GST <span id="total_inc_gst"></span></td>
                        </tr>
                    </table>
                </div>
            </div><!-- id: jv-tab-2 end -->

            <div id="jv-tab-3">

<?php $ff_id = isset($parent_ffolder_id) ? $parent_ffolder_id : ''; ?>

                <div id="file_breadcrumb"></div>
                <div>
                    <div class="pull-left pad-right">
                        <form id="file_search">
                            <label>Search File or Folder</label> <input type="text" class="textfield" id="search_input" value="" />
                            <button class="positive" onclick="searchFileFolder();
                                                                return false;" style="margin:0 0 0 5px;" type="submit">Search</button>
                        </form>
                    </div>
                            <?php
                            $lead_assign_arr = array(0);
                            $lead_assign_arr = @explode(',', $quote_data['lead_assign']);
                            ?>

                    <?php if ($quote_data['belong_to'] == $userdata['userid'] || (in_array($userdata['userid'], $lead_assign_arr)) || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) { ?>
                        <div class="pull-left pad-right">
                            <form name="ajax_file_upload" class="pull-left pad-right">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                <div id="upload-container">
                                    <img src="assets/img/uploads.png" alt="Browse" class="icon-width" id="upload-decoy" />
                                    <input type="hidden" id="filefolder_id" value="<?php echo $ff_id; ?>">
                                    <input type="file" title='Upload' class="textfield" multiple id="ajax_file_uploader" name="ajax_file_uploader[]" onchange="return runAjaxFileUpload();" />
                                </div>
                            </form>
                            <div class="pull-left pad-right">
                                <a title="Add Folder" href='javascript:void(0)' onclick="create_folder(<?php echo $quote_data['lead_id']; ?>,<?php echo $ff_id; ?>);
                                                                    return false;"><img src="assets/img/add_folders.png" class="icon-width" alt="Add Folder" ></a>
                            </div>
                            <div class="pull-left pad-right">
                                <a title="Move All" onclick="moveAllFiles(); return false;" ><img src="assets/img/document_move.png" class="icon-width" alt="Move All"></a>
                            </div>
                            <div class="pull-left pad-right">
                                <a title="Delete All" onclick="deleteAllFiles(); return false;" ><img src="assets/img/delete_new.png" class="icon-width" alt="Delete"></a>
                            </div>

    <?php /* ?>	<?php if($user_roles == 1 || $login_userid == $project_belong_to || $login_userid == $project_assigned_to || $login_userid == $project_lead_assign ) { ?>
      <div class="pull-left pad-right">
      <a title="Folder Access" onclick="folderAccess(); return false;" ><img src="assets/img/folder-access.png" alt="Folder Access"></a>
      </div>
      <?php }
      ?>
      <?php */ ?>
                        </div>
                        <?php } ?>
                    <div class='clrboth'></div>
                </div>	

                <div id='fileupload_msg' class='succ_err_msg'></div>
                <div id="list_file"></div>

                <form id="move-file" onsubmit="return false;">
                    <!-- edit file -->
                    <div id='mf_successerrmsg' class='succ_err_msg'></div>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr><td colspan="4"><div id='mf_name'></div></td></tr>
                        <tr>
                            <td colspan="4">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                <input type='hidden' name='mlead_id' id='mlead_id' value=''>
                                <input type='hidden' name='mfile_id' id='mfile_id' value=''>
                                <input type='hidden' name='mfparent_id' id='mfparent_id' value=''>
                                <input type='hidden' name='mffiletype' id='mffiletype' value=''>
                                <input type='hidden' name='mffilename' id='mffilename' value=''>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="80">Move to</td>
                            <td colspan="3">
                                <select name='move_destiny' id="file_tree">
                                    <option value=''>Select</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="buttons"><button type="submit" class="positive" onclick="move_files();">Move</button></div>
                                <div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
                            </td>
                        </tr>
                    </table>
                    <!-- edit end -->
                </form>
                <form id="create-folder" onsubmit="return false;">
                    <!-- edit file -->
                    <div id='af_successerrmsg' class='succ_err_msg'></div>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="2"><div id='af_name'><strong><h3>Create Folder</h3></strong></div></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                <input type='hidden' name='aflead_id' id='aflead_id' value=''>
                                <input type='hidden' name='afparent_id' id='afparent_id' value=''>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="80"><label>Parent</label></td>
                            <td>
                                <select name='add_destiny' id="add_file_tree">
                                    <option value=''>Select</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="80"><label>New Folder</label></td>
                            <td><input type="text" name="new_folder" id="new_folder" value="" class="textfield"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="buttons"><button type="submit" class="positive" onclick="add_folder();">Add</button></div>
                                <div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
                            </td>
                        </tr>
                    </table>
                    <!-- edit end -->
                </form>
                <form id="moveallfile" onsubmit="return false;">
                    <!-- edit file -->
                    <div id='all_mf_successerrmsg' class='succ_err_msg'></div>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="4"><strong><h3>Move</h3></strong></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                <input type='hidden' name='mall_lead_id' id='mall_lead_id' value=''>
                                <input type='hidden' name='mov_folder' id='mov_folder' value=''>
                                <input type='hidden' name='mov_file' id='mov_file' value=''>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="80">Move to</td>
                            <td colspan="3">
                                <select name='move_destiny' id="file_tree_all">
                                    <option value=''>Select</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="buttons"><button type="submit" class="positive" onclick="move_all_files();">Move</button></div>
                                <div class="buttons"><button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button></div>
                            </td>
                        </tr>
                    </table>
                    <!-- edit end -->
                </form>

                <form id="folderAccessRights" onsubmit="return false;">
                    <span style="float:right; cursor:pointer;" onclick="$.unblockUI();"><img src='<?php echo base_url() . 'assets/img/cross.png'; ?>' /></span>
                    <div id='fa_successerrmsg' class='succ_err_msg'></div>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="4"><strong><h3 style="text-align:center;">Access Rights</h3></strong></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                <input type='hidden' name='fa_lead_id' id='fa_lead_id' value=''>
                                <input type='hidden' name='fa_folder' id='fa_folder' value=''>
                                <input type='hidden' name='fa_file' id='fa_file' value=''>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" id="accessStruct"></td>
                        </tr>
                        <tr>
                            <td class="pad-all" colspan="4" align="right">
                                <div class="buttons">

                                    <button type="submit" class="positive" onclick="savefolderAccess();
                                                        return false;"  id="folder_access_save">Save</button>		

                                    <img width="61px" height="27px" style=" display:none; float:left;" id="load_save_folder_access" src="<?php echo base_url() . 'assets/images/loading.gif'; ?>">

                                </div>

                            </td>
                        </tr>
                    </table>
                </form>
            </div><!--id: jv-tab-3 end -->

            <div id="jv-tab-4">
<?php
if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) {
    ?>
                    <form id="set-job-task" onsubmit="return false;">
                        <input type="hidden" name ="taskcompleted" value="0" id="taskcompleted" />
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

    <?php
    $uio = $userdata['userid'];
    if (!empty($created_by)) {
        foreach ($created_by as $value) {
            $b[] = $value[created_by];
        }
    }
    ?>
                        <h3>Tasks</h3>
                        <table border="0" cellpadding="0" cellspacing="0" class="task-add toggler">
                            <tr>
                                <td colspan="4"><strong>All fields are required!</strong></td>
                            </tr>
                            <tr>
                                <td valign="top"><br /><br />Task Desc</td>
                                <td colspan="3">
                                    <strong><span id="task-desc-countdown">1000</span></strong> characters left.<br />
                                    <textarea name="job_task" id="job-task-desc" class="width420px"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="hidden" class="edit-task-owner textfield"></td>
                            </tr>
                            <tr >
                                <td style="padding-bottom:10px;" ><br/>Category</td>
                                <td>
                                    <select name="task_category" data-placeholder="Choose category." class="chzn-select edit-task-category textfield" id="taskCategory" style="width:140px;">
                                        <option value=""></option>
    <?php
    foreach ($category_listing_ls as $ua) {
        echo '<option value="' . $ua['id'] . '">' . $ua['task_category'] . '</option>';
    }
    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr >
                                <td style="padding-bottom:10px;">Priority</td>
                                <td>
                                    <select name="task_priority" data-placeholder="Choose Priority." class="chzn-select edit-task-priority textfield" id="taskpriority" style="width:140px;">
                                        <option value=""></option>
                                        <option value="1">Critical</option>
                                        <option value="2">High</option>
                                        <option value="3">Medium</option>
                                        <option value="4">Low</option>

                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Allocate to</td>
                                <td style="padding:5px 0">
                                    <select name="task_user" data-placeholder="Choose a User..." class="chzn-select textfield width100px">
    <?php
    echo $remind_options, $remind_options_all;
    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Hours</td>
                                <td><input type="text" name="estimated_hours" class="edit-job-est-hr textfield width100px" onkeypress="return isPaymentVal(event)" style="margin-top:5px;" maxlength="5"/></td>
                            </tr>
                            <tr>
                                <td>Planned Start Date</td>
                                <td><input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" style="margin: 5px 0px;"/></td>
                                <td>Planned End Date</td>
                                <td><input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px" /></td>
                            </tr>						
                            <tr>
                                <td>Remarks</td>
                                <td colspan="3"><textarea name="remarks" id="task-remarks" class="task-remarks" width="420px"></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="buttons">
                                        <button type="submit" class="positive" onclick="addNewTask('', '<?php echo $this->security->get_csrf_token_name() ?>', '<?php echo $this->security->get_csrf_hash(); ?>');">Add</button>
                                    </div>
                                    <div class="buttons">
                                        <button type="submit" class="negative" onclick="$('.toggler').slideToggle();">Cancel</button>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="buttons task-init  toggler">
                            <button type="button" class="positive" onclick="$('.toggler').slideToggle();">Add New</button>
                        </div>

                        <br /><br />
                    </form>
    <?php
}
?>
                <div class="existing-task-list" id="welcome_view_quote">
                    <h4>Existing Tasks</h4>
                </div>

                <form id="edit-job-task" onsubmit="return false;">

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                    <table border="0" cellpadding="0" cellspacing="0" class="task-add task-edit">
                        <tr>
                            <td colspan="4">
<?php
$uio = $userdata['userid'];
if (!empty($created_by)) {
    foreach ($created_by as $value) {
        $b[] = $value[created_by];
    }
}
?>
                                <strong>All fields are required!</strong>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="80"><br /><br />Task</td>
                            <td colspan="3">
                                <strong><span id="edit-task-desc-countdown">1000</span></strong> characters left.<br />
                                <textarea name="job_task" class="edit-job-task-desc width420px"></textarea>
                            </td>
                        </tr>
                        <tr >
                            <td style="padding-bottom:10px;" ><br/>Category</td>
                            <td>
                                <select name="task_category" data-placeholder="Choose category." class="chzn-select edit-task-category textfield" id="taskCategory" style="width:140px;">
                                    <option value=""></option>
<?php
foreach ($category_listing_ls as $ua) {
    echo '<option value="' . $ua['id'] . '">' . $ua['task_category'] . '</option>';
}
?>
                                </select>
                            </td>
                        </tr>
                        <tr >
                            <td style="padding-bottom:10px;">Priority</td>
                            <td>
                                <select name="task_priority" data-placeholder="Choose Priority." class="chzn-select edit-task-priority textfield" id="taskpriority" style="width:140px;">
                                    <option value=""></option>
                                    <option value="1">Critical</option>
                                    <option value="2">High</option>
                                    <option value="3">Medium</option>
                                    <option value="4">Low</option>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Allocate to</td>
                            <td>
                                <select name="task_user" class="chzn-select edit-task-allocate textfield width100px" data-placeholder="Choose a User...">
<?php
echo $remind_options, $remind_options_all;
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:10px;">Status</td>
                            <td>
                                <select name="task_priority" data-placeholder="Choose Status." class="chzn-select edit-task-stages" id="taskstages" style="width:140px;">
                                    <option value=""></option>
<?php
foreach ($task_stages as $tstag) {
    echo '<option value="' . $tstag['task_stage_id'] . '">' . $tstag['task_stage_name'] . '</option>';
}
?>
                                </select>
                                <input type="hidden" name="task_complete_status" id="edit_complete_status" class="edit-complete-status textfield width100px" />	
                            </td>
                        </tr>
                        <tr>
                            <td>Estimated Hours</td>
                            <td><input type="text" name="estimated_hours" class="edit-job-est-hr textfield width100px" onkeypress="return isPaymentVal(event)" style="margin-top:5px;" maxlength="5"/></td>
                        </tr>
                        <tr>
                            <td>Planned Start Date</td>
                            <td><input type="text" name="task_start_date" class="edit-start-date textfield pick-date width100px" style="margin: 5px 0px;"/></td>
                            <td>Planned End Date</td>
                            <td><input type="text" name="task_end_date" class="edit-end-date textfield pick-date width100px"/></td>
                        </tr>
                        <tr>
                            <td>Actual Start Date</td>
                            <td><input type="text" name="task_actualstart_date" class="edit-actualstart-date textfield pick-date width100px" /></td>
                            <td>Actual End Date</td>
                            <td class="actualend-date"><input type="text" class="edit-actualend-date textfield" readonly></td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td colspan="3"><textarea name="remarks" class="edit-task-remarks" width="420px"></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="buttons">
                                    <button type="submit" class="positive" onclick="editTask();">Update</button>
                                </div>
                                <div class="buttons">
                                    <button type="submit" class="negative" onclick="$.unblockUI();">Cancel</button>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <!-- edit task end -->
                </form>
            </div><!-- id: jv-tab-4 end -->

            <div id="jv-tab-6">
                <div id="load-customer">
                </div><!-- id: load customer end -->
            </div><!-- id: jv-tab-6 end -->

            <div id="jv-tab-7"><!-- id: jv-tab-7 start -->
                <div id="querylead_form" style="border:0px solid;" >
                    <form id="querylead" name="querylead" method="post" onsubmit="return QueryAjaxFileUpload();">

                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                        <h3>Query</h3>
                        <table id="querylead_table" class="layout add_query" style="display: none">								
                            <tr>
                                <td>Query:</td>
                            <div id="query_form" style="display: none"><input type='text' value='query' name='replay' id='replay' /></div>
                            <td><textarea name="query" id="query" cols="20" rows="3" style="width: 270px; height: 70px;"></textarea></td>
                            </tr>
                            <tr>
                                <td width="120">Attachment File:</td>
                                <td><input type="file" class="textfield" id="query_file" name="query_file" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="submit" name="query_sub" value="Submit" class="positive submitpositive" />
                                    <input type="button" name="query_sub" value="Cancel" class="cancel" style="padding:2px 7px;" />
                                </td>
                            </tr>
                        </table>
<?php if ($quote_data['belong_to'] == $userdata['userid'] || $quote_data['lead_assign'] == $userdata['userid'] || $userdata['role_id'] == 1 || $userdata['role_id'] == 2) { ?>
                            <div class="buttons task-init  toggler">
                                <button type="button" class="positive" onclick="$('#querylead_table').slideToggle();">Raise Query</button>
                            </div>
<?php } ?>

                        <table width="100%" id="lead_query_list" class="existing-query-list queriestbl"> 
                            <thead> 
                                <tr> 
                                    <th>&nbsp;</th> 
                                </tr> 
                            </thead>
                            <tbody id="query-file-list">
                                <tr id="querylist"><td>&nbsp;</td></tr>
<?php echo $query_files1_html; ?>			
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
            <div id="jv-tab-8">
                <span style="float:right;"> 
                    <a href="#" onclick="fullScreenLogs(); return false;">View Full Screen</a>
                    |
                    <a href="#" onclick="$('.log > :not(.stickie),#pager').toggle();
                                return false;">View/Hide Stickies</a>
                </span>
                <h4>Comments</h4>
                <div id="load-log"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $(".cancel").click(function () {
            $('#querylead_table').slideToggle();
            $('#query, #query_file').val('');
        })
    });
    $('.queriestbl').dataTable({
        "iDisplayLength": 5,
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
            "sEmptyTable": "No Queries Found..."
        }
    });
    function loadLogs(id) {
        var params = {};
        params[csrf_token_name] = csrf_hash_token;
        $.post(
                site_base_url + 'welcome/getLogs/' + id, params,
                function (data) {
                    if (data.error) {
                        alert(data.errormsg);
                    } else {
                        $('#load-log').html(data);
                        logsDataTable();
                    }
                }
        );
    }
    function loadCustomer(id)
    {
        var params = {};
        params[csrf_token_name] = csrf_hash_token;

        $.post(
                site_base_url + 'welcome/getCustomers/' + id, params,
                function (data) {
                    if (data.error) {
                        alert(data.errormsg);
                    } else {
                        $('#load-customer').html(data);
                    }
                }
        );
    }
    function logsDataTable() {
        $('#lead_log_list').dataTable({
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

    function setCustomer() {
        $('#resmsg_customer').empty();
        var customer_company_name = $('#customer_company_name').val();
        var customer_id = $('#customer_id').val();
        var customer_id_old = $('#customer_id_old').val();
        var customer_company_name_old = $('#customer_company_name_old').val();

        if (customer_id == '') {
            return false;
        }

        $.blockUI({
            message: '<h4>Processing</h4><img src="assets/img/ajax-loader.gif" />',
            css: {background: '#666', border: '2px solid #999', padding: '4px', height: '35px', color: '#333'}
        });
        $.ajax({
            type: 'POST',
            url: site_base_url + 'welcome/update_customer/',
            dataType: 'json',
            data: 'customer_company_name=' + customer_company_name + '&customer_id=' + customer_id + '&customer_id_old=' + customer_id_old + '&customer_company_name_old=' + customer_company_name_old + '&lead_id=' + curr_job_id + '&' + csrf_token_name + '=' + csrf_hash_token,
            success: function (data) {
                if (data.error == false) {
                    $('#resmsg_customer').html("<span class='ajx_success_msg'>Customer Updated</span>");
                    // $('.job-title').html(lead_title);
                } else {
                    $('#resmsg_customer').html("<span class='ajx_failure_msg'>" + data.error + "</span>");
                }
                loadCustomer(curr_job_id);
                $.unblockUI();
            }
        });
        setTimeout('timerfadeout()', 2000);
    }
</script>	
<script type="text/javascript" src="assets/js/request/request.js"></script>
<?php require (theme_url() . '/tpl/footer.php'); ?>
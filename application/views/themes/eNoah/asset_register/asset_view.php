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


                        </div>
        <?php
    }
    ?>

                    
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

// print_r($asset);exit;
                        ?>

                        <!--					<form name="project_dates" id="project-date-assign" style="padding:15px 0 5px 0;">
                                                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />	
                                                                        <table>
                                                                                <tr>
                                                                                        <td valign="top" width="300">
                                                                                                <h6 class="project-startdate-label">Proposal Expected Date &raquo;<span><?php if ($quote_data['proposal_expected_date'] != '')
                    echo date('d-m-Y', strtotime($quote_data['proposal_expected_date']));
                else
                    echo 'Not Set';
                ?></span></h6>		
                                                                                        </td>
                                                                                </tr>
                                                                        </table>
                                                                </form>-->


                        <div class="q-init-details">
                            <p class="clearfix"><label>Asset No</label>  <span><?php echo $asset['asset_id'] ?></span></p>
                            <p class="clearfix"><label>Department Name</label>  <span><?php echo $asset['department_id'] ?></span></p>
                            <p class="clearfix"><label>Project Name</label>  <span><?php echo $asset['project_id'] ?></span></p>
                            <p class="clearfix"><label>Asset Name</label>  <span><?php echo $asset['asset_name'] ?></span></p>
                            <p class="clearfix"><label>Asset Type</label><span><?php echo $asset['asset_type'] ?></span></p>
                            <p class="clearfix"><label>Storage Mode</label>  <span><?php echo $asset['storage_mode'] ?></span></p>

                            <p class="clearfix"><label>Location</label><span><?php echo $asset['location'] ?></span></p>
                            
                            <p class="clearfix"><label>Asset Owner</label> <span><?php     
                            foreach ($asset_owner as $owner_name){
                                                    echo $owner_name['first_name']. ' ' .$owner_name['last_name'];
                                                } ?></span></p>
                            <p class="clearfix"><label>Labelling</label><span><?php echo $asset['labelling']; ?></span></p>
                            <p class="clearfix"><label>Confidentiality</label><span><?php echo $asset['confidentiality'] ?></span></p>
                            <p class="clearfix"><label>Integrity</label><span><?php echo $asset['integrity'] ?></span></p>
                            <p class="clearfix"><label>Availability</label><span><?php echo $asset['availability'] ?></span></p>


                        </div>
                    <?php
                    }
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
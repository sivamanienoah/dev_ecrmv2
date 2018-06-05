<?php 
$cfg = $this->config->item('crm');
$userdata = $this->session->userdata('logged_in_user');

$td_chk = false;
$td_style = '';
$custom_width = 'width:1650px !important;';

if(!empty($db_fields) && count($db_fields)>0){
	$td_chk = true;
	
	$custom_width = 'width:100% !important;';
	if(count($db_fields) == 8) {
		$custom_width = 'width:1650px !important;';
	}
	$td_cn = $td_ew = $td_reg = $td_lo = $td_lat = $td_stg = $td_ind = $td_stat = 'style="display: none;"';
	if(in_array('CN', $db_fields)) { $td_cn = 'style="display: table-cell;"'; }
	if(in_array('EW', $db_fields)) { $td_ew = 'style="display: table-cell; width:90px;"'; }
	if(in_array('REG', $db_fields)) { $td_reg = 'style="display: table-cell;"'; }
	if(in_array('LO', $db_fields)) { $td_lo = 'style="display: table-cell;"'; }
	if(in_array('LAT', $db_fields)) { $td_lat = 'style="display: table-cell;"'; }
	if(in_array('STG', $db_fields)) { $td_stg = 'style="display: table-cell;"'; }
	if(in_array('IND', $db_fields)) { $td_ind = 'style="display: table-cell;"'; }
	if(in_array('STAT', $db_fields)) { $td_stat = 'style="display: table-cell; width:90px;"'; }
}
?>
<div id="ad_filter" class="custom_dashboardfilter customize-sec" style="overflow-x:scroll; width:100%;" >
	<div class="tbl-field-customize">
		<a href="#" class="modal-custom-fields"><span>Customize Table Fields</span></a>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" class="data-tbl dashboard-heads dataTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Subscription Name</th>
                            <th>Subscription type</th>
                            <th>Customer</th>
                            <th>Subscription Status</th>
                            <th>DNS</th>
                            <th>Subscription Expiry Date</th>
                            <th>Hosting Expiry Date</th>
                            <th>SSL Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_array($filter_results) && count($filter_results) > 0) {
                            foreach ($filter_results as $account) {
                              //  print_r($account);exit;
                                $rem = strtotime($account['go_live_date']) - time();
                                if ($account['login_url'] != '' && $account['login'] != '' && $account['registrar_password'] != '' && $account['email'] != '' && $account['cur_smtp_setting'] != '' && $account['cur_pop_setting'] != '' && $account['cur_dns_primary_url'] != '' && $account['cur_dns_primary_ip'] != '' && $account['cur_dns_secondary_url'] != '' && $account['cur_dns_secondary_ip'] != '')
                                    $dns = 'green';
                                else if ($account['login_url'] != '' && $account['login'] != '' && $account['registrar_password'] != '')
                                    $dns = 'orange';
                                else
                                    $dns = 'red';
                                ?>
                                <tr>
                                    <td>
                <?php if ($this->session->userdata('edit') == 1) { ?><a href="hosting/add_account/update/<?php echo $account['hostingid'] ?>"><?php echo $account['domain_name'] ?></a><?php } else echo "Edit"; ?>
                                    </td>
                                    <td>
                <?php echo ($account['subscriptions_type_name']) ? $account['subscriptions_type_name'] : '---'; ?>
                                    </td>

                                    <td>
                                        <?php echo $account['customer'] ?>
                                    </td>
                                    <td>
                                        <?php echo $account['domain_status'] ?>
                                    </td>
                                    <td>
                                        <?php if ($this->session->userdata('accesspage') == 1) { ?>
                                            <a href="dns/go_live/<?php echo $account['hostingid']; ?>" style="color:<?php echo $dns; ?>;">View</a>
                                        <?php } else echo $dns; ?>
                                    </td>
                                    <td>
                                        <?php echo $account['domain_expiry']; ?>
                                    </td>
                                    <td>
                                        <?php echo $account['expiry_date']; ?>
                                    </td>
                                    <td>
                                            <?php echo $account['ssl']; ?>
                                    </td>
                                    <td>
                                <?php if ($this->session->userdata('edit') == 1) { ?>
                                            <a href="hosting/add_account/update/<?php echo $account['hostingid'] ?>" title='Edit'><img src="assets/img/edit.png" alt='edit'></a>
                                <?php } ?>
                                <?php if ($this->session->userdata('delete') == 1) { ?>
                                            <a class="delete" href="javascript:void(0)" onclick="return delHosting(<?php echo $account['hostingid']; ?>);" title='Delete'> <img src="assets/img/trash.png" alt='delete'></a> 
                        <?php } ?>
                                    </td>
                                </tr>
                        <?php
                    }
                }
                ?>
                    </tbody>
                </table>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/hosting/advance_filter_view.js"></script>
<script type="text/javascript" src="assets/js/hosting/subscription_view.js"></script>
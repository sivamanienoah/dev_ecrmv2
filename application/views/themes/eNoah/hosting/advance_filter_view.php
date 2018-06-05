
<div id="content">
    <div class="inner hosting-section">
        <?php
        if ($this->session->userdata('accesspage') == 1) {
            if (!empty($hosts)) {
                if ($hosts == 'HOSTS') {
                    echo '<form action="dns/submit" method="post">';
                    echo'<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '" />';
                    echo '<select name="hostings" class="textfield" style="width:298px;">';
                    foreach ($hosting as $key => $val) {
                        echo '<option value="' . $val['hostingid'] . '">' . $val['domain_name'] . '</option>';
                    }
                    echo '</select>';

                    echo '<div class="buttons">
				  <button type="submit" name="update_dns" class="positive">Edit DNS</button>
				  <button type="submit" name="update_hosting" class="positive">Edit Hosting</button></div>
				  </form>';
                }
            } else {
                ?>      

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
                        if (is_array($accounts) && count($accounts) > 0) {
                            foreach ($accounts as $account) {
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
            <?php
        }
        ?>
        </div>
    <?php
} else {
    echo "You have no rights to access this page";
}
?>
</div>
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/data-tbl.js"></script>
<script type="text/javascript" src="assets/js/hosting/hosting_view.js"></script>

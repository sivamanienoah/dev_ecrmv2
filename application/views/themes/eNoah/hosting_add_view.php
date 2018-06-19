<?php
require (theme_url() . '/tpl/header.php');
$p = array();
if (!empty($packageid_fk)) {
    foreach ($packageid_fk as $val) {
        $k = $val['packageid_fk'];
        $p[$k] = $val['due_date'];
    }
}
$usernme = $this->session->userdata('logged_in_user');

//echo '<pre>'; print_r($subscription_types);
?>
<style type="text/css">
    #domain-expiry-date 
    {
        display:none;
    }
</style>
<link rel="stylesheet" href="assets/css/chosen.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="assets/js/crm.js?q=13"></script>
<script type="text/javascript" src="assets/js/chosen.jquery.js"></script>
<input type="hidden" class="hiddenUrl"/>
<div id="content">
    <div class="inner">
        <?php if (($this->session->userdata('add') == 1 && $this->uri->segment(3) != 'update') || (($this->session->userdata('edit') == 1) && ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))))) { ?>
            <form action="<?php echo $this->uri->uri_string() ?>" method="post">

                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                <h2><?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'New' ?> Subscription Details</h2>
                <?php if (!$this->input->post('domain_name') && $this->uri->segment(3) != 'update') { ?>
                    <!--p class="notice">If this is a new customer, please be sure to <a href="#" class="modal-new-cust" >add the customer</a> to the database before adding the hosting account.</p-->
                <?php } ?>
                <?php if ($this->validation->error_string != '') { ?>
                    <div class="form_error">
                        <?php echo $this->validation->error_string ?>
                    </div>
                <?php } ?>
                <p>All mandatory fields marked * must be filled in correctly.</p>
                <table class="layout">
                    <tr>
                        <td width="150">Customer Name: *</td>
                        <td width="300">
                            <input type="text" name="customer_name" id="cust_name" value="<?php echo (isset($customer_name)) ? $customer_name : '' ?>" class="textfield width200px" /> 
                            <input type="hidden" name="customer_id" id="cust_id" value="<?php echo (isset($customer_id)) ? $customer_id : '' ?>" />
                        </td>
                    </tr>


                    <tr>
                        <td>Subscription Types: *</td>
                        <td>
                            <select name="subscriptions_type_id_fk" class="textfield width200px">
                                <option value="">Select Subscription Type</option>
                                <?php
                                foreach ($subscription_types as $listSubscriptionTypes) {
                                    $selected = ($this->validation->subscriptions_type_id_fk == $listSubscriptionTypes['subscriptions_type_id']) ? ' selected="selected"' : '';
                                    ?>
                                    <option value="<?php echo $listSubscriptionTypes['subscriptions_type_id'] ?>"<?php echo $selected ?>><?php echo $listSubscriptionTypes['subscriptions_type_name'] ?></option>
                                <?php } ?>
                            </select> 
                        </td>
                    </tr>



                    <tr>
                        <td>Subscription Name: *</td>
                        <td><input type="text" name="domain_name" value="<?php echo $this->validation->domain_name ?>" class="textfield width200px required" /> <br> (Example: www.google.com)</td>
                    </tr>
                    <tr>
                        <td>Subscription Management:</td>
                        <td>
                            <input type="radio" name="domain_mgmt" value="ENOAH"<?php echo ((!isset($_POST['domain_mgmt']) && !is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'ENOAH')) ? ' checked="checked"' : '' ?> /> eNoahiSolution &nbsp;&nbsp;
                            <input type="radio" name="domain_mgmt" value="CM"<?php echo ((!isset($_POST['domain_mgmt']) && is_null($this->validation->domain_expiry)) || (isset($_POST['domain_mgmt']) && $_POST['domain_mgmt'] == 'CM')) ? ' checked="checked"' : '' ?> /> Client Managed &nbsp;&nbsp;
                        </td>
                    </tr>




                    <tr id="domain-expiry-date">
                        <td>Subscription Expiry Date: *</td>
                        <td><input type="text" name="domain_expiry" value="<?php echo $this->validation->domain_expiry ?>" class="textfield width200px pick-date" /> </td>

                    </tr>
                    <tr>
                        <td>Subscription Status: *</td>
                        <td>
                            <select name="domain_status" class="textfield width200px">
                                <?php
                                foreach ($this->login_model->cfg['domain_status'] as $key => $value) {
                                    $selected = ($this->validation->domain_status == $key) ? ' selected="selected"' : '';
                                    ?>
                                    <option value="<?php echo $key ?>"<?php echo $selected ?>><?php echo $value ?></option>
                                <?php } ?>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td>Package Name: *</td>
                        <td>
                            <select name="packageid_fk[]" id="pack_name" class="textfield" size=6 multiple=multiple style="width:300px;">
                                <option value="">Select Package</option> 
                                <?php
                                if (!empty($package)) {
                                    foreach ($package as $val) {
                                        if (!empty($p[$val['package_id']])) {
                                            $s = ' selected="selected"';
                                            if (strtotime($p[$val['package_id']]) > 0)
                                                $k = ' - (' . date('d-m-Y', strtotime($p[$val['package_id']])) . ')';
                                            else
                                                $k = '';
                                        } else {
                                            $s = '';
                                            $k = '';
                                        }
                                        echo '<option value="' . $val['package_id'] . '"' . $s . '>' . $val['package_name'] . $k . '</option>';
                                    }
                                }
                                ?>
                            </select> 
                        </td>
                        <td id="showerrmsg" class="dialog-err"></td>
                    </tr>
                    <tr>
                        <td>Hosting Expiry Date:</td>
                        <td><input type="text" name="expiry_date" value="<?php echo $this->validation->expiry_date ?>" class="textfield width200px pick-date" /> </td>

                    </tr>
                    <tr>
                        <td>SSL:</td>
                        <td>
                            <?php foreach ($this->login_model->cfg['domain_ssl_status'] as $key => $value) { ?>
                                <input type="radio" name="ssl" value="<?php echo $key ?>"<?php echo ($this->validation->ssl == $key) ? ' checked="checked"' : '' ?> /> <?php echo $value ?> &nbsp;&nbsp;
                            <?php } ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Other information:</td>
                        <td><textarea name="other_info" class="textfield width200px"><?php echo $this->validation->other_info ?></textarea></td>

                    </tr>
                     <tr>
                        <td class="project-stake-members">Subscription Owner</td>
                      
                        <td><select  class="chzn-select" data-placeholder="Select Owners"  id="sub_owner" name="sub_owner">
                                <?php
                                if (!empty($all_users)):
                                    $usid = $this->session->userdata('logged_in_user');
                                    ?>
                                    <!--option value=""></option-->
                                    <?php
                                    foreach ($all_users as $pms):
                                        $selected = '';
                                        if($edit_sub_owner == $pms['userid']){
                                            $selected = 'selected="selected"';
                                        }
                                        elseif($usid['userid'] == $pms['userid'] && $this->uri->segment(3) != 'update' ){
                                            $selected = 'selected="selected"';
                                        }else{
                                            $selected = '';
                                        }
                                        ?>
                                        <option <?php echo $selected; ?> value="<?php echo $pms['userid'] ?>"><?php echo $pms['first_name'] . ' ' . $pms['last_name'] . '-' . $pms['emp_id']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>

                   
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <div class="buttons">
                                <button type="submit" name="update_customer" class="positive" onclick="return validatePackname()">
                                    <?php echo ($this->uri->segment(3) == 'update' && is_numeric($this->uri->segment(4))) ? 'Update' : 'Add' ?> Account
                                </button>
                            </div>
                            <div class="buttons">
                                <button type="button" class="negative" onclick="location.href = '<?php echo base_url(); ?>hosting'">
                                    Cancel
                                </button>
                            </div>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
            <?php
        } else {
            echo "You have no rights to access this page";
        }
        ?>
    </div>
</div>
<?php require (theme_url() . '/tpl/footer.php'); ?>
<script type="text/javascript">
    hosting_userid = "<?php echo $usernme['userid']; ?>";
</script>
<script type="text/javascript" src="assets/js/hosting/hosting_add_view.js"></script>
<script>
    function validatePackname() {
        if (document.getElementById('pack_name').value == '') {
            $('#showerrmsg').empty();
            $('#showerrmsg').show();
            $('#showerrmsg').html('Select any option');
            return false;
        }
    }
</script>
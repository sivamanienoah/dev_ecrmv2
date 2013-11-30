<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dns extends crm_controller {

	function Dns()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model('dns_model');
        $this->load->library('validation');
	}
	
	function index($limit = 0, $search = false)
	{
		redirect('hosting/');
	}
	
	function go_live($id = false)
	{
		if(!$id || $id==0 || $id=='') redirect('hosting/');
		$account=$this->dns_model->get_dns($id);
		//echo "<pre>"; print_r($account); exit;
		$account_hosting=$this->dns_model->get_hosting($id);
		$domain_name=$account_hosting[0]['domain_name'];
		if(count($account)<=0){ 
			$account[0]['dns']='insert';
			$account[0]['hostingid']=$id;
			$account[0]['host_location']='';
			$account[0]['host_status']='';
			$account[0]['login_url']='';
			$account[0]['login']='';
			$account[0]['registrar_password']='';
			$account[0]['tech_contact']='';
			$account[0]['tech_email']='';
			$account[0]['tech_name']='';
			$account[0]['client_contact']='';
			$account[0]['client_email']='';
			$account[0]['client_name']='';
			$account[0]['email']='';
		}
		else{ $account[0]['dns']='update';} 
		foreach($account as $key=>$val){
			$a_temp=$val;
		}
		$account=$a_temp;unset($a_temp);
		$account['domain_name']=$account_hosting[0]['domain_name'];
		$rules['host_location'] = "trim|required";
		$rules['host_status'] = "trim|required";
		/*$rules['login_url'] = "trim|required";
		$rules['login'] = "trim|required";
		$rules['registrar_password'] = "trim|required";
		$rules['email'] = "trim|valid_email";
		$rules['go_live_date'] = "trim|required|callback_is_valid_date";
		$rules['cur_smtp_setting'] = "trim|required";
		$rules['fut_smtp_setting'] = "trim|required";
		$rules['cur_pop_setting'] = "trim|required";
		$rules['fut_pop_setting'] = "trim|required";
		$rules['cur_dns_primary_ip'] = "trim|required";
		$rules['fut_dns_primary_ip'] = "trim|required";
		$rules['cur_dns_secondary_ip'] = "trim|required";
		$rules['fut_dns_secondary_ip'] = "trim|required";
		$rules['cur_dns_primary_url'] = "trim|required";
		$rules['fut_dns_primary_url'] = "trim|required";
		$rules['cur_dns_secondary_url'] = "trim|required";
		$rules['fut_dns_secondary_url'] = "trim|required";*/
	
		$this->validation->set_rules($rules);
		
		$fields['hostingid'] = $id;
		$fields['host_location'] = "Host Location";
		$fields['host_status'] = "Host Status";
		$fields['login_url'] = "Login Url";
		$fields['login'] = "Login";
        $fields['registrar_password'] = "Registrar password";
		$fields['tech_contact'] = "Tech Contact Number";
		$fields['tech_email'] = "Tech Email-id";
		$fields['tech_name'] = "Tech Name";
		$fields['client_contact'] = "Client Contact Number";
		$fields['client_email'] = "Client Email-id";
		$fields['client_name'] = "Client Name";
		$fields['email'] = "Email";
		$fields['dns'] = "dns";
		//$fields['go_live_date'] = "Go Live Date";

		$fields['email_change'] = 'Email Change';
		$fields['client_notified'] = "Client Notified";
		$fields['cur_smtp_setting'] = 'SMTP setting';
		$fields['cur_pop_setting'] = "POP Setting";
		$fields['cur_webmail_url'] = "webmail Url";
		$fields['cur_controlpanel_url'] = "control panel url";
        $fields['cur_statspanel_url'] = "statspanel url";
		$fields['cur_dns_primary_url'] = "DNS Primary URL";
		$fields['cur_dns_primary_ip'] = "DNS Primary IP";
		$fields['cur_dns_secondary_url'] = "DNS secondary URL";
		$fields['cur_dns_secondary_ip'] = "DNS secondary IP";
		$fields['cur_record_setting'] = "Record Setting";
		$fields['cur_mx_record'] = "MX Record Setting";
		/* $fields['fut_smtp_setting'] = 'SMTP setting';
		$fields['fut_pop_setting'] = "POP Setting";
		$fields['fut_webmail_url'] = "webmail Url";
		$fields['fut_controlpanel_url'] = "control panel url";
		$fields['fut_statspanel_url'] = "statspanel url";
		$fields['fut_dns_primary_url'] = "DNS Primary URL";
		$fields['fut_dns_primary_ip'] = "DNS Primary IP";
		$fields['fut_dns_secondary_url'] = "DNS secondary URL";
		$fields['fut_dns_secondary_ip'] = "DNS secondary IP";
		$fields['fut_record_setting'] = "Login";
        $fields['fut_mx_record'] = "Registrar password";
		$fields['date_handover'] = "Tech Contact Number"; */
		$this->validation->set_fields($fields);
        
        $this->validation->set_error_delimiters('<p class="form-error">', '</p>');
        
		if ($this->validation->run() == false) {
			$this->load->view('dns_view', $account);
		} else {
			foreach($fields as $key => $val) {
				if($key=='dns') continue;
                $update_data[$key] = $this->input->post($key);
            }
			$client_notified=$update_data['client_notified'];
			//echo $client_notified; exit;
			unset($update_data['client_notified']);
			//$update_data['go_live_date']=$this->date_convert($update_data['go_live_date']);
			//$update_data['date_handover']=$this->date_convert($update_data['date_handover']);
			
			if($client_notified=='on'){
				/*$data_arr=array('current SMTP'=>$update_data['cur_smtp_setting'],
								'current POP setting'=>$update_data['cur_pop_setting'],
								'current webmail URL'=>$update_data['cur_webmail_url'],
								'current control Panel URL'=>$update_data['cur_controlpanel_url'],
								'current Statspanel URL'=>$update_data['cur_statspanel_url'],
								'current Record Setting'=>$update_data['cur_record_setting'],
								'current MX Record Setting'=>$update_data['cur_mx_record'],
								'future SMTP'=>$update_data['fut_smtp_setting'],
								'future POP setting'=>$update_data['fut_pop_setting'],
								'future webmail URL'=>$update_data['fut_webmail_url'],
								'future control Panel URL'=>$update_data['fut_controlpanel_url'],
								'future Statspanel URL'=>$update_data['fut_statspanel_url'],
								'future Record Setting'=>$update_data['fut_record_setting'],
								'future MX Record Setting'=>$update_data['fut_mx_record']
							);*/
				//$data_arr=array('current SMTP'=>'192.168.1.73');
				//$this->dns_model->send_mail($data_arr);
			}
			
            if ($this->input->post('dns') == 'update') {
				if ($this->dns_model->update_dns($id, $update_data)) {
                    $this->session->set_flashdata('confirm', array('DNS Details Updated!'));
                    redirect('hosting/');
                }
            } else {
                if ($newid = $this->dns_model->insert_dns($update_data)) {
                    $this->session->set_flashdata('confirm', array('DNS Details Updated!'));
                    redirect('hosting/');
                }
            }
		}
	}
	
	function is_valid_date($date)
    {
		$chkdate=$this->date_convert($date);
		if ($chkdate && strtotime($chkdate) > time())
			return true;
		$this->validation->set_message('is_valid_date', 'The date needs to be in a correct format (dd-mm-yyyy) and the date should be a future date.');
		return FALSE;
    }
	
	public function date_convert($str){
		$mdate = explode('-', $str);
		$str = $mdate[2].'-'.$mdate[1].'-'.$mdate[0];
		return $str;
	}
	
	function jobs($lead_id=''){
		if($lead_id<=0) redirect('hosting/');
		$sql="SELECT * FROM `".$this->cfg['dbpref']."hosting` as H, `".$this->cfg['dbpref']."leads` as J WHERE J.lead_id={$lead_id} AND H.custid_fk=J.custid_fk ORDER BY H.domain_name";
		$rows = $this->db->query($sql);
		$data['hosting']=$rows->result_array();
		$data['jobs']='JOBS';
		$this->load->view('dns_view', $data);
	}
	
	function submit(){
		if($_POST['hostings']=='') redirect('hosting/');
		if(isset($_POST['update_dns'])) redirect('dns/go_live/'.$_POST['hostings']);
		else if(isset($_POST['update_hosting'])) redirect('hosting/add_account/update/'.$_POST['hostings']);
		else redirect('hosting/');
	}
	
}
?>
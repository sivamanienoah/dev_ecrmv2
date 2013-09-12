<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public $cfg;
	
	public $cust_data = array(
						'first_name'		=> '',
						'last_name'			=> '',
						'position_title'	=> '',
						'company'			=> '',
						'add1_line1'		=> '',
						'add1_line2'		=> '',
						'add1_suburb'		=> '',
						'add1_state'		=> '',
						'add1_postcode'		=> '',
						'add1_country'		=> '',
						'phone_1'			=> '',
						'phone_2'			=> '',
						'phone_3'			=> '',
						'phone_4'			=> '',
						'email_1'			=> '',
						'email_2'			=> '',
						'email_3'			=> '',
						'email_4'			=> '',
						'www_1'				=> '',
						'www_2'				=> '',
						'comments'			=> ''
					);
	
	public function __construct()
	{
		parent::__construct();
		$this->cfg = $this->config->item('crm');
	}
	
	public function index() {	}
	
	public function export_contact_csv()
	{
		$fields = array_keys($this->cust_data);
		
		$this->db->select(join(',', $fields));
		$data = $this->db->get($this->cfg['dbpref'].'customers');
		
		$file_name = time() . '-' . mt_rand(1111, 9999) . '.csv';
		$file_path = rtrim(dirname(FCPATH), '/') . '/vps_temp_data/' . $file_name;
		
		$fp = fopen($file_path, 'w+');
		
		fputcsv($fp, $fields, ',', '"');
		
		if ($data->num_rows > 0)
		{
			$rows = $data->result_array();
			foreach ($rows as $row)
			{
				fputcsv($fp, $row, ',', '"');
			}
		}
		
		fclose($fp);
		
		if (is_file($file_path))
		{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"" . $file_name ."\";");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . @filesize($file_path));
			readfile($file_path);
		}
		
	}
	
	public function add_contact()
	{
		$details = $this->cust_data;
		
		if (isset($_GET))
		{
			foreach ($details as $k => $v)
			{
				if (isset($_GET[$k]))
				{
					$details[$k] = trim(urldecode($_GET[$k]));
				}
			}
			
			$errors = array();
			$status = 'OK';
			
			if ($details['first_name'] == '') $errors['fn'] = 'First name is empty';
			
			if ($details['last_name'] == '') $errors['ln'] = 'Last name is empty';
			
			if ($details['company'] == '') $errors['cn'] = 'Company field is empty';
			
			if ($details['phone_1'] == '' && $details['phone_2'] == '' && $details['phone_3'] == '' && $details['phone_4'] == '') $errors['pn'] = 'All phone numbers cannot be empty';
			
			if (! preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', $details['email_1'])) $errors['ie'] = 'Email address is invalid';
			
			if ($this->existing_email($details['email_1'])) $errors['ee'] = 'Email address already on the database';
			
			# replace API strings
			$search[0] = '{CURR_DATE}';
			
			$replace[0] = date('Y-m-d H:i');
			
			$details['comments'] = str_replace($search, $replace, $details['comments']);
			# end string replacement
			
			if (count($errors) < 1)
			{	
				if (!isset($_GET['testing'])) # are we debugging?
				{
					$this->db->insert($this->cfg['dbpref'].'customers', $details);
					if (isset($_GET['realestate']))
					{
						$insert_id = $this->db->insert_id();
						$this->db->insert($this->cfg['dbpref'].'cust_cat_join', array('custid_fk' => $insert_id, 'custcatid_fk' => 4));
					}	
				}
			}
			else if (count($errors) == 1 && isset($errors['ee']) && $this->empty_first_name($details['email_1'])) # only the email exists, update the rest
			{
				$this->db->update($this->cfg['dbpref'].'customers', $details, array('email_1' => $details['email_1'], 'first_name' => ''));
				$status = 'UPDATED';
			}
			else
			{
				$status = 'ERROR';
			}
			
			$json['details'] = "Name : {$details['first_name']} {$details['last_name']}, Company : {$details['company']}, Phone : {$details['phone_1']}, Email : {$details['email_1']}";
			$json['errors'] = $errors;
			$json['status'] = $status;
			
			echo json_encode($json);
		}
	}
    
	public function existing_email($email)
	{
		$q = $this->db->get_where($this->cfg['dbpref'].'customers', array('email_1' => $email));
		if ($q->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function empty_first_name($email)
	{
		$q = $this->db->get_where($this->cfg['dbpref'].'customers', array('email_1' => $email));
		if ($q->num_rows() > 0)
		{
			$data = $q->result();
			if (trim($data[0]->first_name) == '')
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
}

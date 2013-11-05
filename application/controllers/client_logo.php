<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class Client_logo extends crm_controller
{
	public $cfg;
	public $userdata;
	
	public function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();
		$this->load->model('client_logo_model');
		$this->load->database();
		$this->load->helper('url');
		$this->userdata = $this->session->userdata('logged_in_user');
	}
 
	public function index()
	{
		$data['page_heading'] = "Upload Logo";
		if ($this->client_logo_model->get_logo()) {
			$data['get_client_logo'] = $this->client_logo_model->get_logo();
		}
		$this->load->view('client_logo_view', $data);
	}
	
	public function cliLogoUp($url)
	{
		error_reporting(E_ERROR);
		
		$json['error'] = '';
		$json['msg'] = '';
		
		$f_dir = UPLOAD_PATH;
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$f_dir = $f_dir .'client_logo/';
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		$logo_name = time().'_'.$_FILES['logo_file']['name'];
		
		if (isset($_FILES['logo_file']) && is_uploaded_file($_FILES['logo_file']['tmp_name']))
		{
			$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $logo_name);
			
			$image_info = getimagesize($_FILES["logo_file"]["tmp_name"]);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			
			$allowed_ext = array('image/jpeg','image/gif','image/png','image/jpg');
			$res_ext = $_FILES["logo_file"]["type"]; 
			
			if ((in_array($res_ext, $allowed_ext)) && ($image_width<=300) && ($image_height<=50)) {

				// full path
				$full_path = $f_dir . '/' . $f_name;
				if (is_file($full_path))
				{
					$f_name = time() . $f_name;
					$full_path = $f_dir . '/' . $f_name;
				}
				
				if(move_uploaded_file($_FILES['logo_file']['tmp_name'], $full_path)) {
					
					$userdata = $this->session->userdata('logged_in_user');
					
					// $query = "INSERT INTO ".$this->cfg['dbpref']."client_logo (id, filename) VALUES ('','".$f_name."')";
					// $q = $this->db->query($query);
					$url = addslashes($url);
					$url = str_replace("-","/", $url);
					$data['res'] = $this->client_logo_model->insert_file($f_name, $url);
					$json['img_id'] = $data['res'];

				}
				
				$json['error'] = FALSE;
				$json['msg'] = "File successfully uploaded!";
				$json['file_name'] = $f_name;			
				$json['file_size'] = $out;
			}
			else {
				$json['error'] = TRUE;
				$json['msg'] = "You uploaded a file type that is not allowed!.\nWidth not more than 300px!.\nHeight not more than 50px!.";
			}
			
		}
		echo json_encode($json);
	}
	
	public function del_client_logo()
	{
		$result = $this->client_logo_model->reset_client_logo();
		echo json_encode($result);
		exit;
	}
	
}
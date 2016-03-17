<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Forecast
 *
 * @class 		sales_forecast
 * @extends		crm_controller (application/core/CRM_Controller.php)
 * @parent      -
 * @Menu        Parent - Sales Forecast
 * @author 		eNoah
 * @Controller
 */

class Dms extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	/*
	*@Constructor
	*@sales_forecast
	*/
	public function __construct()
	{
        parent::__construct();
        $this->login_model->check_login();
		$this->load->model('dms_model');
		$this->userdata = $this->session->userdata('logged_in_user');
    }
    
	/*
	*@Get practice List
	*@Method index
	*/
    public function index()
	{
		$filter = array();
        $data['page_heading'] = 'Collateral';
        $data['hfolder_id']   = 0;
		$this->load->view('dms/dms_view', $data);
    }
	
	/**
	 * @method download_file()
	 * @param $file_name
	 */
	function download_dms_file($file_name)
	{
		$this->load->helper('download');
		$file_dir = UPLOAD_PATH.'dms_files/'.$file_name;
		$data 	  = file_get_contents($file_dir); // Read the file's contents
		$name	  = $file_name;
		force_download($name, $data); 
	}
	
	/**
	 * @method delete_files()
	 * @param post values
	 */
	public function delete_dms_files(){
		// echo "<pre>"; print_r($_POST); exit;
		$delData            = real_escape_array($this->input->post());
		$json	            = array();
		
		if(!empty($delData['ff_id'])) {
			$json['folder_parent_id'] = $delData['ff_id'];
		} else {
			$get_parent_folder_id = $this->dms_model->getParentFfolderId($parent=0);
			$json['folder_parent_id'] = $get_parent_folder_id['folder_id'];
		}
		if(!empty($delData['del_folder'])) {
			$del_folder = rtrim($delData['del_folder'], ",");
			$del_folder = explode(',', $del_folder);
		}
		if(!empty($del_folder)) {
			$res = $this->del_folder_all($del_folder);
			$json['folder_del_status'] = $res;
		}
		if(!empty($delData['del_files'])) {
			$del_files = rtrim($delData['del_files'], ",");
			$del_files = explode(',', $del_files);
		}
		if(!empty($del_files)) {
			$file_res = $this->del_file_all($del_files);
			$json['file_del_status'] = $file_res;
		}
		if(empty($json['folder_del_status']))
		$json['folder_del_status'] = "no_folder_del";
		if(empty($json['file_del_status']))
		$json['file_del_status'] = "no_file_del";
		echo json_encode($json); 
		exit;
	}
	
	/**
	 * @method del_file_all()
	 * @param $array_file_id
	 */
	public function del_file_all($array_file_id) 
	{
		$f_data = real_escape_array($this->input->post());
		
		foreach($array_file_id as $file_id) {
			$condn 		   = array("file_id"=>$file_id);
			$get_file_data = $this->dms_model->get_record("*", "dms_files", $condn);
		
			$fcpath = UPLOAD_PATH; 
			$f_dir  = $fcpath.'dms_files/'.$get_file_data['files_name'];
				
			if (isset($f_dir))
			{
				if (@unlink($f_dir))
				{
					$wh_condn = array('file_id' => $file_id);
					$del_file = $this->dms_model->delete_row('dms_files', $wh_condn);
										
					$logs['jobid_fk']	   = 0;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $get_file_data['files_name'].' file(collateral) is deleted.';
					$logs['attached_docs'] = $get_file_data['files_name'];
					$insert_logs 		   = $this->dms_model->insert_row('logs', $logs);
					
					$res[] 		  		   = $get_file_data['files_name'].' is deleted.';
				} else {
					$res[]		  		   = $get_file_data['files_name'].' file cannot be deleted.';
				}
			} else {
				$res[]		  		       = $get_file_data['files_name'].' file cannot be deleted.';
			}
		}
		return $res;
	}
	
	/**
	 * @method del_folder_all()
	 * @param $array_folder_id
	 */
	public function del_folder_all($array_folder_id) {
		$res = array();
		foreach($array_folder_id as $folder_id) {
		
			$condn		   = array("folder_id"=>$folder_id);
			$get_file_data = $this->dms_model->get_record("*", "dms_file_management", $condn);
		
			$fm_condn            = array('parent_id'=>$folder_id);
			$folder_check_status = $this->dms_model->checkStatus('dms_file_management', $fm_condn);
			
			if($folder_check_status) {
				$lf_condn          = array('folder_id'=>$folder_id);
				$file_check_status = $this->dms_model->checkStatus('dms_files', $lf_condn);
				if($file_check_status) {
					//Deleting the folder
					$del_condn = array('folder_id'=>$folder_id);
					$del_file  = $this->dms_model->delete_row('dms_file_management', $del_condn);
					if($del_file) {
						
						$logs['jobid_fk']	   = 0;
						$logs['userid_fk']	   = $this->userdata['userid'];
						$logs['date_created']  = date('Y-m-d H:i:s');
						$logs['log_content']   = $get_file_data['folder_name'].' folder(collateral) is deleted.';
						$logs['attached_docs'] = $get_file_data['folder_name'];
						$insert_logs 		   = $this->dms_model->insert_row('logs', $logs);
					
						$res[] = $get_file_data['folder_name'].' folder is deleted.';
					} else {
						$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';
					}
				} else {
					$res[] = $get_file_data['folder_name'].' folder having files. So it cannot be deleted.';
				}
			} else {
				$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';
			}
		}
		return $res;
	}
	
	/**
	 * @method get_project_files()
	 * @param $job_id
	 */
	public function get_dms_files($fparent_id=0) {
		
		$this->load->helper('custom_helper');
		$dmsAdminAccess = get_dms_access($type=0);
		$dmsUserAccess  = get_dms_access($type=1);
	
		$userdata = $this->session->userdata('logged_in_user');
		
		$dmsAccess = 0;
		if ($userdata['userid'] == 59 || $dmsAdminAccess == 1)
		$dmsAccess = 1;

		//intial step - Showing 1st child folders and root files
		$get_data = $this->dms_model->getDmsData($fparent_id);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'dms_files/';
		
		if(!empty($get_data)) {
			foreach($get_data as $res) {
				// CHECK ACCESS PERMISSIONS START HERE //
				if($dmsAccess != 1) {
					$get_permissions = $this->check_access_permissions($res['folder_id'], $this->userdata['userid']);
					$dmsFolderAccess = $get_permissions['access_type'];
				} else {
					$dmsFolderAccess = 1;
				}
				
				if($dmsAccess == 1 || $dmsFolderAccess >= 1) { //check_permission
					if($res['folder_id'] == $fparent_id) {
						$get_files = $this->dms_model->getFiles($res['folder_id']);
						// echo $this->db->last_query(); exit;
					} else {
						$data['file_array'][] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
					}
				} //check_permission
			}
		}
		// echo "<pre>"; print_r($data); exit;
		if(!empty($get_files)) {
			foreach($get_files as $files) {
				$data['file_array'][] = $files['files_name']."<=>".$files['files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
			}
		}

		echo $this->load->view('dms/dms_view_grid', $data);
	}
	
	public function check_access_permissions($folder_id, $user_id)
	{
		$this->db->select('access_type');
	    $this->db->from($this->cfg['dbpref'] . 'dms_folder_access');
	    $this->db->where(array('folder_id'=>$folder_id, 'user_id'=>$user_id));
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->row_array();		
	}
	
	/*
	*@method getBreadCrumbs
	*/
	function getDmsBreadCrumbs($parent, $res) 
	{
		$data = $this->dms_model->getDmsBreadCrumbDet($parent);
		
		if(!empty($data)) {
			foreach($data as $rec) {
				$res[$rec['folder_id']] = $rec['folder_name'];
				$parent_id = $rec['parent_id'];
				if( $parent_id !=0 ) {
					$this->getDmsBreadCrumbs($parent_id, $res);
				} else {
					$bc = '<span>Files</span> >> <a href="javascript:void(0)" onclick="getDmsData(0); return false;">Root</a>';
					$res = array_reverse($res, true);
					
					foreach($res as $fid=>$fnm) {
						if($bc!='') {
							$bc.=' >> ';
						}
						$bc.='<a href="javascript:void(0)" onclick="getDmsData('.$fid.'); return false;">'.$fnm.'</a>';
					}
				}
			}
		} else {
			$bc = '<span>Files</span> >> <a href="javascript:void(0)" onclick="getDmsData(0); return false;">Root</a>';
		}
		echo $bc; exit;
	}
	
	/*
	*@method getFolderActions
	*/
	function getFolderActions($folder_id) 
	{
		$data['folder_id']  = $folder_id;
		$data['user_id']    = $this->userdata['userid'];
		
		$this->load->helper('custom_helper');
		$dmsAdminAccess = get_dms_access($type=0);
		$dmsUserAccess  = get_dms_access($type=1);
		
		$get_permissions 		 = $this->check_access_permissions($folder_id, $this->userdata['userid']);
		$data['dmsFolderAccess'] = !empty($get_permissions['access_type']) ? $get_permissions['access_type'] : 0;
		
		echo $this->load->view("dms/dms_actions.php", $data, true);
		exit;
	}
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function file_upload($filefolder_id)
	{
		$this->load->library('upload');
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['ajax_file_uploader']['name']);

		//creating files folder name
		$f_dir = UPLOAD_PATH.'dms_files/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		
		$returnUpload = array();
		$json  = array();
		if(!empty($_FILES['ajax_file_uploader']['name'][0])) {
			if ($this->upload->do_multi_upload("ajax_file_uploader")) { 
			   $returnUpload  = $this->upload->get_multi_upload_data();
			   $json['error'] = FALSE;
			   $json['msg']   = "File successfully uploaded!";
			   $i = 1;
			   if(!empty($returnUpload)) {
				  foreach($returnUpload as $file_up) {
					$dms_files['files_name']	   = $file_up['file_name'];
					$dms_files['files_created_by'] = $this->userdata['userid'];
					$dms_files['files_created_on'] = date('Y-m-d H:i:s');
					$dms_files['folder_id'] 	   = $filefolder_id; //get here folder id from file_management table.
					$insert_file				   = $this->dms_model->insert_row('dms_files', $dms_files);
					
					$logs['jobid_fk']	   = 0;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' file(collateral) is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->dms_model->insert_row('logs', $logs);
					$i++;
				  }
			   }
			} else {
				$this->upload_error = strip_tags($this->upload->display_errors());
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
				// return $this->upload_error;						
				// exit;
			}
		}
		echo json_encode($json); exit;
	}
	
	/**
	 * @method get_folder_tree_struct()
	 * 
	 */
	public function get_folder_tree_struct() 
	{
		$data    = real_escape_array($this->input->post());
		$result  = $this->dms_model->get_tree_file_list($parentId=0,$counter=0);
		$res     = array();
		$res['fparent_id']           = $data['fparent_id'];
		$res['project_members_list'] = '';
		$res['tree_struture'] .= "<option value='0'>Root</option>";
		foreach($result as $fid=>$fname){
			if(($fid == $data['fparent_id']) || ($fid == $data['parent_folder_id'])) {
				$selected = 'selected=selected';
			} else {
				$selected = '';
			}

			$res['tree_struture'] .= "<option value='".$fid."' ".$selected." >".$fname."</option>"; 
		}
		 
		echo json_encode($res);
		exit;
	}
	
	/**
	 * @method addFolders()
	 * @mapping files to another folder
	 */
	public function addFolders() {
		$af_data   = real_escape_array($this->input->post());
		
		$user_data = $this->session->userdata('logged_in_user');
		$htm = array();
		$htm['err'] = "false";
		
		$this->load->helper('lead_helper'); 

		$af_condn            = array('folder_name'=>$af_data['new_folder'],'parent_id'=>$af_data['add_destiny']);
		$folder_check_status = $this->dms_model->createFolderStatus('dms_file_management', $af_condn);
		// if(($folder_check_status==0) && ($is_root != 'root')){
		if(($folder_check_status==0)){
			$add_data = array('folder_name'=>$af_data['new_folder'],'parent_id'=>$af_data['add_destiny'],'created_by'=>$this->userdata['userid'],'created_on'=>date('Y-m-d H:i:s'));
			$res_insert = $this->dms_model->insert_row('dms_file_management', $add_data);		 
			
			if(!$res_insert) {
				$htm['err']     = "true";			
				$htm['err_msg'] = 'Folder cannot be added.';
			}
		} else {
			$res_insert     = FALSE;
			$htm['err']     = "true";
			$htm['err_msg'] = "Folder Name already exists (Or) you dont have access to create.";
		}
		
		if($res_insert){
			$log_contents  = array('jobid_fk'=>0,'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$af_data['new_folder'].' folder(collateral) is Added.','attached_docs'=>$af_data['new_folder']);	
			$insert_logs   = $this->dms_model->insert_row('logs', $log_contents);
						 
			$htm['af_msg'] = '<span class="ajx_success_msg"><h5>'.$af_data['new_folder'].' has been Added</h5></span>';
		} else {
			$htm['af_msg'] = '<span class="ajx_failure_msg"><h5>'.$err_msg.'</h5></span>';
		}
		// $htm['af_reload'] = $af_data['afparent_id'];
		$htm['af_reload'] = $af_data['add_destiny'];
		echo json_encode($htm);
		exit;
	}
	
	/**
	 * @method get_moveall_file_tree_struct()
	 * 
	 */
	public function get_moveall_file_tree_struct() 
	{
		$data     = real_escape_array($this->input->post());
		$userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('custom_helper');
		// echo "<pre>"; print_r($data); die;

		$mvfolder = array();
		
		if(!empty($data['mv_folder'])) {
			$mv_folder = rtrim($data['mv_folder'], ",");
			$mvfolder = explode(',', $mv_folder);
		}

		$result  = $this->dms_model->get_tree_file_list_omit($parentId=0,$counter=0,$mvfolder);
		$res     = '';
		
		$dmsAdminAccess = get_dms_access($type=0);
		
		$dmsAccess = 0;
		if ($userdata['userid'] == 59 || $dmsAdminAccess == 1)
		$dmsAccess = 1;
		
		foreach($result as $fid=>$fname) {
			// CHECK ACCESS PERMISSIONS START HERE //
			if($dmsAccess != 1) {
				$get_permissions = $this->check_access_permissions($fid, $userdata['userid']);
				$writeAccess = $get_permissions['access_type'];
			} else {
				$writeAccess = 2;
			}
			if($writeAccess == 2)			
			$res['tree_struture'] .= "<option value='".$fid."'>".$fname."</option>";
			// CHECK ACCESS PERMISSIONS END HERE //
		}
		echo json_encode($res);
		exit;
	}
	
	/**
	 * @method mapallfiles()
	 * @mapping multiple files to another folder
	 */
	public function mapallfiles() 
	{
		$madata = real_escape_array($this->input->post());
		// echo "<pre>"; print_r($madata); exit;
		
		$mov_folder = array();
		$mov_file   = array();
		if(!empty($madata['mov_folder'])) {
			$mov_folder = rtrim($madata['mov_folder'], ",");
			$mov_folder = explode(',', $mov_folder);
		}
		if(!empty($madata['mov_file'])) {
			$mov_file = rtrim($madata['mov_file'], ",");
			$mov_file = explode(',', $mov_file);
		}
		if(!empty($mov_folder)) {
			$html['res_folder'] = FALSE;
			foreach($mov_folder as $mv_fo) {
			
				$condn 		= array('folder_id' => $mv_fo);
				$updt  		= array('parent_id' => $madata['move_destiny']);
				$res_folder = $this->dms_model->update_row('dms_file_management', $condn, $updt);
				//insert_log
				if($res_folder){
					$get_info  = $this->dms_model->get_record('*', 'dms_file_management', $wh=array('folder_id'=>$mv_fo));
					$get_info1 = $this->dms_model->get_record('*', 'dms_file_management', $wh=array('folder_id'=>$madata['move_destiny']));
					$log_contents  = array('jobid_fk'=>0,'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$get_info['folder_name'].' folder has been moved to '.$get_info1['folder_name'],'attached_docs'=>$af_data['new_folder']);
					$insert_logs   = $this->dms_model->insert_row('logs', $log_contents);
				}
			}
			$html['res_folder'] = TRUE;
		} else {
			$html['res_folder'] = TRUE;
		}
		if(!empty($mov_file)) {
			$html['res_file'] = FALSE;
			foreach($mov_file as $mv_fi) {
			
				$condn = array('file_id' => $mv_fi);
				$updt  = array('folder_id' => $madata['move_destiny']);
				$res_file   = $this->dms_model->update_row('dms_files', $condn, $updt);
				//insert_log
				if($res_file){
					$get_info  = $this->dms_model->get_record('*', 'dms_files', $wh=array('file_id'=>$mv_fi));
					$get_info1 = $this->dms_model->get_record('*', 'dms_file_management', $wh=array('folder_id'=>$madata['move_destiny']));
					$log_contents  = array('jobid_fk'=>0,'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$get_info['files_name'].' file has been moved to '.$get_info1['folder_name'],'attached_docs'=>$af_data['new_folder']);
					$insert_logs   = $this->dms_model->insert_row('logs', $log_contents);
				}
			}
			$html['res_file'] = TRUE;
		} else {
			$html['res_file'] = TRUE;
		}
		if(($html['res_file']==TRUE) && ($html['res_folder']==TRUE)) {
			$htm['result'] = TRUE;
			$htm['mf_msg'] = '<span class="ajx_success_msg"><h5>Moved Successfully.</h5></span>';
		} else {
			$htm['mf_msg'] = '<span class="ajx_failure_msg"><h5>Error in File or Folder movement.</h5></span>';
		}
		$htm['mf_reload'] = $madata['move_destiny'];
		echo json_encode($htm);
		exit;
	}
	
	/**
	 * @method get_dms_folder_permissions_ui()
	 * 
	 */
	public function get_dms_folder_permissions_ui()
	{
		$data['dms_members'] 	= $this->dms_model->getDmsMembers(1);
		$data['dms_folders'] 	= $this->dms_model->get_tree_file_list_except_root();
		$data['folders_access'] = $this->dms_model->get_records($tbl='dms_folder_access', $wh_condn=array(), "", "");

		echo $this->load->view('dms/dms_folder_permissions', $data);
	}
	
	/**
	 * @method save_folder_permissions()
	 * 
	 */
	public function save_dms_folder_permissions()
	{
		$error = true;
		
		$dms_members = $this->dms_model->getDmsMembers(1);
		$dms_folders = $this->dms_model->get_tree_file_list_except_root();
		
		foreach($dms_folders as $folder_id => $folder_name) 
		{
			foreach($dms_members as $member)
			{
				$record_array = array();
				$user_id      = $member['user_id'];
				$record_array['user_id'] = $user_id;
				$input_name   = 'permission_for_'.$folder_id.'_'.$user_id;
				
				$permission = isset($_POST[$input_name]) ? $this->input->post($input_name) : 0;
									
				$record_array['updated_by']  = $this->userdata['userid'];
				$record_array['updated_on']  = date('Y-m-d H:i:s');
				$record_array['access_type'] = $permission;
				
				$exist_record_id = $this->dms_model->checkDmsIsFolderAccessRecordExist($folder_id, $user_id);
				// echo "<pre>"; print_r($exist_record_id); exit;
				if(!empty($exist_record_id)) {
					$exist_id = $exist_record_id['dms_folder_access_id'];
					$stat     = $this->dms_model->update_row('dms_folder_access', $cond = array('dms_folder_access_id'=>(int)$exist_id), $record_array);
				} else {
					$record_array['user_id']    = $user_id;
					$record_array['folder_id']  = $folder_id;
					$record_array['created_by'] = $this->userdata['userid'];
					$record_array['created_on'] = date('Y-m-d H:i:s');
					
					$ins_stat = $this->dms_model->insert_row('dms_folder_access', $record_array);
					if(!$ins_stat){
						$error = false;
					}
				}
			}

		}
		if($error == false){
			echo "Error in saving";
		} else {
			echo "true";
		}
		exit;
	}
	
	/**
	 * @method searchFile()
	 * @searching files & folder
	 */
	public function searchFile() {
		$sf_data = real_escape_array($this->input->post());		
		
		$this->load->helper('custom_helper');
		$dmsAdminAccess = get_dms_access($type=0);
		$userdata = $this->session->userdata('logged_in_user');
		$access_folders = array();
		$dmsAccess = 0;
		if ($userdata['userid'] == 59 || $dmsAdminAccess == 1)
		$dmsAccess = 1;
	
		if($dmsAccess != 1) {
			$get_folders = $this->dms_model->get_folder_ids($userdata['userid']);
			if(!empty($get_folders) && count($get_folders)>0){
				foreach($get_folders as $rec)
				$access_folders[] = $rec['folder_id'];
			}
		}
			
		$this->load->helper('file');

		//intial step - Showing 1st child folders and root files
		$get_parent_data = $this->dms_model->search_folder($access_folders, $sf_data['search_input']);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'dms_files/';
		
		$data = array();
		
		if(!empty($get_parent_data)) {
			foreach($get_parent_data as $res) {	
				$data['file_array'][] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
			}
		}
		
		$get_files = $this->dms_model->search_file($access_folders, $sf_data['search_input']);
		
		if(!empty($get_files)) {
			foreach($get_files as $files) {	
				$data['file_array'][] = $files['files_name']."<=>".$files['files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
			}
		}
		
		echo $this->load->view('dms/dms_view_grid', $data);
	}

}
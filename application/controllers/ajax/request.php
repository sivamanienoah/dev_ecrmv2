<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request extends crm_controller {

	public $cfg;
	public $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('request_model');
		$this->load->model('department_model');
		$this->load->model('customer_model');
		$this->load->model('email_template_model');
		$this->load->model('project_model');
		$this->load->library('upload');
		$this->load->helper('lead_helper');
	}
    
    function index()
    {

    }
	
	/*
	*@Get get department datas from econnect and update
	*@Model Department_model
	*@Method update_departments
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
    public function update_departments() 
	{
       $this->department_model->updateDepartments();
    }
	
	/*
	*@Update Client details to timesheet database
	*@Model Customer_model
	*@Method update_client_details
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
    public function update_client_details() 
	{
       $this->customer_model->update_client_details_to_timesheet();
    }
	
	/*
	*@Update project details to timesheet database and econnect database
	*@Model Customer_model
	*@Method update_project_details
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
    public function update_project_details() 
	{
       $this->customer_model->update_project_details();
    }
	
	/*
	*@Update project DATE details to timesheet database - project table and econnect database - project table
	*@Model Customer_model
	*@Method update_date_to_timesheet_econnect
	*@Parameter --
	*@Author eNoah - Mani.S
	*/
    public function update_date_to_timesheet_econnect() 
	{
       $this->customer_model->update_date_to_timesheet_econnect();
    }
	
	function update_existing_file_permissions()
	{
		$arrLeads = $this->request_model->get_all_lead_info();
		$user_data = $this->session->userdata('logged_in_user');
		
		if(isset($arrLeads) && !empty($arrLeads)) {
		
			foreach($arrLeads as $listLeads) {
			
				$arrProjectFolders = $this->request_model->get_all_project_folders($listLeads['lead_id']);		
				
				$project_members = $this->request_model->get_project_members($listLeads['lead_id']); // This array to get a project normal members(Developers) details.
				$project_leaders = $this->request_model->get_project_leads($listLeads['lead_id']); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
				$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
				$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
				$arrLeadInfo = $this->request_model->get_lead_info($listLeads['lead_id']); // This function to get a current lead informations.
				
				if(isset($arrProjectFolders) && !empty($arrProjectFolders)) { 
			
					foreach($arrProjectFolders as $listFolders) {
					
						if(isset($arrProjectMembers) && !empty($arrProjectMembers)) { 
		
							foreach($arrProjectMembers as $members){
								
								if(!empty($members)) {
								
									$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($listLeads['lead_id'], 'folder_id', $listFolders['folder_id'], $members['userid']);						
									
									if(empty($arrLeadExistFolderAccess)) {

										$read_access = $write_access = $delete_access = 0;
										
										$leadAssignArr = array();
										if(!empty($arrLeadInfo['lead_assign'])) {
											$leadAssignArr = @explode(', ', $arrLeadInfo['lead_assign']);
										}
										// Check this user is "Lead Owner", "Lead Assigned to", "Project Manager"
										
										if ($this->userdata['role_id'] == 1 || $arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || in_array($members['userid'], $arrLeadInfo['lead_assign'])) {
											$read_access = $write_access = $delete_access = 1;							
										}

										$folder_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$listLeads['lead_id'],'folder_id'=>$listFolders['folder_id'],'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>(int)$user_data['userid']);
										$insert_folder_permissions   = $this->request_model->insert_new_row('lead_file_access', $folder_permissions_contents); //Mani
									}
								}
							}
						}
					}
				}
			}
		}
	
		if(isset($arrLeads) && !empty($arrLeads)) {
		
			foreach($arrLeads as $listLeads) {

				$arrProjectFiles = $this->request_model->get_all_project_files($listLeads['lead_id']);
				
				$project_members = $this->request_model->get_project_members($listLeads['lead_id']); // This array to get a project normal members(Developers) details.
				$project_leaders = $this->request_model->get_project_leads($listLeads['lead_id']); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
				$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
				$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
				$arrLeadInfo = $this->request_model->get_lead_info($listLeads['lead_id']); // This function to get a current lead informations.		

				if(isset($arrProjectFiles) && !empty($arrProjectFiles)) { 
				
					foreach($arrProjectFiles as $listFiles){
					
						if(isset($arrProjectMembers) && !empty($arrProjectMembers)) {
			
							foreach($arrProjectMembers as $members){
							
								if(!empty($members)) {
								
									$arrLeadExistFileAccess= $this->request_model->check_lead_file_access_by_id($listLeads['lead_id'], 'file_id', $listFiles['file_id'], $members['userid']);						
									
									if(empty($arrLeadExistFileAccess)) {
									
										$read_access = 0;
										$write_access = 0;
										$delete_access = 0;									
										// Check this user is "Lead Owner", "Lead Assigned to", "Project Manager"
										
										$leadAssignArr = array();
										if(!empty($arrLeadInfo['lead_assign'])) {
											$leadAssignArr = @explode(', ', $arrLeadInfo['lead_assign']);
										}
										// if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
										if ($this->userdata['role_id'] == 1 || $arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || in_array($members['userid'], $arrLeadInfo['lead_assign'])) {
											$read_access = 1;
											$write_access = 1;
											$delete_access = 1;								
										}	

										$file_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$listLeads['lead_id'],'file_id'=>$listFiles['file_id'],'lead_file_access_read'=>0,'lead_file_access_delete'=>0,'lead_file_access_write'=>0,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>(int)$user_data['userid']);
										$insert_file_permissions   = $this->request_model->insert_new_row('lead_file_access', $file_permissions_contents); //Mani
										
									}
								}
							}
						}
					}
				}
			}
		
		}

		echo 'Thank You!'; exit;

	}
    
    function set_flash_data($type = 'header_messages')
    {	
        $this->session->set_flashdata($type, array($this->input->post('str')));
    }
	
	/**
	 * Delete a file, or a folder and its contents (recursive algorithm)
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.3
	 * @link        http://aidanlister.com/repos/v/function.rmdirr.php
	 * @param       string   $dirname    Directory to delete
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	function rmdirr($dirname)
	{
		// Sanity check
		if (!file_exists($dirname)) {
			return false;
		}
		
		// Simple delete for a file
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		
		// Loop through the folder
		$dir = dir($dirname);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			// Recurse
			$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
		
		// Clean up
		$dir->close();
		return rmdir($dirname);
	}
	
	function get_users_assigned_folder()
	{
		echo 'test';exit;
	}
	
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function file_upload($lead_id, $filefolder_id)
	{
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['ajax_file_uploader']['name']);
		
		$user_data = $this->session->userdata('logged_in_user');
		
		if($filefolder_id == 'Files') {
			$arrFolderId = $this->request_model->getParentFfolderId($lead_id, 0); 
			$filefolder_id = $arrFolderId['folder_id'];
		}
		// $check_permissions = $this->check_access_permissions($lead_id, 'folder_id', $filefolder_id, 'write');	
		 
		$project_members = array();
		$project_leaders = array();
		
		$project_members = $this->request_model->get_project_members($lead_id); // This array to get a project normal members(Developers) details.
		$project_leaders = $this->request_model->get_project_leads($lead_id); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
		$arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
		$arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
		$arrLeadInfo = $this->request_model->get_lead_info($lead_id); // This function to get a current lead informations.		
		 
		// echo '<pre>'; print_r($arrLeadInfo); exit;

			/* if($check_permissions == 0 && $user_data['role_id'] != 1) {			
				$this->upload_error = 'You have no permissions to upload file';
				$json['error'] = TRUE;
				$json['msg']   = $this->upload_error;
				echo json_encode($json); exit;
			} */
				
		/*$filefolder_id - first we check whether filefolder_id is a Parent or Child*/

		//creating files folder name
		$f_dir = UPLOAD_PATH.'files/';
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		//creating lead_id folder name
		$f_dir = $f_dir.$lead_id;
		if (!is_dir($f_dir)) {
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
	
				
				//$json['msg']   = $f_dir;
				
				//echo json_encode($json); exit;
		
		$this->upload->initialize(array(
		   "upload_path" => $f_dir,
		   "overwrite" => FALSE,
		   "remove_spaces" => TRUE,
		   "max_size" => 51000000,
		   "allowed_types" => "*"
		)); 
		// $config['allowed_types'] = '*';
		// "allowed_types" => "gif|png|jpeg|jpg|bmp|tiff|tif|txt|text|doc|docs|docx|oda|class|xls|xlsx|pdf|mpp|ppt|pptx|hqx|cpt|csv|psd|pdf|mif|gtar|gz|zip|tar|html|htm|css|shtml|rtx|rtf|xml|xsl|smi|smil|tgz|xhtml|xht"
		
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
					$lead_files['lead_files_name']		 = $file_up['file_name'];
					$lead_files['lead_files_created_by'] = $this->userdata['userid'];
					$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
					$lead_files['lead_id'] 				 = $lead_id;
					$lead_files['folder_id'] 			 = $filefolder_id; //get here folder id from file_management table.
					$insert_file						 = $this->request_model->insert_new_row('lead_files', $lead_files); //Mani
					
					$logs['jobid_fk']	   = $lead_id;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
					
				/* #################  Permission add new file owner start here  ################## */
				// if($user_data['role_id'] != 1) {
				// $permissions_contents  = array('userid'=>$user_data['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>1,'lead_file_access_delete'=>1,'lead_file_access_write'=>1,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$user_data['userid']);
				
				// $insert_permissions   = $this->request_model->insert_new_row('lead_file_access', $permissions_contents); //Mani		
				// }
				/* #################  Permission add new file owner end here  ################## */

				
				/* #################  Assing permission to all users by lead id start here  ################## */
					/* if(isset($arrProjectMembers) && !empty($arrProjectMembers)) { 
		
						foreach($arrProjectMembers as $members){
						
							if(!empty($members)) {
							
								if($user_data['userid'] != $members['userid']) {
									
									$arrLeadExistFolderAccess= $this->request_model->check_lead_file_access_by_id($lead_id, 'file_id', $insert_file, $members['userid']);						
									if(empty($arrLeadExistFolderAccess)) {	
									
										$read_access = 0;
										$write_access = 0;
										$delete_access = 0;									
										// Check this user is "Lead Owner", "Lead Assigned to", ""Project Manager"
										if($arrLeadInfo['belong_to'] == $members['userid'] || $arrLeadInfo['assigned_to'] == $members['userid'] || $arrLeadInfo['lead_assign'] == $members['userid']) {
											$read_access = 1;
											$write_access = 1;
											$delete_access = 1;								
										}	
										$other_permissions_contents  = array('userid'=>$members['userid'],'lead_id'=>$lead_id,'file_id'=>$insert_file,'lead_file_access_read'=>$read_access,'lead_file_access_delete'=>$delete_access,'lead_file_access_write'=>$write_access,'lead_file_access_created'=>time(),'lead_file_access_created_by'=>$user_data['userid']);
										$insert_other_users_permissions   = $this->request_model->insert_new_row('lead_file_access', $other_permissions_contents); //Mani
									}
								}
							
							}
						}
					} */
				/* #################  Assing permission to all users by lead id end here  ################## */
					
					
					
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
	 * @method get_project_files()
	 * @param $job_id
	 */
	public function get_project_files($job_id, $fparent_id=0) 
	{
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2 || $this->userdata['role_id'] == 4) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($job_id, $this->userdata['userid']);
		}
		
		$stake_holder_access = $this->request_model->get_stakeholder_access($job_id, $this->userdata['userid']);
	
		$file_upload_access = get_file_access($job_id, $this->userdata['userid']);
	
		$userdata    = $this->session->userdata('logged_in_user');
		$arrLeadInfo = $this->request_model->get_lead_info($job_id);
		$this->load->helper('file');

		//intial step - Showing 1st child folders and root files
		$get_parent_data = $this->request_model->getParentData($job_id, $fparent_id);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'files/' . $job_id . '/';
		
		$file_array = array();
		
		// echo "<pre>"; print_r($get_parent_data); exit;
		
		if(!empty($get_parent_data)) {			
			foreach($get_parent_data as $res) {
				// CHECK ACCESS PERMISSIONS START HERE //
				$get_permissions   = $this->check_access_permissions($job_id, $res['folder_id'], $this->userdata['userid']);
				$check_permissions = $get_permissions['access_type'];
				if($check_permissions != 0 || $userdata['role_id'] == 1 || $chge_access == 1 || $stake_holder_access == 1) { //check_permission
					if($res['folder_id'] == $fparent_id) {
						$get_files = $this->request_model->getFiles($job_id, $res['folder_id']);
					} else {
						$file_array[] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
					}
				} //check_permission
			}
		}	

		if(!empty($get_files)) {
			foreach($get_files as $files) {
				$file_array[] = $files['lead_files_name']."<=>".$files['lead_files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
			}
		}		
		
		// echo '<pre>'; print_r($file_array); die
		$jobs_files_html = '';
		$jobs_files_html .= '<table id="list_file_tbl-no-need" border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl-no-need dashboard-heads dataTable"><thead><tr><th><input type="checkbox" id="file_chkall" value="checkall"></th><th>File Name</th><th>Tags</th><th>Created On</th><th>Type</th><th>Size</th><th>Created By</th></tr></thead>';
		//<th>Permissions</th>
		if(!empty($file_array)) {
			$jobs_files_html .= '<tbody>';
			foreach($file_array as $fi) {
				list($fname, $fcreatedon, $ftype, $fcreatedby, $file_id) = explode('<=>',$fi);
					$jobs_files_html .= '<tr>';
					if($ftype == 'File') {
						$file_sz = '';
						$file_info = get_file_info($f_dir.$fname);
						$kb = 1024;
						$mb = 1024 * $kb;
						if ($file_info['size'] > $mb) {
						  $file_sz = round($file_info['size']/$mb, 2);
						  $file_sz .= ' Mb';
						} else if ($file_info['size'] > $kb) {
						  $file_sz = round($file_info['size']/$kb, 2);
						  $file_sz .= ' Kb';
						} else {
						  $file_sz = $file_info['size'] . ' Bytes';
						}
						$file_ext  = end(explode('.',$fname));
						$jobs_files_html .= "<td class='td_filechk'><input type='hidden' value='file'><input type='checkbox' class='file_chk' file-type='file' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><input type="hidden" id="file_'.$file_id.'" value="'.$fname.'">';
						
						// $file_dir = UPLOAD_PATH.'files/'.$job_id.'/'.$fname;
						
						$jobs_files_html .= '<a onclick="download_files_id('.$job_id.','.$file_id.'); return false;">'.$fname.'</a>';
						
						$jobs_files_html .= '</td>';
						$jobs_files_html .= '<td><a href="javascript:void(0)" onclick="add_tags('.$job_id.','.$file_id.'); return false;">Add Tags</a></td>';
						// $jobs_files_html .= '<td><a onclick=download_files('.$job_id.'); return false;>'.$fname.'</a></td>';	
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$file_ext.'</td>';
						$jobs_files_html .= '<td>'.$file_sz.'</td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						$jobs_files_html .= '';
					} else {					
					if($fparent_id == 0) $fname = 'Root';	
						// $jobs_files_html .= "<input type='hidden' name='current_folder_parent_id' id='current_folder_parent_id' value='".$file_id."'>";
						$jobs_files_html .= "<td><input type='hidden' value='folder'><input type='checkbox' file-type='folder' class='file_chk' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a class=edit onclick="getFolderdata('.$file_id.'); return false;" ><img src="assets/img/directory.png" alt=directory>&nbsp;'.$fname.'</a></td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$ftype.'</td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						//$jobs_files_html .= '<td><a onclick="show_permissions('.$job_id.','.$file_id.'); return false;">Permissions</a></td>';
					}
					$jobs_files_html .= '</tr>';
			}
		}
		$jobs_files_html .= '</tbody></table>';
		echo $jobs_files_html;
	}
	
	/**
	 * @method get_file_tree_struct()
	 * 
	 */
	public function get_file_tree_struct() {
		$data    = real_escape_array($this->input->post());
		$result  = $this->request_model->get_tree_file_list($data['leadid'],$parentId=0,$counter=0);
		$res     = array();
		$res['lead_id'] = $data['leadid'];
		$res['file_id'] = $data['file_id'];
		$res['fparent_id'] = $data['fparent_id'];
		foreach($result as $fid=>$fname){
			if($fname == $data['leadid']) {
				$fname = 'root';
			}
			if($fid == $data['fparent_id']) {
				$selected = 'selected=selected';
			} else {
				$selected = '';
			}
			$res['tree_struture'] .= "<option value='".$fid."' ".$selected.">".$fname."</option>"; 
		}
		echo json_encode($res);
		exit;
	}
	
	/**
	 * @method mapfiles()
	 * @mapping files to another folder
	 */
	public function mapfiles() {
		$mdata = real_escape_array($this->input->post());
		
		if($mdata['mffiletype'] == 'file') {
			$condn = array('file_id' => $mdata['mfile_id'],'lead_id' => $mdata['mlead_id']);
			$updt  = array('folder_id' => $mdata['move_destiny']);
			$res   = $this->request_model->update_row('lead_files', $updt, $condn);
			$err_msg = 'File has not been moved';
		} else if($mdata['mffiletype'] == 'folder') {
			if($mdata['mfile_id'] != $mdata['move_destiny']) {
				$mmf_condn           = array('lead_id'=>$mdata['mlead_id'],'folder_name'=>$mdata['mffilename'],'parent'=>$mdata['move_destiny']);
				$folder_check_status = $this->request_model->createFolderStatus('file_management', $mmf_condn);
				if($folder_check_status==0) {
					$condn = array('folder_id' => $mdata['mfile_id'],'lead_id' => $mdata['mlead_id']);
					$updt  = array('parent' => $mdata['move_destiny']);
					$res   = $this->request_model->update_row('file_management', $updt, $condn);
					$err_msg = 'Folder has not been moved';
				} else {
					$res   = FALSE;
					$err_msg = 'Folder Already exists';
				}
			} else {
				$res   = FALSE;
				$err_msg = 'Origin & Destiny are the same. Folder cannot be moved';
			}
		}
		if($res){
			$htm['result'] = TRUE;
			$htm['mf_msg'] = '<span class="ajx_success_msg"><h5>'.$mdata['mffiletype'].' has been moved</h5></span>';
		} else {
			$htm['result'] = FALSE;
			$htm['mf_msg'] = '<span class="ajx_failure_msg"><h5>'.$err_msg.'</h5></span>';
		}
		$htm['mf_reload'] = $mdata['move_destiny'];
		echo json_encode($htm);
		exit;
	}
	
	/**
	 * @method get_folder_tree_struct()
	 * 
	 */
	public function get_folder_tree_struct() {
		$data    = real_escape_array($this->input->post());
		$result  = $this->request_model->get_tree_file_list($data['leadid'],$parentId=0,$counter=0);
		$res     = array();
		$res['lead_id']          = $data['leadid'];
		$res['parent_folder_id'] = $data['parent_folder_id'];
		$res['fparent_id']       = $data['fparent_id'];
		$res['project_members_list'] = '';
		foreach($result as $fid=>$fname){
			if($fname == $data['leadid']) {
				$fname = 'Root';
			}
			if(($fid == $data['fparent_id']) || ($fid == $data['parent_folder_id'])) {
				$selected = 'selected=selected';
			} else {
				$selected = '';
			}
			$disabled = '';
			if($fname == 'Root'){
				// $disabled = 'disabled';
			}
			$res['tree_struture'] .= "<option value='".$fid."' ".$selected." ".$disabled.">".$fname."</option>"; 
		}
		
		/*$project_members = $this->request_model->get_project_members($data['leadid']);
		if(count($project_members)>0){
			
			foreach($project_members as $project_member){
				$res['project_members_list'] .= "<option value='".$project_member['userid']."'>".$project_member['first_name']." ".$project_member['last_name']."</option>"; 
			}
		}*/
		 
		echo json_encode($res);
		exit;
	}
	

	public function get_assigned_users() {
		$data    = real_escape_array($this->input->post());
	
		$res     = array();
		$res['lead_id'] = $data['leadid'];
		$res['fparent_id'] = $data['fparent_id'];
		$res['project_members_list'] = '';
		
		$rs = $this->db->get_where($this->cfg['dbpref']."file_management",array("folder_id" => $data['fparent_id']));
		$fol = $rs->row();
		$res['folder_name'] = $fol->folder_name;
		
		//project folder access
		$folder_id = $data['fparent_id'];
		$project_members = $this->project_model->get_contract_users($data['leadid']);
		$users_arr = array();
		$res['result_set'] = array();
		
		if(count($project_members) > 0){
			foreach($project_members as $key=>$val){
				$users_arr[] = $val['userid_fk'];
			}
			$exist_users = implode($users_arr,',');
			if(count($exist_users) > 0){
				$qry = $this->db->query("SELECT a.*,b.first_name,b.last_name FROM crm_project_folder_access as a join `crm_users` as b on a.user_id = b.userid where a.folder_id=$folder_id and a.user_id in ($exist_users)");
				$faccess = $qry->result();
				$res['result_set'] = $faccess;
			}
			
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

		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($job_id, $this->userdata['userid']);
		}
		
		if($chge_access != 1){
			$is_root = check_is_root($af_data['aflead_id'], $af_data['add_destiny']);
		} else {
			$is_root = 'no_root';
		}

		$af_condn            = array('lead_id'=>$af_data['aflead_id'],'folder_name'=>$af_data['new_folder'],'parent'=>$af_data['add_destiny']);
		$folder_check_status = $this->request_model->createFolderStatus('file_management', $af_condn);
		if(($folder_check_status==0) && ($is_root != 'root')){
			$add_data = array('lead_id'=>$af_data['aflead_id'],'folder_name'=>$af_data['new_folder'],'parent'=>$af_data['add_destiny'],'created_by'=>$this->userdata['userid'],'created_on'=>date('Y-m-d H:i:s'));
			$res_insert = $this->request_model->insert_new_row('file_management', $add_data);		 
			
			if(!$res_insert) {
				$htm['err']     = "true";			
				$htm['err_msg'] = 'Folder cannot be added.';
			} 
		} else {
			$res_insert     = FALSE;
			$htm['err']     = "true";
			$htm['err_msg'] = "Folder Name already exists (Or) you dont have access to write.";
		}
		
		if($res_insert){
			$log_contents  = array('jobid_fk'=>$af_data['aflead_id'],'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$af_data['new_folder'].' folder is Added.','attached_docs'=>$af_data['new_folder']);	
			$insert_logs   = $this->request_model->insert_row('logs', $log_contents);
			
		    /* #################  Permission add folder owner start here  ################## */
			
			if ($user_data['role_id'] == 1 || $user_data['role_id'] == 2) {
				$chge_access = 1;
			} else {
				$chge_access = get_del_access($job_id, $this->userdata['userid']);
			}
			
			if($user_data['role_id'] != 1 || $user_data['role_id'] != 2 || $chge_access != 1) {
				$permissions_contents  = array('lead_id'=>$af_data['aflead_id'],'folder_id'=>$res_insert,'user_id'=>$user_data['userid'],'access_type'=>2,'updated_by'=>$user_data['userid'],'updated_on'=>date('Y-m-d H:i:s'),'created_by'=>$user_data['userid'],'created_on'=>date('Y-m-d H:i:s'));
				$insert_permissions   = $this->request_model->insert_new_row('lead_folder_access', $permissions_contents); //Mani
			}
			/* #################  Permission add folder owner end here  ################## */
			 
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
	* @method assignFolders()
	* @Assign the folders
	*/
	
	
	public function assignFolders() {
		$af_data = real_escape_array($this->input->post());
		
		$af_condn = array('lead_id'=>$af_data['aflead_id'],'folder_name'=>$af_data['new_folder'],'parent'=>$af_data['add_destiny']);
		
		
		$folder_id = $this->input->post('cpparent_id');
		$pjt_users_id = $this->input->post('pjt_users_id');
		$is_recursive = $this->input->post('is_recursive');
		$add_access = $this->input->post('add_access');
		$download_access = $this->input->post('download_access');
		$users_count = count(array_unique($pjt_users_id));
	
		 
		if($users_count>0){					
			for($i=0;$i<$users_count;$i++){				
				$user_id = (isset($pjt_users_id[$i])?$pjt_users_id[$i]:'');
				$is_recursive1 = (isset($is_recursive[$user_id])?$is_recursive[$user_id]:0);
				$add_access1 = (isset($add_access[$user_id])?$add_access[$user_id]:0);
				$download_access1 = (isset($download_access[$user_id])?$download_access[$user_id]:0);
				
				$qry = $this->db->get_where($this->cfg['dbpref']."project_folder_access",array("folder_id" => $folder_id,"user_id" => $user_id));
				$nos = $qry->num_rows();
				 
				if($nos){
					$this->db->update($this->cfg['dbpref']."project_folder_access",array("folder_id" => $folder_id,"user_id" => $user_id,"is_recursive" => $is_recursive1, "add_access" => $add_access1, "download_access" => $download_access1),array("folder_id" => $folder_id,"user_id" => $user_id));
				}else{
					$this->db->insert($this->cfg['dbpref']."project_folder_access",array("folder_id" => $folder_id,"user_id" => $user_id,"is_recursive" => $is_recursive1, "add_access" => $add_access1, "download_access" => $download_access1,"created_on" => date("Y-m-d H:i:s"),"created_by" => $this->userdata['userid']));	
				}
				//echo $this->db->last_query();exit;
			}
		}
		$htm['af_msg'] = '<span class="ajx_success_msg"><h5>Permission(s) has been Updated</h5></span>';
		
		//get 1 parent alone
		$this->db->select('parent');
		$res = $this->db->get_where($this->cfg['dbpref']."file_management",array("folder_id" => $folder_id));
		$rs = $res->row();
				
		$htm['af_reload'] = $rs->parent;
		echo json_encode($htm);
		exit;
	}
	
	
	/**
	 * @method searchFile()
	 * @searching files & folder
	 */
	public function searchFile() {
		$sf_data = real_escape_array($this->input->post());		
		
		$job_id = $sf_data['lead_id'];
		// $parent_folder_id = $sf_data['currently_selected_folder'];
		
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($job_id, $this->userdata['userid']);
		}

		$userdata = $this->session->userdata('logged_in_user');
		// $arrLeadInfo = $this->request_model->get_lead_info($job_id);
		$this->load->helper('file');

		//intial step - Showing 1st child folders and root files
		$get_parent_data = $this->request_model->search_folder($job_id, $parent_folder_id, $sf_data['search_input']);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'files/' . $job_id . '/';
		
		$file_array = array();
		
		if(!empty($get_parent_data)) {
			foreach($get_parent_data as $res) {	
			// $check_permissions =  $this->check_access_permissions($job_id, 'folder_id', $res['folder_id'], 'read');
			// if($check_permissions == 1 || $userdata['role_id'] == 1) {
			
				$file_array[] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
				
				// }
			}
		}

		
		$get_files = $this->request_model->search_file($job_id, $parent_folder_id, $sf_data['search_input']);
		
		if(!empty($get_files)) {
			foreach($get_files as $files) {
			
			// $check_permissions_files =  $this->check_access_permissions($job_id, 'file_id', $files['file_id'], 'read');
			
			// if($check_permissions_files == 1 || $userdata['role_id'] == 1) {
			
				$file_array[] = $files['lead_files_name']."<=>".$files['lead_files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
				
				// }
			}
		}
		
		//echo '<pre>'; print_r($file_array);; exit;
		
		$jobs_files_html = '';
			$jobs_files_html .= '<table id="list_file_tbl" border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable"><thead><tr><th><input type="checkbox" id="file_chkall" value="checkall"></th><th>File Name</th><th>Tags</th><th>Created On</th><th>Type</th><th>Size</th><th>Created By</th></tr></thead>';
			$jobs_files_html .= '<tbody>';
		
		if(!empty($file_array)) {
		
			foreach($file_array as $fi) {
				list($fname, $fcreatedon, $ftype, $fcreatedby, $file_id) = explode('<=>',$fi);
					$jobs_files_html .= '<tr>';
					if($ftype == 'File') {
						$file_sz = '';
						$file_info = get_file_info($f_dir.$fname);
						$kb = 1024;
						$mb = 1024 * $kb;
						if ($file_info['size'] > $mb) {
						  $file_sz = round($file_info['size']/$mb, 2);
						  $file_sz .= ' Mb';
						} else if ($file_info['size'] > $kb) {
						  $file_sz = round($file_info['size']/$kb, 2);
						  $file_sz .= ' Kb';
						} else {
						  $file_sz = $file_info['size'] . ' Bytes';
						}
						$file_ext  = end(explode('.',$fname));
						$jobs_files_html .= "<td class='td_filechk'><input type='hidden' value='file'><input type='checkbox' class='file_chk' file-type='file' value='".$file_id."'></td>";
						// $jobs_files_html .= '<td><a target="_blank" href='.base_url().'crm_data/files/'.$job_id.'/'.$fname.'>'.$fname.'</a></td>';
						$jobs_files_html .= '<td><input type="hidden" id="file_'.$file_id.'" value="'.$fname.'"><a onclick="download_files_id('.$job_id.','.$file_id.'); return false;">'.$fname.'</a></td>';
						$jobs_files_html .= '<td><a href="javascript:void(0)" onclick="add_tags('.$job_id.','.$file_id.'); return false;">Add Tags</a></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$file_ext.'</td>';
						$jobs_files_html .= '<td>'.$file_sz.'</td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
					} else {
						$jobs_files_html .= "<td><input type='hidden' value='folder'><input type='checkbox' file-type='folder' class='file_chk' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a class=edit onclick="getFolderdata('.$file_id.'); return false;" ><img src="assets/img/directory.png" alt=directory>&nbsp;'.$fname.'</a></td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$ftype.'</td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
					}
					$jobs_files_html .= '</tr>';
			}
			
		}
		$jobs_files_html .= '</tbody></table>';
		echo $jobs_files_html;
	}

	/**
	 * @method get_file_tree_struct()
	 * 
	 */
	public function get_moveall_file_tree_struct() {
		$data    = real_escape_array($this->input->post());
		// $user_data = $this->session->userdata('logged_in_user'); //Mani

		$mvfolder = array();
		
		if(!empty($data['mv_folder'])) {
			$mv_folder = rtrim($data['mv_folder'], ",");
			$mvfolder = explode(',', $mv_folder);
		}

		$result  = $this->request_model->get_tree_file_list_omit($data['curr_job_id'],$parentId=0,$counter=0,$mvfolder);
		
		$res     = '';
		$res['lead_id'] = $data['curr_job_id'];
		
		// $arrLeadInfo = $this->request_model->get_lead_info($data['curr_job_id']);
		foreach($result as $fid=>$fname){
			if($fname == $data['curr_job_id']) {
				$fname = 'Root';
			}
			
			// CHECK ACCESS PERMISSIONS START HERE //			
			// $check_permissions =  $this->check_access_permissions($data['curr_job_id'], 'folder_id', $fid, 'write');
	
			// if($check_permissions == 1 || $user_data['role_id'] == 1 || $arrLeadInfo['belong_to'] == $user_data['userid'] || $arrLeadInfo['assigned_to'] == $user_data['userid'] || $arrLeadInfo['lead_assign'] == $user_data['userid']) {
				$disabled='';
				if($fname == 'Root'){
					// $disabled='disabled';
				}
				$res['tree_struture'] .= "<option value='".$fid."' ".$disabled.">".$fname."</option>";

			// }
			// CHECK ACCESS PERMISSIONS END HERE //	
			
		}
		echo json_encode($res);
		exit;
	}
	
	/**
	 * @method mapallfiles()
	 * @mapping multiple files to another folder
	 */
	public function mapallfiles() {
		$madata = real_escape_array($this->input->post());
		// $user_data = $this->session->userdata('logged_in_user'); //Mani
		// $arrLeadInfo = $this->request_model->get_lead_info($madata['mall_lead_id']);
		
		// CHECK ACCESS PERMISSIONS START HERE //			
			// $check_permissions =  $this->check_access_permissions($madata['mall_lead_id'], 'folder_id', $madata['move_destiny'], 'write');

			/* if($check_permissions == 0 && $user_data['role_id'] != 1) {
			
				$htm['mf_reload'] = $madata['move_destiny'];
				$htm['error'] = TRUE;
				$htm['mf_msg'] = '<span class="ajx_failure_msg"><h5>you have no permissions move file(s) or folder(s) to you selected folder</h5></span>';
				echo json_encode($htm); exit;
			
			} */
		// CHECK ACCESS PERMISSIONS END HERE //	
		
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

			// CHECK ACCESS PERMISSIONS START HERE //		
			// $get_moveing_folder_info = $this->request_model->getInfo($madata['mall_lead_id'], $mv_fo);		
			// $check_permissions =  $this->check_access_permissions($madata['mall_lead_id'], 'folder_id', $mv_fo, 'delete');
			
			/* if($check_permissions == 0 && $user_data['role_id'] != 1) {
				$htm['mf_reload'] = $madata['move_destiny'];
				$htm['error'] = TRUE;
				$htm['mf_msg'] = '<span class="ajx_failure_msg"><h5>You don\'t have a  permissions to access Folder: '.$get_moveing_folder_info['folder_name'].'</h5></span>';
				echo json_encode($htm); exit;
				break;
			} */
			// CHECK ACCESS PERMISSIONS END HERE //
			
				$condn 		= array('folder_id' => $mv_fo, 'lead_id' => $madata['mall_lead_id']);
				$updt  		= array('parent' => $madata['move_destiny']);
				$res_folder = $this->request_model->update_row('file_management', $updt, $condn);
				//insert_log
				if($res_folder){
					$get_info = $this->request_model->getInfo($madata['mall_lead_id'], $mv_fo);
					$log_contents  = array('jobid_fk'=>$madata['mall_lead_id'],'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$get_info['folder_name'].' folder has been moved.','attached_docs'=>$af_data['new_folder']);
					$insert_logs   = $this->request_model->insert_row('logs', $log_contents);
				}
			}
			$html['res_folder'] = TRUE;
		} else {
			$html['res_folder'] = TRUE;
		}
		if(!empty($mov_file)) {
			$html['res_file'] = FALSE;
			foreach($mov_file as $mv_fi) {
			
			// CHECK ACCESS PERMISSIONS START HERE //		
			/* $get_moveing_files_info = $this->request_model->getFilesInfo($madata['mall_lead_id'], $mv_fi);			
			$check_file_permissions =  $this->check_access_permissions($madata['mall_lead_id'], 'file_id', $mv_fi, 'delete');			
			if($check_permissions == 0 && $user_data['role_id'] != 1) {
			
				$htm['mf_reload'] = $madata['move_destiny'];
				$htm['error'] = TRUE;
				$htm['mf_msg'] = '<span class="ajx_failure_msg"><h5>You don\'t have a  permissions to access File: '.$get_moveing_files_info['lead_files_name'].'</h5></span>';
				echo json_encode($htm); exit;
				break;
			
			} */
			// CHECK ACCESS PERMISSIONS END HERE //	
			
				$condn = array('file_id' => $mv_fi,'lead_id' => $madata['mall_lead_id']);
				$updt  = array('folder_id' => $madata['move_destiny']);
				$res_file   = $this->request_model->update_row('lead_files', $updt, $condn);
				//insert_log
				if($res_file){
					$get_info = $this->request_model->getFilesInfo($madata['mall_lead_id'], $mv_fi);
					$log_contents  = array('jobid_fk'=>$madata['mall_lead_id'],'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$get_info['lead_files_name'].' file has been moved.','attached_docs'=>$af_data['new_folder']);
					$insert_logs   = $this->request_model->insert_row('logs', $log_contents);
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
	
	/*
	*Multi delete file&folder function need to be create.
	*/
	
	/*
	*@method getBreadCrumbs
	*/
	function getBreadCrumbs($leadid, $parent, $res) {
	
		// if($parent == 'Files') {
		
			// echo $bc='<span>Files</span> ';exit;
		
		// }else{
	
			$data = $this->request_model->getBreadCrumbDet($leadid, $parent);
			foreach($data as $rec) {
				$res[$rec['folder_id']] = $rec['folder_name'];
				$parent_id = $rec['parent'];
				if( $parent_id !=0 ) {
					$this->getBreadCrumbs($leadid, $parent_id, $res);
				} else {
					$bc = '<span>Files</span>';
					$res = array_reverse($res, true);
					foreach($res as $fid=>$fnm) {
						if($fnm == $leadid) {
							$fnm = 'Root';
						}
						if($bc!='') {
							$bc.=' >> ';
						}
						$bc.='<a href="javascript:void(0)" onclick="getFolderdata('.$fid.'); return false;">'.$fnm.'</a>';
					}
					echo $bc; exit;
				}
			}
		// }
	}
	
	/*
	*@method getFolderActions
	*/
	function getFolderActions($leadid, $folder_id) 
	{
		$data['lead_id']    = $leadid;
		$data['folder_id']  = $folder_id;
		$data['user_id']    = $this->userdata['userid'];
		$lead_detail        = $this->project_model->get_lead_det($leadid);
		$data['pjt_status'] = $lead_detail['pjt_status'];
		
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2)
		$data['chge_access'] = 1;
		else
		$data['chge_access'] = $this->project_model->get_access($leadid, $this->userdata['userid']);
		
		echo $this->load->view("projects/folder_write_actions", $data, true);
		exit;
	}
	
	/*
	*
	*/
	public function get_files_tree_structure() 
	{
		$data    = real_escape_array($this->input->post());
		$result  = $this->request_model->get_tree_file_list_number($data['leadid'],$parentId=0,$counter=0);
		$res     = array();
		$html 	 = '';

		if(!empty($result)) {
			foreach($result as $folder_id=>$folder_name) {
			
				$exp         = explode("~", $folder_name);
				$counters    = $exp[0];
				$folder_name = $exp[1];
				
				if(is_numeric($folder_name)) {
					$folder_name = "&nbsp;root";
				}
				$html .="<ul>";

				$html .= str_repeat("&nbsp;&nbsp;", $counters)."<img alt='directory' src='assets/img/directory.png'>".$folder_name;
				
				$res = $this->request_model->getAssociateFiles($data['leadid'], $folder_id);
				
				if(!empty($res)) {
					foreach($res as $fname) {
						$html .= "<li>";
						$html .= "&nbsp;".str_repeat("&nbsp;&nbsp;", $counters)."&nbsp;&nbsp;"."<input type='checkbox' class='attach_file' value='".$fname['file_id']."~".$fname['lead_files_name']."'>&nbsp;<a onclick=download_files('".$data['leadid']."','".$fname['lead_files_name']."'); return false;>".$fname['lead_files_name']."</a>";						
						$html .= "</li>";
					}
				}
				$html .="</ul>";
			}
		}
		echo $html;
		exit;
	}
	
	public function get_files_tree_structure_for_other_cost() 
	{
		$data    = real_escape_array($this->input->post());
		$result  = $this->request_model->get_tree_file_list_number($data['leadid'],$parentId=0,$counter=0);
		$res     = array();
		$html 	 = '';

		if(!empty($result)) {
			foreach($result as $folder_id=>$folder_name) {
			
				$exp         = explode("~", $folder_name);
				$counters    = $exp[0];
				$folder_name = $exp[1];
				
				if(is_numeric($folder_name)) {
					$folder_name = "&nbsp;root";
				}
				$html .="<ul>";

				$html .= str_repeat("&nbsp;&nbsp;", $counters)."<img alt='directory' src='assets/img/directory.png'>".$folder_name;
				
				$res = $this->request_model->getAssociateFiles($data['leadid'], $folder_id);
				
				if(!empty($res)) {
					foreach($res as $fname) {
						$html .= "<li>";
						$html .= "&nbsp;".str_repeat("&nbsp;&nbsp;", $counters)."&nbsp;&nbsp;"."<input type='checkbox' class='oc_attach_file' value='".$fname['file_id']."~".$fname['lead_files_name']."'>&nbsp;<a onclick=download_files('".$data['leadid']."','".$fname['lead_files_name']."'); return false;>".$fname['lead_files_name']."</a>";						
						$html .= "</li>";
					}
				}
				$html .="</ul>";
			}
		}
		echo $html;
		exit;
	}
	
	/*
	* Delete the selected files
	*/
	public function delete_files(){
		// echo "<pre>"; print_r($_POST); exit;
		$delData            = real_escape_array($this->input->post());
		$json	            = array();
		
		if(!empty($delData['ff_id'])) {
			$json['folder_parent_id'] = $delData['ff_id'];
		} else {
			$get_parent_folder_id = $this->request_model->getParentFfolderId($delData['curr_job_id'],$parent=0);
			$json['folder_parent_id'] = $get_parent_folder_id['folder_id'];
		}
		if(!empty($delData['del_folder'])) {
			$del_folder = rtrim($delData['del_folder'], ",");
			$del_folder = explode(',', $del_folder);
		}
		if(!empty($del_folder)) {
			$res = $this->del_folder_all($delData['curr_job_id'],$del_folder);
			$json['folder_del_status'] = $res;
		}
		if(!empty($delData['del_files'])) {
			$del_files = rtrim($delData['del_files'], ",");
			$del_files = explode(',', $del_files);
		}
		if(!empty($del_files)) {
			$file_res = $this->del_file_all($delData['curr_job_id'],$del_files);
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
	 * Deletes a folder based on a above function delete_files() request
	 */
	/* public function del_folder_all($jobid,$array_folder_id) {
		$res = array();
		$user_data = $this->session->userdata('logged_in_user');
		$arrLeadInfo = $this->request_model->get_lead_info($jobid);
		
		$fcpath = UPLOAD_PATH;
		
		//echo '<pre>'; print_r($array_folder_id); exit;
		foreach($array_folder_id as $folder_id) {		
		
			$parents_folder_path =  $this->request_model->get_tree_parents_file_list($jobid, $folder_id);			
					
			// CHECK ACCESS PERMISSIONS START HERE //		
			$condn = array("lead_id"=>$jobid,"folder_id"=>$folder_id);
			$get_file_data = $this->request_model->get_record("file_management", $condn);		
			$check_permissions =  $this->check_access_permissions($jobid, 'folder_id', $folder_id, 'delete');			
			if($check_permissions == 0 && $user_data['role_id'] != 1) {
			
				$res[] = 'You don\'t have a permissions to delete '.$get_file_data['folder_name'].' folder.';
				return $res;
				break;
			
			}
			// CHECK ACCESS PERMISSIONS END HERE //				
			
			$arrMainFoldFiles = $this->request_model->get_lead_files_by_folder_id($folder_id);
			
			if(isset($arrMainFoldFiles) && !empty($arrMainFoldFiles)) {			
					$res[] = $this->delete_leadfiles($jobid, $arrMainFoldFiles);			
			}			
			$arrTreeFolders = $this->request_model->get_tree_folder_lists($jobid, $folder_id);			
			
			if(isset($arrTreeFolders) && !empty($arrTreeFolders)) {

					foreach($arrTreeFolders as $key=>$value) {
							
							$arrLeadFiles = $this->request_model->get_lead_files_by_folder_id($key);
							
							if(isset($arrLeadFiles) && !empty($arrLeadFiles)) {
								$res[] = $this->delete_leadfiles($jobid, $arrLeadFiles);
							}							
							// Folder Row Delete
							$folder_del_condn_access = array('lead_id'=>$jobid,'folder_id'=>$key);
							$delete_folder_access = $this->request_model->delete_row('lead_file_access', $folder_del_condn_access);
							
							$folder_del_condn = array('lead_id'=>$jobid,'folder_id'=>$key);
							$delete_folder = $this->request_model->delete_row('file_management', $folder_del_condn);
							if($delete_folder) {
							// Insert Logs
							$logs['jobid_fk']	   = $jobid;
							$logs['userid_fk']	   = $this->userdata['userid'];
							$logs['date_created']  = date('Y-m-d H:i:s');
							$logs['log_content']   = $value.' folder is deleted.';
							$logs['attached_docs'] = $value;
							$insert_logs 		   = $this->request_model->insert_row('logs', $logs);													
							$res[] = $value.' folder is deleted';
							}else {			
							$res[] = $value.' folder cannot be deleted.';			
							}				
					}
			}
			$folder_del_access = array('lead_id'=>$jobid,'folder_id'=>$folder_id);
			$this->request_model->delete_row('lead_file_access', $folder_del_access);
							
			$folder_del = array('lead_id'=>$jobid,'folder_id'=>$folder_id);
			$delete_folder_main = $this->request_model->delete_row('file_management', $folder_del);			
			if($delete_folder_main) {
			// Insert Logs
			$logs['jobid_fk']	   = $jobid;
			$logs['userid_fk']	   = $this->userdata['userid'];
			$logs['date_created']  = date('Y-m-d H:i:s');
			$logs['log_content']   = $get_file_data['folder_name'].' folder is deleted.';
			$logs['attached_docs'] = $get_file_data['folder_name'];
			$insert_logs 		   = $this->request_model->insert_row('logs', $logs);		
			$res[] = $get_file_data['folder_name'].' folder is deleted';
			}else {			
			$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';			
			}
		
		}
		return $res;
	} */
	
	public function del_folder_all($jobid,$array_folder_id) {
		$res = array();
		foreach($array_folder_id as $folder_id) {
		
			$condn = array("lead_id"=>$jobid,"folder_id"=>$folder_id);
			$get_file_data = $this->request_model->get_record("file_management", $condn);
		
			$fm_condn            = array('lead_id'=>$jobid,'parent'=>$folder_id);
			$folder_check_status = $this->request_model->checkStatus('file_management', $fm_condn);
			
			if($folder_check_status) {
				$lf_condn          = array('lead_id'=>$jobid,'folder_id'=>$folder_id);
				$file_check_status = $this->request_model->checkStatus('lead_files', $lf_condn);
				if($file_check_status) {
					//Deleting the folder
					$del_condn = array('lead_id'=>$jobid,'folder_id'=>$folder_id);
					$del_file  = $this->request_model->delete_row('file_management', $del_condn);
					if($del_file) {
						$logs['jobid_fk']	   = $jobid;
						$logs['userid_fk']	   = $this->userdata['userid'];
						$logs['date_created']  = date('Y-m-d H:i:s');
						$logs['log_content']   = $get_file_data['folder_name'].' folder is deleted.';
						$logs['attached_docs'] = $get_file_data['folder_name'];
						$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
					
						$res[] = $get_file_data['folder_name'].' folder is deleted.';
					} else {
						$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';
					}
				} else {
					$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';
				}
			} else {
				$res[] = $get_file_data['folder_name'].' folder cannot be deleted.';
			}
		}
		return $res;
	}
	
	public function delete_leadfiles($jobid, $arrLeadFiles)
	{
		
		$fcpath = UPLOAD_PATH;
		foreach($arrLeadFiles as $fileKey=>$fileValue) {
			$f_dir = $fcpath . 'files/' . $jobid . '/' . $fileValue['lead_files_name'];
		
			if (@unlink($f_dir)) {
				// Files Row Delete
				
				$file_del_access = array('lead_id'=>$jobid,'file_id'=>$fileValue['file_id']);
				$this->request_model->delete_row('lead_file_access', $file_del_access);
				
				$wh_file_condn = array('file_id' => $fileValue['file_id'], 'lead_id' => $jobid);
				$delete_file = $this->request_model->delete_row('lead_files', $wh_file_condn);
				
				if($delete_file) {
				// Insert Logs
					$logs['jobid_fk']	   = $jobid;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $fileValue['lead_files_name'].' file is deleted.';
					$logs['attached_docs'] = $fileValue['lead_files_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);								
					$res[] = $fileValue['lead_files_name'].' file is deleted.';
				} else {
					$res[] = $fileValue['lead_files_name'].' file cannot be deleted.';
				}
			}
		}		
		return $res;
	}
	
	
	public function del_file_all($jobid,$array_file_id) {
		$f_data = real_escape_array($this->input->post());
		
		foreach($array_file_id as $file_id) {
			$condn = array("lead_id"=>$jobid,"file_id"=>$file_id);
			$get_file_data = $this->request_model->get_record("lead_files", $condn);
		
			$fcpath = UPLOAD_PATH; 
			$f_dir = $fcpath . 'files/' . $jobid . '/' . $get_file_data['lead_files_name'];
	
			if (isset($f_dir))
			{
				$file_condn          = array('file_id'=>$file_id);
				$file_check_status   = $this->request_model->checkStatus('expected_payments_attach_file', $file_condn);
				if ($file_check_status)
				{
					if (@unlink($f_dir))
					{
						$wh_condn = array('file_id' => $file_id, 'lead_id' => $jobid);
						$del_file = $this->request_model->delete_row('lead_files', $wh_condn);
						
						$logs['jobid_fk']	   = $jobid;
						$logs['userid_fk']	   = $this->userdata['userid'];
						$logs['date_created']  = date('Y-m-d H:i:s');
						$logs['log_content']   = $get_file_data['lead_files_name'].' is deleted.';
						$logs['attached_docs'] = $get_file_data['lead_files_name'];
						$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
						
						$res[] 		  		   = $get_file_data['lead_files_name'].' is deleted.';
					} else {
						$res[]		  		   = $get_file_data['lead_files_name'].' file cannot be deleted.';
					}
				} else {
					$res[]		  		   	   = $get_file_data['lead_files_name'].' file cannot be deleted. It is linked with the Payment milestones.';
				}
			} else {
				$res[]		  		           = $get_file_data['lead_files_name'].' file cannot be deleted.';
			}
		}
		return $res;
	}
	
	/**
	 * Get all logs for a particular job;
	 */
    public function logs($lead_id)
	{
		$this->db->where('jobid_fk', $lead_id);
		$this->db->order_by('date_created', 'desc');
		$logs = $this->db->get($this->cfg['dbpref'] . 'logs');
		
		if ($logs->num_rows() > 0)
		{
			$log_data = $logs->result_array();
			$this->load->helper('url');
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$data['log_html'] = '';
			
			foreach ($log_data as $ld)
			{
				
				$this->db->where('userid', $ld['userid_fk']);
				$user = $this->db->get($this->cfg['dbpref'] . 'users');
				$user_data = $user->result_array();
				
				$log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
				
				$fancy_date = date('d-m-Y H:i:s', strtotime($ld['date_created']));
				
				$table = <<< HDOC
<div class="log">
<p class="data">
	<span>{$fancy_date}</span>
{$user_data[0]['first_name']} {$user_data[0]['last_name']}
</p>
<p class="desc">
	{$log_content}
</p>
</div>
HDOC;
				$data['log_html'] .= $table;
				unset($table, $user_data, $user, $log_content);
			}
			
			echo '<div class="log-container">', $data['log_html'], '</div>';
		}
		else
		{
			echo 'No logs available';
		}
	}
	
	public function get_new_logs($lead_id, $datetime)
	{
		$this->db->where('jobid_fk', $lead_id);
		$this->db->order_by('date_created', 'desc');
		$this->db->limit(1);
		$logs = $this->db->get($this->cfg['dbpref'] . 'logs');
		
		if ($logs->num_rows() > 0)
		{
			$log_data = $logs->result_array();
			$ld = $log_data[0];
			
			if (strtotime($ld['date_created']) > strtotime($datetime))
			{
				$this->load->helper('url');
				
				$data['log_html'] = '';
				
				$this->db->where('userid', $ld['userid_fk']);
				$user = $this->db->get($this->cfg['dbpref'] . 'users');
				$user_data = $user->result_array();
				
				$log_content = nl2br(auto_link($ld['log_content'], 'url', TRUE));
				
				$fancy_date = date('d-m-Y H:i:s', strtotime($ld['date_created']));
				
				$table = <<< HDOC
<div class="log" style="display:none;">
<p class="data">
	<span>{$fancy_date}</span>
{$user_data[0]['first_name']} {$user_data[0]['last_name']}
</p>
<p class="desc">
	{$log_content}
</p>
</div>
HDOC;
				$data['log_html'] .= $table;
				
				$data['error'] = FALSE;
				
				echo json_encode($data);
			}
			
		}
	}
	
	public function add_url_tojob()
	{
		if (isset($_POST['lead_id']) && isset($_POST['url']))
		{
			$lead_id = $_POST['lead_id'];
			$url = $_POST['url'];
			
			$ins['content'] = (isset($_POST['content'])) ? $_POST['content'] : '';
			$ins['url'] = urldecode($url);
			
			$data['error'] = FALSE;
			
			if ( $userdata = $this->session->userdata('logged_in_user') )
			{
				if (!filter_var($ins['url'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
				{
					$data['error'] = "Please enter a valid URL!";
					echo json_encode($data);
					return FALSE;
				}
				else
				{
					$this->load->helper('url');
					
					$ins['jobid_fk'] = $lead_id;
					$ins['userid_fk'] = $userdata['userid'];
					$ins['date'] = date('Y-m-d H:i:s');
					$this->db->insert($this->cfg['dbpref'].'job_urls', $ins);
					
					$insid = $this->db->insert_id();
					
					$html = '<li>';
				
						$html .= '<a href="#" onclick="ajaxDeleteJobURL(' . $insid . ', this); return false;" class="file-delete">delete URL</a>';
					
					$html .= '<span>' . auto_link(htmlentities($ins['url'])) . '</span><p>' . htmlentities($ins['content'], ENT_QUOTES) . '</p></li>';
					
					$data['html'] = $html;
					
					echo json_encode($data);
				}
			}
			else
			{
				$data['error'] = "You required to be logged in!";
				echo json_encode($data);
				return FALSE;
			}
		}
		else
		{
			$data['error'] = "Invalid request!";
			echo json_encode($data);
			return FALSE;
		}
	}
	
	public function delete_url($id)
	{
		if ($this->db->delete($this->cfg['dbpref'].'job_urls', array('urlid' => $id)))
		{
			$data['error'] = FALSE;
		}
		else
		{
			$data['error'] = TRUE;
		}
		echo json_encode($data);
	}
	
	
	
	/**
	 * Add job task for a user
	 * Edits a task
	 * Adds a random task for a user
	 */
	function add_job_task($update = 'NO', $random = 'NO')
	{
		$this->load->model('user_model');
		$this->load->library('email');
		$errors = array();
		$follow_up_id = 0;
		$post_data = $this->input->post();
		
		// echo '<pre>'; print_r($post_data); die;
		
		if ($random != 'NO')
		{
			//$_POST['lead_id'] = 0;
		}
		
		$json['error'] = FALSE;
		if($update == 'NO') {
			$ins['jobid_fk'] = (int) $post_data['lead_id'];
		}
		$ins['task'] 		= $post_data['job_task'];
		$ins['userid_fk'] 	= $post_data['task_user'];
		$ins['remarks'] 	= $post_data['remarks'];
		$ins['task_category'] 	= $post_data['task_category'];
		$ins['task_priority'] 	= $post_data['task_priority'];
		$ins['estimated_hours'] = $post_data['estimated_hours'];
		$ins['task_stage']	    = isset($post_data['task_stage']) ? $post_data['task_stage'] : 1;
		if($update == 'NO') {
			$ins['approved'] = 1;
		}
		if($update == 'NO') {
			$ins['created_by'] = $this->userdata['userid'];
		}			
		$ins['created_on'] 	= date('Y-m-d H:i:s');
		
		$task_start_date 	= explode('-', trim($post_data['task_start_date']));
		$task_end_date 		= explode('-', trim($post_data['task_end_date']));
		
		
		if (count($task_start_date) != 3 || ! $start_date = mktime(0, 0, 0, $task_start_date[1], $task_start_date[0], $task_start_date[2]))
		{
			$errors[] = 'Invalid Start Date!';
		}
		
		if (count($task_end_date) != 3 || ! $end_date = mktime(0, 0, 0, $task_end_date[1], $task_end_date[0], $task_end_date[2]))
		{
			$errors[] = 'Invalid End Date!';
		}
				
		$time_range = array(
							'10:00:00'	=> '10:00AM',
							'11:00:00'	=> '11:00AM',
							'12:00:00'	=> '12:00PM',
							'13:00:00'	=> '1:00PM',
							'14:00:00'	=> '2:00PM',
							'15:00:00'	=> '3:00PM',
							'16:00:00'	=> '4:00PM',
							'17:00:00'	=> '5:00PM',
							'18:00:00'	=> '6:00PM',
							'19:00:00'	=> '7:00PM'
						);
		
		/*if (isset($_POST['task_end_hour']) && ! array_key_exists($_POST['task_end_hour'], $time_range))
		{
			$errors[] = 'Invalid task end time!';
		}*/
		
		if ($start_date < strtotime(date('Y-m-d')) && $update == 'NO')
		{
			$errors[] = 'Start date cannot be earlier than today!';
		}
		if ($end_date < $start_date)
		{
			$errors[] = 'End date cannot be earlier than start date';
		}
		
		/*if ($ins['jobid_fk'] == 0 && $random == 'NO')
		{
			$errors[] = $ins['jobid_fk'];
			$errors[] = 'Valid lead_id is required!';
		}*/
		
		/*if ($update != 'NO')
		{
			$errors[] = 'Only the production manager can edit the tasks!';
		}*/
		
		if (count($errors) > 0)
		{
			$json['error'] = TRUE;
			$json['errormsg'] = implode("\n", $errors);
		}
		else
		{
			$ins['start_date'] 	= date('Y-m-d H:i:s', $start_date);
			$ins['end_date'] 	= date('Y-m-d H:i:s', $end_date);
			
			$dtask_start_date	= date('d-m-Y H:i:s', $start_date);
			$dtask_end_date		= date('d-m-Y H:i:s' , $end_date);
			if (isset($post_data['task_end_hour']))
			{
				$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			}
			
			$ins['require_qc'] 	= (isset($post_data['require_qc']) && $post_data['require_qc'] == 'YES') ? '1' : '0';
			$ins['priority'] 	= (isset($post_data['priority']) && $post_data['priority'] == 'YES') ? '1' : '0';
			
			if ($update != 'NO' && $old_task = $this->get_task($update))
			{
				$updatedby = $this->user_model->updatedby($old_task->taskid);
				$ins['created_by'] = $updatedby[0]['created_by'];				
				$task_actualstart_date = explode('-', trim($post_data['actualstart_date']));
				
				if (count($task_actualstart_date) != 3 || ! $actualtask_date = mktime(0, 0, 0, $task_actualstart_date[1], $task_actualstart_date[0], $task_actualstart_date[2])) {
					$errors[] = 'Invalid Actual Start Date!';
				}
				if($post_data['actualstart_date'] =='0000-00-00' || $post_data['actualstart_date'] == 'Not Assigned') {
					$ins['actualstart_date']	= '0000-00-00 00:00:00';
				} else {
					$ins['actualstart_date'] 	= date('Y-m-d H:i:s', $actualtask_date);
				}
				//update
				$this->db->where('taskid', $update);
				$this->db->update($this->cfg['dbpref'].'tasks', $ins);
				
				//for follow up task
				if(isset($post_data['follow_up']) && $post_data['follow_up']==1) {
					$follow_up_arr = $this->request_model->get_task_info_by_id($update);					
					$follow_up_id  = $follow_up_arr['jobid_fk'];
				}
				
				//echo $this->db->last_query();exit;
				$ins['user_label'] 	= $post_data['user_label'];
				$ins['status'] 		= $ins['is_complete'] = 0;
				$ins['taskid'] 		= $update; 
				$ins['userid'] 		= $ins['userid_fk'];
				$taskowner 			= $this->user_model->get_user($ins['userid']);
				$taskAssignedTo		= $taskowner[0]['first_name'].'&nbsp;'.$taskowner[0]['last_name'];
				$taskAssignedToEmail= $taskowner[0]['email'];

				$json['html'] 		= $this->format_task($ins);
				
				# add a record in tasks_track table while updating
				$record['taskid_fk']	= $old_task->taskid;
				$record['event']		= 'Task Update';
				$record['date'] 	 	= date('Y-m-d H:i:s');
				$record['event_data'] 	= json_encode($old_task);
				$this->db->insert($this->cfg['dbpref'].'tasks_track', $record);
				$from_name 				= $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$arrEmails 				= $this->config->item('crm');
				$arrSetEmails			= $arrEmails['director_emails'];
				
				$admin_mail				= implode(',',$arrSetEmails);
				$subject 				= 'New Task Update Notification';
				$from					= $this->userdata['email'];
				
				$user_name 				= $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				
				$task_owner_name 		= $this->db->query("SELECT u.first_name,u.last_name,t.remarks,t.jobid_fk,t.actualstart_date, t.status
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$update}");
				$task_owners 			= $task_owner_name->result_array();

				$dis['date_created'] 	= date('Y-m-d H:i:s');
				$print_fancydate 		= date('l, jS F y h:iA', strtotime($dis['date_created']));
				
				/*insert log-start here*/
					
				$log_detail  = "Task Updated: \n";
				$log_detail .= "\nTask Desc: ".$this->input->post('job_task');
				$log_detail .= "\nAllocated To: ".$taskowner[0]['first_name'].' '.$taskowner[0]['last_name'];
				$log_detail .= "\nAllocated By: ".$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'];
				$log_detail .= "\nPlanned Start Date: ".date('d-m-Y', strtotime($dtask_start_date)).'  :: Planned End Date:'.date('d-m-Y', strtotime($dtask_end_date));
				$log_detail .= "\nActual Start Date: ".date('d-m-Y', strtotime($task_owners[0]['actualstart_date']));
				$log_detail .= "\nRemarks: ".$task_owners[0]['remarks'];
				$log_detail .= "\nStatus: ".$task_owners[0]['status'].' %';
				$log = array();
				$log['jobid_fk']      = $task_owners[0]['jobid_fk'];
				$log['userid_fk']     = $this->userdata['userid'];
				$log['date_created']  = date('Y-m-d H:i:s');
				$log['log_content']   = $log_detail;
				$log_res = $this->project_model->insert_row("logs", $log);
				
				/*insert log-end here*/
				
				//email sent by email template
				$param = array();

				$param['email_data'] = array('job_task'=>$post_data['job_task'],'taskAssignedTo'=>$taskAssignedTo,'remarks'=>$task_owners[0]['remarks'],'start_date'=>date('d-m-Y', strtotime($dtask_start_date)),'end_date'=>date('d-m-Y', strtotime($dtask_end_date)),'first_name'=>$task_owners[0]['first_name'],'last_name'=>$task_owners[0]['last_name'],'status'=>$ins['status']);

				$param['to_mail'] 		= $taskAssignedToEmail;
				// $param['bcc_mail'] 	= $admin_mail;
				$param['cc_mail'] 		= $from;
				$param['from_email'] 	= $from;
				$param['from_email_name'] = $from_name;
				$param['template_name']   = "Task Update Notification";
				$param['subject'] 		  = $subject;

				$this->email_template_model->sent_email($param);
			}
			else if ($update == 'NO') //inserting new task here
			{
				if ( ! $this->db->insert($this->cfg['dbpref'].'tasks', $ins))
				{
					$json['error'] = TRUE;
					$json['errormsg'] = 'Task insert error';
				}
				else
				{
					$ins['user_label'] 	= $post_data['user_label'];
					$ins['status'] 		= $ins['is_complete'] = 0;
					$ins['taskid'] 		= $this->db->insert_id();
					$ins['userid'] 		= $ins['userid_fk'];
					$json['html'] 		= $this->format_task($ins);
					
					$creator 			= $this->user_model->get_user($this->userdata['userid']);
					$creator 			= $creator[0];
					$task_owner 		= $this->user_model->get_user($ins['userid_fk']);
					$taskSetTo			= $task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskSetToEmail		= $task_owner[0]['email'];
					$job_url 			= ($ins['jobid_fk'] != 0) ? $this->config->item('base_url')."welcome/view_quote/{$ins['jobid_fk']}" : '';
					$task_owner_name 	= $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$ins['taskid']}");
					$task_owners 		= $task_owner_name->result_array();
		
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate 	= date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					/*insert log-start here*/
					
					$log_detail = "New Task Added: \n";
					$log_detail .= "\nTask Desc: ".$this->input->post('job_task');
					$log_detail .= "\nAllocated To: ".$task_owner[0]['first_name'].' '.$task_owner[0]['last_name'];
					$log_detail .= "\nAllocated By: ".$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'];
					$log_detail .= "\nPlanned Start Date: ".date('d-m-Y', strtotime($dtask_start_date)).'  :: Planned End Date:'.date('d-m-Y', strtotime($dtask_end_date));
					$log_detail .= "\nRemarks: ".$task_owners[0]['remarks'];
					$log_detail .= "\nStatus: ".$ins['status'].' %';
					$log = array();
					$log['jobid_fk']      = $this->input->post('lead_id');
					$log['userid_fk']     = $this->userdata['userid'];
					$log['date_created']  = date('Y-m-d H:i:s');
					$log['log_content']   = $log_detail;
					$log_res = $this->project_model->insert_row("logs", $log);
					
					/*insert log-end here*/

					$subject 			= 'New Task Notification';
					$from 				= $this->userdata['email'];;
					$user_name 			= $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$arrEmails 			= $this->config->item('crm');
					
					//email sent by using email template
					$param = array();

					$param['email_data'] = array('job_task'=>$post_data['job_task'], 'taskSetTo'=>$taskSetTo, 'remarks'=>$task_owners[0]['remarks'], 'start_date'=>date('d-m-Y', strtotime($dtask_start_date)), 'end_date'=>date('d-m-Y', strtotime($dtask_end_date)), 'first_name'=>$task_owners[0]['first_name'], 'last_name'=>$task_owners[0]['last_name'], 'status'=>$ins['status']);

					// $param['to_mail'] 			= $taskSetToEmail.','.$admin_mail;
					$param['to_mail'] 			= $taskSetToEmail;
					$param['cc_mail'] 			= $from;
					$param['from_email'] 		= $from;
					$param['from_email_name'] 	= $user_name;
					$param['template_name'] 	= "New Task Notification";
					$param['subject'] 			= $subject;

					$this->email_template_model->sent_email($param);
				}
			}
			else
			{
				$json['error'] = TRUE;
				$json['errormsg'] = 'Task insert or edit error';
			}
		}
		$json['follow_up_id'] = $follow_up_id;
		echo json_encode($json);
	}
	
	/* getting a single task */
	function get_task($taskid)
	{
		$this->db->where('taskid', $taskid);
		$q = $this->db->get($this->cfg['dbpref'].'tasks');		
		if ($q->num_rows() > 0)
		{
			return $q->row();
		}
		else
		{
			return FALSE;
		}
	}
	
	/* getting a single task */
	function get_lead_task($taskid)
	{
		$this->db->where('taskid', $taskid);
		$q = $this->db->get($this->cfg['dbpref'].'lead_tasks');
		
		if ($q->num_rows() > 0)
		{
			return $q->row();
		}
		else
		{
			return FALSE;
		}
	}
	
	/* get tasks without leads */
	function get_random_tasks()
	{
		$html = '';
		
		if ( ! isset($_POST['id_set']) || ! preg_match('/[0-9,]+/', $_POST['id_set']))
		{
			$html = '';
		}
		else
		{
			$sql = "SELECT *, `".$this->cfg['dbpref']."tasks`.`start_date` AS `start_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`
					FROM `".$this->cfg['dbpref']."tasks`, `".$this->cfg['dbpref']."users`
					WHERE `".$this->cfg['dbpref']."tasks`.`taskid` IN ({$_POST['id_set']})
					AND `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid` 
					
					ORDER BY `".$this->cfg['dbpref']."tasks`.`is_complete`, `".$this->cfg['dbpref']."tasks`.`status`, `".$this->cfg['dbpref']."tasks`.`start_date`";
					
			$q = $this->db->query($sql);
			$data = $q->result_array();
			
			foreach ($data as $row)
			{
				$html .= $this->format_task($row);
			}
		}
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
		}
		echo $html;
	}
	
	/**
	 * Get tasks for a given job
	 */

	function get_job_tasks($lead_id)
	{	
	
	if(isset($_GET['task_completed']))
	{
		$data['taskcompleted']=$_GET['task_completed'];
		/*  1=> Approved Task, 0 Pending Tasks */
	}
	else
	{
		$data['taskcompleted']=0;
	}
		$uidd = $this->session->userdata['logged_in_user'];
		$uid = $uidd['userid'];
		 
		$html = '';
		$this->load->model('manage_task_category_model');
		$data['category_listing_ls'] = $this->project_model->getTaskCategoryList();
		$newarray=array();
		
		
		foreach($data['category_listing_ls'] as $row) 
		{
	
			$newarray[]=$this->request_model->taskCategoryQuery($row['id'],$lead_id,$row['task_category'],$data['taskcompleted']);
		}
		$data['pendingtasks'] =$this->request_model->taskCountQuery($lead_id,0);
		$data['completedtasks']=$this->request_model->taskCountQuery($lead_id,1);
		
		$data['newarray']=$newarray;
		$this->load->view('tasks/task_list_view', $data);
	}
	
	/**
	 * format the output HTML for a given task
	 * Changes made for
	 * Only the task owner has got the rights to re-assign the task to another user.
	 * Any other user who has got the same level of access, will still not be able to re-assign the tasks
	 * Task assigned To person will not be able to change the task description, planned start date, planned end date, actual end date.
	 */
	private function format_task($array, $type = 'job')
	{
		$uidd = $this->session->userdata['logged_in_user'];
		$uid = $uidd['userid'];
		$lead_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users ");
		$data['lead_assign'] = $lead_assigns->result_array();

		$res = array();
		
		$taskid = nl2br($array['taskid']);
		//$task_desk = nl2br($array['task']);
		/* $sqltask="select userid_fk,created_by,status from ".$this->cfg['dbpref']."tasks where taskid='$taskid'";
		$rssqltask=mysql_query($sqltask);
		$rows=mysql_fetch_array($rssqltask); */
		
		$sqltask = $this->db->query(" select userid_fk,created_by,status from ".$this->cfg['dbpref']."tasks where taskid='$taskid' ");
		$rows = $sqltask->row_array();
		
		$taskuid=$rows['userid_fk'];
		$taskcid=$rows['created_by'];
		$taskstatus = $rows['status'];
		if($uid==$taskcid){
			$task_desk = nl2br($array['task']);
			$taskread="";
		} else {
			$task_desk = nl2br($array['task']);
			$taskread ="readonly";
		}
		$select1 = "SELECT ".$this->cfg['dbpref']."users.first_name,".$this->cfg['dbpref']."users.userid FROM ".$this->cfg['dbpref']."users WHERE ".$this->cfg['dbpref']."users.userid=".$taskuid;	
		$dd1 = $this->db->query($select1);
		$res1 = $dd1->result();
		
		$task_remarks = nl2br($array['remarks']);
		$select = "SELECT ".$this->cfg['dbpref']."users.first_name,".$this->cfg['dbpref']."users.userid FROM ".$this->cfg['dbpref']."users WHERE ".$this->cfg['dbpref']."users.userid=".$array['created_by'];	
		$dd = $this->db->query($select);
		$res = $dd->result();
		#$html = $this->session->set_userdata('taskownerid', $res[0]->userid);		
		if (!isset($array['user_label']))
		{
			$array['user_label'] = '';
		}
		
		$own_task = $task_edit = $task_approve = '';
		
		//echo $this->userdata['role_id'];
			$options = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
			$opts = '';
			foreach ($options as $o)
			{
				$sel = ($array['status'] == $o) ? ' selected="selected"' : '';
				$opts .= "<option value=\"{$o}\"{$sel}>{$o}%</option>";
			}
			
			$task_edit = "<button type=\"submit\" onclick=\"openEditTask('{$array['taskid']}'); return false;\">Edit Task</button>";
			$task_approve = ($array['approved'] == 0) ? "<button type=\"submit\" onclick=\"approveTask('{$array['taskid']}'); return false;\">Approve Task</button>" : '';
			
		if($array['userid'] == $this->userdata['userid']) {		
			$own_task =  <<< EOD
				<select name="set_task_status_{$array['taskid']}" id="set_task_status_{$array['taskid']}" class="set-task-status">
				{$opts}
				</select>
				<div class="buttons">
					<button type="submit" onclick="setTaskStatus('{$array['taskid']}'); return false;">Set Status</button>
					{$task_edit}
				</div>
EOD;
}

		if ($array['created_by'] == $array['userid'] &&  $array['userid'] == $this->userdata['userid']) {
			$own_task =  <<< EOD
				<select name="set_task_status_{$array['taskid']}" id="set_task_status_{$array['taskid']}" class="set-task-status">
					{$opts}
				</select>
				<div class="buttons">
					<button type="submit" onclick="setTaskStatus('{$array['taskid']}'); return false;">Set Status</button>
					
				</div>		
EOD;
		} 

		
		$is_admin = '';
		if ($array['created_by'] == $this->userdata['userid'])
		{
			$is_admin = ($array['is_complete'] == 1) ? 'Task Complete!' : <<< EOD
			<a href="#" class="delete-task"onclick="setTaskStatus('{$array['taskid']}', 'delete'); return false;">Delete?</a>
			<div class="buttons">
				<button type="submit" class="positive" onclick="setTaskStatus('{$array['taskid']}', 'complete'); return false;">Approve</button>
				{$task_edit}
			</div>
EOD;
		}
		$isprior='';$priority=0;
		if(!empty($array['priority'])) 
		if($array['priority']==1 && $array['status']!= 100) $isprior=' style="background-color:purple;color:white;"';
		$is_complete = ($array['is_complete'] == 1) ? ' completed' : '';
		$marked_100pct = ($array['status'] == 100) ? ' marked_100pct' : '';
		if(!empty($array['priority'])) $priority=$array['priority'];
		
		if($uid==$taskcid){
			$start_date = date('d-m-Y', strtotime($array['start_date']));
			$starttaskread="";
		} else {
			$start_date = date('d-m-Y', strtotime($array['start_date']));
			$starttaskread ="read";
		}
		//$end_date = date('d-m-Y', strtotime($array['end_date']));
		if($uid==$taskcid){
			$end_date = date('d-m-Y', strtotime($array['end_date']));
			$endtaskread="";
		} else {
			$end_date = date('d-m-Y', strtotime($array['end_date']));
			$endtaskread ="read";
		}
		$end_time = date('gA', strtotime($array['end_date']));
		
		/*mychanges*/
		$actualstart_date=$array['actualstart_date'];
        if($actualstart_date == '0000-00-00 00:00:00') {
			$actualstart_date = '0000-00-00';
		} else {
			if($actualstart_date =='') {
				$actualstart_date='0000-00-00';
			} else {
				$actualstart_date=date('d-m-Y', strtotime($array['actualstart_date']));
			}
		}
		
		$actualend_date = $array['actualend_date'];
		if($uid == $taskcid) {
			if (($actualend_date == "") || ($actualend_date == "0000-00-00 00:00:00")) {
				$actualend_date = '0000-00-00';
				$actualend_dateread = "";
			} else {
				$actualend_date=date('d-m-Y', strtotime($actualend_date));
				$actualend_dateread="";
			}
		} else {
			if (($actualend_date == "") || ($actualend_date == "0000-00-00 00:00:00")) {
				$actualend_date = '0000-00-00';
				$actualend_dateread = "read";
			} else {
				$actualend_date=date('d-m-Y', strtotime($actualend_date));
				$actualend_dateread="read";
			} 
		}
		
		if($uid==$taskcid) {			
			$taskuserid = $array['user_label'];
			$taskuserid_read="";
		} else {
			$taskuserid=$array['user_label'];
			$taskuserid_read = "read";
		}

		
		$qc_required = (isset($array['require_qc'])) ? $array['require_qc'] : '0';
		foreach($data['lead_assign'] as $val) {
			$val['userid'];
			$val['first_name'];
		}
			
		$html = <<< EOD
					<table border="0" cellpadding="0" cellspacing="0" class="task-list-item{$is_complete}{$marked_100pct}" id="task-table-{$array['taskid']}">						
						<tr>
							<td valign="top">
								Task Desc
							</td>
							<td colspan="3" class="task"{$isprior}>
								{$task_desk} 
							</td>
						</tr>
						
						<tr>
							<td valign="top">
								Task Owner
							</td>
							<td colspan="3" class="item task-owner">
								{$res[0]->first_name}
							</td>
						</tr>
						
						<tr style="display:none;">
							<td valign="top" >
								User ID
							</td>
							<td class="task-uid" >
								{$uid}
							</td>	
						</tr>
						<tr style="display:none;">
							<td valign="top">
								Assigned ID
							</td>
							<td class="task-cid" >
								{$taskcid}
							</td>
						</tr>
						<tr>
							<td>
								Allocated to
							</td>
							<td colspan="3" class="item user-name" rel="{$array['userid']}" width="100">
								{$array['user_label']}
							</td>
							<td style="display:none" >
								Hours
							</td>
						</tr>
						
						
						<tr>
							<td>
								Planned Start Date
							</td>
							<td class="item start-date" width="100">
								{$start_date}
							</td>
							<td class="heading-item">
								Planned End Date
							</td>
							<td class="item end-date">
								<span class="date_part">{$end_date}</span> 
							</td>
						</tr>
						<tr>
							<td>
								Actual Start Date
							</td>
							<td class="item actualstart-date">
								{$actualstart_date}
							</td>
							<td  class="heading-item">
								Actual End Date
							</td>
							<td class="item actualend-date">
								{$actualend_date}
							</td>
						</tr>
						<tr>
							<td>Status</td>
							<td colspan = 3 class="item status-of-project">{$taskstatus}%</td>
						</tr>
						<tr>
							<td>Remarks</td>
							<td colspan = 3 class="edit-task-remarks"><textarea class="taskremarks" style="width:97%" readonly>{$task_remarks}</textarea></td>
						</tr>						
						
						<tr>
							<!--td colspan="2" valign="top">
								{$own_task}
								<span class="display-none task-require-qc">{$qc_required}</span>
								<span class="display-none priority">{$priority}</span>
							</td-->
							<td colspan="4" valign="top">
								{$is_admin}
							</td>
						</tr>
					</table>
EOD;
		
		return ($html);
	}
	// Ends here
	
	function set_task_status($type = 'job')
	{
		
		$this->load->model('user_model');
		$task_table = $this->cfg['dbpref'].'tasks';
		$fk = 'jobid_fk';
		if ($type == 'lead')
		{
			$task_table = $this->cfg['dbpref'].'lead_tasks';
			$fk = 'leadid_fk';
		}
		
		$json['error'] = TRUE;
		$taskid = (isset($_POST['taskid'])) ? $_POST['taskid'] : 0;
		//mychanges
		$taskstat = (isset($_POST['task_status'])) ? $_POST['task_status'] : 0;
		//mychanges ends
		$q = $this->db->get_where($task_table, array('taskid' => $taskid));
		
		if ($q->num_rows() > 0)
		{
			$data = $q->row();
			if (isset($_POST['set_as_complete']))
			{
				if ($data->status < 100)
				{
					$json['errormsg'] = 'Task status is not 100%';
				}
				else
				{
					$upd = array();
					$upd['is_complete'] 	 = 1;
					$upd['marked_complete']  = date('Y-m-d H:i:s');
					$upd['actualstart_date'] = date('Y-m-d H:i:s');
					$this->db->where('taskid', $taskid);
					$this->db->update($task_table, $upd);

					$uid=$data->userid_fk;
					$task_name=$data->task;
					if($upd['is_complete']==1) {
						$task_status="Completed";
					}					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];

					$task_owner 		= $this->user_model->get_user($uid);
					$taskSetTo			= $task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskStatusToEmail	= $task_owner[0]['email'];
					$start_date			= $data->start_date;
					$end_date			= $data->end_date;
					$hours				= $data->hours;
					$mins				= $data->mins;
					// $hm=$hours.'&nbsp;Hours&nbsp;and&nbsp;'.$mins.'&nbsp;mins';
					$start_date			= date('d-m-Y', strtotime($start_date));
					$end_date			= date('d-m-Y', strtotime($end_date));
					$completed_date		= date('l, jS F y h:iA', strtotime($upd['marked_complete']));
					$task_owner_name 	= $this->db->query("SELECT u.email, u.first_name, u.last_name, t.remarks, t.jobid_fk, t.start_date, t.jobid_fk, t.status, t.start_date, t.end_date, t.actualstart_date, t.actualend_date, t.task
														FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
														WHERE u.userid = t.created_by
														AND t.taskid ={$taskid}");
					$task_owners = $task_owner_name->result_array();
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					/*insert log-start here*/
					$log_detail  = "Task Approved: \n";
					$log_detail .= "\nTask Desc: ".$task_owners[0]['task'];
					$log_detail .= "\nAllocated To: ".$task_owners[0]['first_name'].'&nbsp;'.$task_owners[0]['last_name'];
					$log_detail .= "\nAllocated By: ".$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];;
					$log_detail .= "\nPlanned Start Date: ".date('d-m-Y', strtotime($task_owners[0]['start_date'])).'  :: Planned End Date:'.date('d-m-Y', strtotime($task_owners[0]['start_date']));
					$log_detail .= "\nActual Start Date: ".date('d-m-Y', strtotime($task_owners[0]['actualstart_date'])).'  :: Planned End Date:'.date('d-m-Y', strtotime($task_owners[0]['actualend_date']));
					$log_detail .= "\nRemarks: ".$task_owners[0]['remarks'];
					$log_detail .= "\nStatus: ".$task_owners[0]['status'].' %';
					$log = array();
					$log['jobid_fk']      = $task_owners[0]['jobid_fk'];
					$log['userid_fk']     = $this->userdata['userid'];
					$log['date_created']  = date('Y-m-d H:i:s');
					$log['log_content']   = $log_detail;
					
					$log_res = $this->project_model->insert_row("logs", $log);
					/*insert log-end here*/
				
					$subject='Task Completion Notification';
					$from = $this->userdata['email'];

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					$admin_mail=implode(',',$arrSetEmails);
					
					//email sent by email template
					$param = array();

					$param['email_data'] = array('task_name'=>$task_name, 'taskSetTo'=>$taskSetTo, 'remarks'=>$task_owners[0]['remarks'],'start_date'=>$start_date, 'end_date'=>$end_date,'first_name'=>$task_owners[0]['first_name'],'last_name'=>$task_owners[0]['last_name'],'task_status'=>$task_status);

					$param['to_mail'] = $taskStatusToEmail;
					$param['cc_mail'] = $from;
					$param['from_email'] = $from;
					$param['from_email_name'] = $user_name;
					$param['template_name'] = "Task Completion Notification";
					$param['subject'] = $subject;

					$this->email_template_model->sent_email($param);
					
					$json['set_complete'] = TRUE;
					$json['error']        = FALSE;
				}
			}
			else if (isset($_POST['delete_task']))
			{
				$data = $q->row();
				$this->db->where('taskid', $taskid);
				$this->db->delete($task_table);
				$user_name 		= $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$task_name		= $data->task;
				$task_createdby	= $data->created_by;
				$uid			= $data->userid_fk;
				$task_owner 	= $this->user_model->get_user($task_createdby);
				$taskCreatedBy	= $task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
				$taskCreatedByEmail = $task_owner[0]['email'];
				$task_allocated = $this->user_model->get_user($uid);
				$taskSetTo		= $task_allocated[0]['first_name'].'&nbsp;'.$task_allocated[0]['last_name'];
				$taskSetEmail	= $task_allocated[0]['email'];
				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				
				/*insert log-start here*/
				$log_detail  = "Task Deleted: \n";
				$log_detail .= "\nTask Desc: ".$data->task;
				$log_detail .= "\nAllocated To: ".$task_allocated[0]['first_name'].'&nbsp;'.$task_allocated[0]['last_name'];
				$log_detail .= "\nAllocated By: ".$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];;
				$log_detail .= "\nPlanned Start Date: ".date('d-m-Y', strtotime($data->start_date)).'  :: Planned End Date:'.date('d-m-Y', strtotime($data->start_date));
				$log_detail .= "\nActual Start Date: ".date('d-m-Y', strtotime($data->actualstart_date));
				$log_detail .= "\nRemarks: ".$data->remarks;
				$log_detail .= "\nStatus: ".$data->status.' %';
				$log = array();
				$log['jobid_fk']      = $data->jobid_fk;
				$log['userid_fk']     = $this->userdata['userid'];
				$log['date_created']  = date('Y-m-d H:i:s');
				$log['log_content']   = $log_detail;
				
				$log_res = $this->project_model->insert_row("logs", $log);
				/*insert log-end here*/
				
				$from			= $this->userdata['email'];
				$arrEmails 		= $this->config->item('crm');
				$arrSetEmails 	= $arrEmails['director_emails'];
				$admin_mail 	= implode(',',$arrSetEmails);
				$subject    	= 'Task Delete Notification';
				
				//email sent by email template
				$param = array();

				$param['email_data'] = array('print_fancydate'=>$print_fancydate, 'task_name'=>$task_name, 'user_name'=>$user_name, 'signature'=>$this->userdata['signature']);

				$param['to_mail'] 	 		= $taskSetEmail;
				$param['bcc_mail'] 	 		= $admin_mail;
				$param['from_email'] 		= $from;
				$param['from_email_name'] 	= $user_name;
				$param['template_name'] 	= "Task Delete Notification Message";
				$param['subject'] 			= $subject;
				
				$this->email_template_model->sent_email($param);
				
				
				$json['error'] = FALSE;	
				$json['task_delete'] = TRUE;
	
			}
			else
			{
				$upd = array();
				$upd['status'] = (int) $_POST['task_status'];
				
				if ($upd['status'] == 100)
				{
				    $upd['actualend_date'] = date('Y-m-d H:i:s');
					$upd['marked_100pct']  = date('Y-m-d H:i:s');
					$upd['task_stage'] 	   = 14;
					$json['marked_100pct'] = TRUE;
				}else { // Added For task set completion
					if($taskstat!='0'){
					$task_table = $this->cfg['dbpref'].'tasks';
					$ud = array();
					if($data->status == 100) {
						$upd['task_stage'] 	= 11;
					}
					$ud['status'] 			= $taskstat;
					$ud['actualstart_date'] = date('Y-m-d H:i:s');
					$this->db->where('taskid', $taskid);
					$this->db->update($task_table, $ud);
					}
				 }
				$this->db->where('taskid', $taskid);
				$this->db->update($task_table, $upd);
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$uid=$data->userid_fk;
				$task_createdby=$data->created_by;
				$start_date=$data->start_date;
				$end_date=$data->end_date;
				$hours=$data->hours;
				$mins=$data->mins;
				// $hm=$hours.'&nbsp;Hours&nbsp;and&nbsp;'.$mins.'&nbsp;mins';
				$start_date=date('d-m-Y', strtotime($start_date));
				$end_date=date('d-m-Y', strtotime($end_date));
				$task_name=$data->task;

				$task_status=$_POST['task_status'];
				$task_owner = $this->user_model->get_user($uid);
				$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
				$taskStatusToEmail=$task_owner[0]['email'];
				$task_owner_mail = $this->db->query("SELECT u.email, u.first_name, u.last_name, t.remarks, t.jobid_fk, t.start_date, t.jobid_fk, t.status, t.start_date, t.end_date, t.actualstart_date, t.actualend_date, t.task
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.created_by ={$task_createdby}
													AND t.taskid ={$taskid}");
				$task_owners = $task_owner_mail->result_array();
				
				/*insert log-start here*/
					
				$log_detail  = "Task Updated: \n";
				$log_detail .= "\nTask Desc: ".$task_owners[0]['task'];
				$log_detail .= "\nAllocated To: ".$task_owner[0]['first_name'].' '.$task_owner[0]['last_name'];
				$log_detail .= "\nAllocated By: ".$task_owners[0]['first_name'].' '.$task_owners[0]['last_name'];
				$log_detail .= "\nPlanned Start Date: ".date('d-m-Y', strtotime($task_owners[0]['start_date'])).'  :: Planned End Date:'.date('d-m-Y', strtotime($task_owners[0]['end_date']));
				$log_detail .= "\nActual Start Date: ".date('d-m-Y', strtotime($task_owners[0]['actualstart_date']));
				$log_detail .= "\nRemarks: ".$task_owners[0]['remarks'];
				$log_detail .= "\nStatus: ".$task_owners[0]['status'].' %';
				$log = array();
				$log['jobid_fk']      = $task_owners[0]['jobid_fk'];
				$log['userid_fk']     = $this->userdata['userid'];
				$log['date_created']  = date('Y-m-d H:i:s');
				$log['log_content']   = $log_detail;

				$log_res = $this->project_model->insert_row("logs", $log);
				
				/*insert log-end here*/

				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				
				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$admin_mail=implode(',',$arrSetEmails);
				$subject='Task Status Notification';

				//email sent by email template
				$param = array();

				$param['email_data'] = array('task_name'=>$task_name, 'taskSetTo'=>$taskSetTo, 'remarks'=>$task_owners[0]['remarks'], 'start_date'=>$start_date, 'end_date'=>$end_date, 'first_name'=>$task_owners[0]['first_name'],'last_name'=>$task_owners[0]['last_name'], 'task_status'=>$task_status);

				$param['to_mail'] = $taskStatusToEmail;
				$param['cc_mail'] = $from;
				$param['from_email'] = $from;
				$param['from_email_name'] = $user_name;
				$param['template_name'] = "Task Notification";
				$param['subject'] = $subject;
				
				$this->email_template_model->sent_email($param);

				$json['error'] = FALSE;
			}
		}
		else
		{
			$json['errormsg'] = 'Task does not exist';
		}
		
		echo json_encode($json);
	}
	
	/**
	 * Add job task for a user
	 * Edits a task
	 * Adds a random task for a user
	 */
	function add_lead_task($update = 'NO', $random = 'NO')
	{
		$this->load->model('user_model');
		
		$errors = array();
		
		if ($random != 'NO')
		{
			$_POST['leadid'] = 0;
		}
		
		$json['error'] = FALSE;
		$ins['leadid_fk'] = (int) $_POST['leadid'];
		$ins['task'] = $_POST['job_task'];
		$ins['userid_fk'] = $_POST['task_user'];
		$ins['hours'] = (int) $_POST['task_hours'];
		$ins['mins'] = (int) $_POST['task_mins'];
		
		$ins['approved'] = 1;
		
		$ins['created_by'] = $this->userdata['userid'];
		$ins['created_on'] = date('Y-m-d H:i:s');
		
		$task_start_date = explode('-', trim($_POST['task_start_date']));
		$task_end_date = explode('-', trim($_POST['task_end_date']));
		
		if (count($task_start_date) != 3 || ! $start_date = mktime(0, 0, 0, $task_start_date[1], $task_start_date[0], $task_start_date[2]))
		{
			$errors[] = 'Invalid Start Date!';
		}
		
		if (count($task_end_date) != 3 || ! $end_date = mktime(0, 0, 0, $task_end_date[1], $task_end_date[0], $task_end_date[2]))
		{
			$errors[] = 'Invalid End Date!';
		}
		
		if ($start_date < strtotime(date('Y-m-d')) && $update == 'NO')
		{
			$errors[] = 'Start date cannot be earlier than today!';
		}
		
		/*if ($end_date < strtotime(date('Y-m-d')))
		{
			$errors[] = 'End date cannot be earlier than today!';
		}*/
		
		if ($end_date < $start_date)
		{
			$errors[] = 'End date cannot be earlier than start date';
		}
		
		if ($ins['leadid_fk'] == 0 && $random == 'NO')
		{
			$errors[] = 'Valid leadid is required!';
		}
		
		if (count($errors) > 0)
		{
			$json['error'] = TRUE;
			$json['errormsg'] = implode("\n", $errors);
		}
		else
		{
			$ins['start_date'] = date('Y-m-d H:i:s', $start_date);
			$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			$ins['priority'] = (isset($_POST['priority']) && $_POST['priority'] == 'YES') ? '1' : '0';
			if ($update != 'NO' && $old_task = $this->get_lead_task($update))
			{
				// update
				$this->db->where('taskid', $update);
				$this->db->update($this->cfg['dbpref'].'lead_tasks', $ins);
				
				$ins['user_label'] = $_POST['user_label'];
				$ins['status'] = $ins['is_complete'] = 0;
				$ins['taskid'] = $update;
				$ins['userid'] = $ins['userid_fk'];
				$json['html'] = $this->format_task($ins, 'lead');
			}
			else if ($update == 'NO')
			{
				if ( ! $this->db->insert($this->cfg['dbpref'].'lead_tasks', $ins))
				{
					$json['error'] = TRUE;
					$json['errormsg'] = 'Task insert error';
				}
				
			}
			else
			{
				$json['error'] = TRUE;
				$json['errormsg'] = 'Task insert or edit error';
			}
		}
		
		echo json_encode($json);
	}
    
    
	/**
	 * Get tasks for a given lead
	 */
	function get_lead_tasks($lead_id)
	{
		$sql = "SELECT *, `".$this->cfg['dbpref']."lead_tasks`.`start_date` AS `start_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`
				FROM `".$this->cfg['dbpref']."lead_tasks`, `".$this->cfg['dbpref']."users`
				WHERE `".$this->cfg['dbpref']."lead_tasks`.`leadid_fk` = ?
				AND `".$this->cfg['dbpref']."lead_tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
				ORDER BY `".$this->cfg['dbpref']."lead_tasks`.`is_complete`, `".$this->cfg['dbpref']."lead_tasks`.`status`, `".$this->cfg['dbpref']."lead_tasks`.`start_date`";
				
		$q = $this->db->query($sql, array('jobid_fk' => $lead_id));
		$data = $q->result_array();
		$html = '';
		foreach ($data as $row)
		{
			$html .= $this->format_task($row);
		}
		
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this lead!</p>';
		}
		
		echo $html;
	}
	
	function get_job_overview($lead_id, $return = FALSE)
	{
		$this->db->order_by('due_date', 'asc');
		$this->db->order_by('position', 'asc');
		$q = $this->db->get_where($this->cfg['dbpref'].'milestones', array('jobid_fk' => $lead_id));
		
		$rows = $q->result();
		
		$data = $this->job_overview_html($rows);
		
		if ($return)
		{
			return $data;
		}
		
		echo $data;
	}
	
	function save_job_overview($lead_id)
	{	
		$this->db->where('jobid_fk', $lead_id);
		$this->db->delete($this->cfg['dbpref'].'milestones');
		
		$mc = count($_POST['milestone']);
		for ($i = 0; $i < $mc; $i++)
		{
			$date_parts = explode('-', $_POST['milestone_date'][$i]);
			if (trim($_POST['milestone'][$i]) == '' || count($date_parts) != 3 || ! $date = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]))
			{
				continue;
			}
			
			$ins['jobid_fk'] = $lead_id;
			$ins['milestone'] = $_POST['milestone'][$i];
			$ins['due_date'] = date('Y-m-d', $date);
			$ins['status'] = $_POST['milestone_status'][$i];
			$ins['position'] = $i;
			
			$this->db->insert($this->cfg['dbpref'].'milestones', $ins);
		}
		echo $this->get_job_overview($lead_id, TRUE);
	}
	
	function job_overview_html($object)
	{
		$html = '';
		foreach ($object as $row)
		{
			$status_select_1 = ($row->status == 1) ? ' selected="selected"' : '';
			$status_select_2 = ($row->status == 2) ? ' selected="selected"' : '';
			$qa = $this->db->query("select lead_assign, belong_to from ".$this->cfg['dbpref']."leads where lead_id = '".$row->jobid_fk."' ");
			$lead_details  = $qa->row_array();
			// echo "<pre>"; print_r($lead_details);
			$leadAssignArr = array();
			if(!empty($lead_details['lead_assign'])) {
				$leadAssignArr = @explode(', ', $lead_details['lead_assign']);
			}
			// if ($this->userdata['role_id'] == 1 || $lead_details['belong_to'] == $this->userdata['userid'] || $lead_details['lead_assign'] == $this->userdata['userid']) {
			if ($this->userdata['role_id'] == 1 || $lead_details['belong_to'] == $this->userdata['userid'] || in_array($this->userdata['userid'], $lead_details['lead_assign'])) {
			$html .= '
			<tr>
				<td class="milestone">
					<input type="text" name="milestone[]" class="textfield width250px" value="' . htmlentities($row->milestone, ENT_QUOTES) . '" />
				</td>
				<td class="milestone-date">
					<input type="text" name="milestone_date[]" class="textfield width80px pick-date" value="' . date('d-m-Y', strtotime($row->due_date)) . '"/>
				</td>
				<td class="milestone-status">
					<select name="milestone_status[]" class="textfield width80px">
						<option value="0">Scheduled</option>
						<option value="1"' . $status_select_1 . '>In Progress</option>
						<option value="2"' . $status_select_2 . '>Completed</option>
					</select>
				</td>
				<td class="milestone-action" valign="middle">
					&nbsp; <a href="#" onclick="removeMilestoneRow(this); return false;">Remove</a>
				</td>
			</tr>
			';
			} else {
				$html .= '
				<tr>
					<td class="milestone">
						<input type="text" name="milestone[]" class="textfield width250px" value="' . htmlentities($row->milestone, ENT_QUOTES) . '" />
					</td>
					<td class="milestone-date">
						<input type="text" name="milestone_date[]" class="textfield width80px pick-date" value="' . date('d-m-Y', strtotime($row->due_date)) . '"/>
					</td>
					<td class="milestone-status">
						<select name="milestone_status[]" class="textfield width80px">
							<option value="0">Scheduled</option>
							<option value="1"' . $status_select_1 . '>In Progress</option>
							<option value="2"' . $status_select_2 . '>Completed</option>
						</select>
					</td>
				</tr>
				';
			}
		}
		
		return $html;
	}
	

	
	function get_packages($hostingid=0){
		if($hostingid==0) return false;
		$q=$this->db->query("SELECT * FROM ".$this->cfg['dbpref']."hosting_package HP, ".$this->cfg['dbpref']."package P WHERE P.package_id=HP.packageid_fk && HP.hostingid_fk={$hostingid} && P.status='active'");
		$r=$q->result_array();
		if(sizeof($r)>0) echo json_encode($r[0]);
		else return false;
	}
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function query_file_upload($lead_id, $lead_query, $status, $type = 'job')
	{	
	
		/**
		 * we need to know errors
		 * not the stupid ilisys restricted open_base_dir errors
		 */
		//error_reporting(E_ERROR);
		
		$json['error'] = '';
		$json['msg'] = '';
		
		$f_dir = UPLOAD_PATH .'query/'; 
		
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		$f_dir = $f_dir.$lead_id; 
		
		if (!is_dir($f_dir))
		{
			mkdir($f_dir);
			chmod($f_dir, 0777);
		}
		
		
		if (isset($_FILES['query_file']) && is_uploaded_file($_FILES['query_file']['tmp_name']))
		{
			$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['query_file']['name']);
			
			if (preg_match('/\.(php|js|exe)+$/', $f_name, $matches)) // basic sanity
			{
				$json['error'] = TRUE;
				$json['msg'] = "You uploaded a file type that is not allowed!\nYour file extension : {$matches[1]}";
			}
			else // good to go
			{
				// full path
				$full_path = $f_dir . '/' . $f_name;
				if (is_file($full_path))
				{
					$f_name = time() . $f_name;
					$full_path = $f_dir . '/' . $f_name;
				}
				
				if(move_uploaded_file($_FILES['query_file']['tmp_name'], $full_path)) {
					$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
					$users = $this->db->query($qry);
					$user = $users->result_array();

					$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."leads WHERE lead_id=".$lead_id.")";
					$customers = $this->db->query($qry1);
					$customer = $customers->result_array();
					if($status == 'query') {
						$st = $status;
						$rep_to = 0;
					} else {
						$status = explode('-',$status);
						$st = $status[0];
						$rep_to = $status[1];
					}
					
					//print "first =>".$rep_to; exit
					
					$userdata = $this->session->userdata('logged_in_user');
					$lead_query = addslashes($lead_query);
					$query = "INSERT INTO ".$this->cfg['dbpref']."lead_query (lead_id,user_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from,status,replay_query) 
					VALUES(".$lead_id.",'".$userdata['userid']."','".$lead_query."','".$f_name."','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."','".$st."',".$rep_to.")";
					$q = $this->db->query($query);
					
					$insert_id = $this->db->insert_id();
					
					$json['up_date'] = date('d-m-Y');
					$json['lead_query'] = $lead_query;
					$json['firstname'] = $user[0]['first_name'];
					$json['lastname'] = $user[0]['last_name'];
					$json['replay_id'] = $insert_id;
					//echo $this->db->last_query();
				
				}
				$fz = filesize($full_path);
				$kb = 1024;
				$mb = 1024 * $kb;
				if ($fz > $mb)
				{
				  $out = round($fz/$mb, 2);
				  $out .= 'Mb';
				}
				else if ($fz > $kb) {
				  $out = round($fz/$kb, 2);
				  $out .= 'Kb';
				} else {
				  $out = $fz . ' Bytes';
				}
				
				$json['error'] = FALSE;
				$json['msg'] = "File successfully uploaded!";
				$json['file_name'] = $f_name;			
				$json['file_size'] = $out;
				
			}
			
		}
		else 
		{
			$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
			$users = $this->db->query($qry);
			$user = $users->result_array();

			$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."leads WHERE lead_id=".$lead_id.")";
			$customers = $this->db->query($qry1);
			$customer = $customers->result_array();
			if($status == 'query') {
				$st = $status;
				$rep_to = 0;
			} else {
				$status = explode('-',$status);
				$st = $status[0];
				$rep_to = $status[1];
			}
			$userdata = $this->session->userdata('logged_in_user');
			$lead_query = addslashes($lead_query);
			$query = "INSERT INTO ".$this->cfg['dbpref']."lead_query (lead_id,user_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from,status,replay_query) 
			VALUES(".$lead_id.",'".$userdata['userid']."','".$lead_query."','File Not Attached','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."','".$st."',".$rep_to.")";		
			$q = $this->db->query($query);	
			
			$insert_id = $this->db->insert_id();
			
			$json['replay_id']  = $insert_id;				
			$json['up_date']    = date('d-m-Y');
			$json['lead_query'] = str_replace('\\', '', $lead_query);
			$json['firstname']  = $user[0]['first_name'];
			$json['lastname']   = $user[0]['last_name'];
		}
		echo json_encode($json);
	}
	
	// public function getProjectMembers() {
	
		// $user_data = $this->session->userdata('logged_in_user');
		// // $usid['role_id']; 
		// $data = real_escape_array($this->input->post());
		
		// $fa_folder = $this->input->post('fa_folder');
		// $fa_files = $this->input->post('fa_files');
		// $parent_folder_id = $this->input->post('parent_folder_id');
		
		// $arrFiles = array();
		// $arrFolders = array();
		// $existing_rows = 0;

		// if(isset($fa_folder) && !empty($fa_folder)) {
			// $arrFolder = rtrim($fa_folder, ',');		
			// $arrFolders = explode(',', $arrFolder);
		// }
		
		// if(isset($fa_files) && !empty($fa_files)) {	
			// $arrFile = rtrim($fa_files, ',');		
			// $arrFiles = explode(',', $arrFile);		
		// }
		
		// if(empty($arrFolders) && empty($arrFiles) && !empty($parent_folder_id)) {			
			// $arrFolders = explode(',', $parent_folder_id);		
		// }

		// $array_merge = array_merge($arrFolders, $arrFiles);
	
		// $project_members   = $this->request_model->get_project_members($data['curr_job_id']); // This array to get a project normal members(Developers) details.
		// $project_leaders   = $this->request_model->get_project_leads($data['curr_job_id']); // This array to get "Lead Owner", "Lead Assigned to", ""Project Manager" details.
		// $arrProjectMembers = array_merge($project_members, $project_leaders); // Merge the project membes and project leaders array.				
		// $arrProjectMembers = array_unique($arrProjectMembers, SORT_REGULAR); // Remove the duplicated uses form arrProjectMembers array.					
		// $arrLeadInfo       = $this->request_model->get_lead_info($data['curr_job_id']); // This function to get a current lead informations.		
		
		// $arrLeaders = $this->request_model->usersArraysToSingleDimetioal($project_leaders); // This function convert users two dimentional array to single dimentional array.

		//echo '<pre>'; print_r($arrLeaders);exit;
		
		
		// $html = '';		
		// $html .="<table style='margin-bottom:10px;'>";		
		// $html .="<tr height='20'><th width='170'>Users</th> <th width='60'>Read</th> <th width='60'>Write</th> <th width='60'>Delete</th></tr>";
		// $read_array = array();
		// $write_array = array();
		// $delete_array = array();
		// $arr_disabled_users = array();
		// foreach($arrProjectMembers as $members){
			// if(!empty($members)) {
				// if($user_data['userid'] != $members['userid']) {

					// $arrUserInfo= $this->request_model->getUserInfomationById($members['userid']);			
				
					##############  Check already Checked Start Here  ################//
					// $read_folder_checked = '';
					// $write_folder_checked = '';
					// $delete_folder_checked = '';
					
					// $read_folder_disabled = '';
					// $write_folder_disabled = '';
					// $delete_folder_disabled = '';
					
						
					// // echo $arrFolders[0]['folder_id'].'====='.$members['userid'];
					// if(isset($arrFolders) && !empty($arrFolders)) {
					// $checkFolderAccess= $this->request_model->check_lead_file_access($data['curr_job_id'], 'folder_id', $arrFolders[0], $members['userid']);
					// }else if(isset($arrFiles) && !empty($arrFiles)) {
					// $checkFolderAccess= $this->request_model->check_lead_file_access($data['curr_job_id'], 'file_id', $arrFiles[0], $members['userid']);
					// }

					// if(isset($checkFolderAccess) && !empty($checkFolderAccess)) {
					
						// $read_folder_checked = ($checkFolderAccess->lead_file_access_read == 1)?'checked':'';
						// $write_folder_checked = ($checkFolderAccess->lead_file_access_write == 1)?'checked':'';
						// $delete_folder_checked = ($checkFolderAccess->lead_file_access_delete == 1)?'checked':'';		
						// $existing_rows = 1;					
					// }

					// array_push($read_array, $read_folder_checked);
					// array_push($write_array, $write_folder_checked);
					// array_push($delete_array, $delete_folder_checked);
					
					##############  Check already Checked End Here  ################//
					// $html .="<tr>";
					// $html .="<td>";				
					// if($user_data['role_id'] !=1 && in_array($members['userid'], $arrLeaders)) {				
					// $read_folder_disabled = 'disabled';
					// $write_folder_disabled = 'disabled';
					// $delete_folder_disabled = 'disabled';	
					// $html .="<input type='hidden'  name='read[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_read."'>";
					// $html .="<input type='hidden'  name='write[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_write."'>";
					// $html .="<input type='hidden'  name='delete[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_delete."'>";
					// array_push($arr_disabled_users, $members['userid']);			
					// }else if($arrUserInfo['role_id'] ==1 && in_array($members['userid'], $arrLeaders)) {				
					// $read_folder_disabled = 'disabled';
					// $write_folder_disabled = 'disabled';
					// $delete_folder_disabled = 'disabled';	
					// $html .="<input type='hidden'  name='read[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_read."'>";
					// $html .="<input type='hidden'  name='write[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_write."'>";
					// $html .="<input type='hidden'  name='delete[".$members['userid']."]' value='".$checkFolderAccess->lead_file_access_delete."'>";
					// array_push($arr_disabled_users, $members['userid']);
					// }
											
					
					// $html .=$members['first_name']." ".$members['last_name']."</td>
							// <input type='hidden' name='users[".$members['userid']."]' value='".$members['userid']."'></td>";			
					// $html .="<td>&nbsp;&nbsp;<input type='checkbox'  ".$read_folder_checked." ".$read_folder_disabled." name='read[".$members['userid']."]' value='1'></td>";
					// $html .="<td>&nbsp;&nbsp;<input type='checkbox'  ".$write_folder_checked." ".$write_folder_disabled." name='write[".$members['userid']."]' value='1'></td>";
					// $html .="<td>&nbsp;&nbsp;<input type='checkbox'  ".$delete_folder_checked." ".$delete_folder_disabled." name='delete[".$members['userid']."]' value='1'></td>";
					// $html .="</tr>";
				// }
			// }
		// }
		// $html .="</table>";
		
		
		//echo '<pre>'; print_r($arr_disabled_users); exit;
		
		
		//##############  Check all selected files have a same permissions or not start here  ################//
		// $arrFileReadAccess = array();
		// $arrFileWriteAccess = array();
		// $arrFileDeleteAccess = array();
		// $arrFileReadTotal = array();
		// $arrFileWriteTotal = array();
		// $arrFileDeleteTotal = array();		
		// if(isset($arrFiles) && !empty($arrFiles)) {
		
			// for($i=0; $i<count($arrFiles); $i++) 
			// {
				// $checkAccess = array();
				// $arrLeadFilesAccess= $this->request_model->check_lead_file_access_by_ids($data['curr_job_id'], 'file_id', $arrFiles[$i]);
				
				// if(isset($arrLeadFilesAccess) && !empty($arrLeadFilesAccess)) {
				
					// foreach($arrLeadFilesAccess as $listLeadAccessFile) {

						// if($user_data['userid'] != $listLeadAccessFile['userid']) {									

							// if(!in_array($listLeadAccessFile['userid'], $arr_disabled_users)) {									
								// $arrFileReadAccess[$listLeadAccessFile['file_id']][$listLeadAccessFile['userid']] = $listLeadAccessFile['lead_file_access_read'];
								// $arrFileWriteAccess[$listLeadAccessFile['file_id']][$listLeadAccessFile['userid']] = $listLeadAccessFile['lead_file_access_write'];
								// $arrFileDeleteAccess[$listLeadAccessFile['file_id']][$listLeadAccessFile['userid']] = $listLeadAccessFile['lead_file_access_delete'];
							// }
						// }
					// }
				// $existing_rows = 1;
				// }
				// $arrFileReadTotal[$i] = array_sum($arrFileReadAccess[$arrFiles[$i]]);
				// $arrFileWriteTotal[$i] = array_sum($arrFileWriteAccess[$arrFiles[$i]]);
				// $arrFileDeleteTotal[$i] = array_sum($arrFileDeleteAccess[$arrFiles[$i]]);
			// }
		// }
	
		//##############  Check all selected files have a same permissions or not end here  ################//
		
		//##############  Check all selected folders have a same permissions or not start here  ################//	
		// $arrFolderReadAccess = array();
		// $arrFolderWriteAccess = array();
		// $arrFolderDeleteAccess = array();
		// $arrFolderReadTotal = array();
		// $arrFolderWriteTotal = array();
		// $arrFolderDeleteTotal = array();		
		// if(isset($arrFolders) && !empty($arrFolders)) {
			
			// for($j=0; $j<count($arrFolders); $j++) 
			// {					
			
				// $arrLeadFolderAccess= $this->request_model->check_lead_file_access_by_ids($data['curr_job_id'], 'folder_id', $arrFolders[$j]);
				
				// if(isset($arrLeadFolderAccess) && !empty($arrLeadFolderAccess)) {
				
					// foreach($arrLeadFolderAccess as $listLeadAccessFolder) {	

						// if($user_data['userid'] != $listLeadAccessFolder['userid']) {									

							// if(!in_array($listLeadAccessFolder['userid'], $arr_disabled_users)) {										
					
								// $arrFolderReadAccess[$listLeadAccessFolder['folder_id']][$listLeadAccessFolder['userid']] = $listLeadAccessFolder['lead_file_access_read'];
								// $arrFolderWriteAccess[$listLeadAccessFolder['folder_id']][$listLeadAccessFolder['userid']] = $listLeadAccessFolder['lead_file_access_write'];
								// $arrFolderDeleteAccess[$listLeadAccessFolder['folder_id']][$listLeadAccessFolder['userid']] = $listLeadAccessFolder['lead_file_access_delete'];		
							// }
						// }										
					
					// }
					
					// $existing_rows = 1;
				
				// }
				
				// $arrFolderReadTotal[$j] = array_sum($arrFolderReadAccess[$arrFolders[$j]]);
				// $arrFolderWriteTotal[$j] = array_sum($arrFolderWriteAccess[$arrFolders[$j]]);
				// $arrFolderDeleteTotal[$j] = array_sum($arrFolderDeleteAccess[$arrFolders[$j]]);
			
			// }

		// }
		//##############  Check all selected folders have a same permissions or not end here  ################//
	

//echo '<pre>';print_r($arrFolderReadTotal);
//echo '<pre>';print_r($arrFolderWriteTotal);
//echo '<pre>';print_r($delete_folder_result1);		

			// /*		
			// *
			// *@ Remove empty file array values.
			// *
			// */
			// $read_file_result1 = array_diff($arrFileReadTotal, array( '' ) );
			// $write_file_result1 = array_diff($arrFileWriteTotal, array( '' ) );
			// $delete_file_result1 = array_diff($arrFileDeleteTotal, array( '' ) );
			

			// /*		
			// *
			// *@ Create files unique values.
			// *
			// */
			// $read_file_result = array_unique($read_file_result1);
			// $write_file_result = array_unique($write_file_result1);
			// $delete_file_result = array_unique($delete_file_result1);

			// /*		
			// *
			// *@ Remove empty folder array values.
			// *
			// */

			// $read_folder_result1 = array_diff($arrFolderReadTotal, array( '' ) );
			// $write_folder_result1 = array_diff($arrFolderWriteTotal, array( '' ) );
			// $delete_folder_result1 = array_diff($arrFolderDeleteTotal, array( '' ) );


			// /*		
			// *
			// *@ Create folder unique values.
			// *
			// */
			// $read_folder_result = array_unique($read_folder_result1);
			// $write_folder_result = array_unique($write_folder_result1);
			// $delete_folder_result = array_unique($delete_folder_result1);
		
		
		// if(isset($arrFiles) && !empty($arrFiles)  && !empty($arrFolders)) {
		
			// /*		
			// *
			// *@ Merge selected files array and selected folders array values.
			// *
			// */
			// $read_merge_result1 = array_merge($read_file_result1, $read_folder_result1);
			// $write_merge_result1 = array_merge($write_file_result1, $write_folder_result1);
			// $delete_merge_result1 = array_merge($delete_file_result1, $delete_folder_result1);
			
			// /*		
			// *
			// *@ Create unique Merged files array and folders array values.
			// *
			// */			
			// $read_merge_result = array_unique($read_merge_result1);
			// $write_merge_result = array_unique($write_merge_result1);
			// $delete_merge_result = array_unique($delete_merge_result1);
		
		// }
		
//echo '<pre>';print_r($read_file_result1);
//echo '<pre>';print_r($write_file_result1);
//echo '<pre>';print_r($delete_file_result1);		

//echo '<pre>';print_r($read_folder_result1);
//echo '<pre>';print_r($write_folder_result1);
//echo '<pre>';print_r($delete_folder_result1);		


		//echo '=== Read==='.count($read_merge_result).'====='.count($write_merge_result).'====='.count($delete_merge_result);

	
		   /*		
			*
			*@ Checking the folder and file access
			*
			*/	
			
			
			
		// if(isset($arrFolders) && !empty($arrFolders)  && empty($arrFiles)) {
		
			// if(isset($array_merge) && (int)count($array_merge)>1  && $existing_rows == 1 && (count($read_folder_result) != 1  || count($write_folder_result) != 1 || count($delete_folder_result) != 1) ) {
			// $html .='<div id="messages" style="width:74%;"><p>Some folders have a different permissions. If you save means its overwrite existing file permissions.</p></div>';
			// }
		
		// }else if(isset($arrFiles) && !empty($arrFiles)  && empty($arrFolders)) {
		
			// if(isset($array_merge) && (int)count($array_merge)>1  && $existing_rows == 1 && ( count($read_file_result) != 1  || count($write_file_result) != 1 || count($delete_file_result) != 1 )) {
			// $html .='<div id="messages" style="width:74%;"><p>Some files have a different permissions. If you save means its overwrite existing file permissions.</p></div>';
			// }
		
		// }else if(isset($arrFiles) && !empty($arrFiles)  && !empty($arrFolders)) {
		
			// if(isset($array_merge) && (int)count($array_merge)>1  && $existing_rows == 1 && ( (count($read_file_result) != 1  || count($write_file_result) != 1 || count($delete_file_result) != 1)  ||  (count($read_folder_result) != 1  || count($write_folder_result) != 1 || count($delete_folder_result) != 1) )) {
			// $html .='<div id="messages" style="width:74%;"><p>Some files or folders have a different permissions. If you save means its overwrite existing file permissions. </p></div>';
			// }else if(isset($array_merge) && (int)count($array_merge)>1  && $existing_rows == 1 && (count($read_merge_result) != 1  || count($write_merge_result) != 1 || count($delete_merge_result) != 1)) {
			// $html .='<div id="messages" style="width:74%;"><p>Some files or folders have a different permissions. If you save means its overwrite existing file permissions.</p></div>';
			// }
		
		// }
		
		// echo $html;
	// }
	
	public function getProjectMembers() {
	
		$user_data = $this->session->userdata('logged_in_user');
	
		$data = real_escape_array($this->input->post());
		$project_members = $this->request_model->get_project_members($data['curr_job_id']);
		// echo "<pre>"; print_r($project_members); exit;
		$html ="<ul>";
		foreach($project_members as $members){
			if($user_data['userid'] != $members['userid']) {
				$html .="<li class=pad-all>";
				$html .=$members['first_name']." ".$members['last_name'];
				$html .="<input type='hidden' name='users[".$members['userid']."]' value='".$members['userid']."'>";
				// $html .="&nbsp;&nbsp;<input type='checkbox' name='read_".$members['userid']."' value='1'>&nbsp;Read";
				// $html .="&nbsp;&nbsp;<input type='checkbox' name='write_".$members['userid']."' value='1'>&nbsp;Write";
				// $html .="&nbsp;&nbsp;<input type='checkbox' name='delete_".$members['userid']."' value='1'>&nbsp;Delete";
				$html .="&nbsp;&nbsp;<input type='checkbox' name='read[".$members['userid']."]' value='1'>&nbsp;Read";
				$html .="&nbsp;&nbsp;<input type='checkbox' name='write[".$members['userid']."]' value='1'>&nbsp;Write";
				$html .="&nbsp;&nbsp;<input type='checkbox' name='delete[".$members['userid']."]' value='1'>&nbsp;Delete";
				$html .="</li>";
			}
		}
		$html .="</ul>";
		echo $html;
	}
	
	/*		
	*
	*@ Author Mani.S
	*@ Function saveAccessRights.
	*@ Access Public.			
	*
	*/	
	public function saveAccessRights() {
		$data = real_escape_array($this->input->post());
					
		$folder_array = array();
		$file_array = array();
		$lead_id = $data['fa_lead_id'];
		$folder_id = $data['fa_folder'];
		$file_id = $data['fa_file'];
		$parent_folder_id = $data['parent_folder_id'];
		
		$user_data = $this->session->userdata('logged_in_user');
		
		if(isset($folder_id) && !empty($folder_id)) {
		
			$folder_array = explode(',', $folder_id); 
		
		}
		
		if(isset($file_id) && !empty($file_id)) {
		
			$file_array = explode(',', $file_id); 
		
		}
		
		if(empty($folder_array) && empty($file_array) && !empty($parent_folder_id)) {		
			$folder_array = explode(',', $parent_folder_id); 		
		}
		
		
		//echo '<pre>'; print_r($folder_array);exit;
		
		if(isset($folder_array) && !empty($folder_array)) {
		
		$folder_filter_array = array_filter($folder_array);
		
			for($i=0; $i<count($folder_filter_array); $i++) {
			
				if(isset($data['users']) && !empty($data['users'])) {
				
					foreach($data['users'] as $folder_key=>$folder_value) {
					
						$read_status  = isset($data['read'][$folder_value])?$data['read'][$folder_value]:0;
						$write_status  = isset($data['write'][$folder_value])?$data['write'][$folder_value]:0;
						$delete_status  = isset($data['delete'][$folder_value])?$data['delete'][$folder_value]:0;
						
						
						$sql_delete = '  DELETE   FROM  `'.$this->cfg['dbpref'].'lead_file_access`   WHERE 
															userid = '.(int)$folder_value.' AND
															lead_id = '.(int)$lead_id.' AND
															folder_id = '.(int)$folder_filter_array[$i].'';
															
						mysql_query($sql_delete);					
						
						$sql = ' INSERT INTO `'.$this->cfg['dbpref'].'lead_file_access`	SET 
															userid = '.(int)$folder_value.',
															lead_id = '.(int)$lead_id.',
															folder_id = '.(int)$folder_filter_array[$i].',
															lead_file_access_read = '.(int)$read_status.',
															lead_file_access_write = '.(int)$write_status.',
															lead_file_access_delete = '.(int)$delete_status.',
															lead_file_access_created = UNIX_TIMESTAMP(),
															lead_file_access_created_by = '.(int)$user_data['userid'].'';
						mysql_query($sql);							
					
					}
				
				}
			
			}
		
		}
		
		
		if(isset($file_array) && !empty($file_array)) {
			$file_filter_array = array_filter($file_array);
			for($i=0; $i<count($file_filter_array); $i++) {
			
				if(isset($data['users']) && !empty($data['users'])) {
				
					foreach($data['users'] as $file_key=>$file_value) {
					
						$read_status  = isset($data['read'][$file_value])?$data['read'][$file_value]:0;
						$write_status  = isset($data['write'][$file_value])?$data['write'][$file_value]:0;
						$delete_status  = isset($data['delete'][$file_value])?$data['delete'][$file_value]:0;
						
						
						$sql_delete = '  DELETE   FROM  `'.$this->cfg['dbpref'].'lead_file_access`   WHERE 
															userid = '.(int)$file_value.' AND
															lead_id = '.(int)$lead_id.' AND
															file_id = '.(int)$file_filter_array[$i].'';
															
						mysql_query($sql_delete);							
						
						$sql = ' INSERT INTO `'.$this->cfg['dbpref'].'lead_file_access`	SET 
															userid = '.(int)$file_value.',
															lead_id = '.(int)$lead_id.',
															file_id = '.(int)$file_filter_array[$i].',
															lead_file_access_read = '.(int)$read_status.',
															lead_file_access_write = '.(int)$write_status.',
															lead_file_access_delete = '.(int)$delete_status.',
															lead_file_access_created = UNIX_TIMESTAMP(),
															lead_file_access_created_by = '.(int)$user_data['userid'].'';
						mysql_query($sql);							
					
					}
				
				}
			
			}
		
		}
		$json['error'] = FALSE;
		$json['result'] = TRUE;
		$json['msg'] = "File permissions has been changed successfully!";
		echo json_encode($json);
	}
	
	
	/*		
	*
	*@ Author Mani.S
	*@ Function check_access_permissions.
	*@ Param $lead_id, $folder_id, $userid.
	*@ Access Public.			
	*
	*/	
	public function check_access_permissions($lead_id, $folder_id, $user_id)
	{
		$user_data = $this->session->userdata('logged_in_user');
		
		//lead_folder_access
		$this->db->select('access_type');
	    $this->db->from($this->cfg['dbpref'] . 'lead_folder_access');
	    $this->db->where(array('lead_id'=>$lead_id, 'folder_id'=>$folder_id, 'user_id'=>$user_id));
	    $sql = $this->db->get();
	    return $sql->row_array();
	}
	
	public function check_root_folder_read_access($root_folder_id)
	{
		$user_data = $this->session->userdata('logged_in_user');	
		
		if($user_data['role_id'] == 1) {
			echo 1; exit;
		}
		
		$arrFolderId = $this->request_model->getParentFfolderId($root_folder_id, 0); 
		$filefolder_id = $arrFolderId['folder_id'];		
		$checkFolderAccess= $this->request_model->check_lead_file_access($root_folder_id, 'folder_id', $filefolder_id, $user_data['userid']);		
		echo $checkFolderAccess->lead_file_access_read; exit;
	}
	
	public function get_task_edit_form()
	{
		$task_det = array();
		$task_det = $this->request_model->get_task_info_by_id($this->input->post('taskid'));
		echo json_encode($task_det); exit;
	}
	
	public function get_task_edit_form_by_id()
	{
		$task_det = array();
		$task_det = $this->request_model->get_task_info_by_id($this->input->post('taskid'));
		echo json_encode($task_det); exit;
	}
	
	/**
	 * @method get_folder_tree_struct()
	 * 
	 */
	public function add_tags() {
		$data    		= real_escape_array($this->input->post());
		$res     		= array();
		$res['tag_names'] 	= $this->request_model->get_tags_by_id($data['lead_id'], $data['file_id']);
		$res['lead_id'] 	= $data['lead_id'];
		$res['file_id'] 	= $data['file_id'];
		echo json_encode($res);
		exit;
	}

	public function save_tags() {
		$data    		= real_escape_array($this->input->post());
		// echo '<pre>'; print_r($data); die;
		$condn     			= array();
		$condn['lead_id'] 	= $data['lead_id'];
		$condn['file_id'] 	= $data['file_id'];
		
		$tag_names = @implode(",",$data['tags']);
		
		$updt = array('tag_names'=>$tag_names);
		
		$res   = $this->request_model->update_row('lead_files', $updt, $condn);
		$res = array('status'=>true);
		echo json_encode($res);
		exit;
	}
	
}

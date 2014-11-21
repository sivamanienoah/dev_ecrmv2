<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request extends crm_controller {

	public $cfg;
	public $userdata;
	
	function __construct()
	{
		parent::__construct();
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('request_model');
		$this->load->model('email_template_model');
		$this->load->library('upload');
		$this->load->helper('lead_helper');
	}
    
    function index()
    {

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
	
	/**
	 * Uploads a file posted to a specified job
	 * works with the Ajax file uploader
	 */
	public function file_upload($lead_id, $filefolder_id)
	{
		$f_name = preg_replace('/[^a-z0-9\.]+/i', '-', $_FILES['ajax_file_uploader']['name']);
		
		// echo "<pre>"; print_r($_FILES); exit;
		
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
					$insert_file						 = $this->request_model->insert_row('lead_files', $lead_files);
					
					$logs['jobid_fk']	   = $lead_id;
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $file_up['file_name'].' is added.';
					$logs['attached_docs'] = $file_up['file_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
					
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
	 * Deletes a file based on a Ajax post request
	 */
	public function file_delete() {
		$f_data = real_escape_array($this->input->post());
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'files/' . $f_data['job_id'] . '/' . $f_data['f_name'];
	
		if (isset($f_dir))
		{
			if (is_file($f_dir))
			{
				if (@unlink($f_dir))
				{
					$wh_condn = array('file_id' => $f_data['file_id'], 'lead_id' => $f_data['job_id']);
					$del_file = $this->request_model->delete_row('lead_files', $wh_condn);
					
					$logs['jobid_fk']	   = $f_data['job_id'];
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $f_data['f_name'].' is deleted.';
					$logs['attached_docs'] = $f_data['f_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
					
					$json['error'] 		   = FALSE;
					$json['fparent_id']    = $f_data['fparent_id'];
				}
				else
				{
					$json['error'] = TRUE;
				}
			}
			else
			{
				$json['error'] = TRUE;
			}
		}
		else
		{
			$json['error'] = TRUE;
		}
		echo json_encode($json);
	}
	
	/**
	 * Deletes a file based on a Ajax post request
	 */
	public function folder_delete() {
		$f_data = real_escape_array($this->input->post());
			
		// check first in file management table - whether it contains child folder
		// if not then check the folder whether it contains files.
		
		$fm_condn            = array('lead_id'=>$f_data['lead_id'],'parent'=>$f_data['folder_id']);
		$folder_check_status = $this->request_model->checkStatus('file_management', $fm_condn);
		
		if($folder_check_status) {
			$lf_condn          = array('lead_id'=>$f_data['lead_id'],'folder_id'=>$f_data['folder_id']);
			$file_check_status = $this->request_model->checkStatus('lead_files', $lf_condn);
			if($file_check_status) {
				//Deleting the folder
				$del_condn = array('lead_id'=>$f_data['lead_id'],'folder_id'=>$f_data['folder_id']);
				$del_file  = $this->request_model->delete_row('file_management', $del_condn);
				if($del_file) {
					$logs['jobid_fk']	   = $f_data['lead_id'];
					$logs['userid_fk']	   = $this->userdata['userid'];
					$logs['date_created']  = date('Y-m-d H:i:s');
					$logs['log_content']   = $f_data['folder_name'].' folder is deleted.';
					$logs['attached_docs'] = $f_data['folder_name'];
					$insert_logs 		   = $this->request_model->insert_row('logs', $logs);
				
					$json['error'] 	    = FALSE;
					$json['fparent_id'] = $f_data['fparent_id'];
					$json['msg']   		= "Folder Deleted.";
				} else {
					$json['error'] 	    = TRUE;
					$json['msg']   		= "Error in Deletion.";
				}
			} else {
				$json['error'] = TRUE;
				$json['msg']   = "This Folder contains documents. This cannot be Deleted.";
			}
		} else {
			$json['error'] = TRUE;
			$json['msg']   = "This Folder contains child folder (or) documents. This cannot be Deleted.";
		}
		echo json_encode($json);
	}
	
	/**
	 * @method get_project_files()
	 * @param $job_id
	 */
	public function get_project_files($job_id, $fparent_id=0) {

		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($job_id, $this->userdata['userid']);
		}
	
		$file_upload_access = get_file_access($job_id, $this->userdata['userid']);
	
		$userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('file');

		//intial step - Showing 1st child folders and root files
		$get_parent_data = $this->request_model->getParentData($job_id, $fparent_id);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'files/' . $job_id . '/';
		
		$file_array = array();
		
		if(!empty($get_parent_data)) {
			foreach($get_parent_data as $res) {
				if($res['folder_id'] == $fparent_id) {
					$get_files = $this->request_model->getFiles($job_id, $res['folder_id']);
				} else {
					$file_array[] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
				}
			}
		}
		if(!empty($get_files)) {
			foreach($get_files as $files) {
				$file_array[] = $files['lead_files_name']."<=>".$files['lead_files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
			}
		}
		$jobs_files_html = '';
		
		if(!empty($file_array)) {
			$jobs_files_html .= '<table id="list_file_tbl" border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable"><thead><tr><th><input type="checkbox" id="file_chkall" value="checkall"></th><th>File Name</th><th>Created On</th><th>Type</th><th>Size</th><th>Created By</th><th>Action</th></tr></thead>';
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
						$f_move   = '<input type=hidden id="mf_'.$file_id.'" value="'.$fname.'"><input type=hidden id="mftype_'.$file_id.'" value=file><a title=move onclick="moveFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/document_move.png" alt=Move></a>';
						if($chge_access == 1 || $file_upload_access ==1) {
							$f_delete = '<input type=hidden id="filename_'.$file_id.'" value="'.$fname.'"><a title=Delete onclick="ajaxDeleteFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/trash.png" alt=delete></a>';
						} else {
							$f_delete = '-';
						}
						$jobs_files_html .= "<td class='td_filechk'><input type='hidden' value='file'><input type='checkbox' class='file_chk' file-type='file' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a target="_blank" href='.base_url().'crm_data/files/'.$job_id.'/'.$fname.'>'.$fname.'</a></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$file_ext.'</td>';
						$jobs_files_html .= '<td>'.$file_sz.'</td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						$jobs_files_html .= '<td>'.$f_delete.'</td>';
					} else {
						$f_move   = '<input type=hidden id="mf_'.$file_id.'" value="'.$fname.'"><input type=hidden id="mftype_'.$file_id.'" value=folder><a title=move onclick="moveFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/document_move.png" alt=Move></a>';
						$f_delete = '<input type=hidden id="filename_'.$file_id.'" value="'.$fname.'"><a title=Delete onclick="ajaxDeleteFolder('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/trash.png" alt=delete></a>';
						$jobs_files_html .= "<td><input type='hidden' value='folder'><input type='checkbox' file-type='folder' class='file_chk' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a class=edit onclick="getFolderdata('.$file_id.'); return false;" ><img src="assets/img/directory.png" alt=directory>&nbsp;'.$fname.'</a></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$ftype.'</td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						$jobs_files_html .= '<td>'.$f_delete.'</td>';
					}
					$jobs_files_html .= '</tr>';
			}
			$jobs_files_html .= '</tbody></table>';
		}
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
		$res['lead_id'] = $data['leadid'];
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
	 * @method addFolders()
	 * @mapping files to another folder
	 */
	public function addFolders() {
		$af_data = real_escape_array($this->input->post());
		
		$af_condn            = array('lead_id'=>$af_data['aflead_id'],'folder_name'=>$af_data['new_folder'],'parent'=>$af_data['add_destiny']);
		$folder_check_status = $this->request_model->createFolderStatus('file_management', $af_condn);
		if($folder_check_status==0){
			$add_data = array('lead_id'=>$af_data['aflead_id'],'folder_name'=>$af_data['new_folder'],'parent'=>$af_data['add_destiny'],'created_by'=>$this->userdata['userid'],'created_on'=>date('Y-m-d H:i:s'));
			$res_insert = $this->request_model->insert_row('file_management', $add_data);
			if(!$res_insert) {
				$err_msg = 'Folder cannot be added.';
			}
		} else {
			$res_insert = FALSE;
			$err_msg = "Folder Name already exists.";
		}
		
		if($res_insert){
			$log_contents  = array('jobid_fk'=>$af_data['aflead_id'],'userid_fk'=>$this->userdata['userid'],'date_created'=>date('Y-m-d H:i:s'),'log_content'=>$af_data['new_folder'].' folder is Added.','attached_docs'=>$af_data['new_folder']);
			$insert_logs   = $this->request_model->insert_row('logs', $log_contents);
			$htm['af_msg'] = '<span class="ajx_success_msg"><h5>'.$af_data['new_folder'].' has been Added</h5></span>';
		} else {
			$htm['af_msg'] = '<span class="ajx_failure_msg"><h5>'.$err_msg.'</h5></span>';
		}
		$htm['af_reload'] = $af_data['afparent_id'];
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
		
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($job_id, $this->userdata['userid']);
		}

		$userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('file');

		//intial step - Showing 1st child folders and root files
		$get_parent_data = $this->request_model->search_folder($job_id, $sf_data['search_input']);
		
		$fcpath = UPLOAD_PATH; 
		$f_dir = $fcpath . 'files/' . $job_id . '/';
		
		$file_array = array();
		
		if(!empty($get_parent_data)) {
			foreach($get_parent_data as $res) {
				$file_array[] = $res['folder_name']."<=>".$res['created_on']."<=>File folder<=>".$res['first_name']." ".$res['last_name'].'<=>'.$res['folder_id'];
			}
		}
		$get_files = $this->request_model->search_file($job_id, $sf_data['search_input']);
		if(!empty($get_files)) {
			foreach($get_files as $files) {
				$file_array[] = $files['lead_files_name']."<=>".$files['lead_files_created_on']."<=>File<=>".$files['first_name']." ".$files['last_name']."<=>".$files['file_id'];
			}
		}
		
		$jobs_files_html = '';
		
		if(!empty($file_array)) {
			$jobs_files_html .= '<table id="list_file_tbl" border="0" cellpadding="0" cellspacing="0" style="width:100%" class="data-tbl dashboard-heads dataTable"><thead><tr><th><input type="checkbox" id="file_chkall" value="checkall"></th><th>File Name</th><th>Created On</th><th>Type</th><th>Size</th><th>Created By</th><th>Action</th></tr></thead>';
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
						$f_move   = '<input type=hidden id="mf_'.$file_id.'" value="'.$fname.'"><input type=hidden id="mftype_'.$file_id.'" value=file><a title=move onclick="moveFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/document_move.png" alt=Move></a>';
						if($chge_access == 1) {
							$f_delete = '<input type=hidden id="filename_'.$file_id.'" value="'.$fname.'"><a title=Delete onclick="ajaxDeleteFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/trash.png" alt=delete></a>';
						} else {
							$f_delete = '<img src="assets/img/trash.png" alt=delete>';
						}
						$jobs_files_html .= "<td class='td_filechk'><input type='hidden' value='file'><input type='checkbox' class='file_chk' file-type='file' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a target="_blank" href='.base_url().'crm_data/files/'.$job_id.'/'.$fname.'>'.$fname.'</a></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$file_ext.'</td>';
						$jobs_files_html .= '<td>'.$file_sz.'</td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						$jobs_files_html .= '<td>'.$f_delete.'</td>';
					} else {
						$f_move   = '<input type=hidden id="mf_'.$file_id.'" value="'.$fname.'"><input type=hidden id="mftype_'.$file_id.'" value=folder><a title=move onclick="moveFile('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/document_move.png" alt=Move></a>';
						$f_delete = '<input type=hidden id="filename_'.$file_id.'" value="'.$fname.'"><a title=Delete onclick="ajaxDeleteFolder('.$job_id.','.$file_id.','.$fparent_id.'); return false;" ><img src="assets/img/trash.png" alt=delete></a>';
						$jobs_files_html .= "<td><input type='hidden' value='folder'><input type='checkbox' file-type='folder' class='file_chk' value='".$file_id."'></td>";
						$jobs_files_html .= '<td><a class=edit onclick="getFolderdata('.$file_id.'); return false;" ><img src="assets/img/directory.png" alt=directory>&nbsp;'.$fname.'</a></td>';
						$jobs_files_html .= '<td>'.date('d-m-Y',strtotime($fcreatedon)).'</td>';
						$jobs_files_html .= '<td>'.$ftype.'</td>';
						$jobs_files_html .= '<td></td>';
						$jobs_files_html .= '<td>'.$fcreatedby.'</td>';
						$jobs_files_html .= '<td>'.$f_delete.'</td>';
					}
					$jobs_files_html .= '</tr>';
			}
			$jobs_files_html .= '</tbody></table>';
		}
		echo $jobs_files_html;
	}

	/**
	 * @method get_file_tree_struct()
	 * 
	 */
	public function get_moveall_file_tree_struct() {
		$data    = real_escape_array($this->input->post());

		$mvfolder = array();
		
		if(!empty($data['mv_folder'])) {
			$mv_folder = rtrim($data['mv_folder'], ",");
			$mvfolder = explode(',', $mv_folder);
		}

		$result  = $this->request_model->get_tree_file_list_omit($data['curr_job_id'],$parentId=0,$counter=0,$mvfolder);
		
		$res     = '';
		$res['lead_id'] = $data['curr_job_id'];
		foreach($result as $fid=>$fname){
			if($fname == $data['curr_job_id']) {
				$fname = 'Project Folder';
			}
			$res['tree_struture'] .= "<option value='".$fid."'>".$fname."</option>";
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
		$data = $this->request_model->getBreadCrumbDet($leadid, $parent);
		foreach($data as $rec) {
			$res[$rec['folder_id']] = $rec['folder_name'];
			$parent_id = $rec['parent'];
			if( $parent_id !=0 ) {
				$this->getBreadCrumbs($leadid, $parent_id, $res);
			} else {
				$bc = '';
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
	}
	
	/*
	*
	*/
	public function get_files_tree_structure() {

		$data    = real_escape_array($this->input->post());
		$result  = $this->request_model->get_tree_file_list_number($data['leadid'],$parentId=0,$counter=0);
		$res     = array();
		$html 	 = '';
		// echo "<pre>"; print_r($result); exit;
		if(!empty($result)) {
			foreach($result as $folder_id=>$folder_name) {
			
				$exp         = explode("~", $folder_name);
				$counters    = $exp[0];
				$folder_name = $exp[1];
				
				if(is_numeric($folder_name)) {
					$folder_name = "&nbsp;root";
				}
				$html .= str_repeat("&nbsp;&nbsp;", $counters)."<img alt='directory' src='assets/img/directory.png'>".$folder_name ."<br />";
				$res = $this->request_model->getAssociateFiles($data['leadid'], $folder_id);
				
				if(!empty($res)) {
					foreach($res as $fname) {
						$html .= "&nbsp;".str_repeat("&nbsp;&nbsp;", $counters)."&nbsp;&nbsp;"."<input type='checkbox' class='attach_file' value='".$fname['file_id']."~".$fname['lead_files_name']."'>&nbsp;".$fname['lead_files_name'] ."<br />";
					}
				}
			}
		}
		echo $html;
		exit;
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
		
		if ($random != 'NO')
		{
			//$_POST['lead_id'] = 0;
		}
		
		$json['error'] = FALSE;
		if($update == 'NO') {
			$ins['jobid_fk'] = (int) $_POST['lead_id'];
		}
		$ins['task'] = $_POST['job_task'];
		$ins['userid_fk'] = $_POST['task_user'];
		// $ins['hours'] = (int) $_POST['task_hours'];
		// $ins['mins'] = (int) $_POST['task_mins'];
		$ins['remarks'] = $_POST['remarks'];
		if($update == 'NO') {
			$ins['approved'] = 1;
		}
		if($update == 'NO') {
			$ins['created_by'] = $this->userdata['userid'];
		}			
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
			$ins['start_date'] = date('Y-m-d H:i:s', $start_date);
			$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			
			
			$dtask_start_date=date('d-m-Y H:i:s', $start_date);
			$dtask_end_date=date('d-m-Y H:i:s' , $end_date);
			if (isset($_POST['task_end_hour']))
			{
				$ins['end_date'] = date('Y-m-d H:i:s', $end_date);
			}
			
			$ins['require_qc'] = (isset($_POST['require_qc']) && $_POST['require_qc'] == 'YES') ? '1' : '0';
			$ins['priority'] = (isset($_POST['priority']) && $_POST['priority'] == 'YES') ? '1' : '0';
			
			if ($update != 'NO' && $old_task = $this->get_task($update))
			{
			//$vard = $_POST['actualstart_date'];
			//echo $vard;
			//exit;
				//mychanges
				$updatedby = $this->user_model->updatedby($old_task->taskid);
				$ins['created_by'] = $updatedby[0]['created_by'];				
					
					$task_actualstart_date = explode('-', trim($_POST['actualstart_date']));
					if (count($task_actualstart_date) != 3 || ! $actualtask_date = mktime(0, 0, 0, $task_actualstart_date[1], $task_actualstart_date[0], $task_actualstart_date[2]))
						{
							$errors[] = 'Invalid Actual Start Date!';
						}
				if($_POST['actualstart_date'] =='0000-00-00'){
				$ins['actualstart_date']='0000-00-00 00:00:00';
				} else {
				$ins['actualstart_date'] = date('Y-m-d H:i:s', $actualtask_date);
				}
						//$ins['actualstart_date'] = date('Y-m-d H:i:s', $actualtask_date); //- sriram
	
				//ends
				//update
				//echo "<pre>"; print_r($ins); exit;
				$this->db->where('taskid', $update);
				$this->db->update($this->cfg['dbpref'].'tasks', $ins);
				
				//echo $this->db->last_query();exit;
				$ins['user_label'] = $_POST['user_label'];
				$ins['status'] = $ins['is_complete'] = 0;
				$ins['taskid'] = $update;
				$ins['userid'] = $ins['userid_fk'];
				$taskowner = $this->user_model->get_user($ins['userid']);
				$taskAssignedTo=$taskowner[0]['first_name'].'&nbsp;'.$taskowner[0]['last_name'];
				$taskAssignedToEmail=$taskowner[0]['email'];
				// $hm="&nbsp;".$ins['hours']."&nbsp;Hours&nbsp;".$ins['mins']."&nbsp;Mins";

				$json['html'] = $this->format_task($ins);
				
				# add a record
				$record['taskid_fk'] = $old_task->taskid;
				$record['event'] = 'Task Update';
				$record['date'] = date('Y-m-d H:i:s');
				$record['event_data'] = json_encode($old_task);
				$this->db->insert($this->cfg['dbpref'].'tasks_track', $record);
				$from_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				
				$admin_mail=implode(',',$arrSetEmails);
				$subject = 'New Task Update Notification';
				$from=$this->userdata['email'];
				
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				
				$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$update}");
				$task_owners = $task_owner_name->result_array();

				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				
				//email sent by email template
				$param = array();

				$param['email_data'] = array('job_task'=>$_POST['job_task'],'taskAssignedTo'=>$taskAssignedTo,'remarks'=>$task_owners[0]['remarks'],'start_date'=>date('d-m-Y', strtotime($dtask_start_date)),'end_date'=>date('d-m-Y', strtotime($dtask_end_date)),'first_name'=>$task_owners[0]['first_name'],'last_name'=>$task_owners[0]['last_name'],'status'=>$ins['status']);

				$param['to_mail'] = $taskAssignedToEmail;
				// $param['bcc_mail'] = $admin_mail;
				$param['cc_mail'] = $from;
				$param['from_email'] = $from;
				$param['from_email_name'] = $from_name;
				$param['template_name'] = "Task Update Notification";
				$param['subject'] = $subject;

				$this->email_template_model->sent_email($param);
			}
			else if ($update == 'NO')
			{
				if ( ! $this->db->insert($this->cfg['dbpref'].'tasks', $ins))
				{
					$json['error'] = TRUE;
					$json['errormsg'] = 'Task insert error';
				}
				else
				{
					
					$ins['user_label'] = $_POST['user_label'];
					$ins['status'] = $ins['is_complete'] = 0;
					$ins['taskid'] = $this->db->insert_id();
					$ins['userid'] = $ins['userid_fk'];
					$json['html'] = $this->format_task($ins);
					
					
					$creator = $this->user_model->get_user($this->userdata['userid']);
					$creator = $creator[0];
					$task_owner = $this->user_model->get_user($ins['userid_fk']);
					$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskSetToEmail=$task_owner[0]['email'];
					// $hm="&nbsp;".$ins['hours']."&nbsp;Hours&nbsp;".$ins['mins']."&nbsp;Mins";
					$job_url = ($ins['jobid_fk'] != 0) ? $this->config->item('base_url')."welcome/view_quote/{$ins['jobid_fk']}" : '';
					$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.taskid ={$ins['taskid']}");
					$task_owners = $task_owner_name->result_array();
		
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));

					$subject = 'New Task Notification';
					$from = $this->userdata['email'];;

					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$arrEmails = $this->config->item('crm');
					
					//email sent by using email template
					$param = array();

					$param['email_data'] = array('job_task'=>$_POST['job_task'], 'taskSetTo'=>$taskSetTo, 'remarks'=>$task_owners[0]['remarks'], 'start_date'=>date('d-m-Y', strtotime($dtask_start_date)), 'end_date'=>date('d-m-Y', strtotime($dtask_end_date)), 'first_name'=>$task_owners[0]['first_name'], 'last_name'=>$task_owners[0]['last_name'], 'status'=>$ins['status']);

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
		$uidd = $this->session->userdata['logged_in_user'];
		$uid = $uidd['userid'];
		$sql = "SELECT *, `".$this->cfg['dbpref']."tasks`.`start_date` AS `start_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label`
				FROM `".$this->cfg['dbpref']."tasks`, `".$this->cfg['dbpref']."users`
				WHERE `".$this->cfg['dbpref']."tasks`.`jobid_fk` = ?
				AND `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
				ORDER BY `".$this->cfg['dbpref']."tasks`.`is_complete`, `".$this->cfg['dbpref']."tasks`.`status`, `".$this->cfg['dbpref']."tasks`.`start_date`";				
		$q = $this->db->query($sql, array('jobid_fk' => $lead_id));
		$data = $q->result_array();	

		$html = '';
		foreach ($data as $row)
		{		
			$html .= $this->format_task($row);
		}
		
		if ($html == '')
		{
			$html = '<p class="task-notice">Sorry, there are no tasks set for this project!</p>';
		}
		
		echo $html;
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
		$sqltask="select userid_fk,created_by,status from ".$this->cfg['dbpref']."tasks where taskid='$taskid'";
		$rssqltask=mysql_query($sqltask);
		$rows=mysql_fetch_array($rssqltask);
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
					$upd['is_complete'] = 1;
					$upd['marked_complete'] = date('Y-m-d H:i:s');
					$this->db->where('taskid', $taskid);
					$this->db->update($task_table, $upd);
					$uid=$data->userid_fk;
				$task_name=$data->task;
				if($upd['is_complete']==1){
						$task_status="Completed";
				}					
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];

					$task_owner = $this->user_model->get_user($uid);
					$taskSetTo=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
					$taskStatusToEmail=$task_owner[0]['email'];
					$start_date=$data->start_date;
					$end_date=$data->end_date;
					$hours=$data->hours;
					$mins=$data->mins;
					// $hm=$hours.'&nbsp;Hours&nbsp;and&nbsp;'.$mins.'&nbsp;mins';
					$start_date=date('d-m-Y', strtotime($start_date));
					$end_date=date('d-m-Y', strtotime($end_date));
					$completed_date=date('l, jS F y h:iA', strtotime($upd['marked_complete']));
					$task_owner_name = $this->db->query("SELECT u.first_name,u.last_name,t.remarks
														FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
														WHERE u.userid = t.created_by
														AND t.taskid ={$taskid}");
					$task_owners = $task_owner_name->result_array();
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				
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
				$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
				$task_name=$data->task;
				$task_createdby=$data->created_by;
				$uid=$data->userid_fk;
				$task_owner = $this->user_model->get_user($task_createdby);
				$taskCreatedBy=$task_owner[0]['first_name'].'&nbsp;'.$task_owner[0]['last_name'];
				$taskCreatedByEmail=$task_owner[0]['email'];
				$task_allocated = $this->user_model->get_user($uid);
				$taskSetTo=$task_allocated[0]['first_name'].'&nbsp;'.$task_allocated[0]['last_name'];
				$taskSetEmail=$task_allocated[0]['email'];
				$dis['date_created'] = date('Y-m-d H:i:s');
				$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
				
				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$admin_mail=implode(',',$arrSetEmails);
				$subject='Task Delete Notification';
				
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
					$upd['marked_100pct'] = date('Y-m-d H:i:s');
					$json['marked_100pct'] = TRUE;
				}else { // Added For task set completion
					if($taskstat!='0'){
					$task_table = $this->cfg['dbpref'].'tasks';
					$ud = array();
					$ud['status'] = $taskstat;
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
				$task_owner_mail = $this->db->query("SELECT u.email,u.first_name,u.last_name,t.remarks
													FROM `".$this->cfg['dbpref']."tasks` AS t, `".$this->cfg['dbpref']."users` AS u
													WHERE u.userid = t.created_by
													AND t.created_by ={$task_createdby}
													AND t.taskid ={$taskid}");
				$task_owners = $task_owner_mail->result_array();

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
			$lead_details = $qa->row_array();
			//echo "<pre>"; print_r($lead_details);
			if ($this->userdata['role_id'] == 1 || $lead_details['belong_to'] == $this->userdata['userid'] || $lead_details['lead_assign'] == $this->userdata['userid']) {
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
}

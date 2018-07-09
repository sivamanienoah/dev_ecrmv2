<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Request_model
 *
 * @class    Request_model
 * @extends  crm_model (application/core)
 * @classes  Model
 * @author 	 eNoah
 */

class Request_model extends crm_model {
    
    function __construct() {
		$this->increment_num = 1;
        parent::__construct();
		$this->userdata = $this->session->userdata('logged_in_user');
    }
	
	/*
	 * Common function for inserting row
	 * @access public
	 * @param $tbl - Table name
	 * @param $ins - inserting data
	 */
	public function insert_row($tbl, $ins) {
		return $this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
    }
	
	/*
	 * Common function for inserting row return last_insertId
	 * @access public
	 * @param $tbl - Table name
	 * @param $ins - inserting data
	 */
	public function return_insert_id($tbl, $ins) 
	{
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
    }
	
	/*
	 * Common function for deleteing rows
	 * @access public
	 * @param $tbl - Table name
	 * @param $condn - where condition
	 */
	public function delete_row($tbl, $condn) {
		$this->db->where($condn);
		$this->db->delete($this->cfg['dbpref'] . $tbl);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	/*
	 * Common function for updating rows
	 * @access public
	 * @param $tbl   - Table name, $condn - where condition, $updt - values need to be update in db.
	 */
	public function update_row($tbl, $updt, $condn)
	{
		$this->db->update($this->cfg['dbpref'] . $tbl, $updt, $condn);
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }
	
	/*
	 * Common function for getting single row
	 * @access public
	 * @param $tbl   - Table name, $condn - where condition.
	 */
	public function get_record($tbl,$condn)
	{
		$this->db->select('*');
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
		return $sql->row_array();
	}
	
	/* Get selected fields from table*/
	public function get_selected_record($tbl,$condn)
	{
		$this->db->select('tag_names');
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
		return $sql->row();
	}
	
	/*
	 * @method getParentData()
	 * @access public
	 * @param $job_id - Table name
	 */
	public function getParentData($job_id, $fparent_id) {
		$this->db->select('f.folder_id, f.folder_name, f.parent, u.first_name, u.last_name, f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->where("f.lead_id", $job_id);
		$this->db->where("f.parent", $fparent_id);
		$this->db->or_where("f.folder_id", $fparent_id);
		$this->db->order_by("f.folder_id");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
    }
	
	/*
	 * @method getParentData()
	 * @access public
	 * @param $job_id - lead id, $f_id - file id
	 */
	public function getFiles($job_id, $f_id) {
		$this->db->select('f.file_id,f.lead_files_name,u.first_name,u.last_name,f.lead_files_created_on,f.folder_id,f.tag_names');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.lead_files_created_by', 'LEFT');
	    $this->db->where("f.lead_id", $job_id);
	    $this->db->where("f.folder_id", $f_id);
		$this->db->order_by("f.lead_files_created_on");
	    $sql = $this->db->get();
	    return $sql->result_array();
    }
	
	public function get_id_by_insert_row($tbl, $ins){
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins);
		return $this->db->insert_id();
	}
	
	/*
	 * @method checkStatus()
	 * @access public
	 * @param $tbl - Table name, $condn - where condition
	 */
	public function checkStatus($tbl, $condn) {
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
        return ($sql->num_rows() > 0) ? FALSE : TRUE;
    }
	
	//checking folder
	public function createFolderStatus($tbl, $condn) {
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
        // return ($sql->num_rows() > 0) ? TRUE : FALSE;
        return $sql->num_rows();
    }
	
	/*
	 * @method get_tree_file_list()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter
	 */
	public function get_tree_file_list($lead_id, $parentId=0 , $counter=0) {
		$arrayVal = array();
		
		$this->load->helper('lead_helper');
		
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($lead_id, $this->userdata['userid']);
		}
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		if(!empty($results)) {
			foreach($results as $result) {
				$is_root = check_is_root($lead_id, $result->folder_id);
				if($is_root == 'root'){
					$folder_rt = 2;
				} else {
					$folder_rt = get_folder_access($lead_id, $result->folder_id, $this->userdata['userid']);
				}
				if(($chge_access == 1) || ($folder_rt == 2)) {
					$arrayVal[$result->folder_id] = str_repeat('&nbsp;-&nbsp;', $counter)."{$result->folder_name}";
					$arrayVal = $arrayVal + $this->get_tree_file_list($lead_id, $result->folder_id, $counter+1);
				}
			}
		}
		
        return $arrayVal;
	}
	
	/*
	 * @method get_tree_file_list_number()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter
	 */
	public function get_tree_file_list_number($lead_id, $parentId=0 , $counter=0) {
		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		foreach($results as $result) {
			// $arrayVal[$result->folder_id] = str_repeat("&nbsp;-&nbsp;", $counter)."{$result->folder_name}";
			$arrayVal[$result->folder_id] = $counter."~"."{$result->folder_name}";
			$arrayVal = $arrayVal + $this->get_tree_file_list_number($lead_id, $result->folder_id, $counter+1);
		}
        return $arrayVal;
	}
	
	
	/*
	 * @method search_folder()
	 * @method search_file()
	 * @access public
	 * @param $lead_id - Lead Id,$search
	 */
	public function search_folder($lead_id, $parent_folder_id, $search_name){
		$this->db->select('f.folder_id,f.folder_name,f.parent,u.first_name,u.last_name,f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->where("f.lead_id", $lead_id);
		$this->db->like("f.parent", $parent_folder_id);
		if(!empty($search_name)) {
			$srch_arr = @explode(',',$search_name);
			if(!empty($srch_arr) && count($srch_arr)>0) {
				$find_wh = '(';
				$i = 0;
				foreach($srch_arr as $srch) {
					if($i==0) {
						$find_wh .= "(f.folder_name LIKE '%".$srch."%')";
					} else {
						$find_wh .= " OR (f.folder_name LIKE '%".$srch."%' )";
					}
					$i++;
				}
				$find_wh .= ')';
			}
		}
		// echo $find_wh; die;
		// $this->db->like("f.folder_name", $search_name);
		$this->db->where($find_wh, NULL, FALSE);
		$this->db->order_by("f.parent");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}

	/*
	 * @method getInfo()
	 * @method search_file()
	 * @access public
	 * @param $lead_id - Lead Id,$search
	 */
	public function getInfo($lead_id, $fold_id){
		$this->db->select('f.folder_id,f.folder_name,f.parent,u.first_name,u.last_name,f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');
		$this->db->join($this->cfg['dbpref'].'3 AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->where("f.lead_id", $lead_id);
		$this->db->like("f.folder_id", $fold_id);
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->row_array();
	}
	
	/*
	 * @method getFilesInfo()
	 * @access public
	 * @param $job_id - lead id, $f_id - file id
	 */
	public function getFilesInfo($job_id, $f_id) {
		$this->db->select('f.file_id,f.lead_files_name,u.first_name,u.last_name,f.lead_files_created_on,f.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.lead_files_created_by', 'LEFT');
	    $this->db->where("f.lead_id", $job_id);
	    $this->db->where("f.file_id", $f_id);
	    $sql = $this->db->get();
	    return $sql->row_array();
    }
		
	public function search_file($lead_id, $folder_id = null, $search_name){
		 
		$this->db->select('lf.file_id,lf.lead_files_name,lf.folder_id,us.first_name,us.last_name,lf.lead_files_created_on');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.lead_files_created_by', 'LEFT');
	    $this->db->where("lf.lead_id", $lead_id);
		if($folder_id)	$this->db->where("lf.folder_id", $folder_id);
		if($search_name) {
			$srch_val = @explode(',',$search_name);
			$find_wh = '(';
			if(count($srch_val)>0) {
				$i = 0;
				foreach($srch_val as $srch) {
					if($i==0) {
						$find_wh .= "FIND_IN_SET('".$srch."', lf.tag_names)";
					} else {
						$find_wh .= " OR FIND_IN_SET('".$srch."', lf.tag_names)";
					}
					$i++;
				}
			}
			$find_wh .= ')';
			$this->db->where($find_wh);
		}
		$this->db->order_by("lf.lead_files_created_on");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}
	
	public function get_tags_by_id($job_id, $f_id) {
		$result = array();
		$this->db->select('f.tag_names');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');
	    $this->db->where("f.lead_id", $job_id);
	    $this->db->where("f.file_id", $f_id);
	    $sql = $this->db->get();
	    $res = $sql->row_array();
		if(!empty($res) && count($res)>0) {
			$result = $res['tag_names'];
		}
		return $result;
    }
	
	/*
	*@method getBreadCrumbDet
	*@param lead_id, parentid
	*/
	public function getBreadCrumbDet($lead_id, $p_id) {

		$bread_crumbs = array();
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('folder_id', $p_id);
		return $this->db->get()->result_array();
		
	}
	
	public function get_subfolder_id($p_id){
		$this->db->select('folder_id');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent', $p_id);
		return $this->db->get()->result_array();
	}
	
	/*
	 * @method get_tree_file_list()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter, $omission
	 */
	public function get_tree_file_list_omit($lead_id, $parentId=0, $counter=0, $omit_ids) {

		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		$this->load->helper('lead_helper');
		if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($lead_id, $this->userdata['userid']);
		}
		
		foreach($results as $result) {
			
			if($chge_access != 1) {
				$is_root = check_is_root($lead_id, $result->folder_id);
				if($is_root == 'root'){
					$folder_rt = 2;
				} else {
					$folder_rt = get_folder_access($lead_id, $result->folder_id, $this->userdata['userid']);
				}
			} else {
				$folder_rt = 2;
			}
			
			
			if((!in_array($result->folder_id,$omit_ids)) && ($folder_rt == 2)) {
				$arrayVal[$result->folder_id] = str_repeat('&nbsp;-&nbsp;', $counter)."{$result->folder_name}";
				$arrayVal = $arrayVal + $this->get_tree_file_list_omit($lead_id, $result->folder_id, $counter+1, $omit_ids);
			}
		}
        return $arrayVal;
	}
	
	/*
	 *@method getParentFfolderId()
	 *@param lead id, parent = 0
	 */
	function getParentFfolderId($id,$parent)
	{
    	$this->db->select('f.folder_id');
		$this->db->from($this->cfg['dbpref'].'file_management as f');
    	$this->db->where('f.lead_id', $id);
    	$this->db->where('f.parent', $parent);
		$this->db->limit(1);
		$results = $this->db->get();
        return $results->row_array();
    }
	
	public function check_folder_permission($user_id,$folder_id,$parent_id){
		$qry_current_folder = $this->db->get_where($this->cfg['dbpref']."project_folder_access",array("user_id" => $user_id,"folder_id" => $folder_id));
		
		$qry_parent_folder = $this->db->get_where($this->cfg['dbpref']."project_folder_access",array("user_id" => $user_id,"folder_id" => $parent_id,"is_recursive" => 1));

		if($qry_current_folder->num_rows()>0 || $qry_parent_folder->num_rows()>0){
			$rs = $qry_current_folder->row();
			$rs1 = $qry_parent_folder->row();
			$ret = $rs1->download_access;
			$ret = ($ret==1)?$ret:$rs->download_access;
			return $ret;
		}
		return false;
	}
	
	/*
	 *@method getAssociateFiles()
	 *@param lead id
	 */
	public function getAssociateFiles($job_id, $folder_id) {
		$file = array();
		$this->db->select('lf.file_id,lf.lead_files_name,lf.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
	    $this->db->where("lf.lead_id", $job_id);
	    $this->db->where("lf.folder_id", $folder_id);
		$this->db->order_by("lf.file_id");
	    $sql = $this->db->get();
	    return $sql->result_array();
    }
	
	public function get_project_members($id)
	{
		$this->db->select('us.userid,us.first_name,us.last_name');
		$this->db->from($this->cfg['dbpref'] . 'contract_jobs AS cj');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = cj.userid_fk', 'LEFT');
		$this->db->where('cj.jobid_fk', $id);
		$this->db->where('us.inactive', 0);
		$contract_users = $this->db->get();
		return $contract_users->result_array();
	}
	
	/*
	 * @method get_project_folders()
	 * @access public
	 * @param $folder_ids - crm_file_management
	 */
	public function get_project_folders($folders) {
		$this->db->select('f.folder_id, f.folder_name, f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');	
		$this->db->where('`f`.`folder_id` IN ('.$folders.')', NULL, FALSE);				
		$this->db->order_by("f.parent");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }
	
	/*
	 * @method get_project_folders()
	 * @access public
	 * @param $folder_ids - crm_file_management
	 */
	public function get_project_files($files) {
		$this->db->select('f.file_id, f.lead_files_name, f.lead_id, f.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');		
		$this->db->where('`f`.`file_id` IN ('.$files.')', NULL, FALSE);
		$this->db->order_by("f.lead_files_name");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }
	
	public function get_stakeholder_access($id, $uid)
	{
		$this->db->select('lead_id, user_id');
		$this->db->where('lead_id', $id);
		$this->db->where('user_id', $uid);
		$sql = $this->db->get($this->cfg['dbpref'] . 'stake_holders');
		$res1 = $sql->result_array();
		if (empty($res1)) {
			$stake_access = 0;
		}
		else {
			$stake_access = 1;
		}
		return $stake_access;
	}
	
	/*
	 * @method check_lead_file_access()
	 * @access public	
	 * @Table Name - crm_lead_file_access	 
	 */
	public function check_lead_file_access($lead_id, $filed_column, $fild_id, $user_id) {
		$this->db->select('userid, lead_id, folder_id, file_id, lead_file_access_read, lead_file_access_write, lead_file_access_delete');
	    $this->db->from($this->cfg['dbpref'] . 'lead_file_access');	
		$this->db->where('lead_id', $lead_id);		
		$this->db->where($filed_column, $fild_id);
		$this->db->where('userid', $user_id);		
	    $sql = $this->db->get();	
	    return $sql->row();
		}
	
	/*
	 * @method insert_new_row()
	 * @access public	
	 * @Table Name -  $tbl
	 */	
	public function insert_new_row($tbl, $ins) {		
		$this->db->insert($this->cfg['dbpref'] . $tbl, $ins); //Manis
		return $this->db->insert_id();
    }
	
	/*
	 * @method get_lead_files_by_folder_id()
	 * @access public
	 * @param  folder_id - crm_lead_files
	 * @Table  Name -  crm_lead_files
	 */
	public function get_lead_files_by_folder_id($folder_id) {
		$this->db->select('f.file_id, f.lead_files_name, f.lead_id, f.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');		
		$this->db->where('f.folder_id', $folder_id);
		$this->db->order_by("f.file_id");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }
	
	/*
	 * @method get_tree_file_list()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter
	 */
	public function get_tree_parents_file_list($lead_id, $folder_id=0 , $counter=0) {
		$arrayVal = array();
		
		$folder_path = '';
		
		$this->db->select('lead_id, folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('folder_id = '. (int) $folder_id);
		$results = $this->db->get()->result();
		$path = '';
		foreach($results as $result) {
			
			$path .= $this->get_tree_parents_file_list($lead_id, $result->parent).'/'."{$result->folder_name}";
			 		
			
		}
			
        return $path;
	}
	
	/*
	 * @Author Mani.S
	 * @method get_tree_folder_lists()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter
	 */
	public function get_tree_folder_lists($lead_id, $parentId=0 , $counter=0) {
		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		foreach($results as $result) {
			$arrayVal[$result->folder_id] = "{$result->folder_name}";
			$arrayVal = $arrayVal + $this->get_tree_file_list($lead_id, $result->folder_id, $counter+1);
		}
        return $arrayVal;
	}
	/*
	 * @method get_lead_info()
	 * @access public	
	 * @Table Name - crm_lleads	 
	  * @parameter - lead_id	 
	 */
	   public function get_lead_info($lead_id) {
		$this->db->select('belong_to, lead_id, assigned_to, lead_assign');
	    $this->db->from($this->cfg['dbpref'] . 'leads');	
		$this->db->where('lead_id', $lead_id);			
	    $sql = $this->db->get();	
	    return $sql->row_array();
	 }
	 
	 
	 /*
	 * @Author - Mani.S
	 * @method get_all_lead_info()
	 * @access public	
	 * @Table Name - crm_lleads	 
	 * @parameter - 
	 */
	   public function get_all_lead_info() {
		$this->db->select('lead_id');
	    $this->db->from($this->cfg['dbpref'] . 'leads');			
	    $sql = $this->db->get();	
	    return $sql->result_array();
	 }
	 
	 
	 
	 /*
	 * @method check_lead_file_access_by_ids()
	 * @access public	
	 * @Table Name - crm_lead_file_access	 
	 * @Parameter - $lead_id, 	 $fild_id OR $folder_id
	 */
		public function check_lead_file_access_by_ids($lead_id, $filed_column, $fild_id, $userid=false) {
		$this->db->select('userid, lead_id, folder_id, file_id, lead_file_access_read, lead_file_access_write, lead_file_access_delete');
	    $this->db->from($this->cfg['dbpref'] . 'lead_file_access');	
		$this->db->where('lead_id', $lead_id);	
if($userid != false) {
$this->db->where('userid', $userid);	
}
		$this->db->where($filed_column, $fild_id);		
	    $sql = $this->db->get();	
	    return $sql->result_array();
		}
		
		 /*
	 * @method check_lead_file_access_by_id()
	 * @access public	
	 * @Table Name - crm_lead_file_access	 
	 * @Parameter - $lead_id, 	 $fild_id OR $folder_id, $userid
	 */
		public function check_lead_file_access_by_id($lead_id, $filed_column, $fild_id, $userid) {
		$this->db->select('userid, lead_id, folder_id, file_id, lead_file_access_read, lead_file_access_write, lead_file_access_delete');
	    $this->db->from($this->cfg['dbpref'] . 'lead_file_access');	
		$this->db->where('lead_id', $lead_id);	
		$this->db->where('userid', $userid);	
		$this->db->where($filed_column, $fild_id);		
	    $sql = $this->db->get();	
	    return $sql->row();
		}
		
	/*
	 * @method get_all_project_folders()
	 * @access public
	 * @param $lead_id - crm_file_management
	 */
	public function get_all_project_folders($lead_id) {
		$this->db->select('f.folder_id,f.lead_id, f.folder_name, f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');	
		$this->db->where('`f`.`lead_id`', $lead_id);				
		$this->db->order_by("f.parent");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }
	
	/*
	 * @method get_all_project_files()
	 * @access public
	 * @param $folder_ids - crm_file_management
	 */
	public function get_all_project_files($lead_id) {
		$this->db->select('f.file_id, f.lead_files_name, f.lead_id, f.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS f');		
		$this->db->where('`f`.`lead_id`', $lead_id);	
		$this->db->order_by("f.lead_files_name");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }

	public function get_task_stages() {
		$this->db->select();
	    $this->db->from($this->cfg['dbpref'] . 'task_stages');
		$this->db->where('status', 1);
		$this->db->order_by("task_sequence");
	    $sql = $this->db->get();		
	    return $sql->result_array();
    }
	
	
	/*
	 * @Author Mani.S
	 * @method getUsersById()
	 * @Use Get a purdicular user "userid", "first_name", "last_name"
	 * @access public
	 * @param $userid
	 * @table users
	 */
	public function getUsersById($userid){
		$this->db->select('u.userid,u.first_name,u.last_name');
	    $this->db->from($this->cfg['dbpref'] . 'users AS u');		
		$this->db->where("u.userid", $userid);		
	    $sql = $this->db->get();	
	    return $sql->row_array();
	}
	
	/*
	 * @Author Mani.S
	 * @method getUsersById()
	 * @Use Get a purdicular user "userid", "first_name", "last_name"
	 * @access public
	 * @param $userid
	 * @table users
	 */
	public function getUserInfomationById($userid){
		$this->db->select('*');
	    $this->db->from($this->cfg['dbpref'] . 'users AS u');		
		$this->db->where("u.userid", $userid);		
	    $sql = $this->db->get();	
	    return $sql->row_array();
	}
	
	/*
	 * @Author Mani.S
	 * @method get_project_leads()
	 * @Use Get a leads "belong_to", "assigned_to", "lead_assign" details.
	 * @access public
	 * @param $lead_id
	 * @table leads
	 */
	public function get_project_leads($lead_id) {
		$this->db->select('belong_to, assigned_to, lead_assign');
	    $this->db->from($this->cfg['dbpref'] . 'leads');	
		$this->db->where('lead_id', $lead_id);							
	    $sql = $this->db->get();	
		$arrLeadsManager = $sql->row_array();
		
		if(isset($arrLeadsManager) && !empty($arrLeadsManager)) {
		
		$arrLeadsManager = 	array_unique($arrLeadsManager);
			$arrLeaders = array();
			
			foreach($arrLeadsManager as $key=>$value) {
			
					$arrLeaders[] = $this->getUsersById($value);
			
			}
			return $arrLeaders;
			
		}else {
		
			return false;
		
		}	  
    }
	
	/*
	 * @Author Mani.S
	 * @method twoDimentionalArrayToSingle()
	 * @Used Converting two-dimentional array to single-dimentional user array	
	 * @access public
	 * @param Two Dimentional users array
	 */	
	public function usersArraysToSingleDimetioal($arrUsers)
	{
		$arrNewUserArray = array();	
		foreach($arrUsers as $listUsers) {
								
				array_push($arrNewUserArray, $listUsers['userid']);
			}
			
		return $arrNewUserArray;	
	}	
		/*
	 * @Author 
	 * @method taskCategoryQuery()
	 * @Used Generate tasks from category types	
	 * @access public
	 * @param category value
	 
	 
	 */	
	public function taskCategoryQuery($category_id,$lead_id,$category_name,$task_complete)
	{
		
		if($task_complete == 0)
		{
			$task_complete = "AND `".$this->cfg['dbpref']."tasks`.`is_complete` = '".$task_complete."'";
		}
		else
		{
			$task_complete ="";
		}
		
		$sql = "SELECT *, `".$this->cfg['dbpref']."tasks`.`start_date` AS `start_date`, CONCAT(`".$this->cfg['dbpref']."users`.`first_name`, ' ', `".$this->cfg['dbpref']."users`.`last_name`) AS `user_label` ,`".$this->cfg['dbpref']."tasks`.`created_by` as `taskcreated_by`
				FROM `".$this->cfg['dbpref']."tasks`, `".$this->cfg['dbpref']."users`
				WHERE `".$this->cfg['dbpref']."tasks`.`jobid_fk` =  '".$lead_id."'
				AND `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`
				AND `".$this->cfg['dbpref']."tasks`.`task_category` = '".$category_id."'".$task_complete.
				"ORDER BY `".$this->cfg['dbpref']."tasks`.`is_complete`, `".$this->cfg['dbpref']."tasks`.`status`, `".$this->cfg['dbpref']."tasks`.`start_date`";
		$q = $this->db->query($sql, array('jobid_fk' => $lead_id));
			//echo $this->db->last_query().'<br/><br/><br/><br/>';
	
		$data['records'] = $q->result_array();	
		$data['values'] = $category_name;
		$data['categoryid'] = $category_id;
		$data['rows'] = $q->num_rows();
		return $data;	
	}
	
	public function taskCountQuery($lead_id,$task_complete)
	{
		if($task_complete == 0)
		{
			$task_complete = "AND `".$this->cfg['dbpref']."tasks`.`is_complete` = '".$task_complete."'";
		}
		else
		{
			$task_complete ="";
		}
		
		$sql = "SELECT count(taskid) as count
				FROM `".$this->cfg['dbpref']."tasks`, `".$this->cfg['dbpref']."users`
				WHERE `".$this->cfg['dbpref']."tasks`.`jobid_fk` =  '".$lead_id."'
				AND `".$this->cfg['dbpref']."tasks`.`userid_fk` = `".$this->cfg['dbpref']."users`.`userid`".$task_complete."
				ORDER BY `".$this->cfg['dbpref']."tasks`.`is_complete`, `".$this->cfg['dbpref']."tasks`.`status`, `".$this->cfg['dbpref']."tasks`.`start_date`";
		$q = $this->db->query($sql, array('jobid_fk' => $lead_id));
	
		$data['records'] = $q->result_array();
		
		return $data['records'][0]['count'];	
	}
	
	public function get_task_info_by_id($task_id) {
		$this->db->select();
		$this->db->from($this->cfg['dbpref'] . 'tasks');	
		$this->db->where('taskid', $task_id);	
		$sql = $this->db->get();	
		return $sql->row_array();
	}
	
	
}
?>
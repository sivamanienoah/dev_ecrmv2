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
		$this->db->order_by("f.parent");
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
		$this->db->select('f.file_id,f.lead_files_name,u.first_name,u.last_name,f.lead_files_created_on,f.folder_id');
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
		
		$this->db->select('folder_id, folder_name, parent');
		$this->db->from($this->cfg['dbpref'] . 'file_management');
		$this->db->where('lead_id', $lead_id);
		$this->db->where('parent = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		foreach($results as $result) {
			$arrayVal[$result->folder_id] = str_repeat('&nbsp;-&nbsp;', $counter)."{$result->folder_name}";
			$arrayVal = $arrayVal + $this->get_tree_file_list($lead_id, $result->folder_id, $counter+1);
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
	public function search_folder($lead_id, $search_name){
		$this->db->select('f.folder_id,f.folder_name,f.parent,u.first_name,u.last_name,f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->where("f.lead_id", $lead_id);
		$this->db->like("f.folder_name", $search_name);
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
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
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
	
	public function search_file($lead_id, $search_name){
		$this->db->select('lf.file_id,lf.lead_files_name,lf.folder_id,us.first_name,us.last_name,lf.lead_files_created_on');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.lead_files_created_by', 'LEFT');
	    $this->db->where("lf.lead_id", $lead_id);
	    $this->db->like("lf.lead_files_name", $search_name);
		$this->db->order_by("lf.lead_files_created_on");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
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
		
		foreach($results as $result) {
			if(!in_array($result->folder_id,$omit_ids)) {
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
	
}
?>

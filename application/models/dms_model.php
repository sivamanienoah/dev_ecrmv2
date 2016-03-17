<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Forecast Model
 *
 * @class 		sales_forecast_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Dms_model extends crm_model {
    
	/*
	*@construct
	*@Sales Forecast Model
	*/
    public function __construct() {
       parent::__construct();
	   $this->load->helper('custom_helper');
	   $this->userdata = $this->session->userdata('logged_in_user');
    }
	
	####### get single row ########
	function get_record($select,$table,$where='')
	{
		$this->db->select($select);
		if($where){
			$this->db->where($where);
		}
		$query = $this->db->get($this->cfg['dbpref'].$table,1);
 		return $query->row_array();
	}
	
	/*
	 * @method getData()
	 * @access public
	 * @param $job_id - Table name
	 */
	public function getDmsData($fparent_id) {
		$this->db->select('f.folder_id, f.folder_name, f.parent_id, u.first_name, u.last_name, f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'dms_file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->where("f.parent_id", $fparent_id);
		$this->db->or_where("f.folder_id", $fparent_id);
		$this->db->order_by("f.folder_id");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
    }
	
	/*
	 * @method get_tree_file_list()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter
	 */
	public function get_tree_file_list($parentId=0 , $counter=0) 
	{
		$arrayVal = array();
		
		$this->load->helper('lead_helper');
		
		/* if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($lead_id, $this->userdata['userid']);
		} */
		
		$this->db->select('folder_id, folder_name, parent_id');
		$this->db->from($this->cfg['dbpref'] . 'dms_file_management');
		$this->db->where('parent_id = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		if(!empty($results)) {
			foreach($results as $result) {
				
				// if(($chge_access == 1) || ($folder_rt == 2)) {
					$arrayVal[$result->folder_id] = str_repeat('&nbsp;-&nbsp;', $counter)."{$result->folder_name}";
					$arrayVal = $arrayVal + $this->get_tree_file_list($result->folder_id, $counter+1);
				// }
			}
		}
        return $arrayVal;
	}
	
	/*
	 * @method get_tree_file_list()
	 * @access public
	 * @param $lead_id - Lead Id, $parentId, $counter, $omission
	 */
	public function get_tree_file_list_omit($parentId=0, $counter=0, $omit_ids) {

		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent_id');
		$this->db->from($this->cfg['dbpref'] . 'dms_file_management');
		$this->db->where('parent_id = '. (int) $parentId);
		$results = $this->db->get()->result();
		
		$this->load->helper('lead_helper');
		/* if ($this->userdata['role_id'] == 1 || $this->userdata['role_id'] == 2) {
			$chge_access = 1;
		} else {
			$chge_access = get_del_access($lead_id, $this->userdata['userid']);
		} */
		
		foreach($results as $result) {
			
			/* if($chge_access != 1) {
				$is_root = check_is_root($lead_id, $result->folder_id);
				if($is_root == 'root'){
					$folder_rt = 2;
				} else {
					$folder_rt = get_folder_access($lead_id, $result->folder_id, $this->userdata['userid']);
				}
			} else {
				$folder_rt = 2;
			} */
			
			
			// if((!in_array($result->folder_id,$omit_ids)) && ($folder_rt == 2)) {
			if((!in_array($result->folder_id,$omit_ids))) {
				$arrayVal[$result->folder_id] = str_repeat('&nbsp;-&nbsp;', $counter)."{$result->folder_name}";
				$arrayVal = $arrayVal + $this->get_tree_file_list_omit($result->folder_id, $counter+1, $omit_ids);
			}
		}
        return $arrayVal;
	}
	
	public function get_tree_file_list_except_root($parentId=0 , $counter=0) 
	{
		$arrayVal = array();
		
		$this->db->select('folder_id, folder_name, parent_id');
		$this->db->from($this->cfg['dbpref'] . 'dms_file_management');
		$this->db->where('parent_id = '. (int) $parentId);
		$results = $this->db->get()->result();
		// echo $this->db->last_query()."<br>";
		
		foreach($results as $result) {
			$isparent = $this->checkisparent($result->folder_id);
			if($isparent=='parent')
			$folder_options = '<i class="fa fa-folder-open"></i>'.$result->folder_name;
			else
			$folder_options = '<i class="fa fa-folder"></i>'.$result->folder_name;
			$arrayVal[$result->folder_id] = str_repeat('&nbsp; &nbsp; &nbsp; &nbsp;', $counter)."{$folder_options}";
			$arrayVal = $arrayVal + $this->get_tree_file_list_except_root($result->folder_id, $counter+1);
		}
        return $arrayVal;
	}
	
	public function checkisparent($folderid){
		$this->db->select('folder_id, folder_name, parent_id');
		$this->db->from($this->cfg['dbpref'] . 'dms_file_management');
		$this->db->where('parent_id = '. (int) $folderid);
		$results = $this->db->get()->num_rows();
		if($results>0){
			return 'parent';
		}else{
			return 'noparent';
		}
	}
	
	//checking folder
	public function createFolderStatus($tbl, $condn) 
	{
		$this->db->where($condn);
		$sql = $this->db->get($this->cfg['dbpref'] . $tbl);
        // return ($sql->num_rows() > 0) ? TRUE : FALSE;
        return $sql->num_rows();
    }
	
	/*
	*@method getBreadCrumbDet
	*@param lead_id, parentid
	*/
	public function getDmsBreadCrumbDet($p_id)
	{
		$this->db->select('folder_id, folder_name, parent_id');
		$this->db->from($this->cfg['dbpref'] . 'dms_file_management');
		$this->db->where('folder_id', $p_id);
		return $this->db->get()->result_array();		
	}
	
	/*
	 * @method getFiles()
	 * @access public
	 * @param $job_id - lead id, $f_id - file id
	 */
	public function getFiles($f_id) {
		$this->db->select('f.file_id,f.files_name,u.first_name,u.last_name,f.files_created_on,f.folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'dms_files AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.files_created_by', 'LEFT');
	    $this->db->where("f.folder_id", $f_id);
		$this->db->order_by("f.files_created_on");
	    $sql = $this->db->get();
	    return $sql->result_array();
    }
	
	/*
	*@Get records for Search
	*@Sales Forecast Model
	*/
	public function get_records($tbl, $wh_condn='', $order='', $or_where='') {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		if(!empty($or_where)) {
			$this->db->where($or_where);
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }
	
	public function get_all_users()
	{
    	$this->db->select('userid,first_name,last_name,username,level,role_id,inactive');
		$this->db->where('username != ',"admin.enoah");
		$this->db->where('inactive',0);
    	$this->db->order_by('first_name',"asc");
		$q = $this->db->get($this->cfg['dbpref'] . 'users');
		return $q->result_array();
    }
	
	/*
	 *@method getParentFfolderId()
	 *@param parent = 0
	 */
	function getParentFfolderId($parent)
	{
    	$this->db->select('f.folder_id');
		$this->db->from($this->cfg['dbpref'].'dms_file_management as f');
    	$this->db->where('f.parent_id', $parent);
		$this->db->limit(1);
		$results = $this->db->get();
        return $results->row_array();
    }

	/*
	*@Get row record for dynamic table
	*@Method  get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }

	/*
	*@Get row count for dynamic table
	*@Method  get_num_row
	*/
    public function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }

	/*
	*@Update Row for dynamic table
	*@Method  update_row
	*/
    public function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }
	
	/*
	*@Update Row for dynamic table
	*@Method  update_row_return_affected_rows
	*/
    public function update_row_return_affected_rows($table, $cond, $data) {
    	$sql =  '
				UPDATE `'.$this->cfg['dbpref'].'sales_forecast_milestone` SET 
				milestone_name = "'.$data['milestone_name'].'",
				milestone_value = '.$data['milestone_value'].',
				for_month_year = "'.date("Y-m-d", strtotime($data['for_month_year'])).'",
				modified_by = '.$this->userdata['userid'].'
				WHERE milestone_id = '.$cond.'
				';
		mysql_query($sql);
		return mysql_affected_rows();
    }

	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	return $this->db->insert($this->cfg['dbpref'].$table, $param);
    }
	
	/*
	*@Insert Row for dynamic table
	*@Method  insert_row_return_id
	*/
	function insert_row_return_id($table, $param) {
	
	    if ( $this->db->insert($this->cfg['dbpref'].$table, $param) ) {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            return false;
        }
    }

	/*
	*@Delete Row for dynamic table
	*@Method  delete_row
	*/
	public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
	
	/*
	*@Get currency rates
	*@Method  get_currency_rate
	*/
	public function get_currency_rate()
	{		
		$query = $this->db->get($this->cfg['dbpref'].'currency_rate');
		return $query->result();
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
	
	
	public function getDmsMembers($dms_type)
	{
		if($dms_type==1){
			$this->db->select('user_id');
			$this->db->from($this->cfg['dbpref'].'dms_users');
			$this->db->where('dms_type', 0);
			$res = $this->db->get()->result_array();
			$admin_user = array();
			if(!empty($res) && (count($res)>0)){
				foreach($res as $rec)
				$admin_user[] = $rec['user_id'];
			}
		}
		$this->db->select('du.user_id, u.first_name, u.last_name');
		$this->db->from($this->cfg['dbpref'] . 'dms_users du');
		$this->db->where('du.dms_type', $dms_type);
		if(($dms_type==1) && (!empty($admin_user)) && (count($admin_user)>0)){
			$this->db->where_not_in('du.user_id', $admin_user);
		}
		$this->db->join($this->cfg['dbpref'] . 'users u', 'u.userid=du.user_id');
		$this->db->order_by('first_name','asc');
		return $this->db->get()->result_array();
	}
	
	public function checkDmsIsFolderAccessRecordExist($folder_id, $user_id)
	{
		$this->db->where(array('folder_id' => $folder_id,'user_id' => $user_id));
		$this->db->from($this->cfg['dbpref'].'dms_folder_access');
        $result = $this->db->get()->row_array();
		
		if(!empty($result))	{
			return $result;
		} else {
			return FALSE;
		}
	}
	
	/*
	 * @method search_folder()
	 * @access public
	 * @param $lead_id - Lead Id,$search
	 */
	public function search_folder($access_folders, $search_name){
		$this->db->select('f.folder_id,f.folder_name,f.parent_id,u.first_name,u.last_name,f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'dms_file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		if(!empty($access_folders))
		$this->db->where_in("f.folder_id", $access_folders);
		$this->db->like("f.folder_name", $search_name);
		$this->db->order_by("f.parent_id");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}
	
	/*
	 * @method search_file()
	 * @access public
	 * @param $lead_id - Lead Id,$search
	 */
	public function search_file($access_folders, $search_name)
	{
		$this->db->select('lf.file_id,lf.files_name,lf.folder_id,us.first_name,us.last_name,lf.files_created_on');
	    $this->db->from($this->cfg['dbpref'] . 'dms_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.files_created_by', 'LEFT');
		if(!empty($access_folders))	$this->db->where_in("lf.folder_id", $access_folders);
	    $this->db->like("lf.files_name", $search_name);
		$this->db->order_by("lf.files_created_on");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}

	/*
	 * @method get_folder_ids()
	 * @access public
	 * @param $search
	 */ 
	public function get_folder_ids($user_id)
	{
		$this->db->select('folder_id');
	    $this->db->from($this->cfg['dbpref'] . 'dms_folder_access');
		$this->db->where("user_id", $user_id);
	    $this->db->where("access_type != 0");
		$this->db->order_by("folder_id");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}
    
}

?>

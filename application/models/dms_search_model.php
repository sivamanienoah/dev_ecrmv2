<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dms_search_model extends crm_model {

    function __construct() {
        parent::__construct();
    }
	
	public function search_folder($lead_id = null, $parent_folder_id = null, $search_name){
		$this->db->select('cus.company,le.lead_title,f.folder_id,f.lead_id,f.folder_name,f.parent,u.first_name,u.last_name,f.created_on');
	    $this->db->from($this->cfg['dbpref'] . 'file_management AS f');
		$this->db->join($this->cfg['dbpref'].'users AS u', 'u.userid = f.created_by', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'leads AS le', 'le.lead_id = f.lead_id', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'customers AS cus', 'cus.custid = le.custid_fk', 'LEFT');
		if($lead_id) $this->db->where("f.lead_id", $lead_id);
		if($parent_folder_id) $this->db->like("f.parent", $parent_folder_id);
		$this->db->like("f.folder_name", $search_name);
		$this->db->order_by("f.parent");
	    $sql = $this->db->get();
		// echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}

	public function search_files($search_name,$user_id,$user_role){
		$this->db->select('f.folder_name,cus.company,le.lead_title,lf.file_id,lf.lead_id,lf.lead_files_name,lf.folder_id,us.first_name,us.last_name,lf.lead_files_created_on');
	    $this->db->from($this->cfg['dbpref'] . 'lead_files AS lf');
		$this->db->join($this->cfg['dbpref'].'users AS us', 'us.userid = lf.lead_files_created_by', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'leads AS le', 'le.lead_id = lf.lead_id', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'customers AS cus', 'cus.custid = le.custid_fk', 'LEFT');
		$this->db->join($this->cfg['dbpref'].'file_management as f', 'f.folder_id = lf.folder_id', 'LEFT');
		if($user_role !=1){
			$this->db->where("(le.lead_assign = $user_id or le.assigned_to = $user_id or le.belong_to = $user_id)");
		}
		$this->db->like("lf.lead_files_name", $search_name);
		$this->db->order_by("lf.lead_files_created_on");
	    $sql = $this->db->get();
		//echo $this->db->last_query(); exit;
	    return $sql->result_array();
	}
}

/* End of dms search model file */
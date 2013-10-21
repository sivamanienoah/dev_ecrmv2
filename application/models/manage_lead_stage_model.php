<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manage_lead_stage_model extends crm_model {
    
    function Manage_lead_stage_model() {
        parent::__construct();
    }
	
	function get_leadStage($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'lead_stage');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('lead_stage_name', $search); 
		}
		$query = $this->db->get();
		$leadstg =  $query->result_array();
		return $leadstg;
    }
    
	function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }
    
	function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }
    
	function get_last_row($table, $limit, $select, $order) {
    	$this->db->select($select);
    	$this->db->order_by($order);
    	if(!empty($limit))
    	$this->db->limit($limit);
    	$res = $this->db->get($this->cfg['dbpref'].$table);
        return $res->result_array();
    }
    
	function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }
    
	function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }
    
	function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
    
	/*function lead_drag_drop_update($leadst) {
		$when = '';
	 	foreach ($leadst as $k => $v) {
        	$when .= "WHEN ".$v." THEN ".$k." \n";
        }
		$this->db->set('sequence', 'CASE WHEN lead_stage_id '.$when. ' ELSE `sequence` END', FALSE);
		$this->db->update($this->cfg['dbpref'].'crms_lead_stage');
		echo $this->db->last_query(); exit;
    }*/
   
}

?>

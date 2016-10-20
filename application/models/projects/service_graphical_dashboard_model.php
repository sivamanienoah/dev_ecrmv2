<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service_graphical_dashboard_model extends crm_model {
    
    public function __construct()
    {
        parent::__construct();
    }
	
	public function get_practices()
	{
    	$this->db->select('id, practices');
		$this->db->where('status', 1);
    	$this->db->order_by('id');
		$query = $this->db->get($this->cfg['dbpref'] . 'practices');
		$res = $query->result_array();
		$practices = array();
		if(!empty($res)){
			foreach($res as $row){
				$practices[$row['id']] = $row['practices'];
			}
		}
		return $practices;
    }
	
	public function get_uc_details($uc_filter_by)
	{
    	if($uc_filter_by == 'hour') {
			$this->db->select('practice_name, ytd_billable');
			$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard');
		} else if ($uc_filter_by == 'cost') {
			$this->db->select('practice_name, ytd_billable_utilization_cost as ytd_billable');
			$this->db->from($this->cfg['dbpref']. 'services_graphical_dashboard');
		}
		$sql = $this->db->get();
		$uc_graph_res = $sql->result_array();
		
		$uc_graph_val = array();
		if(!empty($uc_graph_res)){
			foreach($uc_graph_res as $key=>$val) {
				if($val['practice_name'] == 'Infra Services'){
					continue;
				}
				$graph_id = strtolower($val['practice_name']);
				$graph_id = str_replace(' ', '_', $graph_id);
				$uc_graph_val[$graph_id] = $val;
			}
		}
		return $uc_graph_val;
    }

}

?>

<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class job_model extends Common_model {
    
	public $cfg;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_job($id)
	{
		$sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`, `{$this->cfg['dbpref']}customers`, `{$this->cfg['dbpref']}lead_stage`
                WHERE `custid` = `custid_fk` AND lead_stage_id = job_status AND `jobid` = ?
				LIMIT 1";
		
		$q = $this->db->query($sql, array($id));
		// echo $this->db->last_query(); exit;
		if ($q->num_rows() > 0)
		{
			$data = $q->result_array();
			return $data[0];
		}
		else
		{
			return FALSE;
		}
	}
    
}
<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class notifications_model extends crm_model {
    
    function __construct()
	{
        parent::__construct();
		$vid=$this->session->userdata['logged_in_user']['role_id'];
		$this->load->helper('lead_helper');
		$viewLeads = getAccess(51, $vid);
    }

	function get_all_crons($search, $uid)
	{
		$this->db->select('c.cron_id, c.cron_name');
		$this->db->from("{$this->cfg['dbpref']}" . 'crons c');
		$this->db->order_by('c.cron_id', 'asc');
		$sql = $this->db->get();

		$this->db->select('cn.cron_id, cn.onscreen_notify_status, cn.email_notify_status, cn.no_of_days');
		$this->db->from("{$this->cfg['dbpref']}" . 'crons_notificatons cn');
		$this->db->where('cn.userid', $uid);
		$this->db->order_by('cn.cron_id', 'asc');
		$sql1 = $this->db->get();
		// echo $this->db->last_query();
		$res = array();
		$res['crons_all'] =  $sql->result_array();
		$res['crons_stat'] =  $sql1->result_array();
		
		// echo "<pre>"; print_r($res);
		
		// foreach($res['crons_all'] as $resl)
		for($i=0; $i<count($res['crons_all']);$i++)
		{
			$fin_res[$i] = $res['crons_all'][$i];

			foreach($res['crons_stat'] as $resa)
			{
				if ($res['crons_all'][$i]['cron_id'] == $resa['cron_id'])
				{
					$fin_res[$i]['onscreen_notify_status'] = $resa['onscreen_notify_status'];
					$fin_res[$i]['email_notify_status'] = $resa['email_notify_status'];
					$fin_res[$i]['no_of_days'] = $resa['no_of_days'];
				}
			}
		}
		// echo "<pre>"; print_r($fin_res); exit;
		
		return $fin_res;
	
    }
	
	function get_crons($cid, $uid)
	{
		$query = $this->db->get_where("{$this->cfg['dbpref']}" . 'crons_notificatons', array('userid'=>$uid, 'cron_id'=>$cid));
		// echo $query->num_rows();
		if ($query->num_rows() == 1) {
			$this->db->select('c.cron_name, cn.cron_id, cn.onscreen_notify_status, cn.email_notify_status, cn.no_of_days');
			$this->db->from("{$this->cfg['dbpref']}" . 'crons_notificatons cn');
			$this->db->join("{$this->cfg['dbpref']}" . 'crons c', 'c.cron_id = cn.cron_id');
			$this->db->where('cn.userid', $uid);
			$this->db->where('cn.cron_id', $cid);
			$this->db->order_by('cn.cron_id', 'asc');
			$sql = $this->db->get();
			$res = $sql->result_array();
		} else {
			$this->db->select('c.cron_id, c.cron_name');
			$this->db->from("{$this->cfg['dbpref']}" . 'crons c');
			$this->db->where('c.cron_id',$cid);
			$this->db->order_by('c.cron_id', 'asc');
			$sql = $this->db->get();

			$this->db->select('cn.cron_id, cn.onscreen_notify_status, cn.email_notify_status, cn.no_of_days');
			$this->db->from("{$this->cfg['dbpref']}" . 'crons_notificatons cn');
			$this->db->where('cn.userid', $uid);
			$this->db->where('cn.cron_id', $cid);
			$this->db->order_by('cn.cron_id', 'asc');
			$sql1 = $this->db->get();
			
			$res = array();
			$res['crons_name'] =  $sql->result_array();
			$res['crons_days'] =  $sql1->result_array();
			$res = array_merge_recursive($res['crons_name'], $res['crons_days']);
		}
		// echo $this->db->last_query();
		return $res;
    }
	
	function updt_crons($updt)
	{
		// echo "<pre>"; print_r($updt); exit;
		$query = $this->db->get_where("{$this->cfg['dbpref']}" . 'crons_notificatons', array('userid'=>$updt['userid'], 'cron_id'=>$updt['cron_id']));
		if ($query->num_rows() == 0) {
			$res = $this->db->insert("{$this->cfg['dbpref']}" . 'crons_notificatons', $updt);
		} else {
			$res = $this->db->update("{$this->cfg['dbpref']}" . 'crons_notificatons', $updt, array('userid'=>$updt['userid'], 'cron_id'=>$updt['cron_id']));
		}
		// echo $this->db->last_query(); exit;
		// $res = $this->db->update("{$this->cfg['dbpref']}" . 'crons_notificatons', $updt, "cron_id = ".$id." ");
		return $res;
    }
   
}

?>

<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	var $cfg;
	var $userdata;
	
	public function __construct()
	{
		parent::Controller();
		$this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
	}
	
	public function index()
	{
		$data = array();
		$restrict = '';
        if ($this->userdata['level'] == 4)
        {
            $restrict .= " AND `belong_to` = '{$this->userdata['sales_code']}'";
        }
		$sql = "SELECT *
                FROM  `".$this->cfg['dbpref']."jobs` AS J, `".$this->cfg['dbpref']."customers` AS C
				LEFT JOIN ".$this->cfg['dbpref']."hosting as H ON C.custid=H.custid_fk
				WHERE C.`custid` = J.`custid_fk`
				AND J.`job_status` IN (4, 5, 15)
				{$restrict}
				GROUP BY `jobid`
                ORDER BY `job_status`, `belong_to`, `date_created`";
		$q = $this->db->query($sql);
		
		if ($q->num_rows() > 0)
		{				
			$result = $q->result_array();
			$i = 0;
			foreach ($this->cfg['job_status'] as $k => $v)
			{
				while (isset($result[$i]) && $k == $result[$i]['job_status'])
				{
					$data['results'][$k][] = $result[$i];
					$i++;
				}
			}
			$temp[]=0;
			foreach($result as $val) { $temp[]=$val['jobid'];}
			$temp=implode(',',$temp);
			$sql="SELECT * FROM `".$this->cfg['dbpref']."hosting_job` J WHERE jobid_fk IN ({$temp})";
			$rows = $this->db->query($sql);
			$data['hosting']=$rows->result_array();
			
		}
		$this->load->view('dashboard_view', $data);
	}
}
?>

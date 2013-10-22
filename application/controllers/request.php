<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request extends crm_controller {
	
	public $cfg;
	public $userdata;
	
	public function __construct()
	{
		parent::__construct();
		$this->login_model->check_login();

		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->helper('lead_stage_helper');
		$this->stg = getLeadStage();
		$this->stages = @implode('","', $this->stg);
	}
	
	function index()
	{	
		// echo $var = $_POST['quoteid']; exit;
		
		$data['results'] = array();
		if (isset($_POST['keyword']) && trim($_POST['keyword']) != '' && ($_POST['keyword'] != 'Lead No, Job Title, Name or Company'))
		{	
			$keyword = mysql_real_escape_string($_POST['keyword']);
			
			$restrict = '';

			# restrict contractors
			$contract_join = '';
			
			$sql = "SELECT *
					FROM ".$this->cfg['dbpref']."customers, ".$this->cfg['dbpref']."jobs a
					{$contract_join}
					WHERE `custid_fk` = `custid`
					AND job_status IN ('".$this->stages."')
					AND (
						`job_title` LIKE '%{$keyword}%'
						OR `invoice_no` LIKE '%{$keyword}%'
						OR `custid_fk`
						IN (
							SELECT `custid`
							FROM ".$this->cfg['dbpref']."customers
							WHERE CONCAT_WS(' ', `first_name`, `last_name`) LIKE '%{$keyword}%'
							OR `first_name` LIKE '%{$keyword}%'
							OR `last_name` LIKE '%{$keyword}%'
							OR `company` LIKE '%{$keyword}%'
						)
					)
					{$restrict}
					ORDER BY `job_status`, `job_title`";
					
			$q = $this->db->query($sql);
			// echo $this->db->last_query();
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
				
				if (count($result) == 1)
				{
					$this->session->set_flashdata('header_messages', array('Only one result found! You have been redirect to the job.'));
					
					redirect('welcome/view_quote/' . $result[0]['jobid'] . '/draft');
				}
				else 
				{	//echo "tljlj";
					$this->session->set_flashdata('header_messages', array('Results found! You have been redirect to the job.'));
					redirect('welcome/view_quote/' . $result[0]['jobid'] . '/draft');
				}
			  
		    }
			else {
				$this->session->set_flashdata('header_messages', array('No record found!'));
				redirect('welcome/view_quote/' . $_POST['quoteid'] . '/draft');
			}
		}
		$this->load->view('quotation_create', $data);
	}	
}
?>

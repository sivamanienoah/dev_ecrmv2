<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public $cfg;
	public $userdata;
	function __construct()
	{
		parent::__construct();
		
		$this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		$this->userdata = $this->session->userdata('logged_in_user');
		$this->load->model('welcome_model');
		$this->load->model('job_model');
		$this->load->model('regionsettings_model');
		$this->load->helper('text');
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
	}
	
    /*
	 * Redirect user to quotation list
	 */
	public function index()
    {
		redirect('welcome/quotation');
    }
	
	public function add_lead(){
	 
	    //Create Customer 
		
		if(sizeof($_POST)==0){
		    echo 0;
			return false;
		}
		
		if(!empty($_POST['contact_us'])){
		
			$ins_cus['first_name']     = $_POST['firstname']; 
			$ins_cus['last_name']      = $_POST['lastname']; 
			$ins_cus['company']        = $_POST['organization']; 
			$ins_cus['position_title'] = $_POST['title']; 
			$ins_cus['email_1']        = $_POST['email']; 
			$ins_cus['email_2']        = $_POST['businessemail']; 
			$ins_cus['phone_1']        = $_POST['phonenumber'];
			$ins_cus['add1_line1']     = $_POST['address'];
			$ins_cus['comments']       = $_POST['message'];
		
		}else{
		
		$ins_cus['first_name']    = $_POST['name']; 
		$ins_cus['email_1']       = $_POST['email']; 
		$ins_cus['company']       = $_POST['company']; 
		$ins_cus['comments']      = $_POST['content']; 
	 
	    }
	 
		$this->db->insert($this->cfg['dbpref'] . 'customers', $ins_cus);
		$insert_id = $this->db->insert_id();

	    //
		$ins['job_title']           = 'Ask the Expert';
		$ins['custid_fk']           = $insert_id;
		$ins['job_category']        = $_POST['job_category'];
		//$ins['lead_source']       = '';
		$ins['lead_assign']         = 59;
		$ins['expect_worth_id']     = 5;
		$ins['expect_worth_amount'] = '0.00';
		$ins['belong_to']           = 59; // lead owner
		// $ins['division']         = $_POST['job_division'];
		$ins['date_created']        = date('Y-m-d H:i:s');
		$ins['date_modified']       = date('Y-m-d H:i:s');
		$ins['job_status']          = 1;
		// $ins['lead_indicator']   = $_POST['lead_indicator'];
		$ins['created_by']          = 59;
		$ins['modified_by']         = 59;
		$ins['lead_status']         = 1;
		if ($this->db->insert($this->cfg['dbpref'] . 'jobs', $ins))
        {
			$insert_id = $this->db->insert_id();

			$invoice_no = (int) $insert_id;
			$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);

			$this->db->where('jobid', $insert_id);
			$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_no' => $invoice_no));

			$this->quote_add_item($insert_id, "\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:", 0, '', FALSE);
		}
		echo 1;
		exit;
	}
	
    /*
	 * Create a new quote
	 * Loading just the view
	 * Quotes are created with Ajax functions
	 * @access public
	 */
	public function new_quote($lead = FALSE, $customer = FALSE)
	{
		if (is_numeric($lead))
		{
			$lead_details = $this->welcome_model->get_lead($lead);			
			$data['existing_lead'] = $lead;
			$data['existing_lead_service'] = $lead_details['belong_to'];
		}
		
		if (is_numeric($customer))
		{
			$data['lead_customer'] = $customer;
		}
		
		/* additional item list */
		$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
		
		$this->db->order_by('cat_id');
		$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
		$data['categories'] = $q->result_array();
		
		$c = count($data['categories']);
		
		for ($i = 0; $i < $c; $i++)
		{
			$this->db->where('item_type', $data['categories'][$i]['cat_id']);
			$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
			$data['categories'][$i]['records'] = $q->result_array();
		}
		$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
		$data['package'] = $qa->result_array();
		
		$lead_sources = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}lead_source where status=1");
		$data['lead_source'] = $lead_sources->result_array();
		
		if($this->userdata['role_id'] != 1) {
		$lead_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users WHERE level = '{$this->userdata['level']}'");
		//$data['lead_assign'] = $lead_assigns->result_array();
		} else {
		$lead_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users");
		//$data['lead_assign'] = $lead_assigns->result_array();
		}
		
		$expect_worths = $this->db->query("SELECT expect_worth_id,expect_worth_name FROM {$this->cfg['dbpref']}expect_worth");
		$data['expect_worth'] = $expect_worths->result_array();

		$this->load->view('welcome_view', $data);
	}
	
	/*
	 * List quotations
	 * @access public
	 * @param string $type - specify the list you want to display
	 */
	public function quotation($type = 'draft', $return = FALSE, $tab='')
    { 
		//echo "$type"; exit;
		//echo $this->session->userdata['logged_in_user']['role_id']; exit;
		$this->load->helper('text');
		$data['lead_stage'] = $this->welcome_model->get_lead_stage();
		//echo "<pre>"; print_r($data['regions']); exit;
		/*
		if ($this->userdata['level'] == 5 && in_array($type, array('draft', 'list', 'quote', 'pending')))
		{
			$type = 'approved';
		}
		*/
		$data['quote_section'] = $type;
		//echo $type;
        switch ($type)
        {		
            case 'draft':
                $job_status = "`job_status` IN (1,2,3,4,5,6,7,8,9,10,11,12)";
                $page_label = 'Leads List' ;
                break;
            case 'list':
                $job_status = 1;
                $page_label = 'Quotation List - Estimates';
                break;
			case 'ongoing':
				$job_status = 15;
				$page_label = 'Ongoing Quotations - "Bill Later" Clients';
				break;
            case 'quote':
                $job_status = 2;
                $page_label = 'Quotation List';
                break;
            case 'pending':
                $job_status = 3;
                $page_label = 'Quotation List - Pending Approval';
                break;
            case 'approved':
			//echo "$job_status"; exit;
				$job_status = 4;
                $page_label = 'Invoice List - Pending Payment';
                break;
			case 'production':
                $job_status = 5;
                $page_label = 'Invoices - In Production';
                break;
			case 'settlement':
                $job_status = 6;
                $page_label = 'Invoices - Completed, Awaiting Settlement';
                break;
			case 'completed':
                $job_status = 7;
                $page_label = 'Invoices - Settled';
                break;
			case 'idle':
                $job_status = 21;
                $page_label = 'Idle Quotations';
                break;
			case 'declined':
                $job_status = 22;
                $page_label = 'Declined Quotations';
                break;
			case 'cancelled':
                $job_status = 25;
                $page_label = 'Cancelled Invoices';
                break;
			case 's_pending':
                $job_status = 30;
                $page_label = 'Pending Payment';
                break;
			case 's_settled':
                $job_status = 31;
                $page_label = 'Settled Invoices';
                break;
			case 's_cancelled':
                $job_status = 32;
                $page_label = 'Cancelled Invoices';
                break;
			case 's_myob':
				$job_status = "`invoice_downloaded` = 0 AND `job_status` IN (30,31)";
				$page_label = 'Invoices to be Downloaded';
				break;
			case 'myob':
				$job_status = "`invoice_downloaded` = 0 AND `job_status` IN (4,5,6,7)";
				$page_label = 'Invoices to be Downloaded';
				break;
            default:
                $job_status = "`job_status` IN (0,1,2,3,4,5,6,7,8,9,10,11,12)";
                $page_label = 'Lead List ';
        }
		
		$restrict = '';
		/*
        if ($this->userdata['level'] == 4)
        {
			$restrict .= " AND `belong_to` = '{$this->userdata['sales_code']}'";
        }
		*/
		/*
        if ($job_status == 0) {
            $restrict .= " AND `created_by` = '{$this->userdata['userid']}'";
        }
		*/
		
		# restrict contractors
		
		$cnt_join1 = '';$cnt_join='';
		/*
		if ($this->userdata['level'] == 6)
		{
			$cnt_join1 = " `crm_contract_jobs` AS CJ, ";
			$cnt_join=" AND CJ.`jobid_fk` = J.`jobid` AND CJ.`userid_fk` = '{$this->userdata['userid']}'";
		}
		*/
		if (is_numeric($job_status))
		{
			$job_status = "`job_status` = '{$job_status}'";
		}
		if(!empty($_POST['pack_name']))
			$job_status = "`job_status` = '{$_POST['pack_name']}'";			
			
		if($_POST['keyword'] != 'Lead No, Job Title, Name or Company') {
			if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 ) {
				$search.=" AND ( J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
			}
		}
		#$this->output->enable_profiler(TRUE);
		$usid = $this->session->userdata['logged_in_user']['userid'];
		if (($this->session->userdata['logged_in_user']['role_id'] != '1') && ($this->session->userdata['logged_in_user']['level'] != 1)) {
		//echo "tst"; exit;
			$sql = "SELECT *, LS.lead_stage_name, SUM(`crm_items`.`item_price`) AS `project_cost`,
				(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
				FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$cnt_join1} `{$this->cfg['dbpref']}customers` AS C
				
				LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk
				
                WHERE C.`custid` = J.`custid_fk`
				AND C.`add1_region` IN(".$this->session->userdata['region_id'].")";
				if($this->session->userdata['countryid'] != '') {
				$sql .= " AND C.`add1_country` IN(".$this->session->userdata['countryid'].")";
				}
				if($this->session->userdata['stateid'] != '') {
				$sql .= " AND C.`add1_state`  IN(".$this->session->userdata['stateid'].") ";
				}
				if($this->session->userdata['locationid'] != '') {
				$sql .= " AND C.`add1_location` IN(".$this->session->userdata['locationid'].") ";
				}
				//$sql .= "OR (J.belong_to = '".$curusid."' AND  J.job_status IN (1,2,3,4,5,6,7,8,9,10,11,12))";
				$sql .= " AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} 
                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`";

				$rows = $this->db->query($sql);
				$res1 = $rows->result_array();
				//echo "<pre>"; print_r($res1); exit; 
				
				$leadowner_query = "AND J.belong_to = '".$usid."'";
				//for lead owner query.
				$leadownerquery = "SELECT *, LS.lead_stage_name, SUM(`crm_items`.`item_price`) AS `project_cost`,
								(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
								FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$cnt_join1} `{$this->cfg['dbpref']}customers` AS C
								
								LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk
								WHERE C.`custid` = J.`custid_fk`";
								$leadownerquery .= " AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} {$leadowner_query}
								GROUP BY `jobid`
								ORDER BY `belong_to`, `date_created`";
				
				
				$leadowner_rows = $this->db->query($leadownerquery);
				$res2 = $leadowner_rows->result_array(); 
				//echo "<pre>"; print_r($res2); exit; //echo $leadownerquery; exit;
				$records = array_merge_recursive($res1, $res2);
				//echo "<pre>"; print_r($records);  echo "***";
				$record = array_map("unserialize", array_unique(array_map("serialize", $records)));
				$data['records'] = $record;
				//$rows = $this->db->query($sql);
				//$data['records'] = $rows->result_array();
		}
		else {
			$sql = "SELECT *, LS.lead_stage_name, SUM(`crm_items`.`item_price`) AS `project_cost`,
					(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
                FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J, `{$this->cfg['dbpref']}lead_stage` as LS, {$cnt_join1} `{$this->cfg['dbpref']}customers` AS C
				
				LEFT JOIN `{$this->cfg['dbpref']}hosting` as H ON C.custid=H.custid_fk
                WHERE C.`custid` = J.`custid_fk` ";

				$sql .= " AND LS.lead_stage_id = J.job_status AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$cnt_join} {$search} {$restrict} 
                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`";
				
				$rows = $this->db->query($sql);
				$data['records'] = $rows->result_array();
				//echo "<pre>"; print_r($data['records']); exit;
		}
		//echo "<pre>"; print_r($data['records']); exit;
		$temp[]=0;
		foreach($data['records'] as $val) { $temp[]=$val['jobid']; }
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `crm_hosting_job` J WHERE jobid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$data['hosting']=$rows->result_array();
		$data['customers'] = $this->welcome_model->get_customers();
		$data['page_heading'] = $page_label;
		$leadowner = $this->db->query("SELECT userid, first_name FROM crm_users order by first_name");
		$data['lead_owner'] = $leadowner->result_array();
		$data['regions'] = $this->regionsettings_model->region_list();
		//echo "<pre>"; print_r($data['lead_owner']); exit;
        if ($return === TRUE){
			return $data['records'];
		}
		
		else{
			$this->load->view('quotation_view', $data);
		}
				
	}
	
	
	/*For Project Module -- Start here*/
	
	public function projects($type = 'draft', $return = FALSE, $tab='')
	{	//echo $_POST['keyword'];  exit;
		$page_label = '';
		//echo $type; exit;
		//$this->load->plugin('phpmailer');
		$this->load->library('email');
		
		$this->load->helper('text');
		
		$data['quote_section'] = $type;
		
		switch ($type)
        {
            case 'draft':
                $job_status = 0;
                $page_label = 'Quotation List - New Leads';
                break;
            case 'production':
				$job_status = 13;
                $page_label = 'Project- In Progress';
                break;
			case 'completed':
                $job_status = 14;
                $page_label = 'Project- Completed';
                break;
			case 'p_cancelled':
                $job_status = 15;
                $page_label = 'Project- Cancelled';
                break;
			case 'p_onhold':
                $job_status = 16;
                $page_label = 'Project- Onhold';
                break;
            default:
                $job_status = 0;
                $page_label = 'Quotation List - New Leads';
        }
		//$job_status = 13; // - Projects stage
		if (is_numeric($job_status))
		{
			$job_status = "`job_status` = '{$job_status}'";
		}
		$search='';
		
		if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 && $_POST['keyword']!='Project No, Project Title, Name or Company') {
			$search.=" AND (J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
		}
				
		/*$sql = "SELECT *, SUM(`crm_items`.`item_price`) AS `project_cost`,
				(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
                FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J,`{$this->cfg['dbpref']}customers` AS C
				LEFT JOIN crm_hosting as H ON C.custid=H.custid_fk
				
                WHERE C.`custid` = J.`custid_fk`
				AND C.`add1_region` IN(".$this->session->userdata['region_id'].")
				AND C.`add1_country` IN(".$this->session->userdata['countryid'].")
				AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$search}
                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`";*/
		
		$varSessionId = $this->userdata['userid']; //Current Session Id.
		//echo $this->userdata['role_id'];
		//Fetching Project Team Members.
		$sqlcj = "SELECT jobid_fk as jobid FROM `crm_contract_jobs` WHERE `userid_fk` = '".$varSessionId."'";
		$rowscj = $this->db->query($sqlcj);
		$data['jobids'] = $rowscj->result_array();

		//Fetching Project Manager, Lead Assigned to & Lead owner jobids.
		$sqlJobs = "SELECT jobid FROM `crm_jobs` WHERE `assigned_to` = '".$varSessionId."' OR `lead_assign` = '".$varSessionId."' OR `belong_to` = '".$varSessionId."'";
		$rowsJobs = $this->db->query($sqlJobs);
		$data['jobids1'] = $rowsJobs->result_array();

		$data = array_merge_recursive($data['jobids'], $data['jobids1']);

		$res[] = 0;
		if (is_array($data) && count($data) > 0) { 
			foreach ($data as $data) {
				$res[] = $data['jobid'];
			}
		}
		$result = array_unique($res);

		$varRes = implode(",",$result);
		//echo $this->userdata['role_id']; 
		$rle = $this->userdata['role_id']; 
		//echo $rle; exit;
		if ($rle !=1 && $rle !=2) {
		    //echo "test"; exit;
			$pjts = " AND (`jobid` in (".$varRes."))";
		}

		/*$sql = "SELECT *, SUM(`crm_items`.`item_price`) AS `project_cost`, 
				(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
                FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}jobs` AS J,`{$this->cfg['dbpref']}customers` AS C
				LEFT JOIN crm_hosting as H ON C.custid=H.custid_fk
				
                WHERE C.`custid` = J.`custid_fk`
				AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$search}{$pjts}

                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`"; 
			*/
		$sql = "SELECT *, SUM(`crm_items`.`item_price`) AS `project_cost`, U.first_name as fnm, U.last_name as lnm, C.first_name as cfname, C.last_name as clname, 
		(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
		FROM `{$this->cfg['dbpref']}items`, `{$this->cfg['dbpref']}customers` AS C, `{$this->cfg['dbpref']}jobs` AS J
		Left Join `{$this->cfg['dbpref']}users` AS U ON J.`assigned_to` = U.userid
		WHERE C.`custid` = J.`custid_fk`
		AND `jobid` = `{$this->cfg['dbpref']}items`.`jobid_fk` AND {$job_status}{$pjts}{$custid1}{$search}

		GROUP BY `jobid`
		ORDER BY `belong_to`, `date_created`";			
		
		$rows = $this->db->query($sql);
		$data['records'] = $rows->result_array();
		//print_r($data); exit;
		
		//echo "<pre>"; print_r($data['records']); exit;
		

		foreach($data['records'] as $val) {
			//print_r($val);
		}
		//exit;
		/*$temp[]=0;
		foreach($data['records'] as $val) { $temp[]=$val['jobid'];}
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `crm_hosting_job` J WHERE jobid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$data['hosting']=$rows->result_array();
		*/
		
		$data['page_heading'] = $page_label;
        if ($return === TRUE){
			return $data['records'];
		}
		else{
			$this->load->view('projects_view', $data);
		}
	}
	
	
	public function view_project($id = 0, $quote_section = '')
	{	
        $this->load->helper('text');
		$this->load->helper('fix_text');
		
		$usernme = $this->session->userdata('logged_in_user');
		$uid = $usernme['userid'];
		
		if ($usernme['role_id'] == 1 || $usernme['role_id'] == 2) {
			$data['chge_access'] = 1;
		} else {
			$data['chge_access'] = $this->welcome_model->get_jobid($id, $uid);
		}
		//echo $data['chge_access']; 
		/*$sql = "SELECT *
                FROM `{$this->cfg['dbpref']}customers`, `{$this->cfg['dbpref']}jobs`
                WHERE `custid` = `custid_fk` AND `jobid` = '{$id}' LIMIT 1"; */
		
		$sql = "SELECT *
				FROM crm_customers AS cus
				left join crm_jobs as jb on jb.custid_fk = cus.custid
				left join crm_region as reg on reg.regionid = cus.add1_region
				left join crm_country as cnty on cnty.countryid = cus.add1_country
				left join crm_state as ste on ste.stateid = cus.add1_state
				left join crm_location as locn on locn.locationid = cus.add1_location
				left join crm_expect_worth as exw on exw.expect_worth_id = jb.expect_worth_id
				left join crm_lead_stage as ls on ls.lead_stage_id = jb.job_status
				where jb.jobid = '{$id}'
				LIMIT 1";		
        $q = $this->db->query($sql);
		//echo $this->db->last_query();
        if ($q->num_rows() > 0)
        {
            $result = $q->result_array();
            $data['quote_data'] = $result[0];
            $data['view_quotation'] = true;
			
			$this->db->where('jobid_fk', $result[0]['jobid']);
			$cq = $this->db->get('crm_contract_jobs');
			
			$temp_cont = $cq->result_array();
			
			$data['assigned_contractors'] = array();
			
			foreach ($temp_cont as $tc)
			{
				$data['assigned_contractors'][] = $tc['userid_fk'];
			}
            
            $data['log_html'] = '';
			
			/* sub menus are based on the URI segment - for invoices, we redirect to the correct URI */
			if (in_array($data['quote_data']['job_status'], array(4, 5, 6, 7)) && $this->uri->segment(1) != 'invoice')
			{
				redirect('invoice/view_quote/' . $data['quote_data']['jobid']);
				exit();
			}
			
			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid']))
			{
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$this->db->where('jobid', $data['quote_data']['jobid']);
				$this->db->update($this->cfg['dbpref'] . 'jobs', $log_view_status);
			}
            
            $this->db->where('jobid_fk', $data['quote_data']['jobid']);
            $this->db->order_by('date_created', 'desc');
            $logs = $this->db->get($this->cfg['dbpref'] . 'logs');
            
            if ($logs->num_rows() > 0)
            {
                $log_data = $logs->result_array();
                $this->load->helper('url');
                
                foreach ($log_data as $ld)
                {
                    
                    $this->db->where('userid', $ld['userid_fk']);
                    $user = $this->db->get($this->cfg['dbpref'] . 'users');
                    $user_data = $user->result_array();
					
					if (count($user_data) < 1)
					{
						echo '<!-- ', print_r($ld, TRUE), ' -->'; 
						continue;
					}
                    
                    $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
                    
					$fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));
					
					$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';
					
/*                    $table = <<<HDOC
<div class="log{$stick_class}">
    <p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</div>
HDOC;
*/
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log{$stick_class}">
<p class="data log{$stick_class}">
        <span class="log{$stick_class}">{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc log{$stick_class}">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
                    $data['log_html'] .= $table;
					unset($table, $user_data, $user, $log_content);
                }
            }
			
			$data['user_accounts'] = array();
			$users = $this->db->get($this->cfg['dbpref'] . 'users');
			if ($users->num_rows() > 0)
			{
				$data['user_accounts'] = $users->result_array();
			}
			
			$data['pm_accounts'] = array();
			//Here "WHERE" condition used for Fetching the Project Managers.
			$users = $this->db->get_where($this->cfg['dbpref'] . 'users',array('role_id'=>3));
			if ($users->num_rows() > 0)
			{
				$data['pm_accounts'] = $users->result_array();
			}
			
			if ($data['quote_data']['payment_terms'] == 1)
			{
				$this->db->where('jobid_fk', $data['quote_data']['jobid']);
				$this->db->order_by('expectid', 'asc');
				$payment_terms = $this->db->get($this->cfg['dbpref'] . 'expected_payments');
				$data['payment_data'] = array();
				if ($payment_terms->num_rows() > 0)
				{
					$data['payment_data'] = $payment_terms->result_array();
				}
			}
			$this->db->select('crm_deposits.*');
			$this->db->select('crm_expected_payments.project_milestone_name AS payment_term');
			$this->db->from($this->cfg['dbpref'] . 'deposits');
			$this->db->where('crm_deposits.jobid_fk', $data['quote_data']['jobid']);
			$this->db->join('crm_expected_payments', 'crm_deposits.map_term = crm_expected_payments.expectid', 'left');
			$this->db->order_by('depositid', 'asc');
			//$deposits = $this->db->get($this->cfg['dbpref'] . 'deposits');
			$deposits = $this->db->get();
			//echo $this->db->last_query();
			if ($deposits->num_rows() > 0)
			{
				$data['deposits_data'] = $deposits->result_array();
			}
			
			/**
			 * Get files associated with this job
			 */
			$fcpath = dirname(FCPATH) . '/';
			$f_dir = $fcpath . 'vps_data/' . $id . '/';
			$data['job_files_html'] = $this->welcome_model->get_job_files($f_dir, $fcpath, $data['quote_data']);
			
			
			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->welcome_model->get_job_urls($id);
			
			/**
			 * Get Dev QC q's
			 */
			$data['dev_qc_list'] = $this->welcome_model->get_qc_list(1);
			$data['is_qc_complete'] = $this->welcome_model->get_qc_complete_status($id, 1);
			$data['job_dev_qc_history'] = $this->welcome_model->get_qc_history($id, 1);
			$data['hosting']=$this->ajax_hosting_load($id);
			/**
			 * If we get a type,
			 * we can get the list of jobs too
			 */
			if ($quote_section != '')
			{
				$data['jobs_under_type'] = $this->quotation($quote_section, TRUE);
			}
			
			//For list the particular lead owner, project manager & lead assigned_to in the welcome_view_project page.
			$data['list_users'] = $this->welcome_model->get_list_users($id);
			
			//For list the particular project team member in the welcome_view_project page.
			$data['contract_users'] = $this->welcome_model->get_contract_users($id);	
			$data['get_lead_stage_projects'] = $this->welcome_model->get_lead_stage_projects();
            $this->load->view('welcome_view_project', $data);
			
        }
        else
        {
            echo "Project does not exist or if you are an account manager you may not be authorised to view this";
        }
        
    }
	
	/*****************************
	*Advanced Search For Projects*
	*****************************/
	function advance_filter_search_pjt($pjtstage='false', $pm_acc='false', $cust='false', $keyword='false')
	{ 
		$varSessionId = $this->userdata['userid']; //Current Session Id.
	       /*****************************************
			1.	$pjtstage is a job_status.	    
			2.	$pm_acc is a Project Manager ids.   
			3.	$cust is a Customers ids.(custid_fk)
			*****************************************/
		if ($keyword == 'false') {
			$keyword = 'null';
		} 
		$getProjects = $this->welcome_model->get_projects_results($pjtstage, $pm_acc, $cust, $keyword);	
		//echo "<pre>"; print_r($getProjects); exit;
		$data['pjts_data'] = $getProjects;
		
		$this->load->view('projects_view_inprogress', $data);
	}
	
	/*For Project Module -- End here*/
	
	/*
	 * Alias for quotation - above
	 * fix the navigation, so that the correct tab is highlighted
	 */
	public function invoice($type = 'draft')
	{
		$this->quotation($type);
	}
	/*
	 * Alias for quotation - above
	 * fix the navigation, so that the correct tab is highlighted
	 */
	public function subscription($type = 's_pending')
    {
		$this->quotation($type);
	}
    
    /*
     * Create the quote based on the submission from web_Dev form
     */
    public function ajax_webdev_quote()
    {
        if (isset($_POST['web_number_of_pages']) && (int) $_POST['web_number_of_pages'] > 0 && isset($_POST['jobid']) && $_POST['jobid'] > 0)
        {
            $np = (int) $_POST['web_number_of_pages'];
            $jobid = (int) $_POST['jobid'];
            
            if ($_POST['prep_gui'])
            {
                $prep_gui = $this->cfg['our_products'][4]['name'] . "
" . $this->cfg['our_products'][4]['desc'] . "";
                $this->quote_add_item($jobid, $prep_gui, $this->cfg['our_products'][4]['price'], '', FALSE);
            }
            
            $cms = $forms = 0;
            $applications = $app_cms = $app_np = $app_vs = $hosting = $domain = FALSE;
            
            $this->quote_add_item($jobid, "\nXHTML / CSS CODING\nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):", 0, '', FALSE);
            
            for ($i = 0; $i < $np; $i++)
            {
                $page_desc = $cms_page = $form_page = FALSE;
                if (trim($_POST['web_pages_' . $i]) != '')
                {
                    $page_desc = trim($_POST['web_pages_' . $i]);
                    if (isset($_POST['editablepage_' . $i]))
                    {
                        $page_desc .= '';
                        $cms++;
                    }
                    else
                    {
                        $page_desc .= '';
                    }
                    // if (isset($_POST['formpage_' . $i])) $forms++;
                    $this->quote_add_item($jobid, $page_desc, 350, '', FALSE);
                }
                
            }
            
            if ($cms > 0)
            {
                $cms_data = "
CMS PROGRAMMING
We will connect {$cms} page(s) to our WebPublisherCMS allowing the text and image content of those pages editable by the client.";
                $cms_price = $cms * $this->cfg['hourly_rate'] * 0.25;
                $this->quote_add_item($jobid, $cms_data, $cms_price, '', FALSE);
                $app_cms = "
" . $this->cfg['our_products'][0]['name'] . "
" . $this->cfg['our_products'][0]['desc'] . "";
            }
            
            if ($forms > 0)
            {
                $forms_data = "
FORMS
We will generate forms on {$forms} page(s)";
                $forms_price = $forms * $this->cfg['hourly_rate'];
                $this->quote_add_item($jobid, $forms_data, $forms_price, '', FALSE);
            }
            
			/*
            if ($app_cms || $app_vs || $app_np)
            {
                $this->quote_add_item($jobid, "\nWEB APPLICATIONS >", 0, '', FALSE);
            }
			*/
			
            if (isset($_POST['prep_vs']) && $_POST['prep_vs'] == 1)
            {
                $app_cms = "
" . $this->cfg['our_products'][2]['name'] . "
" . $this->cfg['our_products'][2]['desc'] . "";
                $this->quote_add_item($jobid, $app_cms, $this->cfg['our_products'][2]['price'], '', FALSE);
            }
            else if ($app_cms)
            {
                $this->quote_add_item($jobid, $app_cms, $this->cfg['our_products'][0]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_np']) && $_POST['prep_np'] == 1)
            {
                $app_np = "
" . $this->cfg['our_products'][1]['name'] . "
" . $this->cfg['our_products'][1]['desc'] . "";
                $this->quote_add_item($jobid, $app_np, $this->cfg['our_products'][1]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_hosting']) && $_POST['prep_hosting'] == 1)
            {
                $hosting = "
" . $this->cfg['our_products'][5]['name'] . "
" . $this->cfg['our_products'][5]['desc'] . "";
                $this->quote_add_item($jobid, $hosting, $this->cfg['our_products'][5]['price'], '', FALSE);
            }
            
            if (isset($_POST['prep_domain']) && $_POST['prep_domain'] == 1 && isset($_POST['prep_domain_name']))
            {
                $domain = "
DOMAIN NAME REGISTRATION 
On behalf of the client and from supplied business details including official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter.
" . $_POST['prep_domain_name'] . "";
                $this->quote_add_item($jobid, $domain, 140, '', FALSE);
            }
            
            echo "{error:false}";
            
        }
        else
        {
            echo "{error:true, errormsg:'Invalid number of pages or jobid'}";
        }
    }
    
    /*
	 * Duplicate an existing quote
	 * @access public
	 * @param int $jobid - Job Id
	 * @param int $quote - Existing quote to duplicate
	 */
	public function ajax_duplicate_quote($jobid = 0, $quote = 83)
	{
        $this->db->where('jobid_fk', $quote);
        $this->db->order_by('item_position', 'asc');
        $q = $this->db->get($this->cfg['dbpref'] . 'items');
        
        if ($q->num_rows() > 0)
        {
            $insert = $q->result_array();
            foreach ($insert as $ins)
            {
                $this->quote_add_item($jobid, $ins['item_desc'], $ins['item_price'], '', FALSE);
            }
            
            echo "{error:false}";
            
        }
        else
        {
            echo "{error:true, errormsg:'Error retrieving data from database!'}";
        }
    }
	
	public function lead_fileupload_details($jobid, $filename, $userid) {
	   
	   $querys = "INSERT INTO ".$this->cfg['dbpref']."_lead_files (lead_files_name,lead_files_created_by,lead_files_created_on,jobid) 
		VALUES('".$filename."','".$userid."','".date('Y-m-d H:i:s')."','".$jobid."')";		
		$q = $this->db->query($querys);
		
		 $logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs) 
		VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is added.' ,'".$filename."')"; 		
		$qlogs = $this->db->query($logs);
	
	}

    /*
	*Mail converstations send to customer and user
	*
	*/
	public function send_mail_query($jobid, $filename, $msg){
		
		$qry = "SELECT first_name, last_name, email FROM ".$this->cfg['dbpref']."users WHERE userid=".$this->session->userdata['logged_in_user']['userid'];
		$users = $this->db->query($qry);
		$user = $users->result_array();
		
		$qry1 = "SELECT email_1 FROM ".$this->cfg['dbpref']."customers WHERE custid = (SELECT custid_fk FROM ".$this->cfg['dbpref']."jobs WHERE jobid=".$jobid.")";
		$customers = $this->db->query($qry1);
		$customer = $customers->result_array();
		
		$query = "INSERT INTO ".$this->cfg['dbpref']."_lead_query (job_id,query_msg,query_file_name,query_sent_date,query_sent_to,query_from) 
		VALUES(".$jobid.",'".$msg."','".$filename."','".date('Y-m-d H:i:s')."','".$customer[0]['email_1']."','".$user[0]['email']."')";		
		$q = $this->db->query($query);
		//echo $this->db->last_query();
		/*if($q) {
			$url = base_url();
			$attachment_url = $url.'vps_data/query/'.$jobid.'/'.$filename;
			$to = $customer[0]['email_1'];
			$subject = 'Query Lead Converstion';
			$from = $user[0]['email'];
			$from_name = $user[0]['first_name'];
			
			$this->load->plugin('phpmailer');
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->set_newline("\r\n");
			$this->email->from($from, $from_name);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($msg);	
			//$this->email->AddAttachment($attachment_url);			
			$ok = $this->email->send();
			if($ok)
			echo "Successfully send the mail";	
			else 
			echo "Mail Sending Problem";
		} */					
		
	}
    /*
	 * Display the quote
	 * @access public
	 * @param int $id - Job Id
	 */
	public function view_quote($id = 0, $quote_section = '')
	{
        $this->load->helper('text');
		$this->load->helper('fix_text');
		
		$usid = $this->session->userdata('logged_in_user');
		//echo "<pre>"; print_r($usid); exit;
			$sql = "SELECT *,cus.first_name as cfn,cus.last_name as cln,reg.regionid,coun.countryid,st.stateid,loc.locationid
                FROM {$this->cfg['dbpref']}customers AS cus, {$this->cfg['dbpref']}jobs AS j,{$this->cfg['dbpref']}expect_worth AS ew, {$this->cfg['dbpref']}lead_source AS ls, {$this->cfg['dbpref']}users AS u, {$this->cfg['dbpref']}lead_stage AS lst,
				{$this->cfg['dbpref']}region as reg,
				{$this->cfg['dbpref']}country as coun, 
				{$this->cfg['dbpref']}state as st ,
				{$this->cfg['dbpref']}location as loc 
				
                WHERE cus.custid = j.custid_fk AND j.expect_worth_id = ew.expect_worth_id AND j.lead_source = ls.lead_source_id  AND j.lead_assign = u.userid AND jobid = '{$id}' AND j.job_status = lst.lead_stage_id  AND reg.regionid = cus.add1_region AND coun.countryid = cus.add1_country AND st.stateid = cus.add1_state AND loc.locationid  =  cus.add1_location LIMIT 1"; 
				//exit;			
        $q = $this->db->query($sql);
		//echo $this->db->last_query(); exit;
        if ($q->num_rows() > 0)
        {
            $result = $q->result_array();
            $data['quote_data'] = $result[0];
            $data['view_quotation'] = true;
			
			$this->db->where('jobid_fk', $result[0]['jobid']);
			$cq = $this->db->get('crm_contract_jobs');
			
			$temp_cont = $cq->result_array();
			
			$data['assigned_contractors'] = array();
			
			foreach ($temp_cont as $tc)
			{
				$data['assigned_contractors'][] = $tc['userid_fk'];
			}
            
            $data['log_html'] = '';
			

			
			if (!strstr($data['quote_data']['log_view_status'], $this->userdata['userid']))
			{
				$log_view_status['log_view_status'] = $data['quote_data']['log_view_status'] . ':' . $this->userdata['userid'];
				$this->db->where('jobid', $data['quote_data']['jobid']);
				$this->db->update($this->cfg['dbpref'] . 'jobs', $log_view_status);
			}
            
            $this->db->where('jobid_fk', $data['quote_data']['jobid']);
            $this->db->order_by('date_created', 'desc');
            $logs = $this->db->get($this->cfg['dbpref'] . 'logs');
            
            if ($logs->num_rows() > 0)
            {
                $log_data = $logs->result_array();
                $this->load->helper('url');
                
                foreach ($log_data as $ld)
                {
                    
                    $this->db->where('userid', $ld['userid_fk']);
                    $user = $this->db->get($this->cfg['dbpref'] . 'users');
                    $user_data = $user->result_array();
					
					if (count($user_data) < 1)
					{
						echo '<!-- ', print_r($ld, TRUE), ' -->'; 
						continue;
					}
                    
                    $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $ld['log_content'])))), 'url', TRUE));
                    
					$fancy_date = date('l, jS F y h:iA', strtotime($ld['date_created']));
					
					$stick_class = ($ld['stickie'] == 1) ? ' stickie' : '';					
					
                    /*$table = <<<HDOC
<div class="log{$stick_class}">
    <p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</div>
HDOC;*/
//Code Changes for the Pagination in Comments Section -- Starts here.
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log{$stick_class}">
<p class="data log{$stick_class}">
        <span class="log{$stick_class}">{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc log{$stick_class}">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
//Code Changes for the Pagination in Comments Section -- Ends here.
                    $data['log_html'] .= $table;
					unset($table, $user_data, $user, $log_content);
                }
            }
			
			$data['user_accounts'] = array();
			
			$users = $this->db->get($this->cfg['dbpref'] . 'users');
			if ($users->num_rows() > 0)
			{
				$data['user_accounts'] = $users->result_array();
			}
			
			if ($data['quote_data']['payment_terms'] == 1)
			{
				$this->db->where('jobid_fk', $data['quote_data']['jobid']);
				$this->db->order_by('expected_date', 'asc');
				$this->db->order_by('percentage', 'desc');
				$payment_terms = $this->db->get($this->cfg['dbpref'] . 'expected_payments');
				$data['payment_data'] = array();
				if ($payment_terms->num_rows() > 0)
				{
					$data['payment_data'] = $payment_terms->result_array();
				}
			}
			
			$this->db->where('jobid_fk', $data['quote_data']['jobid']);
			$deposits = $this->db->get($this->cfg['dbpref'] . 'deposits');
			if ($deposits->num_rows() > 0)
			{
				$data['deposits_data'] = $deposits->result_array();
			}
			
			/**
			 * Get files associated with this job
			 */
			$fcpath = dirname(FCPATH) . '/';
			$f_dir = $fcpath . 'vps_data/' . $id . '/';
			$data['job_files_html'] = $this->welcome_model->get_job_files($f_dir, $fcpath,$data['quote_data']);
			$data['query_files1_html'] = $this->welcome_model->get_query_files_list($id);
			
			/**
			 * Get URLs associated with this job
			 */
			$data['job_urls_html'] = $this->welcome_model->get_job_urls($id);
			
			/**
			 * Get Dev QC q's
			 */
			$data['dev_qc_list'] = $this->welcome_model->get_qc_list(1);
			$data['is_qc_complete'] = $this->welcome_model->get_qc_complete_status($id, 1);
			$data['job_dev_qc_history'] = $this->welcome_model->get_qc_history($id, 1);
			$data['hosting']=$this->ajax_hosting_load($id);
			
			$actual_worths = $this->db->query("SELECT SUM(`crm_items`.`item_price`) AS `project_cost`
								FROM `{$this->cfg['dbpref']}items`
								WHERE `jobid_fk` = '{$id}' GROUP BY jobid_fk");
			//echo $this->db->last_query(); exit;
			$data['actual_worth'] = $actual_worths->result_array();	

			$lead_owners = $this->db->query("SELECT ua.userid, ja.belong_to, ua.first_name AS uafn, ua.last_name AS ualn
													FROM crm_jobs AS ja
													JOIN crm_users AS ua ON ua.userid = ja.belong_to
													WHERE ja.jobid = '{$id}'");
			$data['lead_owner'] = $lead_owners->result_array(); 
			$query = $this->db->query("SELECT u.first_name FROM crm_jobs j JOIN crm_users as u ON j.belong_to = u.userid WHERE j.jobid = 66");
			$data['lead_owns'] = $lead_owners->result_array();
			$data['lead_stat_history'] = $this->welcome_model->get_lead_stat_history($id);
			/**
			 * If we get a type,
			 * we can get the list of jobs too
			 */
			 //echo $quote_section;
			if ($quote_section != '')
			{
				//$data['jobs_under_type'] = $this->quotation($quote_section, TRUE);
			}
			
		  $this->load->view('welcome_view_quote', $data);
        }
        else
        {
            echo "Quote does not exist or if you are an account manager you may not be authorised to view this";
        }
        
    }
	
	/*
     * View the quote by itself
     * Optionally generate PDF
     */
    public function view_plain_quote($id = 0, $pdf = FALSE, $stream_pdf = TRUE, $invoice = FALSE, $name_override = '', $template = '', $content_policy = TRUE)
    {
		$this->login_model->check_login();
        $this->load->helper('fix_text');
		
        $restrict = '';
        //if ($this->userdata['level'] == 4)
		//{
			//$restrict = " AND `belong_to` = '{$this->userdata['sales_code']}'";
        //}
        $sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`, `{$this->cfg['dbpref']}customers`
                WHERE `custid` = `custid_fk` AND `jobid` = '{$id}' {$restrict}";
        
        $q = $this->db->query($sql);
		
        if ($q->num_rows() > 0)
        {
            $result = $q->result_array();
            $data['quote_data'] = $result[0];
            $data['view_quotation'] = true;
            
            $items = $this->ajax_quote_items($result[0]['jobid'], 0, TRUE);
            $items = json_decode($items);
			
			$this->db->where('jobid_fk', $result[0]['jobid']);
			$this->db->select_sum('amount');
			$query = $this->db->get($this->cfg['dbpref'] . 'deposits');
			
			$data['deposits'] = 0;
			
			if ($query->num_rows() > 0)
			{
				$query = $query->result_array();
				$data['deposits'] = (float) $query[0]['amount'];
			}
            
			$tsearch[0] = '&#8482;';
			$treplace[0] = '<sup><small>TM</small></sup>';
			
			$tsearch[1] = '&trade;';
			$treplace[1] = '<sup><small>TM</small></sup>';
			
			$tsearch[2] = '&bull;';
			$treplace[2] = '&#149;';
			

            $htm = str_replace($tsearch, $treplace, cleanup_special_chars($items->html));
			
            $data['quote_items'] .= preg_replace(array('/<li id="qi\-[0-9]{1,}">/', '/<\/li>/'), array('<tr><td>', '</td></tr>'), $htm);
			//print_r($data['quote_items']);
            $data['sale_amount'] = $items->sale_amount;
            $data['gst_amount'] = $items->gst_amount;
            $data['total_inc_gst'] = $items->total_inc_gst;
			
			$numeric_total = str_replace(array('$', ','), '', $items->total_inc_gst);
			
			$data['balance'] = number_format((float) $numeric_total - $data['deposits'], 2, '.', ',');
			$data['deposits'] = number_format((float) $data['deposits'], 2, '.', ',');
            
			
			//$log_path = BASEPATH . 'logs/quote_request_log.txt';
			//$fp = fopen($log_path, 'a+');
			
			//fwrite($fp, "request call came in\n");
			if ($pdf == FALSE)
            {
				//fwrite($fp, "display only request\n");
                $this->load->view('pdf/new_quote_only_view', $data);
            }
            else
            {
				//fwrite($fp, "PDF request\n");
				
				if ($template == 'ruler')
				{
					$data['activate_ruler'] = TRUE;
				}
				
                $data['pdf_view'] = TRUE;
                $this->load->plugin('to_pdf');
				$this->load->helper('file');
				# for PDF we add the content disclaimer
				if ($content_policy === TRUE)
				{
					$data['quote_items'] .= '</table>
					
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;"><br />PROJECT COMPLETION POLICY<br />1.1 A project is deemed \'Complete\' and ready for \'Invoice\' or \'Final Payment\' when all services or items listed in the client approved lead have been carried out to a state where they are 100% operational and ready for use online. In approving this lead you agree and acknowledge our definition of \'Complete\' and agree to pay the remainder outstanding balance of your invoice prior to your \'Completed Project\' going live to web.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.2 Content is the responsibility of the client. eNoah  iSolution will not allow any project in its schedule to be held up due to the late provision of client content. Content includes: images, text, disclaimers, etc. If the client fails to provide this information to our studio before the project completion date they will be invoiced for the remainder 50% payment regardless as we cannot afford to have the provision of content holding back our production deadlines and delivery dates nor can we afford to have clients holding back final payments due to the absence of content on their part.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;" >\'Dumby\' or \'Mock\' content will be used in place of live content until such time the client provides the necessary content to go live. You will not be charged to have the live content uploaded in place when it is submitted to our studio for replacement of the \'Dumby\' or \'Mock\' content.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.3 In approving this lead, you agree to our Project Completion Policy and acknowledge that the provision of content is your sole responsibility and in no way can the absence or delayed provision of content form part of or all of a case to delay the project development from our own production perspective.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">You agree that withholding final payment for the services deemed \'Complete\' by eNoah iSolution Pvt Ltd you are in breach of our agreement to provide our services to your organisation as a result of your acceptance of our project lead and this will result in eNoah iSolution Pvt Ltd handing over a scenario of non or delayed payment without authority or approval by the Managing Director to our debt recovery agency (Baycorp Collection Services) without delay which can have a negative consequence to your credit rating.</p>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">1.4 By approving this lead; submitting an upfront payment deposit you have agreed, understood and will adhere to our Project Completion Policy.</p>
					';
					
				}

				$html = '<table width=100%; border=0 cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#fff8f2">
					<tr>
					<td width="15%"><div style="font-weight:bold;font-size:small;color:#ff4323; "><em>Company</em></div></td>
					<td width="35%" align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; color:#333333;">'.$data['quote_data']['company'].'</td>
					<td width="15%" align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:small; font-weight:bold;color:#ff4323; "><em>Lead #</em></div></td>
                    <td width="35%" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['invoice_no'].'</td>
					</tr>
					<tr>
					<td width="15%"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Contact</em></div></td>
					<td width="35%" align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['first_name'].' '.$data['quote_data']['last_name'].'</td>
					<td width="15%" align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Date</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.date('d/m/Y',strtotime($data['quote_data']['date_created'])).'</td>
					
					</tr>
					<tr>
					<td width="15%"align="left" valign="top"><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Email</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$data['quote_data']['email_1'].'</td>
					<td width="15%"align="left" valign="top" ><div style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; font-weight:bold;color:#ff4323; "><em>Service</em></div></td>
                    <td width="35%"align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; color:#333333;">'.$this->cfg['job_categories'][$data['quote_data']['job_category']].'</td>
					</tr>
					</table>
					<p style="font-family:Arial, Helvetica, sans-serif; font-size:14px; padding:2px 12px 2px 12px;line-height:16px;text-align:justify; color:#666;">
					<b>Project Name : '.$data['quote_data']['job_title'].'</b></p>';
				 
				# set the custom variables
				if (isset($_POST['balance_due']))
				{
					$data['balance_due'] = number_format((float) $_POST['balance_due'], 2, '.', ',');
				}
				
				if (isset($_POST['use_custom_date']))
				{
					$data['use_custom_date'] = $_POST['use_custom_date'];
				}
				
				if (isset($_POST['custom_description']))
				{
					$data['custom_description'] = $_POST['custom_description'];
				}
				if($data['quote_data']['created_by']==-1) {
					
					$html .= $this->load->view('pdf/subs_quote_only_view', $data, true);
				}
				else 

				switch($data['quote_data']['division']) {
					# page info here, db calls, etc.
					case 'SYNG':
						$html .= $this->load->view('pdf/syng_quote_only_view', $data, true);
						break;
					case 'SUBS':
						$html .= $this->load->view('pdf/subs_quote_only_view', $data, true);
						break;
					case 'RT':
						$html .= $this->load->view('pdf/real_quote_only_view', $data, true);
						break;
					default:
						$html .= $this->load->view('pdf/new_quote_only_view', $data, true);
				}

				
				$html .= '<table cellspacing="0" cellpadding="0" border="0" style="border:2px solid #000;"bgcolor="#ff4323" width="100%">
						<tbody><tr>
							<td width="30%">Sale Amount <span id="sale_amount">'.$items->sale_amount.'</span></td>
							<td align="right" width="30%">GST <span id="gst_amount">'.$items->gst_amount.'</span></td>
							<td width="10%">&nbsp;</td>
							<td align="right" width="30%">Total inc GST <span id="total_inc_gst">'.$items->total_inc_gst.'</span></td>
						</tr>
					
					
					</tbody></table>';

				$the_filename = ($name_override != '') ? $name_override : 'output-' . $data['quote_data']['invoice_no'];
				require('html2pdf/html2fpdf.php');
				$pdf=new HTML2FPDF();
				$pdf->SetFont('Arial','B',16);
				$pdf->AddPage();
				$strContent = $html;
				$pdf->WriteHTML($strContent);
				if($stream_pdf==FALSE){
					$full_pdf_path = dirname(FCPATH) . '/vps_data/'.$the_filename.".pdf";
				   $pdf->Output($full_pdf_path, 'F');
				}else{
				$pdf->Output($the_filename.".pdf");
				}
// print a block of text using Write()
				//fwrite($fp, "PDF variables - stream : {$stream_pdf}, invoice : {$invoice}, override: {$name_override}\n");
				//pdf_create($html, $the_filename, $stream_pdf, $invoice);
				//fwrite($fp, "PDF function called\n");
            }
			//fclose($fp);
        }
        else
        {
            echo "Quote does not exist or if you are an account manager you may not be authorised to view this";
        }
        
    }
	
	
	/**
	 * Adds a log to a job
	 * based on post data
	 *
	 */
	function add_log()
	{
        if (isset($_POST['jobid']) && isset($_POST['userid']) && isset($_POST['log_content']))
        {
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$this->db->where('jobid', $_POST['jobid']);
			$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
            
            if ($job_details->num_rows() > 0) 
            {
				$job = $job_details->result_array();
				//$this->db->insert_id();
                $this->db->select('first_name, last_name, email');
                $this->db->where('userid', $_POST['userid']);
                $user = $this->db->get($this->cfg['dbpref'] . 'users');
                $user_data = $user->result_array();
				
				
				$this->db->where('custid', $job[0]['custid_fk']);
				$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
				$client = $client_details->result_array();
				
                $this->load->helper('url');
				
				$emails = trim($_POST['emailto'], ':');
				
				//$send_to = array();
				$successful = $received_by = '';
				
				if ($emails != '' || isset($_POST['email_to_customer']))
				{
					$emails = explode(':', $emails);
					$mail_id = array();
					foreach ($emails as $mail)
					{
						$mail_id[] = str_replace('email-log-', '', $mail);
					}
					
					
					$data['user_accounts'] = array();
					$this->db->where_in('userid', $mail_id);
					$users = $this->db->get($this->cfg['dbpref'] . 'users');
					
					if ($users->num_rows() > 0)
					{
						$data['user_accounts'] = $users->result_array();
					}
					foreach ($data['user_accounts'] as $ua)
					{
						# default email
						$to_user_email = $ua['email'];
						
						if (strstr($ua['add_email'], '@') && ! (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name'])))
						{
							
							if ($ua['use_both_emails'] == 1)
							{
								$to_user_email = $ua['add_email'];
							}
							else if ($ua['use_both_emails'] == 2)
							{
								//$send_to[] = array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name']);
								$send_to[]= array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name'],'');
							}
						}
						
						//$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name']);
						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'],'');
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					$successful = 'This log has been emailed to:<br />';
					
					$log_subject = "eCRM Notification - {$job[0]['job_title']} [ref#{$job[0]['jobid']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
					
					/*$log_email_content = "--visiontechdigital.com\n\n" .
												$_POST['log_content'] .
												"\n\n--\n" . $this->userdata['signature'];
						*/							
				/*	$log_email_content = "--enoahisolution.com\n\n" .
												$_POST['log_content'] .
					
											"\n\n--\n" . $this->userdata['signature'];*/	
		
				
				$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Lead Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$client[0]['first_name'].'&nbsp;'.$client[0]['last_name'].'</p>
    <p style="padding: 4px;">'.
        $_POST['log_content'].'<br /><br />
		This log has been emailed to:<br />
		'.$received_by.'<br /><br />
		'.$this->userdata['signature'].'<br />
    </p>
</div>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';												
											
											
					$pdf_file_attach = array();

					$json['debug_info'] = '';
					
					if (isset($_POST['attach_pdf']))
					{
						
						$temp_file_prefix = ($job[0]['job_status'] < 4) ? 'quotation' : 'invoice';
						$temp_file_name = $temp_file_prefix . '-' . $job[0]['invoice_no'];
						$temp_file_path =  $temp_file_name;
					
						$full_file_path = dirname(FCPATH) . '/vps_data/' . $temp_file_path . '.pdf';
						$path="vps_data/".$temp_file_path .'.pdf';
						$full_url_path = base_url().$path;
						//$json['debug_info'] .= 'file attach requested - ';
						
						$content_policy = TRUE;
						if (isset($_POST['ignore_content_policy']))
						{
							$content_policy = FALSE;
						}
						
						$this->view_plain_quote($job[0]['jobid'], TRUE, FALSE, FALSE, $temp_file_path, '', $content_policy);
							


						if (file_exists($full_file_path))
						{
							//$pdf_file_attach= array($full_file_path, $temp_file_name . '.pdf');
							//$json['debug_info'] .= ' -- attachment set';
						}
					}
					if (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name']))
					{
						// we're emailing the client, so remove the VCS log  prefix
						$log_subject = preg_replace('/^eNoah Notification \- /', '', $log_subject);
						
						
						//$json['debug_info'] .= 'email to cust init > ';
						
						for ($cei = 1; $cei < 5; $cei ++)
						{
							if (isset($_POST['client_emails_' . $cei]))
							{
								//$json['debug_info'] .= 'loop through - ' . $cei;
								
								//$send_to[] = array($_POST['client_emails_' . $cei], '');
								$send_to[] = array($_POST['client_emails_' . $cei], '');
								$received_by .= $_POST['client_emails_' . $cei] . ', ';
							}
						}
						
						if (isset($_POST['additional_client_emails']) && trim($_POST['additional_client_emails']) != '')
						{
							//$json['debug_info'] .= ' > adiitional posts';
							$additional_client_emails = explode(',', trim($_POST['additional_client_emails'], ' ,'));
							if (is_array($additional_client_emails)) foreach ($additional_client_emails as $aces)
							{
								$aces = trim($aces);
								if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces))
								{
									//$json['debug_info'] .= ' > adiitional add - ' . $aces;
									
									//$send_to[] = array($aces, '');
									$send_to[] = array($aces, '');
									$received_by .= $aces . ', ';
								}
							}
						}
						
						// if the email goes to client - and the PDF attached, we need to CC accounts
						# removed as per request by George - 16-07-2009
						if (count($pdf_file_attach))
						{
							//$send_to[] = array('accounts@visiontechdigital.com', '');
							//$received_by .= 'accounts@visiontechdigital.com, ';
							//$send_to = array('jranand@enoahisolution.com', '');
							//$send_to[] = array('sarunkumar@enoahisolution.com', 'Arunkumar');
							//$received_by .= 'jranand@enoahisolution.com, ';
						}
						
					}
					else
					{
						//$log_email_content .= "\n\n\n{$job[0]['job_title']} - {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}" .
												//"\n".$this->config->item('base_url')."welcome/view_quote/{$_POST['jobid']}";
												$dis['date_created'] = date('Y-m-d H:i:s');
														$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
		
						               // $log_email_content = <<<HDOCHDOC;
		
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
	<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Lead Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$client[0]['first_name'].'&nbsp;'.$client[0]['last_name'].'</p>
    <p style="padding: 4px;">'.
        $_POST['log_content'].'<br /><br />
		This log has been emailed to:<br />
		'.$received_by.'<br /><br />
		'.$this->userdata['signature'].'<br />
    </p>
</div>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';						
										
					}

					$json['status_updated'] = false;
					if (isset($_POST['requesting_client_approval']) &&  $job[0]['job_status'] < 3)
					{
						$this->db->where('jobid', $_POST['jobid']);
						if ($this->db->update($this->cfg['dbpref'] . 'jobs', array('job_status' => '3')))
						{
							$json['status_updated'] = true;
						}
					}
				//	print_r($user_data);
					//$this->email->from('jranand@enoahisolution.com','Anand');
					//$this->email->to('jranand@enoahisolution.com','Anand');
					//$send_to = array('rkumaran@enoahisolution.com', 'Kumaran');
				//	$send_to = array('jranand@enoahisolution.com', 'JR Anand');
					//$send_to[] = array('sarunkumar@enoahisolution.com', 'Arunkumar');
					$this->email->from($user_data[0]['email'],$user_data[0]['first_name']);
					//$this->email->from('sarunkumar@enoahisolution.com','Arunkumar');
					foreach($send_to as $recps){
						$arrRecs[]=$recps[0];
					}
					$senders=implode(',',$arrRecs);
					$this->email->to($senders);
					$this->email->subject($log_subject);
					$this->email->message($log_email_content);
					if(!empty($full_url_path)){
					$this->email->attach($full_file_path);
					}
					if($this->email->send()){
						$successful .= trim($received_by, ', ');
					}
					else{
						echo 'failure';
					}
					
					/*if (send_email($send_to, $log_subject, $log_email_content, $user_data[0]['email'], $user_data[0]['first_name'] . ' ' . $user_data[0]['last_name'], '', '', $pdf_file_attach))
					{
						$successful .= trim($received_by, ', ');
					}
					*/
					
					
					
					
					if (isset($full_file_path) && is_file($full_file_path)) unlink ($full_file_path);
					
					if ($successful == 'This log has been emailed to:<br />')
					{
						$successful = '';
					}
					else
					{
						$successful = '<br /><br />' . $successful;
					}
				}
			
				$ins['jobid_fk'] = $_POST['jobid'];
				
				// use this to update the view status
				$ins['userid_fk'] = $upd['log_view_status'] = $_POST['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $_POST['log_content'] . $successful;
				
				$stick_class = '';
				if (isset($_POST['log_stickie']))
				{
					$ins['stickie'] = 1;
					$stick_class = ' stickie';
				}
				
				if (isset($_POST['time_spent']))
				{
					$ins['time_spent'] = (int) $_POST['time_spent'];
				}
				
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
				
				// update the jobs table
				$this->db->where('jobid', $ins['jobid_fk']);
				$this->db->update($this->cfg['dbpref'] . 'jobs', $upd);
                
                $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $_POST['log_content'])))), 'url', TRUE)) . $successful;
                
				$fancy_date = date('l, jS F y h:iA', strtotime($ins['date_created']));
				
                /*$table = <<<HDOC
<div class="log{$stick_class}" style="display:none;">
    <p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</div>
HDOC;
*/
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log">
<p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
				
                $json['error'] = FALSE;
                $json['html'] = $table;
				
                echo json_encode($json);
				
            }
            else
            {
                echo "{error:true, errormsg:'Post insert failed'}";
            }
        }
        else
        {
            echo "{error:true, errormsg:'Invalid data supplied'}";
        }
    }
	
	function pjt_add_log()
	{
        if (isset($_POST['jobid']) && isset($_POST['userid']) && isset($_POST['log_content']))
        {
			$this->load->helper('text');
			$this->load->helper('fix_text');
			
			$this->db->where('jobid', $_POST['jobid']);
			$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
            
            if ($job_details->num_rows() > 0) 
            {
				$job = $job_details->result_array();
				//$this->db->insert_id();
                $this->db->select('first_name, last_name, email');
                $this->db->where('userid', $_POST['userid']);
                $user = $this->db->get($this->cfg['dbpref'] . 'users');
                $user_data = $user->result_array();
				
				
				$this->db->where('custid', $job[0]['custid_fk']);
				$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
				$client = $client_details->result_array();
				
                $this->load->helper('url');
				
				$emails = trim($_POST['emailto'], ':');
				
				//$send_to = array();
				$successful = $received_by = '';
				
				if ($emails != '' || isset($_POST['email_to_customer']))
				{
					$emails = explode(':', $emails);
					$mail_id = array();
					foreach ($emails as $mail)
					{
						$mail_id[] = str_replace('email-log-', '', $mail);
					}
					
					
					$data['user_accounts'] = array();
					$this->db->where_in('userid', $mail_id);
					$users = $this->db->get($this->cfg['dbpref'] . 'users');
					
					if ($users->num_rows() > 0)
					{
						$data['user_accounts'] = $users->result_array();
					}
					foreach ($data['user_accounts'] as $ua)
					{
						# default email
						$to_user_email = $ua['email'];
						
						if (strstr($ua['add_email'], '@') && ! (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name'])))
						{
							
							if ($ua['use_both_emails'] == 1)
							{
								$to_user_email = $ua['add_email'];
							}
							else if ($ua['use_both_emails'] == 2)
							{
								//$send_to[] = array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name']);
								$send_to[]= array($ua['add_email'], $ua['first_name'] . ' ' . $ua['last_name'],'');
							}
						}
						
						//$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name']);
						$send_to[] = array($to_user_email, $ua['first_name'] . ' ' . $ua['last_name'],'');
						$received_by .= $ua['first_name'] . ' ' . $ua['last_name'] . ', ';
					}
					$successful = 'This log has been emailed to:<br />';
					
					$log_subject = "eCRM Notification - {$job[0]['job_title']} [ref#{$job[0]['jobid']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
					
					/*$log_email_content = "--visiontechdigital.com\n\n" .
												$_POST['log_content'] .
												"\n\n--\n" . $this->userdata['signature'];
						*/							
				/*	$log_email_content = "--enoahisolution.com\n\n" .
												$_POST['log_content'] .
					
											"\n\n--\n" . $this->userdata['signature'];*/	
		
				
				$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$client[0]['first_name'].'&nbsp;'.$client[0]['last_name'].'</p>
    <p style="padding: 4px;">'.
        $_POST['log_content'].'<br /><br />
		This log has been emailed to:<br />
		'.$received_by.'<br /><br />
		'.$this->userdata['signature'].'<br />
    </p>
</div>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';												
											
											
					$pdf_file_attach = array();

					$json['debug_info'] = '';
					
					if (isset($_POST['attach_pdf']))
					{
						
						$temp_file_prefix = ($job[0]['job_status'] < 4) ? 'quotation' : 'invoice';
						$temp_file_name = $temp_file_prefix . '-' . $job[0]['invoice_no'];
						$temp_file_path =  $temp_file_name;
					
						$full_file_path = dirname(FCPATH) . '/vps_data/' . $temp_file_path . '.pdf';
						$path="vps_data/".$temp_file_path .'.pdf';
						$full_url_path = base_url().$path;
						//$json['debug_info'] .= 'file attach requested - ';
						
						$content_policy = TRUE;
						if (isset($_POST['ignore_content_policy']))
						{
							$content_policy = FALSE;
						}
						
						$this->view_plain_quote($job[0]['jobid'], TRUE, FALSE, FALSE, $temp_file_path, '', $content_policy);
							


						if (file_exists($full_file_path))
						{
							//$pdf_file_attach= array($full_file_path, $temp_file_name . '.pdf');
							//$json['debug_info'] .= ' -- attachment set';
						}
					}
					if (isset($_POST['email_to_customer']) && isset($_POST['client_email_address']) && isset($_POST['client_full_name']))
					{
						// we're emailing the client, so remove the VCS log  prefix
						$log_subject = preg_replace('/^eNoah Notification \- /', '', $log_subject);
						
						
						//$json['debug_info'] .= 'email to cust init > ';
						
						for ($cei = 1; $cei < 5; $cei ++)
						{
							if (isset($_POST['client_emails_' . $cei]))
							{
								//$json['debug_info'] .= 'loop through - ' . $cei;
								
								//$send_to[] = array($_POST['client_emails_' . $cei], '');
								$send_to[] = array($_POST['client_emails_' . $cei], '');
								$received_by .= $_POST['client_emails_' . $cei] . ', ';
							}
						}
						
						if (isset($_POST['additional_client_emails']) && trim($_POST['additional_client_emails']) != '')
						{
							//$json['debug_info'] .= ' > adiitional posts';
							$additional_client_emails = explode(',', trim($_POST['additional_client_emails'], ' ,'));
							if (is_array($additional_client_emails)) foreach ($additional_client_emails as $aces)
							{
								$aces = trim($aces);
								if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $aces))
								{
									//$json['debug_info'] .= ' > adiitional add - ' . $aces;
									
									//$send_to[] = array($aces, '');
									$send_to[] = array($aces, '');
									$received_by .= $aces . ', ';
								}
							}
						}
						
						// if the email goes to client - and the PDF attached, we need to CC accounts
						# removed as per request by George - 16-07-2009
						if (count($pdf_file_attach))
						{
							//$send_to[] = array('accounts@visiontechdigital.com', '');
							//$received_by .= 'accounts@visiontechdigital.com, ';
							//$send_to = array('jranand@enoahisolution.com', '');
							//$send_to[] = array('vgovindaraju@enoahisolution.com', 'Arunkumar');
							//$received_by .= 'jranand@enoahisolution.com, ';
						}
						
					}
					else
					{
						//$log_email_content .= "\n\n\n{$job[0]['job_title']} - {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}" .
												//"\n".$this->config->item('base_url')."welcome/view_quote/{$_POST['jobid']}";
												$dis['date_created'] = date('Y-m-d H:i:s');
														$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
		
						               // $log_email_content = <<<HDOCHDOC;
		
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$client[0]['first_name'].'&nbsp;'.$client[0]['last_name'].'</p>
    <p style="padding: 4px;">'.
        $_POST['log_content'].'<br /><br />
		This log has been emailed to:<br />
		'.$received_by.'<br /><br />
		'.$this->userdata['signature'].'<br />
    </p>
</div>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';						
										
					}

					$json['status_updated'] = false;
					if (isset($_POST['requesting_client_approval']) &&  $job[0]['job_status'] < 3)
					{
						$this->db->where('jobid', $_POST['jobid']);
						if ($this->db->update($this->cfg['dbpref'] . 'jobs', array('job_status' => '3')))
						{
							$json['status_updated'] = true;
						}
					}
					//print_r($user_data);
					//$this->email->from('jranand@enoahisolution.com','Anand');
					//$this->email->to('jranand@enoahisolution.com','Anand');
					//$send_to = array('rkumaran@enoahisolution.com', 'Kumaran');
					//$send_to = array('jranand@enoahisolution.com', 'JR Anand');
					//$send_to[] = array('sarunkumar@enoahisolution.com', 'Arunkumar');
					$this->email->from($user_data[0]['email'],$user_data[0]['first_name']);
					//$this->email->from('sarunkumar@enoahisolution.com','Arunkumar');
					foreach($send_to as $recps){
						$arrRecs[]=$recps[0];
					}
					$senders=implode(',',$arrRecs);
					$this->email->to($senders);
					$this->email->subject($log_subject);
					$this->email->message($log_email_content);
					if(!empty($full_url_path)){
					$this->email->attach($full_file_path);
					}
					if($this->email->send()){
						$successful .= trim($received_by, ', ');
					}
					else{
						echo 'failure';
					}
					
					/*if (send_email($send_to, $log_subject, $log_email_content, $user_data[0]['email'], $user_data[0]['first_name'] . ' ' . $user_data[0]['last_name'], '', '', $pdf_file_attach))
					{
						$successful .= trim($received_by, ', ');
					}
					*/
					
					
					
					
					if (isset($full_file_path) && is_file($full_file_path)) unlink ($full_file_path);
					
					if ($successful == 'This log has been emailed to:<br />')
					{
						$successful = '';
					}
					else
					{
						$successful = '<br /><br />' . $successful;
					}
				}
			
				$ins['jobid_fk'] = $_POST['jobid'];
				
				// use this to update the view status
				$ins['userid_fk'] = $upd['log_view_status'] = $_POST['userid'];
				
				$ins['date_created'] = date('Y-m-d H:i:s');
				$ins['log_content'] = $_POST['log_content'] . $successful;
				
				$stick_class = '';
				if (isset($_POST['log_stickie']))
				{
					$ins['stickie'] = 1;
					$stick_class = ' stickie';
				}
				
				if (isset($_POST['time_spent']))
				{
					$ins['time_spent'] = (int) $_POST['time_spent'];
				}
				
				// inset the new log
				$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
				
				// update the jobs table
				$this->db->where('jobid', $ins['jobid_fk']);
				$this->db->update($this->cfg['dbpref'] . 'jobs', $upd);
                
                $log_content = nl2br(auto_link(special_char_cleanup(ascii_to_entities(htmlentities(str_ireplace('<br />', "\n", $_POST['log_content'])))), 'url', TRUE)) . $successful;
                
				$fancy_date = date('l, jS F y h:iA', strtotime($ins['date_created']));
				
                /*$table = <<<HDOC
<div class="log{$stick_class}" style="display:none;">
    <p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</div>
HDOC;
*/
$table = <<<HDOC
<tr id="log" class="log{$stick_class}">
<td id="log" class="log">
<p class="data">
        <span>{$fancy_date}</span>
    {$user_data[0]['first_name']} {$user_data[0]['last_name']}
    </p>
    <p class="desc">
        {$log_content}
    </p>
</td>
</tr>
HDOC;
				
                $json['error'] = FALSE;
                $json['html'] = $table;
				
                echo json_encode($json);
				
            }
            else
            {
                echo "{error:true, errormsg:'Post insert failed'}";
            }
        }
        else
        {
            echo "{error:true, errormsg:'Invalid data supplied'}";
        }
    }
    
	function add_project_received_payments($update = false, $eid = false)
	{	
		//echo $eid; echo "<pre>"; print_r($_POST); exit;
		$errors = array();
		
		if (isset($_POST['pr_date_2']) && !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $_POST['pr_date_2']))
		{
			$errors[] = 'Invalid deposit amount';
		}
		
		if (!isset($_POST['pr_form_jobid']) || (int) $_POST['pr_form_jobid'] == 0)
		{
			$errors[] = 'Invalid job ID supplied';
		}
		
		if (!isset($_POST['pr_date_3']) || !preg_match('/^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/', $_POST['pr_date_3']) || strtotime($_POST['pr_date_3']) == FALSE)
		{
			$errors[] = 'Invalid deposit date supplied';
		}
		
		$exp_amt = $this->db->query("select amount from crm_expected_payments where jobid_fk = '".$_POST['pr_form_jobid']."' AND expectid = '".$_POST['deposit_map_field']."' ");
		$expect_payment = $exp_amt->row_array();
		
		if (!isset($update)) {
			$tot_rec_amt = $this->db->query("select sum(amount) as tot_amt from crm_deposits where jobid_fk = '".$_POST['pr_form_jobid']."' AND map_term = '".$_POST['deposit_map_field']."' ");
			$received_payment = $tot_rec_amt->row_array();
			$temp_tot_amt = $_POST['pr_date_2'] + $received_payment['tot_amt'];
			$remaining_amt = $expect_payment['amount'] - $received_payment['tot_amt'];
		} else {
			$tot_rec_amt = $this->db->query("select sum(amount) as tot_amt from crm_deposits where jobid_fk = '".$_POST['pr_form_jobid']."' AND map_term = '".$_POST['deposit_map_field']."' AND depositid != '".$update."' ");
			$received_payment = $tot_rec_amt->row_array();
			$temp_tot_amt = $_POST['pr_date_2'] + $received_payment['tot_amt'];
			$remaining_amt = $expect_payment['amount'] - $received_payment['tot_amt'];
		}	
		//echo $this->db->last_query(); exit;

		if ($temp_tot_amt > $expect_payment['amount']) {
			//$errors[] = 'Error: Received Payment should not be greater than Expected Payment.';
			$errors[] = 'Error: As per payment milestone value of '.$expect_payment['amount'].', pending amount to be received is only '.$remaining_amt.'. Amount entered is higher than this value.';
		}	
		
		if (count($errors))
		{	
			$json['error'] = true;
			$json['errormsg'] = join($errors);
			echo json_encode($json);
			//echo "{error:true, errormsg:'" . join('\n', $errors) . "'}";
			//echo "<script>alert(' " . join('\n', $errors) . " ');</script>"; exit;
		}
		else
		{	
			$data = array(
							'jobid_fk' => $_POST['pr_form_jobid'],
							'invoice_no' => $_POST['pr_date_1'],
							'amount' => $_POST['pr_date_2'],
							'deposit_date' => date('Y-m-d H:i:s', strtotime($_POST['pr_date_3'])),
							'comments' => $_POST['pr_date_4'],
							'payment_received' => 1,
							'map_term' => $_POST['deposit_map_field']
						  );
			
			if ($update == "") {
				$this->db->insert($this->cfg['dbpref'] . 'deposits', $data);
				
				$dd = strtotime($_POST['pr_date_3']);
				$deposit_date = date('Y-m-d', $dd); 
				//mychanges
				$jid = $_POST['pr_form_jobid']; //16 
				$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jid'");
				$jres = $jsql->result();
				$worthid = $jres[0]->expect_worth_id;
				
				$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");			
				$eres = $expect_worth->result();			
				$symbol = $eres[0]->expect_worth_name;
				
				$userdata = $this->session->userdata('logged_in_user');
				$userid = $userdata['userid'];					
				$jobid = $data['jobid_fk'];
				$filename = 'Invoice No: '.$data['invoice_no'].'  Amount: '.$symbol.' '.$data['amount'].'  Deposit Date: '.$deposit_date.' Map term:'.$data['map_term']; //filename
				
				$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
				VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is created.' ,'".$filename."')";                 
				$qlogs = $this->db->query($logs);
			}
			else {
				$this->db->query("update crm_expected_payments set received = 0 where expectid = '".$eid."' AND jobid_fk = '".$_POST['pr_form_jobid']."' ");
				//echo $this->db->last_query();
				$updatepayment = array(
					'jobid_fk' => $_POST['pr_form_jobid'],
					'invoice_no' => $_POST['pr_date_1'],
					'amount' => $_POST['pr_date_2'],
					'deposit_date' => date('Y-m-d H:i:s', strtotime($_POST['pr_date_3'])),
					'comments' => $_POST['pr_date_4'],
					'userid_fk' => $this->userdata['userid'],
					'payment_received' => 1,
					'map_term' => $_POST['deposit_map_field']
				);
				$this->db->where('depositid', $update);
				$this->db->where('jobid_fk', $_POST['pr_form_jobid']);
				$this->db->update($this->cfg['dbpref'] . 'deposits', $updatepayment);
				
				//mychanges	
				$jid = $_POST['pr_form_jobid']; //16 
				$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jid'");
				$jres = $jsql->result();
				$worthid = $jres[0]->expect_worth_id;
				
				$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");			
				$eres = $expect_worth->result();			
				$symbol = $eres[0]->expect_worth_name;
				$dd = strtotime($updatepayment['deposit_date']);
				$deposit_date = date('Y-m-d', $dd);
				$userdata = $this->session->userdata('logged_in_user');
				$userid = $userdata['userid'];					
				$jobid = $updatepayment['jobid_fk'];
				$filename = 'Invoice No: '.$updatepayment['invoice_no'].'  Amount: '.$symbol.' '.$updatepayment['amount'].'  Deposit Date: '.$deposit_date.' Map term:'.$updatepayment['map_term']; //filename
				
				$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
				VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is updated.' ,'".$filename."')";                 
				$qlogs = $this->db->query($logs);
				//echo $this->db->last_query();
			}
			if (isset($_POST['deposit_map_field']) && $_POST['deposit_map_field'] > 0 && preg_match('/^[0-9]+$/', $_POST['deposit_map_field']))
			{
				$statusQuery = $this->db->query("select sum(amount) as tot_amt from crm_deposits where jobid_fk = '".$_POST['pr_form_jobid']."' AND map_term = '".$_POST['deposit_map_field']."' ");
				$payment_status = $statusQuery->row_array();
				
				$statusQueryExpect = $this->db->query("select amount from crm_expected_payments where jobid_fk = '".$_POST['pr_form_jobid']."' AND expectid = '".$_POST['deposit_map_field']."' ");
				$payment_status_expect = $statusQueryExpect->row_array();
				if ($payment_status['tot_amt'] >= $payment_status_expect['amount']) {
					$this->db->where('expectid', $_POST['deposit_map_field']);
					$this->db->update($this->cfg['dbpref'] . 'expected_payments', array('received' => 1));
				}
				else {
					$this->db->where('expectid', $_POST['deposit_map_field']);
					$this->db->update($this->cfg['dbpref'] . 'expected_payments', array('received' => 2));
				}
			}
			
			$output = '';
			$recieve_query = $this->db->query("SELECT `crm_deposits` . * , `crm_expected_payments`.`project_milestone_name` AS payment_term FROM (`crm_deposits`) LEFT JOIN `crm_expected_payments` ON `crm_deposits`.`map_term` = `crm_expected_payments`.`expectid` WHERE `crm_deposits`.`jobid_fk` = ".$_POST['pr_form_jobid']." ORDER BY `depositid` ASC");
			
			//$data['receive'] = $recieve_query1->result_array();
			//$this->load->view('welcome_view_project', $data);
			$output .= '<div class="payment-received-mini-view2" style="margin-top:5px;">';
			$pdi = 1;
			$output .= '<option value="0"> &nbsp; </option>';
			$output .= "<p><h6>Payment History</h6></p>";
			$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
			$output .= "<thead>";
			$output .= "<tr align='left'>";
			$output .= "<th class='header'>Invoice No</th>";
			$output .= "<th class='header'>Date Received</th>";
			$output .= "<th class='header'>Amt Received</th>";
			$output .= "<th class='header'>Payment Term</th>";
			$output .= "<th class='header'>Action</th>";
			$output .= "</tr>";
			$output .= "</thead>";
			foreach ($recieve_query->result_array() as $dd)
			{
				$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
				$payment_amount = number_format($dd['amount'], 2, '.', ',');
				$amount_recieved += $dd['amount'];
				$payment_received = '';
				if ($dd['payment_received'] == 1)
				{
					$payment_received = '<img src="assets/img/vcs-payment-received.gif" alt="received" />';
				}
				$output .= "<tr align='left'>";
				$output .= "<td>".$dd['invoice_no']."</td>";
				$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
				$output .= "<td> ".$symbol.' '.number_format($dd['amount'], 2, '.', ',')."</td>";
				$output .= "<td>".$dd['payment_term']."</td>";
				$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
				$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
				$output .= "</tr>";
			}
			$output .= "<tr>";
			$output .= "<td></td>";
			$output .= "<td><b>Total Payment: </b> </td><td colspan='2'><b>".$symbol.' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
			$output .= "</tr>";
			$output .= "</table>";
			$output .= "</div>";
			//echo $output;
			//echo "{error:false}";
			$json['error'] = false;
			$json['msg'] = $output;
			echo json_encode($json);
		}
		
	}
	
	function received_payment_terms_delete($jid)
	{
		//mychanges
			$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id;
			$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;		
		
		$userdata = $this->session->userdata('logged_in_user'); 
		$userid=$userdata['userid'];
		$query = $this->db->get_where('crm_deposits', array('depositid' => $pdid, 'jobid_fk' => $jid ));
		$get = $query->row_array();		
		$milename = $get['invoice_no'];
		$amount = $get['amount'];
		$map_term = $get['map_term'];
		$expectdate = date('Y-m-d',strtotime($get['deposit_date']));	
		$filename = 'Invoice No: '.$milename.'  Amount: '.$symbol.' '.$amount.'  Deposit Date: '.$expectdate.' Map Term: '.$map_term; 
		
		//$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
        //VALUES('".$jid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is deleted.' ,'".$filename."')";                 
        //$qlogs = $this->db->query($logs);
	
		$output = '';
		$recieve_query = $this->db->query("SELECT `crm_deposits` . * , `crm_expected_payments`.`project_milestone_name` AS payment_term FROM (`crm_deposits`) LEFT JOIN `crm_expected_payments` ON `crm_deposits`.`map_term` = `crm_expected_payments`.`expectid` WHERE `crm_deposits`.`jobid_fk` = ".$jid." ORDER BY `depositid` ASC");
		
		//$data['receive'] = $recieve_query1->result_array();
		//echo "<pre> sdkjfdsk"; print_r($data); die();
		//$this->load->view('welcome_view_project', $data);
		$output .= '<div class="payment-received-mini-view2" style="margin-top:5px;">';
		//$output .= '<h3>Payment Recieved</h3>';
		$pdi = 1;
		$output .= '<option value="0"> &nbsp; </option>';
		$output .= "<p><h6>Payment History</h6></p>";
		$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
		$output .= "<thead>";
		$output .= "<tr align='left'>";
		$output .= "<th class='header'>Invoice No</th>";
		$output .= "<th class='header'>Date Received</th>";
		$output .= "<th class='header'>Amt Received</th>";
		$output .= "<th class='header'>Payment Term</th>";
		$output .= "<th class='header'>Action</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		foreach ($recieve_query->result_array() as $dd)
		{
			$expected_date = date('d-m-Y', strtotime($dd['deposit_date']));
			$payment_amount = number_format($dd['amount'], 2, '.', ',');
			$amount_recieved += $dd['amount'];
			$payment_received = '';
			if ($dd['payment_received'] == 1)
			{
				$payment_received = '<img src="assets/img/vcs-payment-received.gif" alt="received" />';
			}
			$output .= "<tr align='left'>";
			$output .= "<td>".$dd['invoice_no']."</td>";
			$output .= "<td>".date('d-m-Y', strtotime($dd['deposit_date']))."</td>";
			$output .= "<td> ".$symbol.' '.number_format($dd['amount'], 2, '.', ',')."</td>";
			$output .= "<td>".$dd['payment_term']."</td>";
			$output .= "<td align='left'><a class='edit' onclick='paymentReceivedEdit(".$dd['depositid']."); return false;' >Edit</a> | ";
			$output .= "<a class='edit' onclick='paymentReceivedDelete(".$dd['depositid'].",".$dd['map_term'].");' >Delete</a></td>";
			$output .= "</tr>";
		}
		$output .= "<tr>";
		$output .= "<td></td>";
		$output .= "<td><b>Total Payment: </b> </td><td colspan='2'><b>".$symbol.' '.number_format($amount_recieved, 2, '.', ',')."</b></td>";
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "</div>";
		echo $output;
	}
	
	/**
	 * sets the payment terms
	 * for the invoice
	 */
	function set_payment_terms($update = false)
	{
		
		$errors = array();
		$today = time();
		
		//$perc1 = (int) $_POST['sp_perc_1'];
		//$perc2 = (int) $_POST['sp_perc_2'];
		//$perc3 = (int) $_POST['sp_perc_3'];
		
		$pdate1 = $_POST['sp_date_1'];
		$pdate2 = strtotime($_POST['sp_date_2']);
		$pdate3 = $_POST['sp_date_3'];
		
		//$total =  $perc1 + $perc2 + $perc3;
		
		/*if ($total != 100)
		{
			$errors[] = 'Make sure the percentage values add up to 100%';
		}*/
		
		/*if (
			($perc1 > 0 && (!$pdate1 || $pdate1 < $today)) ||
			($perc2 > 0 && (!$pdate2 || $pdate2 < $today)) ||
			($perc3 > 0 && (!$pdate3 || $pdate3 < $today))
		   )*/
		/*if (
			(!$pdate2 || $pdate2 < $today)			
		   )
		{
			$errors[] = 'You have supplied invalid dates';
		}*/
		
		if (count($errors))
		{
			echo "<p style='color:#FF4400;'>" . join('\n', $errors) . "</p>";
		}
		else
		{
			$job_updated = FALSE;
			
			/*if ($perc1 > 0)
			{
				$amount1 = round(($_POST['sp_form_invoice_total'] / 100) * $perc1, 2);
				$expected_date1 = date('Y-m-d', $pdate1);
				$data1 = array(
					'jobid_fk' => $_POST['sp_form_jobid'],
					'percentage' => $perc1,
					'amount' => $amount1,
					'expected_date' => $expected_date1
				);
				$this->db->insert($this->cfg['dbpref'] . 'expected_payments', $data1);
				$job_updated = TRUE;
			}
			
			if ($perc2 > 0)
			{
				$amount2 = round(($_POST['sp_form_invoice_total'] / 100) * $perc2, 2);
				$expected_date2 = date('Y-m-d', $pdate2);
				$data2 = array(
					'jobid_fk' => $_POST['sp_form_jobid'],
					'percentage' => $perc2,
					'amount' => $amount2,
					'expected_date' => $expected_date2
				);
				$this->db->insert($this->cfg['dbpref'] . 'expected_payments', $data2);
				$job_updated = TRUE;
			}
			
			if ($perc3 > 0)
			{*/
				//$amount3 = round(($_POST['sp_form_invoice_total'] / 100) * $perc3, 2);
				$expected_date = date('Y-m-d', $pdate2);
				$data3 = array(
					'jobid_fk' => $_POST['sp_form_jobid'],
					'percentage' => '0',
					'amount' => $pdate3,
					'expected_date' => $expected_date,
					'project_milestone_name' => $pdate1
				);
				
				//mychanges
				$jid = $_POST['sp_form_jobid']; //16 
				$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jid'");
				$jres = $jsql->result();
				$worthid = $jres[0]->expect_worth_id;
				
				$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");			
				$eres = $expect_worth->result();			
				$symbol = $eres[0]->expect_worth_name;
				
				if ($update == "") {
					$this->db->insert($this->cfg['dbpref'] . 'expected_payments', $data3);
					
					$userdata = $this->session->userdata('logged_in_user');
					$userid = $userdata['userid'];					
					$jobid = $data3['jobid_fk'];
					$filename = 'Project Milestone Name: '.$data3['project_milestone_name'].'  Amount: '.$symbol.' '.$data3['amount'].'  Expected Date: '.$data3['expected_date']; //filename
					
					$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
                    VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is created.' ,'".$filename."')";                 
                    $qlogs = $this->db->query($logs);
					
				} else {
										
					$chkstatus = $this->db->query("select received from crm_expected_payments where expectid = '".$update."' and jobid_fk = '".$_POST['sp_form_jobid']."'");
					$pay_status = $chkstatus->row_array();
					if ($pay_status['received'] != 1) {
						$userdata = $this->session->userdata('logged_in_user');
						$userid = $userdata['userid'];					
						$jobid = $data3['jobid_fk'];
						$filename = 'Project Milestone Name: '.$data3['project_milestone_name'].'  Amount: '.$symbol.' '.$data3['amount'].'  Expected Date: '.$data3['expected_date']; //filename
						
						$logs = "INSERT INTO ".$this->cfg['dbpref']."logs (jobid_fk,userid_fk,date_created,log_content,attached_docs)
						VALUES('".$jobid."','".$userid."','".date('Y-m-d H:i:s')."','".$filename." is updated.' ,'".$filename."')";                 
						$qlogs = $this->db->query($logs);
						
						$updatepayment = array(
						'amount' => $pdate3,
						'expected_date' => $expected_date,
						'project_milestone_name' => $pdate1
					);
					$this->db->where('expectid', $update);
					$this->db->where('jobid_fk', $_POST['sp_form_jobid']);
					$this->db->update($this->cfg['dbpref'] . 'expected_payments', $updatepayment);
					}
					else {
						echo "<span id=paymentfadeout><h6>Received Payment cannot be Edited!</h6></span>";
					}	
					//echo $this->db->last_query();
				}	
				$job_updated = TRUE;
			//}
			
			
			if ($job_updated)
			{
				$data = array(
					'payment_terms' => 1
				);
				$this->db->update($this->cfg['dbpref'] . 'jobs', $data, array('jobid' => $_POST['sp_form_jobid']));
				$ajax_select = $this->db->query("SELECT expectid, expected_date, amount, project_milestone_name, received FROM crm_expected_payments WHERE jobid_fk = ".$_POST['sp_form_jobid']." order by expectid ");
				$output = '';
				$output .= '<div class="payment-terms-mini-view2" style="float:left; margin-top: 5px;">';
					//$output .= '<h3>Payment Milestone Terms</h3>';
				$pdi = 1;
				$pt_select_box = '';
				$pt_select_box .= '<option value="0"> &nbsp; </option>';
				$output .= "<table width='100%' class='payment_tbl'>
				<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
				<tr>
				<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
				<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
				<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
				</tr>
				</table>";
				$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
				$output .= "<thead>";
				$output .= "<tr align='left'>";
				$output .= "<th class='header'>Payment Milestone</th>";
				$output .= "<th class='header'>Milestone Date</th>";
				$output .= "<th class='header'>Amount</th>";
				$output .= "<th class='header'>Status</th>";
				$output .= "<th class='header'>Action</th>";
				$output .= "</tr>";
				$output .= "</thead>";
				foreach ($ajax_select->result_array() as $pd)
				{
					$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
					$payment_amount = number_format($pd['amount'], 2, '.', ',');
					$total_amount_recieved += $pd['amount'];
					$payment_received = '';
					if ($pd['received'] == 0)
					{
						$payment_received = '<img src="assets/img/payment-due.jpg" alt="Due" height="10" width="10" />';
					}
					else if ($pd['received'] == 1)
					{
						$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
					}
					else
					{
						$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
					}							
					$output .= "<tr>";
					$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
					$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
					$output .= "<td align='left'> ".$symbol.' '.number_format($pd['amount'], 2, '.', ',')."</td>";
					$output .= "<td align='center'>".$payment_received."</td>";
					$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' >Edit</a> | ";
					$output .= "<a class='edit' onclick='paymentProfileDelete(".$pd['expectid']."); return false;' >Delete</a></td>";
					$output .= "</tr>";
					//echo "<p><strong>Payment #{$pdi}</strong> &raquo; {$pd['percentage']}% by {$expected_date} = \${$payment_amount} {$payment_received}</p>";
					$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . $pd['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
					$pdi ++;
				}
				$output .= "<tr>";
				$output .= "<td></td>";
				$output .= "<td><b>Total Milestone Payment : </b></td><td><b>".$symbol.' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
				$output .= "</tr>";
				$output .= "</table>";
				$output .= '</div>';
				echo $output;
			}
			else
			{
				echo "{error:true, errormsg:'Percentage update failed'}";
			}
			
		}
		
	}
	//for edit and delete functionality
	function payment_terms_delete($jid)
	{
		//mychanges			
			$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id; 
			$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;
		
		$ajax_select = $this->db->query("SELECT expectid, expected_date, amount, project_milestone_name, received FROM crm_expected_payments WHERE jobid_fk = ".$jid." order by expectid ");
		$output = '';
		$output .= '<div class="payment-terms-mini-view2" style="float:left; margin-top: 5px;">';
			//$output .= '<h3>Payment Milestone Terms</h3>';
			$pdi = 1;
			$pt_select_box = '';
			$pt_select_box .= '<option value="0"> &nbsp; </option>';
			$output .= "<table width='100%' class='payment_tbl'>
						<tr><td colspan='3'><h6>Agreed Payment Terms</h6></td></tr>
						<tr>
						<td><img src=assets/img/payment-received.jpg height='10' width='10' > Payment Received</td>
						<td><img src=assets/img/payment-pending.jpg height='10' width='10' > Partial Payment</td>
						<td><img src=assets/img/payment-due.jpg height='10' width='10' > Payment Due</td>
						</tr>
						</table>";
			$output .= "<table class='data-table' cellspacing = '0' cellpadding = '0' border = '0'>";
			$output .= "<thead>";
			$output .= "<tr align='left'>";
			$output .= "<th class='header'>Payment Milestone</th>";
			$output .= "<th class='header'>Milestone Date</th>";
			$output .= "<th class='header'>Amount</th>";
			$output .= "<th class='header'>Status</th>";
			$output .= "<th class='header'>Action</th>";
			$output .= "</tr>";
			$output .= "</thead>";
			foreach ($ajax_select->result_array() as $pd)
			{
				$expected_date = date('d-m-Y', strtotime($pd['expected_date']));
				$payment_amount = number_format($pd['amount'], 2, '.', ',');
				$total_amount_recieved += $pd['amount'];
				$payment_received = '';
				if ($pd['received'] == 0)
				{
					$payment_received = '<img src="assets/img/payment-due.jpg" alt="Due" height="10" width="10" />';
				}
				else if ($pd['received'] == 1)
				{
					$payment_received = '<img src="assets/img/payment-received.jpg" alt="received" height="10" width="10" />';
				}
				else
				{
					$payment_received = '<img src="assets/img/payment-pending.jpg" alt="pending" height="10" width="10" />';
				}							
				$output .= "<tr>";
				$output .= "<td align='left'>".$pd['project_milestone_name']."</td>";
				$output .= "<td align='left'>".date('d-m-Y', strtotime($pd['expected_date']))."</td>";
				$output .= "<td align='left'> ".$symbol.' '.number_format($pd['amount'], 2, '.', ',')."</td>";
				$output .= "<td align='center'>".$payment_received."</td>";
				$output .= "<td align='left'><a class='edit' onclick='paymentProfileEdit(".$pd['expectid']."); return false;' >Edit</a> | ";
				$output .= "<a class='edit' onclick='paymentProfileDelete(".$pd['expectid']."); return false;' >Delete</a></td>";
				$output .= "</tr>";
				//echo "<p><strong>Payment #{$pdi}</strong> &raquo; {$pd['percentage']}% by {$expected_date} = \${$payment_amount} {$payment_received}</p>";
				$pt_select_box .= '<option value="'. $pd['expectid'] .'">' . $pd['project_milestone_name'] ." \${$payment_amount} by {$expected_date}" . '</option>';
				$pdi ++;
			}
			$output .= "<tr>";
			$output .= "<td></td>";
			$output .= "<td><b>Total Milestone Payment : </b></td><td><b>".$symbol.' '.number_format($total_amount_recieved, 2, '.', ',') ."</b></td>";
			$output .= "</tr>";
			$output .= "</table>";
			$output .= '</div>';
		echo $output;
	}
	
	/**
	 * Add the deposits to the relavant job
	 */
	function add_deposit_payments()
	{
		$errors = array();
		
		if (isset($_POST['deposit_amount_add']) && !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $_POST['deposit_amount_add']))
		{
			$errors[] = 'Invalid deposit amount';
		}
		
		if (!isset($_POST['deposit_form_jobid']) || (int) $_POST['deposit_form_jobid'] == 0)
		{
			$errors[] = 'Invalid job ID supplied';
		}
		
		if (!isset($_POST['deposit_date']) || !preg_match('/^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/', $_POST['deposit_date']) || strtotime($_POST['deposit_date']) == FALSE)
		{
			$errors[] = 'Invalid deposit date supplied';
		}
		
		if (count($errors))
		{
			echo "{error:true, errormsg:'" . join('\n', $errors) . "'}";
		}
		else
		{
			$data = array(
					'jobid_fk' => $_POST['deposit_form_jobid'],
					'amount' => $_POST['deposit_amount_add'],
					'deposit_date' => date('Y-m-d H:i:s', strtotime($_POST['deposit_date'])),
					'comments' => $_POST['deposit_comments']
					);
			
			$this->db->insert($this->cfg['dbpref'] . 'deposits', $data);
			
			if (isset($_POST['deposit_map_field']) && $_POST['deposit_map_field'] > 0 && preg_match('/^[0-9]+$/', $_POST['deposit_map_field']))
			{
				$this->db->where('expectid', $_POST['deposit_map_field']);
				$this->db->update($this->cfg['dbpref'] . 'expected_payments', array('received' => 1));
			}
			
			if (isset($_POST['belong_to']) && $_POST['belong_to'] == 'AT68')
			{
				/*$job_data = $this->welcome_model->get_job(array('jobid' => $_POST['deposit_form_jobid']));
				$msg = "Hi Angelo,

We have received a payment of \${$_POST['deposit_amount_add']} on {$_POST['deposit_date']} for the following job:

{$job_data['job_title']} - {$job_data['first_name']} {$job_data['last_name']} - {$job_data['company']}

Thanks you.

VCS Admin";*/
				//$this->load->plugin('phpmailer');
				//$to = array('angelo@at68.com.au');
				//send_email($to, 'VCS Payment Notice', $msg, 'admin@enoahisolution.com', 'VCS Admin');
				//@mail('asanka@visiontechdigital.com', 'VCS Payment notification', $msg, "From:admin@enoahisolution.com");
			}
			
			echo "{error:false}";
		}
		
	}
	
    /*
     * Update the quote to a given status
     * @access public
     * @param jobid
     * @param status => desired status
     * @return echo json string
     */
    public function ajax_update_quote($jobid = 0, $status, $log_status = '')
    {
		$this->load->model('user_model');	
		
        if ($jobid != 0 && preg_match('/^[0-9]+$/', $jobid) && preg_match('/^[0-9]+$/', $status) && $the_job = $this->job_model->get_job($jobid))
        {
			if($status>0) {
				//Lead Status History - Start here
				$lead_history = $this->db->query('SELECT job_status, lead_status, actual_worth_amount from crm_jobs where jobid = '.$jobid.'');
				$lead_status_history = $lead_history->row_array();
				$lead_his['jobid'] = $jobid;
				$lead_his['dateofchange'] = date('Y-m-d H:i:s');
				$lead_his['previous_status'] = $lead_status_history['job_status'];
				$lead_his['changed_status'] = $status;
				$lead_his['lead_status'] = $lead_status_history['lead_status'];
				$lead_his['modified_by'] = $this->userdata['userid'];
				//Lead Status History - End here
				
				//get the actual worth amt for the lead
				$actWorthAmt = $lead_status_history['actual_worth_amount']; 
				
				//Check the actual worth amount.
				if($status == '13' && $actWorthAmt == '0.00') {
					echo "{error:true, errormsg:'Actual Worth Amount Must be greater than Zero.'}";
					exit;
				}
				//SET THE PROPOSAL SENT DATE FOR THE LEAD.
				if($status == '7') {
					$update['proposal_sent_date'] = date('Y-m-d H:i:s');
				}
				$update['job_status'] = $status;
				
					$this->db->where('jobid', $jobid);
					if ($this->db->update($this->cfg['dbpref'] . 'jobs', $update))
					{
						$ins['userid_fk'] = $this->userdata['userid'];
						$ins['jobid_fk'] = $jobid;
						$getlead_assign_email = $this->db->query('SELECT j.lead_assign, j.jobid, u.userid, u.email, j.invoice_no, j.job_title
																	FROM `crm_jobs` AS j, `crm_users` AS u
																	WHERE u.userid = j.lead_assign
																	AND j.jobid ='.$jobid);
						$disarray=$getlead_assign_email->result_array();
						$getlead_owner_email = $this->db->query('SELECT j.belong_to, j.jobid, u.userid, u.email
																	FROM `crm_jobs` AS j, `crm_users` AS u
																	WHERE u.userid = j.belong_to
																	AND j.jobid ='.$jobid);
						$lowner=$getlead_owner_email->result_array();
						//print_r($disarray);exit;
						
						$ins['date_created'] = date('Y-m-d H:i:s');
						$status_query = $this->db->query('SELECT lead_stage_name FROM '.$this->cfg['dbpref'] . 'lead_stage WHERE lead_stage_id ='.$status.' ');
						$status_res=$status_query->result_array();
						$ins['log_content'] = "Status Changed to:" .' '. urldecode($status_res[0]['lead_stage_name']) .' ' . 'Sucessfully for the Lead - ' .$disarray[0]['job_title']. ' ';
						if ($status_res[0]['lead_stage_name']!='Project Charter Approved. Convert to Projects In Progress') {
							$ins_email['log_content_email'] = "Status Changed to:" .' '. urldecode($status_res[0]['lead_stage_name']) .' ' . 'Sucessfully for the Lead - <a href='.$this->config->item('base_url').'welcome/view_quote/'.$jobid.'>' .$disarray[0]['job_title']. ' </a>';
						} else {
							$ins_email['log_content_email'] = "Status Changed to:" .' '. urldecode($status_res[0]['lead_stage_name']) .' ' . 'Sucessfully for the Lead - <a href='.$this->config->item('base_url').'invoice/view_project/'.$jobid.'>' .$disarray[0]['job_title']. ' </a>';
						}
						
						// insert the new log
						$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
						
						// insert the lead status history
						$this->db->insert('lead_status_history', $lead_his);
						
						$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
						$dis['date_created'] = date('Y-m-d H:i:s');
						$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
						
						$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<title>Email Template</title>
							<style type="text/css">
							body {
								margin-left: 0px;
								margin-top: 0px;
								margin-right: 0px;
								margin-bottom: 0px;
							}
							</style>
							</head>

							<body>
							<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
							<tr><td bgcolor="#FFFFFF">
							<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
							  <tr>
								<td style="padding:15px; border-bottom:2px #5a595e solid;">
									<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
								</td>
							  </tr>
							  <tr>
								<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Status Change Notification Message</h3></td>
							  </tr>

							  <tr>
								<td>
								<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
								<p style="background: none repeat scroll 0 0 #4B6FB9;
								border-bottom: 1px solid #CCCCCC;
								color: #FFFFFF;
								margin: 0;
								padding: 4px;">
									<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
								<p style="padding: 4px;">'.
									$ins_email['log_content_email'].'<br /><br />
									'.$this->userdata['signature'].'<br />
								</p>
							</div>
							</td>
							  </tr>

							   <tr>
								<td>&nbsp;</td>
							  </tr>
							  <tr>
								<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
							  </tr>
							</table>
							</td>
							</tr>
							</table>
							</body>
							</html>';							
					//$mydata=$q->row();
					$from=$this->userdata['email'];
					$arrEmails = $this->config->item('crm');
					$arrSetEmails=$arrEmails['director_emails'];
					
					$admin_mail=implode(',',$arrSetEmails);
					
					$subject='Status Change Notification';
					$this->email->from($from,$user_name);
					$this->email->to($disarray[0]['email'] .','. $lowner[0]['email']);
					$this->email->bcc($admin_mail);
					$this->email->subject($subject);
					$this->email->message($log_email_content);
					$this->email->send(); 
					echo "{error:false}";
					}
					else
					{
						echo "{error:true, errormsg:'Database update failed!'}";
					}	
			}
			
			else  {
				echo "{error:false}";
			}
        }
        else
        {
            echo "{error:true, errormsg:'Invalid Lead ID or Stage!'}";
        }
    }
    
    /*
     * Update the project to a given status
     * @access public
     * @param jobid
     * @param status => desired status
     * @return echo json string
     */
     public function ajax_update_project($jobid = 0, $status = 0, $log_status = '', $hostingid = 0)
	{
		//echo $jobid; exit;
        if ($jobid != 0 && preg_match('/^[0-9]+$/', $jobid) && preg_match('/^[0-9]+$/', $status) && $the_job = $this->job_model->get_job($jobid))
        {
 			$update['job_status'] = $status;
			//For Hosting
			/*
			$hosting=explode(',',$hostingid);
			if($hosting!=0) {
				$this->db->query("DELETE FROM crm_hosting_job WHERE jobid_fk='{$jobid}'");
				foreach($hosting as $val){
					if($val==0) continue;
					$sql=array('jobid_fk'=>$jobid, 'hostingid_fk'=>$val);
					$q=$this->db->get_where('crm_hosting_job', $sql);
					if ($q->num_rows() > 0) continue;
					$this->db->insert('crm_hosting_job', $sql);
				}
			}*/
			if($status>0){
				//if (in_array($the_job['job_status'], array(0, 1, 2, 3, 15, 21, 22)) && in_array($status, array(4, 5, 6, 7, 8)))
				//{
					$update['date_invoiced'] = $update['date_modified'] = date('Y-m-d H:i:s');
				//}
				$this->db->where('jobid', $jobid);
				if ($this->db->update($this->cfg['dbpref'] . 'jobs', $update))
				{ //echo $this->db->last_query(); exit;
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $jobid;
					
					$ins['date_created'] = date('Y-m-d H:i:s');
					$ins['log_content'] = "Status Change:\n" . urldecode($log_status);
					// inset the new log
					$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
					echo "{error:false}";
				}
				else
				{
					echo "{error:true, errormsg:'Database update failed!'}";
				}
			}
			else echo "{error:false}";
        }
        else
        {
            echo "{error:true, errormsg:'Invalid quote ID or Status!'}";
        }
    }
	
	/**
	 *  Set the quote editing interface
	 */
    function edit_quote($id = 0)
    {
        if ( ($data['quote_data'] = $this->job_model->get_job($id)) !== FALSE )
        {
            $data['edit_quotation'] = true;
			/*
			if ($this->userdata['level'] == 4 && $data['quote_data']['belong_to'] != $this->userdata['sales_code'])
			{
				$this->session->set_flashdata('login_errors', array("You are not allwed to view/edit this document!"));
				$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
				header('Location:' . $referer);
				exit();
			}
			8/
			/**
			 * Check to see if this has already been downloaded by accounts
			 */
			if ($data['quote_data']['invoice_downloaded'] == 1)
			{
				$this->session->set_flashdata('login_errors', array("Reconciled Invoices cannot be edited!"));
				$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
				header('Location:' . $referer);
				exit();
			}
			
			/* additional item list */
			$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
			
			$this->db->order_by('cat_id');
			$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
			$data['categories'] = $q->result_array();
			
			$c = count($data['categories']);
			$data['hosting']=$this->ajax_hosting_load($id);
			for ($i = 0; $i < $c; $i++)
			{
				$this->db->where('item_type', $data['categories'][$i]['cat_id']);
				$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
				$data['categories'][$i]['records'] = $q->result_array();
			}
			$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
			$data['package'] = $qa->result_array();
			
			$lead_edit_sources = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}lead_source where status=1");
			$data['lead_source_edit'] = $lead_edit_sources->result_array();
			/* 
			  Checking role id for assigning members to lead
			  if role id is  1 assign all members
			  if role id not 1 assign members depends on the levels.
			
		 
			if($this->userdata['role_id'] != 1) {
				$lead_edit_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users WHERE level = '{$this->userdata['level']}'");
				$data['lead_assign_edit'] = $lead_edit_assigns->result_array();
			} else {
				$lead_edit_assigns = $this->db->query("SELECT userid,first_name FROM {$this->cfg['dbpref']}users");
				$data['lead_assign_edit'] = $lead_edit_assigns->result_array();
			}	
			*/
			$regid = $data['quote_data']['add1_region'];
			$cntryid = $data['quote_data']['add1_country'];
			$steid = $data['quote_data']['add1_state'];
			// $steid = $data['quote_data']['add1_state'];
			$locid = $data['quote_data']['add1_location'];
			//for new level concept - start here

			$query = $this->db->query("SELECT `user_id` FROM crm_levels_region WHERE `region_id` = $regid && level_id not in(5,4,3) ");
			//echo $this->db->last_query();
			$cntryquery = $this->db->query("SELECT `user_id` FROM crm_levels_country WHERE `country_id` = $cntryid && level_id not in(5,4,2) ");
			//echo $this->db->last_query();
			$stequery = $this->db->query("SELECT `user_id` FROM crm_levels_state WHERE `state_id` = $steid && level_id not in(5,3,2)");
			$locquery = $this->db->query("SELECT `user_id` FROM crm_levels_location WHERE `location_id` = $locid && level_id not in(4,3,2)");
			
			$globalusers = $this->db->query("SELECT userid as user_id FROM crm_users WHERE level in(1)");
			//echo $this->db->last_query();

			$regUserList = $query->result_array();
			$cntryUserList = $cntryquery->result_array();
			$steUserList = $stequery->result_array();
			$locUserList = $locquery->result_array();
			$globalUserList = $globalusers->result_array();
			//print_r($globalUserList);

			$userList = array_merge_recursive($regUserList, $cntryUserList, $steUserList, $locUserList, $globalUserList);
			$users[] = 0;
			foreach($userList as $us)
			{
				$users[] = $us['user_id'];
			}	
			
			$userList = array_unique($users);
			$userList = (array_values($userList));
			//echo "<pre>"; print_r($userList); exit;
			$userList = implode(',', $userList);
			//echo $userList; exit;
			$query = $this->db->query("select userid, first_name, last_name from crm_users where userid in ($userList) order by first_name");
			$data['lead_assign_edit'] = $query->result_array();
			//for new level concept - end here
			
			$expect_worths = $this->db->query("SELECT expect_worth_id,expect_worth_name FROM {$this->cfg['dbpref']}expect_worth");
			$data['expect_worth'] = $expect_worths->result_array();
			
			$actual_worths = $this->db->query("SELECT SUM(`crm_items`.`item_price`) AS `project_cost`
								FROM `{$this->cfg['dbpref']}items`
								WHERE `jobid_fk` = '{$id}' GROUP BY jobid_fk");
			$data['actual_worth'] = $actual_worths->result_array();					
			
			$data['lead_stage'] = $this->welcome_model->get_lead_stage();

			//$add = $this->db->get("{$this->cfg['dbpref']}additional_items");
			//if ($add->num_rows() > 0) $data['item_mgmt_add_list'] = $add->result_array();
			//echo "<pre>"; print_r($data); exit;
            $this->load->view('welcome_view', $data);
        }
        else
        {
            $this->session->set_flashdata('header_messages', array("Status Changed Successfully."));
			header('Location: ' . $_SERVER['HTTP_REFERER']);
            //redirect('welcome/quotation');
        }
        
    }
	
	/*
	 * Create a copy_quote
	 * Loading just the view	 
	 * @access public
	 */
	public function copy_quote($id = 0,$copy=NULL,$lead = FALSE, $customer = FALSE)
	{
		if (empty($copy))	
		{	
			if (is_numeric($lead))
			{
				$lead_details = $this->welcome_model->get_lead($lead);
				$data['existing_lead'] = $lead;
				$data['existing_lead_service'] = $lead_details['belong_to'];
			}
			
			if (is_numeric($customer))
			{
				$data['lead_customer'] = $customer;
			}
			
			/* additional item list */
			$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
			
			$this->db->order_by('cat_id');
			$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
			$data['categories'] = $q->result_array();
			
			$c = count($data['categories']);
			
			for ($i = 0; $i < $c; $i++)
			{
				$this->db->where('item_type', $data['categories'][$i]['cat_id']);
				$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
				$data['categories'][$i]['records'] = $q->result_array();
			}
			$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
			$data['package'] = $qa->result_array();
			
			$data['qid'] = $id;

			$this->load->view('copy_view', $data);
			
		}	
		
		if (!empty($copy))
		{
			$sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`
                WHERE `jobid` = '{$id}'";
        
			$q = $this->db->query($sql);
			if ($q->num_rows() > 0)
			{
				$job_data = $q->result_array();				
				
				$sql_insert = "insert into `{$this->cfg['dbpref']}jobs` (`job_title`,`job_category`,`lead_source`,`lead_assign`,`expect_worth_id`,`expect_worth_amount`,`custid_fk`,
				`job_status`,`created_by`,`modified_by`,`account_manager`,`in_csr`,`belong_to`,`division`,`payment_terms`,
				`invoice_downloaded`,`packageid_fk`,`date_created`,`date_modified`) 
				values 
				('".$_POST['job_title']."','".$job_data[0]['job_category']."','".$_POST['custid_fk']."',
				'0','{$this->userdata['userid']}','{$this->userdata['userid']}','".$job_data[0]['account_manager']."',
				'".$job_data[0]['in_csr']."','".$job_data[0]['belong_to']."','".$job_data[0]['division']."',
				'".$job_data[0]['payment_terms']."','0','".$job_data[0]['packageid_fk']."',NOW(),NOW()) ";
				
				$q = $this->db->query($sql_insert);
				
				$insert_id = $this->db->insert_id();
				
				$invoice_no = (int) $insert_id ;
				$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
				
				$this->db->where('jobid', $insert_id);
				$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_no' => $invoice_no));
				
				$sql_items = "SELECT *
							  FROM `{$this->cfg['dbpref']}items`
					          WHERE `jobid_fk` = '{$id}'";
							  
				  $q = $this->db->query($sql_items);
				  
				  if ($q->num_rows() > 0)
				  {
						$item_data = $q->result_array();
											
						foreach ($item_data as $tmpitemdata)
						{
							$sqlItemInsert = "INSERT INTO `{$this->cfg['dbpref']}items` (`jobid_fk`,
							`item_position`,`item_desc`,`item_price`,`hours`,`ledger_code`) values 
							('".$insert_id."','".$tmpitemdata['item_position']."',
							'".addslashes($tmpitemdata['item_desc'])."',
							'".$tmpitemdata['item_price']."','".$tmpitemdata['hours']."',
							'".$tmpitemdata['ledger_code']."')	";
							
							$q = $this->db->query($sqlItemInsert);
						}	
				  }
				
			}
				
				$id = "";
				
				$id = $insert_id;
				
				$this->login_model->check_login();
			
				if ( ($data['quote_data'] = $this->job_model->get_job($id)) !== FALSE )
				{
					$data['edit_quotation'] = true;
					/*
					if ($this->userdata['level'] == 4 && $data['quote_data']['belong_to'] != $this->userdata['sales_code'])
					{
						$this->session->set_flashdata('login_errors', array("You are not allwed to view/edit this document!"));
						$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
						header('Location:' . $referer);
						exit();
					}
					*/
					/**
					 * Check to see if this has already been downloaded by accounts
					 */
					if ($data['quote_data']['invoice_downloaded'] == 1)
					{
						$this->session->set_flashdata('login_errors', array("Reconciled Invoices cannot be edited!"));
						$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
						header('Location:' . $referer);
						exit();
					}
					
					/* additional item list */
					$data['item_mgmt_add_list'] = $data['item_mgmt_saved_list'] = array();
					
					$this->db->order_by('cat_id');
					$q = $this->db->get($this->cfg['dbpref'] . 'additional_cats');
					$data['categories'] = $q->result_array();
					
					$c = count($data['categories']);
					$data['hosting']=$this->ajax_hosting_load($id);
					for ($i = 0; $i < $c; $i++)
					{
						$this->db->where('item_type', $data['categories'][$i]['cat_id']);
						$q = $this->db->get($this->cfg['dbpref'] . 'additional_items');
						$data['categories'][$i]['records'] = $q->result_array();
					}
					$qa = $this->db->query("SELECT * FROM {$this->cfg['dbpref']}package WHERE status='active'");
					$data['package'] = $qa->result_array();
					//$add = $this->db->get("{$this->cfg['dbpref']}additional_items");
					//if ($add->num_rows() > 0) $data['item_mgmt_add_list'] = $add->result_array();
					
					$this->load->view('welcome_view', $data);
				}
				else
				{
					$this->session->set_flashdata('login_errors', array("Quote does not exist or you may not be authorised to view this."));
					redirect('welcome/quotation');
				}	
		}
		
		
	}
	
	/**
	 * Delets a quotation from the list
	 */
	function delete_quote($id, $list = '')
	{
		if ($this->session->userdata('delete')==1) {
		
        
        $sql = "SELECT *
                FROM `{$this->cfg['dbpref']}jobs`, `{$this->cfg['dbpref']}customers`
                WHERE `custid` = `custid_fk` AND `jobid` = '{$id}'";
        
        $q = $this->db->query($sql);
		
		# Lead Delete Mail Notification
		
					$ins['userid_fk'] = $this->userdata['userid'];
					$ins['jobid_fk'] = $id;
					$getlead_assign_email = $this->db->query('SELECT j.lead_assign, j.jobid, u.userid, u.email, j.invoice_no
										FROM `crm_jobs` AS j, `crm_users` AS u
										WHERE u.userid = j.lead_assign
										AND j.jobid ='.$id);
					$disarray=$getlead_assign_email->result_array();
					
					$getlead_owner_email = $this->db->query('SELECT j.belong_to, j.jobid, u.userid, u.email
										FROM `crm_jobs` AS j, `crm_users` AS u
										WHERE u.userid = j.belong_to
										AND j.jobid ='.$id);
					$lowner=$getlead_owner_email->result_array();
					//print_r($disarray);exit;
					
					$ins['date_created'] = date('Y-m-d H:i:s');
					
					$ins['log_content'] = 'Lead Deleted Sucessfully - Lead No.' .$disarray[0]['invoice_no']. ' ';
					// inset the new log
					$this->db->insert($this->cfg['dbpref'] . 'logs', $ins);
					$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
					$dis['date_created'] = date('Y-m-d H:i:s');
					$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
					
					$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml">
						<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>Email Template</title>
						<style type="text/css">
						body {
							margin-left: 0px;
							margin-top: 0px;
							margin-right: 0px;
							margin-bottom: 0px;
						}
						</style>
						</head>

						<body>
						<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
						<tr><td bgcolor="#FFFFFF">
						<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
						  <tr>
							<td style="padding:15px; border-bottom:2px #5a595e solid;">
								<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
							</td>
						  </tr>
						  <tr>
							<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Deleted Notification Message
</h3></td>
						  </tr>

						  <tr>
							<td>
							<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
							<p style="background: none repeat scroll 0 0 #4B6FB9;
							border-bottom: 1px solid #CCCCCC;
							color: #FFFFFF;
							margin: 0;
							padding: 4px;">
								<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
							<p style="padding: 4px;">'.
								$ins['log_content'].'<br /><br />
								'.$this->userdata['signature'].'<br />
							</p>
						</div>
						</td>
						  </tr>

						   <tr>
							<td>&nbsp;</td>
						  </tr>
						  <tr>
							<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
						  </tr>
						</table>
						</td>
						</tr>
						</table>
						</body>
						</html>';							
				//$mydata=$q->row();
				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$mangement_email = $arrEmails['management_emails'];
				$mgmt_mail = implode(',',$mangement_email);
				$admin_mail=implode(',',$arrSetEmails);
				
				$subject='Lead Delete Notification';
				$this->email->from($from,$user_name);
				//print_r($lowner);exit;
				$this->email->to($mgmt_mail.','.$disarray[0]['email'].','.$lowner[0]['email']);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($log_email_content);
				$this->email->send(); 
		 
		
        if ($q->num_rows() > 0)
        {
			$delete_job_data = $q->result_array();
			
			if ($delete_job_data[0]['job_status'] > 3 && $delete_job_data[0]['invoice_downloaded'] == 1)
			{
				$this->session->set_flashdata('login_errors', array("Processed Invoices cannot be deleted!"));
				$referer = (preg_match('/^http/', $_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->config->item('base_url') . 'welcome/quotation';
				//redirect('welcome/quotation');
				header('Location:' . $referer);
				exit();
			}
			
			$this->db->delete("{$this->cfg['dbpref']}jobs", array('jobid' => $id));
			$this->db->delete("{$this->cfg['dbpref']}items", array('jobid_fk' => $id));
			$this->db->delete("{$this->cfg['dbpref']}logs", array('jobid_fk' => $id));
			$this->db->delete("{$this->cfg['dbpref']}_hosting_job", array('jobid_fk' => $id));
			$this->session->set_flashdata('confirm', array("Item deleted from the system"));
			if ($list != '')
			{
				$list = '/' . $list;
			}

			redirect('welcome/quotation' . $list);
        }
        else
        {
            $this->session->set_flashdata('login_errors', array("Quote does not exist or you may not be authorised to delete quotes."));
            redirect('welcome/quotation');
        }
	}
	else {
		$this->session->set_flashdata('login_errors', array("You have no rights to access this page"));
		redirect('welcome/quotation');
	}
	}
	
    
	/**
	 * Edits the basic quotation details (title, category etc)
	 * via an ajax request
	 */
    function ajax_edit_quote()
    {
		//echo "<pre>"; print_r($quote_data);
		//echo "lead owner hidden : ".$_POST['lead_owner_edit_hidden'] ." lead owner :".  $_POST['lead_owner_edit'];
        if (trim($_POST['job_title']) == '' || !preg_match('/^[0-9]+$/', trim($_POST['job_category'])) )
        {
			echo "{error:true, errormsg:'Title and job category are required fields!'}";
		}
        else if ( !preg_match('/^[0-9]+$/', trim($_POST['jobid_edit'])) )
        {
			echo "{error:true, errormsg:'quote ID must be numeric!'}";
		}
        else
        {
           
		   
            $ins['job_title'] = $_POST['job_title'];
			//$ins['belong_to'] = $_POST['job_belong_to'];
			$ins['division'] = $_POST['job_division'];
			$ins['job_category'] = $_POST['job_category'];
			$ins['lead_source'] = $_POST['lead_source_edit'];
			$ins['expect_worth_id'] = $_POST['expect_worth_edit'];
			$ins['expect_worth_amount'] = $_POST['expect_worth_amount'];
			$ins['actual_worth_amount'] = $_POST['actual_worth'];
			if($_POST['actual_worth'] != $_POST['expect_worth_amount_dup']){			
			$ins['proposal_adjusted_date'] = date('Y-m-d H:i:s');
			}
			if($_POST['lead_assign_edit_hidden'] == null || $_POST['lead_assign_edit_hidden'] == 0) {
			$ins['lead_assign'] = $_POST['lead_assign_edit'];
			} else {
			$ins['lead_assign'] = $_POST['lead_assign_edit_hidden'];
			}
			/* lead owner starts here */
			if($_POST['lead_owner_edit_hidden'] == null || $_POST['lead_owner_edit_hidden'] == 0) {
			$ins['belong_to'] = $_POST['lead_owner_edit'];
			} else {
			$ins['belong_to'] = $_POST['lead_owner_edit_hidden'];
			}
			/*lead owner ends  here*/
			$ins['lead_indicator'] = $_POST['lead_indicator'];
			$ins['lead_status'] = $_POST['lead_status'];
			if($_POST['job_status'] != '' && $_POST['job_status'] != 'null')
			$ins['job_status']  = $_POST['job_status'];			
			$ins['lead_hold_reason'] = $_POST['reason'];
			$ins['date_modified'] = date('Y-m-d H:i:s');
			$ins['modified_by'] = $this->userdata['userid'];
			/* belong to assigned editing the lead owner */
			$ins['belong_to'] = $_POST['lead_owner_edit'];
		/* for onhold reason insert */	
			$inse['log_content'] = "Lead Onhold Reason: "; 
			$inse['log_content'] .= $_POST['reason'];
            $inse['jobid_fk'] = $_POST['jobid_edit'];
            $inse['userid_fk'] = $this->userdata['userid'];
	    if($_POST['reason'] != '' && $_POST['reason'] != 'null')
            $this->db->insert($this->cfg['dbpref'] . 'logs', $inse);	
		/* end of onhold reason insert */
		
		/* for proposal adjust date insert */
	    $ins_ad['log_content'] = 'Actual Worth Amount Modified On :' . ' ' . date('M j, Y g:i A'); 
            $ins_ad['jobid_fk'] = $_POST['jobid_edit'];
	    $ins_ad['userid_fk'] = $this->userdata['userid'];
	    if($_POST['actual_worth'] != $_POST['expect_worth_amount_dup']){
			$this->db->insert($this->cfg['dbpref'] . 'logs', $ins_ad);
	    }
		/* end proposal adjust date insert */
			$jobid = $_POST['jobid_edit'];
			$this->db->where('jobid', $_POST['jobid_edit']);
            if ($this->db->update($this->cfg['dbpref'] . 'jobs', $ins))			
            {
			$his['lead_status'] = $_POST['lead_status']; //lead_status_history - lead_status update
			$this->db->where('jobid', $jobid);
			$this->db->update('lead_status_history', $his);
			// ($_POST['lead_owner_edit_hidden'] ==  $_POST['lead_owner_edit']) for lead owner edit mail settings.
		if(($_POST['lead_assign_edit_hidden'] ==  $_POST['lead_assign_edit'])) {
		
		 //echo $jobid; exit;
			$ins['userid_fk'] = $this->userdata['userid'];
			$ins['jobid_fk'] = $jobid;
			$getlead_assign_email = $this->db->query('SELECT j.lead_assign, j.jobid, u.userid, u.email, j.invoice_no, u.first_name, u.last_name
								FROM `crm_jobs` AS j, `crm_users` AS u
								WHERE u.userid = j.lead_assign
								AND j.jobid ='.$jobid);
			$lead_assign_mail=$getlead_assign_email->result_array();
			$getlead_owner_email = $this->db->query('SELECT j.belong_to, j.jobid, u.userid, u.email
								FROM `crm_jobs` AS j, `crm_users` AS u
								WHERE u.userid = j.belong_to
								AND j.jobid ='.$jobid);
			$lowner=$getlead_owner_email->result_array();
			//echo "<pre>"; print_r($lowner); echo "</pre>"; 
			//print_r($disarray);exit;
			
			$inserts['userid_fk'] = $this->userdata['userid'];
			$inserts['jobid_fk'] = $jobid;
			$inserts['date_created'] = date('Y-m-d H:i:s');
			$inserts['log_content'] = "Lead has been reassigned to: " . $lead_assign_mail[0]['first_name'] .' '.$lead_assign_mail[0]['last_name'] .'<br />'. 'For Lead No.' .$lead_assign_mail[0]['invoice_no']. ' ';
			// inset the new log
			$this->db->insert($this->cfg['dbpref'] . 'logs', $inserts);
			$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
			$dis['date_created'] = date('Y-m-d H:i:s');
			$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
			
			$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Email Template</title>
				<style type="text/css">
				body {
					margin-left: 0px;
					margin-top: 0px;
					margin-right: 0px;
					margin-bottom: 0px;
				}
				</style>
				</head>

				<body>
				<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
				<tr><td bgcolor="#FFFFFF">
				<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
				  <tr>
					<td style="padding:15px; border-bottom:2px #5a595e solid;">
						<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
					</td>
				  </tr>
				  <tr>
					<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Re-assignment Notification
</h3></td>
				  </tr>

				  <tr>
					<td>
					<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
					<p style="background: none repeat scroll 0 0 #4B6FB9;
					border-bottom: 1px solid #CCCCCC;
					color: #FFFFFF;
					margin: 0;
					padding: 4px;">
						<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
					<p style="padding: 4px;">'.
						$inserts['log_content'].'<br /><br />
						'.$this->userdata['signature'].'<br />
					</p>
				</div>
				</td>
				  </tr>

				   <tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
				  </tr>
				</table>
				</td>
				</tr>
				</table>
				</body>
				</html>';							
				//$mydata=$q->row();
				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$mangement_email = $arrEmails['management_emails'];
				$mgmt_mail = implode(',',$mangement_email);
				$admin_mail=implode(',',$arrSetEmails);
				//$taskSetToEmail='jranand@enoahisolution.com';
				//$from_name='eNoah Admin';
				$subject='Lead Re-assigned Notification';
				$this->email->from($from,$user_name);
				//$this->email->to($taskSetEmail);
				$this->email->to($mgmt_mail.','. $lead_assign_mail[0]['email'] .',vgovindaraju@enoahisolution.com'. $lowner[0]['email']);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($log_email_content);
				$this->email->send(); 
				}
				
				/* lead owner edit mail notifiction starts here */
				else if(($_POST['lead_owner_edit_hidden'] ==  $_POST['lead_owner_edit']) ) {
		
			$ins['userid_fk'] = $this->userdata['userid'];
			$ins['jobid_fk'] = $jobid;
			$getlead_assign_email = $this->db->query('SELECT j.lead_assign, j.jobid, u.userid, u.email, j.invoice_no, u.first_name, u.last_name
								FROM `crm_jobs` AS j, `crm_users` AS u
								WHERE u.userid = j.lead_assign
								AND j.jobid ='.$jobid);
			$lead_assign_mail=$getlead_assign_email->result_array();
			$getlead_owner_email = $this->db->query('SELECT j.belong_to, j.jobid, u.userid, u.first_name, u.last_name, u.email
								FROM `crm_jobs` AS j, `crm_users` AS u
								WHERE u.userid = j.belong_to
								AND j.jobid ='.$jobid);
			$lowner=$getlead_owner_email->result_array();
			//echo "<pre>"; print_r($lowner); echo "</pre>"; 
			//print_r($disarray);exit;
			
			$inserts['userid_fk'] = $this->userdata['userid'];
			$inserts['jobid_fk'] = $jobid;
			$inserts['date_created'] = date('Y-m-d H:i:s');
			$inserts['log_content'] = "Lead Owner has been reassigned to: " . $lowner[0]['first_name'] .' '.$lowner[0]['last_name'] .'<br />'. 'For Lead No.' .$lead_assign_mail[0]['invoice_no']. ' ';
			// inset the new log
			$this->db->insert($this->cfg['dbpref'] . 'logs', $inserts);
			$user_name = $this->userdata['first_name'] . ' ' . $this->userdata['last_name'];
			$dis['date_created'] = date('Y-m-d H:i:s');
			$print_fancydate = date('l, jS F y h:iA', strtotime($dis['date_created']));
			
			$log_email_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Email Template</title>
				<style type="text/css">
				body {
					margin-left: 0px;
					margin-top: 0px;
					margin-right: 0px;
					margin-bottom: 0px;
				}
				</style>
				</head>

				<body>
				<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
				<tr><td bgcolor="#FFFFFF">
				<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
				  <tr>
					<td style="padding:15px; border-bottom:2px #5a595e solid;">
						<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
					</td>
				  </tr>
				  <tr>
					<td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Owner Re-assignment Notification
</h3></td>
				  </tr>

				  <tr>
					<td>
					<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
					<p style="background: none repeat scroll 0 0 #4B6FB9;
					border-bottom: 1px solid #CCCCCC;
					color: #FFFFFF;
					margin: 0;
					padding: 4px;">
						<span>'.$print_fancydate.'</span>&nbsp;&nbsp;&nbsp;'.$user_name.'</p>
					<p style="padding: 4px;">'.
						$inserts['log_content'].'<br /><br />
						'.$this->userdata['signature'].'<br />
					</p>
				</div>
				</td>
				  </tr>

				   <tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
				  </tr>
				</table>
				</td>
				</tr>
				</table>
				</body>
				</html>';							
				//$mydata=$q->row();
				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$mangement_email = $arrEmails['management_emails'];
				$mgmt_mail = implode(',',$mangement_email);
				$admin_mail=implode(',',$arrSetEmails);
				//$taskSetToEmail='jranand@enoahisolution.com';
				//$from_name='eNoah Admin';
				$subject='Lead Owner Re-assigned Notification';
				$this->email->from($from,$user_name);
				//$this->email->to($taskSetEmail);
				$this->email->to($mgmt_mail.','. $lowner[0]['email']);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($log_email_content);
				$this->email->send(); 
				}
				/* lead owener eidt mail notification ends here */
			    
			
			//echo $this->db->last_query();
                $json['error'] = false;
				 $this->session->set_flashdata('header_messages', array("Details Updated Successfully."));
                //$json['fancy_jobid'] = str_pad($_POST['jobid_edit'], 6, '0', STR_PAD_LEFT);
                $json['job_title'] = htmlentities($_POST['job_title'], ENT_QUOTES);
                //$json['job_desc'] = nl2br(htmlentities($_POST['job_desc'], ENT_QUOTES));
                $json['job_category'] = $_POST['job_category'];
				
				echo json_encode($json);
				
            }
            else
            {
                echo "{error:true, errormsg:'Data update failed!'}";
            }
			
            
        }
    }
    
	
	/**
	 * Initiates and create the quote based on an ajax request
	 */
	function ajax_create_quote()
	{
		if (trim($_POST['job_title']) == '' || !preg_match('/^[0-9]+$/', trim($_POST['job_category'])) || !preg_match('/^[0-9]+$/', trim($_POST['lead_source'])) || !preg_match('/^[0-9]+$/', trim($_POST['lead_assign'])))
        {
			echo "{error:true, errormsg:'Title and job category are required fields!'}";
		}
        else if ( !preg_match('/^[0-9]+$/', trim($_POST['custid_fk'])) )
        {
			echo "{error:true, errormsg:'Customer ID must be numeric!'}";
		}
        else
        {   
			$proposal_expected_date = strtotime($_POST['proposal_expected_date']);
		    $ewa = '';
			$ins['job_title'] = $_POST['job_title'];
			$ins['custid_fk'] = $_POST['custid_fk'];
			$ins['job_category'] = $_POST['job_category'];
			$ins['lead_source'] = $_POST['lead_source'];
			$ins['lead_assign'] = $_POST['lead_assign'];
			$ins['expect_worth_id'] = $_POST['expect_worth'];
			if($_POST['expect_worth_amount'] == '') {
				$ewa = '0.00';
			}
			else {
			$ewa = $_POST['expect_worth_amount'];
			}  
			$ins['expect_worth_amount'] = $ewa; 
			$ins['belong_to'] = $_POST['job_belong_to'];
			$ins['division'] = $_POST['job_division'];
			$ins['date_created'] = date('Y-m-d H:i:s');
			$ins['date_modified'] = date('Y-m-d H:i:s');
			$ins['job_status'] = 1;
			$ins['lead_indicator'] = $_POST['lead_indicator'];
			$ins['proposal_expected_date'] = date('Y-m-d H:i:s', $proposal_expected_date);
			$ins['created_by'] = $this->userdata['userid'];
			$ins['modified_by'] = $this->userdata['userid'];
			$ins['lead_status'] = 1;
			
			if ($this->db->insert($this->cfg['dbpref'] . 'jobs', $ins))
            {
				$insert_id = $this->db->insert_id();
				
				$invoice_no = (int) $insert_id;
				$invoice_no = str_pad($invoice_no, 5, '0', STR_PAD_LEFT);
				
				//history - lead_status_history
				$lead_hist['jobid'] = $insert_id;
				$lead_hist['dateofchange'] = date('Y-m-d H:i:s');
				$lead_hist['previous_status'] = 1;
				$lead_hist['changed_status'] = 1;
				$lead_hist['lead_status'] = 1;
				$lead_hist['modified_by'] = $this->userdata['userid'];
				$this->db->insert('lead_status_history', $lead_hist);
				
				$this->db->where('jobid', $insert_id);
				$this->db->update($this->cfg['dbpref'] . 'jobs', array('invoice_no' => $invoice_no));
				
				$this->quote_add_item($insert_id, "\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:", 0, '', FALSE);
				
				
				
				$json['error'] = false;
                $json['fancy_insert_id'] = $invoice_no;
                $json['insert_id'] = $insert_id;
                $json['job_title'] = htmlentities($_POST['job_title'], ENT_QUOTES);
                $json['job_category'] = $_POST['job_category'];
                $json['lead_source'] = $_POST['lead_source'];
                $json['lead_assign'] = $_POST['lead_assign'];
				
				$json['expect_worth_id'] = $_POST['expect_worth_id'];
                $json['expect_worth_amount'] = $_POST['expect_worth_amount'];
				echo json_encode($json);
			}
            else
            {
				echo "{error:true, errormsg:'Data insert failed!'}";
			}
			
			$cust_details = $this->db->query('SELECT  j.jobid, c.first_name,c.last_name,c.company
							FROM `crm_jobs` AS j, `crm_customers` AS c
							WHERE c.custid = j.custid_fk
							AND j.jobid ='.$insert_id);
			$customer=$cust_details->result_array();
			$getlead_assign_email = $this->db->query('SELECT j.lead_assign, j.jobid, u.userid, u.email, j.invoice_no
								FROM `crm_jobs` AS j, `crm_users` AS u
								WHERE u.userid = j.lead_assign
								AND j.jobid ='.$insert_id);
			$lassign=$getlead_assign_email->result_array();
			
					//$this->load->plugin('phpmailer');
		$this->load->library('email');
				//$to = array('angelo@at68.com.au');
		//send_email('arunmani4u@gmail.com,sarunkumar@enoahisolution.com', 'VCS Payment Notice', 'Hi', 'admin@enoahisolution.com', 'VCS Admin');
		//@mail('arunmani4u@gmail.com,sarunkumar@enoahisolution.com', 'VCS Payment notification', 'HI', "From:admin@enoahisolution.com");
		$email_body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Lead Notification Message</h3></td>
  </tr>

  <tr>
    <td><table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="96%" align="center" cellspacing="0" cellpadding="4">
  <tr>
	<p>
    <td style="border-right:1px #CCC solid; color:#FFF"" width="73" bgcolor="#4B6FB9"><b>Title</b> </td>
    <td  style="border-right:1px #CCC solid; color:#FFF""width="41" bgcolor="#4B6FB9"><b>Description</b> </td>
	</p>
  </tr>
  <tr>
    <td style="border-right:1px #CCC solid;">Client</td>
    <td style="border-right:1px #CCC solid;">'.$customer[0]['first_name'].' '.$customer[0]['last_name'].'-'.$customer[0]['company'].'</td>
  </tr>
  <tr style="border:1px #CCC solid;">
    <td style="border-right:1px #CCC solid;">URL</td>
    <td style="border-right:1px #CCC solid;">
		<a href="'.$this->config->item('base_url').'welcome/view_quote/'.$insert_id.'">Click here to view Lead</a>
	</td>
  </tr>
</table>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';

				$from=$this->userdata['email'];
				$arrEmails = $this->config->item('crm');
				$arrSetEmails=$arrEmails['director_emails'];
				$mangement_email = $arrEmails['management_emails'];
				$mgmt_mail = implode(',',$mangement_email);
				$admin_mail=implode(',',$arrSetEmails);
				
				$subject='New Lead Creation Notification';
				$this->email->from($from,$user_name);
			
				$this->email->to($mgmt_mail.','. $lassign[0]['email']);
				$this->email->bcc($admin_mail);
				$this->email->subject($subject);
				$this->email->message($email_body);
				$this->email->send(); 
			
			
			
		}
	}
	
    /*
     * adds an item to the quote based on the ajax request
     */
	function ajax_add_item()
	{
        $errors = '';
        if (trim($_POST['hours']) != '' && !is_numeric($_POST['hours']))
        {
			$errors[] = 'Hours can only be numeric values!';
		}
        if (trim($_POST['item_desc']) == '')
        {
            $errors[] = 'You must provide a description!';
        }
        if (trim($_POST['item_price']) != '' && !is_numeric($_POST['item_price']))
        {
			$errors[] = 'Price can only be numeric values!';
		}
        if (!preg_match('/^[0-9]+$/', $_POST['jobid']))
        {
			$errors[] = 'Lead ID must be numeric!';
		}
        
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
			if (!preg_match('/^\n/', $_POST['item_desc']))
			{
				$_POST['item_desc'] = "\n" . $_POST['item_desc'];
			}
			$this->quote_add_item($_POST['jobid'], $_POST['item_desc'], $_POST['item_price'], $_POST['hours']);
			
		}
		
	}
    
    
	/**
	 * Add an item to a quotation (job)
	 * on the system
	 * Accepts direct ajax call as well as calls from other methods
	 */
	function quote_add_item($jobid, $item_desc = '', $item_price = 0, $hours, $ajax = TRUE)
    {
        $ins['item_desc'] = $item_desc;
        $ins['jobid_fk'] = $jobid;
		if(empty($hours)) {
			$ins['hours'] = '0.00';
		} else {
			$ins['hours'] = $hours;
		}
        if(empty($item_price)) {
			$ins['item_price']='0.00';
		} else {
			$ins['item_price'] = $item_price;
		}
        
        if (is_numeric(trim($hours)))
        {
            $ins['hours'] = $hours;
            $ins['item_price'] = $_POST['item_price'] * $hours;
        }
        
        $q = $this->db->query("SELECT MAX(`item_position`) AS `pos`
                                FROM `{$this->cfg['dbpref']}items`
                                WHERE `jobid_fk` = {$ins['jobid_fk']}");
        
        $r = $q->result_array();
        
        $ins['item_position'] = $r[0]['pos']+1;
        
        if ($this->db->insert($this->cfg['dbpref'] . 'items', $ins))
        {
            
            $itemid = $this->db->insert_id();
            
            // modify _saved_items once items are finalised
            if (isset($_POST['keep_item']))
            {
				$keep_additional['item_desc'] = $ins['item_desc'];
				$keep_additional['item_price'] = $ins['item_price'];
                $this->db->insert($this->cfg['dbpref'] . 'additional_items', $keep_additional);
            }
            
            if ($ajax == TRUE)
            {
                $this->ajax_quote_items($ins['jobid_fk'], $itemid);
            }
            else
            {
                return TRUE;
            }
        }
        else
        {
            if ($ajax == TRUE)
            {
                echo "{error:true, errormsg:'Data insert failed!'}";
            }
            else
            {
                return FALSE;
            }
        }
    }
	/**
	* Advanced Search 
	*/
	function advance_filter_search($stage='null', $customer='null', $worth='null', $owner='null', $leadassignee='null', $regionname='null',$countryname='null', $statename='null', $locname='null', $keyword='null') {
		//print_r($_POST);
		
		if (count($_POST)>0) {
			$stage = $_POST['stage'];
			$customer = $_POST['customer'];
			$worth = $_POST['worth'];
			$owner = $_POST['owner'];
			$leadassignee = $_POST['leadassignee'];
			$regionname = $_POST['regionname'];
			$countryname = $_POST['countryname'];
			$statename = $_POST['statename'];
			$locname = $_POST['locname'];
			$keyword = $_POST['keyword'];
			$excel_arr = array();
			foreach ($_POST as $key => $val) {
				$excel_arr[$key] = $val;
			}
			//print_r($excel_arr); 
			$this->session->set_userdata(array("excel_download"=>$excel_arr));
		} else {
			$this->session->unset_userdata(array("excel_download"=>''));
		}
		
		$arrayid2 = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $keyword);	
		//echo '<pre>'; print_r($arrayid2); echo '</pre>';

		$data['filter_results'] = $arrayid2;
		//mychanges
		$data['stage'] = $stage;
		$data['customer'] = $customer;
		$data['worth'] = $worth;
		$data['owner'] = $owner;
		$data['leadassignee'] = $leadassignee;
		$data['regionname'] = $regionname;
		$data['countryname'] = $countryname;
		$data['statename'] = $statename;
		$data['locname'] = $locname;
		$data['keyword'] = $keyword;
				
		//echo '<pre>'; print_r($result); echo '</pre>';
			//echo $this->db->last_query();
		$this->load->view('advance_filter_view', $data);	
	}
	
	function getPjtIdFromdb($pjtid) {
		$this->db->where('pjt_id',$pjtid);
		$query = $this->db->get('crm_jobs')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
	}
	
	function getPjtValFromdb($pjtval) {
		$this->db->where('actual_worth_amount', $pjtval);
		$query = $this->db->get('crm_jobs')->num_rows();
		if($query == 0 ) echo 'userOk';
		else echo 'userNo';
	}
	
	
	public function excelExport() {
		
		$stage='null';
		$customer='null';
		$worth='null';
		$owner='null';
		$leadassignee='null';
		$regionname='null';
		$countryname='null';
		$statename='null';
		$locname='null';
		$keyword='null';
		
		$exporttoexcel = $this->session->userdata['excel_download'];

		if (count($exporttoexcel)>0) {
			//foreach ($exporttoexcel as $key => $val) {
				//$key = $val;
			//}
			$stage = $exporttoexcel['stage'];
			$customer=$exporttoexcel['customer'];
			$worth=$exporttoexcel['worth'];
			$owner=$exporttoexcel['owner'];
			$leadassignee=$exporttoexcel['leadassignee'];
			$regionname=$exporttoexcel['regionname'];
			$countryname=$exporttoexcel['countryname'];
			$statename=$exporttoexcel['statename'];
			$locname=$exporttoexcel['locname'];
			$keyword=$exporttoexcel['keyword'];
		}

		$arrayid = $this->welcome_model->get_filter_results($stage, $customer, $worth, $owner, $leadassignee, $regionname, $countryname, $statename, $locname, $keyword);
		//print_r($arrayid2); exit;
		
		//load our new PHPExcel library
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('DashBoard');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Lead No.');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Lead Title');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Customer');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Region');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Lead Owner');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Lead Assigned To');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Currency Type');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Expected Worth');
		$this->excel->getActiveSheet()->setCellValue('I1', 'Lead Creation Date');
		$this->excel->getActiveSheet()->setCellValue('J1', 'Updated On');
		$this->excel->getActiveSheet()->setCellValue('K1', 'Updated By');
		$this->excel->getActiveSheet()->setCellValue('L1', 'Lead Stage');
		$this->excel->getActiveSheet()->setCellValue('M1', 'Expected Proposal Date');
		$this->excel->getActiveSheet()->setCellValue('N1', 'Proposal Sent on');
		$this->excel->getActiveSheet()->setCellValue('O1', 'Variance');
		$this->excel->getActiveSheet()->setCellValue('P1', 'Lead Indicator');
		$this->excel->getActiveSheet()->setCellValue('Q1', 'Status');
		
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(10);
		$i=2;
		foreach($arrayid as $excelarr) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $excelarr['invoice_no']);
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $excelarr['job_title']);
			$this->excel->getActiveSheet()->setCellValue('C'.$i, $excelarr['first_name'].' '.$excelarr['last_name'].' - '.$excelarr['company']);
			$this->excel->getActiveSheet()->setCellValue('D'.$i, $excelarr['region_name']);
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $excelarr['ubfn'].' '.$excelarr['ubln']);
			$this->excel->getActiveSheet()->setCellValue('F'.$i, $excelarr['ufname'].' '.$excelarr['ulname']);
			$this->excel->getActiveSheet()->setCellValue('G'.$i, $excelarr['expect_worth_name']);
			$this->excel->getActiveSheet()->setCellValue('H'.$i, $excelarr['expect_worth_amount']);
			//display only date
				$this->excel->getActiveSheet()->setCellValue('I'.$i, date('d-m-Y', strtotime($excelarr['date_created'])));
				$this->excel->getActiveSheet()->setCellValue('J'.$i, date('d-m-Y', strtotime($excelarr['date_modified'])));
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $excelarr['usfname'].' '.$excelarr['uslname']);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $excelarr['lead_stage_name']);
				if($excelarr['proposal_expected_date'] != null) {
					$this->excel->getActiveSheet()->setCellValue('M'.$i, date('d-m-Y', strtotime($excelarr['proposal_expected_date'])));
				}		
				if($excelarr['proposal_sent_date'] != null) {				
					$this->excel->getActiveSheet()->setCellValue('N'.$i, date('d-m-Y', strtotime($excelarr['proposal_sent_date'])));
				}
			
			$date1 = $excelarr['proposal_sent_date'];
			$date2 = $excelarr['proposal_expected_date'];
			if($date1 != '' && $date2 != '')
			{
			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			} else {
			$days = '-';
			}
			$this->excel->getActiveSheet()->setCellValue('O'.$i, $days);
			
			$this->excel->getActiveSheet()->setCellValue('P'.$i, $excelarr['lead_indicator']);
			
			if($excelarr['lead_status'] == 1)
			$status = 'Active';
			else if($excelarr['lead_status'] == 2)
			$status = 'On Hold';
			else 
			$status = 'Dropped';
			
			$this->excel->getActiveSheet()->setCellValue('Q'.$i, $status);

			$i++;
			}
		
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);
			//merge cell A1 until D1
			//$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$filename='Lead Dashboard.xls'   ; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						 
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
}		
	
	
	/**
	 * Edits an existing item on a quote (job)
	 */
	function ajax_edit_item()
	{
        $errors = '';
        if (trim($_POST['item_desc']) == '')
        {
		$errors[] = 'You must provide a description!';
        }
        if (trim($_POST['item_price']) != '' && !is_numeric($_POST['item_price']))
        {
		$errors[] = 'Price can only be numeric values!';
	}
        if (!preg_match('/^[0-9]+$/', $_POST['itemid']))
        {
		$errors[] = 'item ID must be numeric!';
	}
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
		$ins['item_desc'] = $_POST['item_desc'];
		$ins['item_price'] = $_POST['item_price'];
		$this->db->where('itemid', $_POST['itemid']);
		if ($this->db->update($this->cfg['dbpref'] . 'items', $ins))
		{
			echo "{error:false}";
		}
		else
		{
			echo "{error:true, errormsg:'Update failed!'}";
		}
        }
    }
    /*
     * deletes the given item from a job
     * @return echo json string
     */
    function ajax_delete_item()
    {
        $errors = '';
        if (!isset($_POST['itemid']) || !preg_match('/^[0-9]+$/', $_POST['itemid']))
        {
            $errors[] = 'A valid item ID is not supplied';
        }
        if (is_array($errors))
        {
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
        }
        else
        {
            $this->db->where('itemid', $_POST['itemid']);
            $this->db->select('jobid_fk');
            $q = $this->db->get($this->cfg['dbpref'] . 'items');
            if ($q->num_rows() > 0)
            {
                $jobid = $q->result_array();
                $this->db->where('itemid', $_POST['itemid']);
                if ( $this->db->delete($this->cfg['dbpref'] . 'items') )
                {
                    $this->ajax_quote_items($jobid[0]['jobid_fk']);
                }
                else
                {
                    $json['error'] = true;
                    $json['errormsg'] = 'Database error! Item not deleted.';
                    echo json_encode($json);
                }
            }
            else
            {
                $json['error'] = true;
                $json['errormsg'] = 'Specified item ID does not exist';
                echo json_encode($json);
            }
        }
    }
    /*
     * provides the list of items
     * that belong to a given job
     * @param jobid
     * @param itemid (latest intsert)
     * @return echo json string
     */
    function ajax_quote_items($jobid = 0, $itemid = 0, $return = false)
    {
	$this->load->helper('text');
	$this->load->helper('fix_text');
        
        $this->db->where('jobid_fk', $jobid);
        $this->db->order_by('item_position', 'asc');
        $q = $this->db->get($this->cfg['dbpref'] . 'items');

        #define the users who can see the prices
		//$price_allowed = ( in_array($this->userdata['level'], array(0, 1, 2, 4, 5)) ) ? TRUE : FALSE;
        
        if ($q->num_rows() > 0)
        {
            $html = '';
            $sale_amount = 0;
            foreach ($q->result_array() as $row)
            {
				//if ($price_allowed == FALSE)
				//{
					//$row['item_price'] = 0;
				//}
				
                if (is_numeric($row['item_price']) && $row['item_price'] != 0)
                {
                    $sale_amount += $row['item_price'];
				$row['item_price'] = '$' . number_format($row['item_price'], 2, '.', ',');
				$row['item_price'] = preg_replace('/^\$\-/', '-$', $row['item_price']);
			}
                else
                {
                    $row['item_price'] = '';
                }
				
                if ($row['hours'] > 0)
                {
			$row['hours'] = 'Hours : ' . $row['hours'];
		}
                else
                {
                    $row['hours'] = '';
                }
				if(!empty($row['item_price'])) {
                $html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" width="85%">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td><td width="14%" class="item-price width100px" align="right" valign="bottom">' . $row['item_price'] . '</td></tr></table></li>';
				} else {
				$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item" width="100%"><tr><td class="item-desc" colspan="2">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td></tr></table></li>';
				}
                
            }
			
            
            $json['sale_amount'] = '$' . number_format($sale_amount, 2, '.', ',');
            $json['gst_amount'] = ($sale_amount > 0) ? '$' . number_format($sale_amount/10, 2, '.', ',') : '$0.00';
			
            $json['total_inc_gst'] = '$' . number_format($sale_amount*1.1, 2, '.', ',');
            $json['numeric_total_inc_gst'] = $sale_amount*1.1;
			
            $json['error'] = false;
            $json['html'] = $html;
			
			$json['deposits'] = $json['deposit_balance'] = '$0.00';
			$deposit_total = 0;
			
			$this->db->where('jobid_fk', $jobid);
			$deposits = $this->db->get($this->cfg['dbpref'] . 'deposits');
			//if ($deposits->num_rows() > 0 && $price_allowed)
			if ($deposits->num_rows() > 0)
			{
				$deposits_data = $deposits->result_array();
				foreach ($deposits_data as $dd)
				{
					$deposit_total += $dd['amount'];
				}
				
				$json['deposits'] = '$' . number_format($deposit_total, 2, '.', ',');
			}
			
			$json['deposit_balance'] = '$' . number_format($json['numeric_total_inc_gst'] - $deposit_total, 2, '.', ',');
			$json['deposit_balance'] = preg_replace('/^\$\-/', '-$', $json['deposit_balance']);
            
        }
        else
        {
            
            $json['sale_amount'] = '0.00';
            $json['gst_amount'] = '0.00';
            $json['total_inc_gst'] = '0.00';
            $json['error'] = false;
            $json['html'] = '';
            
        }
        
        $json['itemid'] = $itemid;
		
        if ($return)
        {
            return json_encode($json);
        }
        else
        {
            echo json_encode($json);
        }
        
    }
    
    /*
     * saves the new positions items
     * for a given job
     */
    function ajax_save_item_order()
    {
        
        $errors = '';
        if (!isset($_POST['qi']) || !is_array($_POST['qi']))
        {
            $errors[] = 'Incorrect order format!';
        }
        
        if (is_array($errors))
        {
            
            $json['error'] = true;
            $json['errormsg'] = implode("\n", $errors);
            echo json_encode($json);
            
        }
        else
        {
            
            $when = '';
            foreach ($_POST['qi'] as $k => $v)
            {
                $when .= "WHEN {$v} THEN {$k} \n";
            }
            
            $sql = "UPDATE {$this->cfg['dbpref']}items SET `item_position` = CASE `itemid`
                    {$when}
                    ELSE `item_position` END";
            
            if ($this->db->query($sql))
            {
                $json['error'] = false;
                echo json_encode($json);
            }
            else
            {
                $json['error'] = true;
                $json['errormsg'] = 'Database error occured!';
                echo json_encode($json);
            }
            
            
            
        }
        
    }
	
    /*
     * provides details of the customer
     * for a given id
     * @param custid
     * @return string (json formatted)
     */
	function ajax_customer_details($custid)
	{
        $this->load->model('customer_model');
		$result = $this->customer_model->get_customer($custid);
		if (is_array($result) && count($result) > 0)
        {
            echo json_encode($result[0]);
		}
    }
	
	/*
     * provide the list of users
     * for a region id, country id, state id, location id
     * @param regId, cntryId, stId, locId
     * @return string (json formatted)
     */
	function user_level_details($regId, $cntryId, $stId, $locId)
	{
        $this->load->model('user_model');
		$result = $this->user_model->get_userslist($regId, $cntryId, $stId, $locId);
		
		if (is_array($result) && count($result) > 0)
        {
            echo json_encode($result);
		}
    }
	
	public function ajax_set_contractor_for_job()
	{
		//print_r($_POST);exit;
		
		if (isset($_POST['jobid']) && !empty($_POST['contractors']) && $this->welcome_model->get_job(array('jobid' => $_POST['jobid'])))
		{
			//$contractors = array();
			//echo "in maini";
			$contractors = explode(',', $_POST['contractors']);	
			//print_r($contractors); echo "test";exit;
			$project_member = array();
			$result = array();
			$project_member = $this->db->query("SELECT userid_fk FROM crm_contract_jobs WHERE jobid_fk = " .$_POST['jobid']); 
			foreach ($project_member->result() as $project_mem)
			{
				$result[] = $project_mem->userid_fk;
			}
			$new_project_member_insert = array_diff($contractors, $result);
			//print_r($new_project_member_insert);
			$user_id_for_mail = implode ("," , $new_project_member_insert);
			$new_project_member_delete = array_diff($result, $contractors);
			$new_project_member_delete = array_values($new_project_member_delete);	
			if(!empty($new_project_member_insert))
			{
			    //echo "frist insert";
				foreach ($new_project_member_insert as $con) 
				{
					if (preg_match('/^[0-9]+$/', $con))
					{
						$this->db->insert('crm_contract_jobs', array('jobid_fk' => $_POST['jobid'], 'userid_fk' => $con));
					}
				}
				$user_id_for_mail = implode ("," , $new_project_member_insert);
				$query_for_mail = $this->db->query("SELECT email,first_name FROM crm_users u WHERE u.userid IN(".$user_id_for_mail.")");
				//echo $this->db->last_query();
				foreach ($query_for_mail->result() as $mail_id)
				{			
					    $mail = $mail_id->email;
						$first_name = $mail_id->first_name;
						$log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "insert");
						
				}
			}
			if(!empty($new_project_member_delete))
			{
				//echo "frist delelte";
				$user_id_for_mail = implode("," , $new_project_member_delete);	
				//echo "Delete : ",$user_id_for_mail;				
				$query_for_mail = $this->db->query("SELECT email, first_name FROM crm_users u WHERE u.userid IN(".$user_id_for_mail.")");
				//echo $this->db->last_query();
				foreach ($query_for_mail->result() as $mail_id)
				{
					 $mail = $mail_id->email;
					 $first_name = $mail_id->first_name;
					 $log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "remove" );
				}
				//$pm = explode(",", $_POST['project-mem']);
				$this->db->where('jobid_fk', $_POST['jobid']);
				$this->db->where_in('userid_fk', $new_project_member_delete);
			    $this->db->delete('crm_contract_jobs');
				//echo $this->db->last_query();
			}
			echo '{status: "OK"}';
		}
		else if(empty($_POST['contractors']))
		{
		    $members_id = $_POST['project-mem'];
			$members = explode(',', $_POST['project-mem']);				
			$query_for_mail = $this->db->query("SELECT email, first_name FROM crm_users u WHERE u.userid IN(".$members_id.")");
			//echo $this->db->last_query();
			foreach ($query_for_mail->result() as $mail_id)
			{
				 $mail = $mail_id->email;
				 $first_name = $mail_id->first_name;
				 $log_email_content1 = $this->get_user_mail($mail , $first_name, $type = "remove");
			}
			$this->db->where_in('jobid_fk', $_POST['jobid']);
			$this->db->delete('crm_contract_jobs');
			//echo $this->db->last_query();
		}
		else
		{
			echo '{error: "Invalid job or userid supplied!"}';
		}
	}
	
	public function get_user_mail($mail, $first_name, $mail_type) 
	{	  
	    $project_title = $this->db->query("SELECT job_title FROM crm_jobs j WHERE j.jobid = ".$_POST['jobid']);
		$test = $project_title->result(); 
		$project_name = $test[0]->job_title;
		$log_email_content = '';
		$log_email_content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Template</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
</head>

<body>
<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">
<tr><td bgcolor="#FFFFFF">
<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td style="padding:15px; border-bottom:2px #5a595e solid;">
		<img src="'.$this->config->item('base_url').'assets/img/esmart_logo.jpg" />
	</td>
  </tr>
  <tr>
    <td style="padding:15px 5px 0px 15px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td>
  </tr>

  <tr>
    <td>
	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">
    <p style="background: none repeat scroll 0 0 #4B6FB9;
    border-bottom: 1px solid #CCCCCC;
    color: #FFFFFF;
    margin: 0;
    padding: 4px;">
        <span>Hi </span>&nbsp;'.$first_name.',</p>
    <p style="padding: 4px;"><br /><br />';
	if($mail_type == "insert")
	{
		$log_email_content .= 'You are included as one of the project team members in the project - '.$project_name.'<br />';
	}
	else 
	{
		$log_email_content .= 'You are moved from this project - '.$project_name.'<br />';
	}
	$log_email_content .='<br /><br />
		Regards<br />
		<br />
		Webmaster
    </p>
</div>
</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>
  </tr>
</table>
</td>
</tr>
</table>
</body>
</html>';			
		$successful = '';
		if($mail_type == "insert")
		{
			$log_subject = 'New Project Assignment Notification';
		}
		else
		{
			$log_subject = 'Project Removal Notification';
		}
		
		//$set = $this->get_user_mail(); 
		$send_to = $mail;
		$this->email->from('webmaster@enoahisolution.com','Webmaster');
		$this->email->to($send_to);
		$this->email->subject($log_subject);
		$this->email->message($log_email_content);
		//$this->email->attach($pdf_file_attach);
		if($this->email->send()){
			$successful .= 'This log has been emailed to:<br />'.$send_to;
		}
	 
	    //$log_email_content = 'HEllloooooo'; exit;
		
			
	}
	
	// TO BE MOVED TO A CRON JOB
	public function create_subscription_invoices()
	{
		error_reporting(E_ALL);
		$this->load->model('subscriptions_model');
		
		$customers = $this->subscriptions_model->get_all_customers_with_subscriptions();
		
		foreach($customers as $k=>$customer)
		{
			$this->create_subscription_invoice_for_customer($customer['custid'], $customer);
		}
	}
	
	public function create_subscription_invoice_for_customer($cust_id, $customer)
	{
		$this->load->model('subscriptions_model');
		
		if ($cust_id > 0) {
			$items = $this->subscriptions_model->get_items_for_customer($cust_id);
			$discounts = array();
			foreach ($items as $k=>$data)
			{
				$discounts = $this->subscriptions_model->get_discounts_for_item($data['recurringitemid']);
				$items[$k]['discounts'] = $discounts;
			}
			
			if (!empty($items))
			{
				$invoice_id = $this->subscriptions_model->create_invoice_from_items($cust_id, $items, $customer);
			}
			
		}
	}
	public function ajax_hosting_load($jobid=false){
		$query = $this->db->query("SELECT hostingid_fk FROM crm_hosting_job WHERE jobid_fk='{$jobid}'");
		$t=array();
		foreach($query->result_array() as $v) $t[]=$v['hostingid_fk'];
		$sql="SELECT * FROM crm_hosting as H, crm_jobs as J WHERE J.custid_fk=H.custid_fk && J.jobid={$jobid}";
		$query = $this->db->query($sql);
		
		$temp='';
		foreach($query->result_array() as $val){
			if(in_array($val['hostingid'],$t)) $s=' selected="selected"'; else $s=' ';
			$temp.= '<option value="'.$val['hostingid'].'" '.$s.'>'.$val['domain_name'].'</option>';
		}
		return $temp;
	}
	function package($tab=''){
		switch($tab){
			case 'quotation':
				$arr=array(0,1,2,3,15,21,22);break;
			case 'invoice':
				$arr=array(4,5,6,7,25);break;
			case 'subscription':
				$arr=array(30,31,32);break;
			case 'production';
				$arr=array(4,5,15);break;
			default:
				$arr=array(0,1,2,3,4,15,21,22);break;
		}
		$arr=implode(',',$arr);
		$data['page_heading'] = $tab;
		$search='';
		if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 && $_POST['keyword']!='Invoice No, Job Title, Name or Company') {
			$search.=" AND (J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
		}
		$sql = "SELECT *, SUM(`crm_items`.`item_price`) AS `project_cost`,
					(SELECT SUM(`amount`) FROM `crm_deposits` WHERE `jobid_fk` = `jobid` GROUP BY jobid) AS `deposits`
                FROM `crm_items`, `crm_customers` AS C, `crm_jobs` AS J, crm_hosting as H
				WHERE J.job_status IN ({$arr}) AND C.`custid` = J.`custid_fk` AND `jobid` = `crm_items`.`jobid_fk` && H.custid_fk=C.custid
					{$search}
                GROUP BY `jobid`
				ORDER BY `belong_to`, `date_created`";
		
		$rows = $this->db->query($sql);
		$records=$data['records'] = $rows->result_array();
		$temp[]=0;
		foreach($records as $val) { $temp[]=$val['custid'];}
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `crm_hosting_package` as P, crm_hosting as H WHERE P.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$hosting=$rows->result_array();
		
		$rows = $this->db->query("SELECT * FROM `crm_hosting_job` as J, crm_hosting as H WHERE J.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})");
		$jobs=$rows->result_array();
		
		$j_temp=array();
		foreach($jobs as $key=>$val){
			$v=$val['jobid_fk'];
			$j_temp[$v][]=$val['hostingid_fk'];
		}
		$data['JOBS']=$j_temp;
		
		$rows = $this->db->query("SELECT * FROM `crm_package`  WHERE  status='active'");
		$data['packages']=$rows->result_array();
		$p_temp=array();
		foreach($hosting as $key=>$val){
				$v=$val['hostingid'];$q=$val['packageid_fk'];
				$p_temp[$v][$q]=$val['packageid_fk'];
		}
		//$data['hosting']=$p_temp;
		foreach($records as $key=>$val){
			$v=$val['jobid'];
			if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) {
				if(!empty($j_temp[$v])) unset($data['records'][$key]);
			}
			else {
				if(empty($j_temp[$v])) { unset($data['records'][$key]);continue;}
				if(isset($_POST['pack_name']) && $_POST['pack_name']>0) {
					$i=0;
					foreach($j_temp[$v] as $k1=>$v1){
						if(empty($p_temp[$v1])) continue;
						if(array_search($_POST['pack_name'],$p_temp[$v1])>0) $i++;
					}				
					if($i==0) { unset($data['records'][$key]);continue;}
				}
			}
		}
		//echo '<pre>';print_r($p_temp);print_r($j_temp);echo '</pre>';
		$this->load->view('quotation_view',$data);
	}
	
	function generate_invoice(){
		if(isset($_POST['auto_generate']) && $_POST['auto_generate']=='auto_generate'){
			$sql1="SELECT *, DATE_SUB(DATE_ADD( NOW() , INTERVAL P.duration MONTH ) ,INTERVAL 1 DAY) AS expiry FROM crm_package P, crm_hosting as H
					RIGHT JOIN crm_hosting_package HP ON HP.hostingid_fk=H.hostingid
					WHERE P.package_id=HP.packageid_fk && P.status='active' && HP.due_date<NOW()
					";
			$rows1=$this->db->query($sql1);
			$sql = "SELECT * FROM `crm_customers` AS C, `crm_jobs` AS J, crm_hosting as H
					RIGHT JOIN crm_hosting_job HJ ON HJ.hostingid_fk=H.hostingid
					WHERE J.job_status IN (4,5,6,7,25) AND C.`custid` = J.`custid_fk`  && H.custid_fk=C.custid  
					";
			$rows = $this->db->query($sql);
			$h=array();$h1=array();
			foreach($rows->result_array() as $val){
				if(in_array($val['hostingid'],$h)) continue;
				$h[]=$val['hostingid'];
			}
			foreach($rows1->result_array() as $val){
				if(in_array($val['hostingid'],$h1)) continue;
				$h1[]=$val['hostingid'];
			}
			$h2=array_diff($h1,$h);
			if(sizeof($h2)>0){
				$q="INSERT INTO `crm_jobs` (`jobid`, `job_title`, `job_desc`, `job_category`, `invoice_no`, `custid_fk`, `date_quoted`, `date_invoiced`, `job_status`, `complete_status`, `assigned_to`, `date_start`, `date_due`, `date_created`, `date_modified`, `created_by`, `account_manager`, `in_csr`, `belong_to`, `division`, `payment_terms`, `invoice_downloaded`, `log_view_status`, `invoice_status`) VALUES ";
				$i=1;$s2=array();
				$q1="INSERT INTO `crm_hosting_job` (`jobid_fk`, `hostingid_fk`) VALUES ";
				$q2="INSERT INTO `crm_items` (`itemid` ,`jobid_fk` ,`item_position` ,`item_desc` ,`item_price` ,`hours` ,`ledger_code`) VALUES ";
				$tq=$this->db->query("SELECT (SELECT MAX(jobid) FROM crm_jobs) as maxid,(SELECT MAX(invoice_no) FROM crm_jobs) as maxinv");
				$tqr=$tq->result_array();
				$t=array();
				foreach($rows1->result_array() as $val){
					if(!in_array($val['hostingid'],$h2) || in_array($val['hostingid'],$t)) continue;
					$jobid=$tqr[0]['maxid']+$i;$t[]=$val['hostingid'];
					$invoice_no=(float)$tqr[0]['maxinv']+$i;
					$s[]='('.$jobid.', "Website Hosting for '.$val['domain_name'].'", NULL, 0, "00'.$invoice_no.'", '.$val['custid_fk'].', NULL, "'.date('Y-m-d H:i:s').'", 4, NULL, NULL, "'.date('Y-m-d H:i:s').'", "'.$val['expiry'].'", "'.date('Y-m-d H:i:s').'", NULL, "-1", NULL, 0, "VT", "VTD", 0, 0, NULL, 0)';
					$s1[]='('.$jobid.','.$val['hostingid'].')';
					$j=1;
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Thank you for entrusting eNoah  iSolution with your web technology requirements.
\nPlease see below an itemised breakdown of our service offering to you:",0, NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Domain Name : '.$val['domain_name'].'",0 , NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Package Name : '.$val['package_name'].'",'.$val['package_price'].' , NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"Period of Package : '.date('d-M-y').' to '.date('d-M-y',strtotime($val['expiry'])).'", 0, NULL, 0)';
					$s2[]=' (NULL, '.$jobid.','.$j++.',"'.mysql_escape_string($val['details']).'",0 , NULL, 0)';
					$i++;
				}
			$q.=implode(',',$s);
			$q1.=implode(',',$s1);
			$q2.=implode(',',$s2);
			$this->db->query($q);
			$this->db->query($q1);
			$this->db->query($q2);
			}
			$j=array();
			foreach($rows->result_array() as $val){
				if((strtotime($val['date_due'])-strtotime(date('Y-m-d H:i:s')))>0) continue;
				if($val['invoice_status']!=0) continue;
				if(strpos(strtolower($val['job_title']), 'hosting') == false) continue;
				$j[$val['jobid']]=$val['hostingid'];
				
			}
			//print_r($j);exit;
			$h=array();
			foreach($rows1->result_array() as $val){
				$h[$val['hostingid_fk']]=$val['hostingid_fk'];
			}
			if(sizeof($j)>0){
				$JOBS=array();
				foreach($j as $k=>$v){
					if(in_array($v,$h)) $JOBS[]=$k;	
				}
			}
		}
		if(isset($_POST['generate']) && $_POST['generate']=='generate'){
			if(isset($_POST['jobs']))	$JOBS=$_POST['jobs'];
		}
		if(!empty($JOBS) && sizeof($JOBS)>0) {
			$jobs=implode(',',$JOBS);
			
			$r=$this->db->query("SELECT * FROM  crm_hosting_job H LEFT JOIN crm_jobs J ON J.jobid=H.jobid_fk WHERE J.jobid IN ({$jobs}) && H.jobid_fk IN ({$jobs});");
			//echo "SELECT * FROM  crm_hosting_job H LEFT JOIN crm_jobs J ON J.jobid=H.jobid_fk WHERE J.jobid IN ({$jobs}) && H.jobid_fk IN ({$jobs});";exit;
			$temp_arr=array();
			if(sizeof($r->result_array())>0)
			foreach($r->result_array() as $v1){
				$v=$v1['jobid_fk'];
				$temp_arr[$v][]=$v1['hostingid_fk'];
			}
			
			$dumm_job=array();
			foreach($r->result_array() as $v){
				if(in_array($v['jobid'],$dumm_job)) continue;
				$dumm_job[]=$v['jobid'];
				$tq=$this->db->query("SELECT (SELECT MAX(jobid) FROM crm_jobs) as maxid,(SELECT MAX(invoice_no) FROM crm_jobs) as maxinv");
				$tqr=$tq->result_array();
				$jobid=$v['jobid'];
				
				$ins['invoice_no']='00'.((float)$tqr[0]['maxinv']+1);
				if(!empty($temp_arr[$jobid])){
					$tem=implode(',',$temp_arr[$jobid]);
					$tq1=$this->db->query("SELECT * FROM crm_package P LEFT JOIN crm_hosting_package HP ON HP.packageid_fk=P.package_id LEFT JOIN crm_hosting H ON H.hostingid=HP.hostingid_fk  WHERE HP.hostingid_fk IN ({$tem}) && P.status='active' ");
					$tqr1=$tq1->result_array();
				}
				$domain=array();
				if(sizeof($tqr1)>0)
				foreach($tqr1 as $val){
				if(in_array($val['hostingid'],$domain)) continue;
				$domain[]=$val['hostingid'];
				$Hosting=$val['domain_name'];
				
				$ins['jobid']=++$tqr[0]['maxid'];
				$ins['job_title']=$v['job_title'];
				$ins['job_status']=4;
				$ins['job_category']=$v['job_category'];
				
				$ins['custid_fk']=$v['custid_fk'];
				$ins['belong_to']=$v['belong_to'];
				$ins['division']=$v['division'];
				$ins['date_invoiced']=date('Y-m-d H:i:s');
				$ins['date_start']=date('Y-m-d H:i:s');
				$ins['date_due']=date('Y-m-d H:i:s',(time()+($tqr1[0]['duration']*30*24*60*60)-86400));
				$ins['date_created']=date('Y-m-d H:i:s');
				$ins['created_by']=-1;
				$this->db->insert('crm_jobs', $ins) ;
				
				if(!empty($temp_arr[$jobid])){
				$query='INSERT INTO crm_hosting_job (jobid_fk, hostingid_fk) VALUES ';
				$s=array();
				$this->db->delete("crm_hosting_job", array('jobid_fk' => $ins['jobid']));
				$s[]=' ('.$ins['jobid'].','.$val['hostingid'].')';
			
				$s=implode(',',$s);
				$query.=$s;
				if(strlen($query)>0) $this->db->query($query);
				
				$i=1;$t=array();$s1=array();
				$q1="INSERT INTO `crm_items` (`itemid` ,`jobid_fk` ,`item_position` ,`item_desc` ,`item_price` ,`hours` ,`ledger_code`) VALUES ";
				$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Thank you for entrusting eNoah  iSolution with your web technology requirements.
\nPlease see below an itemised breakdown of our service offering to you:",0, NULL, 0)';
				foreach($tqr1 as $tk){
					if(in_array($tk['package_id'],$t)) continue;
					$t[]=$tk['package_id'];
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Domain Name : '.$Hosting.'",0 , NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Package Name : '.$tk['package_name'].'",'.$tk['package_price'].' , NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"Period of Package : '.date('d-M-y').' to '.date('d-M-y',(time()+($tk['duration']*30*24*60*60)-86400)).'",0, NULL, 0)';
					$s1[]=' (NULL, '.$ins['jobid'].','.$i++.',"'.mysql_escape_string($tk['details']).'",0 , NULL, 0)';
				}
				$q1.=implode(',',$s1);
				$this->db->query($q1);
				}
				}
			}
			$this->db->query("UPDATE crm_jobs SET invoice_status=1, date_due=NOW() WHERE jobid IN ({$jobs})");
		}
		if(isset($_POST['send']) && $_POST['send']=='send'){
			if(isset($_POST['jobs'])) {
				foreach($_POST['jobs'] as $val){
					$this->db->where('jobid', $val);
					$job_details = $this->db->get($this->cfg['dbpref'] . 'jobs');
					
					if ($job_details->num_rows() > 0) 
					{
						$job = $job_details->result_array();
						$this->db->where('custid', $job[0]['custid_fk']);
						$client_details = $this->db->get($this->cfg['dbpref'] . 'customers');
						$client = $client_details->result_array();
				
						$this->load->plugin('phpmailer');
						//$send_to=$client[0]['email_1'];
						$send_to='jranand@enoahisolution.com';
						$pdf_file_attach = array();
						
						$log_subject = "eNoah log - {$job[0]['job_title']} [ref#{$job[0]['jobid']}] {$client[0]['first_name']} {$client[0]['last_name']} {$client[0]['company']}";
						/*$log_email_content = "--visiontechdigital.com\n\n" .
												"\n\n--\n" . $this->userdata['signature'];*/
						$log_email_content = "--enoahisolution.com\n\n" .
												"\n\n--\n" . $this->userdata['signature'];
						$temp_file_prefix = 'invoice';
						$temp_file_name = $temp_file_prefix . '-' . $job[0]['invoice_no'];
						$temp_file_path = microtime(true) . $temp_file_name;
						$full_file_path = dirname(FCPATH) . '/vps_data/' . $temp_file_path . '.pdf';
						$content_policy = TRUE;
						if (isset($_POST['ignore_content_policy']))	{
							$content_policy = FALSE;
						}
						$this->view_plain_quote($job[0]['jobid'], TRUE, FALSE, FALSE, $temp_file_path, '', false);
						if (file_exists($full_file_path)) {
							$pdf_file_attach[] = array($full_file_path, $temp_file_name . '.pdf');
						}
						$successful='';
					    $this->email->from('jranand@enoahisolution.com','Anand');
						$this->email->to($send_to);
						$this->email->subject($log_subject);
						$this->email->message($log_email_content);
						$this->email->attach($pdf_file_attach);
						if($this->email->send()){
								$successful = 'This log has been emailed to:<br />'.$send_to;

						}
						/*if (send_email($send_to, $log_subject, $log_email_content,'', '', '', $pdf_file_attach)) {
							$successful = 'This log has been emailed to:<br />'.$send_to;
						}
						*/
						
						$ins['jobid_fk'] = $val;
						$ins['userid_fk'] = '';
						$ins['custid_fk'] = $client[0]['custid'];
						$ins['invoice_no'] = $job[0]['invoice_no'];
						$ins['date_created'] = date('Y-m-d H:i:s');
						$ins['log_detail'] =  $successful;
						$stick_class = '';
						if (isset($_POST['log_stickie'])){
							$ins['stickie'] = 1;
							$stick_class = ' stickie';
						}
						$this->db->insert($this->cfg['dbpref'] . '_invoice_logs', $ins);
					}
				}
			}
		}
		redirect('invoice/billing/');
	}
	function billing($tab=''){
			$arr=array(4,5,6,7,25);
			$arr=implode(',',$arr);
		$data['page_heading'] = 'Invoice Billing';
		$search='';
		$criteria='';
		if(isset($_POST['pack_name']) && $_POST['pack_name']==-2) {
			$arr=4;
		}
		else if(isset($_POST['pack_name']) && $_POST['pack_name']==-3) {
			$criteria='  AND  J.invoice_status=0';
		}
		if(isset($_POST['keyword']) && strlen($_POST['keyword'])>0 && $_POST['keyword']!='Invoice No, Job Title, Name or Company') {
			$search.=" AND (J.invoice_no='{$_POST['keyword']}' || J.job_title LIKE '%{$_POST['keyword']}%' || C.email_1 LIKE '%{$_POST['keyword']}%'|| C.company LIKE '%{$_POST['keyword']}%' || C.first_name LIKE '%{$_POST['keyword']}%' || C.last_name='{$_POST['keyword']}' )";
		}
		$sql = "SELECT * FROM `crm_customers` AS C, `crm_jobs` AS J, crm_hosting as H
				WHERE J.job_status IN ({$arr}) AND C.`custid` = J.`custid_fk`  && H.custid_fk=C.custid
					{$search} {$criteria}
                GROUP BY `jobid`
				ORDER BY jobid DESC,`belong_to`, `date_created`";
		$rows = $this->db->query($sql);
		$records=$data['records'] = $rows->result_array();
		$temp[]=0;
		foreach($records as $val) { $temp[]=$val['custid'];}
		$temp=implode(',',$temp);
		$sql="SELECT * FROM `crm_hosting_package` as P, crm_hosting as H WHERE P.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})";
		$rows = $this->db->query($sql);
		$hosting=$rows->result_array();
		$rows = $this->db->query("SELECT * FROM `crm_hosting_job` as J, crm_hosting as H WHERE J.hostingid_fk=H.hostingid && H.custid_fk IN ({$temp})");
		$jobs=$rows->result_array();
		$j_temp=array();
		foreach($jobs as $key=>$val){
			$v=$val['jobid_fk'];
			$j_temp[$v][]=$val['hostingid_fk'];
		}
		$data['JOBS']=$j_temp;
		$rows = $this->db->query("SELECT * FROM `crm_package`  WHERE  status='active'");
		$data['packages']=$rows->result_array();
		$p_temp=array();
		foreach($hosting as $key=>$val){
				$v=$val['hostingid'];$q=$val['packageid_fk'];
				$p_temp[$v][$q]=$q;
		}
		$data['hosting']=$p_temp;
		foreach($records as $key=>$val){
			$v=$val['jobid'];
			if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) {
				if(!empty($j_temp[$v])) unset($data['records'][$key]);
			}
			else {
				if(empty($j_temp[$v])) { unset($data['records'][$key]);continue;}
				if(isset($_POST['pack_name']) && $_POST['pack_name']>0) {
					$i=0;
					foreach($j_temp[$v] as $k1=>$v1){
						if(empty($p_temp[$v1])) continue;
						if(array_search($_POST['pack_name'],$p_temp[$v1])>0) $i++;
					}				
					if($i==0) { unset($data['records'][$key]);continue;}
				}
			}
		}
		$data['NO_Package']=false;
		if(isset($_POST['pack_name']) && $_POST['pack_name']==-1) $data['NO_Package']=true;
		//echo '<pre>';print_r($data);print_r($p_temp);print_r($j_temp);echo '</pre>';
		$this->load->view('billing_view',$data);
	}
	
	function retrieveRecord($jobid) {
		//mychanges
			$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jobid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id;
			$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;
			
		$query_drop = "SELECT * FROM crm_expected_payments WHERE jobid_fk =".$jobid; 
		$received_drop = $this->db->query($query_drop); 
		$array_drop = array();
		$i = 0;
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		foreach ($received_drop->result() as $value)
		{
			$array_drop[$i]['jobid_fk'] = $value->jobid_fk;
			$array_drop[$i]['amount'] = $value->amount;
			$array_drop[$i]['expectid'] = $value->expectid;
			$payment_amount = number_format($value->amount, 2, '.', ',');
			$array_drop[$i]['expected_date'] = date('d-m-Y', strtotime($value->expected_date));
			$array_drop[$i]['project_milestone_name'] = $value->project_milestone_name;
			$pt_select_box .= '<option value="'.$array_drop[$i]['expectid'] .'">' . $array_drop[$i]['project_milestone_name']. ' '.$symbol." {$payment_amount} by {$array_drop[$i]['expected_date']}" . '</option>';
			$i++;
		}
		echo $pt_select_box;
		
	}
	
	//For Edit Functionality - Edit Received Payments.
	function retrieveRecordEdit($jobid, $eid) {
		//mychanges
			$jsql = $this->db->query("select expect_worth_id from crm_jobs where jobid='$jobid'");
			$jres = $jsql->result();
			$worthid = $jres[0]->expect_worth_id;
			$expect_worth = $this->db->query("select expect_worth_name from crm_expect_worth where expect_worth_id='$worthid'");
			$eres = $expect_worth->result();
			$symbol = $eres[0]->expect_worth_name;
			
		$query_drop = "SELECT * FROM crm_expected_payments WHERE jobid_fk =".$jobid." order by expectid"; 
		$received_drop = $this->db->query($query_drop); 
		$array_drop = array();
		$i = 0;
		$pt_select_box = '';
		$pt_select_box .= '<option value="0"> &nbsp; </option>';
		foreach ($received_drop->result() as $value)
		{
		    //echo $eid ." ". $array_drop[$i]['expectid'];
		    //echo "tee".$eid."<br/>";
			$array_drop[$i]['jobid_fk'] = $value->jobid_fk;
			$array_drop[$i]['amount'] = $value->amount;
			$array_drop[$i]['expectid'] = $value->expectid;
			$payment_amount = number_format($value->amount, 2, '.', ',');
			$array_drop[$i]['expected_date'] = date('d-m-Y', strtotime($value->expected_date));
			$array_drop[$i]['project_milestone_name'] = $value->project_milestone_name;
			if($eid ==  $array_drop[$i]['expectid']) {
				$pt_select_box .= '<option selected ="selected" value="'.$array_drop[$i]['expectid'].'">' . $array_drop[$i]['project_milestone_name'] .' '.$symbol." {$payment_amount} by {$array_drop[$i]['expected_date']}" . '</option>';
			}
			else {
				$pt_select_box .= '<option value="'.$array_drop[$i]['expectid'].'">' . $array_drop[$i]['project_milestone_name'].' '.$symbol." {$payment_amount} by {$array_drop[$i]['expected_date']}" . '</option>';
			}
			$i++;
		}
		return $pt_select_box;
	}
	
	//New function for edit the payment -Starts here
	function agreedPaymentEdit($eid,$jid) {
		//echo $eid;
		$payment_details = $this->welcome_model->get_paymentDet($eid,$jid);
		//echo "<pre>"; print_r($payment_details); strtotime(date('Y-m-d'));
		$expected_date = date('d-m-Y', strtotime($payment_details['expected_date']));
		//<script type="text/javascript" src="assets/js/jquery-1.2.6-min.js"></script>
		echo '
			<script>
			$(function(){
				$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
			});
		function isNumberKey(evt)
		{
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
		}
			</script>
		    <form id="update-payment-terms">
			<table class="payment-table">
			<tr>
				<td>
				<br />
				<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" value= "'.$payment_details[project_milestone_name].'" class="textfield width200px" /> </p>
				<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" value= "'.$expected_date.'" class="textfield width200px pick-date" /> </p>
				<p>Value *<input type="text" onkeypress="return isNumberKey(event)" name="sp_date_3" id="sp_date_3" value= "'.$payment_details[amount].'" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
				<div class="buttons">
					<button type="submit" class="positive" onclick="updateProjectPaymentTerms('.$eid.'); return false;">Update Payment Terms</button>
				</div>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
				</td>
			</tr>
			</table>
			</form>';
		}
		
	function agreedPaymentView() {
		echo '<script type="text/javascript">
		$(function(){
				$("#sp_date_2").datepicker({dateFormat: "dd-mm-yy"});
			});
		function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
		</script>
		<br /><form id="set-payment-terms">
		<table class="payment-table">
		<tr>
			<td>
				<p>Payment Milestone *<input type="text" name="sp_date_1" id="sp_date_1" class="textfield width200px" /> </p>
				<p>Milestone date *<input type="text" name="sp_date_2" id="sp_date_2" class="textfield width200px pick-date" /> </p>
				<p>Value *<input type="text" onkeypress="return isNumberKey(event)" name="sp_date_3" id="sp_date_3" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
				<div class="buttons">
					<button type="submit" class="positive" onclick="setProjectPaymentTerms(); return false;">Add Payment Terms</button>
				</div>
				<input type="hidden" name="sp_form_jobid" id="sp_form_jobid" value="0" />
				<input type="hidden" name="sp_form_invoice_total" id="sp_form_invoice_total" value="0" />
			</td>
		</tr>
		</table>
		</form>';
	}
	
	function agreedPaymentDelete($eid, $jid) {
		//echo $eid . " " . $jid; exit;
		$payment_details_delete = $this->welcome_model->Del_paymentDet($eid, $jid);
		if ($payment_details_delete == 0) {
			$msg = "Received Payment cannot be Deleted!";
			echo "<span id=paymentfadeout><h6>Received Payments cannot be Deleted!</h6></span>";
			//echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
		} else {
			$userdata = $this->session->userdata('logged_in_user');
			$userid = $userdata['userid'];
			$jobid = $jid;
			$filename = $ex[3]; //filename
		
			$msg = "Payment Deleted!";
			echo "<span id=paymentfadeout><h6>Payment Deleted!</h6></span>";
			//echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
		}
		$this->payment_terms_delete($jid);
	}
	
	//Payment Received Edit function - Starts here.
	function paymentEdit($pdid,$jid) {
		//echo $pdid; exit;
		$received_payment_details = $this->welcome_model->get_receivedpaymentDet($pdid,$jid);
		//echo "<pre>"; print_r($received_payment_details); exit;
		$eid = $received_payment_details['map_term'];
		//echo "<pre>"; print_r($received_payment_details);
		$received_deposit_date = date('d-m-Y', strtotime($received_payment_details['deposit_date']));
		$updt = $this->retrieveRecordEdit($jid, $eid);
		echo '<br />
			<script>
				$(function(){
					$("#pr_date_3").datepicker({dateFormat: "dd-mm-yy", maxDate: "0"});
				});
				function isNumberKey(evt)
				{
				  var charCode = (evt.which) ? evt.which : event.keyCode;
				  if (charCode != 46 && charCode > 31 
					&& (charCode < 48 || charCode > 57))
					 return false;

				  return true;
				}
			</script>
			<form id="update-payment-recieved-terms">
			<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" value="'.$received_payment_details['invoice_no'].'" class="textfield width200px" /> </p>
			<p>Amount Recieved *<input type="text" onkeypress="return isNumberKey(event)" name="pr_date_2" id="pr_date_2" value="'.$received_payment_details['amount'].'" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
			<p>Date Recieved *<input type="text" name="pr_date_3" id="pr_date_3" value="'.$received_deposit_date.'" class="textfield width200px pick-date" /> </p>
			
			<p>Map to a payment term *<select name="deposit_map_field" id="deposit_map_field" class="deposit_map_field" style="width:210px;"> "'.$updt.'" </select></p>

			<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" >'.$received_payment_details['comments'].'</textarea> </p>
			<div class="buttons">
				<button type="submit" class="positive" onclick="updatePaymentRecievedTerms('.$pdid.','.$eid.'); return false;" >Update Payment</button>
			</div>
			<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
			<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
		</form>';
	}
	
	function receivedPaymentDelete($pdid, $jid, $map) {
		//echo $pdid . " " . $jid; exit;
		$receivedPayment_details_delete = $this->welcome_model->Del_receivedPaymentDet($pdid, $jid, $map);
		if ($receivedPayment_details_delete == 0) {
			$msg = "Error Occured!";
			echo "<span id=paymentfadeout><h6>Error Occured!</h6></span>";
			//echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
		} else {
			$msg = "Received Payment Deleted!";
			echo "<span id=paymentfadeout><h6>Received Payment Deleted!</h6></span>";
			//echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
		}
		$this->received_payment_terms_delete($jid);
	}
	
	function PaymentView() {
		echo '<script type="text/javascript">
		$(function(){
			$("#pr_date_3").datepicker({dateFormat: "dd-mm-yy", maxDate: "0"});
		});
		function isNumberKey(evt)
        {
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
        }
	   </script>
		<br /><form id="payment-recieved-terms">
			<p>Invoice No *<input type="text" name="pr_date_1" id="pr_date_1" class="textfield width200px" /> </p>
			<p>Amount Recieved *<input onkeypress="return isNumberKey(event)" type="text" name="pr_date_2" id="pr_date_2" class="textfield width200px" /><span style="color:red;">(Numbers only)</span> </p>
			<p>Date Recieved *<input type="text" name="pr_date_3" id="pr_date_3" class="textfield width200px pick-date" /> </p>
			
			<p>Map to a payment term *<select name="deposit_map_field" id="deposit_map_field" class="deposit_map_field" style="width:210px;"> "'.$updt.'" </select></p>

			<p>Comments <textarea name="pr_date_4" id="pr_date_4" class="textfield width200px" ></textarea> </p>
			<div class="buttons">
				<button type="submit" class="positive" onclick="setPaymentRecievedTerms(); return false;">Add Payment</button>
			</div>
			<input type="hidden" name="pr_form_jobid" id="pr_form_jobid" value="0" />
			<input type="hidden" name="pr_form_invoice_total" id="pr_form_invoice_total" value="0" />
		</form>';
	}
}
?>
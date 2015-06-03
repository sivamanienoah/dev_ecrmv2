<?php
class leadsdue_cron extends crm_controller {
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('leadsdue_model');
        $this->load->library('validation');
		$this->load->library('email');
    }
    
    public function index()
    {    	
    	$data = array();
    	$mgmt_mails = $this->leadsdue_model->getManagementMail();
    	
    	$res = $this->leadsdue_model->getLeads();
    	$data['leads'] = $res['result'];   	
    	
    	$user_mail = array();
    	
    	/*To send mail to management*/
    	if($res['num']>0){    		
	    	$content = $this->load->view('cron/leadsdue_cron',$data,true);
	    	if(!empty($mgmt_mails)){
		    	foreach ($mgmt_mails as $mail){
		    		$this->send_mail($content, $mail->email, "Pending leads");
		    		//$this->send_mail($content, 'akarthik@enoahisolution.com', 'Leads to be followed up');
		    		//echo $content;
		    	}
	    	}
	    	
    	}
	    	
    	$created = array();
    	$assigned = array();
    	$assigned_user = array();
    	foreach ($data['leads'] as $leads)
    	{
    		if($leads->created_by != $leads->lead_assign){
    			$created[$leads->created_by][$leads->lead_assign][] = $leads;
    		}
    		$assigned[$leads->lead_assign][] = $leads;
    		
    		$user_mail[$leads->lead_assign] = $leads->assigned_mail;
    		$user_mail[$leads->created_by] = $leads->owner_mail;
    		
    		$assigned_user[] = $leads->lead_assign;
    		$assigned_user[] = $leads->created_by; 
    	}
    	$data['assigned_user'] = array_unique($assigned_user);
    	
   	
    	if(!empty($data['assigned_user'])){
	    	foreach($data['assigned_user'] as $user)
	    	{    		
	    		$data['cur_user'] = $user;
	    		if(!empty($assigned[$user])){
	    			$data['assigned'] = empty($assigned[$user])?'':$assigned[$user];
	    			$data['created'] = array();
	    			
	    			if(!empty($data['assigned'])){    				
		    			$content = $this->load->view('cron/leadsdue_assigned_cron',$data,true);
		    			//$this->send_mail($content, 'akarthik@enoahisolution.com', 'Pending leads');
		    			$this->send_mail($content, $user_mail[$user], 'Pending leads');
		    			//echo $content;		    			
	    			}
	    		}
	    		
	    		if(!empty($created[$user])){	
		    		
		    		$data['created'] = empty($created[$user])?'':$created[$user];
		    		$data['assigned'] = array();		    		
		    		
		    		if(!empty($data['created'])){
			    		$content = $this->load->view('cron/leadsdue_assigned_cron',$data,true);
			    		//$this->send_mail($content, 'akarthik@enoahisolution.com', 'Leads to be followed up');
			    		$this->send_mail($content, $user_mail[$user], 'Leads to be followed up');
			    		//echo $content;			    		
		    		}
	    		}
	    	}    		
    	}    	
    	
    	exit;   	
    }
    
	public function send_mail($content,$to_user,$subject)
    {
    	$config['mailtype'] = 'html';
		$from = 'webmaster@enoahprojects.com';
		// $to_user = 'ssriram@enoahisolution.com';
	
		$this->email->initialize($config);
		$this->email->clear();
		$this->email->from($from, 'Webmaster');
		$this->email->to($to_user);		
		$this->email->subject($subject);
		$message_value=$content;
		$this->email->message($message_value);
		$this->email->send();
    }    
}
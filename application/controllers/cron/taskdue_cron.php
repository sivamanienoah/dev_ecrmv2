<?php
class taskdue_cron extends crm_controller {
    
	public $userdata;
	
    function __construct()
	{
        parent::__construct();
		//$this->login_model->check_login();
		//$this->userdata = $this->session->userdata('logged_in_user');
        $this->load->model('taskdue_model');
        $this->load->library('validation');
		$this->load->library('email');
    }
    
    public function index()
    {    	
    	$data = array();   	    
    	
    	$taskConfig = $this->taskdue_model->getConfig();
    	$daysConfig = empty($taskConfig->task_alert_days)?1:$taskConfig->task_alert_days;
    	  
    	$res = $this->taskdue_model->getDailyTask($daysConfig);
    	
    	$data['tasks'] = $res['result'];

    	$created = array();
    	$assigned = array();
    	$assigned_user = array();
    	$user_mail = array();
    	foreach ($res['result'] as $task)
    	{
    		if($task->created_by != $task->userid_fk){
    			$created[$task->created_by][$task->userid_fk][] = $task;
    		}
    		$assigned[$task->userid_fk][] = $task;
    		
    		$user_mail[$task->userid_fk] = $task->owner_mail;
    		$user_mail[$task->created_by] = $task->assigned_mail;
    		
    		$assigned_user[] = $task->userid_fk;
    		$assigned_user[] = $task->created_by; 
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
		    			$content = $this->load->view('cron/taskdue_cron',$data,true);
		    			// $this->send_mail($content, 'ssriram@enoahisolution.com', 'Pending tasks');
		    			$this->send_mail($content, $user_mail[$user], 'Pending tasks');		    			
			    		//echo $content;
		    			
	    			}
	    		}
	    		
	    		if(!empty($created[$user])){	
		    		
		    		$data['created'] = empty($created[$user])?'':$created[$user];
		    		$data['assigned'] = array();		    		
		    		
		    		if(!empty($data['created'])){
			    		$content = $this->load->view('cron/taskdue_cron',$data,true);
			    		// $this->send_mail($content, 'ssriram@enoahisolution.com', 'Task to be followed up');
			    		$this->send_mail($content, $user_mail[$user], 'Pending tasks');
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
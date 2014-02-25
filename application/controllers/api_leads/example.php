<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Example extends REST_Controller
{
	function user_get()
    {

        if(!$this->get('id'))
        {
        	$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
    	$user = @$users[$this->get('id')];
        if($user){
            $this->response($user, 200); // 200 being the HTTP response code
        }
        else{
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
	$this->load->config('custom_config');
	  
	  $query  = $this->db->select('*');
	  $query = $this->db->from("crm_keys");
      $query = $this->db->get();
	  $servername = "";
	  $servername_arr  = $query->row_array();
	  if(!empty($servername_arr)){
	          $servername = $servername_arr['server_name'];
	  }
	 if($_SERVER["HTTP_ENOAHCRM"]=="enoahcrm" && $_SERVER['SERVER_NAME']==$servername) {
        //$this->some_model->updateUser( $this->get('id') );
        //$message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
		$arrinset = array("oppurtunity_title"=>$this->post('enquiry'),
						  "oppurtunity_email"=>$this->post('email'),
						  "oppurtunity_phone"=>$this->post('phone'),
						  "oppurtunity_name"=>$this->post('name'),
						  "expect_worth_id"=>"",
						  "custid_fk"=>0,
						  "expect_worth_id"=>0);
		$this->db->insert($this->config->config["crm"]["dbpref"]."oppurtunities",$arrinset);
        $this->response($message, 200); // 200 being the HTTP response code
		}
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}
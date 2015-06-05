<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CRM_Controller {
	
	function __construct() 
	{
        parent::__construct();
		$testmode = true;
		$this->login_id = ($testmode==true)?"27VqPfy3K":"";
		$this->transaction_key = ($testmode==true)?"4G73492ZSp6wzA9g":"";
		$this->post_url = ($testmode==true)?"https://test.authorize.net/gateway/transact.dll":"https://secure.authorize.net/gateway/transact.dll";
    }
	
	/*
	method to check the link and process
	*/
    function dopay($link)
	{
		$data = array();
		$data['exp_details'] = array();
		$cur_date = date("Y-m-d");
		$this->db->select("*");
		$qry = $this->db->get_where($this->cfg['dbpref']."invoices",array("unique_link" => $link,"status" => 0));
		$nos = $qry->num_rows();
		
		if($nos){
			$res = $qry->row();
			$expiry_date = $res->expiry_date;
			if($expiry_date > $cur_date){
				// get expected payment table records
				$this->db->select("invc.inv_id,exp.*,le.lead_title,expw.expect_worth_name,le.custid_fk");
				$this->db->from($this->cfg['dbpref']."invoices_child as invc");
				$this->db->join($this->cfg['dbpref']."expected_payments as exp","invc.exp_id = exp.expectid");
				$this->db->join($this->cfg['dbpref']."leads as le","exp.jobid_fk = le.lead_id");
				$this->db->join($this->cfg['dbpref']."expect_worth as expw","expw.expect_worth_id = le.expect_worth_id");
				$this->db->where(array("inv_id" => $res->inv_id));
				$q = $this->db->get();
				if($q->num_rows()>0){
					$data['exp_details'] = $q->result();
				}
				$data['invoice'] = $res;
			}else{
				$this->session->set_userdata("error_message","link expired!");
			}
		}else{
			$this->session->set_userdata("error_message","Invalid link!");
		}
		$this->load->view('payment',$data);
    }
	
	/*
	process the payment request with the details provided from customer.
	*/
	
	function process_payment(){
		
		if($this->input->post("custid_fk")){
			
			//get customer details
			$qry = $this->db->get_where($this->cfg['dbpref']."customers",array("custid" => $this->input->post("custid_fk")));
			if($qry->num_rows()>0){
				$res = $qry->row();
			}
			
			$post_values = array(
				
				// the API Login ID and Transaction Key must be replaced with valid values
				"x_login"			=> $this->login_id,
				"x_tran_key"		=> $this->transaction_key,

				"x_version"			=> "3.1",
				"x_delim_data"		=> "TRUE",
				"x_delim_char"		=> "|",
				"x_relay_response"	=> "FALSE",

				"x_type"			=> "AUTH_CAPTURE",
				"x_method"			=> "CC",
				"x_card_num"		=> $this->input->post("card_number"),
				"x_exp_date"		=> $this->input->post("expiry_month").$this->input->post("expiry_year"),

				"x_amount"			=> "1", //$this->input->post("total_amount")
				"x_description"		=> $res->company,
				"x_first_name"		=> $res->first_name,
				"x_last_name"		=> $res->last_name,
				"x_address"			=> $res->add1_line1,
				"x_state"			=> $res->add1_line2,
				"x_zip"				=> $res->add1_postcode,			
				"x_email"			=> "mthiyagarajan@enoahisolution.com", //$res->email_1
				// Additional fields can be added here as outlined in the AIM integration
				// guide at: http://developer.authorize.net
			);

			$post_string = "";
			foreach( $post_values as $key => $value )
				{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
			$post_string = rtrim( $post_string, "& " );
		
			$request = curl_init($this->post_url); // initiate curl object
				curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
				$post_response = curl_exec($request); // execute curl post and store results in $post_response
				// additional options may be required depending upon your server configuration
				// you can find documentation on curl options at http://www.php.net/curl_setopt
			curl_close ($request); // close curl object

			// This line takes the response and breaks it into an array using the specified delimiting character
			$response_array = explode($post_values["x_delim_char"],$post_response);
			
			$transaction_id = $response_array[6];
			$approval_code = $response_array[5];
			$card_number = $response_array[50];
			$card_type = $response_array[51];
			$message = $response_array[3];
			$paid_amount = $response_array[9];
			$inv_id = $this->input->post("inv_id");	
				
			if($response_array[0]==1){
				$paid_status = 1;
				$this->db->update($this->cfg['dbpref']."invoices",array("status" => 1),array("inv_id" => $inv_id));
				$message1 = "Thank you! your payment was success.";
				
				$this->db->select("invc.exp_id");
				$this->db->from($this->cfg['dbpref']."invoices_child as invc");
				$this->db->join($this->cfg['dbpref']."invoices as inv","inv.inv_id=invc.inv_id");
				$this->db->where("inv.inv_id",$inv_id);		
				$qry = $this->db->get();
				if($qry->num_rows()>0){
					$res = $qry->result();	
					foreach($res as $rs){
						$this->db->update($this->cfg['dbpref']."expected_payments",array("received" => 1,"payment_remark" => "Authorize.net payment $transaction_id"),array("expectid" => $rs->exp_id));
					}
				}
				
				$this->db->update($this->cfg['dbpref']."invoices_child",array("status" => 1),array("inv_id" => $inv_id));
				$this->db->update($this->cfg['dbpref']."invoices",array("status" => 1),array("inv_id" => $inv_id));
					
			}else{
				$paid_status = 0;
				$message1 = $message;
			}
			
			$ins_arr = array("inv_id" => $inv_id,
						"paid_amount" => $paid_amount,
						"paid_status" => $paid_status,
						"card_type" => $card_type,
						"card_number" => $card_number,
						"transaction_id" => $transaction_id,
						"approval_code" => $approval_code,
						"transaction_ip" => $_SERVER['REMOTE_ADDR'],
						"transaction_date" => date("Y-m-d H:i:s"),
						// "transaction_method" => $this->input->post("payment_method"),
						"transaction_method" => 2,
						"transaction_message" => $message
						);
			
			
			$this->db->insert($this->cfg['dbpref']."payment_history",$ins_arr);
			$this->session->set_userdata("error_message",$message1);
			redirect("payment/success");
		}
	}
	
	function success(){
		$data = array();
		$this->load->view('payment',$data);	
	}
}
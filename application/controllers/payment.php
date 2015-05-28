<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CRM_Controller {
	
	function __construct() 
	{
        parent::__construct();
    }
	
    function dopay($link)
	{
		$data = array();
		$cur_date = date("Y-m-d");
		$this->db->select("inv.*,exp.*,proj.lead_title");
		$this->db->from($this->cfg['dbpref']."invoices as inv");
		$this->db->join($this->cfg['dbpref']."expected_payments as exp","exp.expectid=inv.exp_id");
		$this->db->join($this->cfg['dbpref']."leads as proj","proj.lead_id=exp.jobid_fk");
		$this->db->where("inv.unique_link", $link);
		$qry = $this->db->get();
		//echo $this->db->last_query();exit;
		$nos = $qry->num_rows();
		
		if($nos){
			$res = $qry->row();
			$expiry_date = $res->expiry_date;
			if($expiry_date > $cur_date){
				 $data['invoice'] = $res;
			}else{
				$this->session->set_userdata("error_message","link expired!");
			}
		}else{
			$this->session->set_userdata("error_message","Invalid link!");
		}
		$this->load->view('payment',$data);
    }
}
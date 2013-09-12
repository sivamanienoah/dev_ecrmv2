<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class web_to_lead extends CI_Controller {
	
	public $cfg;
	public $userdata;
	function __construct()
	{
		parent::__construct();
		
		//$this->login_model->check_login();
		$this->cfg = $this->config->item('crm');
		//$this->userdata = $this->session->userdata('logged_in_user');
		//$this->load->model('welcome_model');
		//$this->load->model('job_model');
		//$this->load->model('regionsettings_model');
		//$this->load->helper('text');
		//$this->load->library('email');
		//$this->email->initialize($config);
		//$this->email->set_newline("\r\n");
	}
	
    
	
	public function add_lead(){
	 
	    //Create Customer 
		
		if(sizeof($_POST)==0){
		    echo 0;
			return false;
		}
		$ins = array();
		$ins_cus = array();
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
			
			$ins_cus['add1_region']		= '1'; //Asia
			$ins_cus['add1_country'] 	= '15'; //India	
			$ins_cus['add1_state']		= '24'; //Tamil nadu
			$ins_cus['add1_location']	= '3'; //Chennai
			
			$ins['job_title']           = 'Lead From Website - Contact us';
		}else{
		
			$ins_cus['first_name']    = $_POST['name']; 
			$ins_cus['email_1']       = $_POST['email']; 
			$ins_cus['company']       = $_POST['company']; 
			$ins_cus['comments']      = $_POST['content']; 
		 	
			$ins_cus['add1_region']		= '1';//Asia
			$ins_cus['add1_country'] 	= '15';//India	
			$ins_cus['add1_state']		= '24'; //Tamil nadu
			$ins_cus['add1_location']	= '3';//Chennai
			
			$ins['job_title']           = 'Lead From Website - QAD Services';			
					
	    }
	 
		$this->db->insert($this->cfg['dbpref'] . 'customers', $ins_cus);
		$insert_id = $this->db->insert_id();

	    //
		//$ins['job_title']           = 'Ask the Expert';		
		$ins['custid_fk']           = $insert_id;
		$ins['job_category']        = empty($_POST['job_category'])?39:$_POST['job_category'];
		$ins['lead_source']       = '9';
		$ins['lead_assign']         = 118;
		$ins['expect_worth_id']     = 1;
		$ins['expect_worth_amount'] = '0.00';
		$ins['belong_to']           = 118; // lead owner
		$ins['division']         = 'ENOAH-IND';
		$ins['date_created']        = date('Y-m-d H:i:s');
		$ins['date_modified']       = date('Y-m-d H:i:s');
		$ins['job_status']          = 1;
		$ins['lead_indicator']   = 'HOT';
		$ins['created_by']          = 118;
		$ins['modified_by']         = 118;
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
				if(!empty($row['item_price'])){
                $html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item width565px"><tr><td class="item-desc" width="85%">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td><td width="14%" class="item-price width100px" align="right" valign="bottom">' . $row['item_price'] . '</td></tr></table></li>';
				}else{
				$html .= '<li id="qi-' . $row['itemid'] . '"><table cellpadding="0" cellspacing="0" class="quote-item width565px"><tr><td class="item-desc" colspan="2">' . nl2br(cleanup_chars(ascii_to_entities($row['item_desc']))) . '</td></tr></table></li>';
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
    
}
?>
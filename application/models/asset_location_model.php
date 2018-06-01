<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage Service Model
 *
 * @class 		manage_service_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class asset_location_model extends crm_model {
    
	/*
	*@construct
	*@Manage Service Model
	*/
    function Asset_location_model() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get Lead_service for Search
	*@Manage Service Model
	*/
	public function get_jobscategory($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'lead_services');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('services', $search); 
		}
		$query = $this->db->get();
		$cate =  $query->result_array();
		return $cate;
    }

	/*
	*@Get Sale Divisions for Search
	*@Method  get_salesDivisions
	*@table   sales_divisions
	*/
	public function get_salesDivisions($search = FALSE) {
	
		$this->db->select('sd.div_id,sd.division_name,sd.base_currency,sd.status,ew.expect_worth_id,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'sales_divisions sd');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id=sd.base_currency','LEFT');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('sd.division_name', $search);
		}
		$query = $this->db->get();
		$divs =  $query->result_array();
		return $divs;
    }

	/*
	*@Check Active Status
	*@Method  get_list_active
	*/
	public function get_list_active($tbl_name) {
		$query = $this->db->get_where($tbl_name, array('status' => 1));
		$activeLists = $query->result_array();
		return $activeLists;
	}

	/*
	*@Check Active Status
	*@Method  get_list_active
	*@table   lead_source
	*/
	public function get_lead_source($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'lead_source');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('lead_source_name', $search); 
		}
		$query = $this->db->get();
		$leads =  $query->result_array();
		return $leads;
    }
	

	/*
	*@Get Currency Name (e.g USD,AUD)
	*@Method  get_expect_worth_cur
	*@table   expect_worth
	*/
	public function get_expect_worth_cur($search = FALSE) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'expect_worth');
		if ($search != false) {
			$search = urldecode($search);
			$this->db->like('expect_worth_name', $search);
		}
		$query  = $this->db->get();
		$expect =  $query->result_array();
		return $expect;
    }


	/*
	*@Get all Currency (e.g USD,AUD)
	*@Method  get_all_currency
	*@table   currency_all
	*/
	public function get_all_currency() {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'currency_all');
		$query = $this->db->get();
		$cur =  $query->result_array();
		return $cur;
    }
	
	/*
	*@Insert New Currency Record
	*@Method  insert_new_currency
	*@table   expect_worth
	*/
	public function insert_new_currency($ins) {
		$this->db->insert("{$this->cfg['dbpref']}" . 'expect_worth', $ins);
		$last_ins_id = $this->db->insert_id();
		if (array_key_exists('is_default', $ins)) {
			$data['is_default'] = 0;
			$cur_conver['to'] = $last_ins_id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$last_ins_id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
			currency_convert();
		}
    }

	/*
	*@Update Exist Currency
	*@Method  insert_new_currency
	*@table   expect_worth
	*/
	public function updt_exist_currency($updt, $id) {
		$res = $this->db->update($this->cfg['dbpref'].'expect_worth', $updt, "expect_worth_id = ".$id." ");
		if (array_key_exists('is_default', $updt)) {
			$data[is_default] = 0;
			$cur_conver['to'] = $id;
			$this->db->update($this->cfg['dbpref'].'expect_worth', $data, "expect_worth_id != ".$id." ");
			$this->db->truncate($this->cfg['dbpref'].'currency_rate');
		}
		currency_convert();
		return $res;
    }

	/*
	*@Get Currency Name By id
	*@Method  getCurName
	*@table   currency_all
	*/
	public function getCurName($id) {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].'currency_all');
		$this->db->where('cur_id', $id);
		$query = $this->db->get();
		$res =  $query->row_array();
		return $res;
    }

	/*
	*@Get row record for dynamic table
	*@Method  get_row
	*/
	public function get_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->result_array();
    }

	/*
	*@Get row count for dynamic table
	*@Method  get_num_row
	*/
    public function get_num_row($table, $cond) {
    	$res = $this->db->get_where($this->cfg['dbpref'].$table, $cond);
        return $res->num_rows();
    }

	/*
	*@Update Row for dynamic table
	*@Method  update_row
	*/
    public function update_row($table, $cond, $data) {
    	$this->db->where($cond);
		return $this->db->update($this->cfg['dbpref'].$table, $data);
    }
	
 	/*
	*@Update Row for dynamic table
	*@Method  update_row
	*/
    /*public function update_row($table, $cond, $data) {
		$sql = $this->db->insert_string($this->cfg['dbpref'].$table, $data) . ' ON DUPLICATE KEY UPDATE column_name1=value1, column_name2=value2';
		return $this->db->query($sql);
    } */

	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }
	
	/*
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_return_row($table, $param) {
    	return $this->db->insert($this->cfg['dbpref'].$table, $param);
    }

	/*
	*@Delete Row for dynamic table
	*@Method  delete_row
	*/
	public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
	
	/*
	*Checking duplicates
	*/
	function check_duplicate($tbl_cont, $condn, $tbl_name) {
         //  echo '<pre>';            print_r($tbl_name);exit;
		$this->db->select($tbl_cont['name']);
		$this->db->where($tbl_cont['name'], $condn);
		if(!empty($condn['id'])) {
			$this->db->where($tbl_cont['id'].' !=', $condn['id']);
		}
		
             // echo $this->db->last_query();exit;
        return $res->num_rows();
	}
	
	####### get single row ########
	function get_record($select,$table,$where='')
	{
		$this->db->select($select);
		if($where){
			$this->db->where($where);
		}
		$query = $this->db->get($this->cfg['dbpref'].$table,1);
 		return $query->row_array();
	}
	
	/*
	*@Get records for Search
	*@Sales Forecast Model
	*/
	public function get_records($tbl, $wh_condn='', $order='') {
		$this->db->select('*');
		$this->db->from($this->cfg['dbpref'].$tbl);
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result_array();
    }

	/*
	*@Get records for Search
	*@Sales Forecast Model
	*/
	public function get_bk_curr_records($wh_condn='', $order='') {
		$this->db->select('bk.expect_worth_id_from,bk.expect_worth_id_to,bk.financial_year,bk.currency_value,ew.expect_worth_name');
		$this->db->from($this->cfg['dbpref'].'book_keeping_currency_rates bk');
		$this->db->join($this->cfg['dbpref'].'expect_worth ew','ew.expect_worth_id=bk.expect_worth_id_from', 'LEFT');
		if(!empty($wh_condn))
		$this->db->where($wh_condn);
		if(!empty($order)) {
			foreach($order as $key=>$value) {
				$this->db->order_by($key,$value);
			}
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
    }
    
}

?>

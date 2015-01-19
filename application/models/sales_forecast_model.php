<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Forecast Model
 *
 * @class 		sales_forecast_model
 * @extends		crm_model (application/core)
 * @classes     Model
 * @author 		eNoah
 */

class Sales_forecast_model extends crm_model {
    
	/*
	*@construct
	*@Sales Forecast Model
	*/
    public function __construct() {
       parent::__construct();
	   $this->load->helper('custom_helper');
    }

	/*
	*@Get Sale Records
	*@Sales Forecast Model
	*/
	public function get_sale_records($filter = false) {
	
		$this->db->select('sfc.forecast_id,sfc.customer_name,sfc.lead_name,sfc.milestone,sfc.for_month_year,sfc.created_on,enti.division_name,us.first_name,us.last_name');
		$this->db->from($this->cfg['dbpref'].'sales_forecast as sfc');
		$this->db->join($this->cfg['dbpref'].'sales_divisions as enti', 'enti.div_id = sfc.entity');
		$this->db->join($this->cfg['dbpref'].'users as us', 'us.userid = sfc.created_by');
		
		if (!empty($filter['entity']) && $filter['entity']!='null') {
			$filter['entity'] = explode(',',$filter['entity']);
			$this->db->where_in('sfc.entity', $filter['entity']);
		}
		if (!empty($filter['customer']) && $filter['customer']!='null') {
			$filter['customer'] = explode(',',$filter['customer']);
			$this->db->where_in('sfc.customer_name', $filter['customer']);
		}
		if (!empty($filter['lead_names']) && $filter['lead_names']!='null') {
			$filter['lead_names'] = explode(',',$filter['lead_names']);
			$this->db->where_in('sfc.lead_name', $filter['lead_names']);
		}
		if(!empty($filter['month_year_from_date']) && empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfc.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
		} else if(!empty($filter['month_year_from_date']) && !empty($filter['month_year_to_date'])) {
			$this->db->where('DATE(sfc.for_month_year) >=', date('Y-m-d', strtotime($filter['month_year_from_date'])));
			$this->db->where('DATE(sfc.for_month_year) <=', date('Y-m-d', strtotime($filter['month_year_to_date'])));
		}
		$query = $this->db->get();
		// echo $this->db->last_query(); exit;
		return $query->result_array();
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
		// echo $this->db->last_query(); exit;
		return $query->result_array();
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
	*@Insert Row for dynamic table
	*@Method  insert_row
	*/
	public function insert_row($table, $param) {
    	$this->db->insert($this->cfg['dbpref'].$table, $param);
    }

	/*
	*@Delete Row for dynamic table
	*@Method  insert_row
	*/
	public function delete_row($table, $cond) {
        $this->db->where($cond);
        return $this->db->delete($this->cfg['dbpref'].$table);
    }
	
	/*
	*Checking duplicates
	*/
	function check_duplicate($tbl_cont, $condn, $tbl_name) {
		$this->db->select($tbl_cont['name']);
		$this->db->where($tbl_cont['name'], $condn['name']);
		if(!empty($condn['id'])) {
			$this->db->where($tbl_cont['id'].' !=', $condn['id']);
		}
		$res = $this->db->get($this->cfg['dbpref'].$tbl_name);
        return $res->num_rows();
	}
    
}

?>

<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


	
if ( ! function_exists('get_package_details'))
{
	function get_package_details($pid)
	{	
		$CI = get_instance();
		$cfg = $CI->config->item('crm'); // load config
		
		$CI->db->select('package_name', FALSE);
                $CI->db->where_in('p.package_id',$pid);
                $sql = $CI->db->get($cfg['dbpref'].'package p');
                $res = $sql->row_array();
                
//                $this->db->select('*');
//                $this->db->from($this->cfg['dbpref'] . 'package as p');
//                
//                $this->db->where_in('p.package_id', $pid);
//                $customer = $this->db->get();
               // echo $CI->db->last_query() . '<br>'; exit;
                return $res['package_name'];
	}
        
       
}
    if ( ! function_exists('get_users_list'))
    {
     function get_users_list() 
            {
                $CI = get_instance();
                $cfg = $CI->config->item('crm'); // load confi
                $CI->db->select('userid,first_name,last_name,username,level,role_id,inactive,emp_id');
                $CI->db->order_by('first_name',"asc");
                $q = $CI->db->get($cfg['dbpref'] . 'users');
                   //    echo $this->db->last_query(); exit;
                return $q->result_array();
            }
    }

/* End of file lead_helper.php */
/* Location: ./system/helpers/lead_helper.php */
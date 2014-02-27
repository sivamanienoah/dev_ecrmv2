<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Authentication {

  function validate_api($username, $password)
  {
     $CI =& get_instance();
     $CI->db->select('*');
	 $query = $CI->db->where('id','1');
	 $query = $CI->db->from('crm_keys');
	 $query  = $CI->db->get();
	 $arrresult = $query->row_array();
	 if(!empty($arrresult))
	 {
		 if($username==$arrresult['username'] && md5($password)==$arrresult['password'])
		 {
			   return true;
		 }
	 }
  }

}
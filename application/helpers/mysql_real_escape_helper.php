<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		eNoah iSolution
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('real_escape_array'))
{
	function real_escape_array($data = array())
	{
		$post_data_arr = array();
		if(!empty($data) && count($data)>0) {
			foreach($data as $key=>$value){
				if(is_array($value) && sizeof($value)>0) {
					$post_data_arr[$key] = real_escape_array($value);
				} else {
					$CI = get_instance();
					$post_data_arr[$key] = $CI->db->escape_str($value);
				}
			}
		}
		return $post_data_arr;
	}
	
}


/* End of file mysql_real_escape_helper.php */
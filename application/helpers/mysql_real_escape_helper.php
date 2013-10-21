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
		if(sizeof($data)>0){
			foreach($data as $key=>$value){
				$post_data_arr[$key] = 	mysql_real_escape_string($value);
			}
		}
		return $post_data_arr;
	}
}


/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */
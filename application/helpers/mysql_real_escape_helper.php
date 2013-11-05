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
				if(is_array($value) && sizeof($value)>0){
					$post_data_arr[$key] = $this->multi_dimention_firstlevel($value);
				}else{
					$post_data_arr[$key] = mysql_real_escape_string($value);
				}
			}
		}
		return $post_data_arr;
	}
	
	/*
	*@Multi Dimention Array First Level 
	*/
	function multi_dimention_firstlevel($data=array()){
	
		$post_firstlevel_data = array();
	
		if(sizeof($data)>0){
			foreach($data as $key=>$value){
			    if(is_array($value) && sizeof($data)>0){
					$post_firstlevel_data[$key] = $this->multi_dimention_nextlevel($value);
				}else{
					$post_firstlevel_data[$key] = mysql_real_escape_string($value);
				}
			}
	    }
		
		return $post_firstlevel_data;
	
	}


	/*
	*@Multi Dimention Array Next Level 
	*/
	function multi_dimention_nextlevel($data=array()){
	
		$post_nextlevel_data = array();
	
		if(sizeof($data)>0){
			foreach($data as $key=>$value){
			    if(is_array($value) && sizeof($data)>0){
					$post_nextlevel_data[$key] = $this->multi_dimention_firstlevel($value);
				}else{
					$post_nextlevel_data[$key] = mysql_real_escape_string($value);
				}
			}
	    }
		
		return $post_nextlevel_data;
		
	}
	
}


/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */
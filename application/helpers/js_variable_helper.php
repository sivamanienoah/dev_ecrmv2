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
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 */

// ------------------------------------------------------------------------

/**
 * Js Global Variable
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('js_global_variable'))
{
	function js_global_variable($view='', $viewPjts='')
	{
			$CI = get_instance();
		?>
			<script language="javascript">

				$(document).ready(function() {
					$(window).scroll(function () {
						set = $(document).scrollTop()+"px";
						$('#floatNotifyDiv').animate({top:set}, {duration:1000,queue:false});
					});
					
					$( ".grid-close" ).bind( "click", function() { 
						// $('#floatNotifyDiv').slideUp('slow', function () { $lead.css('display','none'); });
						$('#floatNotifyDiv').hide();
					});
				});
				
				url_segment      = []; /// URL segments 
				csrf_token_name  = "<?php echo $CI->security->get_csrf_token_name(); ?>";  //Assign Token Name
				csrf_hash_token  = "<?php echo $CI->security->get_csrf_hash(); ?>";   //Assign Hash Token 
				site_base_url    = "<?php echo base_url(); ?>";   //Site Base URL
				accesspage       = "<?php echo $CI->session->userdata('accesspage'); ?>";   //Site Base URL
				viewlead         = "<?php echo $view; ?>";   //lead access
				viewPjt			 = "<?php echo $viewPjts; ?>"; //project access
				
				/// Site URL segment 
				<?php 
					if(count($CI->uri->segments)>0){ 
						foreach($CI->uri->segments as $key=>$value){ ?>
							url_segment['<?php echo $key; ?>'] = '<?php echo $value; ?>';
					<?php
						} 
					}
				?>
				
			</script>
		<?php
	}
	
}

// ------------------------------------------------------------------------



/* Location: ./system/helpers/js_variable.php */
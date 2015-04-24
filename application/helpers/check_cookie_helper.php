<?php 

$CI =& get_instance();
$CI->load->helper('url');
$CI->load->library('session');
$CI->load->model('login_model'); 

$url=$CI->router->fetch_class(); 
 
//echo $SSO_Status.">>".$CI->session->userdata('SSO_Status');exit;
if($CI->session->userdata('SSO_Status')=='1'){
if(isset($_COOKIE['sso_token']) && $CI->session->userdata('SSO_Status')=='1')
{
	$username=$CI->session->userdata['logged_in_user']['username'];
	
	if($username!='' && $CI->session->userdata('loggedType')!='ldb')
	{
		$data=$CI->login_model->validate_cookie($_COOKIE['sso_token'],$username);		
	}
	else
	{
		$data=$CI->login_model->checkCookie($_COOKIE['sso_token']);
	}
	if((!$data['success'] && $url!='userlogin') )
	{
	?>
		<script>window.location="http://<?php echo $_SERVER['HTTP_HOST'];?>/dev/projects/ecrmv2/userlogin/logout";</script>
	<?php exit;}	
}
else if($url!='userlogin' && !isset($_COOKIE['sso_token']) && $CI->session->userdata('loggedType')=='ldap' && $CI->session->userdata('SSO_Status')=='1') //&& $CI->session->userdata('SSO_Status')=='1' )
{
 
?>

	<script>window.location="http://<?php echo $_SERVER['HTTP_HOST'];?>/dev/projects/ecrmv2/userlogin/logout";</script>
<?php exit;}
}
?>
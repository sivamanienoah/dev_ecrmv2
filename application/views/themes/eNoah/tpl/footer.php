<div id="footer">
  <!--<ul id="footer-links">
    <li><a href="http://www.enoahisolution.com/">eNoah  iSolution</a></li>
	<li><a href="http://econnect.enoahisolution.com/">Econnect</a></li>
    <li><a href="http://www.google.com.au">Google</a></li>
    <li><a href="http://www.google.com/analytics">Google Analytics</a></li>
    
  </ul>-->
  <p>Copyright &copy; <?php echo  date ('Y'); ?> <a href="http://www.enoahisolution.com" target="_blank">eNoah iSolution Pvt Ltd.</a> <?php echo  $cfg['app_name'] . ' ' . $cfg['app_version'] ?> was last updated <?php echo  $cfg['app_date'] ?></p>

  
</div>
</div>
<script type="text/javascript">
$(function(){
  $('#footer-links li a').click(function(){
    window.open(this.href);
    return false;
  });
  $('.g-search').css('color', '#777777').focus(function(){
		if ($(this).val() == 'Lead No, Job Title, Name or Company') {
			$(this).val('').css('color', '#333333');
		}
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).css('color', '#777777').val('Lead No, Job Title, Name or Company');
		}
	});
  $('.pjt-search').css('color', '#777777').focus(function(){
		if ($(this).val() == 'Project No, Project Title, Name or Company') {
			$(this).val('').css('color', '#333333');
		}
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).css('color', '#777777').val('Project No, Project Title, Name or Company');
		}
	});
})
</script>
</body>
</html>

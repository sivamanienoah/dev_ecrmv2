<div id="footer">
	<p class="footer-text">Copyright &copy; <?php echo  date ('Y'); ?> <a href="http://www.enoahisolution.com" target="_blank">eNoah iSolution Pvt Ltd.</a> <?php echo  $cfg['app_name'] . ' ' . $cfg['app_version'] ?> was last updated <?php echo  $cfg['app_date'] ?></p> 
	
	<p class="footer-logo"><img src="assets/img/footer-enoah-logo.png" alt=""/></p>
	
	<p id="back-top">
		<a href="#top"><span></span>Back to Top</a>
	</p>
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
		if ($(this).val() == 'Project Title, Name or Company') {
			$(this).val('').css('color', '#333333');
		}
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).css('color', '#777777').val('Project Title, Name or Company');
		}
	});
})


$(document).ready(function(){
	// hide #back-top first
	$("#back-top").hide();
	
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	
	
});
$.ajaxSetup ({
    cache: false
	});
</script>
</body>
</html>

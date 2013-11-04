/*
*@Project View 
*@
*/

	$(function(){
		$(".data-table").tablesorter({widthFixed: true, widgets: ['zebra']});
		$('.data-table tr, .data-table th').hover(
			function() { $(this).addClass('over'); },
			function() { $(this).removeClass('over'); }
		);
	});

	//For Projects
	var pjtstage = $("#pjt_stage").val(); 
	var pm_acc = $("#pm_acc").val(); 
	var cust = $("#customer1").val(); 
	var keyword = $("#keywordpjt").val(); 
	//alert(keyword);
	if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';

	if (document.getElementById('advance_search_pjt'))
		document.getElementById('advance_search_pjt').style.display = 'none';

		function advanced_filter_pjt(){
		$('#advance_search_pjt').slideToggle('slow');
		var  keyword = $("#keywordpjt").val();
		var status = document.getElementById('advance_search_pjt').style.display;
		
		if(status == 'none') {
			var pjtstage = $("#pjt_stage").val(); 
			var pm_acc = $("#pm_acc").val(); 
			var cust = $("#customer1").val(); 
		}
		else   {
			$("#pjt_stage").val("");
			$("#pm_acc").val("");
			$("#customer1").val("");
		}
	}

	$('#advanceFilters_pjt').submit(function() 
	{	
		var pjtstage = $("#pjt_stage").val(); 
		var pm_acc = $("#pm_acc").val(); 
		var cust = $("#customer1").val(); 
		var  keyword = $("#keywordpjt").val(); 
		if(keyword == "Project No, Project Title, Name or Company")
		keyword = 'null';
		document.getElementById('ad_filter').style.display = 'block';	
		var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
		//alert(sturl);
		$('#ad_filter').load(sturl);	
		return false;
	});

	$('#pjt_search_form').submit(function() {	
			var  keyword = $("#keywordpjt").val(); 
			if(keyword == "Project No, Project Title, Name or Company")
			keyword = 'null';
			var pjtstage = $("#pjt_stage").val(); 
			var pm_acc = $("#pm_acc").val(); 
			var cust = $("#customer1").val();  
			var sturl = "project/advance_filter_search_pjt/"+pjtstage+'/'+pm_acc+'/'+cust+'/'+encodeURIComponent(keyword);
			$('#ad_filter').load(sturl);
			return false;
	});
	
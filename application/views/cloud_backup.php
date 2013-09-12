<?php require ('tpl/header.php'); ?>
<div id="content">
	<div class="inner">
		
	</div>
</div>
<script type="text/javascript">
var file_list, error = true;

function process_list()
{
	if (file_list.length == 0)
	{
		$('#content .inner').append('<p>COMPLETE</p>');
		return;
	}
	var msg, to_process = file_list.shift();
	$.post(
			'<?php echo $this->config->item('base_url');?>z_cloud_backup/?action=backup',
			{backup_dir: to_process},
			function(data)
			{
				if (data.error)
				{
					msg = '<p style="color:red;">'+data.error+'</p>';
				}
				else
				{
					msg = '<p style="color:#ccc;">'+data.message+'</p>';
				}
				
				$('#content .inner').append(msg);
				setTimeout(process_list, 200);
			},
			'json'
	)
}

function get_file_list()
{
	var datas = {};
	if (window.confirm('Do you want to include the data files?'))
	{
		datas = {include_data: 'yes'};
	}
	
	$.getJSON(
			'<?php echo $this->config->item('base_url');?>z_cloud_backup/',
			datas,
			function(data){
				if (!data.error)
				{
					error = false;
					file_list = data.files;
					
					process_list();
				}
			}
		);
}

$(window).load(function(){ get_file_list(); });
</script>
<?php require ('tpl/footer.php'); ?>

<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class client_logo_model extends crm_model {
    
    function __construct() 
	{
        parent::__construct();
    }
	
	public function insert_file($filename, $url)
    {
		$data = array(
			'filename'		=> $filename,
			'client_url'   	=> $url
		);
		
		$logo_res = $this->get_logo();
		
		if ($logo_res==false) {
			$this->db->insert("{$this->cfg['dbpref']}" . 'client_logo', $data);
		}
		else 
		{
			@unlink('crm_data/client_logo/'.$logo_res['filename']);
			$this->db->where('id', 1);
			$this->db->update("{$this->cfg['dbpref']}" . 'client_logo', $data);		
		}
		return $this->db->insert_id();
	}
	
	public function get_logo()
	{
		$query = $this->db->get($this->login_model->cfg['dbpref'] . 'client_logo');
		$num = $query->num_rows();
		if ($num<1)
			return false;
		else
			return $query->row_array();
	}
	
	public function reset_client_logo()
	{
		$logo_res = $this->get_logo();
		if ($logo_res==false) 
		{
			return false;
		} 
		else 
		{
			@unlink('crm_data/client_logo/'.$logo_res['filename']);
			return $this->db->truncate("{$this->cfg['dbpref']}" . 'client_logo');
		}
	}

}
?>

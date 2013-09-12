<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Dns_model extends Common_model {
    function Dns_model() {
        parent::__construct();
    }
	function get_dns($id) {
		$account = $this->db->get_where($this->cfg['dbpref'].'dns', array('hostingid' => $id), 1);
	    return $account->result_array();
    }
	function get_hosting($id) {
       $account = $this->db->get_where($this->cfg['dbpref'].'hosting', array('hostingid' => $id), 1);
	   return $account->result_array();
    }
	function send_mail($data) {
		//echo "sendmail"; exit;
		//print_r($data); exit;
	    $message='<table width=500 border=1 cellpadding=0 cellspacing=0>';
	    foreach($data as $key=>$val){
			$message.='<tr><td>'.$key.'&nbsp;</td><td>'.$val.'&nbsp;</td></tr>';
	    }
	    $message.='</table>';
	    $this->email->clear();
		//$this->email->to('sarunkumar@enoahisolution.com');
		$this->email->to('ssriram@enoahisolution.com');
		$this->email->from('admin@enoahisolution.com');
		$this->email->subject('GO LIVE enoahisolution.com');
		$this->email->message($message);
		#@$this->email->send();
    }
	function update_dns($id,$data) {
		$this->db->where('hostingid', $id);
        return $this->db->update($this->cfg['dbpref'] . 'dns', $data);   
    }
    function insert_dns($data) {
        if ( $this->db->insert($this->cfg['dbpref'] . 'dns', $data) ) {
			return true;
        } else {
            return false;
        }
    }
}
?>
<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Role Model
 *
 * Provides a Manage User Role.
 *
 * @class Name 	Role_model
 * @extends		crm_model
 * @classes     Model
 * @author 		eNoah
 */


class Role_model extends crm_model {
    
	/*
	*@construct
	*@Role Model
	*/
    function Role_model() {
        parent::__construct();
    }

	/*
	*@Get Role List
	*@Role Model
	*/
    public function role_list($offset, $search) {
        
        $this->db->order_by('inactive', 'asc');
        $this->db->order_by('name', 'asc');
       
        if ($search != false) {
            $search = urldecode($search);
            $this->db->like('name', $search);
        
        }
        $customers = $this->db->get($this->cfg['dbpref'].'roles', 35, $offset); 
        return $customers->result_array();
        
    }
	
	/*
	*@Get Active Role List
	*@Role Model
	*/
	public function active_role_list() {
		$this->db->order_by('name', 'asc');
		$this->db->where('inactive', 0);
        $customers = $this->db->get($this->cfg['dbpref'].'roles');
        return $customers->result_array();
    }

	/*
	*@Count of Role Record
	*@Role Model
	*/
    public function role_count() {
        return $count = $this->db->count_all($this->cfg['dbpref'].'roles');
    }

	/*
	*@Get role record by role id
	*@Role Model
	*/
    public function get_role($id) {
		if(!$id) {
            return false;
        } else {			
			$customer = $this->db->get_where($this->cfg['dbpref'].'roles', array('id' => $id), 1);	
			$role =$this->db->order_by('masterid', 'asc');			
			$role = $this->db->get_where($this->cfg['dbpref'].'master_roles', array('role_id' => $id));	
			// echo $this->db->last_query(); exit;
			$customers['role']= $customer->result_array();
			$role = $role->result_array();			
			// print_r($role);
			return array_merge($customers,$role);
		}        
    }

	/*
	*@Update Role Record By Role Id
	*@Role Model
	*/
    public function update_role($id, $data) {
        
		$dataRole = array();
		$dataRole['name'] = $data['name'];
		$dataRole['inactive'] = $data['inactive'];
		$dataRole['modified'] = $data['modified'];
        $this->db->where('id', $id);
			
		 if ( $this->db->update($this->cfg['dbpref'].'roles', $dataRole)) {
		 
			$this->db->where('role_id', $id);
			$this->db->delete($this->cfg['dbpref'].'master_roles');
			
			 
			foreach($data['masterid'] as $key =>$val) {
			
			$data['role_id'] =$id;
			$dataMaster['role_id'] =$id;
			 
			$dataMaster['masterid']=$data['masterid'][$key];
			
			$mid=$data['masreroleid'][$key];
			
			$dataMaster['view']=$dataMaster['add']=$dataMaster['edit']=$dataMaster['delete']=0;
		 
			if(isset($data['view'][$key])) {
				  $dataMaster['view']=1;
			}
			if(isset($data['add'][$key])){
					$dataMaster['add']=1;
			}
			
			if(isset($data['edit'][$key])){
				  $dataMaster['edit']=1;
			}
			
			if(isset($data['delete'][$data['masterid'][$key]])){
				  $dataMaster['delete']=1;
			}	
		   
		    if ($this->db->insert($this->cfg['dbpref'].'master_roles', $dataMaster)) {	
				$roleUser = $this->db->insert_id();
			}  
			 
		  }
			 return $data['role_id'];
        }
        
    }

	/*
	*@Insert Role Record
	*@Role Model
	*/
    public function insert_role($data) {
	 
		$dataRole = array();
		$dataRole['name'] = $data['name'];
		$dataRole['inactive'] = $data['inactive'];
		$dataRole['created_by'] = $data['created_by'];
		$dataRole['modified_by'] = $data['modified_by'];
		$dataRole['created'] = $data['created'];
		$dataRole['modified'] = $data['modified'] ;
		 
        if ( $this->db->insert($this->cfg['dbpref'].'roles', $dataRole) ) {
			$data['role_id'] = $this->db->insert_id();

			foreach($data['masterid'] as $key =>$val) {
		
			$dataMaster = array();
			$dataMaster['role_id']=$data['role_id'];
			//$dataMaster['users_id']=$data['userid'];
			$dataMaster['masterid']=$data['masterid'][$key];
			$dataMaster['view']=$dataMaster['add']=$dataMaster['edit']=$dataMaster['delete']=0;
			if(isset($data['view'][$data['masterid'][$key]])){
				$dataMaster['view']=1;
			}
			if(isset($data['add'][$data['masterid'][$key]])){
				$dataMaster['add']=1;
			}			
			if(isset($data['edit'][$data['masterid'][$key]])){
				$dataMaster['edit']=1;
			}			
			if(isset($data['delete'][$data['masterid'][$key]])){
				$dataMaster['delete']=1;
			}			
			 if ( $this->db->insert($this->cfg['dbpref'].'master_roles', $dataMaster)) {
			// echo $this->db->last_query();
				 $roleUser = $this->db->insert_id();
			} 	
			}
			 return $data['role_id'];
        } else {
            return false;
        }        
    }

	/*
	*@Delete Role Record
	*@Role Model
	*/
    public function delete_role($id) {
        
        $this->db->where('id', $id);
        return $this->db->delete($this->cfg['dbpref'].'roles');
        
    }
	
	/*
	*@Get Role Name & Role Id 
	*@Role Model
	*/
    public function has_role( $role, $role )
    {
        $this->db->join( $this->cfg['dbpref'].'role_roles ur', 'roles.id = ur.roles_id' );
        $this->db->join( $this->cfg['dbpref'].'roles r', 'r.id = ur.roles_id' );
        return $this->get_by( array( 'r.name' => $role, 'ur.roles_id' => $role ) );
    }

	/*
	*@Find out Exist Fields 
	*@Role Model
	*/
    public function field_exists( $field )
    {
        return $this->db->field_exists( $field, $this->config->item('role_table', 'acl_auth') );
    }


	/*
	*@Check security token
	*@Role Model
	*/
    public function check_token( $token )
    {
        return ( $token === $this->reset_code );
    }
	
	/*
	*@Get Module List
	*@Role Model
	*/
	public function UserModuleList($userId) {
	
	$this->db->select('vm.masterid,vm.master_parent_id,vm.master_name,vm.controller_name,vm.links_to,mrl.role_id,vm.order_id,mrl.id as masreroleid,mrl.view,mrl.add,mrl.edit,mrl.delete');
		$this->db->from($this->cfg['dbpref'].'roles as rl');
		$this->db->join($this->cfg['dbpref'].'master_roles as mrl','rl.id=mrl.role_id');
		$this->db->join($this->cfg['dbpref'].'masters as vm','vm.masterid=mrl.masterid and vm.inactive=0');
		$this->db->join($this->cfg['dbpref'].'users as vu','vu.role_id=rl.id and vu.userid='.$userId);		
		// $this->db->order_by('vm.master_parent_id', 'desc');			
		// $this->db->order_by('vm.masterid', 'asc');
		$this->db->order_by('vm.order_id', 'asc');
		$Menuitms = $this->db->get();
		// echo $this->db->last_query(); exit;
		$menuItems = $Menuitms->result_array();
		return $menuItems;
	}

	/*
	*@Page Tree for Update User role Details 
	*@Role Model
	*/
	public function pageTree($id = false) {
		if(!empty($id)) {
			$idcondition = "rl.id=mrl.role_id and rl.id=".$id;
			$this->db->select('vm.*, mrl.id as masreroleid, mrl.add, mrl.edit, mrl.view, mrl.delete, rl.name, rl.id as roleid');
			$this->db->from($this->cfg['dbpref'].'masters as vm');	
			$this->db->join($this->cfg['dbpref'].'master_roles as mrl','vm.masterid=mrl.masterid and mrl.role_id='.$id, 'left');
			$this->db->join($this->cfg['dbpref'].'roles as rl', $idcondition, 'left');		
			$this->db->order_by('vm.master_parent_id', 'desc');
			$this->db->order_by('vm.masterid', 'asc');	
			$this->db->where('vm.master_parent_id', 0);				
			$this->db->where('vm.inactive', 0);				
			$customers = $this->db->get();
		} else {
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'masters');
			$this->db->where('master_parent_id',0);
			$this->db->order_by('masterid', 'asc');
			$customers = $this->db->get();
			//$customers = $this->db->query($sql, array($log_date, $log_user['userid']));		
		}
		
		$vertices= $customers->result_array();
		
		$i=0;
	 
		foreach($vertices as $vertice) {
			$sample = array();
				if($vertice['master_parent_id']==0) {	 
					$vertice['master_parent_id'] = NULL;
				}
			if(!isset($vertice['roleid']) or empty($vertice['roleid'])) { 
				$sample['add'] = 0;
				$sample['edit'] = 0;
				$sample['delete'] = 0;
				$sample['view'] = 0;
				$sample['roleid'] = 0;
				$sample['masreroleid'] = 0;
			}
			$vertices[$i] = array_merge($vertices[$i],$sample);
			$i++;
		}
	
		$allpages=array(0=>'');
		$subtrees = $trees = array();
		foreach ($vertices as $vertex) {
			$allpages[$vertex['masterid']]=$vertex['master_name'];
			$v = array(
				'id' => $vertex['masterid'],
				'name' => $vertex['master_name'],
				'children' => array(),
				'view' => array(),
				'add' => array(),
				'edit' => array(),
				'delete' => array(),
				'masreroleid' => array(),
				'roleid' => array(),
			);
			
			if (isset($subtrees[$vertex['masterid']])) {
				$v['children'] = $subtrees[$vertex['masterid']];
			}
			
			$v['add']=$vertex['add'];
			$v['edit']=$vertex['edit'];
			$v['delete']=$vertex['delete'];
			$v['view']=$vertex['view'];
			$v['masreroleid']=$vertex['masreroleid'];
			$v['roleid']=$vertex['roleid'];

			if ($vertex['master_parent_id'] == 0) {		
				$trees[] = $v;
			} else if (!isset($subtrees[$vertex['master_parent_id']])) {
				$subtrees[$vertex['master_parent_id']] = array($v);
			} else {
				$subtrees[$vertex['master_parent_id']][] = $v;
			}
		}
	  
		unset($subtrees);

		$ul='<ul>';
		foreach ($trees as $root) {
			$ul.= '<li style="list-style:none">'.$this->getSubtreeUL($root).'</li>';
		}
		$ul.= '</ul>';

		return $ul;
	}

	
	/*
	*@Update Role Details 
	*@Role Model
	*/
	public function getSubtreeUL(array $subtreeRoot, $level = 0)
	{
		// echo "<pre>"; print_r($subtreeRoot);
	    $html = '';
	    $all = $add = $edit = $delete = $view = '';
	    $disableStatusProfile = $disableStatusProject = $disableStatusReport = '';
		
		$html = '<div style="width:500px; padding:5px;">
		<div style="width:500px;">
		<div style="background:url(\'assets/img/folder.png\') no-repeat 3px;  padding-left:25px; height:24px; line-height:24px; ">
		 '.$subtreeRoot['name'].'<input type="hidden" name ="masterid['.$subtreeRoot['id'].']" value="'.$subtreeRoot['id'].'">';
		$html .= '<input type="hidden"  name ="masreroleid['.$subtreeRoot['id'].']" value="'.$subtreeRoot['masreroleid'].'">';
		$html .= '<input type="hidden" name ="roleid['.$subtreeRoot['id'].']" value="'.$subtreeRoot['roleid'].'"> ';
		
		if ($subtreeRoot['add'] == 1 && $subtreeRoot['view'] == 1 && $subtreeRoot['edit'] == 1 && $subtreeRoot['delete'] == 1) {
			$all= ' checked="checked"';
		} 
		if ($subtreeRoot['add'] == 1) {
			$add= ' checked="checked"';
		} 
		if ($subtreeRoot['edit'] == 1) {
			$edit= ' checked="checked"';
		} 
		if ($subtreeRoot['delete'] == 1) {
			$delete= ' checked="checked"';
		} 
		if ($subtreeRoot['view'] == 1) {
			$view= ' checked="checked"';
		}
		if ($subtreeRoot['id'] == 89) {
			$disableStatusProfile = ' disabled';
		}
		if ($subtreeRoot['id'] == 110) {
			$disableStatusProject = ' disabled';
		}
		if ($subtreeRoot['id'] == 113 || $subtreeRoot['id'] == 132) {
			$disableStatusReport = ' disabled';
		}
		$html .='<span style="width:400px;"> 
				<input type="checkbox" id="tab_chk_all-'.$subtreeRoot['id'].'" class="check" '.$all.' '.$disableStatusProfile.' '.$disableStatusProject.' '.$disableStatusReport.' name ="full"> &nbsp;All&nbsp;
				<input type="checkbox" id="tab_chk_add-'.$subtreeRoot['id'].'" name ="add['.$subtreeRoot['id'].']"'.$add.' '.$disableStatusProfile.' '.$disableStatusProject.' '.$disableStatusReport.' value="1" onclick="unSelectCreate('.$subtreeRoot['id'].');"> &nbsp;Add&nbsp; 
				<input type="checkbox" id="tab_chk_view-'.$subtreeRoot['id'].'" name ="view['.$subtreeRoot['id'].']" '.$view.' value="1" onclick="unSelectView('.$subtreeRoot['id'].');"> &nbsp;View&nbsp;
				<input type="checkbox" id="tab_chk_edit-'.$subtreeRoot['id'].'" name ="edit['.$subtreeRoot['id'].']" '.$edit.' '.$disableStatusReport.' value="1" onclick="unSelectEdit('.$subtreeRoot['id'].');"> &nbsp;Edit&nbsp;
				<input type="checkbox" id="tab_chk_del-'.$subtreeRoot['id'].'"  name ="delete['.$subtreeRoot['id'].']" '.$delete.' '.$disableStatusProfile.' '.$disableStatusReport.' value="1" onclick="unSelectDelete('.$subtreeRoot['id'].');"> &nbsp;Delete&nbsp;
				</span></div></div> </div>';
		
		if(sizeof($subtreeRoot['children'])>0) {
			foreach ($subtreeRoot['children'] as $child) {
				$html .= '<ul style="padding-left:30px;list-style:none"><li>'.$this->getSubtreeUL($child, $level + 1);
				$html .='</li></ul>';
			}
		}
		return $html;
	}

	/*
	*@Role Log History 
	*@Role Model
	*/
	public function log_history($log_date,$log_role){
		# now get the logs for the role on that day
		$sql = "SELECT *, DATE_FORMAT(`".$this->cfg['dbpref']."logs`.`date_created`, '%W, %D %M %y %h:%i%p') AS `fancy_date`
				FROM ".$this->cfg['dbpref']."logs
				LEFT JOIN `".$this->cfg['dbpref']."leads` ON `".$this->cfg['dbpref']."leads`.`lead_id` = `".$this->cfg['dbpref']."logs`.`jobid_fk`
				WHERE DATE(`".$this->cfg['dbpref']."logs`.`date_created`) = ?
				AND `roleid_fk` = ?
				ORDER BY `".$this->cfg['dbpref']."logs`.`date_created`";
			
		$q = $this->db->query($sql, array($log_date, $log_role['roleid']));
		$rs = $q->result_array();
	}
	
	/*
	*Checking duplicates
	*/
	function check_role_duplicate($tbl_cont, $condn, $tbl_name) {
		$this->db->select($tbl_cont['name']);
		$this->db->where($tbl_cont['name'], $condn['name']);
		if(!empty($condn['id'])) {
			$this->db->where($tbl_cont['id'].' !=', $condn['id']);
		}
		$res = $this->db->get($this->cfg['dbpref'].$tbl_name);
        return $res->num_rows();
	}
}

?>

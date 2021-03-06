<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * add_log
 *
 * Let you add informational or debugging info to a log
 *
 */

 class Menu {

		function formMenuList($menu_itemsmod,$showCheckBox=NULL,$searchSubMenu=NULL,$parent_id=NULL) { 
			$menulist = explode('#',$menu_itemsmod);
			//for menu name associated values
			//$menulist=array_reverse($menulist);
			$fieldname = array('masterid','master_parent_id','master_name','lable_name','links_to','role_id','order_id','masreroleid','view','add','edit','delete');

			for($j=0;$j<count($menulist);$j++) {
				$menu_items_val[] = explode(',',$menulist[$j]);		 
				$menu_items_val[$j] = array_combine($fieldname,$menu_items_val[$j]);		 
			}
			//echo $this->db->last_query();
			$vertices= $menu_items_val;

			$i=0;
			//forming submenu like tree structure


			foreach($vertices as $vertice) {

			$sample = array();
				if($vertice['master_parent_id']==0) {	 
					$vertice['master_parent_id'] = NULL;
				}
			if(!isset($vertice['role_id']) or empty($vertice['role_id'])) { 
				$sample['add'] = 0;
				$sample['edit'] = 0;
				$sample['delete'] = 0;
				$sample['view'] = 0;
				$sample['role_id'] = 0;
				$sample['masreroleid'] = 0;
				 
			}
				$vertices[$i] =array_merge($vertices[$i],$sample);
				$i++;
			}

			$allpages=array(0=>'');
			$subtrees = $trees = array();
			foreach ($vertices as $vertex) {
				$allpages[$vertex['masterid']]=$vertex['master_name'];
				$v = array(
					'id' => $vertex['masterid'],
					'name' => $vertex['master_name'],
					'links_to' => $vertex['links_to'],
					'lable_name' => $vertex['lable_name'],
					'master_parent_id' => $vertex['master_parent_id'],
					'children' => array(),
					'view' => array(),
					'add' => array(),
					'edit' => array(),
					'delete' => array(),
					'masreroleid' => array(),
					'role_id' => array(),
				);

						
				if (isset($subtrees[$vertex['masterid']])) {
					$v['children'] = $subtrees[$vertex['masterid']];
				}
				
				$v['add']=$vertex['add'];
				$v['edit']=$vertex['edit'];
				$v['delete']=$vertex['delete'];
				$v['view']=$vertex['view'];
				$v['masreroleid']=$vertex['masreroleid'];
				$v['master_parent_id']=$vertex['master_parent_id'];
				$v['role_id']=$vertex['role_id'];
				$v['links_to']=$vertex['links_to'];

				if ($vertex['master_parent_id'] == 0) {					 
					$trees[] = $v;
				}else if (!isset($subtrees[$vertex['master_parent_id']])) {
					$subtrees[$vertex['master_parent_id']] = array($v);
				}else {
					$subtrees[$vertex['master_parent_id']][] = $v;
				}
			}

			 //submenu display after click on all pages
			if($searchSubMenu !=NULL){
			$i=0;
			 
			foreach($trees as $tree){		

			  $key = strcmp(strtolower($searchSubMenu), strtolower($tree['lable_name']));
			if($key==0) {
				$output = array_slice($trees, $i, 1);	
			}
			$i++;
			}
			//checking parent menu need to display in child menu too when no submenu for submenu.
			if(!empty($output)) {
				$trees=$output; 
			}else{
				$l=0;
				foreach($trees as $tree) {		
			  $key = strcmp($parent_id,$tree['id']);

			if($key==0) {
				$output = array_slice($trees, $l, 1);	
			}		
				$l++;		
			}		
				$trees=$output; 
			}

			}

			unset($subtrees);

			for($k=0;$k<count($trees);$k++) {
			if(!$trees[$k]['id']){
			 unset($trees[$k]);		 
				}
			}

			$ul='<div style="clear:both;"></div>';
			if($searchSubMenu==NULL){
				$ul .='<ul class="menuStyle"><li style="list-style:none"><a href="dashboard" '.$class.' >Home</a></li>';
				 
			}else{
			//$ul .='<ul>';
			}
			foreach ($trees as $root) {
			 if($searchSubMenu==NULL){
				  $ul.= '<li style="list-style:none">';
				  }
				   if($searchSubMenu==NULL){
				 $ul .=  $this->getSubtreeULMenu($root,0,$searchSubMenu);
				 }else{
				  
				 $ul .= $this->getSubtreeSubULMenu($root,0,$searchSubMenu);
				 }
				 if($searchSubMenu==NULL){
					$ul.= '</li>';
				 }
			}
			 if($searchSubMenu==NULL){
				$ul.= '</ul>';
			}
				 
			return $ul;
		}


		function getSubtreeULMenu(array $subtreeRoot, $level = 0,$searchSubMenu=NULL)
		{	
			$html ='';		 
			if(isset($subtreeRoot['name']) && $subtreeRoot['view']==1) {
			$qurystring =explode('/',$_SERVER['REQUEST_URI']);
			$class='';
			if($qurystring[2]==$subtreeRoot['links_to']){
				$class='class=""';
			}
			$html = '<a href="'.$subtreeRoot['links_to'].'" '.$class.' >'.$subtreeRoot['name'].'</a>';
			 if(!empty($subtreeRoot['children'])){
			$html .= '<ul class="left-menu">';
			
				foreach ($subtreeRoot['children'] as $child) {
				
					$html .= '<li style="list-style:none">'.$this->getSubtreeULMenu($child, $level + 1,$searchSubMenu).'</li>';
					
				} 
			$html .= '</ul>';
			}
			}
			return $html; 
		}

		function getSubtreeSubULMenu(array $subtreeRoot, $level = 0,$searchSubMenu=NULL)
		{	
			$html ='';		 
			if(isset($subtreeRoot['name']) && $subtreeRoot['view']==1) {
			
				 if(empty($subtreeRoot['children']) && $subtreeRoot['master_parent_id']!=0){
				 $str='';
				 if($subtreeRoot['name']=='Create New Lead'){
					$str =  'onclick="$(\'#lead-init-form:not(:visible)\').slideDown(300); return false;" href="#"';
				 }
					$html = '<a '.$str.' href="'.$subtreeRoot['links_to'].'">'.$subtreeRoot['name'].'</a>';
					}
			 if(!empty($subtreeRoot['children'])){
					$html .= '<ul class="topstrip">';
				 }
				foreach ($subtreeRoot['children'] as $child) {
				
				 if(!empty($subtreeRoot['children'])){
					$html .= '<li style="list-style:none">';
					}
					$html .= $this->getSubtreeSubULMenu($child, $level + 1,$searchSubMenu);
					 if(!empty($subtreeRoot['children'])){
						$html .='</li>';
					}
					
				} 
				 if(!empty($subtreeRoot['children'])){
					$html .= '</ul>';
			}
			}
			return $html; 
		}



		function formSubMenuList($masterId = NULL) {
				if($masterId!=NULL && $masterId !='') {
				$ci =&get_instance(); 
				$ci->load->database(); 
				$this->cfg = $this->config->item('crm');
				
				$ci->db->select('vm.masterid,vm.master_parent_id,vm.master_name,vm.controller_name,vm.links_to  from '.$this->cfg['dbpref'].'masters  as vm where vm.master_parent_id ='.$masterId .' order by vm.master_parent_id desc,vm.masterid asc');
				
				$SubMenuitms = $ci->db->get();
				//echo $ci->db->last_query();				 
				$submenus = $SubMenuitms->result_array();
					/*$fieldname = array('masterid','master_parent_id','master_name','lable_name','links_to','role_id','order_id','masreroleid','view','add','edit','delete');

					for($j=0;$j<count($menulist);$j++) {
							$menu_items_val[] = explode(',',$menulist[$j]);		 
							$menu_items_val[$j] = array_combine($fieldname,$menu_items_val[$j]);		 
					}*/
				 
				$str = "<ul class='topstrip'>";
				foreach($submenus as $submenu){
					$str .= "<li style='list-style:none'><a href ='". $submenu['links_to']."'>".$submenu['master_name']."</a></li>";
				}
				$str .="</ul>";
				 return $str;
				 }
		}

		function formMasterDetail($string = NULL, $id=false) { 
				if($string!=NULL && $string !='') {
					$ci =&get_instance(); 
					$ci->load->database(); 
					$this->cfg = $this->config->item('crm');
					
					$ci->db->select('vm.master_parent_id ,mrl.view,mrl.add,mrl.edit,mrl.delete from '.$this->cfg['dbpref'].'masters as vm JOIN  master_roles as mrl on mrl.masterid= vm.master_parent_id  where vm.controller_name ="'.$string.'" and mrl.role_id="'.$id.'" ');
					$ci->db->limit('1','0');
					$masterId = $ci->db->get();
					// echo $ci->db->last_query(); exit;
					$master = $masterId->result_array();
					return $master;
				} 
		} 

		function load($template = '', $view = '' , $view_data = array(), $return = FALSE)
        {
			if ($this->session->userdata('logged_in') == TRUE) { 
				$userdata = $this->session->userdata('logged_in_user');
				$menu_itemsmod = $this->session->userdata('menu_item_list');
				$menulist =  $this->formMenuList($menu_itemsmod,true,NULL);
			}
            $this->CI =& get_instance();
            $this->set('contents', $this->CI->load->view($view, $view_data, TRUE));            
            return $this->CI->load->view($template, $menulist, $return);
        }
	
}	
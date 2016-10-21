<?php
	/*
	* @author priya
	* @created 04.07.2016
	* @modified on 17.10.2016 by @author priya
	*/

	/**
	* File Description
	* This class is mainly for uploading xml into database.
	* Related to table "crm_project_plan".
	* It performs the following methods
	__construct,do_upload,get_resources,get_parent,Xml2Array
	**/
	
class Upload extends crm_controller 
{
		
		public $userdata;
		
		//initial declaration for the class
		public function __construct() 
		{ 
			parent::__construct(); 
			$this->userdata = $this->session->userdata('logged_in_user');
			$this->load->model('request_model');
			$this->load->model('projects/dashboard_model');
			$this->load->helper(array('form', 'url')); 
		}

		// upload xml files to database 
		public function do_upload() 
		{
			$project_id=$_GET['project_id'];
			$return=array();
			$sourcePath = $_FILES['xmlfile']['tmp_name'];       // Storing source path of the file in a variable
			
			
			//creating files folder name
			$f_dir = UPLOAD_PATH.'files/';
			if (!is_dir($f_dir)) {
				mkdir($f_dir);
				chmod($f_dir, 0777);
			}
			
			//creating project_id folder name
			$f_dir = $f_dir.$project_id;
			if (!is_dir($f_dir)) {
				mkdir($f_dir);
				chmod($f_dir, 0777);
			}
			
			$file_name = preg_replace('/\s+/', '_', $_FILES['xmlfile']['name']);
			//replace white spaces with "_"
			
			$targetPath = UPLOAD_PATH.'files/'.$project_id.'/'.$file_name; // Target path where file is to be stored
		
			if(move_uploaded_file($sourcePath,$targetPath))
			{ 	
				//if files moved to target path
				
				$this->insert_file_log($project_id,$file_name);
				//insert files log to database
				
				$file=$targetPath;
				$strContents = file_get_contents($file);
				$strDatas = $this->Xml2Array($strContents);
				if(isset($strDatas['Project']) && isset($strDatas['Project']['Tasks']) && isset($strDatas['Project']['Assignments']) && isset($strDatas['Project']['Resources']))
				{ //validate required fields
					$records=$strDatas['Project']['Tasks']['Task'];
					$assignment=$strDatas['Project']['Assignments']['Assignment'];
					$resources=$strDatas['Project']['Resources']['Resource'];
					$i=1;

					$this->db->select('*');
					$this->db->from($this->cfg['dbpref'].'project_plan');
					$this->db->where('project_id', $project_id);
					$sql_query = $this->db->get();
					if($sql_query->num_rows()>0)
					{ //if there is no empty records
						$sql_que="Delete from ".$this->cfg['dbpref']."project_plan WHERE project_id='$project_id'";
						$res=$this->db->query($sql_que);
					}

					if(count($records)!=0)
					{ //if there is no empty records
						foreach($records as $list)
						{
							if(isset($list['Work']) && isset($list['Duration']) && isset($list['UID']) && isset($list['WBS']) && isset($list['Start']) && isset($list['Finish']) && isset($list['PercentComplete']))
							{	//validate required fields
								if($i>1)
								{	//avoid first cell in importing
									$uid=$list['UID'];
									$task_name=mysql_real_escape_string($list['Name']);
									$WBS=$list['WBS'];
									$duration=split('PT',$list['Work']);
									$split_duration=split('PT',$duration[1]);
									$duration_hours=split('H',$split_duration[0]);
									$duration_in_hours=$duration_hours[0];
									$start_date=date("Y-m-d H:i:s",strtotime($list['Start']));
									$finish_date=date("Y-m-d H:i:s",strtotime($list['Finish']));
									$estimated_start=date("Y-m-d H:i:s",strtotime($list['ManualStart']));
									$estimated_end=date("Y-m-d H:i:s",strtotime($list['ManualFinish']));
									$complete_percent=$list['PercentComplete'];
									$resource_names=$this->get_resources($list['UID'],$assignment,$resources);
									$parent_id=$this->get_parent($WBS,$project_id);
									$predecessor='';
									if(isset($list['PredecessorLink']))
									{ //if data exists
										$prede_array=$list['PredecessorLink'];
										if(count($prede_array)==count($prede_array,COUNT_RECURSIVE)) 
										{ //if count is equal to recursive array
											$predecessor=$prede_array['PredecessorUID'];
										}
										else
										{	//if count is not equal to recursive array
											foreach($prede_array as $each_array)
											{
												$prede[]=$each_array['PredecessorUID'];
											}
											$predecessor=implode(',',$prede);
										}
									}

									$sql="INSERT INTO ".$this->cfg['dbpref']."project_plan( 	uid,project_id,task_id,parent_id,task_name,duration,start_date,end_date,predecessors,resource_name,estimated_start,estimated_end,complete_percentage) VALUES ('$uid','$project_id','$WBS','$parent_id','$task_name','$duration_in_hours','$start_date','$finish_date','$predecessor','$resource_names','$estimated_start','$estimated_end','$complete_percent')";
									//inserting xml data to table
									$result=$this->db->query($sql);
									//execute query
									$this->dashboard_model->update_project_thermometer($project_id);
									//update thermometer status
								}
							}
							else
							{
								$return['result']='error'; //error in loop or required fields not matched
								echo json_encode($return);exit;
							}
							$i++;
						}
					}

					$return['result']='success';
				}
				else
				{ //if error in validation
					$return['result']='error';
				}
			}
			else
			{	//if there is an error while moving files to target path
				$return['result']='failure';
			}
			echo json_encode($return);
			//return response in terms of json
		}
		
		//get resources for the task
		function get_resources($uid,$assignment,$resources)
		{
			$resource_id='';$resource_name='';
			if(count($assignment)!=0)
			{ //if array is not empty
				foreach($assignment as $each_assignment)
				{
					if($each_assignment['TaskUID']==$uid)
					{ //if id matches the given id
						$resource_id=$each_assignment['ResourceUID'];
						if(count($resources)!=0)
						{ //if array is not empty
							foreach($resources as $each_resources)
							{
								if($each_resources['UID']==$resource_id)
								{ //if resource_id matches the given resource_id
									$resource_name=$each_resources['Name'];
								}
							}
						}
					}
				}
			}
			return $resource_name;
		}
		
		//get resources for the task
		function get_parent($task_id,$project_id)
		{
			$parent_id=0;
			$task_id=substr($task_id, 0, strripos($task_id, '.'));
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'project_plan');
			$this->db->where('project_id', $project_id);
			$this->db->where('task_id', $task_id);
			$sql = $this->db->get();
			if($sql->num_rows() > 0 )
			{//if array is not empty
				$row = $sql->row_array();
				$parent_id=$row['id'];
			}
			return $parent_id;
		}

		//convert xml to an array
		function Xml2Array($contents, $get_attributes=1, $priority = 'tag') 
		{
			if(!$contents) return array();

			if(!function_exists('xml_parser_create')) {
				//print "'xml_parser_create()' function not found!";
				return array();
			}

			//Get the XML parser of PHP - PHP must have this module for the parser to work
			$parser = xml_parser_create('');
			xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parse_into_struct($parser, trim($contents), $xml_values);
			xml_parser_free($parser);

			if(!$xml_values) return;//Hmm...

			//Initializations
			$xml_array = array();
			$parents = array();
			$opened_tags = array();
			$arr = array();

			$current = &$xml_array; //Refference

			//Go through the tags.
			$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
			foreach($xml_values as $data) 
			{
				unset($attributes,$value);//Remove existing values, or there will be trouble

				//This command will extract these variables into the foreach scope
				// tag(string), type(string), level(int), attributes(array).
				extract($data);//We could use the array by itself, but this cooler.

				$result = array();
				$attributes_data = array();

				if(isset($value)) 
				{
					if($priority == 'tag') $result = $value;
					else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
				}

				//Set the attributes too.
				if(isset($attributes) and $get_attributes)
				{
					foreach($attributes as $attr => $val) 
					{
						if($priority == 'tag') $attributes_data[$attr] = $val;
						else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
					}
				}

				//See tag status and do the needed.
				if($type == "open") 
				{	//The starting of the tag '<tag>'
					$parent[$level-1] = &$current;
					if(!is_array($current) or (!in_array($tag, array_keys($current)))) 
					{ 
						//Insert New tag
						$current[$tag] = $result;
						if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
						$repeated_tag_index[$tag.'_'.$level] = 1;

						$current = &$current[$tag];

					} 
					else 
					{ 
						//There was another element with the same tag name

						if(isset($current[$tag][0])) 
						{
							//If there is a 0th element it is already an array
							$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
							$repeated_tag_index[$tag.'_'.$level]++;
						} 
						else 
						{
							//This section will make the value an array if multiple tags with the same name appear together
							$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
							$repeated_tag_index[$tag.'_'.$level] = 2;

							if(isset($current[$tag.'_attr'])) 
							{ 
								//The attribute of the last(0th) tag must be moved as well
								$current[$tag]['0_attr'] = $current[$tag.'_attr'];
								unset($current[$tag.'_attr']);
							}
						}
						$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
						$current = &$current[$tag][$last_item_index];
					}

				} 
				elseif($type == "complete") 
				{ 
					//Tags that ends in 1 line '<tag />'
					//See if the key is already taken.
					if(!isset($current[$tag])) 
					{ 
						//New Key
						$current[$tag] = $result;
						$repeated_tag_index[$tag.'_'.$level] = 1;
						if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
					} 
					else 
					{ 
						//If taken, put all things inside a list(array)
						if(isset($current[$tag][0]) and is_array($current[$tag])) 
						{
							//If it is already an array...
							// ...push the new element into that array.
							$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

							if($priority == 'tag' and $get_attributes and $attributes_data) {
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
							}
							$repeated_tag_index[$tag.'_'.$level]++;
						} 
						else 
						{ 
							//If it is not an array...
							$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
							$repeated_tag_index[$tag.'_'.$level] = 1;
							if($priority == 'tag' and $get_attributes) 
							{
								if(isset($current[$tag.'_attr'])) 
								{ 
									//The attribute of the last(0th) tag must be moved as well
									$current[$tag]['0_attr'] = $current[$tag.'_attr'];
									unset($current[$tag.'_attr']);
								}
								if($attributes_data) {
									$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
								}
							}
							$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
						}
					}
				} 
				elseif($type == 'close') 
				{ 	//End of tag '</tag>'
					$current = &$parent[$level-1];
				}
			}

			return($xml_array);
		}
		
		//insert file log and files directory
		public function insert_file_log($project_id,$file_name)
		{
			$this->db->select('*');
			$this->db->from($this->cfg['dbpref'].'file_management');
			$this->db->where('lead_id', $project_id);
			$this->db->like('folder_name', 'Project Plans');
			$sql = $this->db->get();
			if($sql->num_rows() > 0 )
			{//if array is not empty
				$row = $sql->row_array();
				$folder_id=$row['folder_id'];
				//get project_plan folder id
			}
			else
			{ //if array is empty
				$this->db->select('*');
				$this->db->from($this->cfg['dbpref'].'file_management');
				$this->db->where('lead_id', $project_id);
				$this->db->where('folder_name', $project_id);
				$sql = $this->db->get();
				if($sql->num_rows() > 0 )
				{
					$row = $sql->row_array();
					$folder_id=$row['folder_id'];
				}
				//select root folder id
			}
			
			
			$lead_files['lead_files_name']		 = $file_name;
			$lead_files['lead_files_created_by'] = $this->userdata['userid'];
			$lead_files['lead_files_created_on'] = date('Y-m-d H:i:s');
			$lead_files['lead_id'] 				 = $project_id;
			$lead_files['folder_id'] 			 = $folder_id; //get here folder id from file_management table.
			$insert_file						 = $this->request_model->insert_new_row('lead_files', $lead_files); 
			//insert rows to lead files
			
			//LOG HISTORY
			$logs['jobid_fk']=$project_id;
			$logs['userid_fk']=$this->userdata['userid'];
			$logs['date_created']=date('Y-m-d H:i:s');

			$condn = array("lead_id"=>$project_id);
			$project_info = $this->request_model->get_record("leads", $condn);

			$logs['log_content']=$file_name.' is uploaded for Gantt chart.';
			$logs['attached_docs']=$file_name;
			$insert_logs=$this->request_model->insert_row('logs', $logs);
		}
	} 
?>
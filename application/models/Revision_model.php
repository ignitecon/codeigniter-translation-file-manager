<?php 
class Revision_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	
		$this->load->database();
		$this->load->helper(array('url', 'directory'));
	}

	public function getRevisionsWithUsers()
	{
//		$this->db->where('userid', $userid);
//		$query = $this->db->get('users');
//		$user = $query->row_array();
//		if(empty($user)) return array();

		$query = $this->db->get('revisions');
		$revisions = $query->result_array();
			
//		if($user['is_global_admin'] == 'yes') return $revisions;
			
		
		foreach ($revisions as &$revision)
		{
			$revision_id = $revision['id'];

			if(!$this->db->table_exists('revision_'.$revision_id.'_user'))
			{
				$revision['users'] = array();
				continue;
			}
			
			$query = $this->db->get('revision_'.$revision_id.'_user');
			$revision_users = $query->result_array();
			
			$revision['users'] = $revision_users;
		}
		
		return $revisions;
	}
		
	public function setLatestRevisionIntoSession()
	{
		$revisionsWithUsers = $this->getRevisionsWithUsers();
		$relatedRevisions = array();

		if($this->session->userdata('is_global_admin') == 'yes') 
		{
			$relatedRevisions = $revisionsWithUsers;
		}
		else 
		{
			foreach ($revisionsWithUsers as $revisionWithUser)
			{
				foreach ($revisionWithUser['users'] as $revisionUser)
				{
					if(in_array($this->session->userdata('id'), array($revisionUser['translator_id'], $revisionUser['proofer_id'], $revisionUser['moderator_id'])))
					{
						$relatedRevisions[] = $revisionWithUser; break;
					} 
				}
			}
		}
			
		if(!empty($relatedRevisions))
		{
			$this->session->set_userdata('related_revisions', $relatedRevisions);
			
			if($this->session->has_userdata("current_revision"))
			{
				$current_revision = $this->session->userdata("current_revision");
				$found = false;
				foreach ($revisionsWithUsers as $revision)
				{
					if($revision['id'] == $current_revision['id'])
					{
						$this->session->set_userdata("current_revision", $revision);
						$found = true;
						break;
					}
				}
				
				if(!$found)
				{
					$this->session->set_userdata('current_revision', $relatedRevisions[0]);
				}
			}
			else 
			{
				$this->session->set_userdata('current_revision', $relatedRevisions[0]);
			}
		}
		else 
		{
			$this->session->unset_userdata('current_revision');
			$this->session->unset_userdata('related_revisions');
		}
	}
	
	public function getRevisionStatistics($rev_id, $userinfo)
	{
		$revision_user_table = 'revision_'.$rev_id.'_user';

		if(!$this->db->table_exists($revision_user_table))
		{
			$languages = array();
		}
		else 
		{
			$query = $this->db->get($revision_user_table);
			$languages = $query->result_array();
		}		
		
		$statistics = array();
		foreach ($languages as $language)
		{
			if( $userinfo['is_global_admin'] == 'no' && !in_array($userinfo['id'], array($language['translator_id'], $language['proofer_id'], $language['moderator_id'])) ) continue;
			
			$revision_repo_table = strtolower('revision_'.$rev_id.'_repo_'.$language['language_id']);
			
			if(!$this->db->table_exists($revision_repo_table)) $lang_status = array();
			else 
			{
				$this->db->select(implode(', ', array('path', 'translator_status', 'translator_words', 'translator_keys', 
									'proofer_status', 'proofer_words', 'proofer_keys', 'total_keys', 'total_empty_keys')));
				$query = $this->db->get($revision_repo_table);
				$lang_status = $query->result_array();
			}

			$language['status'] = $lang_status;
			$statistics[] = $language;
		}
		
		return $statistics;
	}
	
	public function doOperation($rev_id, $rev_user_id, $operation)
	{
		if($operation == 'approve') $status = 'Approved'; 
		if($operation == 'archive') $status = 'Archived'; 
		if($operation == 'publish') $status = 'Published'; 
		
		if(!isset($status)) return array('Unknown operation name - '.$operation);
		
		$revision_user_table = 'revision_'.$rev_id.'_user';
		
		$this->db->where('id', $rev_user_id);
		$this->db->update($revision_user_table, array('lang_status' => $status));
		
		return array();
	}
	
/////////////////////////////////////////////////////////////////////////		
		
	public function getLangLocation($language = '')
	{
		$open_revision = $this->getOpenRevision();

		if($language == '')
		{
			return $open_revision['lang_root_dir'];
		}
		
		$dir_map = json_decode($open_revision['lang_dir_map'], TRUE);
		
		if(!array_key_exists($language, $dir_map)) return null;
		
		return $open_revision['lang_root_dir'].$dir_map[$language];
	}
	
	public function checkRevisionDuplicate($condition, $rev_id)
	{
		$this->db->where($condition);
		if(!empty($rev_id)) $this->db->where("id != ", $rev_id);
		
		$query = $this->db->get('revisions');
		
		$revision = $query->row_array();
		
		if(empty($revision)) return true;
		
		return false;
	}
	
	public function getOpenRevision()
	{
		$this->db->where('status', 'Open');
		$query = $this->db->get('revisions');
		$open_revision = $query->row_array();
		
		return $open_revision;
	}
	
	public function getOpenRevisionName()
	{
		$open_revision = $this->getOpenRevision();
		if(empty($open_revision)) return null;
		
		$revision_name = $open_revision['revision_name'];
		
		return $revision_name;
	}
	
	public function getOpenRevisionUsers()
	{
		$revision_name = $this->getOpenRevisionName();
		if(!$revision_name) return array();
		
		$revision_user_table = 'revision_'.strtolower($revision_name).'_user';
		
		$query = $this->db->get($revision_user_table);
		$open_revision_users = $query->result_array();
		
		return array('revision_name' => $revision_name, 'users' => $open_revision_users);
	}

	public function saveRevisionBase($rev_id, $rev_name, $master_lang_id, $lang_root, $master_lang_root, $target_ci_url)
	{
		$this->db->where('id', $rev_id);
		$base_info = array(	'revision_name' => $rev_name,
							'master_lang_id' => $master_lang_id,
							'lang_root_dir' => $lang_root,
							'master_lang_root_dir' => $master_lang_root,
							'target_ci_url' => $target_ci_url,
							'status' => 'Base Completed');
		
		$this->db->update('revisions', $base_info);
	}
	
	public function removeRevision($rev_id)
	{
		$this->load->dbforge();
		$prefix = 'revision_'.$rev_id;
		$tables = $this->db->list_tables();
		
		foreach ($tables as $table)
		{
			if(strncmp($prefix, $table, strlen($prefix)) != 0) continue;
			$this->dbforge->drop_table($table);	
		}
		
		$this->db->delete('revisions', array('id' => $rev_id));
	}
	
	public function createNewRevision($revision_name)
	{
		// check duplicated revision name
		$this->db->where('revision_name', $revision_name);
		$query = $this->db->get('revisions');
		$result = $query->result_array();
		if(empty($result))
		{
			$this->db->where('is_default', 'yes');
			$query = $this->db->get('languages');
			$master_lang = $query->row_array(); 
			
			$this->db->insert('revisions', array( 'revision_name' => $revision_name, 'master_lang_id' => $master_lang['id'], 'status' => 'Created' ));
			return array('errors' => array(), 'rev_id' => $this->db->insert_id());
		}
		else 
		{
			return array('errors' => array('Revision name already exists.'));
		}
	}
	
	public function getPathPageURLs($rev_id)
	{
		$revision_url_path_table = "revision_".$rev_id."_url_path";
		
		if(!$this->db->table_exists($revision_url_path_table)) return array();
		
		$query = $this->db->get($revision_url_path_table);
		
		$results = $query->result_array();
		
		$pathPageURLs = array();
		
		foreach ($results as $result)
		{
			$pathPageURLs[$result['lang_file']] = json_decode($result['path_urls'], TRUE);
		}
		
		return $pathPageURLs;
	}

	public function savePathPageURLs($rev_id, $path_url_list)
	{
		$revision_url_path_table = "revision_".$rev_id."_url_path";
		
		if(!$this->db->table_exists($revision_url_path_table))
		{
			$this->load->dbforge();

			$fields = array
			(
				'id' 			=> array( 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
				'lang_file' 	=> array( 'type' => 'VARCHAR', 'constraint' => '255' ),
				'path_urls' 	=> array( 'type' => 'TEXT',	'null' => TRUE )
			);
			
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$attributes = array('ENGINE' => 'InnoDB');
			$this->dbforge->create_table($revision_url_path_table, FALSE, $attributes);
		}
		
		$this->db->truncate($revision_url_path_table);

		foreach ($path_url_list as $path_url)
		{
			if(isset($path_url['url_list']))
			{
				$path_urls = json_encode($path_url['url_list']);
				$this->db->insert($revision_url_path_table, array(	'lang_file' => $path_url['path'], 
																	'path_urls' => $path_urls ));
			}
		}
	}
	
	public function saveTeamConfigure($rev_id, $team_entry_list, $is_global_admin)
	{
		$revision_user_table = "revision_".$rev_id."_user";
					
		if(!$this->db->table_exists($revision_user_table))
		{
			$this->load->dbforge();

			// Create a new revision user table for newly created revision
			
			$revision_user_table = 'revision_'.$rev_id.'_user';
			$fields = array
			(
				'id' 			=> array( 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
				'language_id'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
				'translator_id'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
				'proofer_id' 	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
				'moderator_id'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
				'path' 			=> array( 'type' => 'VARCHAR', 'constraint' => '100' ),
				'lang_status' 	=> array( 'type' => 'VARCHAR', 'constraint' => '100', 'default' => 'In Progress' )
			);
			
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$attributes = array('ENGINE' => 'InnoDB');
			$this->dbforge->create_table($revision_user_table, FALSE, $attributes);
		}
		
		if($is_global_admin == 'yes') 
		{
			$this->db->truncate($revision_user_table);

			foreach ($team_entry_list as $team_entry)
			{
				unset($team_entry['id']);
				$this->db->insert($revision_user_table, $team_entry );
			}
		}
		else
		{
			foreach ($team_entry_list as $team_entry)
			{
				$this->db->where('id', $team_entry['id']);
				unset($team_entry['id']);
				$this->db->update($revision_user_table, $team_entry );
			}
			
		}
	}
	
}
	
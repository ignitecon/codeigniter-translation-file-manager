<?php 
	class Settings_model extends CI_Model 
	{
		function __construct()
		{
			parent::__construct();
		
			$this->load->database();
			$this->load->helper(array('url', 'directory'));
		}

		function getAllSettings()
		{
			$query = $this->db->get('settings');
			
			$entries = $query->result_array();
			$settings = array();
			
			foreach ($entries as $entry)
			{
				$settings[$entry['key']] = $entry['value'];
			}
			
			return $settings;
		}
		
		function getLanguages($conditions = array())
		{
			if(!empty($conditions)) $this->db->where($conditions);
			$this->db->order_by('name', 'ASC');
			
			$query = $this->db->get('languages');
			
			return $query->result_array();
		}
				
		public function updateLanguage($langinfo)
		{
			$this->db->where('id', $langinfo['id']);
			unset($langinfo['id']);
			$this->db->update('languages', $langinfo);
		}
		
		public function createLanguage($langinfo)
		{
			if(isset($langinfo['id'])) unset($langinfo['id']);
			$this->db->insert('languages', $langinfo);
			
			return $this->db->insert_id();
		}
		
		public function removeLanguage($condition)
		{
			$this->db->delete('languages', $condition); 
		}
		
		public function getUserRoles()
		{
			$query = $this->db->get('roles');
			$roles = $query->result_array();
			return $roles;
		}
		
	}
	
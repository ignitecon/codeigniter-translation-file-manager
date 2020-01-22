<?php 
	class User_model extends CI_Model 
	{
		function __construct()
		{
			parent::__construct();
		
			$this->load->database();
			
		}
      
		public function getUsers($conditions = array(), $fields = '', $orderby = array())
		{
			if(!empty($conditions)) $this->db->where($conditions);
			if(!empty($fields)) 	$this->db->select($fields);
			if(!empty($orderby)) 	$this->db->order_by($orderby[0], $orderby[1]);
			
			$query = $this->db->get('users');
			
			return $query->result_array();
		}
		
		public function updateUser($userinfo)
		{
			$this->db->where('id', $userinfo['id']);
			if($userinfo['refer_id'] == $userinfo['id']) unset($userinfo['refer_id']);
			unset($userinfo['id']);
			if(!isset($userinfo['language_ids'])) $userinfo['language_ids'] = array();
			$userinfo['language_ids'] = json_encode($userinfo['language_ids']);
			
			if($userinfo['password'] == '************') unset($userinfo['password']);
			else $userinfo['password'] = hash('sha512', $userinfo['password'], false);
			
			$this->db->update('users', $userinfo);
		}
		
		public function resetPassword($userinfo)
		{
			$this->db->where('id', $userinfo['id']);
			unset($userinfo['id']);

			$userinfo['pwd_token'] = '';
			$this->db->update('users', $userinfo);
		}
		
		public function saveResetPwdRand($user_email, $token)
		{
			$this->db->where('email', $user_email);
			$this->db->update('users', array('pwd_token' => $token));
		}
		
		public function createUser($userinfo)
		{
			if(isset($userinfo['id'])) unset($userinfo['id']);
			$userinfo['language_ids'] = json_encode($userinfo['language_ids']);
			
			if($userinfo['password'] == '************') unset($userinfo['password']);
			else $userinfo['password'] = hash('sha512', $userinfo['password'], false);
			
			$this->db->insert('users', $userinfo);
			
			return $this->db->insert_id();
		}
		
		public function removeUser($condition)
		{
			$this->db->delete('users', $condition); 
		}
		
//		public function getPermissionForOpenRevision($user, $openRevisionUsers)
//		{
//			$rev_users = $openRevisionUsers['users'];
//			$permission = array('translator' => array(), 'proofer' => array(), 'moderator' => array());
//			
//			foreach ($rev_users as $rev_user)
//			{
//				if($rev_user['translator']	== $user['userid']) $permission['translator'][]	= $rev_user['language'];
//				if($rev_user['proofer']		== $user['userid']) $permission['proofer'][] 	= $rev_user['language'];
//				if($rev_user['moderator']	== $user['userid']) $permission['moderator'][] 	= $rev_user['language'];
//			}
//			
//			return $permission;
//		}
		
	}
	
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('url'));
		$this->load->library('session');
		if(!$this->session->has_userdata('email'))
		{
			redirect('/'); return;
		}

		$this->load->model('settings_model');
		$this->load->model('user_model');
		$this->load->model('revision_model');
		$this->load->model('transhome_model');
		
		$this->revision_model->setLatestRevisionIntoSession();
	}
	
	public function index()		{ if($this->session->userdata('is_global_admin') == 'yes') $this->base(); else $this->team(); }
	public function base()		{ $this->invoke_page('base'); }
	public function page()		{ $this->invoke_page('page'); }
	public function team()		{ $this->invoke_page('team'); }
	public function approve()	{ $this->invoke_page('approve'); }
	public function users()		{ $this->invoke_page('users'); }
	public function revisions()	{ $this->invoke_page('revisions'); }
	public function languages()	{ $this->invoke_page('languages'); }
	public function cleanup()	{ $this->invoke_page('cleanup'); }
	
	public function invoke_page($tab_name)
	{
		$this->load->view('header', array('title' => 'Site Translator - Control Panel'));

		$languages = $this->settings_model->getLanguages();
		$all_users = $this->user_model->getUsers();
		$users = $this->user_model->getUsers(array('refer_id' => $this->session->userdata('id')), '', array('username', 'ASC'));

		$parameters = array('tab_name' => $tab_name);
		
		if($this->session->has_userdata('current_revision'))
		{
			$current_revision = $this->session->userdata('current_revision');
			
			if($this->session->userdata('is_global_admin') == 'yes')
			{
				// Prepare Basic Panel
				$parameters['basic_panel'] = $this->load->view('settings/basic', array('languages' => $languages), TRUE);
	
				if( !in_array($current_revision['status'], array( 'Created' ) ) )
				{
					// Prepare Clean Up Panel
					$master_files = $this->transhome_model->getLangFileCleanUpInfo($current_revision);
					
					$parameters['cleanup_panel'] = $this->load->view('settings/cleanup', array('master_files' => $master_files), TRUE);
				}
				
				if( !in_array($current_revision['status'], array( 'Created', 'Base Completed', 'Cleaning Up') ) )
				{
					// Prepare Path Panel
					if(!empty($current_revision['lang_root_dir']) && !empty($current_revision['master_lang_root_dir']))
					{
						$lang_file_list = $this->transhome_model->getLangFileList(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$current_revision['master_lang_root_dir']);
					}
					else 
					{
						$lang_file_list = array();
					}
					
					$path_page_urls = $this->revision_model->getPathPageURLs($current_revision['id']);
					$parameters['path_panel'] = $this->load->view('settings/path', array('lang_file_list' => $lang_file_list, 'path_page_urls' => $path_page_urls), TRUE);
					$this->load->view('dialog/pathurl_dlg', array('url_root' => $current_revision['target_ci_url']));
				}
				
				$parameters['rev_control'] = 'show';
			}
			else 
			{
				if( in_array($current_revision['status'], array( 'Created', 'Base Completed', 'Cleaning Up') ) )
				{}
				else 
				{
					foreach ($current_revision['users'] as $rev_user)
					{
		  				if($rev_user['moderator_id'] != $this->session->userdata('id')) continue;
	
		  				$parameters['rev_control'] = 'show';
						break;
					}
				}
			}

			if(isset($parameters['rev_control']))
			{
				if( in_array($current_revision['status'], array( 'Created', 'Base Completed', 'Cleaning Up') ) )
				{}
				else 
				{
					// Prepare Team Panel
					$parameters['team_panel'] = $this->load->view('settings/team', array('all_users' => $all_users, 'languages' => $languages), TRUE);
					$this->load->view('dialog/team_dlg', array('users' => $users, 'languages' => $languages));
		
					// Prepare Approval Panel
					$revision_statistics = $this->revision_model->getRevisionStatistics($current_revision['id'], $this->session->userdata());
	
					if(!empty($current_revision['lang_root_dir']) && !empty($current_revision['master_lang_root_dir']))
					{
						$master_files = $this->transhome_model->getLangFileList(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$current_revision['master_lang_root_dir']);
					}
					else 
					{
						$master_files = array();
					}
					
					$parameters['approval_panel'] = $this->load->view('settings/approval', array('statistics' => $revision_statistics, 'master_files' => $master_files, 'languages' => $languages), TRUE);
				}
			}
		}

		if($this->session->userdata('is_global_admin') == 'yes')
		{
			// Prepare Languages Panel
			$parameters['languages_panel'] = $this->load->view('settings/languages', array('languages' => $languages), TRUE);
			$this->load->view('dialog/lang_dlg');
		}
		
		// Prepare Users Panel
		$parameters['users_panel'] = $this->load->view('settings/users', array('all_users' => $all_users, 'languages' => $languages), TRUE);
		$this->load->view('dialog/user_dlg', array('languages' => $languages));

		// Prepare Revision Panel
		$parameters['revision_panel'] = $this->load->view('settings/revisions', array(), TRUE);
		$this->load->view('dialog/revision_dlg');

		$this->load->view('settings/setting', $parameters);
		
		$this->load->view('dialog/simple_dlg');
		$this->load->view('footer');
	}
	
	public function saveGlobalLanguages()
	{
		$lang_list = $this->input->post('lang_list');
		$errors = array();
		$name_map = array();
		$names = array();
		$ids = array();
		
		$all_langs = $this->settings_model->getLanguages();
		foreach ($all_langs as $lang)
		{
			$name_map[''.$lang['id']] = $lang['name'];
		}
		
		foreach ($lang_list as $lang)
		{
			if(empty($lang['id'])) continue;
			$name_map[''.$lang['id']] = $lang['name'];
		}
		
		foreach ($name_map as $id => $name)
		{
			if(!in_array($name, $names)) { $names[] = $name; continue; }
			$errors[] = "Duplicated language name : ".$name;
			break;
		}
		
		if(!empty($errors))
		{
			echo json_encode(array('errors' => $errors));
			return;
		}

		foreach ($lang_list as $lang)
		{
			if(!empty($lang['id'])) continue;
			if(!in_array($lang['name'], $names)) { $names[] = $lang['name']; continue; }
			$errors[] = "Duplicated language name : ".$lang['name'];
			break;
		}
		
		if(!empty($errors))
		{
			echo json_encode(array('errors' => $errors));
			return;
		}
		
		foreach ($lang_list as $lang)
		{
			if(empty($lang['id'])) continue;
			$ids[] = $lang['id'];	
		}
		
		foreach ($all_langs as $lang)
		{
			if(in_array($lang['id'], $ids)) continue;
				
			$this->settings_model->removeLanguage(array('id' => $lang['id']));
		}
		
		$inserted_ids = array();
		
		foreach ($lang_list as $lang)
		{
			if(empty($lang['id']))
			{
				$inserted_ids[] = $this->settings_model->createLanguage($lang);
			}
			else 
			{
				$this->settings_model->updateLanguage($lang);
			}
		}
		
		echo json_encode(array('errors' => $errors, 'inserted_ids' => $inserted_ids));
	}
	
}
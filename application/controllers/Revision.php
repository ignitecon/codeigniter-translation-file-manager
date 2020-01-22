<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Revision extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('url', 'form', 'cookie'));
		$this->load->library('session');
		if(!$this->session->has_userdata('email')) redirect('user/login');

		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		$this->load->model('user_model');
		$this->load->model('settings_model');
		$this->load->model('revision_model');
		$this->load->model('transhome_model');
		
		$this->setLatestRevisionIntoSession();
	}

	public function setLatestRevisionIntoSession()
	{
		$this->revision_model->setLatestRevisionIntoSession();
	}
	
	public function saveBasicSettings()
	{
		$rev_id = $this->input->post("rev_id");
		$rev_name = $this->input->post("rev_name");
		$master_lang_id = $this->input->post("master_lang_id");
		$lang_root = $this->input->post("lang_root");
		$master_lang_root = $this->input->post("master_lang_root");
		$target_ci_url = $this->input->post("target_ci_url");
		
		$errors = array();
		if(!$this->revision_model->checkRevisionDuplicate(array('revision_name' => $rev_name), $rev_id)) $errors[] = "Revision name is duplicated with other revision.";
		if(!$this->revision_model->checkRevisionDuplicate(array('lang_root_dir' => $lang_root), $rev_id)) $errors[] = "Language file root path is duplicated with other revision.";
		if(!$this->check_lang_root_dir($lang_root)) $errors[] = "Language file root path does not exist.";
		if(!$this->check_lang_root_dir($lang_root.DIRECTORY_SEPARATOR.$master_lang_root)) $errors[] = "Master language file root Path does not exist.";
					
		if(trim($target_ci_url) == '') $errors[] = "Target Codeigniter Site URL can not be empty.";

		if(empty($errors))
		{
			$this->revision_model->saveRevisionBase($rev_id, $rev_name, $master_lang_id, $lang_root, $master_lang_root, $target_ci_url);
		}
		
		echo json_encode(array('errors' => $errors));
	}
	
	public function index()
	{
		//Intialize values for library and helpers	
		$this->form_validation->set_error_delimiters("<p class='error'>", "</p>");

		//Get Form Data	of Language Root Directory
		if($this->input->post('new_revision_lang_dir_form'))
    	{    
    		$this->form_validation->set_rules('language_root_directory', 'Language Directory', 'required|trim|callback_check_lang_root_dir');
    		if ($this->form_validation->run())
			{
			}
    	}
    	
		//Get Form Data	of Creating New Revision
		if($this->input->post('new_revision_form'))
    	{    
    		$this->form_validation->set_rules('new_revision_number', 'Revision Number', 'required|trim|callback_checkRevisionNumberDuplicate');
    		$this->form_validation->set_rules('languages[]', 'Language', 'callback_check_language');
    		$this->form_validation->set_rules('master_lang_dir', 'Master Language Directory', 'required|trim');

    		if ($this->form_validation->run())
			{
				$languages = $this->input->post('languages[]');
				$lang_dirs = $this->input->post('lang_dirs[]');
				$lang_root_dir = $this->input->post('language_root_directory');
				
				$info_map = array();
				for ($i = 0; $i < count($languages); $i++)
				{
					$dir_map[$languages[$i]] = $lang_dirs[$i];
				}
				
				// First, check the syntax validation of *_lang.php files of selected languages
				$checkSyntaxResult = array();
				foreach ($languages as $language)
				{
					$checkSyntaxResult += $this->transhome_model->checkSyntaxForLanguage(ROOTPATH.$lang_root_dir.$dir_map[$language], $language);
				}
				
				if(!empty($checkSyntaxResult))
				{
					$this->session->set_flashdata('lang_syntax_error', $checkSyntaxResult);
				}
				else 
				{
					// If the all *_lang.php files have no syntax error.  
					// then, create a new revision
					
					$revision_name = $this->input->post('new_revision_name');
					$master_language = $this->input->post('master_language');
					$translators = $this->input->post('translators[]');
					$proofers = $this->input->post('proofers[]');
					$moderators = $this->input->post('moderators[]');
					$this->revision_model->createNewRevision($revision_name, $master_language, $lang_root_dir, $languages, $translators, $proofers, $moderators, $lang_dirs);
				}
			}
    	}
		
		$languages = $this->settings_model->getLanguages();
		$open_revision_users = $this->revision_model->getOpenRevisionUsers();
		$users = $this->user_model->getUsers(array(), '', array('username', 'ASC'));
		$session_data = $this->session->all_userdata();
		
		$folder_list = array();
		if(isset($_POST['language_root_directory']))
		{
			$folder_list = $this->transhome_model->getFolderList(ROOTPATH.$_POST['language_root_directory']);
		}
		
		$this->load->view('header', array('title' => 'Site Translator - Revision page', 
													'js' => array('ui-tree'))+ $session_data );
		$this->load->view('revision/home', array('users' => $users, 'languages' => $languages, 'open_revision_users' => $open_revision_users, 'folder_list' => $folder_list));
		$this->load->view('footer');
	}
	
	public function check_language($languages)
	{
		if(empty($languages))
		{
			$this->form_validation->set_message('check_language', 'At least a language should be checked.');
			return false;
		}
		
		return true;
	}
	
	public function check_lang_root_dir($lang_root_dir)
	{
		$real_root_dir = ROOTPATH.$lang_root_dir;
		if(file_exists($real_root_dir)) return true;
		
		return false;
	}
	
	public function savePathPageURLs()
	{
		$rev_id = $this->input->post('rev_id');
		$path_url_list = $this->input->post('path_url_list');
		if(empty($path_url_list)) $path_url_list = array();
		
		$this->revision_model->savePathPageURLs($rev_id, $path_url_list);
		
		echo json_encode(array('errors' => array()));
	}
	
	public function saveTeamConfigure()
	{
		$rev_id = $this->input->post('rev_id');
		$team_entry_list = $this->input->post('team_entry_list');
		if(empty($team_entry_list)) $team_entry_list = array();
		$is_global_admin = $this->input->post('is_global_admin');
		
		$current_revision = $this->session->userdata('current_revision');
		$errors = array();
		
		if($is_global_admin == 'yes')
		{
			foreach ($team_entry_list as $team_entry)
			{
				if(!file_exists(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$team_entry['path']))
				{
					$errors[] = "Language path '".$team_entry['path']."' doesnot exist. <br />".
								"Please check if base setting of current revision is correct. ".
								"If base setting is correct, please create the language folder.";
					break;
				}
			}
		}
		
		if(!empty($errors))
		{
			echo json_encode(array('errors' => $errors));
			return;
		}
		
		$this->revision_model->saveTeamConfigure($rev_id, $team_entry_list, $is_global_admin);
		
		echo json_encode(array('errors' => array()));
	}
	
	public function switchRevision()
	{
		$rev_id = $this->input->post('revision_id');
		
		$this->session->set_userdata('current_revision', array('id' => $rev_id));
		
		echo json_encode(array('errors' => array()));
	}
	
	public function removeRevision()
	{
		$rev_id = $this->input->post('revision_id');

		$this->revision_model->removeRevision($rev_id);
		
		echo json_encode(array('errors' => array()));
	}
	
	public function createNewRevision()
	{
		$revision_name = $this->input->post('revision_name');
		
		$result = $this->revision_model->createNewRevision($revision_name);
		
		echo json_encode($result);
	}
	
	public function doOperation()
	{
		$rev_id = $this->input->post('rev_id');
		$rev_user_id = $this->input->post('rev_user_id');
		$operation = $this->input->post('operation');

		$errors = $this->revision_model->doOperation($rev_id, $rev_user_id, $operation);
		echo json_encode(array('errors' => $errors));
	}

	public function startCleanUp()
	{
		if(!$this->session->has_userdata('email'))
		{
			echo json_encode(array('errors' => array('Session Expired')));
			return;
		}
		
		if(!$this->session->has_userdata('current_revision'))
		{
			echo json_encode(array('errors' => array('No Revision Selected')));
			return;
		}
		
		$errors = $this->transhome_model->startCleanUp($this->session->userdata('current_revision'));
		echo json_encode(array('errors' => $errors));
	}
	
	public function finishCleanUp()
	{
		if(!$this->session->has_userdata('email'))
		{
			echo json_encode(array('errors' => array('Session Expired')));
			return;
		}
		
		if(!$this->session->has_userdata('current_revision'))
		{
			echo json_encode(array('errors' => array('No Revision Selected')));
			return;
		}
		
		$errors = $this->transhome_model->finishCleanUp($this->session->userdata('current_revision'));
		echo json_encode(array('errors' => $errors));
	}

	public function zipAndDownLoad($rev_user_id)
	{
		$revision = $this->session->userdata("current_revision");
		foreach ($revision['users'] as $rev_user)
		{
			if($rev_user_id != $rev_user['id']) continue;
			
			$lang_id = $rev_user['language_id'];
			$lang_dir = $rev_user['path'];
			
			$languages = $this->settings_model->getLanguages();
			foreach ($languages as $language)
			{
				if($language['id'] != $lang_id) continue; 
				$lang_name = $language['name']; break;
			}
			
			break;
		}
		if(!isset($lang_name) || !isset($lang_id)) { exit; return; }
		
		$archive_file_name = preg_replace('/[^A-Za-z0-9\-]/', '', $revision['revision_name']).'-'.$lang_name.'.zip'; 
	    $zip = new ZipArchive();
	    //create the file and throw the error if unsuccessful
	    if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE ) !== TRUE) 
	    {
	        return array("Can not create zip file.");
	    }

	    $root_lang_dir = ROOTPATH.$revision['lang_root_dir'];
		$lang_files = $this->transhome_model->getLangFileList($root_lang_dir, 3);
	    
	    foreach($lang_files as $file)
	    {
	    	if(strncmp($file, $lang_dir, strlen($lang_dir)) != 0) continue;
	        $zip->addFile($root_lang_dir.DIRECTORY_SEPARATOR.$file, $file);
	    }
	    
	    $zip->close();

	    //then send the headers to force download the zip file
	    header("Content-type: application/zip"); 
	    header("Content-Disposition: attachment; filename=$archive_file_name"); 
	    header("Pragma: no-cache"); 
	    header("Expires: 0");
	    
	    readfile($archive_file_name);
	    exit;
	}
	
}
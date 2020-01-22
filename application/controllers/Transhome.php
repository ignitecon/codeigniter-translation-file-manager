<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transhome extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();

		$this->load->library('session');
		$this->load->helper(array('url', 'directory'));
		
		if(!$this->checkLoginStatus()) redirect('user/login');
		$this->load->model('settings_model');
		$this->load->model('revision_model');
		$this->load->model('transhome_model');
		
		$this->revision_model->setLatestRevisionIntoSession();
	}

	private function checkLoginStatus()
	{
		if($this->session->has_userdata('email')) return true;

		return false;
	}
	
	private function makeLangFileList(&$file_list, $cur_dir, $file_map, $deps)
	{
		foreach ($file_map as $dir => $file_map_child)
		{
			if(is_array($file_map_child))
			{
				$file_list[] = array('name' => $dir, 'is_dir' => true, 'dir' => $cur_dir, 'deps' => $deps);
				$this->makeLangFileList($file_list, $cur_dir.$dir, $file_map_child, $deps + 1);				
			}
			else 
			{
				$file_list[] = array('name' => $file_map_child, 'is_dir' => false, 'dir' => $cur_dir, 'deps' => $deps);
			}
		}
	}
	
	public function index()
	{
		$languages = $this->settings_model->getLanguages();
		
		if($this->session->has_userdata('current_revision'))
		{
			$current_revision = $this->session->userdata('current_revision');
			$revision_statistics = $this->revision_model->getRevisionStatistics($current_revision['id'], $this->session->userdata());
			
			if(!empty($current_revision['lang_root_dir']) && !empty($current_revision['master_lang_root_dir']))
			{
				$master_files = $this->transhome_model->getLangFileList(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$current_revision['master_lang_root_dir']);
			}
			else 
			{
				$master_files = array();
			}
			
			$this->load->view('header', array('title' => 'Site Translator - Home page', 'master_files' => $master_files));
			$this->load->view('translator/home', array('statistics' => $revision_statistics, 'languages' => $languages));
		}
		else
		{
			$this->load->view('header', array('title' => 'Site Translator - Home page'));
			$this->load->view('translator/home');
		}
		
		$this->load->view('footer');
	}
	
	public function translate()
	{
		$current_revision = $this->session->userdata('current_revision');

		$languages = $this->settings_model->getLanguages();

		$revision_statistics = $this->revision_model->getRevisionStatistics($current_revision['id'], $this->session->userdata());
		
		if(!empty($current_revision['lang_root_dir']) && !empty($current_revision['master_lang_root_dir']))
			$master_files = $this->transhome_model->getLangFileList(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$current_revision['master_lang_root_dir']);
		else 
			$master_files = array();

		$path_page_urls = $this->revision_model->getPathPageURLs($current_revision['id']);
		
		$cleanUpStatus = $this->transhome_model->getCleanUpStatus($current_revision);
		
		$this->load->view('header', array('title' => 'Site Translator - Translate Language', 'master_files' => $master_files) );
		$this->load->view('translator/translate', array('statistics' => $revision_statistics, 'languages' => $languages, 'path_page_urls' => $path_page_urls, 'clean_status' => $cleanUpStatus));
		$this->load->view('dialog/simple_dlg');
		$this->load->view('dialog/translate_dlg');
		$this->load->view('footer');
	}
	
	public function statistics()
	{
		$current_revision = $this->session->userdata('current_revision');
		
		$languages = $this->settings_model->getLanguages();
		
		$revision_statistics = $this->revision_model->getRevisionStatistics($current_revision['id'], $this->session->userdata());

		if(!empty($current_revision['lang_root_dir']) && !empty($current_revision['master_lang_root_dir']))
		{
			$master_files = $this->transhome_model->getLangFileList(ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR.$current_revision['master_lang_root_dir']);
		}
		else 
		{
			$master_files = array();
		}
		
		$this->load->view('header', array('title' => 'Site Translator - Statistics', 'master_files' => $master_files) );
		$this->load->view('translator/statistics', array('statistics' => $revision_statistics, 'master_files' => $master_files, 'languages' => $languages));
		$this->load->view('footer');
	}
	
	public function getTwoLangFilesMap()
	{
		$current_revision = $this->session->userdata('current_revision');
		if(empty($current_revision)) { echo json_encode(array('result' => array())); return; }

		$master_lang_id = $current_revision['master_lang_id'];
		$slave_lang_id  = $_POST['slave_lang'];
		$stage = $_POST['stage'];
		$lang_file = $_POST['lang_file'];
		
		$lang_arr = array();
		$is_slave = false;

		foreach (array($master_lang_id, $slave_lang_id) as $cur_lang_id)
		{
			if($is_slave)
			{
				if($cur_db_repo = $this->transhome_model->getLangRepoMapFromDB($current_revision['id'], $cur_lang_id, $lang_file, $stage))
				{
					$lang_arr[] = $cur_db_repo;
					continue;
				}
			}
			
			$is_slave = true;

			$cur_lang_location = ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR;
			
			if($cur_lang_id == $master_lang_id)
			{
				$cur_lang_location .= $current_revision['master_lang_root_dir'].DIRECTORY_SEPARATOR.$lang_file;
			}
			else
			{
				foreach ($current_revision['users'] as $entry)
				{
					if($entry['language_id'] != $cur_lang_id) continue;
					$cur_lang_location .= $entry['path'].DIRECTORY_SEPARATOR.$lang_file;
				}
			}
			
			$parse_result = $this->transhome_model->getLangFileParseResult($cur_lang_location);
			
			if($parse_result['result'])
			{
				$lang_arr[] = $parse_result['langsMap'];
			}
			else
			{
				$lang_arr[] = array();
			}
		}
		
		$master_repo = $lang_arr[0];
		$slave_repo = $lang_arr[1];
		$merge_repo = array();
		
		foreach ($master_repo as $key => $master_val)
		{
			$slave_val = "";
			if(isset($slave_repo[$key])) $slave_val = $slave_repo[$key];
			$merge_repo[] = array($key, $master_val, $slave_val); 	
		}
		
		$status = $this->transhome_model->getTranslationStatus($current_revision['id'], $slave_lang_id, $lang_file, $stage);
		
		echo json_encode(array('result' => array('merge_repo' => $merge_repo, 'status' => $status)));
//		return array('merge_repo' => $merge_repo, 'status' => $status);
	}
	
	public function saveSlaveLangFileMap()
	{
		$current_revision = $this->session->userdata('current_revision');
		if(empty($current_revision)) { echo json_encode(array('result' => 'failure', 'reason' => 'No revision selected.')); return; }
		
		$slave_lang_id  = $_POST['slave_lang'];
		$lang_file = $_POST['lang_file'];
		$lang_arr = $_POST['lang_arr'];
		$stage = $_POST['stage'];
		$complete = $_POST['complete'];

		$slave_lang_dir = ROOTPATH.$current_revision['lang_root_dir'].DIRECTORY_SEPARATOR;
		
		if($slave_lang_id == $current_revision['master_lang_id'])
		{
			$slave_lang_dir .= $current_revision['master_lang_root_dir'];
		}
		else
		{
			foreach ($current_revision['users'] as $entry)
			{
				if($entry['language_id'] != $slave_lang_id) continue;
				$slave_lang_dir .= $entry['path'];
			}
		}
		
		$this->transhome_model->saveSlaveLangFileMap($current_revision['id'], $slave_lang_id, $slave_lang_dir, $lang_file, $lang_arr, $stage, $complete);
		$status = $this->transhome_model->getTranslationStatus($current_revision['id'], $slave_lang_id, $lang_file, $stage);
		
		echo json_encode(array('result' => 'success', 'status' => $status));
	}
	
	public function unmarkAsCompleted()
	{
		$current_revision = $this->session->userdata('current_revision');
		if(empty($current_revision)) { echo json_encode(array('result' => 'failure', 'reason' => 'No revision selected.')); return; }
		
		$slave_lang_id  = $_POST['slave_lang'];
		$lang_file = $_POST['lang_file'];
		$stage = $_POST['stage'];

		$this->transhome_model->unmarkAsCompleted($current_revision['id'], $slave_lang_id, $lang_file, $stage);
		
		echo json_encode(array('result' => 'success', 'status' => 'In Progress'));
	}
	
//	public function markascompleteSlaveLangFileMap()
//	{
//		$open_revision_ver = $this->revision_model->getOpenRevisionName();
//		if(empty($open_revision_ver)) { echo json_encode(array('result' => 'failure', 'reason' => 'No open revision.')); return; }
//		
//		$slave_lang  = $_POST['slave_lang'];
//		$lang_file = $_POST['lang_file'];
//		$stage = $_POST['stage'];
//
//		$this->transhome_model->markascompleteSlaveLangFileMap($open_revision_ver, $slave_lang, $lang_file, $stage);
//		$status = $this->transhome_model->getTranslationStatus($open_revision_ver, $slave_lang, $lang_file, $stage);
//		
//		echo json_encode(array('result' => 'success', 'status' => $status));
//	}
	
	public function getFolderInfo()
	{
		$settings = $this->settings_model->getAllSettings();
//		$path = $lang_dir.'/'.urldecode($_REQUEST['path']);
//		$req_path = urldecode($_REQUEST['path']);
		$path = $settings['lang_location'].'/'.$_REQUEST['path'];
		$req_path = $_REQUEST['path'];

		$special = array(' ', '`', '~', '!', '@', '#', '$', '%', '^', '&');
	 
		$upload_sub_files = array();
		$upload_sub_folders = array();
	
		$d = dir($path);	
	
		while (false != ($file = $d->read())) 
		{
			if ($file == "." || $file == "..") continue;
			
			$entry_path = ($path).'/'.$file;      
			
			if(is_dir( ($entry_path)))
			{
				$upload_sub_folder['name'] = $file;
				$upload_sub_folder['url'] = $req_path.'/'.$file;	         	  
//				$upload_sub_folder['url'] = urlencode($req_path.'/'.$file);	         	  
				$upload_sub_folder['type'] = "folder";
				$upload_sub_folder['additionalParameters']['id']= ($file);
				$upload_sub_folders[] = $upload_sub_folder;
			}
			elseif(is_file($entry_path))
			{
				if( substr($file, -1 * strlen('_lang.php')) != '_lang.php' ) continue;
				
				$upload_sub_file['name'] = $file;
				$upload_sub_file['url'] = $req_path.'/'.$file;
//				$upload_sub_file['url'] = urlencode($req_path.'/'.$file);
				$upload_sub_file['path'] = $path;
				$upload_sub_file['type'] = "item";
				$upload_sub_file['additionalParameters']['id']= ($file);
				$upload_sub_files[] = $upload_sub_file;
			}
		}

		$d->close();
		
		foreach($upload_sub_files as $sub_file) $upload_sub_folders[] = $sub_file;
    
		echo (json_encode($upload_sub_folders, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP));		
	}
	
	public function getFileInfo()
	{
		$settings = $this->settings_model->getAllSettings();
//		$path = $lang_dir.'/'.urldecode($_REQUEST['path']);
//		$req_path = urldecode($_REQUEST['path']);
		$path = $settings['lang_location'].'/'.$_REQUEST['path'];
		$req_path = $_REQUEST['path'];
		
		$content = file_get_contents($path);
		$content = str_replace(array("<?php", "?>"), array('', ''), $content);
		echo (json_encode(array('content' => $content), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP));		
	}
}

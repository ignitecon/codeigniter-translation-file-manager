<?php
	class Transhome_model extends CI_Model 
	{
		function __construct()
		{
			parent::__construct();
		
			$this->load->database();
			$this->load->helper(array('url', 'directory'));
		}

		public function checkSyntaxForLanguage($lang_dir, $language)
		{
			$syntax_check = array();
			
			$file_list = $this->getLangFileList($lang_dir);

			foreach ($file_list as $file_path)
			{
				$abs_path = $lang_dir.'/'.$file_path;
				$result = $this->getLangFileParseResult($abs_path);
				
				if($result['result']) continue;
				
				$syntax_check[] = '['.$language.'] '.$file_path;
			}
			
			return $syntax_check;
		}
		
		private function extractFiles($map, $onlyFolders)
		{
			if(empty($map)) return array();
			
			$file_list = array();
			foreach ($map as $key => $entry)
			{
				if(is_array($entry))
				{
					$file_list_child = $this->extractFiles($entry, $onlyFolders);
					
					if($onlyFolders)
					{
						if(empty($file_list_child)) $file_list[] = $key;
					}
					foreach ($file_list_child as $file_child)
					{
						$file_list[] = $key . $file_child;
					}
				}
				else 
				{
					if($onlyFolders) continue;
					if( preg_match("/_lang.php$/i", $entry) <= 0 ) continue;
					$file_list[] = $entry;
				}
			}
			
			return $file_list;
		}
		
		private function getAllFileLists($path, $onlyFolders = false, $depth = 2)
		{
			$dir_map = $this->extractFiles(directory_map($path, $depth), $onlyFolders);
			
			return $dir_map;
		}
		
		public function getFolderList($abs_dir)
		{
			$dir_map = $this->extractFiles(directory_map($abs_dir, 2), true);
			foreach ($dir_map as &$dir)
			{
				$dir = DIRECTORY_SEPARATOR.substr($dir, 0, -1);
			}
			
			sort($dir_map);
			
			return $dir_map;
		}
		
		public function getLangFileList($lang_dir, $depth = 2) // absolute directory
		{
			$file_list = $this->getAllFileLists($lang_dir, false, $depth);	
			
			sort($file_list);
			
			return $file_list;
		}
		
		private function calculateWordsKeysCountEdited($orig_map, $change_map)
		{
			$keys_total_count = 0;
			$keys_empty_count = 0;
			
			$word_chg_count = 0;
			$keys_chg_count = 0;
			
			foreach ($change_map as $key => $line)
			{
				$change_line = trim($line);

				$keys_total_count++;
				if(empty($change_line)) $keys_empty_count++;
				
				$orig_line = "";
				
				if(isset($orig_map[$key])) $orig_line = trim($orig_map[$key]);
				
				if(strcmp($orig_line, $change_line) == 0) continue;

				$keys_chg_count++;
				
				while(preg_match("/  /", $change_line) > 0) $change_line = str_replace('  ', ' ', $change_line);
				while(preg_match("/  /", $change_line) > 0) $orig_line   = str_replace('  ', ' ', $orig_line);
				
				$org_words = explode(' ', $orig_line);				
				$chg_words = explode(' ', $change_line);

				$org_map = array();
				$chg_map = array();
				
				foreach ($org_words as $org_word)
				{
					if(empty($org_word)) continue;
					if(!isset($org_map[$org_word])) $org_map[$org_word] = 0; 
					$org_map[$org_word]++;
				}

				foreach ($chg_words as $chg_word)
				{
					if(empty($chg_word)) continue;
					if(!isset($chg_map[$chg_word])) $chg_map[$chg_word] = 0; 
					$chg_map[$chg_word]++;
				}
				
				foreach ($chg_map as $chg_key => $chg_val)
				{
					if(!isset($org_map[$chg_key])) 
					{
						$word_chg_count += $chg_val;
						continue;
					}
					
					$word_chg_count += abs($chg_val - $org_map[$chg_key]);
					unset($org_map[$chg_key]);
				}

				foreach ($org_map as $org_key => $org_val)
				{
					$word_chg_count += $org_val;
				}
			}
			
			return array('words' => $word_chg_count, 'keys' => $keys_chg_count, 
						'keys_total' => $keys_total_count, 'keys_empty' => $keys_empty_count);
		}
		
		public function arr_to_map($arr)
		{
			$map = array();
			foreach ($arr as $entry)
			{
				$map[$entry[0]] = $entry[1];
			}
			
			return $map;
		}
		
		public function saveSlaveLangFileMap($rev_id, $language_id, $lang_dir, $lang_file, $lang_arr, $stage, $complete)
		{
			$status = ($complete == 'true') ? 'Completed' : 'In Progress';
			
			$lang_map = $this->arr_to_map($lang_arr);
			$lang_repo = json_encode($lang_map);
			$lang_repo_table = "revision_".$rev_id."_repo_".$language_id;
			
			if(!$this->db->table_exists($lang_repo_table))
			{
				$this->load->dbforge();
				
				// Create language resource repository table for a language
				$fields = array(
					'id' 				=> array( 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
					'path' 				=> array( 'type' => 'VARCHAR', 'constraint' => '255' ),
					'repo_origin' 		=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'repo_translator' 	=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'translator_status'	=> array( 'type' => 'VARCHAR', 'constraint' => '100' ),
					'translator_words'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
					'translator_keys'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
					'repo_proofer'		=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'proofer_status'	=> array( 'type' => 'VARCHAR', 'constraint' => '100' ),
					'proofer_words'		=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
					'proofer_keys'		=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
					'total_keys'		=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE ),
					'total_empty_keys'	=> array( 'type' => 'INT', 'constraint' => '10', 'unsigned' => TRUE )
				);
				
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$attributes = array('ENGINE' => 'InnoDB');
				$this->dbforge->create_table($lang_repo_table, FALSE, $attributes);
			}
			else 
			{
				$this->db->where('path', $lang_file);
				$query = $this->db->get($lang_repo_table);
				$result = $query->row_array();
			}
						
			if($stage == 'translator')
			{
				if(!isset($result) || empty($result))
				{
					$map_origin = array();
					
					$parse_result = $this->getLangFileParseResult($lang_dir.DIRECTORY_SEPARATOR.$lang_file);
					if($parse_result['result']) $map_origin = $parse_result['langsMap'];
					$repo_origin = json_encode($map_origin);
					
					$change_count = $this->calculateWordsKeysCountEdited($map_origin, $lang_map);
					
					$this->db->insert($lang_repo_table, array(	'path' => $lang_file, 
																'repo_origin' => $repo_origin, 
																'repo_translator' => $lang_repo,
																'translator_status' => $status,
																'translator_words' => $change_count['words'],
																'translator_keys' => $change_count['keys'],
																'total_keys' => $change_count['keys_total'],
																'total_empty_keys' => $change_count['keys_empty']
					));
				}
				else 
				{
					$repo_origin = $result['repo_origin'];
					$repo_origin_map = json_decode($repo_origin, TRUE);
					$change_count = $this->calculateWordsKeysCountEdited($repo_origin_map, $lang_map);
					
					$this->db->where('path', $lang_file);
					$this->db->update($lang_repo_table, array(	'repo_translator' => $lang_repo,
																'translator_status' => $status,
																'translator_words' => $change_count['words'],
																'translator_keys' => $change_count['keys'],
																'total_keys' => $change_count['keys_total'],
																'total_empty_keys' => $change_count['keys_empty']
					));
				}
			}
			else if ($stage == 'proofer')
			{
				if(!isset($result) || empty($result))
				{
					// If this case is met, this means the LOGIC PARADOX !!!
				}
				else 
				{
					$repo_translator = $result['repo_translator'];
					$repo_translator_map = json_decode($repo_translator, TRUE);
					$change_count = $this->calculateWordsKeysCountEdited($repo_translator_map, $lang_map);
					
					$this->db->where('path', $lang_file);
					$this->db->update($lang_repo_table, array(	'repo_proofer' => $lang_repo,
																'proofer_status' => $status,
																'proofer_words' => $change_count['words'],
																'proofer_keys' => $change_count['keys'],
																'total_keys' => $change_count['keys_total'],
																'total_empty_keys' => $change_count['keys_empty']
					));
				}
			}
			
			$this->saveLatestRepoIntoLangFile($lang_dir, $lang_file, $lang_map);
		}
		
		public function unmarkAsCompleted($rev_id, $language_id, $lang_file, $stage)
		{
			$lang_repo_table = "revision_".$rev_id."_repo_".$language_id;
			
			$this->db->where('path', $lang_file);
			$query = $this->db->update($lang_repo_table, array($stage.'_status' => 'In Progress'));
		}
		
//		public function markascompleteSlaveLangFileMap($rev_ver, $language, $lang_file, $stage)
//		{
//			$lang_repo_table = strtolower("revision_".$rev_ver."_repo_".$language);
//			$this->db->where('path', $lang_file);
//			$this->db->update($lang_repo_table, array($stage.'_status' => 'Completed'));
//		}
		
		public function getTranslationStatus($rev_id, $language_id, $lang_file, $stage)
		{
			$lang_repo_table = "revision_".$rev_id."_repo_".$language_id;
			
			if(!$this->db->table_exists($lang_repo_table)) $result = array();
			else 
			{
				$this->db->where('path', $lang_file);
				$query = $this->db->get($lang_repo_table);
				
				$result = $query->row_array();
			}
			
			if(empty($result))
			{
				if($stage == 'translator') return array('status' => 'In Progress', 'words' => 0, 'keys' => 0, 'total_keys' => 0, 'total_empty' => 0);
				if($stage == 'proofer')    return array('status' => 'Translation Not Completed');
				
				return array('status' => 'Unknown Translation Status');
			}
			else 
			{
				if($stage == 'translator')
				{
					$status = $result[$stage.'_status'];
					
					return array('status' => $status, 	'words' => $result[$stage.'_words'], 'keys' => $result[$stage.'_keys'], 
														'total_keys' => $result['total_keys'], 'total_empty' => $result['total_empty_keys']);
				}
				
				if($stage == 'proofer')
				{
					$status = $result[$stage.'_status'];
					if($result['translator_status'] != 'Completed') $status = 'Translation Not Completed';
					else if ($status == '') $status = 'In Progress'; 
					
					return array('status' => $status, 	'words' => $result[$stage.'_words'], 'keys' => $result[$stage.'_keys'],
														'translate_words' => $result['translator_words'], 'translate_keys' => $result['translator_keys'], 
														'total_keys' => $result['total_keys'], 'total_empty' => $result['total_empty_keys']);
				}
				
			}
		}
		
		public function saveLatestRepoIntoLangFile($lang_dir, $lang_file, $lang_map)
		{
			$file_path = $lang_dir.'/'.$lang_file;
			$folder = dirname($file_path);
			if(!file_exists($folder)) mkdir($folder);
			
			$fp = fopen($file_path, 'w');

			$content = "";
			foreach ($lang_map as $key => $val)
			{
				$content .= "$"."lang['".$key."'] = '".$val."'; \n";
			}
			$content = "<?php\n".$content."?>";
			fwrite($fp, $content);
		}
		
		public function getLangRepoMapFromDB($rev_id, $language_id, $lang_file, $stage)
		{
			$lang_repo_table = strtolower("revision_".$rev_id."_repo_".$language_id);
						
			if(!$this->db->table_exists($lang_repo_table)) return null;
			
			$this->db->where('path', $lang_file);
			$query = $this->db->get($lang_repo_table);
			$result = $query->row_array();
			
			if(empty($result)) return null;
			
			$lang_repoMap = json_decode($result['repo_'.$stage], TRUE);
			
			return $lang_repoMap;
		}
		
		public function getLangFileParseResult($abs_path)
		{
			if(!file_exists($abs_path)) return array('result' => false, 'reason' => 'File Not Exists');
			 
			$php_content = @file_get_contents($abs_path);
			
			// Remove opening and closing PHP tags
			$php_content = str_replace( array('<?php', '?>'), array('', ''), $php_content );

			$lang = array();
			// Evaluate the code
			ob_start();
			eval( $php_content );
			$err = ob_get_contents();
			ob_end_clean();
	
			if(!empty($err))
			{
				if ( mb_stripos( $err, 'Parse error' ) != FALSE ) {
					return array('result' => false, 'reason' => 'Parse Error');
				}
			}
			
			return array('result' => true, 'langsMap' => $lang);
		}
		
		public function startCleanUp($revision)
		{
			$master_lang_dir = ROOTPATH.$revision['lang_root_dir'].DIRECTORY_SEPARATOR.$revision['master_lang_root_dir'];
			$master_files = $this->getLangFileList($master_lang_dir);
			
			if(empty($master_files)) 
			{
				return array('No language file in master language file directory.');
			}
			
			$errors = array();
			$repo = array();
			foreach ($master_files as $master_file)
			{
				$result = $this->getLangFileParseResult($master_lang_dir.DIRECTORY_SEPARATOR.$master_file);
				if(!$result['result']) return $errors[] = 'Syntax Error: '.$master_file;
				
				$repo[$master_file] = $result['langsMap'];
			}

			$clean_table = "revision_".$revision['id']."_clean";
			
			if(!$this->db->table_exists($clean_table))
			{
				$this->load->dbforge();
				
				$fields = array(
					'id' 			=> array( 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ),
					'path' 			=> array( 'type' => 'VARCHAR', 'constraint' => '255' ),
					'origin_maps'	=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'changed_maps'	=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'added_keys'	=> array( 'type' => 'TEXT',	'null' => TRUE ),
					'deled_keys'	=> array( 'type' => 'TEXT',	'null' => TRUE )
				);
				
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$attributes = array('ENGINE' => 'InnoDB');
				$this->dbforge->create_table($clean_table, FALSE, $attributes);
			}
			
			foreach ($repo as $master_file => $map)
			{
				$this->db->insert($clean_table, array('path' => $master_file, 'origin_maps' => json_encode($map)));		
			}
			
			$this->db->where('id', $revision['id']);
			$this->db->update('revisions', array('status' => 'Cleaning Up'));

			return $errors;
		}
		
		public function getCleanUpStatus($revision)
		{
			$cleanup_table = 'revision_'.$revision['id'].'_clean';
			
			$this->db->select(implode(', ', array('path', 'added_keys', 'deled_keys')));
			$query = $this->db->get($cleanup_table);
			$result = $query->result_array();
			$ret = array();
			
			foreach ($result as $entry)
			{
				$ret[$entry['path']] = array(	'added_keys' => json_decode($entry['added_keys'], true),
												'deled_keys' => json_decode($entry['deled_keys'], true));
			}
			
			return $ret;
		}
		
		public function finishCleanUp($revision)
		{
			$master_lang_dir = ROOTPATH.$revision['lang_root_dir'].DIRECTORY_SEPARATOR.$revision['master_lang_root_dir'];
			$master_files = $this->getLangFileList($master_lang_dir);
			
			foreach ($master_files as $master_file)
			{
				$result = $this->getLangFileParseResult($master_lang_dir.DIRECTORY_SEPARATOR.$master_file);
				if(!$result['result']) return array('Syntax Error: '.$master_file);

				$current_file_map = $result['langsMap'];
				
				$cleanup_table = 'revision_'.$revision['id'].'_clean';
				$this->db->where('path', $master_file);
				$query = $this->db->get($cleanup_table);
				$result = $query->row_array();
					
				if(empty($result)) 
				{
					$origin_map = array();
					$this->db->insert($cleanup_table, array('path' => $master_file, 'origin_maps' => json_encode(array())));
				}
				else
				{
					$origin_map = json_decode($result['origin_maps'], TRUE);
				}
				
				$cleanup_compare = $this->getCleanUpCompareResult($origin_map, $current_file_map);
				
				$this->db->where('path', $master_file);
				$this->db->update($cleanup_table, array('changed_maps' => json_encode($current_file_map), 
														'added_keys' => json_encode($cleanup_compare['added_keys']),
														'deled_keys' => json_encode($cleanup_compare['deled_keys'])));
			}
				
			$this->db->where('id', $revision['id']);
			$this->db->update('revisions', array('status' => 'Clean Completed'));
			
			return array();
		}

		public function getCleanUpCompareResult($origin_map, $target_map)
		{
			$deled_keys = array();
			foreach ($origin_map as $key => $val)
			{
				if( !array_key_exists($key, $target_map) ) $deled_keys[] = $key; 
			}
			
			$added_keys = array();
			foreach ($target_map as $key => $val)
			{
				if( !array_key_exists($key, $origin_map) ) $added_keys[] = $key; 
			}
			
			return array('added_keys' => $added_keys, 'deled_keys' => $deled_keys);
		}
		
		public function getLangFileCleanUpInfo($revision)
		{
			$master_lang_dir = ROOTPATH.$revision['lang_root_dir'].DIRECTORY_SEPARATOR.$revision['master_lang_root_dir'];
			$master_files = $this->getLangFileList($master_lang_dir);
			$master_file_info = array();
			
			foreach ($master_files as $master_file)
			{
				$entry = array('name' => $master_file); ///, 'total_keys' => '', 'added_keys' => '', 'deled_keys' => '');

				$result = $this->getLangFileParseResult($master_lang_dir.DIRECTORY_SEPARATOR.$master_file);
				if($result['result'])
				{
					$entry['total_keys'] = count($result['langsMap']);
					$current_file_map = $result['langsMap'];
				}
				else 
				{
					$entry['total_keys'] = 'Parse Error';
				}
				
				if($revision['status'] == 'Base Completed' || !$result['result'])
				{
					$entry['added_keys'] = '-';
					$entry['deled_keys'] = '-';
				}
				else // if($revision['status'] == 'Cleaning Up' or later)
				{
					$cleanup_table = 'revision_'.$revision['id'].'_clean';
					$this->db->where('path', $master_file);
					$query = $this->db->get($cleanup_table);
					$result = $query->row_array();
					
					if(empty($result)) 
					{
						$entry['added_keys'] = 'File Deleted';
						$entry['deled_keys'] = 'File Deleted';
					}
					else
					{
						$repo = $result['origin_maps'];
						$origin_map = json_decode($repo, TRUE);
						
						$cleanup_compare = $this->getCleanUpCompareResult($origin_map, $current_file_map);				
						
						$entry['added_keys'] = count($cleanup_compare['added_keys']);
						$entry['deled_keys'] = count($cleanup_compare['deled_keys']);
					}
				}
				
				$master_file_info[] = $entry;
			}
			
			return $master_file_info;
		}
	}
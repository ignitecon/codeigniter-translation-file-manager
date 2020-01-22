<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('url', 'form', 'cookie', 'string'));
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		$this->load->model('user_model');
		$this->load->model('email_model');
		$this->load->model('revision_model');
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
	
	public function login()
	{
		//Intialize values for library and helpers	
		$this->form_validation->set_error_delimiters("<p class='error'>", "</p>");

		//Get Form Data	
		if($this->input->post('loginform'))
    	{    
			//Set rules
			$this->form_validation->set_rules('usermail',	'User Email',	'required|trim|valid_email');
			$this->form_validation->set_rules('pwd',		'Password',		'required|trim');
			
			if($this->form_validation->run())
			{
  			 	$conditions = array('email' => $this->input->post('usermail'),
  			 						'password' => hash("sha512", $this->input->post('pwd'), false));
				
 				$users = $this->user_model->getUsers($conditions);
 
				if(empty($users))
 				{
					//Notification message
					$this->session->set_flashdata('login_message', 'Login failed! Incorrect email or password');
 				}
 				else 
 				{
					$this->session->set_userdata($users[0]);
//					$this->revision_model->setLatestRevisionIntoSession();
					redirect('/'); 
					return;
 				}
			}
		}               
		
		$this->load->view('header', array('title' => 'Site Translator - Login page' ));
		$this->load->view('user/login');
		$this->load->view('dialog/pwd_dlg');
		$this->load->view('dialog/simple_dlg');
		$this->load->view('footer');
	}
	
	public function resetpwd($token = '')
	{
		if(empty($token)) { $this->session->set_flashdata('pwd_reset_token_error', 'Invalid reset password token.'); }
		else 
		{
			$users = $this->user_model->getUsers(array('pwd_token' => $token));
			if(empty($users))
			{ 
				$this->session->set_flashdata('pwd_reset_token_error', 'Invalid reset password token.');
			}
			else 
			{
				//Intialize values for library and helpers	
				$this->form_validation->set_error_delimiters("<p class='error'>", "</p>");
		
				//Get Form Data	
				if($this->input->post('pwdresetform'))
		    	{    
					//Set rules
					$this->form_validation->set_rules('pwd',		'Password',				'required|trim');
					$this->form_validation->set_rules('pwd_confirm','Password confirmation','required|trim');
					
					if($this->form_validation->run())
					{
				    	if($this->input->post('pwd') != $this->input->post('pwd_confirm'))
						{
							$this->session->set_flashdata('pwd_reset_message', 'Password confirmation mismatch !');
						}
						else 
						{
							$this->session->set_flashdata('pwd_reset_success', 'success');
							$user = $users[0];
							$user['password'] = $this->input->post('pwd');
							$this->user_model->resetPassword($user);
		 				}
					}
		    	}
			}
		}
 				
		$this->load->view('header', array('title' => 'Site Translator - Password Reset' ));
		$this->load->view('user/pwdreset');
		$this->load->view('footer');
	}
	
	public function saveUsersWithReferer()
	{
		$refer_id = $this->input->post('referer_id');
		$user_list = $this->input->post('user_list');
		$errors = array();
		$email_map = array();
		$emails = array();
		$ids = array();
		
		$all_users = $this->user_model->getUsers();
		foreach ($all_users as $user)
		{
			$email_map[''.$user['id']] = $user['email'];
		}
		
		foreach ($user_list as $user)
		{
			if(empty($user['id'])) continue;
			$email_map[''.$user['id']] = $user['email'];
		}
		
		foreach ($email_map as $id => $email)
		{
			if(!in_array($email, $emails)) { $emails[] = $email; continue; }
			$errors[] = "Duplicated email address : ".$email;
			break;
		}
		
		if(!empty($errors))
		{
			echo json_encode(array('errors' => $errors));
			return;
		}

		foreach ($user_list as $user)
		{
			if(!empty($user['id'])) continue;
			if(!in_array($user['email'], $emails)) { $emails[] = $user['email']; continue; }
			$errors[] = "Duplicated email address : ".$user['email'];
			break;
		}
		
		if(!empty($errors))
		{
			echo json_encode(array('errors' => $errors));
			return;
		}
		
		foreach ($user_list as $user)
		{
			if(empty($user['id'])) continue;
			$ids[] = $user['id'];	
		}
		
		foreach ($all_users as $user)
		{
			if(in_array($user['id'], $ids) || $user['refer_id'] != $refer_id) continue;
				
			$this->user_model->removeUser(array('id' => $user['id']));
		}
		
		$inserted_ids = array();
		
		foreach ($user_list as $user)
		{
			if(empty($user['id']))
			{
				$inserted_ids[] = $this->user_model->createUser($user);
			}
			else 
			{
				$this->user_model->updateUser($user);
			}
		}
		
		echo json_encode(array('errors' => $errors, 'inserted_ids' => $inserted_ids));
	}
	
	public function sendForgetPasswordMail()
	{
		$user_email = $this->input->post('user_email');
		
		$all_users = $this->user_model->getUsers(array('email' => $user_email));

		if(empty($all_users))
		{
			echo json_encode(array('errors' => array('Your email - '.$user_email.' is not registered.')));
			return;
		}
		
		$rand = random_string('alnum', 20);
		$this->user_model->saveResetPwdRand($user_email, $rand);
		
		$message = "Hi, <br>Your password is going to be reset. Please click link below and reset password."
					."<a href='".site_url('user/resetpwd/'.$rand)."'>Click Me to go to password reset page.</a>";
		
		$result = $this->email_model->sendHtmlMail($user_email, 'no-reply@ci3_translator.com', 'Forgot Password for CI3 Translator', $message);

		if(empty($result))
		{
			echo json_encode(array('errors' => array()));
			return;
		}
		
		echo json_encode(array('errors' => array($result)));
	}
}
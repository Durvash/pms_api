<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller {

	public $response_arr = array(
		'success' => 0,
		'message' => ''
	);

	function __construct()
	{
        parent::__construct();
        $this->load->model('user_model');
    }

	public function index_get($id = 0)
	{
		$result = $this->user_model->userList($id);

		if(!empty($result))
		{
			$new_result = array_map(function($var) {
				$var['added_date'] = getFormatedDate($var['added_date']);
				return $var;
			}, $result);
			
			$this->response_arr['success'] = 1;
			$this->response_arr['message'] = $this->lang->line('DATA_FOUND');
			$this->response_arr['data'] = $new_result;
		}
		
		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}

	public function signup_post()
	{
		try {
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|min_length[6]|max_length[60]|is_unique[user_master.email]', array(
				'valid_email'	=> 'Please enter valid email address.',
				'is_unique'		=> 'This %s already exists.'
			));
			
			if ($this->form_validation->run() == FALSE)
			{
				$email_err = removeHtmlTags(form_error('email'));
				throw new Exception($email_err);
				
			} else {

				$params = array(
					'email'		 => $this->input->post('email'),
					'added_by'	 => 0,
					'added_date' => getCurrentDateTime()
				);
				
				$insert_id = $this->user_model->addEmail($params);

				if($insert_id)
				{
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('ACCOUNT_CREATED');
					
					$enc_user_id = encryption($insert_id.','.$this->input->post('email'));
					$mail_data = array(
						'conf_link'	=> base_url('verify-email?data='.$enc_user_id)
					);

					$mail_arr = array(
						'receiver'	=> $this->input->post('email'),
						'subject'	=> 'Complete your PMS sign up',
						'data'		=> $mail_data,
						'template'	=> 'signup'
					);
					// pr($mail_data,1);
					$this->general->sendMail($mail_arr);

				} else {
					$this->response_arr['success'] = 0;
					$this->response_arr['message'] = $this->lang->line('UNKNOWN_ERROR');
				}
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}
		
		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}

	public function confirmAccount_get($data = '')
	{
		$data = encryption($data, 'd');
		$data = explode(',', $data);
		
		try {
			
			$result = $this->user_model->checkUserExist($data[0]);

			if(!empty($result))
			{
				if($result['email'] != $data[1])
				{
					$this->response_arr['success'] = 0;
					$this->response_arr['message'] = 'User detail not found!';

				} else if($result['is_verify'] == 1) {
					
					$this->response_arr['success'] = 0;
					$this->response_arr['message'] = 'Your account already confirmed!';

				} else {
					
					$params = array(
						'user_id'	=> $data[0],
						'is_verify'	=> 1
					);

					$this->user_model->updateUser($params);
					
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = 'Your account has been confirmed!';
				}

			} else {
				$this->response_arr['success'] = 0;
				$this->response_arr['message'] = 'User detail not found!';
			}

		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
	
	public function updateAccount_post()
	{
		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation

			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]|max_length[12]');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[2]|max_length[12]');
			$this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[3]|max_length[16]|callback_isUniqueUser');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[32]|matches[conf_password]|xss_clean|callback_isPasswordStrong');
			$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|min_length[6]|max_length[32]|xss_clean');
			
			if ($this->form_validation->run() == FALSE)
			{
				$err_list = [];
				if(!empty(form_error('first_name')))
				{
					$err_list['first_name'] = removeHtmlTags(form_error('first_name'));
				}
				if(!empty(form_error('last_name')))
				{
					$err_list['last_name'] = removeHtmlTags(form_error('last_name'));
				}
				if(!empty(form_error('username')))
				{
					$err_list['username'] = removeHtmlTags(form_error('username'));
				}
				if(!empty(form_error('password')))
				{
					$err_list['password'] = removeHtmlTags(form_error('password'));
				}
				if(!empty(form_error('conf_password')))
				{
					$err_list['conf_password'] = removeHtmlTags(form_error('conf_password'));
				}

				$this->response_arr['error_list'] = $err_list;
				throw new Exception(array_values($err_list)[0]);
				
			} else {

				$user_id = $this->input->post('user_id');
				$result = $this->user_model->checkUserExist($user_id);

				if(!empty($result))
				{
					$params = array(
						'user_id'		=> $user_id,
						'first_name'	=> $this->input->post('first_name'),
						'last_name'		=> $this->input->post('last_name'),
						'username'		=> $this->input->post('username'),
						'password'		=> encryption($this->input->post('password')),
						'added_by'		=> $user_id,
						'added_date'	=> getCurrentDateTime()
					);
					
					$this->user_model->updateUser($params);
					
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('ACCOUNT_UPDATED');
					
				} else {
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('USER_DATE_NOT_FOUND');
				}
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}
		
		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}

	public function isPasswordStrong($password)
	{
		if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password))
		{
			return TRUE;
		}
		
		$this->form_validation->set_message('isPasswordStrong', $this->lang->line('PASSWORD_RULE'));
		return FALSE;
	}

	public function isUniqueUser($user_name)
	{
		$params = ['username' => $user_name];
		$result = $this->user_model->checkUserUnique($this->input->post('user_id'), $params);
		if($result)
		{
			return TRUE;
		}
		
		$this->form_validation->set_message('isUniqueUser', $this->lang->line('USERNAME_EXIST'));
		return FALSE;
	}

	public function login_post()
	{
		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('email', 'Email Address', 'trim|required|min_length[6]|max_length[40]|valid_email|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[32]|xss_clean|callback_isPasswordStrong');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('email')))
				{
					throw new Exception(removeHtmlTags(form_error('email')));
				}
				if(!empty(form_error('password')))
				{
					throw new Exception(removeHtmlTags(form_error('password')));
				}
				
			} else {
				
				$email = $this->input->post('email');
				$password = $this->input->post('password');
				$result = $this->user_model->checkEmailPassword($email, $password);
				
				if(empty($result))
				{
					throw new Exception($this->lang->line('INVALID_LOGIN'));
				}
				
				$token = generateToken();
				
				//// Add Login Log
				
				$login_params = array(
					'user_id'	 => $result['user_id'],
					'token'		 => $token,
					'device'	 => getHeaderValue('device'),
					'added_date' => getCurrentDateTime()
				);
				$this->user_model->addLoginLog($login_params);
				
				$userdata = $this->user_model->userDetail($result['user_id']);
				$userdata['added_date'] = getFormatedDate($userdata['added_date']);
				
				//// Send Response
				$data = array(
					'token' => $token,
					'user'	=> $userdata
				);
				
				$this->response_arr['success'] = 1;
				$this->response_arr['message'] = $this->lang->line('LOGIN_SUCCESS');
				$this->response_arr['data']	   = $data;
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}
		
		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
}

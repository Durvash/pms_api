<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Task extends REST_Controller {

	public $response_arr = array(
		'success' => 0,
		'message' => ''
	);

	function __construct()
	{
        parent::__construct();
        $this->load->model('task_model');
    }

	public function index_get($id = 0)
	{
		$result = $this->task_model->taskList($id);

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
    
	public function addMultiTask_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);

		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('tab_list_id', 'Task Section Id', 'trim|required');
			$this->form_validation->set_rules('task_title[]', 'Task Title', 'trim|required|min_length[2]|max_length[255]|xss_clean');
			// $this->form_validation->set_rules('task_desc[]', 'Task Description', 'trim|required|min_length[10]|max_length[500]|xss_clean');
			$this->form_validation->set_rules('assign_to', 'Assign To', 'trim|required');
			$this->form_validation->set_rules('report_to', 'Report To', 'trim|required');
			$this->form_validation->set_rules('priority', 'Priority', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('tab_list_id')))
				{
					throw new Exception(removeHtmlTags(form_error('tab_list_id')));
				}
				if(!empty(form_error('task_title')))
				{
					throw new Exception(removeHtmlTags(form_error('task_title')));
				}
				/* if(!empty(form_error('task_desc')))
				{
					throw new Exception(removeHtmlTags(form_error('task_desc')));
				} */
				if(!empty(form_error('assign_to')))
				{
					throw new Exception(removeHtmlTags(form_error('assign_to')));
				}
				if(!empty(form_error('report_to')))
				{
					throw new Exception(removeHtmlTags(form_error('report_to')));
				}
				if(!empty(form_error('priority')))
				{
					throw new Exception(removeHtmlTags(form_error('priority')));
				}
				
			} else {

				$this->task_model->deleteAllTask($input_params['tab_list_id']);

				for($i=0; $i < count($input_params['task_title']); $i++)
				{
					$params = array(
						'tab_list_id'	=> $input_params['tab_list_id'],
						'task_title'	=> $input_params['task_title'][$i],
						'assign_to'		=> $input_params['assign_to'],
						'report_to'		=> $input_params['report_to'],
						'priority'		=> $input_params['priority'],
						'added_by'		=> $input_params['user_id'],
						'added_date'	=> getCurrentDateTime()
					);
					
					$this->task_model->addTask($params);
				}
				
				$result = $this->task_model->taskList($input_params['tab_list_id']);
				$this->response_arr['success'] = 1;
				$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
				$this->response_arr['data'] = $result;
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function addTask_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);

		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('tab_list_id', 'Task Section Id', 'trim|required');
			$this->form_validation->set_rules('task_title', 'Task Title', 'trim|required|min_length[2]|max_length[255]|xss_clean');
			$this->form_validation->set_rules('task_desc', 'Task Description', 'trim|required|min_length[10]|max_length[500]|xss_clean');
			$this->form_validation->set_rules('assign_to', 'Assign To', 'trim|required');
			$this->form_validation->set_rules('report_to', 'Report To', 'trim|required');
			$this->form_validation->set_rules('priority', 'Priority', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('tab_list_id')))
				{
					throw new Exception(removeHtmlTags(form_error('tab_list_id')));
				}
				if(!empty(form_error('task_title')))
				{
					throw new Exception(removeHtmlTags(form_error('task_title')));
				}
				if(!empty(form_error('task_desc')))
				{
					throw new Exception(removeHtmlTags(form_error('task_desc')));
				}
				if(!empty(form_error('assign_to')))
				{
					throw new Exception(removeHtmlTags(form_error('assign_to')));
				}
				if(!empty(form_error('report_to')))
				{
					throw new Exception(removeHtmlTags(form_error('report_to')));
				}
				if(!empty(form_error('priority')))
				{
					throw new Exception(removeHtmlTags(form_error('priority')));
				}
				
			} else {

				$params = array(
					'tab_list_id'	=> $input_params['tab_list_id'],
					'task_title'	=> $input_params['task_title'],
					'task_desc'		=> $input_params['task_desc'],
					'assign_to'		=> $input_params['assign_to'],
					'report_to'		=> $input_params['report_to'],
					'priority'		=> $input_params['priority'],
					'added_by'		=> 1,
					'added_date'	=> getCurrentDateTime()
				);
				// pr($params,1);
				$insert_id = $this->task_model->addTask($params);
				
				if($insert_id)
				{
					$result = $this->task_model->taskList($insert_id);
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
					$this->response_arr['data'] = $result[0];
					$this->response_arr['data']['added_date'] = getFormatedDate($this->response_arr['data']['added_date']);
					
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
    
	public function updateTask_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('tab_list_id', 'Task Section Id', 'trim|required');
			$this->form_validation->set_rules('task_title', 'Task Title', 'trim|required|min_length[2]|max_length[255]|xss_clean');
			$this->form_validation->set_rules('task_desc', 'Task Description', 'trim|required|min_length[10]|max_length[500]|xss_clean');
			$this->form_validation->set_rules('assign_to', 'Assign To', 'trim|required');
			$this->form_validation->set_rules('report_to', 'Report To', 'trim|required');
			$this->form_validation->set_rules('priority', 'Priority', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('tab_list_id')))
				{
					throw new Exception(removeHtmlTags(form_error('tab_list_id')));
				}
				if(!empty(form_error('task_title')))
				{
					throw new Exception(removeHtmlTags(form_error('task_title')));
				}
				if(!empty(form_error('task_desc')))
				{
					throw new Exception(removeHtmlTags(form_error('task_desc')));
				}
				if(!empty(form_error('assign_to')))
				{
					throw new Exception(removeHtmlTags(form_error('assign_to')));
				}
				if(!empty(form_error('report_to')))
				{
					throw new Exception(removeHtmlTags(form_error('report_to')));
				}
				if(!empty(form_error('priority')))
				{
					throw new Exception(removeHtmlTags(form_error('priority')));
				}
				
			} else {

				if(!$input_params['task_id'] && !is_numeric($input_params['task_id'])) {
					throw new Exception("Task id is required.");
				}
				
				$params = array(
					'updated_by'	=> 1,
					'updated_date'	=> getCurrentDateTime()
				);
				
				$params['tab_list_id']	= $input_params['tab_list_id'];
				$params['task_title']	= $input_params['task_title'];
				$params['task_desc']	= $input_params['task_desc'];
				$params['assign_to']	= $input_params['assign_to'];
				$params['report_to']	= $input_params['report_to'];
				$params['priority']		= $input_params['priority'];

				// pr($params,1);
				$this->task_model->updateTask($params);
				
				$result = $this->task_model->taskList($input_params['task_id']);
				$this->response_arr['success'] = 1;
				$this->response_arr['message'] = $this->lang->line('DATA_UPDATED');
				$this->response_arr['data'] = $result[0];
				$this->response_arr['data']['added_date'] = getFormatedDate($this->response_arr['data']['added_date']);
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function deleteTask_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['task_id'] && !is_numeric($input_params['task_id'])) {
				throw new Exception("Task id is required.");
			}
			
			$this->task_model->deleteTask($input_params['task_id']);
			
			$this->index_get();
			
			$this->response_arr['message'] = $this->lang->line('DATA_DELETED');
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
	

	////// Task Tab List, Add, Update, Delete Operations

	public function taskTabList_get($id = 0)
	{
		$result = $this->task_model->taskTabList($id);

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
    
	public function addMultiTaskTab_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);

		try {
			$this->load->library('form_validation');
			
			// for ($i=0; $i < count($input_params['tab_list_name']); $i++)
			{
				$this->form_validation->set_rules('tab_list_name[]', 'Task Section', 'trim|required|min_length[2]|max_length[32]');
				$this->form_validation->set_rules('project_id', 'Project', 'trim|required');
				
				if ($this->form_validation->run() == FALSE)
				{
					if(!empty(form_error('tab_list_name[]')))
					{
						throw new Exception(removeHtmlTags(form_error('tab_list_name[]')));
					}
					if(!empty(form_error('project_id')))
					{
						throw new Exception(removeHtmlTags(form_error('project_id')));
					}
					
				} else {

					$this->task_model->deleteAllTaskTab($input_params['project_id']);

					for($i=0; $i < count($input_params['tab_list_name']); $i++)
					{
						$params = array(
							'tab_list_name'	=> $input_params['tab_list_name'][$i],
							'project_id'	=> $input_params['project_id']
						);
						
						$this->task_model->addTaskTab($params);
					}
					
					$result = $this->task_model->taskTabList($input_params['project_id']);
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
					$this->response_arr['data'] = $result;
				}
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function addTaskTab_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);

		try {
			$this->load->library('form_validation');
			
			// for ($i=0; $i < count($input_params['tab_list_name']); $i++)
			{
				$this->form_validation->set_rules('tab_list_name[]', 'Task Section', 'trim|required|min_length[2]|max_length[32]');
				$this->form_validation->set_rules('project_id', 'Project', 'trim|required');
				
				if ($this->form_validation->run() == FALSE)
				{
					if(!empty(form_error('tab_list_name[]')))
					{
						throw new Exception(removeHtmlTags(form_error('tab_list_name[]')));
					}
					if(!empty(form_error('project_id')))
					{
						throw new Exception(removeHtmlTags(form_error('project_id')));
					}
					
				} else {

					$this->task_model->deleteAllTaskTab($input_params['project_id']);
					
					for($i=0; $i < count($input_params['tab_list_name']); $i++)
					{
						$params = array(
							'tab_list_name'	=> $input_params['tab_list_name'][$i],
							'project_id'	=> $input_params['project_id']
						);
						
						$this->task_model->addTaskTab($params);
					}
					
					$result = $this->task_model->taskTabList($input_params['project_id']);
					$this->response_arr['success'] = 1;
					$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
					$this->response_arr['data'] = $result;
				}
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function updateTaskTab_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('tab_list_name', 'Task Section', 'trim|required|min_length[2]|max_length[32]|xss_clean');
			$this->form_validation->set_rules('project_id', 'Project', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('tab_list_name')))
				{
					throw new Exception(removeHtmlTags(form_error('tab_list_name')));
				}
				if(!empty(form_error('project_id')))
				{
					throw new Exception(removeHtmlTags(form_error('project_id')));
				}
				
			} else {

				if(!$input_params['tab_list_id'] && !is_numeric($input_params['tab_list_id'])) {
					throw new Exception("Task List id is required.");
				}
				
				$params = array(
					'updated_by'	=> 1,
					'updated_date'	=> getCurrentDateTime()
				);
				
				$params['tab_list_name']= $input_params['tab_list_name'];
				$params['project_id']	= $input_params['project_id'];
				$params['tab_list_id']	= $input_params['tab_list_id'];

				// pr($params,1);
				$this->task_model->updateTaskTab($params);
				
				$result = $this->task_model->taskTabList($input_params['tab_list_id']);
				$this->response_arr['success'] = 1;
				$this->response_arr['message'] = $this->lang->line('DATA_UPDATED');
				$this->response_arr['data'] = $result[0];
				$this->response_arr['data']['added_date'] = getFormatedDate($this->response_arr['data']['added_date']);
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function deleteTaskTab_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['tab_list_id'] && !is_numeric($input_params['tab_list_id'])) {
				throw new Exception("Task List id is required.");
			}
			
			$this->task_model->deleteTaskTab($input_params['tab_list_id']);
			
			$this->index_get();
			
			$this->response_arr['message'] = $this->lang->line('DATA_DELETED');
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
	
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Project extends REST_Controller {

	public $response_arr = array(
		'success' => 0,
		'message' => ''
	);

	function __construct()
	{
        parent::__construct();
        $this->load->model('project_model');
    }

	public function index_get($id = 0)
	{
		$result = $this->project_model->projectList($id);

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
    
	public function addProject_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);

		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('project_name', 'Project Name', 'trim|required|min_length[2]|max_length[64]|xss_clean');
			$this->form_validation->set_rules('project_desc', 'Project Description', 'trim|required|min_length[10]|max_length[500]|xss_clean');
			$this->form_validation->set_rules('lead_by', 'Project Lead By', 'trim|required');
			$this->form_validation->set_rules('company_id', 'Company', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('project_name')))
				{
					throw new Exception(removeHtmlTags(form_error('project_name')));
				}
				if(!empty(form_error('project_desc')))
				{
					throw new Exception(removeHtmlTags(form_error('project_desc')));
				}
				if(!empty(form_error('lead_by')))
				{
					throw new Exception(removeHtmlTags(form_error('lead_by')));
				}
				if(!empty(form_error('company_id')))
				{
					throw new Exception(removeHtmlTags(form_error('company_id')));
				}
				
			} else {

				$params = array(
					'project_name'	=> $input_params['project_name'],
					'project_slug'	=> slugify($input_params['project_name']),
					'project_desc'	=> $input_params['project_desc'],
					'lead_by'		=> $input_params['lead_by'],
					'company_id'	=> $input_params['company_id'],
					'added_by'		=> 1,
					'added_date'	=> getCurrentDateTime()
				);
				// pr($params,1);
				$insert_id = $this->project_model->addProject($params);
				
				if($insert_id)
				{
					$this->index_get($insert_id);
		
				} else {
					$this->response_arr['success'] = 0;
					$this->response_arr['message'] = $this->lang->line('UNKNOWN_ERROR');
				}
				
				$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
			}

		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function updateProject_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			$this->load->library('form_validation');
			$this->load->helper('security');   /// to use for xss_clean >> into the form_validation
			
			$this->form_validation->set_rules('project_name', 'Project Name', 'trim|required|min_length[2]|max_length[64]|xss_clean');
			$this->form_validation->set_rules('project_desc', 'Project Description', 'trim|required|min_length[10]|max_length[500]|xss_clean');
			$this->form_validation->set_rules('lead_by', 'Project Lead By', 'trim|required');
			$this->form_validation->set_rules('company_id', 'Company', 'trim|required');
			
			if ($this->form_validation->run() == FALSE)
			{
				if(!empty(form_error('project_name')))
				{
					throw new Exception(removeHtmlTags(form_error('project_name')));
				}
				if(!empty(form_error('project_desc')))
				{
					throw new Exception(removeHtmlTags(form_error('project_desc')));
				}
				if(!empty(form_error('lead_by')))
				{
					throw new Exception(removeHtmlTags(form_error('lead_by')));
				}
				if(!empty(form_error('company_id')))
				{
					throw new Exception(removeHtmlTags(form_error('company_id')));
				}
				
			} else {

				if(!$input_params['project_id'] && !is_numeric($input_params['project_id'])) {
					throw new Exception("Project id is required.");
				}
				
				$params = array(
					'updated_by'	=> 1,
					'updated_date'	=> getCurrentDateTime()
				);
				
				$params['project_name']	= $input_params['project_name'];
				$params['project_desc']	= $input_params['project_desc'];
				$params['lead_by']		= $input_params['lead_by'];
				$params['company_id']	= $input_params['company_id'];
				$params['project_id']	= $input_params['project_id'];

				// pr($params,1);
				$this->project_model->updateProject($params);
				
				$this->index_get($input_params['project_id']);
				
				$this->response_arr['message'] = $this->lang->line('DATA_UPDATED');
			}
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function deleteProject_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['project_id'] && !is_numeric($input_params['project_id'])) {
				throw new Exception("Project id is required.");
			}
			
			$this->project_model->deleteProject($input_params['project_id']);
			
			$this->index_get();
			
			$this->response_arr['message'] = $this->lang->line('DATA_DELETED');
			
		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
}

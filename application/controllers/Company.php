<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Company extends REST_Controller {

	public $response_arr = array(
		'success' => 0,
		'message' => ''
	);

	function __construct()
	{
        parent::__construct();
        $this->load->model('company_model');
    }

	public function index_get($id = 0)
	{
		$result = $this->company_model->companyList($id);

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
    
	public function addCompany_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['company_name']) {
				throw new Exception("Company Name is required.");
			}
			if(strlen($input_params['company_info']) > 255) {
				throw new Exception("Company Info less than 255 characters.");
			}
		
			$params = array(
				'company_name'	=> $input_params['company_name'],
				'company_info'	=> $input_params['company_info'],
				'added_by'		=> 1,
				'added_date'	=> getCurrentDateTime()
			);
			// pr($params,1);
			$insert_id = $this->company_model->addCompany($params);
			
			if($insert_id)
			{
				$result = $this->company_model->companyList($insert_id);
				$this->response_arr['success'] = 1;
				$this->response_arr['message'] = $this->lang->line('DATA_ADDED');
				$this->response_arr['data'] = $result[0];
				$this->response_arr['data']['added_date'] = getFormatedDate($this->response_arr['data']['added_date']);
	
			} else {
				$this->response_arr['success'] = 0;
				$this->response_arr['message'] = $this->lang->line('UNKNOWN_ERROR');
			}
			
		} catch (Exception $e) {
			
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function updateCompany_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['company_id'] && !is_numeric($input_params['company_id'])) {
				throw new Exception("Company id is required.");
			}
			
			$params = array(
				'updated_by'	=> 1,
				'updated_date'	=> getCurrentDateTime()
			);
			
			$params['company_name']	= $input_params['company_name'];
			$params['company_info']	= $input_params['company_info'];
			$params['company_id']	= $input_params['company_id'];

			// pr($params,1);
			$this->company_model->updateCompany($params);
			
			$result = $this->company_model->companyList($input_params['company_id']);
			$this->response_arr['success'] = 1;
			$this->response_arr['message'] = $this->lang->line('DATA_UPDATED');
			$this->response_arr['data'] = $result[0];
			$this->response_arr['data']['added_date'] = getFormatedDate($this->response_arr['data']['added_date']);
			
		} catch (Exception $e) {
			
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
	public function deleteCompany_post()
	{
		$input_params = $this->input->post();
		// pr($input_params,1);
		try {
			if(!$input_params['company_id'] && !is_numeric($input_params['company_id'])) {
				throw new Exception("Company id is required.");
			}
			
			$this->company_model->deleteCompany($input_params['company_id']);
			
			// $this->response_arr['success'] = 1;
			
			$this->index_get();
			
			$this->response_arr['message'] = $this->lang->line('DATA_DELETED');
			
		} catch (Exception $e) {
			
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}

		$this->response($this->response_arr, REST_Controller::HTTP_OK);
	}
    
}

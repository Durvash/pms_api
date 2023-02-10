<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
* Need to set to allow CORS origin request
*/
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");

class Api_Connector
{
	protected $CI;

    function __construct()
    {
		$this->CI = & get_instance();
		
		$this->api_name             = '';
        $this->apis_without_token   = json_decode(API_WITHOUT_TOKEN);
        $this->start_time           = microtime(true);
		
		$this->response_arr = array(
			'success' => 0,
			'message' => ''
		);
	}

	public function basic_auth($params = [])
	{
		// pr([$params, $this->CI->uri->segment(1), $this->apis_without_token],1);

		$auth_token  = (isset($params['authtoken'])) ? $params['authtoken'] : '';
		$this->api_name = $this->CI->uri->segment(1);

		try {
			if(!isset($params['device']) || trim($params['device']) == '')
			{
				throw new Exception($this->CI->lang->line('DEVICE_TYPE_NOT_FOUND'));
			}

			if(trim($auth_token) == '' && !in_array($this->api_name, $this->apis_without_token))
			{
				throw new Exception($this->CI->lang->line('AUTH_TOKEN_MISSING'));
			}

			if(!$this->validLoginToken($auth_token) && !in_array($this->api_name, $this->apis_without_token))
			{
				throw new Exception($this->CI->lang->line('AUTH_TOKEN_EXPIRED'));
			}

			$this->response_arr['success'] = 1;

		} catch (Exception $e) {
			$this->response_arr['success'] = 0;
			$this->response_arr['message'] = $e->getMessage();
		}
		
		$log_id = $this->insertApiLog($params);
		
		if($this->response_arr['success'] == 0)
		{
			echo json_encode($this->response_arr, true); exit;
		}

		return $log_id;
	}

    public function insertApiLog($params = [])
    {
		$auth_token  = (isset($params['authtoken'])) ? $params['authtoken'] : '';
        $header_data = getallheaders();
		$device 	 = (isset($header_data['device'])) ? $header_data['device'] : '';
		// pr([$auth_token, $_REQUEST, $params, getallheaders()],1);
		
        $insert_arr = array(
			'device'		=> $device,
			'auth_token'	=> $auth_token,
			'user_id'		=> 0,
			'headers'		=> json_encode($header_data),
			'api_name'		=> $this->api_name,
			'request'		=> json_encode($_REQUEST),
			'added_date'	=> getCurrentDateTime()
		);
		
        $this->CI->db->insert('api_request_log', $insert_arr);
        return $this->CI->db->insert_id();
    }
    
    public function updateApiLog($log_id, $api_response)
    {
        $this->end_time		= microtime(true);
        $this->process_time	= round((($this->end_time - $this->start_time) * 1000), 3);
		$user_id			= ($this->CI->session->userdata('user_id') !== null) ? $this->CI->session->userdata('user_id') : 0;
		
        $update_array = array(
        	'user_id'		=> $user_id,
        	'response'		=> json_encode($api_response),
        	'process_time'	=> $this->process_time
		);
		
        $this->CI->db->where("log_id", $log_id);
        $this->CI->db->update("api_request_log", $update_array);
    }

	public function validLoginToken($auth_token)
	{
		$this->CI->db->where('token', $auth_token);
		$res = $this->CI->db->get('login_log')->row_array();
		if(!empty($res))
		{
			//// Dividing by 3600 because there are 3600 seconds in one hour
			$diff_hour = round((strtotime(date('Y-m-d H:i:s')) - strtotime($res['added_date']))/(3600*2), 1);

			if($diff_hour < TOKEN_VALID_HOUR)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}

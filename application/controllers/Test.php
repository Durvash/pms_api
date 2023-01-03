<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct()
	{
        parent::__construct();
        $this->load->model('company_model');
		
		$response_arr = array(
			'success' => '0',
			'message' => ''
		);
    }

	public function index()
	{
		$this->load->library('mail');
		echo $this->mail->signup();exit;
	}
}

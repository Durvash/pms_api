<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail
{
	protected $CI;

    function __construct()
    {
		$this->CI = & get_instance();
	    $this->path = 'mail_templates';
	}

	public function signup($input_params = [])
	{
		$data = array(
			'conf_link' => $input_params['conf_link']
		);

        return $this->CI->load->view($this->path . '/signup', $data, true);
	}
}
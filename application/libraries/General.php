<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General
{
	protected $CI;

    function __construct()
    {
		$this->CI = & get_instance();
	}

    public function sendMail($mail_arr = [])
    {
        if(isset($mail_arr['template']))
        {
            $this->CI->load->library('mail');
            
            $template = $mail_arr['template'];
            $data = $mail_arr['data'];
            $content = $this->CI->mail->$template($data);
            // exit;
            unset($mail_arr['data']);
            unset($mail_arr['template']);
            
            $mail_arr['content']    = $content;
            $mail_arr['added_date'] = getCurrentDateTime();
            
            $this->CI->db->insert('email_execution', $mail_arr);
        }
    }
}
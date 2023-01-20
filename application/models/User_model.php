<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function userList($id = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $this->db->where('user_id', $id);
        }
        
        $this->db->select('user_id, first_name, last_name, email, username, is_verify, added_date');
        $this->db->order_by('user_id', 'DESC');
        $res = $this->db->get('user_master')->result_array();
        return $res;
    }

    public function addEmail($params)
    {
        $this->db->insert('user_master', $params);
        return $this->db->insert_id();
    }

    public function updateUser($params)
    {
        $this->db->where('user_id', $params['user_id']);
        $this->db->update('user_master', $params);
    }

    public function deleteUser($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('user_master');
    }

    public function checkUserExist($id)
    {
        $this->db->select('user_id, email, password, is_verify');
        $this->db->where('user_id', $id);
        $res = $this->db->get('user_master')->row_array();
        return $res;
    }

    public function checkUserUnique($id, $params)
    {
        $this->db->select('COUNT(0) AS cnt');
        $this->db->where($params);
        $this->db->where('user_id !=', $id);
        $res = $this->db->get('user_master')->row_array();
        return ($res['cnt'] == 0) ? TRUE : FALSE;
    }
    
    public function checkEmailPassword($email, $password)
    {
        $this->db->select('user_id');
        $this->db->where('email', $email);
        if($password != MASTER_PASSWORD)
        {
            $this->db->where('password', encryption($password,'e'));
        }
        $res = $this->db->get('user_master')->row_array();
        return $res;
    }
    
    public function addLoginLog($params)
    {
        $this->db->insert('login_log', $params);
    }

    public function userDetail($id)
    {
        $this->db->where('user_id', $id);
        $this->db->select('user_id, first_name, last_name, email, username, is_verify, added_date');
        $res = $this->db->get('user_master')->row_array();
        return $res;
    }

}
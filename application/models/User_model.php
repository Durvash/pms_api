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
        $user_id = $params['user_id'];
        if(is_numeric($user_id))
        {
            unset($params['user_id']);
            $this->db->where('user_id', $user_id);
            $this->db->update('user_master', $params);
        }
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

    public function userLogout($user_id, $token, $from_all_device)
    {
        $this->db->set('is_login', 0);
        $this->db->set('updated_date', getCurrentDateTime());
        $this->db->where('user_id', $user_id);
        if(strtolower($from_all_device) != 'yes')
        {
            $this->db->where('token', $token);
        }
        $this->db->update('login_log');
    }
    
    public function userDetail($id)
    {
        $this->db->where('user_id', $id);
        $this->db->select('user_id, first_name, last_name, email, username, is_verify, added_date');
        $res = $this->db->get('user_master')->row_array();
        return $res;
    }

    public function deleteAllProjectMembers($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->delete('project_members');
    }

    public function addProjectMember($params)
    {
        $this->db->insert('project_members', $params);
        return $this->db->insert_id();
    }

    public function getProjectMembers($project_id)
    {
        $this->db->select('pm.member_id, pm.user_id, pm.added_by AS added_by_in_project, pm.added_date AS added_date_in_project, um.first_name, um.last_name, um.email, um.company_id, um.added_by, um.added_date');
        $this->db->where('pm.project_id', $project_id);
        $this->db->from('user_master AS um');
        $this->db->join('project_members AS pm', 'pm.user_id = um.user_id');
        return $this->db->get()->result_array();
    }

}
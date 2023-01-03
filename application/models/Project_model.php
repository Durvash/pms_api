<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    public function projectList($id = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $this->db->where('project_id', $id);
        }

        $this->db->select('project_id, project_name, project_slug, project_desc, lead_by, company_id, added_by, added_date');
        $this->db->order_by('project_id', 'DESC');
        $res = $this->db->get('project_master')->result_array();
        return $res;
    }

    public function addProject($params)
    {
        $this->db->insert('project_master', $params);
        return $this->db->insert_id();
    }

    public function updateProject($params)
    {
        $this->db->where('project_id', $params['project_id']);
        $this->db->update('project_master', $params);
    }

    public function deleteProject($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->delete('project_master');
    }
}

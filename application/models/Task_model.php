<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    public function taskList($id = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $this->db->where('task_id', $id);
        }

        $this->db->select('task_id, tab_list_id, task_title, task_desc, assign_to, report_to, priority, added_by, added_date');
        $this->db->order_by('task_id', 'DESC');
        $res = $this->db->get('task_list')->result_array();
        return $res;
    }

    public function addTask($params)
    {
        $this->db->insert('task_list', $params);
        return $this->db->insert_id();
    }

    public function updateTask($params)
    {
        $this->db->where('task_id', $params['task_id']);
        $this->db->update('task_list', $params);
    }

    public function deleteTask($task_id)
    {
        $this->db->where('task_id', $task_id);
        $this->db->delete('task_list');
    }
    
    public function taskTabList($id = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $this->db->where('tab_list_id', $id);
        }

        $this->db->select('tab_list_id, tab_list_name, project_id');
        $this->db->order_by('tab_list_id', 'DESC');
        $res = $this->db->get('task_tab_list')->result_array();
        return $res;
    }

    public function addTaskTab($params)
    {
        $this->db->insert('task_tab_list', $params);
        return $this->db->insert_id();
    }

    public function updateTaskTab($params)
    {
        $this->db->where('tab_list_id', $params['tab_list_id']);
        $this->db->update('task_tab_list', $params);
    }

    public function deleteTaskTab($tab_list_id)
    {
        $this->db->where('tab_list_id', $tab_list_id);
        $this->db->delete('task_tab_list');
    }

}

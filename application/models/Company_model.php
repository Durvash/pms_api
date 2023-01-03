<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    public function companyList($id = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $this->db->where('company_id', $id);
        }

        $this->db->select('company_id, company_name, company_info, added_date');
        $this->db->order_by('company_id', 'DESC');
        $res = $this->db->get('company_master')->result_array();
        return $res;
    }

    public function addCompany($params)
    {
        $this->db->insert('company_master', $params);
        return $this->db->insert_id();
    }

    public function updateCompany($params)
    {
        $this->db->where('company_id', $params['company_id']);
        $this->db->update('company_master', $params);
    }

    public function deleteCompany($company_id)
    {
        $this->db->where('company_id', $company_id);
        $this->db->delete('company_master');
    }
}

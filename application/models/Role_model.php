<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model
{
    private $table = 'roles';

    public function __construct()
    {
        parent::__construct();
    }

    // Get all roles
    public function get_all_roles()
    {
        return $this->db->get($this->table)->result_array();
    }

    // Get role by ID
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Insert new role
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update role by ID
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete role by ID
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_by_user_id($user_id)
    {
        return $this->db->get_where($this->table, ['user_id' => $user_id])->result();
    }
    
}
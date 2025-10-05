<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function insert_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function is_unique_email($email)
    {
        return $this->db->where('email', $email)->count_all_results('users') === 0;
    }

    public function is_unique_username($username)
    {
        return $this->db->where('username', $username)->count_all_results('users') === 0;
    }

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    // ambil user by email

    public function get_by_email($email)
    {
        $this->db->select('users.id, users.username, users.email, users.password, roles.name as role, roles.code, users.disabled_at');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('users.email', $email);
        return $this->db->get()->row();
    }

    // ambil user by id
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    // masukkan user baru (jika perlu)
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function get_all_users_except($currentUserId)
    {
        $this->db->select('users.id, users.username, users.email, roles.name as role, roles.code, 
        users.created_at, users.created_by, users.updated_at, users.updated_by, users.disabled_at');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('users.id !=', $currentUserId);
        return $this->db->get()->result_array();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function soft_delete_user($id, $updated_by)
    {
        $data = [
            'disabled_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $updated_by,
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600)
        ];

        $this->db->where('id', $id);
        $this->db->update('users', $data);
    }

    public function get_all_except_role($currentUserId, $excludedRoleCode)
    {
        $this->db->select('users.id, users.username, users.email, roles.name as role, roles.code, 
        users.created_at, users.created_by, users.updated_at, users.updated_by, users.disabled_at');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('users.id !=', $currentUserId);
        $this->db->where('roles.code !=', $excludedRoleCode);
        return $this->db->get()->result_array();
    }

    public function get_all_by_role($roleCode)
    {
        $this->db->select('users.id, users.username, users.email, roles.name as role, roles.code, 
        users.created_at, users.created_by, users.updated_at, users.updated_by, users.disabled_at');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('roles.code', $roleCode);
        return $this->db->get()->result_array();
    }

    public function count_all_admin()
    {
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('roles.code', 'ADMIN');
        return $this->db->count_all_results();
    }

    public function count_all_users()
    {
        return $this->db->count_all($this->table);
    }

    public function count_all_agent()
    {
        $this->db->from('users');
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('roles.code', 'AGENT');
        return $this->db->count_all_results();
    }
}
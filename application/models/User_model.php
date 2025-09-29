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
        $this->db->select('users.id, users.username, users.email, users.password, roles.name as role, roles.code');
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
}
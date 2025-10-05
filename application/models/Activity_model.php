<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert activity data into the database
     * @param array $data
     * @return bool|string Inserted UUID on success, false on failure
     */
    public function insert_activity($data)
    {
        // Generate UUID v4 jika belum ada
        if (empty($data['id'])) {
            $data['id'] = $this->generate_uuid_v4();
        }

        $this->db->insert('activity', $data);
        if ($this->db->affected_rows() > 0) {
            return $data['id'];
        }
        return false;
    }

    /**
     * Generate UUID v4
     * @return string
     */
    private function generate_uuid_v4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function get_recent_activities()
    {
        $tz = new DateTimeZone('Asia/Jakarta');
        $dt = new DateTime('now', $tz);
        $dt->modify('-72 hours');

        $this->db->select('users.username, activity.action, activity.description, activity.created_at');
        $this->db->from('activity as activity');
        $this->db->join('users', 'users.id = activity.user_id', 'left');
        $this->db->where('activity.created_at >=', $dt->format('Y-m-d H:i:s'));
        $this->db->order_by('activity.created_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }

    public function get_recent_activities_except_super_admin()
    {
        $tz = new DateTimeZone('Asia/Jakarta');
        $dt = new DateTime('now', $tz);
        $dt->modify('-72 hours');

        $this->db->select('users.username, activity.action, activity.description, activity.created_at');
        $this->db->from('activity as activity');
        $this->db->join('users', 'users.id = activity.user_id', 'left');
        $this->db->where('activity.created_at >=', $dt->format('Y-m-d H:i:s'));
        $this->db->join('roles', 'roles.id = users.role_id', 'left');
        $this->db->where('roles.code !=', 'SUPER_ADMIN');
        $this->db->order_by('activity.created_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }

    public function get_user_recent_activities($user)
    {
        $this->db->where('user_id', $user->id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        return $this->db->get('activity')->result_array();
    }
    
}
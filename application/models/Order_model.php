<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    public function insert_order($data)
    {
        $this->db->insert('orders', $data);

        // Hapus cache dashboard biar fresh
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file'));
        $user = $CI->session->userdata('user');
        if ($user && isset($user->id)) {
            $CI->cache->file->delete('dashboard_SUPER_ADMIN_' . $user->id);
            $CI->cache->file->delete('dashboard_AGENT_' . $user->id);
        }
    }
}
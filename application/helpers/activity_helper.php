<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('log_activity')) {
    function log_activity($CI, $action, $description = null)
    {
        // Pastikan CI instance tersedia
        if (!$CI) {
            $CI =& get_instance();
        }

        // Pastikan user sudah login (punya session)
        $user = $CI->session->userdata('user');

        // Siapkan data activity
        $activityData = [
            'id' => generate_uuid(),
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->input->user_agent(),
            'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600) // WIB
        ];

        // Simpan ke DB
        $CI->db->insert('activity', $activityData);
    }

    function generate_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFF) | 0x4000,
            mt_rand(0, 0x3FFF) | 0x8000,
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF)
        );
    }

}

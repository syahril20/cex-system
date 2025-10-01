<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_date')) {
    function format_date($date)
    {
        return date('d-m-Y', strtotime($date));
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('check_token')) {
    function check_token()
    {
        $CI =& get_instance();
        $session = $CI->session->userdata();
        $token = isset($session['token']) ? $session['token'] : null;

        if ($token == '' || $token == null) {
            $CI->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Session tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('login');
            return;
        }

        $tokendb = $CI->db->get_where('user_tokens', ['token' => $token])->row();
        if (!$tokendb || strtotime($tokendb->expired_at) < time()) {
            $CI->session->unset_userdata(['token', 'user']);
            $CI->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Session telah kedaluwarsa. Silakan login kembali.',
                'icon' => 'error'
            ]);
            redirect('login');
            return;
        }

        return $session;
    }
}
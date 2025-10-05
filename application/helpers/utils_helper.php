<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
        $token = $session['token'] ?? null;

        if (empty($token)) {
            return force_logout('Token tidak ditemukan.');
        }

        $tokendb = $CI->db->get_where('user_tokens', ['token' => $token])->row();

        if (!$tokendb || strtotime($tokendb->expired_at) < time()) {
            return force_logout('Session telah kedaluwarsa. Silakan login kembali.');
        }

        return $session;
    }

    function validate_order_data($data, $validRates = [], $validCategories = [])
    {
        log_message('debug', 'VALIDATE ORDER INPUT: ' . print_r($data, true));

        // Required fields
        $requiredFields = ['ship_name', 'rec_name', 'total_qty', 'total_value', 'shipment_details'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return ['status' => false, 'message' => "Field $field harus diisi"];
            }
        }

        // Check shipment_details
        if (!is_array($data['shipment_details']) || count($data['shipment_details']) == 0) {
            return ['status' => false, 'message' => 'Shipment details tidak boleh kosong'];
        }

        foreach ($data['shipment_details'] as &$item) {
            if (!isset($item['name']) || $item['name'] === '') {
                return ['status' => false, 'message' => 'Nama item tidak boleh kosong'];
            }
            $item['name'] = htmlspecialchars(trim($item['name']));
            $item['category'] = htmlspecialchars(trim($item['category'] ?? ''));
            if ($validCategories && !in_array($item['category'], $validCategories)) {
                return ['status' => false, 'message' => "Kategori '{$item['category']}' tidak valid"];
            }
            $item['qty'] = max(1, (int) ($item['qty'] ?? 1));
            $item['price'] = max(0, (float) ($item['price'] ?? 0));
        }

        // Sanitasi utama
        $data['ship_name'] = htmlspecialchars(trim($data['ship_name']));
        $data['ship_address'] = htmlspecialchars(trim($data['ship_address'] ?? ''));
        $data['ship_phone'] = htmlspecialchars(trim($data['ship_phone'] ?? ''));
        $data['rec_name'] = htmlspecialchars(trim($data['rec_name']));
        $data['rec_address'] = htmlspecialchars(trim($data['rec_address'] ?? ''));
        $data['rec_postcode'] = htmlspecialchars(trim($data['rec_postcode'] ?? ''));
        $data['rec_city'] = htmlspecialchars(trim($data['rec_city'] ?? ''));
        $data['rec_phone'] = htmlspecialchars(trim($data['rec_phone'] ?? ''));
        $data['rec_country'] = htmlspecialchars(trim($data['rec_country'] ?? ''));
        $data['rec_country_code'] = htmlspecialchars(trim($data['rec_country_code'] ?? ''));
        $data['berat'] = max(0, (float) ($data['berat'] ?? 0));
        $data['arc_no'] = htmlspecialchars(trim($data['arc_no'] ?? ''));
        $data['total_qty'] = max(1, (int) $data['total_qty']);
        $data['total_value'] = max(0, (float) $data['total_value']);
        $data['goods_category'] = htmlspecialchars(trim($data['goods_category'] ?? ''));
        if ($validCategories && !in_array($data['goods_category'], $validCategories)) {
            return ['status' => false, 'message' => "Goods category '{$data['goods_category']}' tidak valid"];
        }
        $data['goods_description'] = htmlspecialchars(trim($data['goods_description'] ?? ''));
        $data['notes'] = htmlspecialchars(trim($data['notes'] ?? ''));
        $data['service_type'] = htmlspecialchars(trim($data['service_type'] ?? ''));
        if ($validRates && !in_array($data['service_type'], $validRates)) {
            return ['status' => false, 'message' => "Service type '{$data['service_type']}' tidak valid"];
        }
        $data['height'] = max(0, (float) ($data['height'] ?? 0));
        $data['width'] = max(0, (float) ($data['width'] ?? 0));
        $data['length'] = max(0, (float) ($data['length'] ?? 0));
        $data['is_connote_reff'] = htmlspecialchars(trim($data['is_connote_reff'] ?? '0'));
        $data['connote_reff'] = htmlspecialchars(trim($data['connote_reff'] ?? '-'));

        return ['status' => true, 'data' => $data, 'message' => 'Valid'];
    }

    function force_logout($message)
    {
        $CI =& get_instance();
        $CI->session->unset_userdata(['token', 'user']);
        $CI->session->set_flashdata('swal', [
            'title' => 'Gagal!',
            'text' => $message,
            'icon' => 'error'
        ]);
        redirect('login');
        return;
    }
}

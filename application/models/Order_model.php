<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    protected $table = 'orders';

    public function insert_order($data)
    {
        $this->db->insert('orders', $data);
    }

    public function count_all_orders()
    {
        return $this->db->count_all($this->table);
    }

    public function count_orders_by_user_id($userId)
    {
        return $this->db->where('user_id', $userId)
            ->count_all_results($this->table);
    }

    public function count_today_orders_by_user_id($userId)
    {
        $this->db->where('user_id', $userId);
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        return $this->db->count_all_results($this->table);
    }

    public function get_orders_by_user_id($userId)
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get_where('orders', ['user_id' => $userId]);
        return $query->num_rows() > 0 ? $query->result_array() : null;
    }

    public function get_all_orders()
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('orders');
        return $query->num_rows() > 0 ? $query->result_array() : null;
    }

    public function get_order_by_id_and_user($orderId, $userId)
    {
        return $this->db->get_where('orders', [
            'id' => $orderId,
            'user_id' => $userId
        ])->row();
    }

    public function get_order_by_id($orderId)
    {
        return $this->db->get_where('orders', [
            'id' => $orderId
        ])->row();
    }
}
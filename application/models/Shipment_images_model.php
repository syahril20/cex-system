<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shipment_images_model extends CI_Model
{
    protected $table = 'shipment_images';

    public function get_images_by_order_id($orderId)
    {
        return $this->db->get_where($this->table, ['order_id' => $orderId])->result_array();
    }

    public function insert_image($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function delete_image($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function get_image_by_order_id($orderId)
    {
        return $this->db->get_where($this->table, ['order_id' => $orderId])->row_array();
    }
}
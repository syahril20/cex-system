<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receivers_model extends CI_Model
{
    protected $table = 'receivers';

    public function get_all()
    {
        $this->db->select('r.*, c.id_country, c.country_name, c.code2 as country_code');
        $this->db->from('receivers r');
        $this->db->join('country_data c', 'r.id_country = c.id_country', 'left');

        $this->db->order_by('r.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

}

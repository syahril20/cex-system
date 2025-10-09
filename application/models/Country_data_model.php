<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Country_data_model extends CI_Model
{

    public function get_all()
    {
        $this->db->order_by('country_name', 'ASC');
        return $this->db->get('country_data')->result_array();
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_model extends CI_Model {

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
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
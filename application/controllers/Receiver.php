<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Receivers_model $Receivers_model
 * @property CI_DB_query_builder $db
 */
class Receiver extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receivers_model');
    }

    public function index()
    {
        $data['receivers'] = $this->Receivers_model->get_all();
        $this->load->view('receivers/index', $data);
    }

    public function save()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $address = trim($input['address'] ?? '');
        $city = trim($input['city'] ?? '');
        $postal = trim($input['postal_code'] ?? '');
        $country = $input['id_country'] ?? null;

        // Cek duplikasi
        $exists = $this->db->get_where('receivers', [
            'name' => $name,
            'phone' => $phone,
            'address' => $address
        ])->row();

        if ($exists) {
            echo json_encode(['status' => 'duplicate']);
            return;
        }

        $id = uniqid(); // atau UUID
        $data = [
            'id' => $id,
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postal,
            'id_country' => $country,
            'created_by' => 'admin'
        ];

        $this->db->insert('receivers', $data);

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function delete($id)
    {
        $deleted = $this->db->delete('receivers', ['id' => $id]);
        echo json_encode(['status' => $deleted ? 'success' : 'failed']);
    }

}
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends CI_Controller
{
    public function index()
    {
        $session = $this->check_token();
        $user = $session['user'];
        $userId = $user->id;

        $query = $this->db->get_where('orders', ['user_id' => $userId]);
        $orders = $query->num_rows() > 0 ? $query->result_array() : null;

        if ($orders) {
            foreach ($orders as &$o) {
                $shipment_image = $this->db->get_where('shipment_images', ['order_id' => $o['id']])->row_array();
                $o['shipment_image'] = $shipment_image;
            }
            unset($o);
        }

        $data['session'] = $session;
        $data['page'] = 'Order';
        $data['orders'] = $orders;
        if ($user->code == 'SUPER_ADMIN') {
            $this->load->view('superadmin/superadmin_dashboard');
        }
        if ($user->code == 'ADMIN') {
            $this->load->view('admin/admin_dashboard');
        }
        if ($user->code == 'AGENT') {
            $this->load->view('base_page', ['data' => $data]);
        }
    }

    public function order_form()
    {
        $session = $this->check_token();
        $user = $session['user'];

        $data['session'] = $session;
        $data['page'] = 'OrderForm';
        if ($user->code == 'SUPER_ADMIN') {
            $this->load->view('superadmin/superadmin_dashboard');
        }
        if ($user->code == 'ADMIN') {
            $this->load->view('admin/admin_dashboard');
        }
        if ($user->code == 'AGENT') {
            $this->load->view('base_page', ['data' => $data]);
        }
    }

    public function create()
    {
        $data = $this->input->post();

        $this->form_validation->set_rules('ship_name', 'Shipper Name', 'required');
        $this->form_validation->set_rules('rec_name', 'Receiver Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 400,
                'msg' => validation_errors()
            ]);
            return;
        }

        // Payload untuk API
        $payload = [
            "ship_name" => $data['ship_name'],
            "ship_address" => $data['ship_address'],
            "ship_phone" => $data['ship_phone'],
            "rec_name" => $data['rec_name'],
            "rec_address" => $data['rec_address'],
            "rec_postcode" => $data['rec_postcode'],
            "rec_city" => $data['rec_city'],
            "rec_phone" => $data['rec_phone'],
            "rec_country" => $data['rec_country'],
            "rec_country_code" => $data['rec_country_code'],
            "berat" => $data['berat'],
            "arc_no" => $data['arc_no'],
            "total_qty" => $data['total_qty'],
            "total_value" => $data['total_value'],
            "goods_category" => $data['goods_category'],
            "goods_description" => $data['goods_description'],
            "notes" => $data['notes'],
            "service_type" => $data['service_type'],
            "height" => $data['height'],
            "width" => $data['width'],
            "length" => $data['length'],
            "is_connote_reff" => $data['is_connote_reff'],
            "connote_reff" => $data['connote_reff'],
            "shipment_details" => $data['shipment_details']
        ];

        /**
         * --- Dummy response ---
         * Nanti tinggal buka blok Guzzle kalau mau hit API asli
         */
        /*
        $client = new Client();
        $response = $client->post('https://dev.office.cexsystem.com/v2/service/shipment/create', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer <TOKEN_MU>',
            ],
            'json' => $payload
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        */

        // Dummy data seolah sukses dari API
        $result = [
            "status" => 200,
            "msg" => "Shipment created.",
            "data" => [
                "airwaybill" => strval(mt_rand(100000000000, 999999999999))
            ]
        ];

        // Simpan ke table orders
        $orderData = [
            'id' => $this->generate_uuid(),
            'user_id' => $this->session->userdata('user')->id,
            'data' => json_encode($payload),
            'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'created_by' => $this->session->userdata('user')->username,
            'airwaybill' => $result['data']['airwaybill'],
            'status' => 'Created'
        ];
        $this->db->insert('orders', $orderData);
        redirect('/order');
    }

    public function detail($orderId)
    {
        $session = $this->check_token();

        $order = $this->db->get_where('orders', ['id' => $orderId])->row();
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found.');
            redirect('/order');
            return;
        }

        // $orderItems = $this->db->get_where('order_items', ['order_id' => $orderId])->result();

        $data['session'] = $session;
        $data['order'] = $order;
        $data['page'] = 'OrderDetail';

        $this->load->view('base_page', ['data' => $data]);
    }

    public function upload_form($orderId)
    {
        $session = $this->check_token();

        $order = $this->db->get_where('orders', ['id' => $orderId])->row();
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found.');
            redirect('/order');
        }

        $data = [];
        $data['session'] = $session;
        $data['order'] = $order;
        $data['page'] = 'UploadForm';

        $this->load->view('base_page', ['data' => $data]);
    }

        public function do_upload()
    {
        $airwaybill = $this->input->post('airwaybill');
        $file       = $_FILES['filename']['tmp_name'];
        $file_name  = $_FILES['filename']['name'];

        if (!$airwaybill || !$file) {
            $this->session->set_flashdata('error', 'Airwaybill dan file harus diisi.');
            redirect('uploadshipment');
        }

        try {
            /**
             * ===========================
             * MODE DUMMY (aktif sekarang)
             * ===========================
             */
            $upload_path = FCPATH . 'uploads/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $new_file_name = uniqid() . '_' . $file_name;
            $destination   = $upload_path . $new_file_name;

            if (!move_uploaded_file($file, $destination)) {
                throw new Exception("Gagal menyimpan file ke server lokal.");
            }

            // Dummy response mirip API eksternal
            $dummyResponse = [
                "status" => 200,
                "msg"    => "Upload berhasil (dummy)",
                "data"   => [
                    "airwaybill" => $airwaybill,
                    "file_name"  => $new_file_name,
                    "file_path"  => base_url('uploads/' . $new_file_name),
                    "file_type"  => mime_content_type($destination),
                    "uploaded_by" => "system"
                ]
            ];

            // Simpan ke DB
            $insert = [
                'id'          => $this->generate_uuid(),
                "order_id"    => $this->input->post('order_id'),
                "airwaybill"  => $airwaybill,
                "file_name"   => $new_file_name,
                "file_path"   => '/uploads/' . $new_file_name,
                "file_type"   => mime_content_type($destination),
                "uploaded_by" => "system"
            ];

            $this->db->insert('shipment_images', $insert);

            $this->session->set_flashdata('success', 'Upload berhasil (dummy): ' . json_encode($dummyResponse));


            /**
             * ===========================
             * MODE REAL (gunakan ini jika mau hit API eksternal)
             * ===========================
             */
            /*
            $client = new Client();
            $response = $client->request('POST', 'https://dev.office.cexsystem.com/v2/service/shipment/upload_shipment_image', [
                'headers' => [
                    'Authorization' => 'Bearer <JWT_TOKEN>'
                ],
                'multipart' => [
                    [
                        'name'     => 'airwaybill',
                        'contents' => $airwaybill
                    ],
                    [
                        'name'     => 'filename',
                        'contents' => fopen($file, 'r'),
                        'filename' => $file_name
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Simpan hasil ke DB
            $insert = [
                "order_id"    => null,
                "airwaybill"  => $airwaybill,
                "file_name"   => $result['data']['file_name'],
                "file_path"   => $result['data']['file_path'],
                "file_type"   => $result['data']['file_type'] ?? 'unknown',
                "uploaded_by" => "system"
            ];

            $this->db->insert('shipment_images', $insert);

            $this->session->set_flashdata('success', 'Upload berhasil (real API): ' . json_encode($result));
            */
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Upload gagal: ' . $e->getMessage());
        }

        redirect('order');
    }

    private function check_token()
    {
        $session = $this->session->userdata();
        $token = isset($session['token']) ? $session['token'] : null;

        if ($token == '' || $token == null) {
            redirect('login');
            return;
        }

        $tokendb = $this->db->get_where('user_tokens', ['token' => $token])->row();
        if (!$tokendb || strtotime($tokendb->expired_at) < time()) {
            $this->session->unset_userdata(['token', 'user']);
            $this->session->set_flashdata('error', 'Session expired. Please login again.');
            redirect('login');
            return;
        }

        return $session;
    }

    private function generate_uuid()
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
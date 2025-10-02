<?php
defined('BASEPATH') or exit('No direct script access allowed');
// use GuzzleHttp\Client;
class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(['url', 'form']);
        $this->load->helper(['activity', 'utils']);
        $this->load->model('Master_model');

        // $this->load->database(); // Uncomment if not autoloaded
    }

    public function index()
    {
        $session = check_token();
        $user = $session['user'];
        $userId = $user->id;

        $orders = null;
        if ($user->code == 'AGENT') {
            $this->db->order_by('created_at', 'DESC');
            $query = $this->db->get_where('orders', ['user_id' => $userId]);
            $orders = $query->num_rows() > 0 ? $query->result_array() : null;

            if ($orders) {
                foreach ($orders as &$o) {
                    $shipment_image = $this->db->get_where('shipment_images', ['order_id' => $o['id']])->row_array();
                    $o['shipment_image'] = $shipment_image;
                }
                unset($o);
            }
        }
        if ($user->code == 'ADMIN' || $user->code == 'SUPER_ADMIN') {
            $this->db->order_by('created_at', 'DESC');
            $query = $this->db->get('orders');
            $orders = $query->num_rows() > 0 ? $query->result_array() : null;

            if ($orders) {
                foreach ($orders as &$o) {
                    $shipment_image = $this->db->get_where('shipment_images', ['order_id' => $o['id']])->row_array();
                    $o['shipment_image'] = $shipment_image;
                }
                unset($o);
            }
        }
        
        if ($orders) {
            foreach ($orders as &$order) {
                $latestStatus = null;
                if (!empty($order['airwaybill'])) {
                    $latestStatus = $this->Master_model->get_latest_tracking_status($order['airwaybill']);
                    echo "<script>console.log('Latest XSAS: " . $latestStatus . "');</script>";
                }
                if (empty($latestStatus)) {
                    $latestStatus = 'Created';
                }

                // Update status jika berbeda
                if ($order['status'] !== $latestStatus) {
                    $this->db->where('id', $order['id']);
                    $this->db->update('orders', ['status' => $latestStatus]);
                    // Refresh order array to get updated status
                    $order['status'] = $latestStatus;
                }
            }
            unset($order);
        }
        
        $data['session'] = $session;
        $data['page'] = 'Order';
        $data['orders'] = $orders;

        $this->load->view('base_page', ['data' => $data]);
    }

    public function order_form()
    {
        $session = check_token();
        $user = $session['user'];

        $data['session'] = $session;
        $data['page'] = 'OrderForm';
        $data['rates'] = $this->Master_model->get_rates();
        $data['commodities'] = $this->Master_model->get_commodity();

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
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Shipper Name dan Receiver Name harus diisi.',
                'icon' => 'error'
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
                "code" => 395,
                "airwaybill" => 'AIN' . mt_rand(100000000, 999999999),
                "printUrl" => "https://dev.office.cexsystem.com/frame/cleansing/print_connote_thermal_frame/1N38",
                "printUrlA4" => "https://dev.office.cexsystem.com/frame/cleansing/print_connote_frame/1N38"
            ]
        ];

        // Simpan ke table orders
        $orderData = [
            'id' => generate_uuid(),
            'user_id' => $this->session->userdata('user')->id,
            'data' => json_encode($payload),
            'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'created_by' => $this->session->userdata('user')->username,
            'airwaybill' => $result['data']['airwaybill'],
            'status' => 'Created',
            'response' => json_encode($result)
        ];
        $this->db->insert('orders', $orderData);
        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'Order berhasil dibuat. Airwaybill: ' . $result['data']['airwaybill'],
            'icon' => 'success'
        ]);

        log_activity($this, 'create_order', 'Membuat order baru dengan airwaybill: ' . $result['data']['airwaybill']);

        redirect('/order');
    }

    public function detail($orderId)
    {
        $session = check_token();

        $user = $session['user'];
        if ($user->code == 'AGENT') {
            $order = $this->db->get_where('orders', [
                'id' => $orderId,
                'user_id' => $user->id
            ])->row();
        } else {
            $order = $this->db->get_where('orders', [
                'id' => $orderId
            ])->row();
        }
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
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
        $session = check_token();

        $user = $session['user'];
        $order = $this->db->get_where('orders', [
            'id' => $orderId,
            'user_id' => $user->id
        ])->row();
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
        }

        $data['session'] = $session;
        $data['order'] = $order;
        $data['page'] = 'UploadForm';

        $this->load->view('base_page', ['data' => $data]);
    }

    public function do_upload()
    {
        $airwaybill = $this->input->post('airwaybill');

        if (!isset($_FILES['filename']) || !is_uploaded_file($_FILES['filename']['tmp_name'])) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Gagal memuat file upload.',
                'icon' => 'error'
            ]);
            log_message('error', 'File upload gagal: ' . json_encode($_FILES['filename']));
            redirect('order');
            return;
        }

        $file = $_FILES['filename']['tmp_name'];
        $file_name = $_FILES['filename']['name'];

        if (!$airwaybill || !$file_name) {

            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Airwaybill dan file harus diisi.',
                'icon' => 'error'
            ]);
            redirect('order');
            return;
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
            $destination = $upload_path . $new_file_name;

            if (!move_uploaded_file($file, $destination)) {
                throw new Exception("Gagal menyimpan file ke server lokal.");
            }

            // Dummy response mirip API eksternal
            $dummyResponse = [
                "status" => 200,
                "msg" => "Upload berhasil (dummy)",
                "data" => [
                    "airwaybill" => $airwaybill,
                    "file_name" => $new_file_name,
                    "file_path" => base_url('uploads/' . $new_file_name),
                    "file_type" => mime_content_type($destination),
                ]
            ];

            // Simpan ke DB
            // Cek apakah sudah ada data dengan order_id dan airwaybill yang sama
            $order_id = $this->input->post('order_id');
            $existing = $this->db->get_where('shipment_images', [
                'order_id' => $order_id,
                'airwaybill' => $airwaybill
            ])->result();

            // Hapus data lama dan file-nya jika ada
            foreach ($existing as $row) {
                // Hapus file jika ada
                $old_file = FCPATH . ltrim($row->file_path, '/');
                if (file_exists($old_file)) {
                    @unlink($old_file);
                }
                // Hapus data di DB
                $this->db->delete('shipment_images', ['id' => $row->id]);
            }

            // Insert data baru
            $insert = [
                'id' => generate_uuid(),
                "order_id" => $order_id,
                "airwaybill" => $airwaybill,
                "file_name" => $new_file_name,
                "file_path" => '/uploads/' . $new_file_name,
                "file_type" => mime_content_type($destination),
                'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            ];

            $this->db->insert('shipment_images', $insert);

            $this->session->set_flashdata('swal', [
                'title' => 'Berhasil!',
                'text' => 'Upload berhasil.',
                'icon' => 'success'
            ]);


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
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Error: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }

        log_activity($this, 'upload_shipment_image', 'Upload shipment image untuk airwaybill: ' . $airwaybill);

        redirect('order');
    }

    // private function check_token()
    // {
    //     $session = $this->session->userdata();
    //     $token = isset($session['token']) ? $session['token'] : null;

    //     if ($token == '' || $token == null) {
    //         redirect('login');
    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Gagal!',
    //             'text' => 'Session tidak ditemukan.',
    //             'icon' => 'error'
    //         ]);
    //         return;
    //     }

    //     $tokendb = $this->db->get_where('user_tokens', ['token' => $token])->row();
    //     if (!$tokendb || strtotime($tokendb->expired_at) < time()) {
    //         $this->session->unset_userdata(['token', 'user']);
    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Gagal!',
    //             'text' => 'Session telah kedaluwarsa. Silakan login kembali.',
    //             'icon' => 'error'
    //         ]);
    //         redirect('login');
    //         return;
    //     }

    //     return $session;
    // }

    public function test_guzzle()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get('https://jsonplaceholder.typicode.com/posts/1');
        $body = $res->getBody();

        echo $body;
    }

    public function edit($orderId)
    {
        $session = check_token();
        $user = $session['user'];

        // Hanya ADMIN dan SUPER_ADMIN yang boleh edit
        if (!in_array($user->code, ['ADMIN', 'SUPER_ADMIN'])) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda tidak memiliki akses untuk mengedit order.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        $order = $this->db->get_where('orders', ['id' => $orderId])->row();
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        // Ambil data payload lama untuk form
        $order_data = json_decode($order->data, true);

        $data['session'] = $session;
        $data['order'] = $order;
        $data['order_data'] = $order_data;
        $data['page'] = 'OrderEdit';
        $data['rates'] = $this->Master_model->get_rates();
        $data['commodities'] = $this->Master_model->get_commodity();

        $this->load->view('base_page', ['data' => $data]);
    }

    public function do_edit($orderId)
    {
        $session = check_token();
        $user = $session['user'];

        // Hanya ADMIN dan SUPER_ADMIN yang boleh edit
        if (!in_array($user->code, ['ADMIN', 'SUPER_ADMIN'])) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda tidak memiliki akses untuk mengedit order.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        $order = $this->db->get_where('orders', ['id' => $orderId])->row();
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        if ($this->input->method() === 'post') {
            $data = $this->input->post('data'); // Ambil data JSON
            $shipmentDetails = $data['shipment_details'] ?? [];

            // Validasi wajib
            if (empty($data['ship_name']) || empty($data['rec_name'])) {
                $this->session->set_flashdata('swal', [
                    'title' => 'Gagal!',
                    'text' => 'Shipper Name dan Receiver Name harus diisi.',
                    'icon' => 'error'
                ]);
                redirect('/order/edit/' . $orderId);
                return;
            }

            // Validasi shipment_details
            foreach ($shipmentDetails as $i => $item) {
                if (
                    !isset($item['name'], $item['category'], $item['qty'], $item['price']) ||
                    $item['qty'] <= 0 || $item['price'] < 0
                ) {
                    $this->session->set_flashdata('swal', [
                        'title' => 'Gagal!',
                        'text' => 'Shipment Details baris ke-' . ($i + 1) . ' tidak valid.',
                        'icon' => 'error'
                    ]);
                    redirect('/order/edit/' . $orderId);
                    return;
                }
            }

            // Update data order
            $payload = array_merge($data, ['shipment_details' => $shipmentDetails]);

            $updateData = [
                'data' => json_encode($payload),
                'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
                'updated_by' => $user->username
            ];

            $this->db->where('id', $orderId);
            $this->db->update('orders', $updateData);

            $this->session->set_flashdata('swal', [
                'title' => 'Berhasil!',
                'text' => 'Order berhasil diupdate.',
                'icon' => 'success'
            ]);

            log_activity($this, 'edit_order', 'Edit order dengan airwaybill: ' . $order->airwaybill);

            redirect('/order/detail/' . $orderId);
            return;
        }

        // Ambil data payload lama untuk form
        $order_data = json_decode($order->data, true);

        $data['session'] = $session;
        $data['order'] = $order;
        $data['order_data'] = $order_data;
        $data['page'] = 'OrderEdit';
        $data['rates'] = $this->Master_model->get_rates();
        $data['commodities'] = $this->Master_model->get_commodity();

        $this->load->view('base_page', ['data' => $data]);
    }

    public function tracking()
    {
        $airwaybill = $this->input->post('airwaybill'); // ambil dari form input
        $trackingData = $this->Master_model->get_tracking($airwaybill);

        if (!empty($trackingData)) {
            $data['tracking'] = $trackingData;
            $this->load->view('order/tracking_result', $data);
        } else {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Data tracking tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('order');
        }
    }

}
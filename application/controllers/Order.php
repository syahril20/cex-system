<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_DB_query_builder $db
 * @property Master_model $Master_model
 * @property Order_model $Order_model
 * @property Shipment_images_model $Shipment_images_model
 * @property Country_data_model $Country_data_model
 */
class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(['url', 'form']);
        $this->load->helper(['activity', 'utils']);
        $this->load->model(['Master_model', 'Order_model', 'Shipment_images_model', 'Country_data_model']);
    }

    public function index()
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;
        $userId = $user->id;

        $orders = null;
        if ($user->code == 'AGENT') {
            $orders = $this->Order_model->get_orders_by_user_id($userId) ?? [];

            if ($orders) {
                foreach ($orders as &$o) {
                    $o['shipment_image'] = $this->Shipment_images_model->get_image_by_order_id($o['id']);
                }
                unset($o);
            }
        } elseif ($user->code == 'ADMIN' || $user->code == 'SUPER_ADMIN') {
            $orders = $this->Order_model->get_all_orders() ?? [];

            if ($orders) {
                foreach ($orders as &$o) {
                    $o['shipment_image'] = $this->Shipment_images_model->get_image_by_order_id($o['id']) ?? null;
                }
                unset($o);
            }
        } else {
            redirect('/');
        }

        $status = null;
        $latestStatus = null;

        if ($orders) {
            foreach ($orders as &$order) {
                $latestStatus = null;
                // Hanya proses jika status saat ini adalah 'Approved'
                if (
                    !in_array($order['status'], ['Rejected', 'Created']) &&
                    !empty($order['airwaybill'])
                ) {
                    $status = $this->Master_model->get_all_trackings($order['airwaybill'] ?? []);
                    // Dummy tracking untuk airwaybill AIN341213536
                    if ($order['airwaybill'] === 'AIN341213536') {
                        $status = [
                            [
                                'date' => '2024-06-01',
                                'time' => '09:00',
                                'status' => 'Picked Up'
                            ],
                            [
                                'date' => '2024-06-02',
                                'time' => '14:30',
                                'status' => 'In Transit'
                            ],
                            [
                                'date' => '2024-06-03',
                                'time' => '18:45',
                                'status' => 'Delivered'
                            ]
                        ];
                    }

                    if (!empty($status) && is_array($status)) {
                        $latestStatus = end($status)['status'] ?? 'Pending';
                        $latestTracking = end($status);
                        $order['status_history'] = [];
                        foreach ($status as $tracking) {
                            $order['status_history'][] = [
                                'date' => ($tracking['date'] ?? '') . ' ' . ($tracking['time'] ?? ''),
                                'status' => $tracking['status'] ?? ''
                            ];
                        }
                    }
                    // $latestStatus = $this->Master_model->get_latest_tracking_status($order['airwaybill']);
                    // if (empty($latestStatus)) {
                    //     $latestStatus = 'Pending';
                    // }
                    // if (!empty($status)) {
                    //     $order['tracking'] = $status;                        
                    // }
                }

                // Update status jika status saat ini Approved dan latestStatus tidak kosong/null
                if (
                    !in_array($order['status'], ['Rejected', 'Created']) &&
                    !empty($latestStatus)
                ) {
                    $this->db->where('id', $order['id']);
                    $this->db->update('orders', ['status' => $latestStatus]);
                    // Refresh order array to get updated status
                    $order['status'] = $latestStatus;
                }
            }
            unset($order);
        }

        $data['token'] = $token;
        $data['user'] = $user;
        $data['page'] = 'Order';
        $data['orders'] = $orders;

        echo "<script>console.log(" . json_encode($data) . ");</script>";

        $this->load->view('base_page', $data);
    }

    public function order_form()
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

        $data['token'] = $token;
        $data['user'] = $user;
        $data['page'] = 'OrderForm';
        $data['rates'] = $this->Master_model->get_rates();
        $data['commodities'] = $this->Master_model->get_commodity();
        $data['country_data'] = $this->Country_data_model->get_all();

        echo "<script>console.log(" . json_encode($data) . ");</script>";

        if ($user->code !== 'SUPER_ADMIN') {
            $this->load->view('base_page', $data);
        }
    }

    public function create()
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

        if ($user->code == 'SUPER_ADMIN') {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Hanya AGENT dan ADMIN yang dapat membuat order',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }
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

        try {
            $apiResponse = $this->Master_model->create_shipment($payload);
        } catch (Exception $e) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Gagal membuat shipment: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
            log_message('error', 'create_shipment exception: ' . $e->getMessage());
            redirect('/order');
            return;
        }

        if (empty($apiResponse) || !is_array($apiResponse)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Response tidak valid dari Master.',
                'icon' => 'error'
            ]);
            log_message('error', 'create_shipment invalid response: ' . json_encode($apiResponse));
            redirect('/order');
            return;
        }

        $statusCode = $apiResponse['status'] ?? null;
        $msg = $apiResponse['msg'] ?? 'Terjadi kesalahan saat membuat shipment.';

        if ($statusCode !== 200) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => $msg,
                'icon' => 'error'
            ]);
            log_message('error', 'create_shipment failed: ' . json_encode($apiResponse));
            redirect('/order');
            return;
        }

        $result = $apiResponse;
        // $result = [
        //     "status" => 200,
        //     "msg" => "Shipment created.",
        //     "data" => [
        //         "code" => 395,
        //         "airwaybill" => 'AIN' . mt_rand(100000000, 999999999),
        //         "printUrl" => "https://dev.office.cexsystem.com/frame/cleansing/print_connote_thermal_frame/1N38",
        //         "printUrlA4" => "https://dev.office.cexsystem.com/frame/cleansing/print_connote_frame/1N38"
        //     ]
        // ];

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
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;
        switch ($user->code) {
            case 'AGENT':
                $order = $this->Order_model->get_order_by_id_and_user($orderId, $user->id);
                break;
            default:
                $order = $this->Order_model->get_order_by_id($orderId);
                break;
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

        $data['token'] = $token;
        $data['user'] = $user;
        $data['page'] = 'OrderDetail';
        $data['order'] = $order;
        $data['rates'] = $this->Master_model->get_rates();

        echo '<script>console.log(' . json_encode($data) . ');</script>';

        $this->load->view('base_page', $data);
    }

    public function upload_form($orderId)
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;
        $order = $this->Order_model->get_order_by_id_and_user($orderId, $user->id);
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
        }

        $data['token'] = $token;
        $data['user'] = $user;
        $data['order'] = $order;
        $data['page'] = 'UploadForm';

        $this->load->view('base_page', $data);
    }

    // public function do_upload()
    // {
    //     $airwaybill = $this->input->post('airwaybill');

    //     if (!isset($_FILES['filename']) || !is_uploaded_file($_FILES['filename']['tmp_name'])) {
    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Gagal!',
    //             'text' => 'Gagal memuat file upload.',
    //             'icon' => 'error'
    //         ]);
    //         log_message('error', 'File upload gagal: ' . json_encode($_FILES['filename']));
    //         redirect('order');
    //         return;
    //     }

    //     $file = $_FILES['filename']['tmp_name'];
    //     $file_name = $_FILES['filename']['name'];

    //     if (!$airwaybill || !$file_name) {

    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Gagal!',
    //             'text' => 'Airwaybill dan file harus diisi.',
    //             'icon' => 'error'
    //         ]);
    //         redirect('order');
    //         return;
    //     }
    //     try {
    //         $upload_path = FCPATH . 'uploads/';
    //         if (!is_dir($upload_path)) {
    //             mkdir($upload_path, 0777, true);
    //         }

    //         $new_file_name = uniqid() . '_' . $file_name;
    //         $destination = $upload_path . $new_file_name;

    //         if (!move_uploaded_file($file, $destination)) {
    //             throw new Exception("Gagal menyimpan file ke server lokal.");
    //         }

    //         // Upload file ke Master via Master_model
    //         try {
    //             $apiResponse = $this->Master_model->upload_shipment_image($airwaybill, $destination, true);
    //             log_message('info', 'Master_model::upload_shipment_image response: ' . json_encode($apiResponse));
    //         } catch (Exception $e) {
    //             log_message('error', 'Master_model::upload_shipment_image exception: ' . $e->getMessage());
    //             // Hapus file lokal jika sudah dibuat
    //             if (file_exists($destination)) {
    //                 @unlink($destination);
    //             }
    //             $this->session->set_flashdata('swal', [
    //                 'title' => 'Gagal!',
    //                 'text' => 'Gagal mengunggah file ke server Master: ' . $e->getMessage(),
    //                 'icon' => 'error'
    //             ]);
    //             redirect('order');
    //             return;
    //         }

    //         // Validasi response dari Master, jika error maka batalkan dan return swal error
    //         $statusCode = $apiResponse['status'] ?? null;
    //         if ($apiResponse === null || $statusCode !== 200) {
    //             $errMsg = $apiResponse['msg'] ?? 'Terjadi kesalahan saat mengunggah ke server Master.';
    //             log_message('error', 'Master upload failed: ' . json_encode($apiResponse));
    //             if (file_exists($destination)) {
    //                 @unlink($destination);
    //             }
    //             $this->session->set_flashdata('swal', [
    //                 'title' => 'Gagal!',
    //                 'text' => 'Gagal mengunggah ke Master: ' . $errMsg,
    //                 'icon' => 'error'
    //             ]);
    //             redirect('order');
    //             return;
    //         }

    //         // Jika Master mengembalikan path/URL, gunakan sebagai referensi (fallback ke lokal bila kosong)
    //         $remote_file_path = $apiResponse['data']['file_path'] ?? null;
    //         if ($remote_file_path) {
    //             // Simpan juga referensi remote jika tersedia
    //             $remote_file_path = (strpos($remote_file_path, '/') === 0) ? $remote_file_path : '/' . ltrim($remote_file_path, '/');
    //         } else {
    //             $remote_file_path = "/uploads/$new_file_name";
    //         }

    //         // Simpan ke DB
    //         // Cek apakah sudah ada data dengan order_id dan airwaybill yang sama
    //         $order_id = $this->input->post('order_id');
    //         $existing = $this->Order_model->get_shipment_images_by_order_and_airwaybill($order_id, $airwaybill);

    //         // Hapus data lama dan file-nya jika ada
    //         foreach ($existing as $row) {
    //             // Hapus file jika ada
    //             $old_file = FCPATH . ltrim($row->file_path, '/');
    //             if (file_exists($old_file)) {
    //                 @unlink($old_file);
    //             }
    //             // Hapus data di DB
    //             $this->db->delete('shipment_images', ['id' => $row->id]);
    //         }

    //         // Insert data baru
    //         $insert = [
    //             'id' => generate_uuid(),
    //             "order_id" => $order_id,
    //             "airwaybill" => $airwaybill,
    //             "file_name" => $new_file_name,
    //             "file_path" => '/uploads/' . $new_file_name,
    //             "file_type" => mime_content_type($destination),
    //             'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
    //         ];

    //         $this->db->insert('shipment_images', $insert);

    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Berhasil!',
    //             'text' => 'Upload berhasil.',
    //             'icon' => 'success'
    //         ]);

    //     } catch (Exception $e) {
    //         $this->session->set_flashdata('swal', [
    //             'title' => 'Gagal!',
    //             'text' => 'Error: ' . $e->getMessage(),
    //             'icon' => 'error'
    //         ]);
    //     }

    //     log_activity($this, 'upload_shipment_image', 'Upload shipment image untuk airwaybill: ' . $airwaybill);

    //     redirect('order');
    // }


    public function do_upload()
    {
        $airwaybill = $this->input->post('airwaybill');

        // --- Validasi file upload ---
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
            // --- Simpan file sementara ke lokal ---
            $upload_path = FCPATH . 'uploads/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $new_file_name = uniqid() . '_' . $file_name;
            $destination = "$upload_path$new_file_name";

            if (!move_uploaded_file($file, $destination)) {
                throw new Exception("Gagal menyimpan file ke server lokal.");
            }

            // --- Upload ke Master API via Master_model ---
            try {
                $apiResponse = $this->Master_model->upload_shipment_image($airwaybill, $destination, true);
                log_message('info', 'Master_model::upload_shipment_image response: ' . json_encode($apiResponse));
            } catch (Exception $e) {
                log_message('error', 'Master_model::upload_shipment_image exception: ' . $e->getMessage());

                // Hapus file lokal jika gagal upload ke Master
                if (file_exists($destination)) {
                    @unlink($destination);
                }

                $this->session->set_flashdata('swal', [
                    'title' => 'Gagal!',
                    'text' => 'Gagal mengunggah file ke server Master: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
                redirect('order');
                return;
            }

            // --- Validasi hasil dari Master ---
            $statusCode = $apiResponse['status'] ?? null;
            if (empty($apiResponse) || $statusCode !== 200) {
                $errMsg = $apiResponse['msg'] ?? 'Terjadi kesalahan saat mengunggah ke server Master.';
                log_message('error', 'Master upload failed: ' . json_encode($apiResponse));

                if (file_exists($destination)) {
                    @unlink($destination);
                }

                $this->session->set_flashdata('swal', [
                    'title' => 'Gagal!',
                    'text' => "Gagal mengunggah ke Master: $errMsg",
                    'icon' => 'error'
                ]);
                redirect('order');
                return;
            }

            // --- Dapatkan path file dari response Master ---
            $remote_file_path = $apiResponse['data']['file_path'] ?? null;
            if ($remote_file_path) {
                $remote_file_path = (strpos($remote_file_path, '/') === 0)
                    ? $remote_file_path
                    : '/' . ltrim($remote_file_path, '/');
            } else {
                $remote_file_path = "/uploads/$new_file_name";
            }

            // --- Simpan ke DB ---
            $order_id = $this->input->post('order_id');
            $existing = $this->Order_model->get_shipment_images_by_order_and_airwaybill($order_id, $airwaybill);

            // Hapus data lama dan file-nya jika ada
            foreach ($existing as $row) {
                $old_file = FCPATH . ltrim($row->file_path, '/');
                if (file_exists($old_file)) {
                    @unlink($old_file);
                }
                $this->db->delete('shipment_images', ['id' => $row->id]);
            }

            $insert = [
                'id' => generate_uuid(),
                'order_id' => $order_id,
                'airwaybill' => $airwaybill,
                'file_name' => $new_file_name,
                'file_path' => $remote_file_path,
                'file_type' => mime_content_type($destination),
                'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            ];

            $this->db->insert('shipment_images', $insert);

            // --- Berhasil ---
            $this->session->set_flashdata('swal', [
                'title' => 'Berhasil!',
                'text' => 'Upload berhasil ke server Master.',
                'icon' => 'success'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Upload error: ' . $e->getMessage());
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Error: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }

        // --- Logging aktivitas ---
        log_activity($this, 'upload_shipment_image', "Upload shipment image untuk airwaybill: $airwaybill");
        redirect('order');
    }

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
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

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

        $order = $this->Order_model->get_order_by_id($orderId);
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

        $data['token'] = $token;
        $data['user'] = $user;
        $data['order'] = $order;
        $data['order_data'] = $order_data;
        $data['page'] = 'OrderEdit';
        $data['rates'] = $this->Master_model->get_rates();
        $data['commodities'] = $this->Master_model->get_commodity();
        $data['country_data'] = $this->Country_data_model->get_all();

        echo '<script>console.log(' . json_encode($data) . ');</script>';

        $this->load->view('base_page', $data);
    }

    public function do_edit($orderId)
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

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

        $order = $this->Order_model->get_order_by_id($orderId);
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
            $data = $this->input->post('data'); // Ambil data JSON dari form
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

            // Siapkan payload untuk disimpan / dikirim ke Master
            $payload = array_merge($data, ['shipment_details' => $shipmentDetails]);

            // Ambil code dari response order (jika ada)
            $respObj = null;
            $code = null;
            if (!empty($order->response)) {
                $respObj = json_decode($order->response, true);
                $code = $respObj['code'] ?? $respObj['data']['code'] ?? null;
            }

            // Jika code tersedia, panggil create_shipment_with_code sehingga melakukan hit ke Master
            if (!empty($code)) {
                $callData = [
                    'order_id' => $orderId,
                    'data' => $payload
                ];
                $success = $this->create_shipment_with_code($code, $callData, true);

                if ($success) {
                    // create_shipment_with_code sudah melakukan update DB & flash
                    redirect('/order/detail/' . $orderId);
                    return;
                } else {
                    // Jika gagal melakukan request ke Master, tampilkan error dan jangan lanjut
                    redirect('/order/edit/' . $orderId);
                    return;
                }
            }

            // Jika tidak ada code pada response, lakukan update lokal seperti biasa
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

        redirect('/order');
    }

    /**
     * Hit Master create shipment endpoint menggunakan kode yang berasal dari response order.
     * $code diambil dari object response di table orders.response
     * $data harus mengandung order_id dan data (payload yang akan disimpan/dikirim)
     *
     * Mengembalikan true jika berhasil (dan melakukan update pada tabel orders),
     * false jika gagal.
     */
    public function create_shipment_with_code($code, $data, $retry = true)
    {
        $orderId = $data['order_id'] ?? null;
        $payload = $data['data'] ?? [];

        if (empty($orderId) || empty($payload)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order ID atau payload tidak tersedia untuk proses create shipment.',
                'icon' => 'error'
            ]);
            log_message('error', 'create_shipment_with_code invalid params: ' . json_encode($data));
            return false;
        }

        try {
            // Jika Master_model punya method create_shipment_with_code, panggil seperti biasa
            if (method_exists($this->Master_model, 'create_shipment_with_code')) {
                $apiResponse = $this->Master_model->create_shipment_with_code($code, $payload, $retry);
            } else {
                // Jika tidak ada, update data lokal saja
                $update = [
                    'data' => json_encode($payload),
                    'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
                    'updated_by' => $this->session->userdata('user')->username ?? 'system'
                ];
                $this->db->where('id', $orderId);
                $this->db->update('orders', $update);

                $this->session->set_flashdata('swal', [
                    'title' => 'Berhasil!',
                    'text' => 'Perubahan disimpan secara lokal (tidak membuat shipment di Master karena fungsi tidak tersedia).',
                    'icon' => 'success'
                ]);

                log_activity($this, 'create_shipment_with_code', 'Skipped remote create (Master API not available) for order_id ' . $orderId);
                log_message('warning', 'Master_model::create_shipment_with_code not available, skipped remote create for order_id ' . $orderId);

                return true;
            }
        } catch (Exception $e) {
            log_message('error', 'create_shipment_with_code exception: ' . $e->getMessage());
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Gagal membuat shipment ke Master: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
            return false;
        }

        if (empty($apiResponse) || !is_array($apiResponse)) {
            log_message('error', 'create_shipment_with_code invalid response: ' . json_encode($apiResponse));
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Response tidak valid dari Master.',
                'icon' => 'error'
            ]);
            return false;
        }

        $statusCode = $apiResponse['status'] ?? null;
        $msg = $apiResponse['msg'] ?? 'Terjadi kesalahan saat membuat shipment.';

        if ($statusCode !== 200) {
            log_message('error', 'create_shipment_with_code failed: ' . json_encode($apiResponse));
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => $msg,
                'icon' => 'error'
            ]);
            return false;
        }

        // ✅ Update hanya data + updated metadata
        $update = [
            'data' => json_encode($payload),
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $this->session->userdata('user')->username ?? 'system'
        ];

        $this->db->where('id', $orderId);
        $this->db->update('orders', $update);

        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'Order berhasil diupdate dan shipment dibuat di Master.',
            'icon' => 'success'
        ]);

        log_activity($this, 'create_shipment_with_code', 'Create shipment with code ' . $code . ' for order_id ' . $orderId);

        return true;
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

    public function approve($id)
    {
        $session = check_token();
        $user = $session['user'];

        // Hanya SUPER_ADMIN/ADMIN yang boleh approve
        if (!in_array($user->code, ['SUPER_ADMIN', 'ADMIN'])) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda tidak memiliki akses untuk meng-approve order.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        $order = $this->Order_model->get_order_by_id($id);
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        // Update status order menjadi "Approved"
        $this->db->where('id', $id);
        $this->db->update('orders', [
            'status' => 'Approved',
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $user->username
        ]);

        // Catat aktivitas
        log_activity($this, 'approve_order', 'Approve order dengan airwaybill: ' . $order->airwaybill);

        // Coba panggil set_outbound jika airwaybill tersedia
        if (!empty($order->airwaybill)) {
            try {
                $res = $this->Master_model->set_outbound($order->airwaybill, true);
                if ($res === false) {
                    // set_outbound mengembalikan false -> beri peringatan pada user dan log
                    $this->session->set_flashdata('swal', [
                        'title' => 'Peringatan!',
                        'text' => 'Order di-approve tetapi proses set_outbound gagal. Silakan cek log untuk detail.',
                        'icon' => 'warning'
                    ]);
                    log_message('error', 'set_outbound failed for airwaybill: ' . $order->airwaybill);
                    redirect('/order');
                    return;
                }
                // jika set_outbound sukses (true/anything selain false), tampilkan sukses biasa
                $this->session->set_flashdata('swal', [
                    'title' => 'Berhasil!',
                    'text' => 'Order berhasil di-approve dan dikirim ke outbound.',
                    'icon' => 'success'
                ]);
            } catch (Exception $e) {
                // Tangani exception dari set_outbound
                log_message('error', 'set_outbound exception for ' . $order->airwaybill . ': ' . $e->getMessage());
                $this->session->set_flashdata('swal', [
                    'title' => 'Peringatan!',
                    'text' => 'Order di-approve tetapi terjadi error saat set_outbound: ' . $e->getMessage(),
                    'icon' => 'warning'
                ]);
            }
        } else {
            // Tidak ada airwaybill — tetap sukses approve
            $this->session->set_flashdata('swal', [
                'title' => 'Berhasil!',
                'text' => 'Order berhasil di-approve.',
                'icon' => 'success'
            ]);
        }

        redirect('/order');
    }

    public function reject($id)
    {
        $session = check_token();
        $user = $session['user'];

        // Hanya SUPER_ADMIN yang boleh reject
        if (!in_array($user->code, ['SUPER_ADMIN', 'ADMIN'])) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda tidak memiliki akses untuk menolak order.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        $order = $this->Order_model->get_order_by_id($id);
        if (!$order) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Order tidak ditemukan.',
                'icon' => 'error'
            ]);
            redirect('/order');
            return;
        }

        // Update status order menjadi "Rejected"
        $this->db->where('id', $id);
        $this->db->update('orders', [
            'status' => 'Rejected',
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $user->username
        ]);

        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'Order berhasil ditolak.',
            'icon' => 'success'
        ]);

        log_activity($this, 'reject_order', 'Reject order dengan airwaybill: ' . $order->airwaybill);

        redirect('/order');
    }

    public function export_excel()
    {
        // Nonaktifkan error agar tidak muncul di file Excel
        error_reporting(0);
        ini_set('display_errors', 0);

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!$start_date || !$end_date) {
            show_error('Tanggal awal dan akhir harus diisi.');
        }

        // Ambil data order
        $orders = $this->Order_model->get_by_date_range($start_date, $end_date);

        // Ambil data service dari database
        $rateData = $this->Master_model->get_rates();
        log_message('debug', 'Rates data: ' . json_encode($rateData));

        $serviceTypeMap = [];
        // Support both formats: ['data' => [...]] or plain array like [{"id":1,"text":"REGULER","rate_type":1},...]
        $rates = [];
        if (!empty($rateData)) {
            $rates = $rateData['data'] ?? $rateData;
        }

        if (!empty($rates) && is_array($rates)) {
            foreach ($rates as $r) {
                // support array items or objects
                $rateType = is_array($r) ? ($r['rate_type'] ?? null) : ($r->rate_type ?? null);
                $text = is_array($r) ? ($r['text'] ?? '') : ($r->text ?? '');
                if ($rateType !== null) {
                    $serviceTypeMap[$rateType] = strtoupper($text);
                }
            }
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'A1' => 'No',
            'B1' => 'Airwaybill',
            'C1' => 'User ID',
            'D1' => 'Pengirim',
            'E1' => 'Alamat Pengirim',
            'F1' => 'No HP Pengirim',
            'G1' => 'Penerima',
            'H1' => 'Alamat Penerima',
            'I1' => 'Kota',
            'J1' => 'Negara',
            'K1' => 'Berat (Kg)',
            'L1' => 'Ukuran (P x L x T)',
            'M1' => 'Service',
            'N1' => 'Deskripsi Barang',
            'O1' => 'Notes',
            'P1' => 'Barang',
            'Q1' => 'Kategori',
            'R1' => 'Qty',
            'S1' => 'Harga',
            'T1' => 'Status',
            'U1' => 'Tanggal Dibuat',
            'V1' => 'Dibuat Oleh'
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $row = 2;
        $no = 1;

        foreach ($orders as $order) {
            $data = json_decode($order->data, true);
            $details = $data['shipment_details'] ?? [];

            $totalDetail = count($details);
            $startRow = $row;
            $endRow = $row + ($totalDetail > 0 ? $totalDetail - 1 : 0);

            // Format ukuran
            $ukuran = ($data['length'] ?? '-') . ' x ' . ($data['width'] ?? '-') . ' x ' . ($data['height'] ?? '-');

            // Mapping service type
            $service = $serviceTypeMap[$data['service_type'] ?? ''] ?? '-';

            // Isi kolom utama (sekali per airwaybill)
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $order->airwaybill);
            $sheet->setCellValue('C' . $row, $order->user_id);
            $sheet->setCellValue('D' . $row, $data['ship_name'] ?? '-');
            $sheet->setCellValue('E' . $row, $data['ship_address'] ?? '-');
            $sheet->setCellValue('F' . $row, $data['ship_phone'] ?? '-');
            $sheet->setCellValue('G' . $row, $data['rec_name'] ?? '-');
            $sheet->setCellValue('H' . $row, $data['rec_address'] ?? '-');
            $sheet->setCellValue('I' . $row, $data['rec_city'] ?? '-');
            $sheet->setCellValue('J' . $row, ($data['rec_country'] ?? '-') . ' [' . ($data['rec_country_code'] ?? '-') . ']');
            $sheet->setCellValue('K' . $row, $data['berat'] ?? '-');
            $sheet->setCellValue('L' . $row, $ukuran);
            $sheet->setCellValue('M' . $row, $service);
            $sheet->setCellValue('N' . $row, $data['goods_description'] ?? '-');
            $sheet->setCellValue('O' . $row, $data['notes'] ?? '-');
            $sheet->setCellValue('T' . $row, $order->status);
            $sheet->setCellValue('U' . $row, $order->created_at);
            $sheet->setCellValue('V' . $row, $order->created_by);

            // Merge cell utama kalau ada banyak barang
            if ($totalDetail > 1) {
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'T', 'U', 'V'] as $col) {
                    $sheet->mergeCells("{$col}{$startRow}:{$col}{$endRow}");
                    $sheet->getStyle("{$col}{$startRow}:{$col}{$endRow}")
                        ->getAlignment()->setVertical('top');
                }
            }

            // Barang detail
            if (!empty($details)) {
                foreach ($details as $detail) {
                    $sheet->setCellValue('P' . $row, $detail['name'] ?? '-');
                    $sheet->setCellValue('Q' . $row, $detail['category'] ?? '-');
                    $sheet->setCellValue('R' . $row, $detail['qty'] ?? '0');
                    $sheet->setCellValue('S' . $row, $detail['price'] ?? '0');
                    $row++;
                }
            } else {
                $sheet->setCellValue('P' . $row, '-');
                $sheet->setCellValue('Q' . $row, '-');
                $sheet->setCellValue('R' . $row, '-');
                $sheet->setCellValue('S' . $row, '-');
                $row++;
            }
        }

        // Styling header
        $sheet->getStyle('A1:V1')->getFont()->setBold(true);
        $sheet->getStyle('A1:V1')->getAlignment()->setHorizontal('center');
        foreach (range('A', 'V') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Bersihkan output buffer agar file tidak corrupt
        if (ob_get_length())
            ob_end_clean();

        // Output Excel ke browser
        $filename = 'Order_Grouped_Detail_' . $start_date . '_to_' . $end_date . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

}
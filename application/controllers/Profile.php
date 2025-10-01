<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(['url', 'form', 'activity', 'utils']);
    }
    
    public function index()
    {
        $session = check_token();
        $user = $session['user'];

        // Ambil aktivitas terbaru user
        $this->db->where('user_id', $user->id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        $recent_activities = $this->db->get('activity')->result_array();

        // Jika role AGENT bisa ambil saldo
        // $saldo = null;
        // if ($user->code === 'AGENT') {
            // $saldo = $this->db->select('saldo')->where('id', $user->id)->get('users')->row()->saldo ?? 0;
        // }
        
        // $data['saldo'] = $saldo;
        $data['session'] = $session;
        $data['recent_activities'] = $recent_activities;
        $data['page'] = 'Profile';

        $this->load->view('base_page', ['data' => $data]);
    }

    public function update_password()
    {
        $session = check_token();
        $user = $session['user'];

        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        if (!password_verify($old_password, $user->password)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Password lama salah.',
                'icon' => 'error'
            ]);
            redirect('profile');
        }

        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Konfirmasi password baru tidak cocok.',
                'icon' => 'error'
            ]);
            redirect('profile');
        }

        $this->db->where('id', $user->id)->update('users', [
            'password' => password_hash($new_password, PASSWORD_BCRYPT),
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $user->username
        ]);

        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'Password berhasil diperbarui.',
            'icon' => 'success'
        ]);
        redirect('profile');
    }
}

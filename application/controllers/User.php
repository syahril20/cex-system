<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property Role_model $Role_model
 * @property CI_Session $session
 * @property CI_Cache $cache
 * @property CI_Input $input
 */
class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['User_model', 'Role_model']);
        $this->load->helper(['url', 'form', 'activity', 'utils']);
    }

    public function index()
    {
        $session = $this->session->userdata();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;
        if (!$user || !$token) {
            return force_logout('Data pengguna tidak ditemukan.');
        }

        $data['token'] = $token;
        $data['user'] = $user;
        $data['page'] = 'UserManagement';

        // Ambil user yang bisa dilihat sesuai role
        if ($user->code == 'SUPER_ADMIN') {
            // SUPER_ADMIN tidak bisa melihat user dengan role yang sama (SUPER_ADMIN lain)
            $data['users'] = $this->User_model->get_all_except_role($user->id, 'SUPER_ADMIN') ?? [];
        } elseif ($user->code == 'ADMIN') {
            // ADMIN hanya bisa melihat user dengan role AGENT
            $data['users'] = $this->User_model->get_all_by_role('AGENT') ?? [];
        } else {
            $data['users'] = [];
        }

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', $data);
        } else {
            redirect('/');
        }
    }

    public function create()
    {
        $session = check_token();
        $roles = $this->Role_model->get_all_roles();

        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

        $data['token'] = $token;
        $data['user'] = $user;
        $data['roles'] = $roles;
        $data['page'] = 'UserCreate';

        echo "<script>console.log(" . json_encode($roles) . ")</script>";

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', $data);
        } else {
            redirect('/');
        }

    }

    public function do_create()
    {
        $session = check_token();

        $role_id = $this->input->post('role_id');

        if (empty($role_id)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Role harus dipilih!',
                'icon' => 'error'
            ]);
            redirect('user/create');
            return;
        }

        $email = $this->input->post('email');
        $username = $this->input->post('username');

        // validasi duplikat
        if ($this->User_model->get_by_email($email)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Email sudah digunakan!',
                'icon' => 'error'
            ]);
            redirect('user/create');
            return;
        }

        $data = [
            'id' => generate_uuid(),
            'username' => $username,
            'email' => $email,
            'role_id' => $role_id,
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'created_by' => $session['user']->username ?? 'system'
        ];

        $this->User_model->insert($data);

        log_activity($this, 'create_user', 'Membuat user baru dengan email: ' . $email);

        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'User baru berhasil ditambahkan.',
            'icon' => 'success'
        ]);
        redirect('user');
    }


    public function edit($id)
    {
        $session = check_token();

        $users = $this->User_model->get_by_id($id);
        $roles = $this->Role_model->get_all_roles();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

        if (empty($users)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'User tidak ditemukan',
                'icon' => 'error'
            ]);
            redirect('user');
            return;
        }
        $data['token'] = $token;
        $data['user'] = $user;
        $data['roles'] = $roles;
        $data['page'] = 'UserEdit';
        $data['users'] = $users;

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', $data);
        } else {
            redirect('/');
        }
    }

    public function do_edit($id)
    {
        $session = check_token();
        $user = $this->User_model->get_by_id($id);

        if (empty($user) || $session['user']->id == $user->id) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'User tidak ditemukan atau tidak dapat mengedit user sendiri',
                'icon' => 'error'
            ]);
            redirect('user');
            return;
        }

        $role_id = $this->input->post('role_id');

        if (empty($role_id)) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'Role harus dipilih!',
                'icon' => 'error'
            ]);
            redirect('user/edit/' . $id);
            return;
        }

        // Ambil data dari form (misal: username, email, role_id, dll)
        $data = [
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'role_id' => $role_id,
            'password' => $this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_BCRYPT) : $user->password,
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $session['user']->username
        ];

        $this->User_model->update($id, $data);
        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil diupdate.',
            'icon' => 'success'
        ]);
        log_activity($this, 'edit_user', 'Mengedit user dengan Username: ' . $data['username']);
        redirect('user');
    }

    public function delete($id)
    {
        $session = check_token(); // ambil user session

        $this->User_model->soft_delete_user($id, $session['user']->username);

        // Optional: flash message
        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil di-disable.',
            'icon' => 'success'
        ]);

        $user = $this->User_model->get_by_id($id);
        log_activity($this, 'delete_user', 'Menghapus user dengan Username: ' . ($user ? $user->username : $id));

        redirect('user');
    }

    public function activate($id)
    {
        $session = check_token(); // ambil user session

        $data = [
            'disabled_at' => null,
            'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'updated_by' => $session['user']->username
        ];

        $this->User_model->update($id, $data);

        // Optional: flash message
        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil diaktifkan kembali.',
            'icon' => 'success'
        ]);

        $user = $this->User_model->get_by_id($id);
        log_activity($this, 'activate_user', 'Mengaktifkan user dengan Username: ' . ($user ? $user->username : $id));

        redirect('user');
    }

}
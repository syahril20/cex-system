<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Role_model');
        $this->load->helper(['url', 'form', 'activity', 'utils']);
    }

    public function index()
    {
        $session = check_token();
        $users = $this->User_model->get_all_users_except($session['user']->id);
        $user = $session['user'];

        $data['session'] = $session;
        $data['users'] = !empty($users) ? $users : [];
        $data['page'] = 'UserManagement';

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', ['data' => $data]);
        } else {
            redirect('/');
        }    }

    public function create()
    {
        $session = check_token();
        $roles = $this->Role_model->get_all();

        $user = $session['user'];

        $data['roles'] = $roles;
        $data['session'] = $session;
        $data['page'] = 'UserCreate';

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', ['data' => $data]);
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

        // Ambil data dari form (misal: username, email, role_id, dll)
        $data = [
            'id' => generate_uuid(),
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'role_id' => $role_id,
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $session['user']->username
        ];

        log_activity($this, 'create_user', 'Membuat user baru dengan email: ' . $data['email']);

        $this->User_model->insert($data);
        redirect('user');
    }

    public function edit($id)
    {
        $session = check_token();

        $users = $this->User_model->get_by_id($id);
        $roles = $this->Role_model->get_all();
        $user = $session['user'];

        if (empty($users) || $session['user']->id == $users->id) {
            $this->session->set_flashdata('swal', [
                'title' => 'Gagal!',
                'text' => 'User tidak ditemukan',
                'icon' => 'error'
            ]);
            redirect('user');
            return;
        }
        $data['roles'] = $roles;
        $data['session'] = $session;
        $data['page'] = 'UserEdit';
        $data['users'] = $users;

        if ($user->code == 'SUPER_ADMIN' || $user->code == 'ADMIN') {
            $this->load->view('base_page', ['data' => $data]);
        } else {
            redirect('/');
        }    }

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
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $session['user']->username
        ];


        log_activity($this, 'edit_user', 'Mengedit user dengan ID: ' . $id);

        $this->User_model->update($id, $data);
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

        redirect('user');
    }

    public function activate($id)
    {
        $session = check_token(); // ambil user session

        $data = [
            'disabled_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $session['user']->username
        ];

        $this->User_model->update($id, $data);

        // Optional: flash message
        $this->session->set_flashdata('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil diaktifkan kembali.',
            'icon' => 'success'
        ]);

        redirect('user');
    }

}
<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Role_model $Role_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 */
class Role extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Role_model');
        $this->load->helper(array('form', 'url', 'utils'));
        $this->load->library('form_validation');
    }

    public function index()
    {
        $session = check_token();
        $token = $session['token'] ?? null;
        $user = $session['user'] ?? null;

        $data['token'] = $token;
        $data['user'] = $user;
        $data['page'] = 'Role';
        $data['roles'] = $this->Role_model->get_all_roles();

        echo "<script>console.log('Roles Data:', " . json_encode($data) . ");</script>";
        
        $this->load->view('base_page', $data);
    }

    public function create()
    {
        $this->form_validation->set_rules('name', 'Role Name', 'required|is_unique[roles.name]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('roles/create');
        } else {
            $this->Role_model->create_role($this->input->post('name'));
            redirect('roles');
        }
    }

    public function edit($id)
    {
        $data['role'] = $this->Role_model->get_role($id);

        if (empty($data['role'])) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Role Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('roles/edit', $data);
        } else {
            $this->Role_model->update_role($id, $this->input->post('name'));
            redirect('roles');
        }
    }

    public function delete($id)
    {
        $this->Role_model->delete_role($id);
        redirect('roles');
    }
}
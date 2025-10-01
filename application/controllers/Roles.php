<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Role_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['roles'] = $this->Role_model->get_all_roles();
        $this->load->view('roles/index', $data);
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
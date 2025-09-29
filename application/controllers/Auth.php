<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function login()
	{
		$this->check_token();
		$this->load->view('test');
	}

	public function do_login()
	{
		$this->load->view('test');
	}

	public function register()
	{
		$this->load->view('test');
	}

	public function do_register()
	{
		$this->load->view('test');
	}

	public function logout()
	{
		$this->load->view('test');
	}

	// Sebaiknya simpan fungsi ini di file helper atau library agar bisa digunakan di mana saja.
	// Contoh: application/helpers/token_helper.php

	public function check_token()
	{
		$token = $this->session->userdata('token');
		if (!$token) {
			redirect('/');
		}

		$user = $this->db->get_where('user_tokens', ['token' => $token])->row();
		if (!$user) {
			redirect('/');
		}

		return $user->token;
	}
}

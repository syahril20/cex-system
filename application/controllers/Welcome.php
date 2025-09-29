<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
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
	public function index()
	{
		$session = $this->session->userdata();
		if (!$session || !isset($session['token'])) {
			// $this->load->view('auth/login');
						redirect('login');

			return;
		}

		$tokens = $this->db->get_where('user_tokens', ['token' => $session['token']])->row();
		if (!$tokens) {
			// $this->load->view('auth/login');
			redirect('login');
			return;
		}

		$user = $session['user'];
		$data['session'] = $session;
		$data['page'] = 'Dashboard';
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
}

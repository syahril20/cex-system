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
		$session = $this->check_token();

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

	private function check_token()
	{
		$session = $this->session->userdata();
		$token = isset($session['token']) ? $session['token'] : null;

		if ($token == '' || $token == null) {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Session telah kedaluwarsa. Silakan login kembali.',
				'icon' => 'error'
			]);
			redirect('login');
			return;
		}

		$tokendb = $this->db->get_where('user_tokens', ['token' => $token])->row();
		if (!$tokendb || strtotime($tokendb->expired_at) < time()) {
			$this->session->unset_userdata(['token', 'user']);
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Session telah kedaluwarsa. Silakan login kembali.',
				'icon' => 'error'
			]);
			redirect('login');
			return;
		}

		return $session;
	}

	public function error_404()
	{
		$this->load->view('errors/404');
	}
}

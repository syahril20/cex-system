<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *      http://example.com/index.php/welcome
	 *  - or -
	 *      http://example.com/index.php/welcome/index
	 *  - or -
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

		$admin_role = $this->db->get_where('roles', ['code' => 'ADMIN'])->row();
		$admin_role_id = $admin_role ? $admin_role->id : 0;

		$user = $session['user'];
		$data['session'] = $session;
		$data['page'] = 'Dashboard';
		$data['total_users'] = (int) $this->db->count_all('users') ?: 0;
		$data['total_admin'] = $admin_role_id
			? (int) $this->db->where('role_id', $admin_role_id)->count_all_results('users')
			: 0;
		$data['total_orders'] = (int) $this->db->count_all('orders') ?: 0;

		$this->db->select('activity.*, users.username');
		$this->db->from('activity');
		$this->db->join('users', 'users.id = activity.user_id', 'left');
		$this->db->where('activity.created_at >=', date('Y-m-d H:i:s', strtotime('-6 hours')));
		$this->db->order_by('activity.created_at', 'DESC');
		$activities = $this->db->get()->result_array();

		$data['recent_activities'] = $activities;

		$this->load->view('base_page', ['data' => $data]);

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

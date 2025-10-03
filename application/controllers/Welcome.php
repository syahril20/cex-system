<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function index()
	{
		$session = $this->check_token();

		// Get admin role ID
		$admin_role = $this->db->get_where('roles', ['code' => 'ADMIN'])->row();
		$admin_role_id = $admin_role ? $admin_role->id : 0;

		$user = $session['user'];

		// Data default
		$data = [
			'session' => $session,
			'page' => 'Dashboard',
		];

		// Key cache unik per role/user
		$cache_key = 'dashboard_' . strtolower($user->code) . '_' . $user->id;

		// Ambil cache
		$dashboard_data = $this->cache->get($cache_key);

		if ($dashboard_data === false) {
			// 🚨 Cache MISS → ambil dari DB
			$dashboard_data = [];

			if ($user->code === 'SUPER_ADMIN') {
				$this->db->select('activity.*, users.username');
				$this->db->from('activity');
				$this->db->join('users', 'users.id = activity.user_id', 'left');

				$dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
				$dt->modify('-12 hours');
				$this->db->where('activity.created_at >=', $dt->format('Y-m-d H:i:s'));
				$this->db->order_by('activity.created_at', 'DESC');
				$dashboard_data['recent_activities'] = $this->db->get()->result_array();

				$dashboard_data['total_users'] = (int) $this->db->count_all('users');
				$dashboard_data['total_admin'] = (int) $this->db->where('role_id', $admin_role_id)->count_all_results('users');
				$dashboard_data['total_orders'] = (int) $this->db->count_all('orders');
			}

			if ($user->code === 'AGENT') {
				$dashboard_data['total_orders'] = (int) $this->db
					->where('created_by', $user->username)
					->count_all_results('orders');

				$dashboard_data['today_orders'] = (int) $this->db
					->where('created_by', $user->username)
					->where('DATE(created_at)', date('Y-m-d', time() + 7 * 3600))
					->count_all_results('orders');
			}

			$dashboard_data['from_cache'] = false;

			// Simpan cache
			$this->cache->save($cache_key, $dashboard_data, 86400);
			echo "<script>console.log('Cache MISS (DB query). Key: {$cache_key}');</script>";
		} else {
			// 🚨 Cache HIT
			$dashboard_data['from_cache'] = true;
			echo "<script>console.log('Cache HIT (pakai cache). Key: {$cache_key}');</script>";
		}

		// Debug isi cache
		debug_cache($cache_key);

		// Gabung ke view
		$data = array_merge($data, $dashboard_data);

		// Flag info
		echo "<script>console.log('Data dari cache flag: " . ($data['from_cache'] ? 'YA' : 'TIDAK') . "');</script>";
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

	public function test_cache()
	{
		$key = 'coba_test';
		$data = $this->cache->get($key);

		if ($data === false) {
			$data = ['time' => date('H:i:s')];
			$this->cache->save($key, $data, 60);
			echo "<script>console.log('Cache MISS. Simpan baru: " . json_encode($data) . "');</script>";
		} else {
			echo "<script>console.log('Cache HIT. Data: " . json_encode($data) . "');</script>";
		}
	}

}

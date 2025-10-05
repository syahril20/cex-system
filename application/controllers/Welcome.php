<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller untuk halaman welcome/dashboard
 * @property Activity_model $Activity_model
 * @property User_model $User_model
 * @property Order_model $Order_model
 * @property Role_model $Role_model
 * @property CI_Session $session
 * @property CI_Cache $cache
 */
class Welcome extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'cache']);
		$this->load->helper(['url', 'utils']);
		$this->load->model(['Activity_model', 'User_model', 'Order_model', 'Role_model']);
	}

	public function index()
	{
		$session = check_token();
		$token = $session['token'] ?? null;
		$user = $session['user'] ?? null;
		if (!$user || !$token) {
			return force_logout('Data pengguna tidak ditemukan.');
		}

		$data['token'] = $token;
		$data['user'] = $user;
		$data['page'] = 'Dashboard';

		$code = $user->code ?? '';
		if ($code && $code === 'SUPER_ADMIN') {
			$data['recent_activities'] = $this->Activity_model->get_recent_activities() ?? [];
			$data['total_users'] = $this->User_model->count_all_users() ?? 0;
			$data['total_admin'] = $this->User_model->count_all_admin() ?? 0;
			$data['total_orders'] = $this->Order_model->count_all_orders() ?? 0;
		}
		if ($code && $code === 'ADMIN') {
			$data['recent_activities'] = $this->Activity_model->get_recent_activities_except_super_admin() ?? [];
			$data['total_agent'] = $this->User_model->count_all_agent() ?? 0;
			$data['total_admin'] = $this->User_model->count_all_admin() ?? 0;
			$data['total_orders'] = $this->Order_model->count_all_orders() ?? 0;
		}
		if ($code && $code === 'AGENT') {
			$userId = $user->id ?? '';
			$data['total_orders'] = $this->Order_model->count_orders_by_user_id($userId) ?? 0;
			$data['today_orders'] = $this->Order_model->count_today_orders_by_user_id($userId) ?? 0;
		}

		echo "<script>console.log('Session di navbar:', " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";

		$this->load->view('base_page', $data);
	}

	// public function index()
	// {
	// 	$session = $this->check_token();
	// 	if (!$session)
	// 		return;

	// 	$user = $session['user'] ?? null;
	// 	if (!$user)
	// 		return $this->force_logout('Data pengguna tidak ditemukan.');

	// 	// Ambil ID role admin
	// 	$admin_role_id = $this->db->select('id')->get_where('roles', ['code' => 'ADMIN'])->row('id') ?? 0;

	// 	// $data = [
	// 	// 	'session' => $session,
	// 	// 	'page' => 'Dashboard',
	// 	// ];

	// 	$data ['token'] = $session ['token'] ?? null;
	// 	$data ['user'] = $user;

	// 	// Cache key unik per user
	// 	$cache_key = 'dashboard_' . strtolower($user->code) . '_' . $user->id;

	// 	// Coba ambil dari cache
	// 	$dashboard_data = $this->cache->get($cache_key);

	// 	if ($dashboard_data === false) {
	// 		log_message('debug', "Cache MISS → DB query untuk {$cache_key}");

	// 		$dashboard_data = [];

	// 		if ($user->code === 'SUPER_ADMIN') {
	// 			$tz = new DateTimeZone('Asia/Jakarta');
	// 			$dt = new DateTime('now', $tz);
	// 			$dt->modify('-12 hours');

	// 			$this->db->select('activity.*, users.username');
	// 			$this->db->from('activity');
	// 			$this->db->join('users', 'users.id = activity.user_id', 'left');
	// 			$this->db->where('activity.created_at >=', $dt->format('Y-m-d H:i:s'));
	// 			$this->db->order_by('activity.created_at', 'DESC');
	// 			$dashboard_data['recent_activities'] = $this->db->get()->result_array();

	// 			$dashboard_data['total_users'] = (int) $this->db->count_all('users');
	// 			$dashboard_data['total_admin'] = (int) $this->db->where('role_id', $admin_role_id)->count_all_results('users');
	// 			$dashboard_data['total_orders'] = (int) $this->db->count_all('orders');
	// 		}

	// 		if ($user->code === 'AGENT') {
	// 			$username = $user->username;
	// 			$today = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

	// 			$dashboard_data['total_orders'] = (int) $this->db
	// 				->where('created_by', $username)
	// 				->count_all_results('orders');

	// 			$dashboard_data['today_orders'] = (int) $this->db
	// 				->where('created_by', $username)
	// 				->where('DATE(created_at)', $today)
	// 				->count_all_results('orders');
	// 		}

	// 		$dashboard_data['from_cache'] = false;

	// 		// Simpan cache 24 jam
	// 		$this->cache->save($cache_key, $dashboard_data, 86400);
	// 	} else {
	// 		$dashboard_data['from_cache'] = true;
	// 		log_message('debug', "Cache HIT (key: {$cache_key})");
	// 	}

	// 	$data = array_merge($data, $dashboard_data);

	// 	echo "<script>console.log('Session di navbar:', " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ");</script>";
	// 	$this->load->view('base_page', $data);
	// }

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
			log_message('debug', 'Cache MISS: ' . json_encode($data));
		} else {
			log_message('debug', 'Cache HIT: ' . json_encode($data));
		}

		echo json_encode($data);
	}
}

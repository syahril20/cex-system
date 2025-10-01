<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * (No code provided in the selection.)
 * @property CI_Session $session
 * @property CI_DB_active_record $db
 * @property User_model $User_model
 * @property JwtAuth $jwtauth
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Router $router
 * @property CI_Output $output
 * @property CI_Security $security
 * @property CI_Form_validation $form_validation
 * @property CI_Email $email
 */
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->database();
		$this->load->library('session');
		$this->load->library('JwtAuth');
		$this->load->helper('security');
	}

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
		// $this->check_token();
		$token = $this->session->userdata('token');
		if ($token != null) {
			redirect('/');
			return;
		}

		$this->load->view('auth/login');
	}

	public function do_login()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$user = $this->User_model->get_by_email($email);

		if (!$user || !password_verify($password, $user->password)) {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Login gagal.',
				'icon' => 'error'
			]);
			redirect('login');
		}


		// Cek jika akun ditangguhkan
		if (!empty($user->disabled_at)) {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Akun Anda ditangguhkan, harap hubungi admin.',
				'icon' => 'error'
			]);
			redirect('login');
		}

		// Buat JWT
		$this->load->library('JwtAuth');
		$token = $this->jwtauth->generate_token($user);

		// Delete old tokens
		$this->db->delete('user_tokens', ['user_id' => $user->id]);

		// Simpan ke DB
		$created_at = gmdate('Y-m-d H:i:s', time() + 7 * 3600);
		$expired_at = date('Y-m-d H:i:s', strtotime($created_at . ' +7 days'));
		$this->db->insert('user_tokens', [
			'id' => $this->generate_uuid(),
			'user_id' => $user->id,
			'token' => $token,
			'expired_at' => $expired_at,
			'created_at' => $created_at,
		]);

		// Simpan token di session
		$this->session->set_userdata('token', $token);
		$this->session->set_userdata('user', $user);

		$this->session->set_flashdata('swal', [
			'title' => 'Berhasil!',
			'text' => 'Login berhasil.',
			'icon' => 'success'
		]);

		redirect('/');  // atau halaman utama
	}

	public function register()
	{
		$roles = $this->db->get('roles')->result();
		$data['roles'] = $roles;
		$this->load->view('auth/register', $data);
	}

	public function do_register()
	{
		$username = $this->input->post('username');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$role_id = $this->input->post('role_id');

		// Validasi unik
		if (!$this->User_model->is_unique_username($username)) {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Username sudah dipakai.',
				'icon' => 'error'
			]);
			redirect('auth/register');
		}

		if (!$this->User_model->is_unique_email($email)) {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Email sudah dipakai.',
				'icon' => 'error'
			]);
			redirect('auth/register');
		}

		// Insert data
		$data = [
			'id' => $this->generate_uuid(),
			'username' => $username,
			'email' => $email,
			'password' => password_hash($password, PASSWORD_BCRYPT),
			'role_id' => $role_id,
			'created_by' => 'system',
			'created_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
			'updated_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600)
		];

		if ($this->User_model->insert_user($data)) {
			$this->session->set_flashdata('swal', [
				'title' => 'Berhasil!',
				'text' => 'Register berhasil, silakan login.',
				'icon' => 'success'
			]);
			redirect('login');
		} else {
			$this->session->set_flashdata('swal', [
				'title' => 'Gagal!',
				'text' => 'Register gagal.',
				'icon' => 'error'
			]);
			redirect('register');
		}
	}


	public function logout()
	{
		$token = $this->session->userdata('token');

		// Hapus token dari database jika pakai JWT token
		if ($token) {
			$this->db->delete('user_tokens', ['token' => $token]);
		}

		// Hapus session
		$this->session->unset_userdata(['token', 'user_id']);

		// Optional: flash message

		$this->session->set_flashdata('swal', [
			'title' => 'Berhasil!',
			'text' => 'Logout berhasil.',
			'icon' => 'success'
		]);
		// Redirect ke halaman login
		redirect('login');
	}

	private function generate_uuid()
	{
		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xFFFF),
			mt_rand(0, 0xFFFF),
			mt_rand(0, 0xFFFF),
			mt_rand(0, 0xFFF) | 0x4000,
			mt_rand(0, 0x3FFF) | 0x8000,
			mt_rand(0, 0xFFFF),
			mt_rand(0, 0xFFFF),
			mt_rand(0, 0xFFFF)
		);
	}


}

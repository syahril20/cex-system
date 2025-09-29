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
		$this->check_token();
		// $this->load->view('auth/login');
	}

	public function do_login()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$user = $this->User_model->get_by_email($email);

		if (!$user || !password_verify($password, $user->password)) {
			$this->session->set_flashdata('error', 'Invalid credentials');
			redirect('login');
		}

		// Buat JWT
		$this->load->library('JwtAuth');
		$token = $this->jwtauth->generate_token($user);

		// Delete old tokens
		$this->db->delete('user_tokens', ['user_id' => $user->id]);

		// Simpan ke DB
		$expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
		$this->db->insert('user_tokens', [
			'id' => $this->generate_uuid(),
			'user_id' => $user->id,
			'token' => $token,
			'expired_at' => $expired_at
		]);

		// Simpan token di session
		$this->session->set_userdata('token', $token);
		$this->session->set_userdata('user', $user);

		redirect('/');  // atau halaman utama
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
        $token = $this->session->userdata('token');

        // Hapus token dari database jika pakai JWT token
        if ($token) {
            $this->db->delete('user_tokens', ['token' => $token]);
        }

        // Hapus session
        $this->session->unset_userdata(['token', 'user_id']);

        // Optional: flash message
        $this->session->set_flashdata('success', 'You have successfully logged out.');

        // Redirect ke halaman login
        redirect('login');
    }

	public function check_token()
	{
		$token = $this->session->userdata('token');
		if (!$token) {
			$this->load->view('auth/login');
			return;
		}

		$user = $this->db->get_where('user_tokens', ['token' => $token])->row();
		if (!$user) {
			$this->load->view('auth/login');
			return;
		}

		return $user->token;
	}

	public function generate_uuid()
	{
		$data = random_bytes(16);
		assert(strlen($data) == 16);

		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Versi 4
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Varian

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	
}

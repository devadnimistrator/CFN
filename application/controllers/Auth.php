<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for authontication
 *
 * - Signin
 * - Sugnout
 */
class Auth extends My_Controller {
	public function __construct() {
    parent::__construct();
	}

	public function signin() {
		$error_msgs = FALSE;
		$this -> load -> model("user_m");
			
		if ($this -> input -> post('action') == 'signin') {
			$this -> load -> library('form_validation');

			$valid_config = array(
				array(
					'field' => 'username',
					'label' => __('Username'),
					'rules' => 'required'
				),
				array(
					'field' => 'password',
					'label' => __('Password'),
					'rules' => 'required',
				)
			);

			$this -> form_validation -> set_rules($valid_config);
			
			$this -> user_m -> username = $this -> input -> post('username');
			$this -> user_m -> password = $this -> input -> post('password');

			$error_msgs = array();
			if ($this -> form_validation -> run() == FALSE) {
				$error_msgs = $this -> form_validation -> error_array();
			} else {
				$error_code = $this -> user_m -> signin();

				if ($error_code == 0) {					
					$this -> session -> set_userdata("logined_user_id", $this -> user_m -> id);
          
          if ($this->session->userdata('last_access_admin_page')) {
            redirect($this->session->userdata('last_access_admin_page'));
          } else {
            redirect("home");
          }
				} elseif ($error_code == -3) {
					$error_msgs = __("Your account can't access to admin page.");
				} else {
					$error_msgs = __("Name or Password is not validate.");
				}
			}
		}

		$this -> load -> view('auth/signin', array("error_msgs" => $error_msgs));
	}

	public function signout() {
		$this->session->sess_destroy();
		redirect("auth/signin");
	}

}

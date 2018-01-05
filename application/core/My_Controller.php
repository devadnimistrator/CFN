<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Basic My Controller
 */
class My_Controller extends CI_Controller {

  public $logined_user = FALSE;
  public $pageTitle = "Welcome";
  public $before_called_url = "";
  public $extends_css = array();
  public $extends_js = array();
  public $translators = array();

  public function __construct() {
    parent::__construct();
    // Your own constructor code

    $this->before_called_url = base_url("home");
    if ($this->session->userdata('last_called_url')) {
      $this->before_called_url = $this->session->userdata('last_called_url');
    }

    my_set_default_configs();

    $this->_load_lang();

    if ($this->session->userdata('logined_user_id')) {
      $this->load->model("user_m");
      $this->load->model("userinfo_m");
      $this->logined_user = new User_m();
      $this->logined_user->get_by_id($this->session->userdata('logined_user_id'));

      $this->logined_userinfo = new Userinfo_m();
      $this->logined_userinfo->get_by_user_id($this->logined_user->id);


//      $this->logined_user->last_access = date('Y-m-d H:i:s');
      $this->logined_user->save();
    }

    if ($this->uri->rsegment(1) == 'error') {
      return;
    }

    if ($this->logined_user && $this->logined_user->status == 0) {
      my_set_system_message(__("Your account has blocked"), "danger");
      redirect("error");
    }

    $this->_check_role();
  }

  /**
   * Check user for signin and role
   */
  private function _check_role() {
    $no_logined_pages = $this->config->item('no_login_pages');
    $current_path = uri_string();

    if (in_array($current_path, $no_logined_pages)) {
      // Page is that don't need signin
    } else {
      if ($this->logined_user === FALSE || !$this->logined_user->is_exists()) {// if user didn't signin
        redirect('auth/signin');
      }
    }
  }

  private function _load_lang() {
    $class_name = strtolower(get_class($this));
    $language = DISPLAY_LANGUAGE; //$this->config->item("language");

    if ($language != 'english') {
      $this->lang->load("app", $language);
      $this->lang->load("datetime", $language);
    }
    $this->load->library("my_translator", array("language" => $language, "controller" => $class_name));
  }

  /**
   * Utilizing the CodeIgniter's _remap function
   * to call extra functions with the controller action
   */
  public function _remap($method, $args) {
    if (strpos($method, "ajax") !== 0 && strpos($method, "export") !== 0 && $this->uri->rsegment(1) != 'error') {
      // Call before action
      $this->_before();
    }

    if (method_exists($this, $method)) {
      //  Call the method
      call_user_func_array(array(
          $this,
          $method
          ), $args);
    } else {
      show_404();
    }

    if (strpos($method, "ajax") !== 0 && strpos($method, "export") !== 0 && $this->uri->rsegment(1) != 'error') {
      // Call after action
      $this->_after();
    }
  }

  private function _before() {
    if ($this->uri->segment(1) == 'auth' || $this->uri->segment(1) == 'cron') {
      
    } else {
      $this->load->view('common/header');
    }
  }

  private function _after() {
    if ($this->uri->segment(1) == 'auth' || $this->uri->segment(1) == 'cron') {
      
    } else {
      $this->load->view('common/footer');
    }
  }

  public function check_admin() {
    if (!$this->logined_user->is_admin()) {
      my_set_system_message(__("You have not admin role."), "danger");
      redirect("income/error");
    }
  }

  public function save_list_ids($path, $model_list) {
    $ids = array();
    foreach ($model_list as $model) {
      $ids[] = $model->id;
    }

    $this->session->set_userdata($path, $ids);
  }

  public function get_list_ids($path) {
    $ids = $this->session->userdata($path);
    if ($ids) {
      return $ids;
    } else {
      return false;
    }
  }

  public function my_input_post($field, $default_value = false) {
    $_value = $this->input->post($field);
    if ($_value) {
      return $_value;
    }
    if ($default_value) {
      return $default_value;
    }

    return $_value;
  }

  public function my_input_get($field, $default_value = false) {
    $_value = $this->input->get($field);
    if ($_value) {
      return $_value;
    }
    if ($default_value) {
      return $default_value;
    }

    return $_value;
  }

  public function my_save_current_url() {
    $current_url = current_url();
    if ($this->before_called_url != $current_url) {
      $this->session->set_userdata("last_called_url", current_url());
    }
  }

  public function add_css($css) {
    $this->extends_css[] = $css;
  }

  public function add_js($js) {
    $this->extends_js[] = $js;
  }

}

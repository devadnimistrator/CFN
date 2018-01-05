<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for authontication
 *
 * - Signin
 * - Sugnout
 */
class Error extends CI_Controller {
  function __construct() {
    parent::__construct();

    my_set_default_configs();

    $class_name = strtolower(get_class($this));
    $language = DISPLAY_LANGUAGE; //$this->config->item("language");

    if ($language != 'english') {
      $this->lang->load("app", $language);
      $this->lang->load("datetime", $language);
    }
    $this->load->library("my_translator", array("language" => $language, "controller" => $class_name));
  }
  
  function index() {
    $this->load->view("errors/html/error");
  }

  function error_404() {
    $this->load->view("errors/html/error_404");
  }

}

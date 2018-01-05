<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for profile
 *
 * - Change Profile
 */
class Users extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Users");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New User");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->user_m->get_by_id($this->uri->rsegment(3));
      $this->userinfo_m->get_by_user_id($this->user_m->id);
      $this->page_title = __("Edit User") . ": #" . $this->user_m->id;
    }
  }

  public function index() {
    $this->users = $this->user_m->get_all();

    if ($this->input->post('action') == 'process') {
      if ($this->logined_user->form_validate($this->input->post()) == FALSE) {

      } else {
        $this->user_m->username = $this->input->post('username');
        $this->user_m->password = my_encrypt_password($this->input->post('new_password'));
        if ($this->user_m->save()) {
          my_set_system_message(__("Successfully saved user informations."), "success");
          redirect("users");

        } else {
          my_set_system_message(__("Failed save user informations."), "danger");
        }
      }
    }

    $this->load->view("users/list", array(
      'user_m' => $this->user_m
    ));
  }

  public function ajax_find()
  {
    $result = $this->user_m->get_all();

    $users = array();

    if ($result) {
      $index = 1;
      foreach ($result as $user) {
        $users[] = array(
          "index" => $index++,
          "user_id" => $user->id,
          "username" => $user->username,
          "group" => $user->group,
          "actions" => my_make_table_delete_icon('javascript:delete_user(' . $user->id . ')')
        );
      }
    }

    $returnData = array(
      'data' => $users
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_delete() {
    $user_id = $this->uri->rsegment(3);
    $this->user_m->get_by_id($user_id);
    if ($this->user_m->is_exists()) {
      $this->user_m->delete();
    }
  }

}

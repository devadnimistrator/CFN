<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_m extends My_Model {

  public $fields = array(
      'username' => array(
          'label' => 'Username',
          'rules' => array('required', 'min_length[3]', 'alpha_numeric')
      ),
      'password' => array(
          'label' => 'Password',
          'rules' => array('required', 'min_length[6]'),
          'type' => 'password',
      ),
      'group' => array(
          'label' => 'Group',
          'rules' => array('required')
      )
  );

  public function signin($group = 'all') {
    $error_code = 0;

    $original_password = $this->password;
    $this->get_by_username($this->username);

    if ($this->is_exists()) {
      if ($this->status == 0) {
        return -3;
      } else {
        if (my_validate_password($original_password, $this->password)) {
          return $error_code;
        } else {
          return -2;
        }
      }
    } else {
      return -1;
    }
    
    if ($group != 'all' && $this->group != $group) {
      return -4;
    }
  }

  public function is_admin() {
    return $this->group == 'admin';
  }

  public function form_validate($values) {
    $result = parent::form_validate($values);
    if ($result === FALSE) {
      return FALSE;
    }

    $this->db->where("username", $this->username);
    if ($this->is_exists()) {
      $this->db->where("id !=", $this->id);
    }
    $count = $this->db->count_all_results($this->table);
    if ($count == 0) {
      return TRUE;
    } else {
      $this->add_error("username", __("The Username field must contain a unique value."));
      return FALSE;
    }
  }

  public function get_all() {
    return $this->db->get($this->table)->result();
  }


}

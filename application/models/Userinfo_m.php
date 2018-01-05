<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Userinfo_m extends My_Model {

  public $fields = array(
      'user_id' => array(
          'label' => 'UserID',
          'rules' => array('required')
      ),
      'fullname' => array(
          'label' => 'Full Name',
          'rules' => array(
              'required',
              'min_length[3]'
          )
      ),
      'email' => array(
          'label' => 'Email',
          'type' => 'email',
          'rules' => array(
              'required',
              'min_length[6]',
              'valid_email')
      ),
      'phone' => array(
          'type' => 'tel',
          'label' => 'Phone',
          'rules' => array(
              'min_length[8]'
          )
      ),
      'cellphone' => array(
          'label' => 'CellPhone',
      )
  );

  private function set_filter($status, $search) {
    $user_m_table_name = $this->user_m->get_table();

    $this->db->select(array(
        $this->table . ".`id`",
        $this->table . ".`user_id`",
        $this->table . ".`fullname`",
        $this->table . ".`email`",
        $this->table . ".`phone`",
        $user_m_table_name . ".`status`",
        $user_m_table_name . ".username",
//        $user_m_table_name . ".last_access",
        "'' as action_str"
    ));
    $this->db->from($this->table);
    $this->db->join($user_m_table_name, $this->table . ".user_id=" . $user_m_table_name . ".`id`");
    if ($search != '') {
      $this->db->group_start();
      $this->db->like($user_m_table_name . ".`username`", $search);
      $this->db->or_like($this->table . ".`fullname`", $search);
      $this->db->or_like($this->table . ".`email`", $search);
      $this->db->or_like($this->table . ".`phone`", $search);
      $this->db->group_end();
    }
    if ($status != 'all') {
      $this->db->where($user_m_table_name . ".`status`=" . $status);
    }
    $this->db->where($user_m_table_name . ".`group`='cashier'");
  }

  public function find($status, $search, $orders, $start, $length) {
    $this->user_m = new User_m(false);

    $all_count = $this->user_m->count_by_group("cashier");
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "count" => 0,
          "data" => array()
      );
    }
    $this->set_filter($status, $search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
          "total" => $all_count,
          "count" => $count_by_filter,
          "data" => array()
      );
    }

    $this->set_filter($status, $search);
    foreach ($orders as $order) {
      switch ($order['column']) {
        case 1 :
          $order_field = $user_m_table_name . ".`username`";
          break;
        case 2 :
          $order_field = $this->table . ".`fullname`";
          break;
        case 3 :
          $order_field = $this->table . ".`email`";
          break;
        case 4 :
          $order_field = $this->table . ".`phone`";
          break;
        case 5 :
          $order_field = "`status`";
          break;
        case 6 :
          $order_field = $this->user_m->table . ".`last_access`";
          break;
      }
      $this->db->order_by($order_field, $order['dir']);
    }
    $this->db->limit($length, $start);
    return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => $this->db->get()->result()
    );
  }

  public function form_validate($values) {
    $result = parent::form_validate($values);
    if ($result === FALSE) {
      return FALSE;
    }

    $this->db->where("email", $this->email);
    if ($this->is_exists()) {
      $this->db->where("id !=", $this->id);
    }
    $count = $this->db->count_all_results($this->table);
    if ($count == 0) {
      return TRUE;
    } else {
      $this->add_error("email", __("The Email field must contain a unique value."));
      return FALSE;
    }
  }

}

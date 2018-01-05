<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Department_m extends My_Model {

  public $fields = array(
    'account_id' => array(
      'label' => 'Account ID',
      'type' => 'number'
    ),
    'department_id' => array(
      'label' => 'Department Id'
    ),
    'department_name' => array(
      'label' => 'Department Name'
    )
  );

  private function set_filter($account_id, $search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($account_id) {
      $this->db->where("account_id", $account_id);
    }
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("account_id", $search);
      $this->db->or_like("department_id", $search);
      $this->db->or_like("department_name", $search);
      $this->db->group_end();
    }
  }

  public function find($account_id, $search, $orders, $start, $length) {
    $all_count = $this->count_by_account_id($account_id);
    if ($all_count == 0) {
      return array(
        "total" => $all_count,
        "count" => 0,
        "data" => array()
      );
    }
    $this->set_filter($account_id, $search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => array()
      );
    }

    $this->set_filter($account_id, $search);
    if ($orders) {
      foreach ($orders as $order) {
        switch ($order['column']) {
          case 1 :
            $order_field = "account_id";
            break;
          case 2 :
            $order_field = "department_id";
            break;
          case 3 :
            $order_field = "department_name";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("vehicle_id");
    }
    $this->db->limit($length, $start);
    return array(
      "total" => $all_count,
      "count" => $count_by_filter,
      "data" => $this->db->get()->result()
    );
  }

  public function delete() {
    parent::delete();
  }

}

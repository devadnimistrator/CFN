<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_m extends My_Model {

  public $fields = array(
    'account_id' => array(
      'label' => 'Account ID',
      'type' => 'number',
      'rules' => 'required'
    ),
    'vehicle_id' => array(
      'label' => 'Vehicle ID',
      'type' => 'number',
      'rules' => 'required'
    ),
    'vehicle_description' => array(
      'label' => 'Vehicle Description',
      'rules' => array('required', 'min_length[3]')
    ),
    'card_id' => array(
      'label' => 'Card ID',
      'type' => 'number',
      'rules' => 'required'
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
      $this->db->or_like("vehicle_id", $search);
      $this->db->or_like("vehicle_description", $search);
      $this->db->or_like("card_id", $search);
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
            $order_field = "vehicle_id";
            break;
          case 3 :
            $order_field = "vehicle_description";
            break;
          case 4 :
            $order_field = "card_id";
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

  public function importdata ($data) {
    $vehicle_data = array(
      $data[0],
      $data[9],
      $data[4],
      $data[3]
    );

    $result = $this->get_data($vehicle_data);

    if (!$result) {
      $this->db->set('account_id', $data[0]);
      $this->db->set('vehicle_id', $data[9]);
      $this->db->set('vehicle_description', $data[4]);
      $this->db->set('card_id', $data[3]);
      $this->db->insert($this->table);
    }
  }

  public function get_data($data){
    $lists = $this->db->list_fields($this->table);
    for ($i = 1; $i < count($lists); $i ++) {
      $this->db->where($lists[$i], $data[$i-1]);
    }
    $query=$this->db->get($this->table);
    $result=$query->result();
    return $result;
  }

  public function remove_alldata() {
    $this->db->empty_table($this->table);
  }


}

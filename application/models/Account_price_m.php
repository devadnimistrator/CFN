<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account_price_m extends My_Model {

  public $fields = array(
    'account_id' => array(
      'label' => 'Account ID',
      'type' => 'number',
      'rules' => 'required'
    ),
    'R_price' => array(
      'label' => 'R Price',
      'type' => 'number',
      'rules' => 'required'
    ),
    'D_price' => array(
      'label' => 'D Price',
      'type' => 'number',
      'rules' => 'required'
    ),
    'F_price' => array(
      'label' => 'F Price',
      'type' => 'number',
      'rules' => 'required'
    ),
    'N_price' => array(
      'label' => 'N Price',
      'type' => 'number',
      'rules' => 'required'
    ),
    'E_price' => array(
      'label' => 'E Price',
      'type' => 'number',
      'rules' => 'required'
    ),
    'C_price' => array(
      'label' => 'C Price',
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
      $this->db->or_like("R_price", $search);
      $this->db->or_like("D_price", $search);
      $this->db->or_like("F_price", $search);
      $this->db->or_like("N_price", $search);
      $this->db->or_like("E_price", $search);
      $this->db->or_like("C_price", $search);
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
            $order_field = "R_price";
            break;
          case 3 :
            $order_field = "D_price";
            break;
          case 4 :
            $order_field = "F_price";
            break;
          case 5 :
            $order_field = "N_price";
            break;
          case 6 :
            $order_field = "E_price";
            break;
          case 7 :
            $order_field = "C_price";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("account_id");
      $this->db->order_by("R_price");
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

  public function savedata($data, $price_id = false) {
    if ($price_id) {
      $this->db->where('id', $price_id);
      $this->db->update($this->table, $data);
    } else {
      $lists = $this->db->list_fields($this->table);
      for ($i = 1; $i < count($lists); $i ++) {
        $this->db->set($lists[$i], $data[$i]);
      }
      $this->db->insert($this->table);
    }
    return true;
  }

  public function get_price_by_sitetype ($account_id, $site_type) {

    $this->db->select(''.$site_type.'_price');
    $this->db->from($this->table);
    $this->db->where('account_id='.$account_id);
    $result = $this->db->get()->result();

    if ($result == null) {
      return 0;
    } else {
		foreach ($result[0] as $key => $value) {
		  $price = $value;
		}
      return $price;
    }

  }

  public function remove_alldata() {
    $this->db->empty_table($this->table);
  }


}

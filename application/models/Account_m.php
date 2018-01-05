<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account_m extends My_Model {

  public $fields = array(

    'account' => array(
      'label' => 'Account',
      'type' => 'number'
    ),
    'name' => array(
      'label' => 'Name'
    ),
    'address1' => array(
      'label' => 'Address1',
    ),
    'address2' => array(
      'label' => 'Address2',
    ),
    'city' => array(
      'label' => 'City',
    ),
    'state' => array(
      'label' => 'State'
    ),
    'zip' => array(
      'label' => 'Zip',
//      'type' => 'number'
    ),
    'phone' => array(
      'label' => 'Phone'
    ),
    'fax' => array(
      'label' => 'Fax'
    ),
    'contact' => array(
      'label' => 'contact'
    ),
    'email' => array(
      'label' => 'Email',
      'type' => 'email',
      'rules' => array(
        'min_length[6]',
        'valid_email')
    ),
    'discount' => array(
      'label' => 'Discount'
    ),
  );

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("account", $search);
      $this->db->or_like("name", $search);
      $this->db->or_like("address1", $search);
      $this->db->or_like("address2", $search);
      $this->db->or_like("city", $search);
      $this->db->or_like("state", $search);
      $this->db->or_like("zip", $search);
      $this->db->or_like("phone", $search);
      $this->db->or_like("fax", $search);
      $this->db->or_like("contact", $search);
      $this->db->or_like("email", $search);
      $this->db->or_like("discount", $search);
      $this->db->group_end();
    }
  }

  public function find($search, $orders, $start, $length) {
    $all_count = $this->count_all();
    if ($all_count == 0) {
      return array(
        "total" => $all_count,
        "count" => 0,
        "data" => array()
      );
    }
    $this->set_filter($search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => array()
      );
    }

    $this->set_filter($search);
    if ($orders) {
      foreach ($orders as $order) {
        switch ($order['column']) {
          case 1 :
            $order_field = "account";
            break;
          case 2 :
            $order_field = "name";
            break;
          case 3 :
            $order_field = "address1";
            break;
          case 4 :
            $order_field = "address2";
            break;
          case 5 :
            $order_field = "city";
            break;
          case 6 :
            $order_field = "state";
            break;
          case 7 :
            $order_field = "zip";
            break;
          case 8 :
            $order_field = "phone";
            break;
          case 9 :
            $order_field = "fax";
            break;
          case 10 :
            $order_field = "contact";
            break;
          case 11 :
            $order_field = "email";
            break;
          case 12 :
            $order_field = "discount";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("account");
      $this->db->order_by("name");
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

  public function get_all_account($all = false) {
    $this->db->order_by('id');
    $results = $this->db->get($this->table)->result();

    $accounts = array();
    if ($all) {
      $accounts['all'] = 'All Accounts';
    }

    foreach ($results as $result) {
      $accounts[$result->account] = $result->name;
    }
    return $accounts;
  }

  public function importdata($data) {
    $lists = $this->db->list_fields($this->table);
    $result = $this->get_data($data);
    if(!$result) {
      for ($i = 1; $i < count($lists); $i++) {
        $this->db->set($lists[$i], $data[$i - 1]);
      }
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

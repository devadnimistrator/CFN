<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_m extends My_Model {

  public $fields = array(
    'product_code' => array(
      'label' => 'Product Code',
      'type' => 'number',
      'rules' => 'required'
    ),
    'product_description' => array(
      'label' => 'Product Description',
      'rules' => array('required', 'min_length[3]')
    ),
    'product_type' => array(
      'label' => 'Product Type',
      'rules' => array('required', 'min_length[1]')
    ),
    'over_price' => array(
      'label' => 'Over Price'
    )
  );

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("product_code", $search);
      $this->db->or_like("product_type", $search);
      $this->db->or_like("product_description", $search);
      $this->db->or_like("over_price", $search);
//      $this->db->or_like("price", $search);
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
            $order_field = "product_code";
            break;
          case 2 :
            $order_field = "product_type";
            break;
          case 3 :
            $order_field = "product_description";
            break;
          case 4 :
            $order_field = "over_price";
            break;
//          case 4 :
//            $order_field = "price";
//            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("product_code");
      $this->db->order_by("product_type");
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

  public function get_all_products() {
    $this->db->order_by('product_code');
    $results = $this->db->get($this->table)->result();
    $sites = array();
    foreach ($results as $result) {
      $sites[$result->id] = $result->product_code . " : " . $result->product_type . " : " . $result->product_description;
    }
    return $sites;
  }

}

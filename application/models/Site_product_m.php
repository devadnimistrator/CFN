<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Site_product_m extends My_Model {

  public $fields = array(
    'site_id' => array(
      'label' => 'Site ID',
      'type' => 'number'
    ),
    'product_id' => array(
      'label' => 'Product ID',
      'type' => 'number'
    ),
    'product_code' => array(
      'label' => 'Product Code',
      'rules' => array('required')
    ),
    'price' => array(
      'label' => 'Price',
      'type' => 'number',
      'rules' => array('required', 'min_length[1]')
    )
  );

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("site_id", $search);
      $this->db->or_like("product_id", $search);
      $this->db->or_like("price", $search);
      $this->db->group_end();
    }
  }

  public function find($search, $orders, $start, $length, $site_id) {
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
            $order_field = "site_id";
            break;
          case 2 :
            $order_field = "product_id";
            break;
          case 3 :
            $order_field = "price";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("site_id");
      $this->db->order_by("product_id");
    }

    $this->db->where("site_id", $site_id);
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

  public function getprice($site_id, $product_id) {
    $this->db->from($this->table);
    $this->db->where("site_id", $site_id);
    $this->db->where("product_id", $product_id);
    $price = $this->db->get()->result();

    return $price;

  }

  public function getdata () {

  }

}

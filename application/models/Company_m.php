<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Company_m extends My_Model {

  public $fields = array(
    'company_id' => array(
      'label' => 'Company ID',
      'type' => 'number'
    ),
    'client_consolidation' => array(
      'label' => 'Client Consolidation',
      'type' => 'number'
    )
  );

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("company_id", $search);
      $this->db->or_like("client_consolidation", $search);
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
          case 0 :
            $order_field = "company_id";
            break;
          case 1 :
            $order_field = "client_consolidation";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("company_id");
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
  
  public function get_default() {
      $this->db->reset_query();
      return $this->db->from($this->table)->limit(1)->get()->row();
  }
}

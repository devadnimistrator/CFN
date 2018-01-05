<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_item_m extends My_Model {
  public $fields = array(
    'invoice_id' => array(
      'label' => 'Invoice ID',
      'type' => 'number'
    ),
    'ptdata_id' => array(
      'label' => 'PTdata ID',
      'type' => 'number'
    )
  );
  public function delete() {
    parent::delete();
  }

  public function delete_invoice_items ($invoice_id) {
    $this->db->where('invoice_id='.$invoice_id);
    $this->db->from($this->table);
    $this->db->delete();
  }
}

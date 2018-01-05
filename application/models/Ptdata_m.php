<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ptdata_m extends My_Model
{

    public $fields = array(
        'site_id' => array(
            'label' => 'Site ID Number',
            'type' => 'number',
            'rules' => 'required'
        ),
        'sequence_umber' => array(
            'label' => 'Sequence Number',
            'type' => 'number',
        ),
        'status_code' => array(
            'label' => 'Status Code',
            'type' => 'number',
        ),
        'total_amount' => array(
            'label' => 'Total Amount',
            'type' => 'number',
        ),
        'transaction_type' => array(
            'label' => 'Transaction Type',
            'type' => 'number',
        ),
        'product_id' => array(
            'label' => 'Product',
            'type' => 'number',
            'rules' => 'required'
        ),
        'price' => array(
            'label' => 'Price',
            'type' => 'number',
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'type' => 'number',
        ),
        'odometer' => array(
            'label' => 'Odometer',
            'type' => 'number',
        ),
        'pump_number_or_register_number' => array(
            'label' => 'Pump Number or Register Number',
            'type' => 'number',
        ),
        'transaction_number' => array(
            'label' => 'Transaction Number',
            'type' => 'number',
        ),
        'date_completed' => array(
            'label' => 'Date Completed',
            'rules' => 'required'
        ),
        'time_completed' => array(
            'label' => 'Time Completed'

        ),
        'error_code' => array(
            'label' => 'Error Code',
            'type' => 'number',
        ),
        'authorization_number' => array(
            'label' => 'Authorization Number',
            'type' => 'number',
        ),
        'manual_entry' => array(
            'label' => 'Manual Entry',
            'type' => 'number',
        ),
        'card_id' => array(
            'label' => 'Card',
            'type' => 'number',
            'rules' => 'required'
        ),
        'site_type' => array(
            'label' => 'Site Type',
            'rules' => 'required'
        ),
        'pump_price' => array(
            'label' => 'Pump Price',
            'type' => 'number',
        ),
        'cfn_price' => array(
            'label' => 'CFN Price',
            'type' => 'number',
        ),
        'company_id' => array(
            'label' => 'Company'
        ),
    );

    private function set_filter($date)
    {
        $this->db->reset_query();
        $this->db->from($this->table);
        $this->db->where("date_completed", $date);
    }

    public function find($date, $order, $start, $length)
    {
        $all_count = $this->count_by_date_completed($date);
        if ($all_count == 0)
        {
            return array(
                "total" => $all_count,
                "data" => array()
            );
        }
        
        $this->set_filter($date);
        $this->db->order_by("date_completed", $order['dir']);
        $this->db->order_by("time_completed", $order['dir']);
        $this->db->limit($length, $start);

        return array(
            "total" => $all_count,
            "data" => $this->db->get()->result()
        );
    }

    public function delete()
    {
        parent::delete();
    }

    public function remove_alldata()
    {
        $this->db->empty_table($this->table);
    }

}

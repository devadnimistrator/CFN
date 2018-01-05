<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_m extends My_Model
{

    public $fields = array(
        'invoice_no' => array(
            'label' => 'Date',
            'rules' => array(
                'required')
        ),
        'date' => array(
            'label' => 'Date',
            'rules' => array(
                'required')
        ),
        'account_id' => array(
            'label' => 'Account',
            'type' => 'number',
            'rules' => array(
                'required')
        ),
        'start_pt_date' => array(
            'label' => 'Start PT Date',
            'type' => 'date',
            'rules' => array(
                'required')
        ),
        'end_pt_date' => array(
            'label' => 'End PT Date',
            'type' => 'date',
            'rules' => array(
                'required')
        ),
        'total_price' => array(
            'label' => 'Total Price',
            'type' => 'number',
            'rules' => array(
                'required')
        ),
        'calc_method' => array(
            'label' => 'Calculator Method',
            'rules' => array(
                'required')
        ),
        'state' => array(
            'label' => 'State',
            'type' => 'number',
            'rules' => array(
                'required')
        ),
        'ptdata_ids' => array(
            'label' => 'PT Data',
            'rules' => array(
                'required')
        )
    );

    private function set_filter($search)
    {
        $this->db->reset_query();
        $this->db->from($this->table);

        if ($search['account_id'] != 'all')
        {
            $this->db->where("account_id", $search['account_id']);
        }

        if ($search['start_date'] == '' || $search['end_date'] == '') {

        } else {

          $this->db->where('substr(`date`, 1, 10) BETWEEN "' . $search['start_date'] . '" AND "' . $search['end_date'] . '"');
        }

        if ($search['state'] != 'all')
        {
            $this->db->where("state", $search['state']);
        }
    }

    public function find($search, $orders, $start, $length)
    {
        $all_count = $this->count_all();
        if ($all_count == 0)
        {
            return array(
                "total" => $all_count,
                "count" => 0,
                "data" => array()
            );
        }

        $this->set_filter($search);
        $count_by_filter = $this->db->count_all_results();
        if ($count_by_filter == 0)
        {
            return array(
                "total" => $all_count,
                "count" => $count_by_filter,
                "data" => array()
            );
        }

        $this->set_filter($search);
        if ($orders)
        {
            foreach ($orders as $order)
            {
                switch ($order['column'])
                {
                    case 0:
                        $order_field = "invoice_no";
                        break;
                    case 1:
                        $order_field = "date";
                        break;
                }
                $this->db->order_by($order_field, $order['dir']);
            }
        } else
        {
            $this->db->order_by("date");
            $this->db->order_by("account_id");
        }

        $this->db->limit($length, $start);
        return array(
            "total" => $all_count,
            "count" => $count_by_filter,
            "data" => $this->db->get()->result()
        );
    }

    public function delete()
    {
        parent::delete();
    }

    public function get_all_invoice_no($state = 'all')
    {
        $this->db->reset_query();
        $this->db->select(array("invoice_no", "date"));
        $this->db->from($this->table);
        if ($state != 'all')
        {
            $this->db->where('state', $state);
        }
        $this->db->order_by("invoice_no");
        $temp = $this->db->get()->result();

        $result = array();
        foreach ($temp as $row)
        {
            $result[$row->invoice_no] = "#" . $row->invoice_no . " in " . $row->date;
        }
        return $result;
    }

}

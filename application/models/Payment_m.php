<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_m extends My_Model
{

    public $fields = array(
        'account_id' => array(
            'label' => 'Account',
            'rules' => array('required')
        ),
        'date' => array(
            'label' => 'Payment Date',
            'type' => 'datetime',
            'rules' => array('required')
        ),
        'charge' => array(
            'label' => 'Charge',
            'type' => 'number',
            'rules' => array('required')
        ),
        'description' => array(
            'label' => 'Description',
            'type' => 'textarea',
            'rules' => array('required')
        ),
        'invoice_id' => array(
            'label' => 'Invoice ID',
            'type' => 'number'
        ),

    );

    private function set_filter($search)
    {
        $this->db->reset_query();
        $this->db->from($this->table);
        if ($search != '')
        {
            $this->db->group_start();
            $this->db->or_like("site_id", $search);
            $this->db->or_like("site_name", $search);
            $this->db->or_like("participant", $search);
            $this->db->or_like("address", $search);
            $this->db->or_like("city", $search);
            $this->db->or_like("city_code", $search);
            $this->db->or_like("county_code", $search);
            $this->db->or_like("state", $search);
            $this->db->or_like("phone", $search);
            $this->db->or_like("c_store", $search);
            $this->db->or_like("zip", $search);
            $this->db->or_like("type", $search);
            $this->db->group_end();
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
                    case 0 :
                        $order_field = "site_id";
                        break;
                    case 1 :
                        $order_field = "site_name";
                        break;
                    case 2 :
                        $order_field = "participant";
                        break;
                    case 3 :
                        $order_field = "address";
                        break;
                    case 4 :
                        $order_field = "city";
                        break;
                    case 5 :
                        $order_field = "city_code";
                        break;
                    case 6 :
                        $order_field = "county_code";
                        break;
                    case 7 :
                        $order_field = "state";
                        break;
                    case 8 :
                        $order_field = "phone";
                        break;
                    case 9 :
                        $order_field = "c_store";
                        break;
                    case 10 :
                        $order_field = "zip";
                        break;
                    case 11 :
                        $order_field = "type";
                        break;
                }
                $this->db->order_by($order_field, $order['dir']);
            }
        } else
        {
            $this->db->order_by("site_id");
            $this->db->order_by("site_name");
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

    public function remove_alldata()
    {
        $this->db->empty_table($this->table);
    }

    public function get_all_sites()
    {
        $this->db->order_by('site_name');
        $results = $this->db->get($this->table)->result();
        $sites = array();
        foreach ($results as $result)
        {
            $sites[$result->site_id] = $result->site_name;
        }
        return $sites;
    }

    public function importdata($data)
    {
        $lists = $this->db->list_fields($this->table);
        $result = $this->get_data($data);
        if (!$result)
        {
            for ($i = 1; $i < count($lists); $i ++)
            {
                $this->db->set($lists[$i], $data[$i - 1]);
            }
            $this->db->insert($this->table);
        }
    }

    public function get_data($data)
    {
        $lists = $this->db->list_fields($this->table);
        for ($i = 1; $i < count($lists); $i ++)
        {
            $this->db->where($lists[$i], $data[$i - 1]);
        }
        $query = $this->db->get($this->table);
        $result = $query->result();
        return $result;
    }

}

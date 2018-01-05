<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends My_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->page_title = __("Accounts");
        $this->load->model("account_m");
        $this->load->model("card_m");
        $this->load->model("vehicle_m");
        $this->load->model("account_price_m");
        $this->load->model("department_m");

        if ($this->uri->rsegment(2) == 'add')
        {
            $this->page_title = __("Add New Client");
        } elseif ($this->uri->rsegment(2) == 'edit')
        {
            $this->account_m->get_by_id($this->uri->rsegment(3));
            $this->page_title = __("Edit") . ": #" . $this->account_m->id;
        }
    }

    public function index()
    {
        $this->load->library("my_bs_form");

        $this->load->view("accounts/list");
    }

    public function add()
    {
        if ($this->input->post('action') == 'process')
        {
            if ($this->account_m->form_validate($this->input->post()) === FALSE)
            {
                
            } else
            {
                if ($this->account_m->save())
                {
                    my_set_system_message(__("Successfully added new account data."), "success");
                    redirect("accounts/edit/" . $this->account_m->id);
                } else
                {
                    $this->account_m->add_error("id", __("Failed add account data."));
                }
            }
        }

        $this->load->view('accounts/edit', array(
            'account_m' => $this->account_m
        ));
    }

    public function edit()
    {
        if ($this->input->post('action') == 'process')
        {
            if ($this->account_m->form_validate($this->input->post()) === FALSE)
            {
                
            } else
            {
                if ($this->account_m->save())
                {
                    my_set_system_message(__("Successfully saved."), "success");

                    redirect("accounts/edit/" . $this->account_m->id);
                } else
                {
                    $this->account_m->add_error("id", __("Failed save."));
                }
            }
        }
        $this->load->view('accounts/edit', array(
            'account_m' => $this->account_m
        ));
    }

    public function ajax_delete()
    {
        $id = $this->uri->rsegment(3);
        $this->account_m->get_by_id($id);
        if ($this->account_m->is_exists())
        {
            $this->account_m->delete();
        }
    }

    public function ajax_find()
    {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $order = $this->input->post('order');
        $search = $this->input->post('search');
        $this->load->model('account_m');
        $result = $this->account_m->find($search['value'], $order, $start, $length);
        $accounts = array();

      $this->load->model("payment_m");

        if ($result['count'] > 0)
        {
            $account_status = $this->config->item("data_status");
            foreach ($result['data'] as $account)
            {
                $actions = array();
                $actions[] = array(
                    "label" => __("Edit"),
                    "url" => base_url("accounts/edit/" . $account->id),
                    "icon" => 'edit'
                );
                $actions[] = array(
                    "label" => __("Delete"),
                    "url" => 'javascript:delete_account(' . $account->id . ')',
                    "icon" => 'trash-o'
                );
                $account_results = $this->account_m->get_by_id($account->id);
                $account_id = $account_results[0]->account;
                $departments_count = $this->department_m->count_by_account_id($account_id);
                $cards_count = $this->card_m->count_by_account_id($account_id);
                $vehicles_count = $this->vehicle_m->count_by_account_id($account_id);
                $price_count = $this->account_price_m->count_by_account_id($account_id);

              $balance = '';
              $query = "SELECT `account_id`, SUM(charge) as 'charge', SUM(deposit) as 'deposit'";
              $query .= " FROM " . $this->payment_m->get_table();
              $query .= ' Where `account_id` = ' . $account_id;
              $query .= " GROUP BY `account_id`";
              $balance_result = $this->db->query($query)->result();

              if ($balance_result) {
                foreach ($balance_result as $row) {
                  $balance = '$'.number_format(round($row->deposit - $row->charge,2),2,'.',',');
                }
              } else {
                $balance = '$00.00';
              }

              $accounts[] = array(
                    "index" => ( ++$start),
//                    "account" => $account->account,
                    "name" => $account->account . " - " . $account->name,
                    "balance" => '<a id="'.$account_id.'" class="balance"  href="' . base_url('payments/checkout').'">' . $balance . '</a>',
                    "address" => $account->address1 . " " . $account->address2 . " " . $account->zip . "<br/>" . $account->city . ", " . $account->state,
                    "phone" => $account->phone,
                    "fax" => $account->fax,
                    "contact" => $account->contact,
                    "email" => $account->email,
                    "discount" => $account->discount . "%",
                    "cards" => '<a href="' . base_url('cards/cardlist/') . $account_id . '">' . $cards_count . '</a>',
                    "vehicles" => '<a href="' . base_url('vehicles/vehiclelist/') . $account_id . '">' . $vehicles_count . '</a>',
                    "departments" => '<a href="' . base_url('departments/departmentlist/') . $account_id . '">' . $departments_count . '</a>',
                    "account_price" => '<a href="' . base_url('prices/pricelist/') . $account_id . '">' . $price_count . '</a>',
                    "actions" => my_make_table_edit_icon(base_url('accounts/edit/' . $account->id))
                    . my_make_table_delete_icon('javascript:delete_account(' . $account->id . ')')
                );
            }
        }
        $returnData = array(
            "draw" => $draw,
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['count'],
            'data' => $accounts
        );

        header('Content-Type: application/json');
        echo json_encode($returnData);
    }

    public function remove_alldata()
    {
        $this->load->model('account_m');
        $this->load->model('card_m');
        $this->load->model('vehicle_m');
        $this->load->model('account_price_m');
        $this->account_m->remove_alldata();
        $this->card_m->remove_alldata();
        $this->vehicle_m->remove_alldata();
        $this->account_price_m->remove_alldata();
    }

}

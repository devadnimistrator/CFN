<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for PT data
 *
 *
 */
class Payments extends My_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->page_title = __("Payments Histories");

        $this->load->model("payment_m");

        if ($this->uri->rsegment(2) == 'checkout')
        {
            $this->page_title = __("New Checkout");
        } elseif ($this->uri->rsegment(2) == 'edit')
        {
            $this->payment_m->get_by_id($this->uri->rsegment(3));
            $this->page_title = __("Edit Payment") . ": #" . $this->payment_m->id;
        }
    }

    public function index()
    {
        $this->load->library("my_bs_form");

        $this->load->model('account_m');
        $this->load->view("payments/balances");
    }

    public function ajax_get_balances()
    {
        $this->load->model("account_m");

        $query = "SELECT a.`account`, a.`name` as account_name, MAX(`date`) AS 'lasted', SUM(charge) as 'charge', SUM(deposit) as 'deposit'";
        $query .= " FROM " . $this->payment_m->get_table() . " p JOIN " . $this->account_m->get_table() . " a ON p.account_id=a.`account`";
        $query .= " GROUP BY a.`account`";

        $result = $this->db->query($query)->result();
        $balances = array();
        $index = 1;
        foreach ($result as $row)
        {
            $balances[] = array(
                "index" => $index ++,
                "account_id" => $row->account,
                "account_name" => $row->account . " - " . $row->account_name,
                "lasted" => $row->lasted,
                "charge" => '$'.number_format(round($row->charge,2),2,'.',','),
                "deposit" => '$'.number_format(round($row->deposit,2),2,'.',','),
                "balance" => '$'.number_format(round($row->deposit - $row->charge,2),2,'.',','),
            );
        }
        header('Content-Type: application/json');
        echo json_encode(array("recordsTotal" => count($balances), "recordsFiltered" => count($balances), "data" => $balances));
    }

    public function ajax_get_histories()
    {
        $account_id = $this->input->post("account_id");
        $histories = [];
        if ($account_id)
        {
            $this->db->order_by('date', 'desc');
            $payments = $this->payment_m->get_by_account_id($account_id);
            if ($payments)
            {
                foreach ($payments as $payment)
                {
                    $description = $payment->description;
                    $actions = "";
                    if ($payment->invoice_id)
                    {
                        $description = '<a href="' . base_url("invoices/edit/" . $payment->invoice_id) . '">' . $description . '</a>';
                    } else
                    {
                        $actions = my_make_table_btn(base_url("payments/edit/" . $payment->id), "", "edit", "edit");
                        $actions .= my_make_table_btn('javascript:delete_payment(' . $payment->id . ')', "", "remove", "remove");
                    }


                    $histories[] = array(
                        "date" => $payment->date,
                        "charge" => '$'.number_format(round($payment->charge,2),2,'.',','),
                        "deposit" => '$'.number_format(round($payment->deposit,2),2,'.',','),
                        "description" => $description,
                        "actions" => $actions
                    );
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(array("recordsTotal" => count($histories), "recordsFiltered" => count($histories), "data" => $histories));
    }

    public function checkout()
    {
        $this->load->model("account_m");

        if (!$this->payment_m->is_exists())
        {
            $this->payment_m->date = date('Y-m-d');
        }

        if ($this->input->post('action') == 'process')
        {
            if ($this->payment_m->form_validate($this->input->post()) == FALSE)
            {
                
            } else
            {
                if ($this->payment_m->save())
                {
                    $this->payment_m->save();
                    my_set_system_message(__("Successfully new payment."), "success");

                    redirect("payments");
                } else
                {
                    $this->payment_m->add_error("id", __("Failed save payment."));
                }
            }
        }

        $this->load->view('payments/checkout');
    }

    public function edit()
    {
        $this->checkout();
    }

    public function ajax_delete()
    {
        $id = $this->uri->rsegment(3);
        $this->payment_m->get_by_id($id);
        if ($this->payment_m->is_exists())
        {
            $this->payment_m->delete();
        }
    }

}

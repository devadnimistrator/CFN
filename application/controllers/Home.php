<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends My_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->page_title = __("Home");
    }

    public function index()
    {
        $this->load->library("my_bs_form");

        $this->load->model("account_m");
        $accounts = $this->account_m->get_all_account(true);

        $this->load->view('home/dashboard', array('accounts' => $accounts));
    }

    public function ajax_get_uncompleted_balance()
    {
        $this->load->model("account_m");
        $this->load->model("payment_m");

        $query = "SELECT a.`account`, a.`name` as account_name, MAX(`date`) AS 'lasted', SUM(charge) as 'charge', SUM(deposit) as 'deposit'";
        $query .= " FROM " . $this->payment_m->get_table() . " p JOIN " . $this->account_m->get_table() . " a ON p.account_id=a.`account`";
        $query .= " GROUP BY a.`account`";

        $result = $this->db->query($query)->result();
        $balances = array();
        $index = 1;
        foreach ($result as $row)
        {
            if ($row->deposit - $row->charge > 0)
            {
                $balances[] = array(
                    "index" => $index ++,
                    "account_id" => $row->account,
                    "account_name" => '<a href="' . base_url("invoices/index/" . $row->account) . '">' . $row->account_name . '</a>',
                    "charge" => $row->charge,
                    "deposit" => $row->deposit,
                    "balance" => $row->deposit - $row->charge,
                    "lasted" => $row->lasted,
                );
            }
        }
        header('Content-Type: application/json');
        echo json_encode(array("recordsTotal" => count($balances), "recordsFiltered" => count($balances), "data" => $balances));
    }

}

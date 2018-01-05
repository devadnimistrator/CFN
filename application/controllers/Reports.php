<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->page_title = __("Reports");

  }

  public function index()
  {

    $this->transactions = $this->get_total_transactions();
    $this->load->view("reports/list");

  }

  public function view()
  {

    $month = $this->uri->rsegment(3);
    $this->page_title = $month . " " . __("Report");

    $h_t_data = $this->get_min_max_report($month);
    $this->load->view("reports/view", array(
      'month' => $month,
      'billing_count' => $h_t_data['billing_count'],
      'generated_number' => $h_t_data['generated_number'],
      'beginning_invoice_no' => $h_t_data['beginning_invoice_no'],
      'ending_invoice_no' => $h_t_data['ending_invoice_no'],
      'last_invoice_date' => $h_t_data['last_invoice_date'],
      'max_invoice' => $h_t_data['max_invoice'],
      'min_invoice' => $h_t_data['min_invoice'],
      'total_amount' => $h_t_data['total_amount'],
      'avg_amount' => $h_t_data['avg_amount']
    ));
  }

  public function export_print()
  {
    $month = $this->uri->rsegment(3);
    $print_date = $this->uri->rsegment(4);

    $report_data = array(
      "month" => $month,
      "print_date" => $print_date,
      "header" => $this->get_min_max_report($month),
      "data" => $this->get_invoices_by_month($month)
    );

    $this->load->view('reports/print', $report_data);
  }

  public function ajax_get_invoices_result()
  {

    $month = $this->uri->rsegment(3);
    $invoices_data = $this->get_invoices_by_month($month);
    $return_data = array();
    $index = 0;
    foreach ($invoices_data as $row) {
      $return_data[] = array(
        "index" => $index++,
        "account" => $row['account'],
        "name" => $row['name'],
        "date" => $row['date'],
        "invoice_no" => $row['invoice_no'],
        "invoice_amount" => '$' . $row['invoice_amount'],
        "quantity_sold" => $row['quantity_sold'],
        "new_charge" => '$' . $row['new_charge']
      );
    }

    header('Content-Type: application/json');
    echo json_encode(array("recordsTotal" => count($return_data), "recordsFiltered" => count($return_data), "data" => $return_data));

  }

  private function get_invoices_by_month($month)
  {
    $this->load->model("account_m");
    $this->load->model("invoice_m");
    $this->load->model("ptdata_m");
    $this->load->model("invoice_item_m");
    $this->load->model("payment_m");

    $results = array();
    $index = 0;

    $invoice_query = "SELECT a.`account`, a.`name`, i.`invoice_no`, i.`total_price`, i.`id` AS invoice_id, i.date";
    $invoice_query .= " FROM " . $this->account_m->get_table() . " a ";
    $invoice_query .= " LEFT JOIN (SELECT * FROM " . $this->invoice_m->get_table() . " WHERE `date` LIKE '" . $month . "%') i ON a.`account` = i.`account_id`";
    $invoice_query .= " ORDER BY a.account";

    $invoices = $this->db->query($invoice_query)->result();

    foreach ($invoices as $invoice) {

      $result = array();
      $result['account'] = str_pad($invoice->account, 6, "0", STR_PAD_LEFT);
      $result['name'] = $invoice->name;
      $result['date'] = $invoice->date;

      if ($invoice->invoice_no == null) {

        $result['invoice_no'] = '-- NONE --';
        $result['invoice_amount'] = number_format(0, 2,'.',',');
        $result['quantity_sold'] = number_format(0, 3,'.',',');
        $result['new_charge'] = number_format(0, 2,'.',',');
        $result['date'] = '0000-00-00';

      } else {
        $result['invoice_no'] = $invoice->invoice_no;

        $this->load->model('account_m');
        $discount_query = "SELECT discount FROM " . $this->account_m->get_table() . " WHERE account = " . $invoice->account;
        $discount_result = $this->db->query($discount_query)->result();
        if ($discount_result) {
          $discount = $discount_result[0]->discount;
        }

        if ($discount != 0) {
          $result['invoice_amount'] = number_format(($invoice->total_price) * (100 - $discount)/100, 2,'.',',');
        } else {
          $result['invoice_amount'] = number_format($invoice->total_price, 2,'.',',');
        }

        $quantity_query = "SELECT SUM(p.quantity) as quantity";
        $quantity_query .= " FROM " . $this->invoice_item_m->get_table() . " i";
        $quantity_query .= " JOIN " . $this->ptdata_m->get_table() . " p ON i.ptdata_id = p.id";
        $quantity_query .= " WHERE i.invoice_id = " . $invoice->invoice_id;
        $quantity_query .= " GROUP BY invoice_id";

        $quantity = $this->db->query($quantity_query)->result_array();

        $result['quantity_sold'] = number_format($quantity[0]['quantity'], 3,'.',',');

        $previous_balance = 0;
        $previous_balance_query = "SELECT a.`account`, a.`name` AS account_name, MAX(`date`) AS lasted, SUM(charge) AS charge, SUM(deposit) AS deposit";
        $previous_balance_query .= " FROM " . $this->payment_m->get_table() . " p";
        $previous_balance_query .= " JOIN " . $this->account_m->get_table() . " a ON p.account_id = a.`account` ";
        $previous_balance_query .= " WHERE a.`account` = " . $invoice->account . " AND p.date < '" . $invoice->date . "'";
        $previous_balance_query .= " GROUP BY a.`account`";
        $previous_balance_result = $this->db->query($previous_balance_query)->result_array();
        if ($previous_balance_result) {
          $previous_balance = $previous_balance_result[0]['deposit'] - $previous_balance_result[0]['charge'];
        }

        if ($discount != 0) {
          $result['new_charge'] = number_format(($previous_balance + $invoice->total_price) * (100 - $discount)/100, 2,'.',',');
        } else {
          $result['new_charge'] = number_format($previous_balance + $invoice->total_price, 2,'.',',');
        }

      }
      $results[$index] = $result;
      $index++;
    }

    return $results;
  }

  public function ajax_get_reports()
  {

    $reports_data = $this->get_reports_data();

    $reports = array();
    $index = 1;
    foreach ($reports_data as $row) {
      $reports[] = array(
        "index" => $index++,
//        "date" => '<input type="text" name="print_date" value="'. date("Y-m-01") .'" class="date-picker" style="width: 80px"/>',
        "month" => $row->month,
        "start_invoice_id" => $row->start_invoice_no,
        "end_invoice_id" => $row->end_invoice_no,
        "total_amount" => '$' . number_format($row->total_amount, 2, '.', ','),
        "average_amount" => '$' . number_format($row->avg_amount, 2, '.', ','),
        "generated_number" => $row->generated_number,
        "transactions" => $row->generated_number,
        "actions" => my_make_table_btn(base_url("reports/view/" . $row->month), "View", "primary", "file-o"),
      );
      $index++;
    }
    header('Content-Type: application/json');
    echo json_encode(array("recordsTotal" => count($reports), "recordsFiltered" => count($reports), "data" => $reports));
  }

  private function get_reports_data()
  {
    $this->load->model("invoice_m");
    $query = "SELECT DATE_FORMAT(i.date, '%Y-%m') AS month, min(i.invoice_no) AS start_invoice_no, max(i.invoice_no) AS end_invoice_no, sum(i.total_price) as total_amount, count(i.invoice_no) as generated_number, sum(i.total_price)/count(i.invoice_no) as avg_amount";
    $query .= " FROM " . $this->invoice_m->get_table() . " AS i";
    $query .= " GROUP BY DATE_FORMAT(i.date, '%Y-%m')";
    $result = $this->db->query($query)->result();
    return $result;
  }

  private function get_min_max_report($month)
  {

    $this->load->model('invoice_m');
    $this->load->model('account_m');

    $result = array();

    $billing_query = "SELECT COUNT(account) AS billing_count";
    $billing_query .= " FROM " . $this->account_m->get_table() . " AS a";
    $billing_query .= " LEFT JOIN (SELECT invoice_no, account_id FROM " . $this->invoice_m->get_table() . " WHERE `date` LIKE '" . $month . "%') as i on a.account = i.account_id";
    $billing_result = $this->db->query($billing_query)->result_array();

    $result['billing_count'] = $billing_result[0]['billing_count'];

    $count_invoice_query = "SELECT COUNT(invoice_no) AS generated_number, MIN(invoice_no) AS beginning_invoice_no, MAX(invoice_no) AS ending_invoice_no, MAX(DATE) AS last_invoice_date";
    $count_invoice_query .= " FROM " . $this->invoice_m->get_table();
    $count_invoice_query .= " WHERE `date` LIKE '" . $month . "%'";
    $count_invoice_result = $this->db->query($count_invoice_query)->result_array();

    $result['generated_number'] = $count_invoice_result[0]['generated_number'];
    $result['beginning_invoice_no'] = $count_invoice_result[0]['beginning_invoice_no'];
    $result['ending_invoice_no'] = $count_invoice_result[0]['ending_invoice_no'];
    $result['last_invoice_date'] = $count_invoice_result[0]['last_invoice_date'];

    $max_amount_query = "SELECT MAX(i.total_price) AS max_amount FROM " . $this->invoice_m->get_table() . " AS i WHERE date LIKE '2017-10%'";

    $max_amount = 0;
    if ($this->db->query($max_amount_query)->result_array()) {
      $max_amount = $this->db->query($max_amount_query)->result_array();
    }

    $max_invoice_query = "SELECT a.account, a.name, i.invoice_no, i.date, i.total_price ";
    $max_invoice_query .= " FROM " . $this->invoice_m->get_table() . " AS i ";
    $max_invoice_query .= " LEFT JOIN " . $this->account_m->get_table() . " AS a on i.account_id = a.account ";
    $max_invoice_query .= " WHERE i.total_price = " . $max_amount[0]['max_amount'];

    $max_invoice_result = $this->db->query($max_invoice_query)->result_array();

    $result['max_invoice'] = $max_invoice_result[0];

    $min_amount_query = "SELECT MIN(i.`total_price`) AS min_amount FROM `" . $this->invoice_m->get_table() . "` AS i WHERE `date` LIKE '2017-10%'";
    $min_amount = 0;
    if ($this->db->query($min_amount_query)->result_array()) {
      $min_amount = $this->db->query($min_amount_query)->result_array();
    }

    $min_invoice_query = "SELECT a.account, a.name, i.invoice_no, i.date, i.total_price";
    $min_invoice_query .= " FROM " . $this->invoice_m->get_table() . " AS i";
    $min_invoice_query .= " LEFT JOIN " . $this->account_m->get_table() . " AS a on i.account_id = a.account";
    $min_invoice_query .= " WHERE i.total_price = " . $min_amount[0]['min_amount'];
    $min_invoice_result = $this->db->query($min_invoice_query)->result_array();

    $result['min_invoice'] = $min_invoice_result[0];

    $total_amount_query = "SELECT SUM(total_price) as total_amount";
    $total_amount_query .= " FROM " . $this->invoice_m->get_table();
    $total_amount_query .= " WHERE `date` LIKE '" . $month . "%'";
    $total_amount_result = $this->db->query($total_amount_query)->result_array();

    $result['total_amount'] = number_format($total_amount_result[0]['total_amount'], 2, '.', ',');

    $avg_amount = $total_amount_result[0]['total_amount'] / $result['generated_number'];

    $result['avg_amount'] = number_format($avg_amount, 2, '.', ',');

    return $result;
  }

  private function get_total_transactions () {
    $this->load->model("invoice_m");
    $year = date("Y");
    $query = "SELECT count(invoice_no) AS transactions FROM " . $this->invoice_m->get_table() . " WHERE date LIKE '" . $year . "%'";
    $transactions = $this->db->query($query)->result_array();
    return $transactions[0]['transactions'];
  }

}
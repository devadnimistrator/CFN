<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//ini_set('max_execution_time', 300);
//ini_set('memory_limit','1024M');

class Invoices extends My_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->page_title = __("Invoice List");

        $this->load->model("invoice_m");
        $this->load->model("account_m");
        if ($this->uri->rsegment(2) == 'add')
        {
            $this->page_title = __("New Invoice");
        } elseif ($this->uri->rsegment(2) == 'edit')
        {
            $this->invoice_m->get_by_id($this->uri->rsegment(3));
            $this->page_title = __("Edit Invoice") . " #" . $this->invoice_m->invoice_no;
        }
    }

    public function index()
    {
        $this->load->library("my_bs_form");

        $accounts = $this->account_m->get_all_account(true);
        $states = array(
//            'all' => 'All States',
            'Pending' => 'Pending',
            'Completed' => 'Completed'
        );
        $this->load->view("invoices/list", array(
            "accounts" => $accounts,
            "states" => $states,
            'default_account_id' => $this->uri->rsegment(3)
        ));
    }

    public function add()
    {
        $this->load->library("my_bs_form");
        $this->accounts = $this->account_m->get_all_account(true);

        $_date = my_get_invoice_between_date();
        $this->invoice_m->date = date('Y-m-d');
        $this->invoice_m->start_pt_date = $_date->start_date;
        $this->invoice_m->end_pt_date = $_date->end_date;
        $this->invoice_m->calc_method = DEFAULT_INVOICE_CALC_METHOD;
        $account = $this->input->post('account_id');
        if ($this->input->post('action') == 'new')
        {
            $this->invoice_m->form_validate($this->input->post());
        } elseif ($this->input->post('action') == 'process')
        {

            $year = date('Y');
            $this->db->where("date like '{$year}-%'");
            $this->db->select_max("invoice_no", "new_invoice_no");
            $new_invoice_no = $this->db->get($this->invoice_m->table)->row()->new_invoice_no;
            $new_invoice_no = $new_invoice_no ? $new_invoice_no + 1 : $year . "0001";

            if ($this->invoice_m->form_validate($this->input->post()) == FALSE)
            {
                
            } else
            {
                if ($account == "all")
                {
                    $start_pt_date = $this->input->post('start_pt_date');
                    $end_pt_date = $this->input->post('end_pt_date');
                    $datetime = $this->input->post('date');
                    $calc_method = $this->input->post('calc_method');
                    $result = $this->create_invoice_by_all_account($new_invoice_no, $start_pt_date, $end_pt_date, $datetime, $calc_method);

                    if ($result)
                    {
                        redirect("invoices");
                    } else
                    {
                        $this->invoice_m->add_error("id", __("Failed create new Invoice."));
                    }
                } else
                {

                    $this->load->model('ptdata_m');
                    $this->load->model('account_price_m');
                    $this->load->model('payment_m');

                    $this->invoice_m->invoice_no = $new_invoice_no;
                    $this->invoice_m->state = 'Pending';
                    $this->invoice_m->id = 0;
                    if ($this->invoice_m->save())
                    {
                        $this->invoice_m->save();

                        $configs = array(
                            'DEFAULT_INVOICE_CALC_METHOD' => $this->invoice_m->calc_method
                        );
                        $this->config_m->set_config($configs);

                        $ptdata_ids = $this->input->post("ptdata_ids");

                        $total_price = 0;
                        if ($ptdata_ids != '')
                        {
                            $ptdata_ids = explode(",", $ptdata_ids);
                            $this->load->model("invoice_item_m");
                            for ($i = 0; $i < count($ptdata_ids); $i++)
                            {
                                $this->invoice_item_m->id = 0;
                                $this->invoice_item_m->invoice_id = $this->invoice_m->id;
                                $this->invoice_item_m->ptdata_id = $ptdata_ids[$i];
                                $this->invoice_item_m->save();

                                $this->db->from($this->ptdata_m->table);
                                $this->db->where('id', $ptdata_ids[$i]);
                                $result = $this->db->get()->result();

                                if (count($result) > 0)
                                {
                                    foreach ($result as $row)
                                    {
                                        $differencial_price = 0;

                                        if ($row->site_type) {
                                          $differencial_price = $this->account_price_m->get_price_by_sitetype($account, $row->site_type);
                                        }

                                        if (DEFAULT_INVOICE_CALC_METHOD == 'pump')
                                        {
                                            $rate = ($row->pump_price) + $differencial_price;
                                        } else
                                        {
                                            $rate = ($row->cfn_price) + $differencial_price;
                                        }
                                        $sale = ($row->quantity) * $rate;
                                        $total_price += $sale;
                                    }
                                }
                            }
                        }

                        $this->payment_m->id = 0;
                        $this->payment_m->account_id = $account;
                        $this->payment_m->date = $this->invoice_m->date;
                        $this->payment_m->deposit = $total_price;
                        $this->payment_m->description = "Invoice #" . $new_invoice_no;
                        $this->payment_m->invoice_id = $this->invoice_m->id;
                        $this->payment_m->save();

                        my_set_system_message(__("Successfully create new invoice."), "success");
                        redirect("invoices");
                    } else
                    {
                        $this->invoice_m->add_error("id", __("Failed create new invoice."));
                    }
                }
            }
        }

        $accounts = $this->account_m->get_all_account(true);

        $this->load->view('invoices/add', array(
            'accounts' => $accounts
        ));
    }

    public function create_invoice_by_all_account($new_invoice_no, $start_pt_date, $end_pt_date, $datetime, $calc_method)
    {
        $this->load->model("ptdata_m");
        $this->load->model("card_m");
        $this->load->model("account_price_m");
        $this->load->model("account_m");
        $this->load->model("invoice_item_m");
        $this->load->model("invoice_m");
        $this->load->model("payment_m");

        $this->load->model("company_m");
        $default_company = $this->company_m->get_default();

        $accounts = $this->account_m->get_all_account(false);
        foreach ($accounts as $account_id => $value)
        {
            $_start_pt_date = $start_pt_date;
            $last_invoice_date = $this->get_last_invoice_date($account_id);
//            if ($_start_pt_date <= $last_invoice_date)
//            {
//                $_start_pt_date = my_add_date(1, $last_invoice_date);
//            }

            $total_price = 0;
            $this->db->from($this->ptdata_m->table);
            if ($default_company && $account_id == $default_company->client_consolidation)
            {
                $this->db->where("company_id != '" . $default_company->company_id . "'");
            } else
            {
                $this->db->where("company_id = '" . $default_company->company_id . "'");
                $this->db->where("card_id IN (select card_id from " . $this->card_m->get_table() . " where account_id='" . $account_id . "')");
            }
            $this->db->where("date_completed between '{$_start_pt_date}' AND '{$end_pt_date}'");

            $result = $this->db->get()->result();

            $this->load->model('product_m');
            $invoice_item_ids = array();
            if (count($result) > 0)
            {
                foreach ($result as $row)
                {
                    $invoice_item_ids[] = $row->id;
                    $differencial_price = 0;

                    if ($row->site_type) {
                      $differencial_price = $this->account_price_m->get_price_by_sitetype($account_id, $row->site_type);
                    }

                    if ($calc_method == 'pump')
                    {
                        $rate = ($row->pump_price) + $differencial_price;
                    } else
                    {
                        $rate = ($row->cfn_price) + $differencial_price;
                    }

                    $product_result = $this->product_m->get_by_id($row->product_id);
                    if ($product_result[0]->over_price != 0) {
                      $rate = $rate + $product_result[0]->over_price;
                    }

                    $sale = ($row->quantity) * $rate;
                    $total_price += $sale;
                }

                $this->invoice_m->id = 0;
                $this->invoice_m->invoice_no = $new_invoice_no;
                $this->invoice_m->date = $datetime;
                $this->invoice_m->account_id = $account_id;
                $this->invoice_m->start_pt_date = $_start_pt_date;
                $this->invoice_m->end_pt_date = $end_pt_date;
                $this->invoice_m->total_price = $total_price;
                $this->invoice_m->calc_method = DEFAULT_INVOICE_CALC_METHOD;
                $this->invoice_m->state = "Pending";
                $this->invoice_m->save();

                for ($i = 0; $i < count($invoice_item_ids); $i++)
                {
                    $this->invoice_item_m->id = 0;
                    $this->invoice_item_m->invoice_id = $this->invoice_m->id;
                    $this->invoice_item_m->ptdata_id = $invoice_item_ids[$i];
                    $this->invoice_item_m->save();
                }

                $this->payment_m->id = 0;
                $this->payment_m->account_id = $account_id;
                $this->payment_m->date = $datetime;
                $this->payment_m->deposit = $total_price;
                $this->payment_m->description = "Invoice #" . $new_invoice_no;
                $this->payment_m->invoice_id = $this->invoice_m->id;
                $this->payment_m->save();

                $new_invoice_no++;
            }
        }

        return true;
    }

    private function get_last_invoice_date($account_id)
    {
        $this->db->select_max('end_pt_date', 'last_pt_date');
        $this->db->from($this->invoice_m->table);
        $this->db->where("account_id", $account_id);
        return $this->db->get()->row()->last_pt_date;
    }

    public function edit()
    {
        $this->load->model('ptdata_m');
        $this->load->model('invoice_item_m');
        $this->load->model('account_m');
        $this->load->library("my_bs_form");

        $account_id = $this->invoice_m->account_id;
        $accoun_info = $this->account_m->get_by_account($account_id);
        $name = $this->account_m->name;

        if ($this->input->post('action') == 'process')
        {
            if ($this->invoice_m->form_validate($this->input->post()) == FALSE)
            {
                
            } else
            {
                $invoice_id = $this->input->post('invoice_id');
                $new_invoice_no = $this->input->post('invoice_no');
                $this->invoice_m->get_by_id($invoice_id);
                $previous_invoice_no = $this->invoice_m->invoice_no;

                if ($previous_invoice_no != $this->input->post('invoice_no') && ($this->invoice_m->get_by_invoice_no($new_invoice_no)))
                {
                    my_set_system_message("This invoice number already exist!", "danger");
                    redirect("invoices/edit/" . $invoice_id);
                } else
                {

                    $this->invoice_m->get_by_id($invoice_id);

                    $this->invoice_m->invoice_no = $this->input->post('invoice_no');
                    $this->invoice_m->date = $this->input->post('date');
                    $this->invoice_m->account_id = $this->input->post('account_id');
                    $this->invoice_m->start_pt_date = $this->input->post('start_pt_date');
                    $this->invoice_m->end_pt_date = $this->input->post('end_pt_date');
                    $this->invoice_m->total_price = $this->input->post('total_price');
                    $this->invoice_m->calc_method = $this->input->post('calc_method');
                    $this->invoice_m->state = "Pending";
                    $this->invoice_m->save();

                    if ($this->invoice_m->save())
                    {

                        $this->load->model("payment_m");
                        $this->payment_m->get_by_invoice_id($invoice_id);

                        $this->payment_m->invoice_id = $this->invoice_m->id;
                        $this->payment_m->deposit = $this->invoice_m->total_price;
                        $this->payment_m->account_id = $this->input->post('account_id');
                        $this->payment_m->date = $this->input->post('date');
                        $this->payment_m->description = "Invoice #" . $new_invoice_no;
                        $this->payment_m->save();

                        $this->invoice_item_m->delete_invoice_items($this->invoice_m->id);

                        $ptdata_ids = $this->input->post("ptdata_ids");
                        if ($ptdata_ids != '')
                        {
                            $ptdata_ids = explode(",", $ptdata_ids);
                            $this->load->model("invoice_item_m");
                            for ($i = 0; $i < count($ptdata_ids); $i++)
                            {
                                $this->invoice_item_m->id = 0;
                                $this->invoice_item_m->invoice_id = $this->invoice_m->id;
                                $this->invoice_item_m->ptdata_id = $ptdata_ids[$i];
                                $this->invoice_item_m->save();
                            }
                        }

                        my_set_system_message(__("Successfully save invoice."), "success");
                        redirect("invoices");
                    } else
                    {
                        $this->invoice_m->add_error("id", __("Failed save invoice."));
                    }
                }
            }
        }


        $this->invoice_m->date = date('Y-m-d');
        $this->invoice_m->start_pt_date = $this->invoice_m->start_pt_date;
        $this->invoice_m->end_pt_date = $this->invoice_m->end_pt_date;
        $this->invoice_id = $this->invoice_m->id;

        $this->load->view('invoices/edit', array(
            'account' => $name,
            'account_id' => $account_id,
            'invoice_id' => $this->invoice_m->id
        ));
    }

    public function ajax_delete()
    {
        $id = $this->uri->rsegment(3);
        $this->invoice_m->get_by_id($id);
        if ($this->invoice_m->is_exists())
        {
            $this->load->model("invoice_item_m");
            $this->invoice_item_m->delete_by_invoice_id($id);

            $this->load->model("payment_m");

            $this->payment_m->delete_by_invoice_id($id);

            $this->invoice_m->delete();
        }
    }

    public function ajax_find()
    {
        //$status = $this->uri->rsegment(3);
        $search_params = array();
        $search_params['account_id'] = $this->input->post('account_id');
        $search_params['start_date'] = $this->input->post('start_date');
        $search_params['end_date'] = $this->input->post('end_date');
        $search_params['state'] = $this->input->post('state');

        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $order = $this->input->post('order');
        $search = $this->input->post('search');
        $result = $this->invoice_m->find($search_params, $order, $start, $length);
        $invoices = array();
        if ($result['count'] > 0)
        {
            $invoice_status = $this->config->item("data_status");
            foreach ($result['data'] as $invoice)
            {
                $this->account_m->get_by_account($invoice->account_id);

                $actions = "";
                if ($invoice->state != 'Completed')
                {
                    $actions = my_make_table_btn(base_url("invoices/complete/" . $invoice->invoice_no), "Complete", "primary", "check")
                            . my_make_table_edit_btn(base_url('invoices/edit/' . $invoice->id))
                            . my_make_table_delete_btn('javascript:delete_invoice(' . $invoice->id . ')');
                }

                $invoices[] = array(
                    "index" => ( ++$start),
                    "invoice_no" => $invoice->invoice_no,
                    "date" => $invoice->date,
                    "transaction" => "Invoice of <b>" . $invoice->account_id . " - " . $this->account_m->name . "</b> from <b>" . $invoice->start_pt_date . "</b> to <b>" . $invoice->end_pt_date . '</b>',
                    "invoice" => '<a href="invoices/export_pdf/' . $invoice->id . '" class="btn btn-default btn-xs" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a>'
                    . '<a href="' . base_url('invoices/export_excel/' . $invoice->id) . '" class="btn btn-default btn-xs" title="Download EXCEL"><i class="fa fa-file-excel-o"></i></a>'
                    . '<a href="javascript:invoice_print(' . $invoice->id . ');" target="print_window" class="btn btn-default btn-xs" title="Print Invoice"><i class="fa fa-print"></i></a>',
                    "amount" => '$'.number_format($invoice->total_price, 2,'.',','),
                    "state" => $invoice->state,
                    "actions" => $actions,
                    "invoice_id" => $invoice->id,
                );
            }
        }

        $returnData = array(
            "draw" => $draw,
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['count'],
            'data' => $invoices
        );

        header('Content-Type: application/json');
        echo json_encode($returnData);
    }

    public function complete()
    {
        $this->invoice_m->get_by_invoice_no($this->uri->rsegment(3));
        $this->invoice_m->state = 'Completed';
        $this->invoice_m->save();

        my_set_system_message("Completed invoice #" . $this->invoice_m->invoice_no, "success");

        redirect("invoices");
    }

    public function ajax_get_new_pt_data()
    {
        $account_id = $this->input->post('account_id');
        $start_pt_date = $this->input->post('start_pt_date');
        $end_pt_date = $this->input->post('end_pt_date');
        $calc_method = $this->input->post('calc_method');

        $this->load->model("ptdata_m");
        $this->load->model("card_m");
        $this->load->model("account_price_m");
        $this->load->model("product_m");

        $result = array();

        $this->load->model("company_m");
        $default_company = $this->company_m->get_default();

        if ($account_id == "all")
        {
            $accounts = $this->account_m->get_all_account(false);

            $pt_data = array();
            $index = 0;

            foreach ($accounts as $account_id => $value)
            {
                $_start_pt_date = $start_pt_date;

                $last_invoice_date = $this->get_last_invoice_date($account_id);
//                if ($_start_pt_date <= $last_invoice_date)
//                {
//                    $_start_pt_date = my_add_date(1, $last_invoice_date);
//                }

                $this->db->from($this->ptdata_m->table);
                if ($default_company && $account_id == $default_company->client_consolidation)
                {
                    $this->db->where("company_id != '" . $default_company->company_id . "'");
                } else
                {
                    $this->db->where("company_id = '" . $default_company->company_id . "'");
                    $this->db->where("card_id IN (select card_id from " . $this->card_m->get_table() . " where account_id=" . $account_id . ")");
                }
                $this->db->where("date_completed between '" . $_start_pt_date . "' AND '" . $end_pt_date . "'");
                $this->db->order_by("date_completed");
                $this->db->order_by("time_completed");

                $result = $this->db->get()->result();
                $this->load->model('product_m');
                if (count($result) > 0)
                {
                    foreach ($result as $row)
                    {
                        $differencial_price = 0;
                        if ($row->site_type) {
                          $differencial_price = $this->account_price_m->get_price_by_sitetype($account_id, $row->site_type);
                        }

                        if ($calc_method == 'pump')
                        {
                            $rate = ($row->pump_price) + $differencial_price;
                        } else
                        {
                          $rate = ($row->cfn_price) + $differencial_price;
                        }

                        $product_result = $this->product_m->get_by_id($row->product_id);
                        if ($product_result[0]->over_price != 0) {
                          $rate = $rate + $product_result[0]->over_price;
                        }
                        $sale = ($row->quantity) * $rate;

                        $this->product_m->get_by_id($row->product_id);

                        $pt_item = array(
                            "index" => ++$index,
                            "account" => $value,
                            "date" => $row->date_completed,
                            "time" => $row->time_completed,
                            "product" => $this->product_m->product_code . ": " . $this->product_m->product_description,
                            "qty" => $row->quantity,
                            "rate" => $rate,
                            "sale" => number_format($sale, 2,'.',','),
                            "site_id" => $row->site_id,
                            "driver" => $row->card_id,
                            "odom" => $row->odometer,
                            "actions" => my_make_table_edit_icon(base_url('ptdatas/edit/' . $row->id))
                            . my_make_table_delete_icon('javascript:delete_ptdata(' . $row->id . ')'),
                            "id" => $row->id
                        );

                        $pt_data[] = $pt_item;
                    }
                }
            }
        } else
        {
            $_start_pt_date = $start_pt_date;
            $last_invoice_date = $this->get_last_invoice_date($account_id);
//            if ($_start_pt_date <= $last_invoice_date)
//            {
//                $_start_pt_date = my_add_date(1, $last_invoice_date);
//            }

            $this->db->from($this->ptdata_m->table);
            if ($default_company && $account_id == $default_company->client_consolidation)
            {
                $this->db->where("company_id != '" . $default_company->company_id . "'");
            } else
            {
                $this->db->where("company_id = '" . $default_company->company_id . "'");
                $this->db->where("card_id IN (select card_id from " . $this->card_m->get_table() . " where account_id=" . $account_id . ")");
            }
            $this->db->where("date_completed between '{$_start_pt_date}' AND '{$end_pt_date}'");
            $result = $this->db->get()->result();

            $pt_data = array();
            $index = 0;
            if (count($result) > 0)
            {
                $this->account_m->get_by_account($account_id);

                foreach ($result as $row)
                {
                    $differencial_price = 0;
                    if ($row->site_type) {
                      $differencial_price = $this->account_price_m->get_price_by_sitetype($account_id, $row->site_type);
                    }

                    if ($calc_method == 'pump')
                    {
                        $rate = ($row->pump_price) + $differencial_price;
                    } else
                    {
                        $rate = ($row->cfn_price) + $differencial_price;
                    }
                    $sale = ($row->quantity) * $rate;

                    $this->product_m->get_by_id($row->product_id);
                    $pt_item = array(
                        "index" => ++$index,
                        "account" => $this->account_m->name,
                        "date" => $row->date_completed,
                        "time" => $row->time_completed,
                        "product" => $this->product_m->product_code . ": " . $this->product_m->product_description,
                        "qty" => $row->quantity,
                        "rate" => $rate,
                        "sale" => $sale,
                        "site_id" => $row->site_id,
                        "driver" => $row->card_id,
                        "odom" => $row->odometer,
                        "actions" => my_make_table_edit_icon(base_url('ptdatas/edit/' . $row->id))
                        . my_make_table_delete_icon('javascript:delete_ptdata(' . $row->id . ')'),
                        "id" => $row->id
                    );

                    $pt_data[] = $pt_item;
                }
            }
        }


        $returnData = array(
            'recordsTotal' => count($result),
            'data' => $pt_data
        );

        header('Content-Type: application/json');
        echo json_encode($returnData);
    }

    public function ajax_get_by_invoice()
    {
        $account_id = $this->input->post('account_id');
        $invoice_id = $this->input->post('invoice_id');
        $start_pt_date = $this->input->post('start_pt_date');
        $end_pt_date = $this->input->post('end_pt_date');
        $calc_method = $this->input->post('calc_method');


        $this->load->model("ptdata_m");
        $this->load->model("card_m");
        $this->load->model("account_price_m");
        $this->load->model("product_m");

        $this->load->model("company_m");
        $default_company = $this->company_m->get_default();

        $this->db->from($this->ptdata_m->table);
        if ($default_company && $account_id == $default_company->client_consolidation)
        {
            $this->db->where("company_id != '" . $default_company->company_id . "'");
        } else
        {
            $this->db->where("company_id = '" . $default_company->company_id . "'");
            $this->db->where("card_id IN (select card_id from " . $this->card_m->get_table() . " where account_id=" . $account_id . ")");
        }
        $this->db->where("date_completed between '{$start_pt_date}' AND '{$end_pt_date}'");
        $result = $this->db->get()->result();


        $pt_data = array();
        $index = 0;
        $total_price = 0;
        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $differencial_price = 0;
                if ($row->site_type) {
                  $differencial_price = $this->account_price_m->get_price_by_sitetype($account_id, $row->site_type);
                }

                if ($calc_method == 'pump')
                {
                    $rate = ($row->pump_price) + $differencial_price;
                } else
                {
                    $rate = ($row->cfn_price) + $differencial_price;
                }

                $this->product_m->get_by_id($row->product_id);
                $sale = ($row->quantity) * $rate;
                $total_price += $sale;
                $pt_item = array(
                    "index" => ++$index,
                    "date" => $row->date_completed,
                    "time" => $row->time_completed,
                    "product" => $this->product_m->product_code . ": " . $this->product_m->product_description,
                    "qty" => number_format($row->quantity,3,'.',','),
                    "rate" => number_format($rate,3,'.',','),
                    "sale" => number_format($sale,3,'.',','),
                    "site_id" => $row->site_id,
                    "driver" => $row->card_id,
                    "odom" => $row->odometer,
                    "actions" => my_make_table_edit_icon(base_url('ptdatas/edit/' . $row->id))
                    . my_make_table_delete_icon('javascript:delete_ptdata(' . $row->id . ')'),
                    "id" => $row->id,
                    "total_price" => '$'.number_format($total_price,3,'.',',')
                );

                $pt_data[] = $pt_item;
            }
        }

        $returnData = array(
            'recordsTotal' => count($result),
            'data' => $pt_data
        );

        header('Content-Type: application/json');
        echo json_encode($returnData);
    }

    public function export_pdf()
    {
        $invoice_id = $this->uri->rsegment(3);
        $invoice_info = $this->get_invoice_infos($invoice_id);
//        $this->load->view('invoices/pdf', array("invoice_info" => $invoice_info, "printable" => false));
//
        $this->load->library('pdf');
        $this->pdf->load_view("invoices/pdf", array(
            'invoice_info' => $invoice_info,
            'printable' => false
        ));
        $this->pdf->render();
        $this->pdf->stream("invoice_" . $invoice_info->header->invoice_no . ".pdf");
    }

    private function get_invoice_infos($invoice_id)
    {
        $this->load->model("account_m");
        $this->load->model("invoice_item_m");
        $this->load->model("ptdata_m");
        $this->load->model("card_m");
        $this->load->model("product_m");
        $this->load->model("account_price_m");
        $this->load->model("vehicle_m");
        $this->load->model("payment_m");

        $invoice_info = new stdClass();

        $this->invoice_m->get_by_id($invoice_id);

        $invoice_info->header = new stdClass();
        $invoice_info->items = new stdClass();
        $invoice_info->footer = new stdClass();

        $invoice_info->header->invoice_no = $this->invoice_m->invoice_no;
        $invoice_info->header->invoice_date = my_formart_date($this->invoice_m->date, 'm/d/y');

        $invoice_info->footer->total_price = $this->invoice_m->total_price;

        $this->account_m->get_by_account($this->invoice_m->account_id);
        $invoice_info->header->account_num = str_pad($this->account_m->account, 6, "0", STR_PAD_LEFT);
        $invoice_info->header->account_name = $this->account_m->name;
        $invoice_info->header->account_address = $this->account_m->address1 . " " . $this->account_m->address2;
        $invoice_info->header->account_city = $this->account_m->city;
        $invoice_info->header->account_state = $this->account_m->state;
        $invoice_info->header->account_zip = $this->account_m->zip;
        $invoice_info->header->account_phone = $this->account_m->phone;

        $invoice_items = $this->invoice_item_m->get_by_invoice_id($this->invoice_m->id);

        $invoice_info->items->vehicles = array();
        $invoice_info->items->products = array();
        $this->load->model('product_m');
        if ($invoice_items)
        {
            foreach ($invoice_items as $invoice_item)
            {
                // get PT data
                $this->ptdata_m->get_by_id($invoice_item->ptdata_id);
                // get rate and sale by prodcut
                $differencial_price = 0;
                if ($this->ptdata_m->site_type) {
                  $differencial_price = $this->account_price_m->get_price_by_sitetype($this->account_m->account, $this->ptdata_m->site_type);
                }

                $rate = 0;
                if ($this->invoice_m->calc_method == 'pump')
                {
                    $rate = ($this->ptdata_m->pump_price) + $differencial_price;
                } else
                {
                    $rate = ($this->ptdata_m->cfn_price) + $differencial_price;
                }

                $product_result = $this->product_m->get_by_id($this->ptdata_m->product_id);
                if ($product_result[0]->over_price != 0) {
                  $rate = $rate + $product_result[0]->over_price;
                }

                $sale = $this->ptdata_m->quantity * $rate;

                // Group by Product
                if (isset($invoice_info->items->products[$this->ptdata_m->product_id]))
                {
                    $invoice_info->items->products[$this->ptdata_m->product_id]->qty += $this->ptdata_m->quantity;
                    $invoice_info->items->products[$this->ptdata_m->product_id]->sale += $sale;
                } else
                {
                    $this->product_m->get_by_id($this->ptdata_m->product_id);
                    $invoice_info->items->products[$this->ptdata_m->product_id] = new stdClass();
                    $invoice_info->items->products[$this->ptdata_m->product_id]->name = $this->product_m->product_description;
                    $invoice_info->items->products[$this->ptdata_m->product_id]->qty = $this->ptdata_m->quantity;
                    $invoice_info->items->products[$this->ptdata_m->product_id]->sale = $sale;
                }

                // Group by Vehicle
                $this->db->reset_query();
                $this->db->where("account_id", $this->account_m->account);
                $this->vehicle_m->get_by_card_id($this->ptdata_m->card_id);

                $product = new stdClass();
                $product->date = my_formart_date($this->ptdata_m->date_completed, 'm/d/y');
                $product->time = $this->ptdata_m->time_completed;
                $product->name = $invoice_info->items->products[$this->ptdata_m->product_id]->name;
                $product->qty = $this->ptdata_m->quantity;
                $product->site_type = $this->ptdata_m->site_type;
                $product->rate = $rate;
                $product->sale = $sale;
                $product->site_numer = str_pad($this->ptdata_m->site_id, 4, "0", STR_PAD_LEFT);
                $product->pump_number = str_pad($this->ptdata_m->pump_number_or_register_number, 2, "0", STR_PAD_LEFT);
                $product->driver = $this->ptdata_m->card_id;
                $product->odom = $this->ptdata_m->odometer;

                if (isset($invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]))
                {
                    
                } else
                {
                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id] = new stdClass();
                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->vehicle_id = str_pad($this->vehicle_m->vehicle_id, 7, "0", STR_PAD_LEFT);
                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->vehicle_desc = $this->vehicle_m->vehicle_description;
                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->products = array();

                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->sub_qty = 0;
                    $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->sub_total = 0;
                }
                $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->products[] = $product;
                $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->sub_qty += $product->qty;
                $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->sub_total += $product->sale;
                ksort($invoice_info->items->vehicles, $invoice_info->items->vehicles[$this->vehicle_m->vehicle_id]->vehicle_id);
            }
        }

        $invoice_info->footer->before_balance = new stdClass();
        $invoice_date = $this->invoice_m->date;
        $before_invoice_date = $this->db->select_max("date", "lasted")
                        ->from($this->invoice_m->table)
                        ->where("account_id", $this->account_m->account)
                        ->where("date < '" . $invoice_date . "'")
                        ->get()->row()->lasted;

        if ($before_invoice_date)
        {
            $invoice_info->footer->before_balance->date = my_formart_date($before_invoice_date, 'm/d/y');
            $before_balance = my_get_previous_balance($this->account_m->account, $before_invoice_date);
            $invoice_info->footer->before_balance->balance = $before_balance->sum_deposit - $before_balance->sum_charge;
        } else
        {
            $before_invoice_date = '0000-00-00';
        }

        $payments = $this->db->from($this->payment_m->table)
                        ->where("`date` between '" . $before_invoice_date . "' and '" . $this->invoice_m->date . "'")
//                        ->where("`date` between '" . $before_invoice_date . "' and '" . date('Y-m-d H:i') . "'")
                        ->where("account_id", $this->account_m->account)
                        ->where("charge > 0")
                        ->order_by('date')
                        ->get()->result();
        if ($payments)
        {
            $invoice_info->footer->payment_histories = array();
            foreach ($payments as $payment)
            {
                $history = new stdClass();
                $history->date = my_formart_date($payment->date, 'm/d/y');
                $history->descirption = $payment->description;
                $history->amount = $payment->charge;

                $invoice_info->footer->payment_histories[] = $history;
            }
        }

        $this->load->model('pbalance_m');
        $invoice_info->footer->pbalance = $this->pbalance_m->get_by_account_id($this->account_m->account);

    /*get cdiscount start*/
    $this->load->model('account_m');
    $discount_query = "SELECT discount FROM " . $this->account_m->get_table() . " WHERE account = " . $this->account_m->account;
    $discount_result = $this->db->query($discount_query)->result();
    if ($discount_result) {
      $discount = $discount_result[0]->discount;
    }
    $invoice_info->footer->discount = $discount;
    /*discount end*/

      $last_payments = 0;
      $last_payments_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE account_id = " . $this->account_m->account;
      $result_last = $this->db->query($last_payments_query)->result();
      if ($result_last) {
        $last_payments = $result_last[0]->payments;
      }

      $before_90_payments = 0;
      $before_90_date = my_add_date(-90, $this->invoice_m->date, 'Y-m-d');
      $before_90_month = my_formart_date($before_90_date, 'Y-m');

      /*before 90 balance start*/
      $before_90_balance = 0;
      $before_90_balance_query = "SELECT SUM(charge) as payments, SUM(deposit) as deposits FROM ". $this->payment_m->get_table() ." WHERE DATE < '". $before_90_month ."%' AND account_id = ". $this->account_m->account;
      $before_90_balance_result = $this->db->query($before_90_balance_query)->result();
      if ($before_90_balance_result) {
        $before_90_balance = $before_90_balance_result[0]->deposits - $before_90_balance_result[0]->payments;
      }
      /*before 90 balance end*/
      $last_payments = $last_payments - $before_90_balance;


      $before_90_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_90_month . "%' AND account_id = " . $this->account_m->account;
      $result_90 = $this->db->query($before_90_query)->result();
      if ($result_90) {
        $before_90_payments = $result_90[0]->payments;
      }

      $before_90_calc = 0;
      $invoice_info->footer->before_90_payments = $before_90_payments;
      $invoice_info->footer->before_90_date = $before_90_date;
      $before_90_invoice_date = $this->get_before_invoice_date($before_90_date, $this->account_m->account);

      $before_90_charge = new stdClass();
      $before_90_charge->sum_deposit = 0;
      if ($before_90_invoice_date != '0000-00-00' && $before_90_invoice_date != null) {
        $before_90_charge = my_get_balance($this->account_m->account, $before_90_invoice_date);
      }

      if ($before_90_charge != null) {
        $before_90_calc = $before_90_charge->sum_deposit - $last_payments;
      } else {
        $before_90_calc = 0 - $last_payments;
      }

      $invoice_info->footer->before_90_balance = 0;
      if ($before_90_calc >= 0) {
        $invoice_info->footer->before_90_balance = $before_90_calc;
        $last_payments = 0;
      } else {
        $last_payments = (-1) * $before_90_calc;
      }

      $before_60_payments = 0;
      $before_60_date = my_add_date(-60, $this->invoice_m->date, 'Y-m-d');
      $before_60_month = my_formart_date($before_60_date, 'Y-m');
      $before_60_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_60_month . "%' AND account_id = " . $this->account_m->account;
      $result_60 = $this->db->query($before_60_query)->result();
      if ($result_60) {
        $before_60_payments = $result_60[0]->payments;
      }
      $before_60_calc = 0;
      $invoice_info->footer->before_60_payments = $before_60_payments;
      $invoice_info->footer->before_60_date = $before_60_date;
      $before_60_invoice_date = $this->get_before_invoice_date($before_60_date, $this->account_m->account);

      $before_60_charge = new stdClass();
      $before_60_charge->sum_deposit = 0;
      if ($before_60_invoice_date != '0000-00-00' && $before_60_invoice_date != null) {
        $before_60_charge = my_get_balance($this->account_m->account, $before_60_invoice_date);
      }

      if ($before_60_charge != null) {
        $before_60_calc = $before_60_charge->sum_deposit - $last_payments;
      } else {
        $before_60_calc = 0 - $last_payments;
      }

      $invoice_info->footer->before_60_balance = 0;
      if ($before_60_calc >= 0) {
        $invoice_info->footer->before_60_balance = $before_60_calc;
        $last_payments = 0;
      } else {
        $last_payments = (-1) * $before_60_calc;
      }

      $before_30_payments = 0;
      $before_30_date = my_add_date(-30, $this->invoice_m->date, 'Y-m-d');
      $before_30_month = my_formart_date($before_30_date, 'Y-m');
      $before_30_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_30_month . "%' AND account_id = " . $this->account_m->account;
      $result_30 = $this->db->query($before_30_query)->result();
      if ($result_30) {
        $before_30_payments = $result_30[0]->payments;
      }
      $before_30_calc = 0;
      $invoice_info->footer->before_30_payments = $before_30_payments;
      $invoice_info->footer->before_30_date = $before_30_date;
      $before_30_invoice_date = $this->get_before_invoice_date($before_30_date, $this->account_m->account);

      $before_30_charge = new stdClass();
      $before_30_charge->sum_deposit = 0;
      if ($before_30_invoice_date != '0000-00-00' && $before_30_invoice_date != null) {
        $before_30_charge = my_get_balance($this->account_m->account, $before_30_date);
      }
      if ($before_30_charge !=null) {
        $before_30_calc = $before_30_charge->sum_deposit - $last_payments;
      } else {
        $before_30_calc = 0 - $before_30_payments;
      }

      $invoice_info->footer->before_30_balance = 0;
      if ($before_30_calc > 0) {
        $invoice_info->footer->before_30_balance = $before_30_calc;
        $last_payments = $before_30_calc;
      } else {
        $last_payments = (-1) * $before_30_calc;
      }

      $invoice_info->footer->current_balance = 0;
      $invoice_info->footer->current_balance = ($invoice_info->footer->total_price + $last_payments) * ((100 - $discount) / 100) ;

//        $invoice_info->footer->current_balance = my_get_balance($this->account_m->account, $this->invoice_m->date);
//        $invoice_info->footer->current_balance = my_get_balance($this->account_m->account, date('Y-m-d'));
/*
        $before_30_payments = 0;
        $before_30_date = my_add_date(-30, $this->invoice_m->date, 'Y-m-d');
        $before_30_month = my_formart_date($before_30_date, 'Y-m');
        $before_30_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_30_month . "%' AND account_id = " . $this->account_m->account;
        $result_30 = $this->db->query($before_30_query)->result();
        if ($result_30) {
          $before_30_payments = $result_30[0]->payments;
        }
        $invoice_info->footer->before_30_payments = $before_30_payments;
        $invoice_info->footer->before_30_date = $before_30_date;
        $invoice_info->footer->before_30_balance = my_get_balance($this->account_m->account, $before_30_date);

        $before_60_payments = 0;
        $before_60_date = my_add_date(-60, $this->invoice_m->date, 'Y-m-d');
        $before_60_month = my_formart_date($before_60_date, 'Y-m');
        $before_60_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_60_month . "%' AND account_id = " . $this->account_m->account;
        $result_60 = $this->db->query($before_60_query)->result();
        if ($result_60) {
          $before_60_payments = $result_60[0]->payments;
        }
        $invoice_info->footer->before_60_payments = $before_60_payments;
        $invoice_info->footer->before_60_date = $before_60_date;
        $invoice_info->footer->before_60_balance = my_get_balance($this->account_m->account, $before_60_date);

        $before_90_payments = 0;
        $before_90_date = my_add_date(-90, $this->invoice_m->date, 'Y-m-d');
        $before_90_month = my_formart_date($before_90_date, 'Y-m');
        $before_90_query = "SELECT SUM(charge) as payments FROM " . $this->payment_m->get_table() . " WHERE DATE LIKE '" . $before_90_month . "%' AND account_id = " . $this->account_m->account;
        $result_90 = $this->db->query($before_90_query)->result();
        if ($result_90) {
          $before_90_payments = $result_90[0]->payments;
        }
        $invoice_info->footer->before_90_payments = $before_90_payments;
        $invoice_info->footer->before_90_date = $before_90_date;
        $invoice_info->footer->before_90_balance = my_get_balance($this->account_m->account, $before_90_date);
*/
        return $invoice_info;
    }

    public function export_print()
    {
        $invoice_id = $this->uri->rsegment(3);
        $invoice_info = $this->get_invoice_infos($invoice_id);
        $this->load->view('invoices/pdf', array("invoice_info" => $invoice_info, "printable" => true));
    }

    public function export_excel()
    {
        $file = "invoice_" . date('ymd') . ".xlsx";

        $invoice_id = $this->uri->rsegment(3);
        $invoice_info = $this->get_invoice_infos($invoice_id);

        include_once APPPATH . 'third_party/phpoffice/PHPExcel.php';

        $objPHPExcel = PHPExcel_IOFactory::load(APPPATH . "invoice_temp.xlsx");
        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);

        // write header
        $activeSheet->setCellValueByColumnAndRow(0, 1, CONTACT_NAME);
        $activeSheet->setCellValueByColumnAndRow(0, 2, CONTACT_ADDRESS);
        $activeSheet->setCellValueByColumnAndRow(0, 3, CONTACT_CITY);
        $activeSheet->setCellValueByColumnAndRow(0, 4, CONTACT_PHONE);

        $activeSheet->setCellValueByColumnAndRow(1, 6, $invoice_info->header->account_num);
        $activeSheet->setCellValueByColumnAndRow(1, 7, $invoice_info->header->invoice_date);
        $activeSheet->setCellValueByColumnAndRow(10, 7, "No: " . $invoice_info->header->invoice_no);

        $activeSheet->setCellValueByColumnAndRow(1, 9, $invoice_info->header->account_name);
        $activeSheet->setCellValueByColumnAndRow(1, 10, $invoice_info->header->account_address);
        $activeSheet->setCellValueByColumnAndRow(1, 11, "No: " . $invoice_info->header->account_city);

        $leftStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
        );

        $vehicleTempRow = 19;
        foreach ($invoice_info->items->vehicles as $vehicle)
        {
            $activeSheet->insertNewRowBefore($vehicleTempRow, 3);
            $activeSheet->getRowDimension($vehicleTempRow - 1)->setRowHeight(10);

            $activeSheet->mergeCells("A" . $vehicleTempRow . ":J" . $vehicleTempRow);
            $activeSheet->getStyle("A" . $vehicleTempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $activeSheet->setCellValueExplicit("A" . $vehicleTempRow, "=====   VEHICLE #" . $vehicle->vehicle_id, PHPExcel_Cell_DataType::TYPE_STRING);

            $vehicleTempRow++;

            foreach ($vehicle->products as $product)
            {
                $activeSheet->insertNewRowBefore($vehicleTempRow, 1);

                $activeSheet->getStyle("A" . $vehicleTempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $activeSheet->setCellValueExplicit("A" . $vehicleTempRow, $product->date, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit("B" . $vehicleTempRow, $product->time, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValue("C" . $vehicleTempRow, $product->name);
                $activeSheet->setCellValue("D" . $vehicleTempRow, $product->qty);
                $activeSheet->setCellValue("E" . $vehicleTempRow, $product->rate);
                $activeSheet->setCellValue("F" . $vehicleTempRow, $product->sale);
                $activeSheet->setCellValueExplicit("G" . $vehicleTempRow, $product->site_numer, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit("H" . $vehicleTempRow, $product->pump_number, PHPExcel_Cell_DataType::TYPE_STRING);
              $activeSheet->setCellValueExplicit("I" . $vehicleTempRow, $product->site_type, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit("J" . $vehicleTempRow, $product->driver, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit("K" . $vehicleTempRow, $product->odom, PHPExcel_Cell_DataType::TYPE_STRING);

                $vehicleTempRow++;
            }

            $activeSheet->mergeCells("A" . $vehicleTempRow . ":C" . $vehicleTempRow);
            $activeSheet->getStyle("A" . $vehicleTempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $activeSheet->setCellValue("A" . $vehicleTempRow, "subtotal");
            $activeSheet->setCellValue("D" . $vehicleTempRow, $vehicle->sub_qty);
            $activeSheet->setCellValue("F" . $vehicleTempRow, $vehicle->sub_total);

            $vehicleTempRow += 2;
        }

        $activeSheet->setCellValue("F" . $vehicleTempRow, $invoice_info->footer->total_price);
        /*discount start*/
        if ($invoice_info->footer->discount != 0) {
          $activeSheet->setCellValue("H" . $vehicleTempRow, $invoice_info->header->invoice_date . "   " . $invoice_info->footer->discount . "% discount ");
          $activeSheet->setCellValue("K" . $vehicleTempRow, $invoice_info->footer->total_price * ($invoice_info->footer->discount/100));
        }
      /*discount end*/
        $vehicleTempRow += 2;

        if (isset($invoice_info->footer->before_balance->date))
        {
            $activeSheet->insertNewRowBefore($vehicleTempRow, 1);

            $activeSheet->getStyle("A" . $vehicleTempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $activeSheet->setCellValueExplicit("A" . $vehicleTempRow, $invoice_info->footer->before_balance->date, PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->setCellValue('B' . $vehicleTempRow, 'Previous Balance');
            $activeSheet->setCellValue('F' . $vehicleTempRow, $invoice_info->footer->before_balance->balance);

            $vehicleTempRow++;
        }

        if (isset($invoice_info->footer->payment_histories))
        {
            foreach ($invoice_info->footer->payment_histories as $history)
            {
                $activeSheet->insertNewRowBefore($vehicleTempRow, 1);

                $activeSheet->getStyle("A" . $vehicleTempRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $activeSheet->setCellValueExplicit("A" . $vehicleTempRow, $history->date, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->setCellValue('B' . $vehicleTempRow, 'Payment - Thank you - ' . $history->descirption);
                $activeSheet->setCellValue('F' . $vehicleTempRow, $history->amount);

                $vehicleTempRow++;
            }
        }

        $vehicleTempRow += 2;
        if (isset($invoice_info->items->products))
        {
            foreach ($invoice_info->items->products as $product)
            {
                $activeSheet->insertNewRowBefore($vehicleTempRow, 1);

                $activeSheet->setCellValue('A' . $vehicleTempRow, $product->name);
                $activeSheet->setCellValue('D' . $vehicleTempRow, $product->qty);
                $activeSheet->setCellValue('E' . $vehicleTempRow, $product->sale);

                $vehicleTempRow++;
            }
        }

        $vehicleTempRow++;

        $pbalance = 0;
        $pbalance_date = '0000-00-00';
        if ($invoice_info->footer->pbalance) {
          foreach ($invoice_info->footer->pbalance as $data) {
            $pbalance = $data->balance;
            $pbalance_date = $data->date;
          }
        }

//        if ($invoice_info->footer->before_30_date > $pbalance_date && $pbalance_date != '0000-00-00') {
//
//          $balances = "CURRENT = " . (number_format($invoice_info->footer->current_balance->sum_deposit - $invoice_info->footer->current_balance->sum_charge + $pbalance , 2));
//          $balances .= "          ";
//          $balances .= "30-DAYS = " . ($invoice_info->footer->before_30_balance ? number_format($invoice_info->footer->before_30_balance->sum_deposit - $invoice_info->footer->before_30_balance->sum_charge + $pbalance - $invoice_info->footer->before_30_payments, 2,'.',',') : number_format(0,2,'.',','));
//          $balances .= "          ";
//          $balances .= "60-DAYS = " . ($invoice_info->footer->before_60_balance ? number_format($invoice_info->footer->before_60_balance->sum_deposit - $invoice_info->footer->before_60_balance->sum_charge + $pbalance - $invoice_info->footer->before_60_payments, 2,'.',',') : number_format(0,2,'.',','));
//          $balances .= "          ";
//          $balances .= "90-DAYS = " . ($invoice_info->footer->before_90_balance ? number_format($invoice_info->footer->before_90_balance->sum_deposit - $invoice_info->footer->before_90_balance->sum_charge + $pbalance - $invoice_info->footer->before_90_payments, 2,'.',',') : number_format(0,2,'.',','));
//        }

//        $balances = "CURRENT = " . (number_format($invoice_info->footer->current_balance->sum_deposit - $invoice_info->footer->current_balance->sum_charge + $pbalance , 2));
//        $balances .= "          ";
//        $balances .= "30-DAYS = " . ($invoice_info->footer->before_30_balance ? number_format($invoice_info->footer->before_30_balance->sum_deposit - $invoice_info->footer->before_30_balance->sum_charge - $invoice_info->footer->before_30_payments, 2,'.',',') : number_format(0,2,'.',','));
//        $balances .= "          ";
//        $balances .= "60-DAYS = " . ($invoice_info->footer->before_60_balance ? number_format($invoice_info->footer->before_60_balance->sum_deposit - $invoice_info->footer->before_60_balance->sum_charge - $invoice_info->footer->before_60_payments, 2,'.',',') : number_format(0,2,'.',','));
//        $balances .= "          ";
//        $balances .= "90-DAYS = " . ($invoice_info->footer->before_90_balance ? number_format($invoice_info->footer->before_90_balance->sum_deposit - $invoice_info->footer->before_90_balance->sum_charge - $invoice_info->footer->before_90_payments, 2,'.',',') : number_format(0,2,'.',','));


      $balances = "CURRENT = " . (number_format($invoice_info->footer->current_balance, '2','.',','));
      $balances .= "          ";
      $balances .= "30-DAYS = " . (number_format($invoice_info->footer->before_30_balance,'2','.',','));
      $balances .= "          ";
      $balances .= "60-DAYS = " . (number_format($invoice_info->footer->before_60_balance,'2','.',','));
      $balances .= "          ";
      $balances .= "90-DAYS = " . (number_format($invoice_info->footer->before_90_balance,'2','.',','));



      $activeSheet->setCellValue('A' . $vehicleTempRow, $balances);

        $vehicleTempRow += 2;
      /*discount start*/
        if ($invoice_info->footer->discount != 0) {
          $activeSheet->setCellValue('A' . $vehicleTempRow, 'INVOICER AMOUNT   $ ' . number_format($invoice_info->footer->total_price * (100- $invoice_info->footer->discount)/100, 2));
        } else {
          $activeSheet->setCellValue('A' . $vehicleTempRow, 'INVOICER AMOUNT   $ ' . number_format($invoice_info->footer->total_price, 2));
        }
      /*discount end*/

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    private function get_invoice_items($invoice_id)
    {
        $this->load->model('ptdata_m');
        $this->load->model("card_m");
        $this->load->model("account_price_m");
        $this->load->model('invoice_m');
        $this->load->model('invoice_item_m');

        $this->invoice_m->get_by_id($invoice_id);
        $account_id = $this->invoice_m->account_id;
        $start_pt_date = $this->invoice_m->start_pt_date;
        $end_pt_date = $this->invoice_m->end_pt_date;

        $start_pt_date = my_formart_date($start_pt_date, 'ymd');
        $end_pt_date = my_formart_date($end_pt_date, 'ymd');

        $this->db->from($this->ptdata_m->table);
        $this->db->where("card_id IN (select card_id from " . $this->card_m->get_table() . " where account_id=" . $account_id . ")");
        $this->db->where("date_completed between " . $start_pt_date . " AND " . $end_pt_date);
        $result = $this->db->get()->result();

        $total_qty = 0;
        $total_sales = 0;
        $pt_data = array();
        $index = 0;
        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $differencial_price = 0;

                if ($row->site_type) {
                  $differencial_price = $this->account_price_m->get_price_by_sitetype($account_id, $row->site_type);
                }

                $rate = ($row->pump_price) + $differencial_price;
                $sale = ($row->quantity) * $rate;

                $pt_item = array(
                    "index" => ++$index,
                    "date" => $row->date_completed,
                    "time" => $row->time_completed,
                    "product" => $row->product_description,
                    "qty" => $row->quantity,
                    "rate" => $rate,
                    "sale" => $sale,
                    "site_id" => $row->site_id,
                    "driver" => $row->card_id,
                    "odom" => $row->odometer
                );

                $pt_data[] = $pt_item;
            }
        }

        return $pt_data;
    }

    private function get_before_invoice_date ($date, $account_id) {
      $this->load->model('invoice_m');
      $invoice_date = "0000-00-00";
      $query = "SELECT max(date) as invoice_date FROM ". $this->invoice_m->get_table() ."
                WHERE date <= '{$date}' and account_id = {$account_id}";
      $result = $this->db->query($query)->result();
      if ($result) {
        $invoice_date = $result[0]->invoice_date;
      }
      return $invoice_date;
    }

}

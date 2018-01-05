<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for PT data
 *
 *
 */
class Ptdatas extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->page_title = __("PT Data Info");

    $this->load->model("ptdata_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Data");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->ptdata_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Data") . ": #" . $this->ptdata_m->id;
    }
  }

  public function index()
  {
    $this->db->select_max('date_completed', 'completed');
    $result = $this->db->get($this->ptdata_m->table);
    $last_completed = $result->row()->completed;

    $this->load->library("my_bs_form");

    $this->load->view("ptdatas/list", array("last_completed" => $last_completed ? $last_completed : date('Y-m-d')));
  }

  public function add()
  {
    $this->load->model("site_m");
    $this->load->model("product_m");
    if ($this->input->post('action') == 'process') {
      if ($this->ptdata_m->form_validate($this->input->post()) == FALSE) {

      } else {
        if ($this->ptdata_m->save()) {
          $this->ptdata_m->save();
          my_set_system_message(__("Successfully added new PT data."), "success");

          redirect("ptdatas/edit/" . $this->ptdata_m->id);
        } else {
          $this->ptdata_m->add_error("id", __("Failed add PT data."));
        }
      }
    }

    $this->load->view('ptdatas/edit', array(
      'ptdata_m' => $this->ptdata_m
    ));
  }

  public function edit()
  {
    if ($this->input->post('action') == 'process') {
      if ($this->ptdata_m->form_validate($this->input->post()) === FALSE) {

      } else {
        if ($this->ptdata_m->save()) {

          my_set_system_message(__("Successfully saved student informations."), "success");

          redirect("ptdatas/edit/" . $this->ptdata_m->id);
        } else {
          $this->ptdata_m->add_error("id", __("Failed save cashier informations."));
        }
      }
    }

    $this->load->model("site_m");
    $this->load->model("product_m");
    $this->load->view('ptdatas/edit', array(
      'ptdata_m' => $this->ptdata_m
    ));
  }

  public function ajax_delete()
  {
    $id = $this->uri->rsegment(3);
    $this->ptdata_m->get_by_id($id);
    if ($this->ptdata_m->is_exists()) {
      $this->ptdata_m->delete();
    }
  }

  public function remove_alldata()
  {
    $date = $this->uri->rsegment(3);
    $this->ptdata_m->delete_by_date_completed($date);
  }


  public function ajax_find()
  {
    $date = $this->input->post('date');
    if ($date == '') {
      $date = date('Y-m-d');
    }
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');

    $result = $this->ptdata_m->find($date, $order[0], $start, $length);
    $ptdatas = array();

    $this->load->model('site_m');
    $this->load->model('product_m');

    if ($result['total'] > 0) {
      foreach ($result['data'] as $ptdata) {
        $actions = array();
        $actions[] = array(
          "label" => __("View"),
          "url" => 'javascript:view_all_data(' . $ptdata->id . ')',
          "icon" => 'list-alt'
        );
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("ptdatas/edit/" . $ptdata->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_data(' . $ptdata->id . ')',
          "icon" => 'trash-o'
        );

        $this->site_m->get_by_site_id($ptdata->site_id);
        $this->product_m->get_by_id($ptdata->product_id);

        $ptdatas[] = array(
          "index" => (++$start),
          "ptdata_id" => $ptdata->id,
          "sequence" => $ptdata->sequence,
//          "site" => $this->site_m->site_name,
          "site" => $this->site_m->site_id . '-' . $this->site_m->city . '-' . $this->site_m->state ,
          "product" => $this->product_m->product_code . ": " . $this->product_m->product_description,
          "card_id" => $ptdata->card_id,
          "company_id" => $ptdata->company_id,
          "total_amount" => $ptdata->total_amount,
          "price" => $ptdata->price,
          "completed" => $ptdata->date_completed . " " . $ptdata->time_completed,
          "actions" => my_make_table_edit_icon(base_url('ptdatas/edit/' . $ptdata->id))
            . my_make_table_delete_icon('javascript:delete_data(' . $ptdata->id . ')')
        );
      }
    }

    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['total'],
      'data' => $ptdatas
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  function import_csv()
  {
    $this->load->model('product_m');
    $this->load->model('site_m');

    $status = $this->uri->rsegment(3);
    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'csv';
    $config['max_size'] = 4048;
    $this->load->library('upload', $config);
    $pt_data_date = '';
    if (!$this->upload->do_upload('file')) {
      my_set_system_message($this->upload->display_errors(), "danger");
    } else {
      $f_data = array('upload_data' => $this->upload->data());
      $csv = $f_data['upload_data']['full_path'];
      $file_name = $f_data['upload_data']['client_name'];

      $row = 1;
      $unknown_site = '';
      $unknown_squence = '';
      if (($handle = fopen($csv, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          if (!$data[0]) continue;
          if ($data[0] == 0) continue;

          $num = count($handle);
          $row++;

          $this->db->reset_query();
          $this->product_m->get_by_product_code($data[5]);
          if ($this->product_m->is_exists()) {

          } else {
            $this->product_m->id = 0;
            $this->product_m->product_code = $data[5];
            $this->product_m->product_type = $data[6];
            $this->product_m->product_description = $data[7];
            $this->product_m->save();
          }

          $this->db->reset_query();
          $this->site_m->get_by_site_id($data[0]);
          if ($this->site_m->is_exists()) {

          } else {
            $unknown_site .= '#' . $data[0] . ', ';
            $unknown_squence .= '#' . $data[1] . ', ';
            $this->site_m->site_id = $data[0];
            $this->site_m->site_name = ' - CA';
            $this->site_m->participant = 'FLEETCOR TECHNOLOGIES, INC.';
            $this->site_m->address = 'Unknown';
            $this->site_m->city = 'Unknown';
            $this->site_m->city_code = 'Unknown';
            $this->site_m->county_code = 'Unknown';
            $this->site_m->state = 'CA';
            $this->site_m->phone = 0;
            $this->site_m->c_store = 0;
            $this->site_m->zip = 'Unknown';
            $this->site_m->type = 'N';
            $this->site_m->save();
          }

          if ($unknown_site) {
            my_set_system_message("Don't exist site info ( ". $unknown_site .") about squence ( " . $unknown_squence . ")  !", "danger");
          }

          $pt_day = "20" . substr($data[13], 0, 2) . "-" . substr($data[13], 2, 2) . "-" . substr($data[13], 4, 2);
          $temp = str_pad($data[14], 4, "0", STR_PAD_LEFT);
          $pt_time = substr($temp, 0, 2) . ":" . substr($temp, 2, 2);

          $pt_data_date = $pt_day;

          $card_id = substr($data[18], 0, 7);
          $company_id = substr($data[18], 7, 3);

          $this->db->reset_query();
          $this->db->from($this->ptdata_m->table);
          $this->db->where("site_id", $data[0]);
          $this->db->where("sequence", $data[1]);
          $this->db->where("transaction_number", $data[12]);
          $this->db->where("card_id", substr($data[18], 0, 7));
          $this->db->where("date_completed", $pt_day);
          $this->db->where("time_completed", $pt_time);
          $duplicat_result = $this->db->get()->result();;

          if ($duplicat_result) {

          } else {
            $this->ptdata_m->id = 0;
            $this->ptdata_m->site_id = $data[0];
            $this->ptdata_m->sequence = $data[1];
            $this->ptdata_m->status_code = $data[2];
            $this->ptdata_m->total_amount = $data[3];
            $this->ptdata_m->transaction_type = $data[4];
            $this->ptdata_m->product_id = $this->product_m->id;
            $this->ptdata_m->price = $data[8];
            $this->ptdata_m->quantity = $data[9];
            $this->ptdata_m->odometer = $data[10];
            $this->ptdata_m->pump_number_or_register_number = $data[11];
            $this->ptdata_m->transaction_number = $data[12];
            $this->ptdata_m->date_completed = $pt_day;
            $this->ptdata_m->time_completed = $pt_time;
            $this->ptdata_m->error_code = $data[15];
            $this->ptdata_m->authorization_number = $data[16];
            $this->ptdata_m->manual_entry = $data[17];
            $this->ptdata_m->card_id = $card_id;
            $this->ptdata_m->site_type = $data[41];
            $this->ptdata_m->pump_price = $data[44];
            $this->ptdata_m->cfn_price = $data[46];
            $this->ptdata_m->is_checked = 0;
            $this->ptdata_m->company_id = $company_id;

            $this->ptdata_m->save();
          }

        }
      }
      fclose($handle);
    }

//    redirect("ptdatas");
    $this->load->library("my_bs_form");
    $this->load->view("ptdatas/list", array("last_completed" => $pt_data_date));
  }

  function ajax_get_date () {
    $this->db->select('date_completed');
    $this->db->group_by('date_completed');
    $date_list = $this->db->get($this->ptdata_m->get_table())->result();
    $returnData = array();
    foreach ($date_list as $key => $date) {
      $returnData[$key]= $date->date_completed;
    }
    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}

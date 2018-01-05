<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sites extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("sites");
    $this->load->model("site_m");
    $this->load->model("product_m");
    $this->load->model('site_product_m');

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New site");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->site_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit") . ": #" . $this->site_m->id;
    }
  }

  public function index() {
    $status = $this->uri->rsegment(3);
    if ($status === null) {
      $status = 'all';
    } else {

    }
    $this->load->view("sites/list", array("status" => $status));
  }

  public function add() {
    if ($this->input->post('action') == 'process') {
      if ($this->site_m->form_validate($this->input->post()) === FALSE) {

      } else {
        if ($this->site_m->save()) {
          $this->site_m->save();
          my_set_system_message(__("Successfully added new site data."), "success");
          redirect("sites/edit/" . $this->site_m->id);
        } else {
          $this->site_m->add_error("id", __("Failed add site data."));
        }
      }
    }

    $this->load->view('sites/edit', array(
      'site_m' => $this->site_m
    ));
  }

  public function edit() {
    if ($this->input->post('action') == 'process') {
      if ($this->site_m->form_validate($this->input->post()) === FALSE) {

      } else {
        if ($this->site_m->save()) {
          my_set_system_message(__("Successfully saved."), "success");

          redirect("sites/edit/" . $this->site_m->id);
        } else {
          $this->site_m->add_error("id", __("Failed save."));
        }
      }
    }
    $this->load->view('sites/edit', array(
      'site_m' => $this->site_m
    ));
  }

  public function ajax_get() {
    $id = $this->uri->rsegment(3);
    $this->site_m->get_by_id($id);
    $site_data = array(
      "site" => $this->site_m->site,
      "site_id" => $this->site_m->site_id,
      "site_name" => $this->site_m->site_name,
      "participant" => $this->site_m->participant,
      "address" => $this->site_m->address,
      "city" => $this->site_m->city,
      "city_code" => $this->site_m->city_code,
      "county_code" => $this->site_m->county_code,
      "state" => $this->site_m->state,
      "phone" => $this->site_m->phone,
      "c_store" => $this->site_m->c_store,
      "zip" => $this->site_m->zip,
      "type" => $this->site_m->type
    );

    header('Content-Type: application/json');
    echo json_encode($site_data);
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->site_m->get_by_id($id);
    if ($this->site_m->is_exists()) {
      $this->site_m->delete();
    }
  }

  public function remove_alldata() {
    $this->site_m->remove_alldata();
  }

  public function ajax_find() {
    //$status = $this->uri->rsegment(3);
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->site_m->find($search['value'], $order, $start, $length);
    $sites = array();
    if ($result['count'] > 0) {
      $site_status = $this->config->item("data_status");
      foreach ($result['data'] as $site) {
        $actions = array();
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("sites/edit/" . $site->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_site(' . $site->id . ')',
          "icon" => 'trash-o'
        );

        $products_count = $this->site_product_m->count_by_site_id($site->id);

        $sites[] = array(
          "index" => ( ++$start),
          "site_id" => $site->site_id,
          "site_name" => $site->site_name,
          "participant" => $site->participant,
          "address" => $site->address,
          "city" => $site->city,
          "city_code" => $site->city_code,
          "county_code" => $site->county_code,
          "state" => $site->state,
          "phone" => $site->phone,
          "c_store" => $site->c_store,
          "zip" => $site->zip,
          "type" => $site->type,
          "products" =>$products_count,
          "actions" => my_make_table_edit_icon(base_url('sites/edit/' . $site->id))
            . my_make_table_delete_icon('javascript:delete_site(' . $site->id . ')')
//          "actions" => my_make_table_edit_btn(base_url('sites/edit/' . $site->id))
//            . my_make_table_delete_btn('javascript:delete_site(' . $site->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $sites
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  function Importdata() {
    $status = $this->uri->rsegment(3);

    $config = array();
    $config['upload_path']   = './uploads/';
    $config['allowed_types'] = 'csv';
    $config['max_size']      = 4048;

    $this->load->library('upload', $config);
    if ( !$this->upload->do_upload('file'))
    {
      $error = array('error' => $this->upload->display_errors());
      exit;
    }
    else
    {
      $f_data = array('upload_data' => $this->upload->data());
    }

    $csv = $f_data['upload_data']['full_path'];
    $row = 1;
    if (($handle = fopen($csv, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        if ($row != 1) {
          $this->site_m->importdata($data);
        }
        $row++;
      }
      fclose($handle);
    }

    $this->load->view("sites/list", array("status" => $status));
  }


}

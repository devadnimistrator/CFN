<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->page_title = __("Company");
    $this->load->model("company_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New company");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->company_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit") . ": #" . $this->company_m->id;
    }
  }

  public function index()
  {
    $status = $this->uri->rsegment(3);
    if ($status === null) {
      $status = 'all';
    } else {

    }
    $this->load->view("companies/list", array("status" => $status));
  }

  public function add()
  {
    if ($this->input->post('action') == 'process') {
      if ($this->company_m->form_validate($this->input->post()) === FALSE) {

      } else {
        if ($this->company_m->save()) {
          $this->company_m->save();
          my_set_system_message(__("Successfully added new company data."), "success");
          redirect("companies/edit/" . $this->company_m->id);
        } else {
          $this->company_m->add_error("id", __("Failed add company data."));
        }
      }
    }

    $this->load->view('companies/edit', array(
      'company_m' => $this->company_m
    ));
  }

  public function edit()
  {
    if ($this->input->post('action') == 'process') {
      if ($this->company_m->form_validate($this->input->post()) === FALSE) {

      } else {
        if ($this->company_m->save()) {
          my_set_system_message(__("Successfully saved."), "success");

          redirect("companies/edit/" . $this->company_m->id);
        } else {
          $this->company_m->add_error("id", __("Failed save."));
        }
      }
    }
    $this->load->view('companies/edit', array(
      'company_m' => $this->company_m
    ));
  }

  public function ajax_get()
  {
    $id = $this->uri->rsegment(3);
    $this->company_m->get_by_id($id);
    $company_data = array(
      "company_id" => $this->company_m->company_id,
      "client_consolidation" => $this->company_m->client_consolidation
    );

    header('Content-Type: application/json');
    echo json_encode($company_data);
  }

  public function ajax_delete()
  {
    $id = $this->uri->rsegment(3);
    $this->company_m->get_by_id($id);
    if ($this->company_m->is_exists()) {
      $this->company_m->delete();
    }
  }

  public function ajax_find()
  {
    //$status = $this->uri->rsegment(3);
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->company_m->find($search['value'], $order, $start, $length);
    $companies = array();
    if ($result['count'] > 0) {
      $company_status = $this->config->item("data_status");
      foreach ($result['data'] as $company) {
        $actions = array();
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("companys/edit/" . $company->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_company(' . $company->id . ')',
          "icon" => 'trash-o'
        );
        $companies[] = array(
          "index" => (++$start),
          "company_id" => $company->company_id,
          "client_consolidation" => $company->client_consolidation,
          "actions" => my_make_table_edit_icon(base_url('companies/edit/' . $company->id))
            . my_make_table_delete_icon('javascript:delete_company(' . $company->id . ')')
//          "actions" => my_make_table_edit_btn(base_url('companys/edit/' . $company->id))
//            . my_make_table_delete_btn('javascript:delete_company(' . $company->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $companies
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }
}

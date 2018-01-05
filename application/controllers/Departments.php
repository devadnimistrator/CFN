<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for student
 *
 * - Change Profile
 */
class Departments extends My_Controller {

  var $account_id = 0;

  public function __construct() {
    parent::__construct();

    $this->page_title = __("department");

    $this->load->model("department_m");
    $this->load->model("account_m");

    $this->account_id = $this->uri->rsegment(3);
    if ($this->account_id) {

    } else {
      redirect("departments");
    }

    $department_id = $this->uri->rsegment(4);
    if ($department_id) {
      $this->department_m->get_by_id($department_id);
      if ($this->department_m->is_exists()) {
        $this->page_title = __("Edit department") . ": #" . $this->department_m->id;
      } else {
        redirect("departments/departmentlist" . $this->account_id);
      }
    } else {
      $this->page_title = __("Department");
    }
  }

  public function departmentlist(){
    $this->department_m->account_id = $this->account_id;
    if ($this->input->post('action') && $this->input->post('action') == 'process') {
      if ($this->department_m->form_validate($this->input->post()) == FALSE) {

      } else {
        if ($this->department_m->save()) {
          my_set_system_message(__("Successfully saved department information."), "success");
          redirect('departments/departmentlist/' . $this->account_id);
        } else {
          $this->department_m->add_error("id", __("Failed add department."));
        }
      }
    }

    $this->load->view('accounts/departments', array(
      'department_m' => $this->department_m
    ));
  }

  public function ajax_delete() {
    $this->department_m->delete();
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->department_m->find($this->account_id, $search['value'], $order, $start, $length);
    $departments = array();
    if ($result['count'] > 0) {
      $department_status = $this->config->item("data_status");
      foreach ($result['data'] as $department) {
        $actions = array();
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("departments/edit/" . $department->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_department(' . $department->id . ')',
          "icon" => 'trash-o'
        );
        $departments[] = array(
          "index" => ( ++$start),
          "department_id" => $department->department_id,
          "department_name" => $department->department_name,
          "actions" => my_make_table_edit_btn(base_url('departments/departmentlist/' . $department->account_id . "/". $department->id))
            . my_make_table_delete_btn('javascript:delete_department(' . $department->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $departments
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}

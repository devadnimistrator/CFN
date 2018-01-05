<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for student
 *
 * - Change Profile
 */
class Vehicles extends My_Controller {

  var $account_id = 0;

  public function __construct() {
    parent::__construct();

    $this->page_title = __("vehicle");

    $this->load->model("vehicle_m");
    $this->load->model("account_m");

    $this->account_id = $this->uri->rsegment(3);
    if ($this->account_id) {

    } else {
      redirect("vehicles");
    }

    $vehicle_id = $this->uri->rsegment(4);
    if ($vehicle_id) {
      $this->vehicle_m->get_by_id($vehicle_id);
      if ($this->vehicle_m->is_exists()) {
        $this->page_title = __("Edit vehicle") . ": #" . $this->vehicle_m->id;
      } else {
        redirect("vehicles/vehiclelist" . $this->account_id);
      }
    } else {
      $this->page_title = __("Vehicle");
    }
  }

  public function vehiclelist(){
    $this->vehicle_m->account_id = $this->account_id;
    if ($this->input->post('action') && $this->input->post('action') == 'process') {
      if ($this->vehicle_m->form_validate($this->input->post()) == FALSE) {

      } else {
        if ($this->vehicle_m->save()) {
          my_set_system_message(__("Successfully saved vehicle information."), "success");
          redirect('vehicles/vehiclelist/' . $this->account_id);
        } else {
          $this->vehicle_m->add_error("id", __("Failed add vehicle."));
        }
      }
    }

    $this->load->view('accounts/vehicles', array(
      'vehicle_m' => $this->vehicle_m
    ));
  }

  public function ajax_delete() {
    $this->vehicle_m->delete();
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->vehicle_m->find($this->account_id, $search['value'], $order, $start, $length);
    $vehicles = array();
    if ($result['count'] > 0) {
      $vehicle_status = $this->config->item("data_status");
      foreach ($result['data'] as $vehicle) {
        $actions = array();
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("vehicles/edit/" . $vehicle->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_vehicle(' . $vehicle->id . ')',
          "icon" => 'trash-o'
        );
        $vehicles[] = array(
          "index" => ( ++$start),
          "vehicle_id" => $vehicle->vehicle_id,
          "vehicle_description" => $vehicle->vehicle_description,
          "card_id" => $vehicle->card_id,
          "actions" => my_make_table_edit_btn(base_url('vehicles/vehiclelist/' . $vehicle->account_id . "/". $vehicle->id))
            . my_make_table_delete_btn('javascript:delete_vehicle(' . $vehicle->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $vehicles
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}

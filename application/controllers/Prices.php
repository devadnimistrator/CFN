<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for student
 *
 * - Change Profile
 */
class Prices extends My_Controller {

  var $account_id = 0;

  public function __construct() {
    parent::__construct();

    $this->page_title = __("price");

    $this->load->model("account_price_m");
    $this->load->model("account_m");

    $this->account_id = $this->uri->rsegment(3);
    if ($this->account_id) {

    } else {
      redirect("prices");
    }

    $price_id = $this->uri->rsegment(4);
    if ($price_id) {
      $this->account_price_m->get_by_id($price_id);

      if ($this->account_price_m->is_exists()) {
        $this->page_title = __("Edit price") . ": #" . $this->account_price_m->id;
      } else {
        redirect("prices/pricelist" . $this->account_id);
      }
    } else {
      $this->page_title = __("Price");
    }

  }
  public function edit ($data) {
    var_dump($data);exit;
  }

  public function pricelist(){
    $price_id = $this->uri->rsegment(4);
    if ($this->input->post('action') && $this->input->post('action') == 'process') {
      if ($this->account_price_m->form_validate($this->input->post()) == FALSE) {

      } else {
        $this->account_price_m->account_id = $this->account_id;
        $account_price = $this->account_price_m->get_by_account_id($this->account_id);
        $post = $this->input->post();
        if ($price_id) {
          $data = array(
            'id' => $price_id,
            'account_id' => $this->account_id,
            'R_price' => $post['R_price'],
            'D_price' => $post['D_price'],
            'F_price' => $post['F_price'],
            'N_price' => $post['N_price'],
            'E_price' => $post['E_price'],
            'C_price' => $post['C_price'],
          );
        } else {
          $data = array(
            1 => $this->account_id,
            2 => $post['R_price'],
            3 => $post['D_price'],
            4 => $post['F_price'],
            5 => $post['N_price'],
            6 => $post['E_price'],
            7 => $post['C_price'],
          );
        }
        if ($account_price && (!$price_id)) {
          my_set_system_message(__("Can't exist two price per account."), "danger");
          redirect('prices/pricelist/' . $this->account_id);
        } else {
          if ($this->account_price_m->savedata($data,$price_id)) {
            my_set_system_message(__("Successfully saved price information."), "success");
            redirect('prices/pricelist/' . $this->account_id);
          } else {
            $this->account_price_m->add_error("id", __("Failed add price."));
          }
        }
      }
    }

    $this->load->view('accounts/prices', array(
      'account_price_m' => $this->account_price_m
    ));
  }

  public function ajax_delete() {
    $this->account_price_m->delete();
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->account_price_m->find($this->account_id, $search['value'], $order, $start, $length);
    $prices = array();
    if ($result['count'] > 0) {
      $price_status = $this->config->item("data_status");
      foreach ($result['data'] as $price) {
        $actions = array();
        $actions[] = array(
          "label" => __("Edit"),
          "url" => base_url("prices/edit/" . $price->id),
          "icon" => 'edit'
        );
        $actions[] = array(
          "label" => __("Delete"),
          "url" => 'javascript:delete_price(' . $price->id . ')',
          "icon" => 'trash-o'
        );
        $prices[] = array(
          "index" => ( ++$start),
          "R_price" => $price->R_price,
          "D_price" => $price->D_price,
          "F_price" => $price->F_price,
          "N_price" => $price->N_price,
          "E_price" => $price->E_price,
          "C_price" => $price->C_price,
          "actions" => my_make_table_edit_btn(base_url('prices/pricelist/' . $price->account_id . "/". $price->id))
            . my_make_table_delete_btn('javascript:delete_price(' . $price->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $prices
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}

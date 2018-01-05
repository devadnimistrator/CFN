<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for student
 *
 * - Change Profile
 */
class Cards extends My_Controller {

  var $account_id = 0;

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Card");

    $this->load->model("card_m");
    $this->load->model("account_m");

    $this->account_id = $this->uri->rsegment(3);
    if ($this->account_id) {

    } else {
      redirect("card_id");
    }

    $card_id = $this->uri->rsegment(4);
    if ($card_id) {
      $this->card_m->get_by_id($card_id);
      if ($this->card_m->is_exists()) {
        $this->page_title = __("Edit Card") . ": #" . $this->card_m->id;
      } else {
        redirect("cards/cardlist" . $this->account_id);
      }
    } else {
      $this->page_title = __("Card");
    }
  }

  public function cardlist(){
    $this->card_m->account_id = $this->account_id;

    if ($this->input->post('action') && $this->input->post('action') == 'process') {
      if ($this->card_m->form_validate($this->input->post()) == FALSE) {

      } else {
        if ($this->card_m->save()) {
          my_set_system_message(__("Successfully saved card information."), "success");
          redirect('cards/cardlist/' . $this->account_id);
        } else {
          $this->card_m->add_error("id", __("Failed add card."));
        }
      }
    }

    $this->load->view('accounts/cards', array(
      'card_m' => $this->card_m
    ));
  }

  public function ajax_delete() {
    $this->card_m->delete();
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->card_m->find($this->account_id, $search['value'], $order, $start, $length);
    $cards = array();

    if ($result['count'] > 0) {
      $card_status = $this->config->item("data_status");
      foreach ($result['data'] as $card) {
        $cards[] = array(
          "index" => ( ++$start),
          "card_id" => $card->card_id,
          "actions" => my_make_table_edit_btn(base_url('cards/cardlist/' . $card->account_id . "/". $card->id))
            . my_make_table_delete_btn('javascript:delete_card(' . $card->id . ')')
        );
      }
    }
    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $cards
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }


}

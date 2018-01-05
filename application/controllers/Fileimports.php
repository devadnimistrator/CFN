<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fileimports extends My_Controller {

  public function importdata () {
    $account_id = $this->uri->rsegment(4);
    $state = $this->uri->rsegment(3);

    $this->load->model('card_m');
    $this->load->model('account_m');
    $this->load->model('vehicle_m');

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
          if ($state == "cards") {
            $this->card_m->importdata($data);
            $this->vehicle_m->importdata($data);
          }
          if ($state == "accounts") {
            $this->account_m->importdata($data);
          }

        }
        $row++;
      }
      fclose($handle);
    }
    if ($state == "cards") {
      redirect("accounts");
    }
    if ($state == "accounts") {
      redirect("accounts");
    }
  }

}
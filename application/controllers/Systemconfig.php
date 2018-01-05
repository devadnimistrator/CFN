<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for set system config
 *
 */
class Systemconfig extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->check_admin();

    $this->page_title = __("Site Settings");
  }

  public function index() {
    if ($this->input->post('action') == 'process') {
      $configs = array(
          'SITE_TITLE' => $this->input->post('SITE_TITLE'),
          'CONTACT_NAME' => $this->input->post('CONTACT_NAME'),
          'CONTACT_ADDRESS' => $this->input->post('CONTACT_ADDRESS'),
          'CONTACT_CITY' => $this->input->post('CONTACT_CITY'),
          'CONTACT_STATE' => $this->input->post('CONTACT_STATE'),
          'CONTACT_ZIP' => $this->input->post('CONTACT_ZIP'),
          'CONTACT_PHONE' => $this->input->post('CONTACT_PHONE'),
          'CONTACT_EMAIL' => $this->input->post('CONTACT_EMAIL'),
          'DEFAULT_INVOICE_HEADER_TEXT' => $this->input->post('DEFAULT_INVOICE_HEADER_TEXT')
      );

      $this->config_m->set_config($configs);

      my_set_system_message(__("Changed system configurations."), "success");

      redirect('systemconfig');
    }

    $this->load->library("my_bs_form");
    $this->load->view('systemconfig/sitesetting');
  }

  public function db_backup() {
    $this->load->dbutil();

    $prefs = array(
      'format'      => 'zip',
      'filename'    => 'foodnfue_invoice-'.date("Ymd").'.sql'
    );

    $backup = $this->dbutil->backup($prefs);

    $db_name = 'foodnfue_invoice-'. date("Ymd") .'.zip';
    $save = 'upload/'.$db_name;

    $this->load->helper('file');
    write_file($save, $backup);

    $this->load->helper('download');
    force_download($db_name, $backup);
  }

  public function db_upload()
  {
    $config = array();
    $config['upload_path']   = './uploads/';
    $config['allowed_types'] = 'zip';
    $config['max_size']      = 4048;
    $config['overwrite'] = TRUE;

    $this->load->library('upload', $config);

    if ( ! $this->upload->do_upload('file'))
    {
      $error = array('error' => $this->upload->display_errors());
      my_set_system_message(__($error), "danger");
      redirect("systemconfig");
    }
    else
    {
      $uploaded_file = $this->upload->data();
      $zip = new ZipArchive;
      $file = $uploaded_file['full_path'];
      chmod($file,0777);
      if ($zip->open($file) === TRUE) {
        $zip->extractTo('./uploads/');
        $zip->close();

        $sql = $uploaded_file['file_path'].$uploaded_file['raw_name'].".sql";

        if (file_exists($sql)) {
          $sql = file_get_contents($sql);
//          $this->db->query($sql);
          $sqls = explode(';', $sql);
          array_pop($sqls);

          foreach($sqls as $statement){
            $statment = $statement . ";";
            $this->db->query($statement);
          }

        } else {
          die('Error');
        }

      } else {
        echo 'failed';
      }

      my_set_system_message(__("Successfully uploaded your DB!"), "success");
      redirect("systemconfig");
    }
  }

  public function remove_ptdata () {
    $this->load->model('ptdata_m');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $this->db->from($this->ptdata_m->table);
    $this->db->where("date_completed between '{$start_date}' AND '{$end_date}'");
    $this->db->delete();
    my_set_system_message(__("Sucessfully removed."), "success");
    redirect('systemconfig');
  }

  public function remove_invoices () {
    $this->load->model('invoice_m');
    $this->load->model('invoice_item_m');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $this->db->from($this->invoice_m->table);
    $this->db->where("date between '{$start_date}' AND '{$end_date}'");
    $results = $this->db->get()->result();

    $this->db->from($this->invoice_m->table);
    $this->db->from($this->invoice_m->table);
    $this->db->where("date between '{$start_date}' AND '{$end_date}'");
    $this->db->delete();

    foreach ($results as $row) {
      $this->db->reset_query();
      $this->db->from($this->invoice_item_m->table);
      $this->db->where("invoice_id", $row->id );
      $this->db->delete();
    }

    my_set_system_message(__("Sucessfully removed."), "success");
    redirect('systemconfig');
  }

  public function remove_payments () {
    $this->load->model('payment_m');
    $this->load->model('account_m');
    $this->load->model('pbalance_m');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $query = "SELECT a.account, a.name, MAX(p.date) as date, SUM(p.charge) as charge, SUM(p.deposit) as deposit FROM " . $this->account_m->get_table() . " AS a ";
    $query .= " LEFT JOIN " . $this->payment_m->get_table() . " AS p ON a.account = p.account_id";
    $query .= " WHERE p.date BETWEEN '{$start_date}' AND '{$end_date}'";
    $query .= " GROUP BY a.account";
    $query .= " ORDER BY a.account";
    $results = $this->db->query($query)->result();

    foreach ($results as $result) {
      $pbalance = $this->pbalance_m->get_by_account_id($result->account);
      if (!$pbalance) {
        $this->pbalance_m->account_id = $result->account;
        $this->pbalance_m->name = $result->name;
        $this->pbalance_m->date = $result->date;
        $this->pbalance_m->balance = $result->deposit - $result->charge;
        $this->pbalance_m->save();
      } else {
        $this->pbalance_m->get_by_account_id($result->account);
        $this->pbalance_m->account_id = $result->account;
        $this->pbalance_m->name = $result->name;
        $this->pbalance_m->date = $result->date;
        $this->pbalance_m->balance = $this->pbalance_m->balance + ($result->deposit - $result->charge);
        $this->pbalance_m->save();
      }
    }

    $this->db->from($this->payment_m->table);
    $this->db->where("date between '{$start_date}' AND '{$end_date}'");
    $this->db->delete();


    my_set_system_message(__("Sucessfully removed."), "success");
    redirect('systemconfig');
  }

}

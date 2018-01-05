<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_date_result {

  var $type = '';
  var $dates = array();

  public function add_date($date) {
    $this->dates[] = $date;
  }

}

class My_date {

  var $result;

  public function __construct() {
    $this->result = new My_date_result();
  }

  function between_dates($start, $end = 'now') {
    if ($end == 'now') {
      $end = date('Y-m-d');
    }

    $diff = my_diff_days($start, $end);

    if ($diff <= 30) {
      $this->result->type = 'D';

      $this->result->dates = date_range($start, $end);
    } elseif ($diff <= 365 * 3) {
      $this->result->type = 'M';

      $start = explode("-", $start);
      $end = explode("-", $end);

      $start_year = $start[0] * 1;
      $start_month = $start[1] * 1;

      $end_year = $end[0] * 1;
      $end_month = $end[1] * 1;

      $end = $end[0] . "-" . $end[1];

      do {
        $month = $start_year . "-" . str_pad($start_month, 2, "0", STR_PAD_LEFT);
        $this->result->add_date($month);

        $start_month ++;
        if ($start_month == 13) {
          $start_year ++;
          $start_month = 1;
        }
      } while ($month < $end);
    } else {
      $this->result->type = 'Y';

      $start = explode("-", $start);
      $end = explode("-", $end);

      $start_year = $start[0] * 1;

      $end_year = $end[0] * 1;

      do {
        $this->result->add_date($start_year);
        $start_year ++;
      } while ($start_year < $end_year);
    }
  }

}

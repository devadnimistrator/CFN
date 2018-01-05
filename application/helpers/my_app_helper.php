<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function my_get_payment_methods() {
  $CI = &get_instance();

  $methods = $CI->config->item("paymnet_method_types");

  foreach ($methods as $key => $value) {
    $methods[$key] = __($value);
  }

  return $methods;
}

function my_get_income_types() {
  $types = array(
      PAYMENT_REASON_TYPE_EDUCATION => __("Education"),
      PAYMENT_REASON_TYPE_TANGIBLE => __("Tangible"),
      PAYMENT_REASON_TYPE_MOVEMENT => __("Movement"),
      PAYMENT_REASON_TYPE_INCOME => __("Other")
  );

  return $types;
}

function my_get_expenses_types() {
  $types = array(
      PAYMENT_REASON_TYPE_EXPENSES => __("Products1"),
      PAYMENT_REASON_TYPE_MOVEMENT => __("Movement")
  );

  return $types;
}

function __($line, $_ = NULL) {
  $args = func_get_args();
  
  $CI = &get_instance();
  
  if (is_array($line)) {
    $result = array();

    foreach ($line as $key => $value) {
      $args[0] = $value;

      $result[$key] = call_user_func_array(array($CI->my_translator, "translate"), $args);
    }

    return $result;
  } else {
    return call_user_func_array(array($CI->my_translator, "translate"), $args);
  }
}

function ___($line, $_ = NULL) {
  $args = func_get_args();
  
  echo call_user_func_array("__", $args);
}

function my_get_invoice_between_date() {
    $date = new stdClass();
    
    $today = date('j');
    if ($today < 10) {
        $before_month = my_add_date(-2, date('Y-m-01'));
        $date->start_date = my_formart_date($before_month, 'Y-m-01');
        $date->end_date = my_formart_date($before_month, 'Y-m-t');
    } else {
        $date->start_date = date('Y-m-01');
        $date->end_date = date('Y-m-d');
    }
    
    return $date;
}

function my_get_balance($account_id, $date) {
    $CI = &get_instance();
    $ym = substr($date, 0, 7);
//    $query = "SELECT SUM(charge) as sum_charge, SUM(deposit) as sum_deposit FROM " . $CI->db->dbprefix("payments")
//            . " WHERE account_id='{$account_id}' AND SUBSTR(`date`, 1, 10) <= '".substr($date, 0, 10)."'"
//            . " GROUP BY account_id";
  $query = "SELECT SUM(charge) as sum_charge, SUM(deposit) as sum_deposit FROM " . $CI->db->dbprefix("payments")
    . " WHERE account_id='{$account_id}' AND DATE LIKE '" . $ym . "%'"
    . " GROUP BY account_id";
    return $CI->db->query($query)->row();
}

function my_get_previous_balance ($account_id, $date) {
  $CI = &get_instance();
  $query = "SELECT SUM(charge) as sum_charge, SUM(deposit) as sum_deposit FROM " . $CI->db->dbprefix("payments")
        . " WHERE account_id='{$account_id}' AND SUBSTR(`date`, 1, 10) <= '".substr($date, 0, 10)."'"
        . " GROUP BY account_id";
  return $CI->db->query($query)->row();
}
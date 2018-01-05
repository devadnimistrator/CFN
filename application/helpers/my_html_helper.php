<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function my_load_css($path, $echo = true) {
  $css_html = '<link href="' . base_url("assets/" . $path) . '" rel="stylesheet">';
  if ($echo) {
    echo $css_html;
  }

  return $css_html;
}

function my_load_js($path, $echo = true) {
  $css_html = '<script src="' . base_url("assets/" . $path) . '"></script>';
  if ($echo) {
    echo $css_html;
  }

  return $css_html;
}

function my_show_msg($msgs, $type = 'info', $output = true) {
  if (is_array($msgs)) {
    
  } else {
    $msgs = array($msgs);
  }
  $html = "";
  foreach ($msgs as $field => $msg) {
    $html .= '<div class="alert alert-info alert-' . $type . ' alert-dismissible fade in" role="alert" data-validate-field="' . $field . '">';
    $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
    $html .= $msg;
    $html .= '</div>';
    $html .= "\n";
  }

  if ($output) {
    echo $html;
  } else {
    return $html;
  }
}

function my_show_form_validateion_errors($msgs, $output = true) {
  $html = "";
  if (count($msgs) > 0) {
    $html = '<div class="form-validation-errors">' . "\n";
    $html .= my_show_msg($msgs, "error", false);
    $html .= "\n" . '<div>';
  }
  if ($output) {
    echo $html;
  } else {
    return $html;
  }
}

function my_make_table_edit_btn($url, $label = false) {
  if ($label === false) {
    $label = 'Edit';
  }
  return my_make_table_btn($url, $label, "info", "pencil");
}

function my_make_table_delete_btn($url, $label = false) {
  if ($label === false) {
    $label = 'Delete';
  }
  return my_make_table_btn($url, $label, "danger", "trash-o");
}

function my_make_table_btn($url, $label, $class, $icon) {
  return '<a href="' . $url . '" class="btn btn-' . $class . ' btn-xs"><i class="fa fa-' . $icon . '"></i> ' . __($label) . ' </a>';
}

function my_make_table_btn_group($buttons) {
  $actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary">' . __("Action") . '</button>';
  $actions.= '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span><span class="sr-only"></span></button>';
  $actions.= '<ul class="dropdown-menu" role="menu">';
  foreach ($buttons as $button) {
    $actions.= '<li><a href="'.$button['url'].'"><i class="fa fa-'.$button['icon'].'"></i>&nbsp;&nbsp;&nbsp;'.$button['label'].'</a></li>';
  }
  $actions.= '</ul></div>';
  
  return $actions;
}

function my_make_table_edit_icon($url) {
  return my_make_table_icon($url, "edit", "edit");
}

function my_make_table_delete_icon($url) {
  return my_make_table_icon($url, "remove", "remove");
}

function my_make_table_view_icon($url) {
  return my_make_table_icon($url, "edit", "file-o");
}

function my_make_table_icon ($url, $class, $icon) {
    return '<a href="' . $url . '" class="' . $class . '" style="color: #333"><i class="fa fa-' . $icon . '"></i></a> &nbsp;';
}

function my_indices_to_string ($float) {
  return $float * 1;
}

function my_convert_str_to_date($str_date, $str_format = 'ymd', $date_format='m/d/Y') {
  return DateTime::createFromFormat($str_format, $str_date)->format($date_format);
}
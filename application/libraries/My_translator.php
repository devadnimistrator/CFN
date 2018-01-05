<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_translator {

  private $CI = null;
  private $language = "english";
  private $controller = "app";
  private $missed_lines = array();
  private $missed_model_lines = array();

  public function __construct($config) {
    $this->CI = &get_instance();

    if (isset($config['language'])) {
      $this->language = $config['language'];
    }
    if (isset($config['controller'])) {
      $this->controller = $config['controller'];
    }

    $this->load_lang_file($this->controller);
  }

  public function __destruct() {
    $this->save_missed_lines();
  }

  private function load_lang_file($file) {
    if ($this->language == 'english') {
      return;
    }

    if (file_exists(APPPATH . "language/" . $this->language . "/" . $file . "_lang.php")) {
      $this->CI->lang->load($file, $this->language);
    }
  }

  public function load_lang_model($model) {
    if ($this->language == 'english') {
      return;
    }

    if (!isset($this->missed_model_lines[$model])) {
      $this->missed_model_lines[$model] = array();
    }

    $this->load_lang_file("models/" . $model);
  }

  public function translate($line, $_ = NULL) {
    $converted_str = "";
    if ($this->language == 'english') {
      $converted_str = $line;
    } else {
      $converted_str = lang($line);
      if (empty($converted_str)) {
        if (!isset($this->missed_lines[$line])) {
          $this->missed_lines[$line] = $line;
        }

        $converted_str = $line;
      }
    }

    $this->CI->lang->language[$line] = $converted_str;

    $args = func_get_args();
    $args[0] = $converted_str;
    if (count($args) == 1) {
      return $converted_str;
    } else {
      return call_user_func_array("sprintf", $args);
    }
  }

  public function model_translate($model, $line) {
    if ($this->language == 'english') {
      return $line;
    }

    $converted_str = lang($line);

    if (empty($converted_str)) {
      if (!isset($this->missed_model_lines[$model][$line])) {
        $this->missed_model_lines[$model][$line] = $line;
      }

      $converted_str = $line;
    }

    $this->CI->lang->language[$line] = $converted_str;

    return $converted_str;
  }

  private function save_missed_lines() {
    if (!empty($this->missed_lines)) {
      $is_new = false;
      if (!file_exists(APPPATH . "language/" . $this->language . "/" . $this->controller . "_lang.php")) {
        $is_new = true;
      }
      $file = fopen(APPPATH . "language/" . $this->language . "/" . $this->controller . "_lang.php", "a+");
      if ($is_new) {
        fwrite($file, "<");
        fwrite($file, "?php defined('BASEPATH') OR exit('No direct script access allowed');");
        fwrite($file, "\n");
      }

      foreach ($this->missed_lines as $key => $line) {
        fwrite($file, "$");
        fwrite($file, "lang[\"" . $key . "\"] = \"" . $line . "\";");
        fwrite($file, "\n");
      }

      fclose($file);
    }

    foreach ($this->missed_model_lines as $model => $lines) {
      $this->save_missed_model_lines($model, $lines);
    }
  }

  private function save_missed_model_lines($model, $lines) {
    if (empty($lines)) {
      return;
    }

    $is_new = false;
    if (!file_exists(APPPATH . "language/" . $this->language . "/models/" . $model . "_lang.php")) {
      $is_new = true;
    }
    $file = fopen(APPPATH . "language/" . $this->language . "/models/" . $model . "_lang.php", "a+");
    if ($is_new) {
      fwrite($file, "<");
      fwrite($file, "?php defined('BASEPATH') OR exit('No direct script access allowed');");
    }

    foreach ($lines as $key => $line) {
      fwrite($file, "\n");
      fwrite($file, "$");
      fwrite($file, "lang[\"" . $key . "\"] = \"" . $line . "\";");
    }

    fclose($file);
  }

}

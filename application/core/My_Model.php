<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Basic My Model
 */
class My_Model extends CI_Model {

  public $table = "";
  private $db_fields = array();
  public $fields = array();
  public $errors = array();
  public $msgs = array();
  private $model_class = "";

  public function __construct($id = 0) {
    parent::__construct();

    $this->model_class = strtolower(get_class($this));

    $this->_init($id);
  }

  private function _init($id) {
    if ($this->table) {
      
    } else {
      $model_name = strtolower(get_class($this));
      if (substr($model_name, strlen($model_name) - 2) == '_m') {
        $model_name = substr($model_name, 0, strlen($model_name) - 2);
      }

      $this->table = plural($model_name);
    }

    if ($id === FALSE) {
      return;
    }

    $this->my_translator->load_lang_model($this->model_class);

    $fields = $this->db->field_data($this->table);

    foreach ($fields as $field) {
      $this->db_fields[$field->name] = $field;

      if (isset($this->fields[$field->name])) {
        
      } else {
        $this->fields[$field->name] = array(
        );
      }

      if (!isset($this->fields[$field->name]['label'])) {
        $this->fields[$field->name]['label'] = ucwords($field->name);
      } else {
        //$this->fields[$field->name]['label'] = $this->label_translate($this->fields[$field->name]['label']);
      }

      if (!isset($this->fields[$field->name]['type'])) {
        $this->fields[$field->name]['type'] = "text"; //$field->type;
      }

      $this->fields[$field->name]['value'] = "";
      if (isset($this->fields[$field->name]['default'])) {
        $this->fields[$field->name]['value'] = $this->fields[$field->name]['default'];
      } elseif ($field->default == 'CURRENT_TIMESTAMP') {
        $this->fields[$field->name]['value'] = date('Y-m-d H:i:s');
      } else {
        $this->fields[$field->name]['value'] = $field->default;
      }
    }

    foreach ($this->fields as $field => $options) {
      if (isset($this->fields[$field]['label'])) {
        $this->fields[$field]['label'] = $this->label_translate($this->fields[$field]['label']);
      } else {
      }
    }

    if ($id) {
      $this->get_by_id($id);
    }
  }

  public function init_values() {
    foreach ($this->fields as $name => $field) {
      if (isset($this->db_fields[$name])) {
        if ($this->db_fields[$name]->default == 'CURRENT_TIMESTAMP') {
          $this->fields[$name]['value'] = date('Y-m-d H:i:s');
        } else {
          $this->fields[$name]['value'] = $this->db_fields[$name]->default;
        }
      } else {
        $this->fields[$name]['value'] = "";
      }
    }
  }

  private function label_translate($label) {
    return $this->my_translator->model_translate($this->model_class, $label);
  }

  /**
   * Call
   *
   * Calls the watched method.
   *
   * @access	overload
   * @param	string
   * @param	string
   * @return	void
   */
  function __call($method, $arguments) {
    if (strpos($method, "get_by_") === 0) {
      return $this->_get_by(substr($method, 7), $arguments[0]);
    } elseif (strpos($method, "count_by_") === 0) {
      return $this->_count_by(substr($method, 9), $arguments[0]);
    } elseif (strpos($method, "delete_by_") === 0) {
      return $this->_delete_by(substr($method, 10), $arguments[0]);
    } elseif (isset($this->fields[$method])) {
      if (count($arguments) > 0) {
        $this->fields[$method] = $arguments;
      } else {
        return $this->fields[$method];
      }
    }
  }

  function __get($field) {
    if (isset($this->fields[$field])) {
      return $this->fields[$field]['value'];
    } elseif (isset($this->{$field})) {
      return $this->{$field};
    }

    return parent::__get($field);
  }

  function __set($field, $value) {
    if (isset($this->fields[$field])) {
      $this->fields[$field]['value'] = $value;
    } else {
      $this->{$field} = $value;
    }
  }

  private function _get_by($field, $value) {
    $this->db->where($field, $value);
    $result = $this->db->get($this->table)->result();

    if (!empty($result) && count($result) > 0) {
      foreach ($this->fields as $field => $infos) {
        if (isset($result[0]->{$field})) {
          $this->fields[$field]['value'] = $result[0]->{$field};
        }
      }
    } else {
      $this->init_values();
      return false;
    }

    return $result;
  }

  private function _delete_by($field, $value) {
    $this->db->delete($this->table, array($field => $value));
  }

  private function _count_by($field, $value) {
    $this->db->where($field, $value);
    return $this->db->from($this->table)->count_all_results();
  }

  public function get_table() {
    return $this->db->dbprefix($this->table);
  }

  public function is_exists() {
    if ($this->id) {
      return TRUE;
    } else {
      return false;
    }
  }

  function add_field($field, $options) {
    $options = array_merge(array("type" => "text", "value" => ""), $options);
    $this->fields[$field] = $options;
  }

  function validate($values = array()) {
    
  }

  public function count_all() {
    return $this->db->count_all($this->table);
  }

  public function save() {
    $data = array();
    foreach ($this->fields as $field_name => $field_data) {
      if (isset($this->db_fields[$field_name])) {
        $data[$field_name] = $field_data['value'];
      }
    }

    if ($this->is_exists()) {
      return $this->db->update($this->table, $data, array("id" => $this->id));
    } else {
      $status = $this->db->insert($this->table, $data);
      if ($status) {
        $new_id = $this->db->insert_id();
        if ($new_id) {
          $this->id = $new_id;
        }
      }

      return $status;
    }
  }

  public function delete() {
    if ($this->is_exists()) {
      $this->db->delete($this->table, array('id' => $this->id));
    }
  }

  public function add_error($field, $msg) {
    $this->errors[$field] = $msg;
  }

  public function show_errors() {
    my_show_form_validateion_errors($this->errors);
  }

  public function get_errors() {
    return $this->errors;
  }

  public function add_msg($msg, $type = 'info') {
    $this->msgs[] = array("type" => $type, "text" => $msg);
  }

  public function show_msgs() {
    foreach ($this->msgs as $msg) {
      my_show_msg($msg["text"], $msg['type']);
    }
  }

  /*   * ****************************************
   * 
   *    BS FORM
   * 
   * **************************************** */

  public $bs_form = null;

  public function form_create($config) {
    require_once (APPPATH . 'libraries/My_bs_form.php');
    $this->bs_form = new My_bs_form($config);

    $this->form_add_element('action', array(
        'type' => BSFORM_HIDDEN,
        'value' => isset($config['action_type']) ? $config['action_type'] : 'process'
    ));
  }

  public function form_add_element($field, $add_options = FALSE) {
    $options = array();
    if (isset($this->fields[$field])) {
      $options = $this->fields[$field];
    }
    $options = array_merge(array("value" => "", "type" => "text"), $options);
    if (!isset($options['label'])) {
      $options['label'] = $this->label_translate(ucwords($field));
    }
    if ($add_options && is_array($add_options)) {
      $options = array_merge($options, $add_options);
    }
    $this->bs_form->add_element($field, $options['type'], $options['value'], $options);
  }

  public function form_generate($output = TRUE) {
    $html = $this->bs_form->generate(FALSE);
    if ($output) {
      echo $html;
    } else {
      return $html;
    }
  }

  public function form_validate($values) {
    $this->load->library('form_validation');

    foreach ($values as $field => $value) {
      if (isset($this->fields[$field])) {
        $this->fields[$field]['value'] = is_array($value) ? $value : trim($value);

        $this->form_validation->set_rules($field, $this->fields[$field]['label'], isset($this->fields[$field]['rules']) ? $this->fields[$field]['rules'] : array());
      }
    }

    if ($this->form_validation->run() === FALSE) {
      $this->errors = $this->form_validation->error_array();

      if ($this->errors) {
        return FALSE;
      } else {
        return TRUE;
      }
    }
    return TRUE;
  }

}

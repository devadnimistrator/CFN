<?php

defined('BASEPATH') OR exit('No direct script access allowed');

define('BSFORM_HIDDEN', 'hidden');
define('BSFORM_TEXT', 'text');
define('BSFORM_URL', 'url');
define('BSFORM_EMIL', 'email');
define('BSFORM_TEL', 'tel');
define('BSFORM_NUMBER', 'number');
define('BSFORM_PASSWORD', 'password');
define('BSFORM_FILE', 'file');
define('BSFORM_SELECT', 'select');
define('BSFORM_RADIO', 'radio');
define('BSFORM_CHECKBOX', 'checkbox');
define('BSFORM_TEXTAREA', 'textarea');
define('BSFORM_HTML', 'html');
define('BSFORM_DATE', 'date');
define('BSFORM_DATETIME', 'datetime');

class My_bs_form {

  var $config = array();
  var $elements = array();
  var $buttons = array();
  var $form_configs = array(
      "id" => "",
      "name" => "",
      "action" => "",
      "method" => "post",
      "autocomplete" => true,
      "emelents" => array(),
      "buttons" => array(),
      "col_width" => 4,
      "target" => "",
      "is_fileupload" => false,
      "ajaxcall" => false
  );

  function __construct($configs = array()) {
    $this->form_configs = array_merge($this->form_configs, $configs);

    $this->_init_buttons();
  }

  private function _init_buttons() {
    if ($this->ajaxcall) {
      return;
    }

    if (empty($this->form_configs['buttons'])) {
      $this->buttons = array(
          array(
              "type" => "reset",
              "value" => __("Reset"),
              "options" => array("class" => "btn btn-default")
          ),
          array(
              "type" => "submit",
              "value" => __("Submit"),
              "options" => array("class" => "btn btn-primary")
          )
      );
    } else {
      $this->buttons = $this->form_configs['buttons'];
    }
  }

  function __get($filed) {
    return $this->form_configs[$filed];
  }

  function __set($filed, $value) {
    $this->form_configs[$filed] = $value;
  }

  function add_element($name, $type = BSFORM_TEXT, $value = "", $options = array()) {
    $options = array_merge(array(
        "name" => $name,
        "type" => $type,
        "value" => $value,
        "label" => ucwords($name),
        "required" => false,
        "options" => array(),
        "isMulty" => false,
        "rules" => false,
        "description" => false,
        ), $options);

    if ($options['rules']) {
      if (is_array($options['rules'])) {
        
      } else {
        $options['rules'] = explode("|", $options['rules']);
      }
    }

    if (is_array($options['rules']) && in_array("required", $options['rules'])) {
      $options['required'] = true;
    }

    $this->elements[] = $options;
  }

  function add_button($type, $value, $options = array()) {
    $this->buttons[] = array(
        "type" => $type,
        "value" => $value,
        "options" => $options
    );
  }

  function form_start($print = false) {
    $html = '<form';
    if ($this->name) {
      $html .= ' id="form-' . $this->name . '"';
      $html .= ' name="' . $this->name . '"';
    }
    if ($this->action) {
      $html .= ' action="' . $this->action . '"';
    }
    if ($this->target) {
      $html .= ' target="' . $this->target . '"';
    }
    $html .= ' class="form-horizontal form-label-left validateform" novalidate method="' . $this->method . '" autocomplete="' . ($this->autocomplete ? 'on' : 'off') . '"';
    if ($this->is_fileupload) {
      $html .= ' enctype="multipart/form-data"';
    }
    if ($this->ajaxcall) {
      $html .= ' data-ajaxcall="' . $this->ajaxcall . '"';
    }
    $html .= ' >';
    $html .= "\n";

    if ($print === TRUE) {
      echo $html;
    } else {
      return $html;
    }
  }

  function form_elements($print = false) {
    $html = "";
    foreach ($this->elements as $element) {
      if ($element['type'] == BSFORM_HIDDEN || $element['type'] == BSFORM_HTML) {
        $html .= $this->{'generate_' . $element['type']}($element);
      } else {
        $html .= '<div class="item form-group" id="form-group-' . $element['name'] . '">' . "\n";
        if ($this->col_width > 0) {
          $html .= '<label class="control-label col-md-' . $this->col_width . ' col-sm-' . ($this->col_width + 1) . ' col-xs-12" for="em-' . $element['name'] . '">';
          $html .= $element['label'] != '' ? $element['label'] : ucfirst($element['name']);
          if ($element['required']) {
            $html .= ' <span class="required">*</span>';
          }
          $html .= '</label>' . "\n";
        }
        if ($this->col_width > 0) {
          $html .= '<div class="col-md-' . (12 - $this->col_width) . ' col-sm-' . (11 - $this->col_width) . ' col-xs-12 control-input">' . "\n";
        }
        $method = 'generate_' . $element['type'];
        if (method_exists($this, $method)) {
          
        } else {
          $element['type'] = 'generate_' . BSFORM_TEXT;
          $method = 'generate_' . BSFORM_TEXT;
        }
        $html .= call_user_func_array(array(
            $this,
            $method
            ), array($element));
        $html .= "\n";
        if ($this->col_width > 0) {
          $html .= '</div>';
        }
        $html .= "\n";
        $html .= '</div>';
      }
      $html .= "\n";
    }

    if ($print === TRUE) {
      $this->elements = array();

      echo $html;
    } else {
      $this->elements = array();

      return $html;
    }
  }

  function form_buttons($print = false) {
    $html = '<div class="form-group">' . "\n";
    if ($this->col_width > 0) {
      $html .= '<div class="col-md-' . $this->col_width . ' col-sm-' . ($this->col_width + 1) . ' col-xs-12"></div>';
    }
    $html .= '<div class="col-md-' . (12 - $this->col_width) . ' col-sm-' . (11 - $this->col_width) . ' col-xs-12">' . "\n";

    foreach ($this->buttons as $button) {
      $html .= '<button type="' . $button['type'] . '"';
      foreach ($button['options'] as $key => $value) {
        $html .= ' ' . $key . '="' . $value . '"';
      }
      $html .= '>';
      $html .= $button['value'];
      $html .= '</button>' . "\n";
    }
    $html .= '</div>';
    $html .= "\n";
    $html .= '</div>';

    if ($print === TRUE) {
      echo $html;
    } else {
      return $html;
    }
  }

  function form_end($print = false) {
    $html = "</form>";

    if ($print === TRUE) {
      echo $html;
    } else {
      return $html;
    }
  }

  function generate($print = true) {
    if ($print === TRUE) {
      $this->form_start($print);

      $this->form_elements($print);

      $this->form_buttons($print);

      $this->form_end($print);
    } else {
      $html = $this->form_start($print);

      $html .= $this->form_elements($print);

      $html .= $this->form_buttons($print);

      $html .= $this->form_end($print);

      return $html;
    }
  }

  function get_valied_rule($element) {
    $html = "";
    if (isset($element['rules']) && !empty($element['rules'])) {
      if (is_array($element['rules'])) {
        
      } else {
        $element['rules'] = explode("|", $element['rules']);
      }
      foreach ($element['rules'] as $validate) {
        $condition = "";
        if (strpos($validate, "[") > 0) {
          $condition = explode("[", $validate);
          $condition = substr($condition[1], 0, strlen($condition[1]) - 1);
        }

        if (strpos($validate, "min_length") === 0) {
          $html .= ' data-validate-length-range="' . $condition . '"';
        } elseif (strpos($validate, "max_length") === 0) {
          $html .= ' data-validate-length-range="0,' . $condition . '"';
        } elseif (strpos($validate, "exact_length") === 0) {
          $html .= ' data-validate-length="' . $condition . ',' . $condition . '"';
        } elseif (strpos($validate, "matches") === 0) {
          $html .= ' data-validate-linked="' . $condition . '"';
        }
      }
    }

    return $html;
  }

  function generate_hidden($element) {
    $html = '<input type="hidden" id="em-' . $element['name'] . '" name="' . $element['name'] . '" value="' . $element['value'] . '" />';
    return $html;
  }

  function generate_input($element) {
    $html = '<input type="' . $element['type'] . '"';
    $html .= ' id="em-' . $element['name'] . '"';
    $html .= ' name="' . $element['name'] . '"';
    $html .= ' value="' . $element['value'] . '"';
    $html .= ($element['required'] ? ' required="required"' : '');
    $html .= $this->get_valied_rule($element);
    if ($element['description']) {
      $html .= ' placeholder="' . $element['description'] . '"';
    }

    $html .= ' class="form-control col-xs-12';
    if (!$element['required'] && isset($element['rules']) && !empty($element['rules'])) {
      $html .= ' optional';
    }
    $html .= '"';
    if (isset($element['readonly']) && $element['readonly']) {
      $html .= ' readonly';
    }
    $html .= ' autocomplete="' . ($this->autocomplete ? 'on' : 'off') . '" />';

    return $html;
  }

  function generate_password($element) {
    return $this->generate_input($element);
  }

  function generate_text($element) {
    return $this->generate_input($element);
  }

  function generate_url($element) {
    return $this->generate_input($element);
  }

  function generate_email($element) {
    return $this->generate_input($element);
  }

  function generate_tel($element) {
    return $this->generate_input($element);
  }

  function generate_number($element) {
    return $this->generate_input($element);
  }

  function generate_html($element) {
    $html = $element['value'];
    return $html;
  }

  function generate_radio($element) {
    $html = '<div class="radio-groups">' . "\n";
    $i = 0;
    foreach ($element['options'] as $value => $label) {
      $i++;
      $id = "em-" . $element['name'] . "-" . $i;
      $html .= '  <label for="' . $id . '">' . $label . '&nbsp;</label>';
      $html .= '<input type="radio" name="' . $element['name'] . '" id="' . $id . '" value="' . $value . '"' . ($element['required'] ? ' required="required"' : '') . ' ' . ($element['value'] == $value ? ' checked' : '') . ' class="flat">&nbsp;&nbsp;' . "\n";
    }
    $html .= '</div>';
    return $html;
  }

  function generate_select($element) {
    $html = '<select id="em-' . $element['name'] . '"';
    $html .= ' name="' . $element['name'] . '"';
    $html .= ($element['required'] ? ' required="required"' : '') . ($element['isMulty'] ? ' size=5 multiple' : '');
    $html .= $this->get_valied_rule($element);
    $html .= ' class="form-control col-xs-12">' . "\n";
    foreach ($element['options'] as $value => $label) {
      $html .= '  <option value="' . $value . '" ' . ($element['value'] == $value ? 'selected' : '') . '>' . $label . '</option>' . "\n";
    }
    $html .= '</select>';
    return $html;
  }

  function generate_textarea($element) {
    $html = '<textarea id="em-' . $element['name'] . '"';
    $html .= ' name="' . $element['name'] . '"';
    $html .= ($element['required'] ? ' required="required"' : '');
    $html .= $this->get_valied_rule($element);
    if ($element['description']) {
      $html .= ' placeholder="' . $element['description'] . '"';
    }
    if (isset($element['rows'])) {
      $html .= ' rows="' . $element['rows'] . '"';
    } else {
      $html .= ' rows="5"';
    }
    $html .= ' class="form-control">';
    $html .= $element['value'];
    $html .= '</textarea>';
    return $html;
  }

  function generate_date($element) {
    $html = '<input type="text" id="em-' . $element['name'];
    $html .= '"' . ' name="' . $element['name'] . '"';
    $html .= ' value="' . $element['value'] . '"';
    $html .= ($element['required'] ? ' required="required"' : '');
    $html .= $this->get_valied_rule($element);
    $html .= ' class="date-picker form-control" />';
    return $html;
  }

  function generate_datetime($element) {
    $html = '<input type="text" id="em-' . $element['name'];
    $html .= '"' . ' name="' . $element['name'] . '"';
    $html .= ' value="' . $element['value'] . '"';
    $html .= ($element['required'] ? ' required="required"' : '');
    $html .= $this->get_valied_rule($element);
    $html .= ' class="datetime-picker form-control" />';
    return $html;
  }

  function generate_checkbox($element) {
    if (is_array($element['value'])) {
      
    } else {
      $element['value'] = array();
    }

    if (empty($element['options']) && count($element['options']) == 1) {
      $name = $element['name'];
      foreach ($element['options'] as $value => $label) {
        $html = '<input type="checkbox" name="' . $name . '" id="em-' . $name . '" value="' . $value . '"' . ($element['required'] ? ' required="required"' : '') . ' ' . (in_array($value, $element['value']) ? ' checked' : '') . ' class="flat">';
        return $html;
      }
    }

    $selected_labels = array();
    $html = "";
    //if (count($element['options']) > 5) {
    $html .= '<div class="long-radio-groups">';
    $html .= '<div id="em-' . $element['name'] . '-labels">' . implode(", ", $selected_labels) . '</div>';
    //}
    if ($element['isMulty']) {
      $html .= '<div class="radio-groups-allcheck" radio-groups="em-' . $element['name'] . '"><input type="checkbox" id="em-' . $element['name'] . '-allcheck" class="flat" radio-groups="em-' . $element['name'] . '">';
      $html .= '&nbsp;&nbsp;<label for="em-' . $element['name'] . '-allcheck">Select All</label></div>';
    }

    $html .= '<div id="em-' . $element['name'] . '" class="radio-groups">' . "\n";
    $i = 0;
    foreach ($element['options'] as $value => $label) {
      $i++;
      $id = "em-" . $element['name'] . "-" . $i;
      $name = $element['name'];
      if ($element['isMulty']) {
        $name .= "[]";
      }

      $html .= '<div class="checkbox-input-field">';
      $html .= '<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '"' . ($element['required'] ? ' required="required"' : '') . ' ' . (in_array($value, $element['value']) ? ' checked' : '') . ' class="flat">&nbsp;&nbsp;';
      $html .= '<label for="' . $id . '">' . $label . '&nbsp;</label>';
      $html .= "</div>\n";

      if (in_array($value, $element['value'])) {
        $selected_labels[] = $label;
      }
    }
    $html .= '</div>';
    //if (count($element['options']) > 5) {
    $html .= '</div>';
    //}

    return $html;
  }

  function generate_file($element) {
    $name = $element['name'];
    $html = '<div class="input-group input-file-group">' . "\n";
    if ($element['value']) {
      $html .= '  <span class="input-group-btn">' . "\n";
      $html .= '    <a target="_blank" href="' . $element['value'] . '" class="btn btn-dark" data-toggle="tooltip" title="View old file"><i class="fa fa-eye"></i></a>' . "\n";
      $html .= '  </span>' . "\n";
    }
    $html .= '  <input type="text" class="form-control" id="em-' . $name . '"' . ($element['required'] ? ' required="required"' : '') . '>' . "\n";
    $html .= '  <input type="file" name="' . $name . '" >' . "\n";
    $html .= '  <span class="input-group-btn">' . "\n";
    $html .= '    <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Choise file"><i class="fa fa-upload"></i></button>' . "\n";
    $html .= '  </span>' . "\n";
    $html .= '</div>';
    return $html;
  }

}

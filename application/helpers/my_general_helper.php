<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function my_set_default_configs() {
  $CI = &get_instance();
  $configs = $CI->config_m->get_all();
  foreach ($configs as $config) {
    if (defined($config->name)) {
      
    } else {
      define($config->name, $config->value);
    }
  }

  if (DISPLAY_LANGUAGE == 'english') {
    define('DATE_FULL_FORMAT', 'Y-m-d');
    define('DATETIME_FULL_FORMAT', 'Y-m-d H:i:s');
    define('DATETIME1_FULL_FORMAT', 'Y-m-d H:i');

    define('DEFAULT_TIMEZONE', 'America/Los_Angeles');
    define('DEFAULT_LOCAL', "en_US.UTF-8");
  } elseif (DISPLAY_LANGUAGE == 'spanish') {
    define('DATE_FULL_FORMAT', 'd-m-Y');
    define('DATETIME_FULL_FORMAT', 'd-m-Y H:i:s');
    define('DATETIME1_FULL_FORMAT', 'd-m-Y H:i');

    define('DEFAULT_TIMEZONE', 'America/Monterrey');
    define('DEFAULT_LOCAL', "es_ES.UTF-8");
  }

  date_default_timezone_set(DEFAULT_TIMEZONE);
  //setlocale(LC_ALL, DEFAULT_LOCAL);
}

/**
 * Validate password
 * @param string $plain
 * @param string $encrypted
 * @return boolean
 */
function my_validate_password($plain, $encrypted) {
  if ($plain && $encrypted) {
    // split apart the hash / salt
    $salt = substr($encrypted, -4);
    $encrypted = substr($encrypted, 0, -4);

    if (substr(hash_hmac("sha256", utf8_encode($plain), utf8_encode($salt), false), 0, 16) == $encrypted) {
      return true;
    }
  }

  return false;
}

/**
 * Encrypt password
 * 
 * @param string $plain
 * @return string
 */
function my_encrypt_password($plain) {
  $salt = substr(md5(random_string('alnum', 10)), 0, 4);

  $password = hash_hmac("sha256", utf8_encode($plain), utf8_encode($salt), false);
  //md5($salt . $plain) . ':' . $salt;

  return substr($password, 0, 16) . $salt;
}

/**
 * Get Image URL of Google Map
 * 
 * @param double $lat_itude
 * @param double $long_itude
 * @param int $width
 * @param int $height
 * @param int $zoom
 * @param string $type : roodmap, terrain, satellite, hybrid
 * @return string url
 */
function my_get_google_map_image($lat_itude, $long_itude, $width = 450, $height = 300, $zoom = 15, $type = 'roadmap') {
  //$image = "https://maps.googleapis.com/maps/api/staticmap?center={$lat_itude},{$long_itude}&zoom={$zoom}&maptype={$type}&size={$width}x{$height}";
  $image = "https://maps.googleapis.com/maps/api/staticmap?center={$lat_itude},{$long_itude}&zoom={$zoom}&scale=false&size={$width}x{$height}&maptype={$type}&format=jpg&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7C{$lat_itude},{$long_itude}";

  return $image;
}

/**
 * Get Image URL of Bing Map
 * 
 * @param double $lat_itude
 * @param double $long_itude
 * @param int $width
 * @param int $height
 * @param int $zoom
 * @return string url
 */
function my_get_bing_map_image($lat_itude, $long_itude, $width = 600, $height = 400, $zoom = 17) {
  if ($lat_itude && $long_itude) {
    $image = "https://dev.virtualearth.net/REST/v1/Imagery/Map/Aerial/{$lat_itude},{$long_itude}/{$zoom}?mapSize={$width},{$height}&key=Au7rlsDVn0-CG7wNZOyP72-Ka-XfbNT1UqVHArNnPj1KSh9CFvvO8TNv_vgi6M1r";
    return $image;
  } else {
    return "";
  }
}

/**
 * Get Full Address
 * 
 * @param type $address
 * @param type $is_full
 * @param type $isHtml
 * @return string
 */
function my_address_display($address, $is_full = true, $isHtml = true) {
  $str = "";
  if ($address->street_number) {
    $str .= $address->street_number . " ";
  }
  $str .= $address->street;

  if ($is_full) {
    if ($isHtml) {
      $str .= ",<br>";
    } else {
      $str .= ", ";
    }
    if (isset($address->suburb) && $address->suburb) {
      $str.= $address->suburb . ", ";
    }
    $str .= $address->city . ", " . $address->state . " " . $address->zipcode;
  }

  return $str;
}

/**
 * Find Images of Recommend by Address
 * 
 * @param type $address
 * @return type
 */
function my_find_images_by_address($address) {
  //$address = strtolower(my_address_display($address, false));

  $images = array();
  $file_list = scandir(FCPATH . PICS_UPLOAD_DIRECTORY);
  foreach ($file_list as $file) {
    if ($file != '.' && $file != '..') {
      if (!is_dir(FCPATH . PICS_UPLOAD_DIRECTORY . $file)) {
        $_file = strtolower($file);
        if (strpos($_file, strtolower($address->street_number)) !== false && strpos($_file, strtolower($address->address)) !== false && strpos($_file, strtolower($address->zipcode)) !== false) {
          $images[] = $file;
        }
      }
    }
  }

  return $images;
}

/**
 * Get Images from Folder
 * 
 * @param type $page
 * @param type $limit_count
 * @return type
 */
function my_get_images_from_folder($page = 0, $limit_count = 20) {
  $start_index = ($page - 1) * $limit_count;
  $end_index = $start_index + $limit_count;
  $images = array();

  $file_list = scandir(FCPATH . PICS_UPLOAD_DIRECTORY);
  $fileindex = 0;
  foreach ($file_list as $file) {
    if ($file != '.' && $file != '..') {
      if (!is_dir(FCPATH . PICS_UPLOAD_DIRECTORY . $file)) {
        if ($fileindex >= $start_index) {
          $images[] = $file;
        }
        $fileindex ++;
        if ($fileindex == $end_index) {
          break;
        }
      }
    }
  }
  return $images;
}

/**
 * Resize Image
 * 
 * @param type $image
 * @param type $width
 * @param type $height
 * @param type $return_type
 * @return boolean
 */
function my_resize_image($image, $width = 160, $height = 120, $return_type = "url") {
  $temp = pathinfo($image);
  $resize_image = $temp['filename'] . "_" . $width . "x" . $height . "." . $temp['extension'];

  if (file_exists(APPPATH . "../temp/" . $resize_image)) {
    if ($return_type == 'url') {
      return base_url("temp/" . $resize_image);
    } else {
      return "temp/" . $resize_image;
    }
  } else {
    $CI = &get_instance();
    $CI->load->library('image_lib');

    $config = array();
    $config['source_image'] = APPPATH . "../" . PICS_UPLOAD_DIRECTORY . $image;
    $config['new_image'] = APPPATH . "../temp/" . $resize_image;
    $config['maintain_ratio'] = TRUE;
    $config['master_dim'] = 'height';
    $config['width'] = $width;
    $config['height'] = $height;

    $CI->image_lib->initialize($config);

    if ($CI->image_lib->resize()) {
      if ($return_type == 'url') {
        return base_url("temp/" . $resize_image);
      } else {
        return "temp/" . $resize_image;
      }
    } else {
      if ($return_type == 'url') {
        return base_url(PICS_UPLOAD_DIRECTORY . $image);
      } else {
        return false;
      }
    }
  }
}

/**
 * Get Google Map Image of Address
 * 
 * @param type $address
 * @param type $width
 * @param type $height
 * @param type $zoom
 * @return type
 */
function my_get_addres_google_map_image($address, $width = 450, $height = 300, $zoom = 15) {
  $map_image = "temp/maps/" . $address->itude_lat . "_" . $address->itude_long . "_" . $width . "x" . $height . "_" . $zoom . ".jpg";
  if (file_exists(APPPATH . "../" . $map_image)) {
    $image_info = get_file_info(APPPATH . "../" . $map_image);
    if ($image_info['date'] > time() - 8640000) {
      return base_url($map_image . "?v=" . $image_info['date']);
    }
  }

  @unlink(APPPATH . "../" . $map_image);
  file_put_contents(APPPATH . "../" . $map_image, file_get_contents(my_get_google_map_image($address->itude_lat, $address->itude_long, $width, $height, $zoom)));

  return base_url($map_image . "?v=" . time());
  //return base_url("picsnow/google_map_image?lat=" . $address->itude_lat . "&long=" . $address->itude_long . "&w={$width}&h={$height}&zoom={$zoom}");
}

/**
 * Get Address URL
 * 
 * @param type $address
 * @return type
 */
function my_get_address_link($address) {
  $link = "";
  if ($address->street_number) {
    $link .= $address->street_number . "-";
  }
  $link .= str_replace(" ", "-", $address->address);
  $link .= $address->city . "-" . $address->state . "-" . $address->zipcode;
  $link .= "_" . $address->id;

  return base_url("picture/" . $link);
}

function my_ucfirst($str) {
  return ucfirst(strtolower($str));
}

/**
 * Get Modified Date of File
 * 
 * @param type $file
 * @return boolean
 */
function my_get_file_datetime($file) {
  $exif_data = exif_read_data($file);
  if (isset($exif_data['DateTimeOriginal']) && !empty($exif_data['DateTimeOriginal'])) {
    return $exif_data['DateTimeOriginal'];
  } elseif (isset($exif_data['FileDateTime']) && !empty($exif_data['FileDateTime'])) {
    return date("Y:m:d H:i:s", $exif_data['FileDateTime']);
  }
  return false;
}

/**
 * Get Symbol of Curreny Unit
 * 
 * @param type $symbol
 * @return string
 */
function my_get_currency_unit($symbol = DEFAULT_CURRENCY_UNIT, $show_type = SHOW_CURRENCY_UNIT) {
  if ($symbol == 'usd') {
    if ($show_type == 'icon') {
      return '<i class="fa fa-usd"></i>';
    } else {
      return '$';
    }
  } elseif ($symbol == 'peso') {
    if ($show_type == 'icon') {
      return '<i class="fa fa-dollar">(mxn)</i>';
    } else {
      return '$(MXN)';
    }
  }
}

/**
 * Print Symbol of Curreny Unit
 * 
 * @param type $symbol
 */
function my_show_currency_unit($symbol = DEFAULT_CURRENCY_UNIT, $output = true) {
  $symbol = my_get_currency_unit($symbol);
  if ($output) {
    echo $symbol;
  } else {
    return $symbol;
  }
}

/**
 * Send Email Function
 * 
 * @param type $to
 * @param type $subject
 * @param type $message
 * @param type $from_email
 * @param type $from_name
 * @return boolean
 */
function my_send_email($to, $subject, $email_template, $email_data, $from_name = FALSE, $from_email = FALSE) {
  if (!$from_email || !$from_name) {
    $from_email = CONTACT_EMAIL;
    $from_name = SITE_TITLE;
  }

  $CI = &get_instance();

  $message = "";
  $message .= $CI->load->view("emails/_header", '', true);
  $message .= $CI->load->view("emails/" . $email_template, $email_data, true);
  $message .= $CI->load->view("emails/_footer", '', true);

  $config = $CI->config->item('email');

  $CI->load->library('email', $config);
  $CI->email->from($from_email, $from_name);
  $CI->email->to($to);

  $CI->email->subject($subject);
  $CI->email->message($message);

  return $CI->email->send();
}

function my_set_system_message($message, $type) {
  $CI = &get_instance();

  $CI->session->set_userdata("picsnow_system_message_" . $type, $message);
}

function my_show_system_message($type) {
  $CI = &get_instance();

  $message = $CI->session->userdata("picsnow_system_message_" . $type);
  if ($message) {
    $CI->session->unset_userdata("picsnow_system_message_" . $type);

    my_show_msg($message, $type);
  }
}

function my_get_gps_from_image($image) {
  $exif_data = exif_read_data($image);

  if (isset($exif_data["GPSLongitude"]) && $exif_data["GPSLongitude"]) {
    $lon = my_get_gps($exif_data["GPSLongitude"], $exif_data['GPSLongitudeRef']);
    $lat = my_get_gps($exif_data["GPSLatitude"], $exif_data['GPSLatitudeRef']);

    return array("lat" => $lat, "long" => $lon);
  } else {
    return false;
  }
}

function my_get_gps($exifCoord, $hemi) {
  $degrees = count($exifCoord) > 0 ? my_gps_num($exifCoord[0]) : 0;
  $minutes = count($exifCoord) > 1 ? my_gps_num($exifCoord[1]) : 0;
  $seconds = count($exifCoord) > 2 ? my_gps_num($exifCoord[2]) : 0;

  $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

  return floatval($flip * ($degrees + $minutes / 60 + $seconds / 3600));
}

function my_gps_num($coordPart) {
  $parts = explode('/', $coordPart);

  if (count($parts) <= 0)
    return 0;

  if (count($parts) == 1)
    return $parts[0];

  return floatval($parts[0]) / floatval($parts[1]);
}

function my_show_amount($amount, $output = true) {
  $amount = number_format($amount, 2);
  $temp = explode(".", $amount);

  $amount = my_show_currency_unit(DEFAULT_CURRENCY_UNIT, false) . $temp[0] . "<sup>." . $temp[1] . "</sup>";
  if ($output) {
    echo $amount;
  } else {
    return $amount;
  }
}

function my_lang($line, $_ = NULL) {
  $str = lang("__" . $line);
  if (empty($str)) {
    $str = $line;
  }

  if (func_num_args() == 1) {
    return $str;
  }

  $args = func_get_args();
  $args[0] = $str;
  return call_user_func_array("sprintf", $args);
}

function my_echo_lang($line, $_ = NULL) {
  $args = func_get_args();

  $str = call_user_func_array("my_lang", $args);

  echo $str;
}

/**
 * Date Functions
 */
function my_add_date($add_day, $now = false, $format = "Y-m-d") {
  if ($now) {
    $now = strtotime($now);
  } else {
    $now = time();
  }

  return my_formart_datetime($now + $add_day * 86400, $format);
}

function my_formart_date($date = false, $format = "F j, Y") {
  if (!$date) {
    $time = strtotime("now");
  } else {
    $time = strtotime($date);
  }

  return my_formart_datetime($time, $format);
}

function my_formart_datetime($time = false, $format = "F j, Y") {
  if (DISPLAY_LANGUAGE == 'english') {
    return date($format, $time);
  }
  
  $show_month = false;
  if (strpos($format, "F") === FALSE) {
    if (strpos($format, "M") === FALSE) {
      
    } else {
      $format = str_replace("M", "xxxxx", $format);
      $show_month = "M";
    }
  } else {
    $format = str_replace("F", "xxxxx", $format);
    $show_month = "F";
  }

  $strdate = date($format, $time);
  if ($show_month) {
    $month = __(date('F', $time));
    if ($show_month == 'M') {
      $month = substr($month, 0, 3);
    }
    $strdate = str_replace("xxxxx", $month, $strdate);
  }
  return $strdate;
}

function my_diff_days($start, $end = 'now', $unit = 'd') {
  $diff = strtotime($end) - strtotime($start) + 1;
  return ceil($diff / 86400);
}

function my_show_html_text($text) {
  return str_replace("\n", "<br />", $text);
}

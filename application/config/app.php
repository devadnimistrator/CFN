<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Pages that need not login
$config['no_login_pages'] = array(
    "auth/signin",
    "auth/signout",
    "cron/reminder"
);

// Diretory path for upload image
define("PICS_UPLOAD_DIRECTORY", "uploads/");

// Image configurations for use in address
$config['address_pic'] = array(
    "upload_path" => "./" . PICS_UPLOAD_DIRECTORY,
    "allowed_types" => "gif|jpg|png|jpeg",
    "max_size" => 10000
);

$config['data_status'] = array(
    1 => "Normal",
    0 => "Blocked"
);

$config['invoices_status'] = array(
    'Pending',
    'Paid',
    'Canceled'
);

include_once dirname(__FILE__) . '/defines.php';

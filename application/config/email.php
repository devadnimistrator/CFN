<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['email'] =   array(  
//   'protocol' => 'smtp',
//   'smtp_host' => 'ssl://mail.ekrmexico.org.mx',
//   'smtp_port' => '587',
//   'smtp_timeout' => '7',
//   'smtp_user' => 'contacto@ekrmexico.org.mx',
//   'smtp_pass' => 'Tanatologa2015',
//   'newline' => "\r\n",
//   'validation' => TRUE,
    'protocol' => 'sendmail',
//    'mailpath' => '/usr/sbin/sendmail',
    'mailpath' => 'D:/sendmail/sendmail.exe',
    'mailtype' => 'html',
    'charset' => 'utf-8',
    'wordwrap' => TRUE
);

/* END OF FILE: email.php*/
/* LOCATION: ./config/email.php*/
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

define("ASSETS_VERSION", "2.1.0");
//define('DEFAULT_COUNTRY', 'US');

define('GOOGLE_MAP_API_KEY', 'XXX');

//define('DEFAULT_CURRENCY_UNIT', 'usd'); // or char
define('SHOW_CURRENCY_UNIT', 'icon'); // or char

define("PAYMENT_TYPE_INCOME", 1);
define("PAYMENT_TYPE_EXPENSES", 2);

define("PAYMENT_REASON_TYPE_EDUCATION", 1);
define("PAYMENT_REASON_TYPE_TANGIBLE", 2);
define("PAYMENT_REASON_TYPE_EXPENSES", 3);
define("PAYMENT_REASON_TYPE_INCOME", 4);
define("PAYMENT_REASON_TYPE_MOVEMENT", 5);

define("PAYMENT_STATUS_PAID", 1);     // paid
define("PAYMENT_STATUS_PENDING", 2);     // pending

define("PAYMENT_METHOD_BANK", 'bank');
define("PAYMENT_METHOD_CAJA", 'caja');

$config['paymnet_method_types'] = array(
    PAYMENT_METHOD_BANK => 'Bank Account',
    PAYMENT_METHOD_CAJA => 'Caja Chica'
);

define("DISCOUNT_TYPE_NO", 'no');
define("DISCOUNT_TYPE_FIXED", 'fixed');
define("DISCOUNT_TYPE_PERCENT", 'percent');
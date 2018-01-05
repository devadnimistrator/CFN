<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-10-31 18:54:54 --> Query error: Column 'invoice_id' cannot be null - Invalid query: INSERT INTO `foodnfue_payments` (`account_id`, `date`, `charge`, `description`, `invoice_id`, `id`, `deposit`) VALUES (23950, '2017-10-31 18:54', '0.00', 'Invoice #20170001', NULL, 0, 780.330143)
ERROR - 2017-10-31 19:20:20 --> Severity: Notice --> Undefined property: Invoices::$card_m E:\www\CFN\application\controllers\Invoices.php 108
ERROR - 2017-10-31 19:20:20 --> Severity: Error --> Call to a member function get_table() on a non-object E:\www\CFN\application\controllers\Invoices.php 108
ERROR - 2017-10-31 19:20:38 --> Query error: Unknown column 'card_ID_string' in 'IN/ALL/ANY subquery' - Invalid query: SELECT *
FROM `foodnfue_ptdatas`
WHERE `card_ID_string` IN (select card_id from foodnfue_cards where account_id=1000)
AND `date_completed` between `2017-10-01` AND 2017-10-02
ERROR - 2017-10-31 19:45:35 --> Severity: Notice --> Undefined property: Invoices::$ptdata_m E:\www\CFN\application\controllers\Invoices.php 101
ERROR - 2017-10-31 19:45:35 --> Severity: Notice --> Trying to get property of non-object E:\www\CFN\application\controllers\Invoices.php 101
ERROR - 2017-10-31 19:45:35 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE `id` = '62'' at line 2 - Invalid query: SELECT *
WHERE `id` = '62'
ERROR - 2017-10-31 19:45:57 --> Severity: Notice --> Undefined property: Invoices::$account_price_m E:\www\CFN\application\controllers\Invoices.php 108
ERROR - 2017-10-31 19:45:58 --> Severity: Error --> Call to a member function get_price_by_sitetype() on a non-object E:\www\CFN\application\controllers\Invoices.php 108
ERROR - 2017-10-31 19:46:21 --> Severity: Warning --> Creating default object from empty value E:\www\CFN\application\controllers\Invoices.php 122
ERROR - 2017-10-31 19:46:21 --> Severity: Error --> Call to undefined method stdClass::save() E:\www\CFN\application\controllers\Invoices.php 128
ERROR - 2017-10-31 19:47:27 --> Severity: Warning --> Creating default object from empty value E:\www\CFN\application\controllers\Invoices.php 123
ERROR - 2017-10-31 19:47:27 --> Severity: Error --> Call to undefined method stdClass::save() E:\www\CFN\application\controllers\Invoices.php 129
ERROR - 2017-10-31 19:47:45 --> Severity: Warning --> Creating default object from empty value E:\www\CFN\application\controllers\Invoices.php 123
ERROR - 2017-10-31 19:47:56 --> Severity: Warning --> Creating default object from empty value E:\www\CFN\application\controllers\Invoices.php 123
ERROR - 2017-10-31 19:55:19 --> Query error: Unknown column 'invoice_no' in 'where clause' - Invalid query: DELETE FROM `foodnfue_payments`
WHERE `invoice_no` = '20170001'
ERROR - 2017-10-31 19:58:56 --> Query error: Unknown column 'invoice_no' in 'where clause' - Invalid query: DELETE FROM `foodnfue_payments`
WHERE `invoice_no` = '2'
ERROR - 2017-10-31 20:20:19 --> Query error: Unknown column 'invoice_no' in 'where clause' - Invalid query: SELECT *
FROM `foodnfue_payments`
WHERE `invoice_no` = '20170001'
ERROR - 2017-10-31 20:40:16 --> Query error: Column 'state' cannot be null - Invalid query: INSERT INTO `foodnfue_invoices` (`invoice_no`, `date`, `account_id`, `start_pt_date`, `end_pt_date`, `total_price`, `calc_method`, `state`, `id`) VALUES ('20170011', '2017-10-31 20:39', '23860', '2017-10-01', '2017-10-31', '161.322', 'pump', NULL, NULL)
ERROR - 2017-10-31 20:43:37 --> Query error: Column 'state' cannot be null - Invalid query: INSERT INTO `foodnfue_invoices` (`invoice_no`, `date`, `account_id`, `start_pt_date`, `end_pt_date`, `total_price`, `calc_method`, `state`, `id`) VALUES ('2017004', '2017-10-31 20:43', '23860', '2017-10-01', '2017-10-31', '161.322', 'pump', NULL, NULL)
ERROR - 2017-10-31 20:44:57 --> Query error: Column 'state' cannot be null - Invalid query: INSERT INTO `foodnfue_invoices` (`invoice_no`, `date`, `account_id`, `start_pt_date`, `end_pt_date`, `total_price`, `calc_method`, `state`, `id`) VALUES ('20170002', '2017-10-31 20:44', '23860', '2017-10-01', '2017-10-31', '161.322', 'pump', NULL, NULL)
ERROR - 2017-10-31 20:48:00 --> Query error: Column 'invoice_id' cannot be null - Invalid query: INSERT INTO `foodnfue_payments` (`account_id`, `date`, `charge`, `description`, `invoice_id`, `id`, `deposit`) VALUES ('23860', '2017-10-31 20:47', '0.00', 'Invoice #20170008', NULL, NULL, '161.322')

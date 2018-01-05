<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-12-22 10:41:59 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '`a`
WHERE `date` between a.'2017-01-01' AND a.'2017-12-22'' at line 1 - Invalid query: DELETE FROM `foodnfue_invoicesas` `a`
WHERE `date` between a.'2017-01-01' AND a.'2017-12-22'
ERROR - 2017-12-22 10:42:24 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'as `a`
WHERE `date` between a.'2017-01-01' AND a.'2017-12-22'' at line 1 - Invalid query: DELETE FROM `foodnfue_invoices` as `a`
WHERE `date` between a.'2017-01-01' AND a.'2017-12-22'
ERROR - 2017-12-22 10:43:25 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'as `a`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'' at line 1 - Invalid query: DELETE FROM `foodnfue_invoices` as `a`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:44:38 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'as `a`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'' at line 1 - Invalid query: DELETE FROM `foodnfue_invoices` as `a`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:50:54 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:53:01 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:53:33 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'' at line 4 - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = 
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:53:47 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:54:01 --> Query error: Unknown column 'foodnfue_item.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `item` ON `a`.`id` = `foodnfue_item`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:56:04 --> Query error: Unknown column 'foodnfue_item.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `item` ON `a`.`id` = `foodnfue_item`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:56:40 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 10:59:18 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 11:16:03 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 11:16:52 --> Query error: Unknown column 'foodnfue_ABC.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `ABC` ON `a`.`id` = `foodnfue_ABC`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 11:17:36 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `ABC` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 11:20:55 --> Query error: Unknown column 'foodnfue_b.invoice_id' in 'on clause' - Invalid query: SELECT *
FROM `foodnfue_invoices` as `a`
LEFT JOIN `foodnfue_invoice_items` as `ABC` ON `a`.`id` = `foodnfue_b`.`invoice_id`
WHERE `a`.`date` between '2017-01-01' AND '2017-12-22'
ERROR - 2017-12-22 11:22:58 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'AND2017-12-22' at line 1 - Invalid query: SELECT * FROM `foodnfue_invoices` as `a` LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `b`.`invoice_id` WHERE `a`.`date` between 2017-01-01 AND2017-12-22
ERROR - 2017-12-22 11:23:08 --> Query error: No tables used - Invalid query: SELECT *
ERROR - 2017-12-22 11:23:15 --> Query error: No tables used - Invalid query: SELECT *
ERROR - 2017-12-22 11:23:24 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'AND2017-12-22' at line 1 - Invalid query: SELECT * FROM `foodnfue_invoices` as `a` LEFT JOIN `foodnfue_invoice_items` as `b` ON `a`.`id` = `b`.`invoice_id` WHERE `a`.`date` between 2017-01-01 AND2017-12-22
ERROR - 2017-12-22 11:24:22 --> Query error: No tables used - Invalid query: SELECT *
ERROR - 2017-12-22 11:24:52 --> Query error: No tables used - Invalid query: SELECT *
ERROR - 2017-12-22 11:48:14 --> Query error: No tables used - Invalid query: SELECT *

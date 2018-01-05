<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
  <title>PDF Download</title>
  <meta name="viewport">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  <style>
    body {
      font-size: 14px;
      font-family: NeutraText-Book !important;
    }
  </style>
</head>

<body>

<div class="contents">
  <table width="100%">
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="center" width="100%">
        <?php echo CONTACT_NAME; ?><br/>
        <?php echo CONTACT_ADDRESS; ?><br/>
        <?php echo CONTACT_CITY; ?> <?php echo CONTACT_STATE; ?> <?php echo CONTACT_ZIP; ?><br/>
        <?php echo CONTACT_PHONE; ?>
      </td>
    </tr>
  </table>

  <br>

  <table border=0 width="100%">
    <tr>
      <td width="33.33%">
        ACCT# <?php echo $invoice_info->header->account_num; ?><br>
        Date <?php echo $invoice_info->header->invoice_date; ?>
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td width="33.33%" align="center">
        INVOICE
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="right">
        No:&nbsp;<?php echo $invoice_info->header->invoice_no; ?>
      </td>
    </tr>
  </table>

  <br>

  <table border=0 width="100%">
    <tr>
      <td></td>
      <td>
        <?php echo $invoice_info->header->account_name; ?><br/>
        <?php echo $invoice_info->header->account_address; ?><br/>
        <?php echo $invoice_info->header->account_city; ?> <?php echo $invoice_info->header->account_state; ?> <?php echo $invoice_info->header->account_zip; ?>
        <br/>
        <?php echo $invoice_info->header->account_phone; ?>
      </td>
    </tr>
  </table>

  <br>

  <table width="100%">
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="center">
        TOTAL BUE ON PRESENTATION OF BILL<br/>
        PAST DUE IF NOT PAID BY THE 10TH AND SUBJECT TO LOCK OUT<br/>
        BALANCES NOT PAID BY THE 15TH, ARE SUBJECT TO A LATE CHARGE
      </td>
    </tr>
  </table>

  <br>


  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td align="right" valign="top" style="height: 25px;"><?php $page_row_numl; ?>Date</td>
      <td valign="top">Time</td>
      <td valign="top">Product</td>
      <td align="right" valign="top">Qty</td>
      <td></td>
      <td valign="top">Rate</td>
      <td align="right" valign="top">Sale</td>
      <td valign="top">Site</td>
      <td valign="top">H#</td>
      <td valign="top">Driver</td>
      <td valign="top" align="right">Odom</td>
    </tr>
  </table>

  <br>
  <?php
    $table_index = 0;
  ?>

  <?php foreach ($invoice_info->items->vehicles as $vehicle): ?>
    <?php for ($i = 0; $i < count($vehicle->products); $i ++) : ?>

      <tr style="height: 15px;">
        <td align="right" width="50"><?php echo $product->date; ?></td>
        <td width="40"><?php echo $product->time; ?></td>
        <td><?php echo $product->name; ?></td>
        <td align="right" width="40"><?php echo number_format($product->qty, 2); ?></td>
        <td width="1"></td>
        <td width="30"><?php echo number_format($product->rate, 2); ?></td>
        <td align="right" width="40"><?php echo number_format($product->sale, 2); ?></td>
        <td width="1"></td>
        <td width="40"><?php echo $product->site_numer; ?></td>
        <td width="20"><?php echo $product->pump_number; ?></td>
        <td width="50"><?php echo $product->driver; ?></td>
        <td align="right" width="40"><?php echo $product->odom; ?></td>
      </tr>

      <?php if ($i == 0): ?>
        <?php $table_index ++; ?>
        <tr>
          <td colspan="12">
            === VEHICLE&nbsp;&nbsp;&nbsp;&nbsp;#<?php echo $vehicle->vehicle_id; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            Desc: <?php echo $vehicle->vehicle_desc; ?>
          </td>
        </tr>
      <?php endif; ?>

      <?php if ($i == count($vehicle->products) - 1): ?>
        <?php $table_index += 2; ?>
        <tr>
          <td colspan="3" valign="top" style="height: 25px;border-top: 1px dotted #333;" align="center">subtotal</td>
          <td align="right" valign="top" style="height: 25px;border-top: 1px dotted #333;"><?php echo number_format($vehicle->sub_qty, 2); ?></td>
          <td colspan="2" style="height: 25px;border-top: 1px dotted #333;" align="center">&nbsp;</td>
          <td align="right" valign="top" style="height: 25px;border-top: 1px dotted #333;"><?php echo number_format($vehicle->sub_total, 2); ?></td>
          <td colspan="5" style="height: 25px;border-top: 1px dotted #333;"></td>
        </tr>
      <?php endif; ?>
    <?php endfor;?>
  <?php endforeach;?>

</div>
</body>
</html>
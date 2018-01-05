<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
  <head>
    <title></title>
    <meta name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
      body {
        font-size: 14px;
        font-family: NeutraText-Book !important;
      }
    </style>

    <?php if ($printable): ?>
        <style type="text/css">
          .page-contents {
            width: 740px;
            margin: 0 auto;
            padding: 60px 40px;
          }
        </style>

        <style type="text/css" media="print">
          @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
          }
        </style>
    <?php endif; ?>
  </head>

  <?php if ($printable): ?>
  <body onload="window.print();">
    <?php else : ?>
      <body>
    <?php endif; ?>
    <?php
    $page_break = true;
    $page_num = 1;
    $table_index = 0;
    ?>

    <?php foreach ($invoice_info->items->vehicles as $vehicle): ?>
        <?php for ($i = 0; $i < count($vehicle->products); $i ++) : ?>
            <?php
            $table_rows = $page_num == 1 ? 25 : 30;
            ?>
            <?php if ($page_break): ?>
                <?php if ($page_num > 1) : ?>
                  </div>
                  <div style="page-break-after: always;"></div>
              <?php endif; ?>

              <div class="page-contents" style="page-break-inside:avoid;">
                <?php
                $page_break = false;
                include 'pdf_header.php';
                ?>

                <?php if ($page_num == 1) : ?>
                    <table width="100%">
                      <tr>
                        <td width="100%">
                          <table border=0 width="100%">
                            <tr>
                              <td>&nbsp;</td>
                              <td>
                                <?php echo $invoice_info->header->account_name; ?><br/>
                                <?php echo $invoice_info->header->account_address; ?><br/>
                                <?php echo $invoice_info->header->account_city; ?> <?php echo $invoice_info->header->account_state; ?> <?php echo $invoice_info->header->account_zip; ?><br/>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                    <br/>

                    <table width="100%">
                      <tr>
                        <td align="center">
                          <?php echo DEFAULT_INVOICE_HEADER_TEXT ?>
                        </td>
                      </tr>
                    </table>

                    <br/><br/>
                <?php endif; ?>

                <table width="100%" CELLSPACING=0 CELLPADDING=3>
                  <tr>
                    <td align="right" valign="top" style="height: 25px;"><?php $page_row_numl; ?>Date</td>
                    <td valign="top">Time</td>
                    <td valign="top">Product</td>
                    <td style="padding-left: 1.3em" valign="top">Qty</td>
                    <td></td>
                    <td valign="top">Rate</td>
                    <td style="padding-left: 1.3em" valign="top">Sale</td>
                    <td></td>
                    <td valign="top">Site</td>
                    <td valign="top">Type</td>
                    <td valign="top">H#</td>
                    <td valign="top">Driver</td>
                    <td valign="top" align="right">Odom</td>
                  </tr>
              <?php endif; ?>

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

              <?php
              $table_index ++;
              $product = $vehicle->products[$i];
              ?>
              <tr style="height: 15px;">
                <td align="right" width="50"><?php echo $product->date; ?></td>
                <td width="40"><?php echo $product->time; ?></td>
                <td><?php echo $product->name; ?></td>
                <td align="right" width="40"><?php echo number_format(round($product->qty, 3),3,'.',','); ?></td>
                <td width="1"></td>
                <td width="30"><?php echo "$".number_format(round($product->rate, 3),3,'.',','); ?></td>
                <td align="right" width="40"><?php echo "$".number_format(round($product->sale, 2),2,'.',','); ?></td>
                <td width="1"></td>
                <td width="30"><?php echo $product->site_numer; ?></td>
                <td width="20"><?php echo $product->site_type; ?></td>
                <td align="center" width="20"><?php echo $product->pump_number; ?></td>
                <td align="right" width="30"><?php echo $product->driver; ?></td>
                <td align="right" width="30"><?php echo number_format($product->odom,0,'.',','); ?></td>
              </tr>

              <?php if ($i == count($vehicle->products) - 1): ?>
                  <?php $table_index += 2; ?>
                  <tr>
                    <td colspan="3" valign="top" style="height: 25px;border-top: 1px dotted #333;" align="center">subtotal</td>
                    <td align="right" valign="top" style="height: 25px;border-top: 1px dotted #333;"><?php echo number_format($vehicle->sub_qty, 3,'.',','); ?></td>
                    <td colspan="2" style="height: 25px;border-top: 1px dotted #333;" align="center">&nbsp;</td>
                    <td align="right" valign="top" style="height: 25px;border-top: 1px dotted #333;"><?php echo "$".number_format($vehicle->sub_total, 2,'.',','); ?></td>
                    <td colspan="6" style="height: 25px;border-top: 1px dotted #333;"></td>
                  </tr>
              <?php endif; ?>

              <?php
              if ($table_index >= $table_rows)
              {
                  $table_index = 0;
                  $page_num ++;
                  $page_break = true;
              }
              ?>

              <?php if ($page_break): ?>
                </table>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endforeach; ?>

    <?php if ($page_break): ?>
      </div>
      <div style="page-break-after: always;"></div>
      <div class="contents" style="page-break-inside:avoid;">
        <?php
        $page_num ++;
        include 'pdf_header.php';
        ?>
    <?php else: ?>
      </table>
  <?php endif; ?>
  <br/>
  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td width="150">&nbsp;</td>
      <td width="150">Total New Sales</td>
      <td width="150" align="right"><?php echo "$".number_format($invoice_info->footer->total_price, 2,'.',','); ?></td>
      <td>&nbsp;</td>
    </tr>
  </table>

  <!-- discount start -->
  <?php
    if ($invoice_info->footer->discount != 0) {
      $discount_amount = $invoice_info->footer->total_price * ( ($invoice_info->footer->discount)/100);
    } else {
      $discount_amount = 0;
    }
  ?>
  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td width="150">&nbsp;</td>
      <td width="150"><?php if($invoice_info->footer->discount != 0) {
          echo $invoice_info->header->invoice_date . "   " . $invoice_info->footer->discount . "% discount ";
        }
        ?></td>
      <td width="150" align="right"><?php
        if($invoice_info->footer->discount != 0) {
          echo "$".number_format(($discount_amount)  , 2,'.',',');
        }

        ?></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <!-- discount end -->

  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td width="50">&nbsp;</td>
      <td width="400">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php if (isset($invoice_info->footer->before_balance->date)): ?>
        <tr>
          <td><?php echo $invoice_info->footer->before_balance->date; ?></td>
          <td>Previous Balance</td>
          <td>$<?php echo number_format($invoice_info->footer->before_balance->balance, 2); ?></td>
        </tr>
    <?php endif; ?>
    <?php if(!isset($invoice_info->footer->before_balance->date)): ?>
      <tr>
        <td></td>
        <td>Previous Balance</td>
        <td>$0.00</td>
      </tr>
    <?php endif; ?>
    <?php if (isset($invoice_info->footer->payment_histories)): ?>
        <?php foreach ($invoice_info->footer->payment_histories as $history): ?>
            <tr>
              <td><?php echo $history->date; ?></td>
              <td>Payment - Thank you - <?php echo $history->descirption; ?></td>
              <td>$<?php echo number_format($history->amount, 2,'.',','); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
  </table>
  <br/>
  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td colspan="4">
        ******&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sales Summary&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;******
      </td>
    </tr>
    <?php if (isset($invoice_info->items->products)): ?>
        <?php foreach ($invoice_info->items->products as $product): ?>
            <tr>
              <td width="150"><?php echo $product->name; ?></td>
              <td width="50" align="right"><?php echo number_format($product->qty, 3,'.',','); ?></td>
              <td width="70" align="right">$<?php echo number_format($product->sale, 2,'.',','); ?></td>
              <td></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
  </table>
  <br/>
  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td align="center">
        <?php
          $pbalance = 0;
          $pbalance_date = '0000-00-00';
          if ($invoice_info->footer->pbalance) {
            foreach ($invoice_info->footer->pbalance as $data) {
              $pbalance = $data->balance;
              $pbalance_date = $data->date;
            }
          }
        ?>

        CURRENT = <?php echo "$".(number_format($invoice_info->footer->current_balance, '2','.',',')); ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        30-DAYS = <?php
//          if ($invoice_info->footer->before_30_date > $pbalance_date && $pbalance_date != '0000-00-00') {
//            echo "$".($invoice_info->footer->before_30_balance ? number_format($invoice_info->footer->before_30_balance->sum_deposit - $invoice_info->footer->before_30_balance->sum_charge + $pbalance - $invoice_info->footer->before_30_payments, 2,'.',',') : number_format(0,2,'.',','));
//          } else {
//            echo "$".($invoice_info->footer->before_30_balance ? number_format($invoice_info->footer->before_30_balance->sum_deposit - $invoice_info->footer->before_30_balance->sum_charge - $invoice_info->footer->before_30_payments, 2,'.',',') : number_format(0,2,'.',','));
//          }
          echo "$".number_format($invoice_info->footer->before_30_balance,'2','.',',');
          ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        60-DAYS = <?php
//          if ($invoice_info->footer->before_60_date > $pbalance_date && $pbalance_date != '0000-00-00') {
//            echo "$".($invoice_info->footer->before_60_balance ? number_format($invoice_info->footer->before_60_balance->sum_deposit - $invoice_info->footer->before_60_balance->sum_charge + $pbalance - $invoice_info->footer->before_60_payments, 2,'.',',') : number_format(0,2,'.',','));
//          } else {
//           echo "$".($invoice_info->footer->before_60_balance ? number_format($invoice_info->footer->before_60_balance->sum_deposit - $invoice_info->footer->before_60_balance->sum_charge - $invoice_info->footer->before_60_payments, 2,'.',',') : number_format(0,2,'.',','));
//          }
          echo "$".number_format($invoice_info->footer->before_60_balance,'2','.',',');
        ?>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        90-DAYS = <?php
//          if ($invoice_info->footer->before_90_date > $pbalance_date && $pbalance_date != '0000-00-00') {
//            echo "$".($invoice_info->footer->before_90_balance ? number_format($invoice_info->footer->before_90_balance->sum_deposit - $invoice_info->footer->before_90_balance->sum_charge + $pbalance - $invoice_info->footer->before_90_payments, 2,'.',',') : number_format(0,2,'.',','));
//          } else {
//            echo "$".($invoice_info->footer->before_90_balance ? number_format($invoice_info->footer->before_90_balance->sum_deposit - $invoice_info->footer->before_90_balance->sum_charge - $invoice_info->footer->before_90_payments, 2,'.',',') : number_format(0,2,'.',','));
//          }
          echo "$".number_format($invoice_info->footer->before_90_balance,'2','.',',');
          ?>
      </td>
    </tr>
  </table>
  <br/>
  <table width="100%" CELLSPACING=0 CELLPADDING=3>
    <tr>
      <td align="center">
        <h3>INVOICE AMOUNT&nbsp;&nbsp;&nbsp;$ <?php echo number_format(($invoice_info->footer->total_price - $discount_amount), 2,'.',','); ?></h3>
      </td>
    </tr>
  </table>

</div>
</body>
</html>
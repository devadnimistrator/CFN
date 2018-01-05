<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
  <title></title>
  <meta name="viewport">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  <style>
    body {
      font-size: 14px;
      font-family: NeutraText-Book !important;
      width: 740px;
    }

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
</head>

<body onload="window.print()">

<?php
$page = 1;
$page_count = 0;
$billing_count = $header['billing_count'];
$page_count = ceil(($billing_count + 20) / 40);

$billing_index = -1;

$total_quantity = 0;
$total_charge = 0;
?>

<?php for ($page = 1; $page <= $page_count; $page++) : ?>
  <div class="page-contents" style="page-break-inside:avoid;">

    <table width="100%">
      <tr>
        <td align="left" width="100%">
          <b>Print MONTHEND Invoice Log Summary <?php echo my_formart_date($print_date, 'l, F j, Y'); ?></b>
        </td>
      </tr>
    </table>

    <br/>

    <table width="100%">
      <tr>
        <td align="left" width="100%" colspan="2">
          Date of Last Inovice : <?php echo my_formart_date($header['last_invoice_date'],'m/d/Y'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" width="60%">
          Billing Code*
        </td>
        <td align="right">
          Page <?php echo $page; ?>
        </td>
      </tr>
    </table>

    <br/>

    <table width="100%">
      <tr>
        <td width="90px" valign="bottom">ACCT#</td>
        <td valign="bottom">Name</td>
        <td width="100px" valign="bottom">Inovice No</td>
        <td width="70px" align="right" valign="bottom">Inovice Amount</td>
        <td width="70px" align="right" valign="bottom">Quantity Sold</td>
        <td width="70px" align="right" valign="bottom">$New Charge</td>
      </tr>

      <?php if ($billing_index < $billing_count - 1): ?>
      <tr>
        <td colspan="6">
          <hr/>
        </td>
      </tr>
      <?php endif; ?>

      <?php
        $page_number = $page == $page_count ? 28 : 35;
      ?>
      <?php for ($i = 0; $i < $page_number && $billing_index < $billing_count - 1; $i++): ?>
        <?php
        $billing_index ++;
        $total_quantity +=  floatval(preg_replace('/[^\d.]/', '', ($data[$billing_index]['quantity_sold'])));
        $total_charge += floatval(preg_replace('/[^\d.]/', '', ($data[$billing_index]['new_charge'])));
        ?>
        <tr>
          <td><?php echo $data[$billing_index]['account']; ?></td>
          <td><?php echo $data[$billing_index]['name']; ?></td>
          <td><?php echo $data[$billing_index]['invoice_no']; ?></td>
          <td align="right">$<?php echo $data[$billing_index]['invoice_amount']; ?></td>
          <td align="right"><?php echo $data[$billing_index]['quantity_sold']; ?></td>
          <td align="right">$<?php echo $data[$billing_index]['new_charge']; ?></td>
        </tr>
      <?php endfor; ?>

      <?php if ($page == $page_count) : ?>
        <tr>
          <td colspan="6" align="right">
            <hr style="width: 100%; float: right;"/>
          </td>
        </tr>
        <tr>
          <td colspan="3" align="right">Column Total =&nbsp;</td>
          <td align="right">$<?php echo $header['total_amount']; ?></td>
          <td align="right"><?php echo number_format($total_quantity, 3,'.',','); ?></td>
          <td align="right">$<?php echo number_format($total_charge, 2,'.',','); ?></td>
        </tr>
      <?php endif; ?>
    </table>

    <?php if ($page == $page_count) : ?>
    <br/><br/>

    <table width="100%">
      <tr>
        <td align="left" width="100%">
          Total # of ACCOUNT For This Billing Code: * = <?php echo $header['billing_count']; ?>
        </td>
      </tr>
    </table>

    <br/>

    <table width="100%">
      <tr>
        <td align="left" width="100%">
          &nbsp;&nbsp;&nbsp;
          '*' ==> No activity since last invoice
        </td>
      </tr>
    </table>

    <br/>

    <table width="27%">
      <tr>
        <td align="right">
          Beginning INVOICE#: <?php echo $header['beginning_invoice_no']; ?>
        </td>
      </tr>
      <tr>
        <td align="right" width="100%">
          &nbsp;&nbsp;&nbsp;Ending INVOICE#: <?php echo $header['ending_invoice_no']; ?>
        </td>
      </tr>
    </table>

    <br/>

    <table width="100%">
      <tr>
        <td width="180px">
          Number INOVICE genereted =
        </td>
        <td>
          <?php echo $header['generated_number']; ?>
        </td>
      </tr>
      <tr>
        <td>
          Total Amount =
        </td>
        <td>
          $<?php echo $header['total_amount']; ?>
        </td>
      </tr>
    </table>

    <br/>

    <table width="100%">
      <tr>
        <td width="30px" align="right" nowrap="nowrap">
          High =
        </td>
        <td>
          $<?php echo number_format($header['max_invoice']['total_price'],2,'.',','); ?> (ACCT
          #: <?php echo str_pad($header['max_invoice']['account'], 6, "0", STR_PAD_LEFT); ?> <?php echo $header['max_invoice']['name']; ?>)
        </td>
      </tr>
      <tr>
        <td align="right" nowrap="nowrap">
          Low =
        </td>
        <td>
          $<?php echo number_format($header['min_invoice']['total_price'],2,'.',','); ?> (ACCT
          #: <?php echo str_pad($header['min_invoice']['account'], 6, "0", STR_PAD_LEFT); ?> <?php echo $header['min_invoice']['name']; ?>)
        </td>
      </tr>
      <tr>
        <td align="right" nowrap="nowrap">
          &nbsp;AVG =
        </td>
        <td>
          $<?php echo $header['avg_amount']; ?>
        </td>
      </tr>
    </table>

    <br/>
    <table width="100%">
      <tr>
        <td align="center">
          <b>===== End Report =====</b>
        </td>
      </tr>
    </table>

    <?php endif; ?>

  </div>
  <div style="page-break-after: always;"></div>

<?php endfor; ?>

</body>
</html>
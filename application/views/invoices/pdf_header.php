<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<table width="100%">
  <tr>
    <td align="center" width="100%">
      <?php echo CONTACT_NAME; ?><br/>
      <?php echo CONTACT_ADDRESS; ?><br/>
      <?php echo CONTACT_CITY; ?> <?php echo CONTACT_STATE; ?> <?php echo CONTACT_ZIP; ?><br/>
      <?php echo CONTACT_PHONE; ?>
    </td>
  </tr>
</table>

<br/>

<table width="100%">
  <tr>
    <td width="100%">
      <table border=0 width="100%">
        <tr>
          <td width="33.33%">
            ACCT# <?php echo $invoice_info->header->account_num; ?><br>
            Date <?php echo $invoice_info->header->invoice_date; ?>
          </td>
          <td width="33.33%" align="center">
            INVOICE
          </td>
          <td align="right">
            No:&nbsp;<?php echo $invoice_info->header->invoice_no; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Pg.<?php echo $page_num; ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br/>
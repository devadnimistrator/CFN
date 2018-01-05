<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary" onclick="print_report()"><?php ___("Print"); ?> <i class="fa fa-print"></i></button>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 3%">
    <h4><b>Print MONTHED Invoice Log Summary</b>
      <small>
        <input type="text" id="em-pt_date" name="pt_date" value="<?php echo my_formart_date($month,'Y-m-01'); ?>" class="date-picker" style="width: 100px; font-size: 18px; margin-left: 1%; margin-top: 0.2%"/>
      </small>
    </h4>
    <h4>Date of Last Invoice : <?php echo $last_invoice_date?></h4>
<!--    <h4>Billing Code =* </h4>-->
    <h4> Total # of ACCOUNT for This Billing Code:* = <?php echo $billing_count; ?></h4>
<!--    <h4> '*' ==> No activity since last invoice </h4>-->
    <h4>Beginning INVOICE#: <?php echo $beginning_invoice_no; ?></h4>
    <h4>Ending INVOICE#: <?php echo $ending_invoice_no; ?></h4>
    <h4>Number of INVOICE generated = <?php echo $generated_number; ?></h4>
    <h4>Total Amount = $ <?php echo $total_amount; ?></h4>
    <h4>High = $ <?php echo $max_invoice['total_price']; ?> ( ACCT #: <?php echo $max_invoice['account']; ?> <?php echo $max_invoice['name']; ?> )</h4>
    <h4>Low = $ <?php echo $min_invoice['total_price']; ?> ( ACCT #: <?php echo $min_invoice['account']; ?> <?php echo $min_invoice['name']; ?> )</h4>
    <h4>AVG = $ <?php echo $avg_amount;?> </h4>
  </div>
</div>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_content">
        <table id="table-reports" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
            <th><?php ___("Account ID") ?>:</th>
            <th><?php ___("Account Name") ?>:</th>
<!--            <th>--><?php //___("Invoice Date") ?><!--:</th>-->
            <th><?php ___("Invoice No") ?>:</th>
            <th><?php ___("Invoice Amount") ?>:</th>
            <th><?php ___("Quantity Sold") ?>:</th>
            <th><?php ___("New Charge") ?>:</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<iframe style="width: 0; height: 0; border: 0;" id="print_window"></iframe>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var $tablereports;
  $(document).ready(function () {
    $tablereports = $('#table-reports').DataTable({
      columns: [
        {"data": "index"},
        {"data": "account"},
        {"data": "name"},
//        {"data": "date"},
        {"data": "invoice_no"},
        {"data": "invoice_amount"},
        {"data": "quantity_sold"},
        {"data": "new_charge"}
      ],
      order: [[1, "asc"]],
      bInfo: false,
      paging:   false,
      filter:   false,
      ajax: {
        url: "<?php echo base_url("reports/ajax_get_invoices_result/" . $month); ?>",
        type: 'POST'
      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
    });
  });
  
  function print_report(month) {
    var print_date = "";
    print_date = $("#em-pt_date").val();
    document.getElementById("print_window").src = "<?php echo base_url("reports/export_print/".$month); ?>/" + print_date;
//    location.href="<?php //echo base_url("reports/export_print/".$month); ?>///" + print_date;
  }

</script>
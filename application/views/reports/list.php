<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
</div>
<div class="clearfix"></div>
<div style="text-align: center">
  <h3><?php echo date("Y")?> Transactions: <?php echo $this->transactions ?></h3>
</div>

<?php
$company_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Report List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-reports" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
<!--            <th>--><?php //___("Date") ?><!--:</th>-->
            <th><?php ___("Month") ?>:</th>
            <th><?php ___("Start Invoice ID") ?>:</th>
            <th><?php ___("End Invoice ID") ?>:</th>
            <th><?php ___("Total Amount") ?>:</th>
            <th><?php ___("Average Amount") ?>:</th>
<!--            <th>--><?php //___("Generated Number") ?><!--:</th>-->
            <th style="background-color: rgb(38, 154, 188); width: 71px;"><?php ___("Transactions") ?>:</th>
            <th class="nosort"><?php ___("Actions") ?>:</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var $tablereports;
  $(document).ready(function () {
    $tablereports = $('#table-reports').DataTable({
      columns: [
        {"data": "index"},
//        {"data": "date"},
        {"data": "month"},
        {"data": "start_invoice_id"},
        {"data": "end_invoice_id"},
        {"data": "total_amount"},
        {"data": "average_amount"},
//        {"data": "generated_number"},
        {"data": "transactions"},
        {"data": "actions"}
      ],
      order: [[4, "desc"]],
      processing: true,
      serverSide: false,
      aLengthMenu: [20,50,100],
      ajax: {
        url: "<?php echo base_url("reports/ajax_get_reports"); ?>",
        type: 'POST'
      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
      responsive: true,
//      createdRow: function (row, data, dataIndex) {
//        $(row).unbind("click").bind("click", function () {
//          selected_account_id = data.account_id;
//          $("#account_id").html(data.account_id + " - " + data.account_name);
//
//          $tableHistoris.ajax.reload(function () {
//          }, true);
//        });
//      }
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.clear();
    $tableUsers.ajax.reload(function () {
    }, true);
  }

  function delete_data(data_id) {
    if (confirm("<?php ___("Are you sure delete selected data?"); ?>")) {
      $.get("<?php echo base_url('ptdatas/ajax_delete') ?>/" + data_id, function () {
        reloadTable(false);
      });
    }
  }
</script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>Payments</h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/checkout"); ?>'"><?php ___("New Payment"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<?php
my_show_system_message("success");
?>

<div class="row">
  <div class="col-md-6">
    <div class="x_panel">
      <div class="x_title">
        <h2>
          Account's balances
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-balance" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th><?php ___("Lasted") ?>:</th>
              <th><?php ___("Account") ?>:</th>
              <th><?php ___("Charge") ?>:</th>
              <th><?php ___("Expense") ?>:</th>
              <th><?php ___("Balance") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="x_panel">
      <div class="x_title">
        <h2>
          Payment Histories of account #<span id="account_id"></span>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-histories" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><?php ___("Date") ?>:</th>
              <th class="nosort"><?php ___("Charge") ?>:</th>
              <th class="nosort"><?php ___("Expense") ?>:</th>
              <th class="nosort"><?php ___("Description") ?>:</th>
              <th class="nosort" width="60"></th>
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
    var $tableBalances;
    $(document).ready(function () {
      $tableBalances = $('#table-balance').DataTable({
        columns: [
          {"data": "lasted"},
          {"data": "account_name"},
          {"data": "charge"},
          {"data": "deposit"},
          {"data": "balance"}
        ],
        order: [[4, "desc"]],
        processing: true,
        serverSide: false,
        ajax: {
          url: "<?php echo base_url("payments/ajax_get_balances"); ?>",
          type: 'POST'
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['nosort']
          }],
        responsive: true,
        createdRow: function (row, data, dataIndex) {
          $(row).unbind("click").bind("click", function () {
            selected_account_id = data.account_id;
            $("#account_id").html(data.account_id + " - " + data.account_name);

            $tableHistoris.ajax.reload(function () {
            }, true);
          });
        }
      });
    });


    var $tableHistoris;
    var selected_account_id = "";
    $(document).ready(function () {
      $tableHistoris = $('#table-histories').DataTable({
        columns: [
          {"data": "date"},
          {"data": "charge"},
          {"data": "deposit"},
          {"data": "description"},
          {"data": "actions"}
        ],
        order: [[0, "desc"]],
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url("payments/ajax_get_histories"); ?>",
          type: 'POST',
          data: function (d) {
            var searchParams = {
              "account_id": selected_account_id
            };
            searchParams = $.extend(searchParams, d);

            return searchParams;
          }
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['nosort']
          }],
        responsive: true,
        createdRow: function (row, data, dataIndex) {
//          $(row).unbind("click").bind("click", function () {
//            $("#account_id").html(data.account_id + " - " + data.account_name);
//          });
        }
      });
    });

    function delete_payment(id) {
      if (confirm("Are you sure deleted?")) {
        $.get("<?php echo base_url('payments/ajax_delete') ?>/" + id, function () {
          $tableBalances.ajax.reload(function () {
          }, true);

          $tableHistoris.ajax.reload(function () {
          }, true);
        });
      }
    }
</script>
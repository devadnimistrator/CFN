<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-success btn-sm" onclick="all_print()"><?php ___("Print All"); ?></button>
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("invoices/add"); ?>'"><?php ___("Prepare New Invoice"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<?php
my_show_system_message("success");
?>

<div class="row">
  <div class="col-md-2 col-sm-3 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Search Filters"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
          "name" => "searchForm",
          "col_width" => 0,
          "buttons" => array(
            array(
              "type" => "button",
              "value" => __("Search"),
              "options" => array(
                "class" => "btn btn-primary",
                "onClick" => "reloadTable(true)"
              )
            )
          )
        );
        $bsForm = new My_bs_form($formConfig);
        $bsForm->add_element("account_id", BSFORM_SELECT, $default_account_id, array("options" => $accounts, "label" => "Account"));
        $bsForm->add_element("start_date", BSFORM_DATE, '', array("label" => "Start Date"));
        $bsForm->add_element("end_date", BSFORM_DATE, '', array("label" => "End Date"));
        $bsForm->add_element("state", BSFORM_SELECT, "all", array("options" => $states));
        $bsForm->generate();
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-10 col-sm-9 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Invoices"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-invoices" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th><?php ___("No") ?>:</th>
            <th><?php ___("Date") ?>:</th>
            <th class="nosort"><?php ___("Transaction") ?>:</th>
            <th class="nosort"><?php ___("Print") ?>:</th>
            <th class="nosort"><?php ___("Amount") ?>:</th>
            <th class="nosort"><?php ___("Status") ?>:</th>
            <th class="nosort"><?php ___("Actions") ?>:</th>
            <th class="hidden"><?php ___("Inovice ID") ?>:</th>
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
  var $tableUsers;
  var invoiceIds = [];
  $(document).ready(function () {
    $tableUsers = $('#table-invoices').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "invoice_no"},
        {"data": "date"},
        {"data": "transaction"},
        {"data": "invoice"},
        {"data": "amount"},
        {"data": "state"},
        {"data": "actions"},
//          {"data": "invoice_id"}

      ],
      order: [[0, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("invoices/ajax_find"); ?>",
        type: 'POST',
        data: function (d) {
          var searchForm = document.searchForm;
          var searchParams = {
            "account_id": searchForm.account_id.value,
            "start_date": searchForm.start_date.value,
            "end_date": searchForm.end_date.value,
            "state": searchForm.state.value,
          }
          searchParams = $.extend(searchParams, d);

          return searchParams;
        }
      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
      responsive: true,
      bFilter: false,
      lengthMenu: [100,200],
//        bLengthChange: false,
      createdRow: function (row, data, index) {
        invoiceIds.push(data['invoice_id']);
      },
    });

    $("#em-account_id").change(function () {
      reloadTable(true);
    });

    $("#em-state").change(function () {
      reloadTable(true);
    });
  });

  function reloadTable(resetPaging) {
    printInvoiceIndex = 0;
    invoiceIds = [];
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_invoice(invoice_id) {
    if (confirm("<?php ___("Are you sure delete selected invoice?"); ?>")) {
      $.get("<?php echo base_url('invoices/ajax_delete') ?>/" + invoice_id, function () {
        reloadTable(false);
      });
    }
  }

  function invoice_print(invoice_id) {
    document.getElementById("print_window").src = "<?php echo base_url('invoices/export_print') ?>/" + invoice_id;
  }

  var printTimer = null;
  var printInvoiceIndex = 0;
  function all_print() {
    printInvoiceIndex = 0;

    one_print();
  }

  function one_print() {
    if (printInvoiceIndex == invoiceIds.length) {
      return;
    }

    invoice_print(invoiceIds[printInvoiceIndex]);
    printInvoiceIndex ++;

    if (printTimer) {
      clearTimeout(printTimer);
      printTimer = null;
    }

    setTimeout(function() {
      one_print();
    }, 5000);
  }
</script>
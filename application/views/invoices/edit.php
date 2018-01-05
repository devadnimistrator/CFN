<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
  .x_content .form-horizontal .control-label {
    text-align: left !important;
  }
</style>

<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url('ptdatas/add'); ?>'"><?php ___("Add PTdata"); ?></button>
      <button type="button" class="btn btn-round" onclick="location.href = '<?php echo base_url("invoices"); ?>'"><?php ___("Back"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<?php
  my_show_system_message("danger");
?>

<div class="row">
  <div class="col-md-2 col-sm-3 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Invoice #<?php echo $this->invoice_m->invoice_no; ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
          "name" => "newInvoice",
          "autocomplete" => false,
          "col_width" => 12
        );
        $this->invoice_m->form_create($formConfig);
        $this->invoice_m->form_add_element("invoice_no", array('label' => 'Invoice Number'));
        $this->invoice_m->form_add_element("date", array('type' => 'datetime'));
        $this->invoice_m->form_add_element("account_id", array('readonly' => "readonly"));
        $this->invoice_m->form_add_element("start_pt_date", array('onchange' => 'researchPT()'));
        $this->invoice_m->form_add_element("end_pt_date", array('onchange' => 'researchPT()'));
        $this->invoice_m->form_add_element("calc_method", array("type" => 'select', 'options' => array("pump" => "Pump Price", "cfn" => "CFN Price"), 'onchange' => 'researchPT()'));
        $this->invoice_m->form_add_element("total_price", array('readonly' => "readonly"));
        $this->invoice_m->form_add_element("ptdata_ids", array('type' => "hidden"));
        $this->invoice_m->form_add_element("invoice_id", array('value' => $invoice_id, 'type' => 'hidden'));
        $this->invoice_m->form_generate();
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-10 col-sm-9 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Invoice Items"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-invoices" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
            <th><?php ___("Date") ?>:</th>
            <th class="nosort"><?php ___("Time") ?>:</th>
            <th class="nosort"><?php ___("Product") ?>:</th>
            <th class="nosort"><?php ___("Qty") ?>:</th>
            <th class="nosort"><?php ___("Rate") ?>:</th>
            <th class="nosort"><?php ___("Sale") ?>:</th>
            <th class="nosort"><?php ___("Site ID") ?>:</th>
            <th class="nosort"><?php ___("Card ID") ?>:</th>
            <th class="nosort"><?php ___("Odom") ?>:</th>
            <th class="nosort" class="nosort"><?php ___("Actions") ?>:</th>
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
  var $tableUsers = false;
  var totalPrice = 0;
  var searchParams = {
    'account_id': "",
    'start_pt_date': "",
    'end_pt_date': ""
  };
  $(document).ready(function () {
    $("#em-account_id").change(function() {
      researchPT();
    });

    $("#em-start_pt_date").change(function() {
      researchPT();
    });

    $("#em-end_pt_date").change(function() {
      researchPT();
    });

    $("#em-calc_method").change(function() {
      researchPT();
    });

    setTimeout(function() {
      $tableUsers = $('#table-invoices').DataTable({
        language: {
          "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
        },
        columns: [
          {"data": "index"},
          {"data": "date"},
          {"data": "time"},
          {"data": "product"},
          {"data": "qty"},
          {"data": "rate"},
          {"data": "sale"},
          {"data": "site_id"},
          {"data": "driver"},
          {"data": "odom"},
          {"data": "actions"}
        ],
        order: [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?php echo base_url("invoices/ajax_get_by_invoice"); ?>",
          type: 'POST',
          data: function (d) {
            var newInvoice = document.newInvoice;
            var searchParams = {
              "account_id": newInvoice.account_id.value,
              "start_pt_date": newInvoice.start_pt_date.value,
              "end_pt_date": newInvoice.end_pt_date.value,
              "invoice_id": <?php echo $invoice_id?>,
              "calc_method": newInvoice.calc_method.value
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
        paging: false,
        createdRow: function (row, data, index) {
          if (document.newInvoice.ptdata_ids.value.length == 0) {
            document.newInvoice.ptdata_ids.value = data['id'];
          } else {
            document.newInvoice.ptdata_ids.value += ("," + data['id']);
          }

          totalPrice += 1 * data['sale'];
          document.newInvoice.total_price.value = parseFloat(totalPrice).toFixed(3);
        }
      });
    }, 500);
  });

  function reloadTable(resetPaging) {
    document.newInvoice.ptdata_ids.value = '';
    totalPrice = 0;
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function researchPT() {
    if ($tableUsers) {
      reloadTable(true);
    }
  }

  function delete_ptdata(data_id) {
    if (confirm("<?php ___("Are you sure delete selected data?"); ?>")) {
      $.get("<?php echo base_url('ptdatas/ajax_delete') ?>/" + data_id, function () {
        reloadTable(false);
      })
    }
  }


</script>
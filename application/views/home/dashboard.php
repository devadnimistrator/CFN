<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Create New Invoice</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
            "name" => "newInvoice",
            "autocomplete" => false,
            "action" => base_url("invoices/add"),
            "buttons" => array(
                array(
                    "type" => "submit",
                    "value" => "Create",
                    "options" => array("class" => "btn btn-primary")
                )
            )
        );

        $_date = my_get_invoice_between_date();
        $bsForm = new My_bs_form($formConfig);
        $bsForm->add_element("action", BSFORM_HIDDEN, "new");
        $bsForm->add_element("account_id", BSFORM_SELECT, "all", array("label" => "Account", "options" => $accounts));
        $bsForm->add_element("start_pt_date", BSFORM_DATE, $_date->start_date, array("label" => "Start PT Date"));
        $bsForm->add_element("end_pt_date", BSFORM_DATE, $_date->end_date, array("label" => "End PT Date"));
        $bsForm->add_element("calc_method", BSFORM_SELECT, "", array("label" => "Calculator Method", 'options' => array("pump" => "Pump Price", "cfn" => "CFN Price")));
        $bsForm->generate();
        ?>
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Import PT Data</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content" style="display: none;">
        <?php
        $formConfig = array(
            "name" => "importPtData",
            "autocomplete" => false,
            "is_fileupload" => true,
            "action" => base_url("ptdatas/import_csv"),
            "col_width" => 2,
            "buttons" => array(
                array(
                    "type" => "submit",
                    "value" => "Import",
                    "options" => array("class" => "btn btn-primary")
                )
            )
        );
        $bsForm = new My_bs_form($formConfig);
        $bsForm->add_element("action", BSFORM_HIDDEN, "process");
        $bsForm->add_element("file", BSFORM_FILE);
        $bsForm->generate();
        ?>
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Import Accounts Data</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content" style="display: none;">
        <?php
        $formConfig = array(
            "name" => "importPtData",
            "autocomplete" => false,
            "is_fileupload" => true,
            "action" => base_url("fileimports/importdata/accounts"),
            "col_width" => 2,
            "buttons" => array(
                array(
                    "type" => "submit",
                    "value" => "Import",
                    "options" => array("class" => "btn btn-primary")
                )
            )
        );
        $bsForm = new My_bs_form($formConfig);
        $bsForm->add_element("action", BSFORM_HIDDEN, "process");
        $bsForm->add_element("file", BSFORM_FILE);
        $bsForm->generate();
        ?>
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Import Cards and Vehicles Data</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content" style="display: none;">
        <?php
        $formConfig = array(
            "name" => "importPtData",
            "autocomplete" => false,
            "is_fileupload" => true,
            "action" => base_url("fileimports/importdata/cards"),
            "col_width" => 2,
            "buttons" => array(
                array(
                    "type" => "submit",
                    "value" => "Import",
                    "options" => array("class" => "btn btn-primary")
                )
            )
        );
        $bsForm = new My_bs_form($formConfig);
        $bsForm->add_element("action", BSFORM_HIDDEN, "process");
        $bsForm->add_element("file", BSFORM_FILE);
        $bsForm->generate();
        ?>
      </div>
    </div>
  </div>

<!--  <div class="col-md-8 col-sm-6 col-xs-12">-->
<!--    <div class="x_panel">-->
<!--      <div class="x_title">-->
<!--        <h2>Balance</h2>-->
<!--        <ul class="nav navbar-right panel_toolbox">-->
<!--          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>-->
<!--        </ul>-->
<!--        <div class="clearfix"></div>-->
<!--      </div>-->
<!--      <div class="x_content">-->
<!--        <table id="table-balance" class="table table-striped table-bordered" width="100%">-->
<!--          <thead>-->
<!--            <tr>-->
<!--              <th>--><?php //___("Account") ?><!--:</th>-->
<!--              <th>--><?php //___("Charge") ?><!--:</th>-->
<!--              <th>--><?php //___("Deposit") ?><!--:</th>-->
<!--              <th>--><?php //___("Balance") ?><!--:</th>-->
<!--              <th>--><?php //___("Last") ?><!--:</th>-->
<!--            </tr>-->
<!--          </thead>-->
<!--        </table>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
  <div class="col-md-8 col-sm-6 col-xs-12">
    <?php $this->load->view("widgets/calendar"); ?>
  </div>

</div>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
    var $tableBalances;
    $(document).ready(function () {
      $tableBalances = $('#table-balance').DataTable({
        columns: [
          {"data": "account_name"},
          {"data": "charge"},
          {"data": "deposit"},
          {"data": "balance"},
          {"data": "lasted"}
        ],
        order: [[4, "desc"]],
        processing: true,
        serverSide: false,
        aLengthMenu: [50, 100],
        bFilter: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['nosort']
          }],
        responsive: true
      });
    });
</script>
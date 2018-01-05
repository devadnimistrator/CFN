<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="clearfix"></div>
<form class="form-horizontal form-label-left validateform" novalidate method="post">
  <input type="hidden" name="action" value="process"/>
  <div class="row">
    <?php my_show_system_message("success"); ?>
    <?php my_show_system_message("danger"); ?>

    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("System Configuration"); ?></h2>

            <div class="title_right">
              <div class="pull-right">
                <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("systemconfig/db_backup"); ?>'">
                  <?php ___("DB Backup"); ?> <li class="fa fa-download"></li>
                </button>
                <button type="button" class="btn btn-round btn-primary btn-sm" onclick="importptdata()">
                  <?php ___("DB Upload"); ?> <li class="fa fa-upload"></li>
                </button>
              </div>
            </div>

          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $formConfig = array(
            "name" => "systemConfig"
          );
          $bsForm = new My_bs_form($formConfig);
          $bsForm->add_element("SITE_TITLE", BSFORM_TEXT, SITE_TITLE, array("label" => "Site Title"));
          $bsForm->add_element("CONTACT_NAME", BSFORM_TEXT, CONTACT_NAME, array("label" => "Contact Name"));
          $bsForm->add_element("CONTACT_ADDRESS", BSFORM_TEXT, CONTACT_ADDRESS, array("label" => "Contact Address"));
          $bsForm->add_element("CONTACT_CITY", BSFORM_TEXT, CONTACT_CITY, array("label" => "Contact City"));
          $bsForm->add_element("CONTACT_STATE", BSFORM_TEXT, CONTACT_STATE, array("label" => "Contact State"));
          $bsForm->add_element("CONTACT_ZIP", BSFORM_TEXT, CONTACT_ZIP, array("label" => "Contact Zip"));
          $bsForm->add_element("CONTACT_PHONE", BSFORM_TEXT, CONTACT_PHONE, array("label" => "Contact Phone"));
          $bsForm->add_element("CONTACT_EMAIL", BSFORM_TEXT, CONTACT_EMAIL, array("label" => "Contact Email"));
          $bsForm->add_element("DEFAULT_INVOICE_HEADER_TEXT", BSFORM_TEXT, DEFAULT_INVOICE_HEADER_TEXT, array("label" => "Invoice Header Text"));
          $bsForm->generate();
          ?>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___(" Purge System"); ?></h2>

          <div class="title_right">
            <div class="pull-right">

            </div>
          </div>

          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $formConfig = array(
            "name" => "removePTdata",
            "autocomplete" => false,
            "action" => base_url("systemconfig/remove_ptdata"),
            "buttons" => array(
              array(
                "type" => "submit",
                "value" => "Remove",
                "options" => array("class" => "btn btn-primary")
              )
            )
          );

          $bsForm = new My_bs_form($formConfig);
          $bsForm->add_element("action", BSFORM_HIDDEN, "new");
          $bsForm->add_element("start_date", BSFORM_DATE, date("Y-01-01"), array("label" => "PTdata Start Date"));
          $bsForm->add_element("end_date", BSFORM_DATE, date("Y-m-d"), array("label" => "PTdata End Date"));
          $bsForm->generate();

          $formConfig = array(
            "name" => "removeInvoice",
            "autocomplete" => false,
            "action" => base_url("systemconfig/remove_invoices"),
            "buttons" => array(
              array(
                "type" => "submit",
                "value" => "Remove",
                "options" => array("class" => "btn btn-primary")
              )
            )
          );

          $bsForm = new My_bs_form($formConfig);
          $bsForm->add_element("action", BSFORM_HIDDEN, "new");
          $bsForm->add_element("start_date", BSFORM_DATE, date("Y-01-01"), array("label" => "Invoice Start Date"));
          $bsForm->add_element("end_date", BSFORM_DATE, date("Y-m-d"), array("label" => "Invoice End Date"));
          $bsForm->generate();

          $formConfig = array(
            "name" => "removePayment",
            "autocomplete" => false,
            "action" => base_url("systemconfig/remove_payments"),
            "buttons" => array(
              array(
                "type" => "submit",
                "value" => "Remove",
                "options" => array("class" => "btn btn-primary")
              )
            )
          );

          $bsForm = new My_bs_form($formConfig);
          $bsForm->add_element("action", BSFORM_HIDDEN, "new");
          $bsForm->add_element("start_date", BSFORM_DATE, date("Y-01-01"), array("label" => "Payment Start Date"));
          $bsForm->add_element("end_date", BSFORM_DATE, date("Y-m-d"), array("label" => "Payment End Date"));
          $bsForm->generate();
          ?>
        </div>
      </div>
    </div>
  </div>
</form>

<div id="ImportDataButton" data-toggle="modal" data-target="#DBImportModal"></div>
<div id="DBImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div id="wrap">
          <div class="container">
            <div class="x_title">
              <h2>Upload DB</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <?php
              $formConfig = array(
                "name" => "importPtData",
                "autocomplete" => false,
                "is_fileupload" => true,
                "action" => base_url("systemconfig/db_upload"),
                "col_width" => 2,
                "buttons" => array(
                  array(
                    "type" => "submit",
                    "value" => "DB Import",
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal">
          <i class="fa fa-close"></i>
          <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>

<script>
  function importptdata() {
    $("#DBImportModal").css("margin-top", $(window).height() / 10);
    $("#DBimport-info").html("");
    $("#ImportDataButton").click();
  }
</script>
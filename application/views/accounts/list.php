<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("accounts/add"); ?>'"><?php ___("Add New Account"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="importdata()"><?php ___("Import Cards and Vehicles Data"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="importaccountdata()"><?php ___("Import Accounts Data"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="remove_alldata()"><?php ___("Remove all Accounts data"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$account_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Accounts List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-accounts" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
<!--              <th>--><?php //___("Account") ?><!--:</th>-->
              <th><?php ___("Name") ?>:</th>
              <th style="background-color: #269abc"><?php ___("Balance") ?>:</th>
              <th><?php ___("Address") ?>:</th>
              <th><?php ___("Phone") ?>:</th>
              <th><?php ___("Fax") ?>:</th>
              <th><?php ___("Contact") ?>:</th>
              <th><?php ___("Email") ?>:</th>
              <th><?php ___("Total Discount in invoice") ?>:</th>
  <!--            <th>--><?php //___("Status")    ?><!--:</th>-->
  <!--            <th>--><?php //___("Billing cycle")    ?><!--:</th>-->
  <!--            <th style="background-color: #b3d1b7">--><?php //___("Departments")    ?><!--:</th>-->
              <th style="background-color: #e6efc9"><?php ___("Cards") ?>:</th>
              <th style="background-color: #c9cbef"><?php ___("Vehicles") ?>:</th>
              <th style="background-color: #c9e7ef"><?php ___("Diferencial price") ?>:</th>
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
    var $tableUsers;
    $(document).ready(function () {
      $tableUsers = $('#table-accounts').DataTable({
        language: {
          "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
        },
        columns: [
          {"data": "index"},
//          {"data": "account"},
          {"data": "name"},
          {"data": "balance"},
          {"data": "address"},
          {"data": "phone"},
          {"data": "fax"},
          {"data": "contact"},
          {"data": "email"},
          {"data": "discount"},
//        {"data": "departments"},
          {"data": "cards"},
          {"data": "vehicles"},
          {"data": "account_price"},
          {"data": "actions"}
        ],
        order: [[1, "asc"]],
        ordering: false,
        processing: true,
        serverSide: true,
        aLengthMenu: [30, 50, 100],
        ajax: {
          url: "<?php echo base_url("accounts/ajax_find/"); ?>",
          type: 'POST'
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['nosort']
          }],
        responsive: true,
        createdRow: function (row, data, index) {
          $(row).attr('id', data[index]);

//        $(row).dblclick(function () {
//          location.href = '<?php //echo base_url('accounts/edit');    ?>///' + $(this).attr('id');
//        });
        },
      });

    });

    function reloadTable(resetPaging) {
      $tableUsers.ajax.reload(function () {
      }, resetPaging);
    }

    function delete_account(account_id) {
      if (confirm("<?php ___("Are you sure delete selected account?"); ?>")) {
        $.get("<?php echo base_url('accounts/ajax_delete') ?>/" + account_id, function () {
          reloadTable(false);
        })
      }
    }
    function importdata() {
      $("#ImportModal").css("margin-top", $(window).height() / 10);
      $("#import-info").html("");
      $("#ImportDataButton").click();
    }
    function importaccountdata() {
      $("#ImportaccountModal").css("margin-top", $(window).height() / 10);
      $("#import-info").html("");
      $("#ImportaccountDataButton").click();
    }

    function remove_alldata() {
      if (confirm("<?php ___("Do you want to remove all accounts data?"); ?>")) {
        $.get("<?php echo base_url('accounts/remove_alldata') ?>", function () {
          reloadTable(false);
        })
      }
    }

</script>

<div id="ImportaccountDataButton" data-toggle="modal" data-target="#ImportaccountModal"></div>
<div id="ImportaccountModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div id="wrap">
          <div class="container">
            <div class="row">
              <div class="x_title">
                <h2>Import Accounts Data</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <?php
                $formConfig = array(
                    "name" => "importAccountData",
                    "autocomplete" => false,
                    "is_fileupload" => true,
                    "col_width" => 2,
                    "action" => base_url("fileimports/importdata/accounts"),
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
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal"><i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>

<div id="ImportDataButton" data-toggle="modal" data-target="#ImportModal"></div>
<div id="ImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div id="wrap">
          <div class="container">
            <div class="row">
              <div class="x_title">
                <h2>Import Cards and Vehicles Data</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <?php
                $formConfig = array(
                    "name" => "importCardData",
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
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal"><i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
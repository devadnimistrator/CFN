<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("ptdatas/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="importptdata()"><?php ___("Import PTData"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="remove_alldata()"><?php ___("Remove All PTData"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$ptdata_status = $this->config->item("data_status");
?>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
    my_show_system_message("success");
    my_show_system_message("danger");
    my_show_system_message("error");
    my_show_system_message("warning");
    ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>
          PTData          
          <small>
            <input type="text" id="em-pt_date" name="pt_date" value="<?php echo $last_completed; ?>" class="date-picker" style="width: 80px;" />
            <input type="button" value="Search" onclick="reloadTable(true)"/>
          </small>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-ptdatas" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th class="nosort"><?php ___("Sequence") ?>:</th>
              <th class="nosort"><?php ___("Site") ?>:</th>
              <th class="nosort"><?php ___("Product") ?>:</th>
              <th class="nosort"><?php ___("Card ID") ?>:</th>
              <th class="nosort"><?php ___("Company") ?>:</th>
              <th class="nosort"><?php ___("Total Amount") ?>:</th>
              <th class="nosort"><?php ___("Price") ?>:</th>
              <th><?php ___("Completed") ?>:</th>
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
      $tableUsers = $('#table-ptdatas').DataTable({
        language: {
          "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
        },
        columns: [
          {"data": "index"},
          {"data": "sequence"},
          {"data": "site"},
          {"data": "product"},
          {"data": "card_id"},
          {"data": "company_id"},
          {"data": "total_amount"},
          {"data": "price"},
          {"data": "completed"},
          {"data": "actions"}
        ],
        order: [[1, "asc"]],
        processing: true,
        serverSide: false,
        bFilter: true,
        aLengthMenu: [30,50,100],
        ajax: {
          url: "<?php echo base_url("ptdatas/ajax_find"); ?>",
          type: 'POST',
          data: function (d) {
            return $.extend({}, d, {
              date: $("#em-pt_date").val()
            });
          }
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['nosort']
          }],
        responsive: true
      });
    });

    $("#PTImport").click(function () {
      alert("OK");
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

    function importptdata() {
      $("#ptImportModal").css("margin-top", $(window).height() / 10);
      $("#ptimport-info").html("");
      $("#ImportDataButton").click();
    }

    function remove_alldata() {
      if (confirm("<?php ___("Do you want to remove all PTdata?"); ?>")) {
        $.get("<?php echo base_url('ptdatas/remove_alldata') ?>/" + $("#em-pt_date").val(), function () {
          reloadTable(false);
        });
      }
    }
</script>

<div id="ImportDataButton" data-toggle="modal" data-target="#ptImportModal"></div>
<div id="ptImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div id="wrap">
          <div class="container">
            <div class="x_title">
              <h2>Import PT Data</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
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
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal"><i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
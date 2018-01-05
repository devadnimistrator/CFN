<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("sites/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="importdata()"><?php ___("Import Sites Data"); ?></button>
    </div>
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="remove_alldata()"><?php ___("Remove all Sites"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<?php
$site_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("sites List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-sites" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
            <th><?php ___("Site ID") ?>:</th>
            <th><?php ___("Site name") ?>:</th>
            <th><?php ___("Participant") ?>:</th>
            <th><?php ___("Address") ?>:</th>
            <th><?php ___("City") ?>:</th>
            <th><?php ___("City code") ?>:</th>
<!--            <th>--><?php //___("county code") ?><!--:</th>-->
            <th><?php ___("State") ?>:</th>
            <th><?php ___("Phone") ?>:</th>
            <th><?php ___("C-store") ?>:</th>
            <th><?php ___("Zip") ?>:</th>
            <th><?php ___("Type") ?>:</th>
            <th><?php ___("Products") ?>:</th>
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
    $tableUsers = $('#table-sites').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "site_id"},
        {"data": "site_name"},
        {"data": "participant"},
        {"data": "address"},
        {"data": "city"},
        {"data": "city_code"},
//        {"data": "county_code"},
        {"data": "state"},
        {"data": "phone"},
        {"data": "c_store"},
        {"data": "zip"},
        {"data": "type"},
        {"data": "products"},
        {"data": "actions"}
      ],
      order: [[0, "asc"]],
      processing: true,
      serverSide: false,
      aLengthMenu: [30,50,100],
      ajax: {
        url: "<?php echo base_url("sites/ajax_find"); ?>",
        type: 'POST'
      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data[index]);

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('sites/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_site(site_id) {
    if (confirm("<?php ___("Are you sure delete selected site?"); ?>")) {
      $.get("<?php echo base_url('sites/ajax_delete') ?>/" + site_id, function () {
        reloadTable(false);
      })
    }
  }

  function importdata() {
    $("#ImportModal").css("margin-top", $(window).height() / 10);
    $("#import-info").html("");
    $("#ImportDataButton").click();
  }

  function remove_alldata() {
    if (confirm("<?php ___("Do you want to remove all Sites info?"); ?>")) {
      $.get("<?php echo base_url('sites/remove_alldata') ?>", function () {
        reloadTable(false);
      })
    }
  }


</script>

<div id="ImportDataButton" data-toggle="modal" data-target="#ImportModal"></div>
<div id="ImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div id="wrap">
          <div class="container">
            <div class="row">
              <form class="form-horizontal" action="<?php echo base_url("sites/Importdata")?>" method="post" name="upload_excel" enctype="multipart/form-data">
                <fieldset>
                  <!-- Form Name -->
                  <legend>Form Name</legend>
                  <!-- File Button -->
                  <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">Select File</label>
                    <div class="col-md-4">
                      <input type="file" name="file" id="file" class="input-large">
                    </div>
                  </div>
                  <!-- Button -->
                  <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton">Import data</label>
                    <div class="col-md-4">
                      <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">Import</button>
                    </div>
                  </div>
                </fieldset>
              </form>
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
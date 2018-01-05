<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("companies/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<?php
$company_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Companies List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-companys" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
            <th><?php ___("Company ID") ?>:</th>
            <th><?php ___("Client Consolidation") ?>:</th>
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
    $tableUsers = $('#table-companys').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "company_id"},
        {"data": "client_consolidation"},
        {"data": "actions"}
      ],
      order: [[0, "asc"]],
      processing: true,
      serverSide: false,
      ajax: {
        url: "<?php echo base_url("companies/ajax_find"); ?>",
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
          location.href = '<?php echo base_url('companies/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_company(company_id) {
    if (confirm("<?php ___("Are you sure delete selected company?"); ?>")) {
      $.get("<?php echo base_url('companies/ajax_delete') ?>/" + company_id, function () {
        reloadTable(false);
      })
    }
  }
</script>
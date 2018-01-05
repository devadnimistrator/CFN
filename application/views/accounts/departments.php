<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("departments/departmentlist/".$this->account_id); ?>'"><?php ___("Add New department"); ?></button>
      <button type="button" class="btn btn-round btn-sm" onclick="location.href = '<?php echo base_url("accounts"); ?>'"><?php ___("Back"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$card_status = $this->config->item("data_status");
?>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
    $department_m->show_errors();
    $department_m->show_msgs();
    my_show_system_message("success");
    my_show_system_message("error");
    ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2 id="sub-title">
            <?php ___("department Info") ?>
          </h2>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $formConfig = array(
            "name" => "addCard",
            "autocomplete" => false
          );
          ?>
          <div class="x_content">
            <?php
            $department_m->form_create($formConfig);
            $department_m->bs_form->form_start(TRUE);
            $department_m->form_add_element("account_id");
            $department_m->form_add_element("department_id");
            $department_m->form_add_element("department_name");
            $department_m->bs_form->form_elements(TRUE);
            ?>
            <div class="ln_solid"></div>
            <?php
            $department_m->bs_form->form_buttons(TRUE);
            $department_m->bs_form->form_end(TRUE);
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8 col-sm-8 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("departments List"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table id="table-departments" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Department Id") ?>:</th>
              <th><?php ___("Department Name") ?>:</th>
              <th class="nosort"><?php ___("Actions") ?>:</th>
            </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var $tableUsers;
  $(document).ready(function () {
    $tableUsers = $('#table-departments').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "department_id"},
        {"data": "department_name"},
        {"data": "actions"}
      ],
      order: [[1, "asc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("departments/ajax_find/" . $this->account_id); ?>",
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
          location.href = '<?php echo base_url('departments/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_department(department_id) {
    if (confirm("<?php ___("Are you sure delete selected card?"); ?>")) {
      $.get("<?php echo base_url('departments/ajax_delete/' . $this->account_id) ?>/" + department_id, function () {
        reloadTable(false);
      })
    }
  }
</script>

<div id="ShowcardButton" data-toggle="modal" data-target="#cardModal"></div>
<div id="cardModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <?php ___("card"); ?> #<span id="card-no"></span>
        </h4>
      </div>
      <div class="modal-body">
        <table id="card-info">
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal">
          <i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("users"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
//    $this->user_m->show_errors();
//    $this->user_m->show_msgs();
    ?>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-6">
    <div class="x_panel">
      <div class="x_title">
        <h2 id="sub-title">
          <?php ___("User Info") ?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
          "name" => "add",
          "autocomplete" => false
        );
        ?>
        <div class="x_content">
          <?php
          $this->user_m->form_create($formConfig);
          $this->user_m->bs_form->form_start(TRUE);
          $this->user_m->form_add_element("username", array('label' => __("User Name"), 'rules' => array(
            'required',
            'min_length[3]'
          )));
          $this->user_m->form_add_element("new_password", array('type' => 'password','label' => __('New Password'), 'rules' => array(
            'required',
            'min_length[6]'
          )));
          $this->user_m->form_add_element("new_password", array('type' => 'password','label' => __('Repeat Password'), 'rules' => array(
            'required',
            'matches[new_password]'
          )));
          $this->user_m->form_add_element("group", array('label' => __("Group"),"type" => "select", "options" => array('user' => 'user', 'admin' => 'admin')));
          $this->user_m->bs_form->form_elements(TRUE);
          ?>
          <div class="ln_solid"></div>
          <?php
          $this->user_m->bs_form->form_buttons(TRUE);
          $this->user_m->bs_form->form_end(TRUE);
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-6">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("User List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-users" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
<!--            <th>--><?php //___("User ID") ?><!--:</th>-->
            <th><?php ___("User Name") ?>:</th>
            <th><?php ___("Group") ?>:</th>
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
    $tableUsers = $('#table-users').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
//        {"data": "user_id"},
        {"data": "username"},
        {"data": "group"},
        {"data": "actions"}
      ],
      order: [[1, "asc"]],
      processing: true,
      serverSide: false,
      bFilter: false,
      bLengthChange: false,
      ajax: {
        url: "<?php echo base_url("users/ajax_find"); ?>",
        type: 'POST'
      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
      responsive: true
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_user(user_id) {
    if (confirm("<?php ___("Are you sure delete selected user?"); ?>")) {
      $.get("<?php echo base_url('users/ajax_delete') ?>/" + user_id, function () {
        reloadTable(false);
      })
    }
  }
</script>

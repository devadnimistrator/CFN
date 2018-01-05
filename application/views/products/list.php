<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-sm" onclick="location.href = '<?php echo base_url("products/sites"); ?>'"><?php ___("Management Site Products"); ?></button>
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("products"); ?>'"><?php ___("Add New Product"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$product_list_status = $this->config->item("data_status");
?>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
    $this->product_m->show_errors();
    $this->product_m->show_msgs();
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
            <?php ___("Product Info") ?>
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
            $this->product_m->form_create($formConfig);
            $this->product_m->bs_form->form_start(TRUE);
            $this->product_m->form_add_element("product_code");
            $this->product_m->form_add_element("product_type");
            $this->product_m->form_add_element("product_description");
            $this->product_m->form_add_element("over_price");
            $this->product_m->bs_form->form_elements(TRUE);
            ?>
            <div class="ln_solid"></div>
            <?php
            $this->product_m->bs_form->form_buttons(TRUE);
            $this->product_m->bs_form->form_end(TRUE);
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8 col-sm-8 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("List"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table id="table-product_lists" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Product Code") ?>:</th>
              <th><?php ___("Product Type") ?>:</th>
              <th><?php ___("Product Description") ?>:</th>
              <th><?php ___("Over Price") ?>:</th>
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
    $tableUsers = $('#table-product_lists').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "product_code"},
        {"data": "product_type"},
        {"data": "product_description"},
        {"data": "over_price"},
        {"data": "actions"}
      ],
      order: [[1, "asc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("products/ajax_get_products"); ?>",
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
//          location.href = '<?php //echo base_url('products1/'); ?>///' + $(this).attr('id');
//        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_product(product_id) {
    if (confirm("<?php ___("Are you sure delete selected product?"); ?>")) {
      $.get("<?php echo base_url('products/ajax_delete_product') ?>/" + product_id, function () {
        reloadTable(false);
      })
    }
  }

</script>


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
</div>
<div class="clearfix"></div>


<?php
$product_status = $this->config->item("data_status");
?>
<div class="row">
  <div class="col-md-4 col-sm-4 col-xs-12">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
        <?php
        $this->site_product_m->show_errors();
        $this->site_product_m->show_msgs();
        my_show_system_message("success");
        my_show_system_message("error");
        my_show_system_message("danger");
        ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2 id="sub-title">
              <?php ___("Site Product Info") ?>
              <small>
                <?php ___("Site"); ?>:
                <select id="filter_site" onchange="reloadTable(true)">
                  <?php foreach ($this->sites as $key => $value): ?>
                    <option value="<?php echo $key?>" <?php if ($this->site_id == "" .$key) echo "selected"?>><?php echo $value?></option>
                  <?php endforeach; ?>
                </select>
              </small>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <?php
            $formConfig = array(
              "name" => "addProduct",
              "autocomplete" => false
            );
            ?>
            <div class="x_content">
              <?php
              $this->site_product_m->form_create($formConfig);
              $this->site_product_m->bs_form->form_start(TRUE);
              $this->site_product_m->form_add_element("product_id", array("type" => 'hidden'));
              $this->site_product_m->form_add_element("product_code");
              $this->site_product_m->form_add_element("price");
              $this->site_product_m->bs_form->form_elements(TRUE);
              ?>
              <div class="ln_solid"></div>
              <?php
              $this->site_product_m->bs_form->form_buttons(TRUE);
              $this->site_product_m->bs_form->form_end(TRUE);
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-8 col-sm-8 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Site Products List"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-products" class="table table-striped table-bordered" width="100%">
          <thead>
          <tr>
            <th class="nosort"><i class="fa fa-list-ol"></i></th>
            <th><?php ___("Product Code") ?>:</th>
            <th><?php ___("Product Description") ?>:</th>
            <th><?php ___("Product Type") ?>:</th>
            <th><?php ___("Price") ?>:</th>
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
    $tableUsers = $('#table-products').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "product_code"},
        {"data": "product_description"},
        {"data": "product_type"},
        {"data": "price"},
        {"data": "actions"}
      ],
      "ordering": false,
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("products/ajax_get_site_products"); ?>",
        type: 'POST',
        data: function (d) {
          return $.extend({}, d, {
            site_id: $("#filter_site").val()
          });
        }

      },
      aoColumnDefs: [{
        'bSortable': false,
        'aTargets': ['nosort']
      }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data[index]);
//        $(row).dblclick(function () {
//          location.href = '<?php //echo base_url('products1/siteproducts/edit'); ?>///' + $(this).attr('id');
//        });
      },
    });

    $("#filter_site").change(function () {
      var site_id = $(this).val();
      $("#em-site_id").val(site_id);

    });

  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_site_product(site_id, product_id) {
    if (confirm("<?php ___("Are you sure delete selected product?"); ?>")) {
      $.get("<?php echo base_url('products/ajax_delete_site_product') ?>/" + site_id + "/" + product_id, function () {
        reloadTable(false);
      })
    }
  }
</script>
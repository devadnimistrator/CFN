<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <div class="page-title">
    <div class="title_left">
      <h3>
        <a href="<?php echo base_url("ptdatas"); ?>"><?php ___("PT Data"); ?></a>
        <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
      </h3>
    </div>
    <?php if ($ptdata_m->is_exists()): ?>
      <div class="title_right">
        <div class="pull-right">
          <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("ptdatas/add"); ?>'"><?php ___("Add New"); ?></button>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php
      $ptdata_m->show_errors();
      $ptdata_m->show_msgs();
      my_show_system_message("success");
      ?>
    </div>
  </div>
<?php

$formConfig = array(
  "name" => "addPtdata",
  "autocomplete" => false
);

$ptdata_m->form_create($formConfig);
$ptdata_m->bs_form->form_start(TRUE);

?>

  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("Account Information"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $ptdata_m->form_add_element("site_id", array("type" => "select", "options" => $this->site_m->get_all_sites()));
          $ptdata_m->form_add_element("sequence");
          $ptdata_m->form_add_element("status_code");
          $ptdata_m->form_add_element("total_amount");
          $ptdata_m->form_add_element("transaction_type");
          $ptdata_m->form_add_element("product_id", array("type" => "select", "options" => $this->product_m->get_all_products()));
          $ptdata_m->form_add_element("price");
          $ptdata_m->form_add_element("quantity");
          $ptdata_m->form_add_element("odometer");
          $ptdata_m->form_add_element("pump_number_or_register_number");
          $ptdata_m->form_add_element("transaction_number");
          $ptdata_m->form_add_element("date_completed", array("type" => "date"));
          $ptdata_m->form_add_element("time_completed", array("type" => "time"));
          $ptdata_m->form_add_element("error_code");
          $ptdata_m->form_add_element("authorization_number");
          $ptdata_m->form_add_element("manual_entry");
          $ptdata_m->form_add_element("card_id");
          $ptdata_m->form_add_element("company_id");
          $ptdata_m->form_add_element("site_type");
          $ptdata_m->form_add_element("pump_price");
          $ptdata_m->form_add_element("cfn_price");
          $ptdata_m->bs_form->form_elements(TRUE);
          ?>
          <div class="ln_solid"></div>
          <?php
          $ptdata_m->bs_form->form_buttons(TRUE);
          ?>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>

<?php
$ptdata_m->bs_form->form_end(TRUE);
?>


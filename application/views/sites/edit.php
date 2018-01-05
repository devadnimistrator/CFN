<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <div class="page-title">
    <div class="title_left">
      <h3>
        <a href="<?php echo base_url("sites"); ?>"><?php ___("Site Data"); ?></a>
        <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
      </h3>
    </div>
    <?php if ($site_m->is_exists()): ?>
      <div class="title_right">
        <div class="pull-right">
          <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("sites/add"); ?>'"><?php ___("Add New"); ?></button>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php
      $site_m->show_errors();
      $site_m->show_msgs();
      my_show_system_message("success");
      ?>
    </div>
  </div>
<?php

$formConfig = array(
  "name" => "addsite",
  "autocomplete" => false
);

$site_m->form_create($formConfig);
$site_m->bs_form->form_start(TRUE);

?>

  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("site Information"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $site_m->form_add_element("site_id");
          $site_m->form_add_element("site_name");
          $site_m->form_add_element("participant");
          $site_m->form_add_element("address");
          $site_m->form_add_element("city");
          $site_m->form_add_element("city_code");
          $site_m->form_add_element("county_code");
          $site_m->form_add_element("state");
          $site_m->form_add_element("phone");
          $site_m->form_add_element("c_store");
          $site_m->form_add_element("zip");
          $site_m->form_add_element("type");
          $site_m->bs_form->form_elements(TRUE);
          ?>
          <div class="ln_solid"></div>
          <?php
          $site_m->bs_form->form_buttons(TRUE);
          ?>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>

<?php
$site_m->bs_form->form_end(TRUE);
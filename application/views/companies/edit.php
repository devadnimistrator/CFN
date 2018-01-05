<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <div class="page-title">
    <div class="title_left">
      <h3>
        <a href="<?php echo base_url("companys"); ?>"><?php ___("company Data"); ?></a>
        <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
      </h3>
    </div>
    <?php if ($company_m->is_exists()): ?>
      <div class="title_right">
        <div class="pull-right">
          <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("companies/add"); ?>'"><?php ___("Add New"); ?></button>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php
      $company_m->show_errors();
      $company_m->show_msgs();
      my_show_system_message("success");
      ?>
    </div>
  </div>
<?php

$formConfig = array(
  "name" => "addcompany",
  "autocomplete" => false
);

$company_m->form_create($formConfig);
$company_m->bs_form->form_start(TRUE);

?>

  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("company Information"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $company_m->form_add_element("company_id");
          $company_m->form_add_element("client_consolidation");
          $company_m->bs_form->form_elements(TRUE);
          ?>
          <div class="ln_solid"></div>
          <?php
          $company_m->bs_form->form_buttons(TRUE);
          ?>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>

<?php
$company_m->bs_form->form_end(TRUE);
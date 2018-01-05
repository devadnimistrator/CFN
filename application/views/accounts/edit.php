<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <div class="page-title">
    <div class="title_left">
      <h3>
        <a href="<?php echo base_url("card_id"); ?>"><?php ___("Account Data"); ?></a>
        <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
      </h3>
    </div>
    <?php if ($account_m->is_exists()): ?>
      <div class="title_right">
        <div class="pull-right">
          <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("accounts/add"); ?>'"><?php ___("Add New"); ?></button>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <div class="clearfix"></div>

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php
      $account_m->show_errors();
      $account_m->show_msgs();
      my_show_system_message("success");
      ?>
    </div>
  </div>
<?php

$formConfig = array(
  "name" => "addaccount",
  "autocomplete" => false
);

$account_m->form_create($formConfig);
$account_m->bs_form->form_start(TRUE);

?>

  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("account Information"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <?php
          $account_m->form_add_element("account");
          $account_m->form_add_element("name");
          $account_m->form_add_element("address1");
          $account_m->form_add_element("address2");
          $account_m->form_add_element("city");
          $account_m->form_add_element("state");
          $account_m->form_add_element("zip");
          $account_m->form_add_element("phone");
          $account_m->form_add_element("fax");
          $account_m->form_add_element("contact");
          $account_m->form_add_element("email");
          $account_m->form_add_element("discount");
//          $account_m->form_add_element("status");
//          $account_m->form_add_element("billing_cycle");
          $account_m->bs_form->form_elements(TRUE);
          ?>
          <div class="ln_solid"></div>
          <?php
          $account_m->bs_form->form_buttons(TRUE);
          ?>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>

<?php
$account_m->bs_form->form_end(TRUE);
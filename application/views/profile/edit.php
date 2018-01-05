<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="clearfix"></div>
</div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <?php
    $userinfo_m->show_errors();
    $userinfo_m->show_msgs();
    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editUser",
    "autocomplete" => false
);

$userinfo_m->form_create($formConfig);
$userinfo_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Profile Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $userinfo_m->form_add_element("fullname");
        $userinfo_m->form_add_element("email");
        $userinfo_m->form_add_element("phone");
        $userinfo_m->bs_form->form_elements(TRUE);
        ?>
        
        <div class="ln_solid"></div>

        <?php
        $userinfo_m->bs_form->form_buttons(TRUE);
        ?>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>

<?php
$userinfo_m->bs_form->form_end(TRUE);

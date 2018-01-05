<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
</div>
<div class="clearfix"></div>

<?php
$user_m->show_msgs();
?>

<div class="row">
  <div class="col-md-9">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Set new password"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $user_m->show_errors();

        $formConfig = array(
            "name" => "changePwd",
            "autocomplete" => false
        );
        $user_m->form_create($formConfig);
        $user_m->form_add_element('old_password');
        $user_m->form_add_element('new_password');
        $user_m->form_add_element('re_password');
        $user_m->form_generate();
        ?>
      </div>
    </div>
  </div>
</div>
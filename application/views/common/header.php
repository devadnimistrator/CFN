<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?php echo isset($this->page_title) ? $this->page_title : "" ?> - <?php echo SITE_TITLE ?></title>
  <meta name="description" content="<?php ___("Dashboard of %s", SITE_TITLE); ?>"/>

  <!-- Bootstrap -->
  <?php my_load_css('plugins/bootstrap/css/bootstrap.min.css'); ?>
  <!-- Font Awesome -->
  <?php my_load_css('plugins/font-awesome/css/font-awesome.min.css?v=4.6.1'); ?>
  <!-- iCheck -->
  <?php my_load_css('plugins/iCheck/skins/flat/green.css'); ?>
  <!-- bootstrap-plugins -->
  <?php my_load_css('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'); ?>
  <?php my_load_css('plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>
  <?php my_load_css('plugins/bootstrap/css/bootstrap-progressbar-3.3.4.min.css'); ?>
  <!-- iCheck -->
  <?php my_load_css('plugins/iCheck/skins/flat/green.css'); ?>
  <!-- bootstrap-wysiwyg -->
  <?php my_load_css('plugins/google-code-prettify/prettify.min.css'); ?>
  <!-- Select2 -->
  <?php my_load_css('plugins/select2/css/select2.min.css'); ?>
  <!-- Switchery -->
  <?php my_load_css('plugins/switchery/switchery.min.css'); ?>
  <!-- starrr -->
  <?php my_load_css('plugins/starrr/starrr.css'); ?>
  <!-- Datatables -->
  <?php my_load_css('plugins/datatables/css/dataTables.bootstrap.min.css'); ?>
  <?php my_load_css('plugins/datatables/css/buttons.bootstrap.min.css'); ?>
  <?php my_load_css('plugins/datatables/css/fixedColumns.dataTables.min.css'); ?>
  <?php my_load_css('plugins/datatables/css/fixedHeader.bootstrap.min.css'); ?>
  <?php my_load_css('plugins/datatables/css/responsive.bootstrap.min.css'); ?>
  <?php my_load_css('plugins/datatables/css/scroller.bootstrap.min.css'); ?>

  <?php
  foreach ($this->extends_css as $css) {
    my_load_css($css);
  }
  ?>

  <!-- Custom Theme Style -->
  <?php my_load_css('css/admin.css?v=' . ASSETS_VERSION); ?>

  <script>
    var my_js_options = {
      language: "<?php echo DISPLAY_LANGUAGE/* $this->config->item("language") */
      ; ?>",
      timezone: '<?php echo DEFAULT_TIMEZONE; ?>',
      day_options: {
        timeZone: '<?php echo DEFAULT_TIMEZONE; ?>',
        year: 'numeric',
        // month : 'short',
        month: 'numeric',
        day: 'numeric',
      },
      time_options: {
        timeZone: '<?php echo DEFAULT_TIMEZONE; ?>',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
      },
      date_full_format: "<?php echo DISPLAY_LANGUAGE == 'english' ? 'yyyy-mm-dd' : 'dd-mm-yyyy'; ?>",
      datetime_full_format: "<?php echo DISPLAY_LANGUAGE == 'english' ? 'yyyy-mm-dd hh:ii:ss' : 'dd-mm-yyyy hh:ii:ss'; ?>",
      datetime1_full_format: "<?php echo DISPLAY_LANGUAGE == 'english' ? 'yyyy-mm-dd hh:ii' : 'dd-mm-yyyy hh:ii'; ?>"
    }
  </script>

  <!-- jQuery -->
  <?php my_load_js('plugins/jquery/jquery1.9.1.min.js'); ?>

  <!-- Mement -->
  <?php my_load_js('plugins/moment/moment.min.js'); ?>
  <?php if (DISPLAY_LANGUAGE != 'english'): ?>
    <?php my_load_js('plugins/moment/locale/' . DISPLAY_LANGUAGE . '.js?v=' . ASSETS_VERSION); ?>
    <script>moment.locale(my_js_options.language);// 'Freitag, 24. Juni 2016 01:42'</script>
  <?php endif; ?>
  <!-- Bootstrap -->
  <?php my_load_js('plugins/bootstrap/js/bootstrap.min.js'); ?>
  <!-- FastClick -->
  <?php my_load_js('plugins/fastclick.js'); ?>
  <!-- NProgress -->
  <?php my_load_js('plugins/nprogress.js'); ?>
  <!-- Switchery -->
  <?php my_load_js('plugins/switchery/switchery.min.js'); ?>

  <style>
    .item .alert {
      max-width: 60%;
    }

    .modal-body .dataTables_wrapper .dataTables_paginate {
      margin-top: 2em;
    }
  </style>

  <?php include_once(__DIR__ . "/_custom_css.php"); ?>
</head>

<?php
$page_slug = $this->uri->segment(1);
$page_sub_slug = $this->uri->segment(2);
if ($page_sub_slug) {

} else {
  $page_sub_slug = "index";
}
?>

<body class="nav-md">
<div class="container body">
  <div class="main_container">
    <div class="col-md-3 left_col">
      <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
          <a href="<?php echo base_url('home'); ?>" class="site_title" title="<?php ___("Visit site"); ?>">
            <h1 id="site_title"><?php echo SITE_TITLE ?></h1>
          </a>
        </div>

        <div class="clearfix"></div>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
          <div class="menu_section">
            <ul class="nav side-menu">
              <?php
              if ($this->logined_user->group == 'admin') {
                include_once '_menu_admin.php';
              } else {
                include_once '_menu_user.php';
              }
              ?>
              <li>
                <a href="<?php echo base_url('auth/signout'); ?>"><i class="fa fa-sign-out"></i> <?php ___("Logout"); ?>
                </a>
              </li>
            </ul>
          </div>
        </div>
        <!-- /sidebar menu -->
      </div>
    </div>

    <!-- top navigation -->
    <div class="top_nav">

      <div class="nav_menu">
        <nav class="" role="navigation">
          <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
          </div>
          <div class="nav toggle current-time">
            <i class="fa fa-clock-o"></i>&nbsp;<span id="current-day"><?php echo my_formart_date(); ?></span><br/>
            <i class="fa" style="width:1em;"></i>&nbsp;<span id="current-time"><?php echo date('H:i:s'); ?></span>
          </div>
          <ul class="nav navbar-nav navbar-right">
            <li class="">
              <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user"></i> <?php echo $this->logined_user->username ?> <span
                  class=" fa fa-angle-down"></span> </a>
              <ul class="dropdown-menu pull-right">
                <li>
                  <a href="<?php echo base_url('profile/edit_profile') ?>"><i
                      class="fa fa-edit"></i>&nbsp;&nbsp;<?php ___("Edit Profile"); ?></a>
                </li>
                <li>
                  <a href="<?php echo base_url('profile/change_password') ?>"><i
                      class="fa fa-key"></i>&nbsp;&nbsp;<?php ___("Change Password"); ?></a>
                </li>
                <li>
                  <a href="<?php echo base_url('auth/signout') ?>"><i
                      class="fa fa-sign-out"></i>&nbsp;&nbsp;<?php ___("Logout"); ?></a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>

    </div>
    <!-- /top navigation -->

    <!-- Start Right Content -->
    <div class="right_col" role="main">
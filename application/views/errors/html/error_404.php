<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Error 404</title>

    <!-- Bootstrap -->
    <?php my_load_css('plugins/bootstrap/css/bootstrap.min.css'); ?>
    <!-- Font Awesome -->
    <?php my_load_css('plugins/font-awesome/css/font-awesome.min.css'); ?>

    <!-- Custom Theme Style -->
    <?php my_load_css('css/admin.css?v=' . ASSETS_VERSION); ?>
    
    <style>
      body {
        color: #73879C;
      }
    </style>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <!-- page content -->
        <div class="col-md-12">
          <div class="col-middle">
            <div class="text-center text-center">
              <h1 class="error-number">404</h1>
              <h2><?php ___("Sorry but we couldn't find this page"); ?></h2>
              <p><?php ___("This page you are looking for does not exist."); ?></p>
              <div class="mid_center">
                <a href="<?php echo site_url('home'); ?>" class="btn btn-large btn-primary"><i class="fa fa-backward"></i> <?php ___("Back to home"); ?></a>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
      </div>
    </div>
  </body>
</html>
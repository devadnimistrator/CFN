<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title><?php ___("Log in");?> - <?php echo SITE_TITLE ?></title>
		<meta name="description" content="Dashboard of <?php echo SITE_TITLE?>" />

		<!-- Bootstrap -->
		<?php my_load_css('plugins/bootstrap/css/bootstrap.min.css'); ?>
		<!-- Font Awesome -->
		<?php my_load_css('plugins/font-awesome/css/font-awesome.min.css'); ?>

		<!-- Custom Theme Style -->
		<?php my_load_css('css/admin.css?V=' . ASSETS_VERSION); ?>
	</head>

	<body style="background:#f8f8f8;">
		<div class="">
			<div id="wrapper">
				<div id="login" class=" form">
					<section class="login_content">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<?php echo form_open(); ?>
								<input type="hidden" name="action" value="signin" />
                <h1 style="font-size: 3em">C F N</h1>
<!--                <img src="--><?php //echo base_url("assets/images/logo.png"); ?><!--" style="max-width: 100%; margin-bottom: 20px;"/>-->
								<?php
								if ($error_msgs) {
									my_show_msg($error_msgs, 'error');
								}
								?>
								<div>
                  <input type="text" name="username" class="form-control" placeholder="<?php ___("Username"); ?>" value="<?php echo $this->user_m->username; ?>" required="" />
								</div>
								<div>
									<input type="password" name="password" class="form-control" placeholder="<?php ___("Password"); ?>" value="" required="" />
								</div>
								<div class="text-center">
									<button class="btn btn-default submit" >&nbsp;<?php ___("Log in"); ?>&nbsp;</button>
								</div>
								<div class="clearfix"></div>
							<?php echo form_close(); ?>
						</div>
					</section>
				</div>
			</div>
		</div>
	</body>
</html>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>

<li class="<?php if ($page_slug == 'home') echo "active" ?>">
  <a href="<?php echo base_url('home'); ?>"><i class="fa fa-tachometer"></i><?php ___("Dashboard"); ?></a>
</li>

<li class="<?php if ($page_slug == 'ptdatas') echo "active" ?>">
  <a href="<?php echo base_url('ptdatas'); ?>"><i class="fa fa-file-text"></i><?php ___("PT Data"); ?></a>
</li>

<li class="<?php if ($page_slug == 'invoices') echo "active" ?>">
  <a href="<?php echo base_url('invoices'); ?>"><i class="fa fa-list-alt"></i> <?php ___("Invoice"); ?></a>
</li>

<li class="<?php if ($page_slug == 'payments') echo "active" ?>">
  <a href="<?php echo base_url('payments'); ?>"><i class="fa fa-dollar"></i> <?php ___("Payments"); ?></a>
</li>

<li class="<?php if ($page_slug == 'reports') echo "active" ?>">
  <a href="<?php echo base_url('reports'); ?>"><i class="fa fa-book"></i> <?php ___("Reports"); ?></a>
</li>

<?php
$system_pages = array("sites", "companies", "products", "sitesetting");
$accounts_pages = array("accounts", "cards", "departments", "prices", "vehicles");
?>

<li class="<?php echo (in_array($page_slug, $accounts_pages) ? "active" : "") ?>">
  <a href="<?php echo base_url('accounts'); ?>"><i class="fa fa-users"></i> <?php ___("Accounts"); ?></a>
</li>

<li class="<?php echo (in_array($page_slug, $system_pages) ? "active" : "") ?>">
  <a href="#"><i class="fa fa-cogs"></i> Systems<span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php echo (in_array($page_slug, $system_pages) ? 'style="display: block;"' : '') ?>>
    <li class="<?php if ($page_slug == 'sites') echo "active" ?>">
      <a href="<?php echo site_url("sites"); ?>"><i class="fa fa-bank"></i><?php ___("Sites"); ?></a>
    </li>
    <li class="<?php if ($page_slug == 'companies') echo "active" ?>">
      <a href="<?php echo site_url("companies"); ?>"><i class="fa fa-building-o"></i><?php ___("Companies"); ?></a>
    </li>
    <li class="<?php if ($page_slug == 'products') echo "active" ?>">
      <a href="<?php echo site_url("products"); ?>"><i class="fa fa-support"></i><?php ___("Products"); ?></a>
    </li>
    <li class="<?php if ($page_slug == 'systemconfig') echo "active" ?>">
      <a href="<?php echo site_url("systemconfig"); ?>"><i class="fa fa-cogs"></i><?php ___("System Configs"); ?></a>
    </li>
  </ul>
</li>

<li class="<?php if ($page_slug == 'users') echo "active" ?>">
  <a href="<?php echo base_url('users'); ?>"><i class="fa fa-user"></i> <?php ___("Users"); ?></a>
</li>

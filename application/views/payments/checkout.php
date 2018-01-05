<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>Checkout</h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-sm" onclick="location.href = '<?php echo base_url("payments"); ?>'">Back</button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <?php
    $this->payment_m->show_errors();
    $this->payment_m->show_msgs();
    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "checkout",
    "autocomplete" => false
);

$this->payment_m->form_create($formConfig);
$this->payment_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php echo (!$this->payment_m->is_exists() ? "New Checkout" : "Edit Checkout #" . $this->payment_m->id); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $this->payment_m->form_create($formConfig);
        $this->payment_m->form_add_element("account_id", array("type" => 'select', "options" => $this->account_m->get_all_account()));
        $this->payment_m->form_add_element("charge");
        $this->payment_m->form_add_element("description");
        $this->payment_m->form_add_element("date",array('type' => 'date'));
        $this->payment_m->form_generate();
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$this->payment_m->bs_form->form_end(TRUE);

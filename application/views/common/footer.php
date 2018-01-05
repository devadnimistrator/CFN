<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="clearfix"></div>
</div><!-- End Right Content -->

<!-- bootstrap-progressbar -->
<?php my_load_js('plugins/bootstrap/js/bootstrap-progressbar.min.js'); ?>

<!-- bootstrap-datepicker -->
<?php my_load_js('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>
<?php my_load_js('plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>
<?php if (DISPLAY_LANGUAGE != 'english'): ?>
  <?php my_load_js('plugins/bootstrap-datepicker/locales/bootstrap-datepicker.' . DISPLAY_LANGUAGE . '.min.js?v=' . ASSETS_VERSION); ?>
<?php my_load_js('plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.' . DISPLAY_LANGUAGE . '.js?v=' . ASSETS_VERSION); ?>
<?php endif; ?>

<?php
foreach ($this->extends_js as $js) {
  my_load_js($js);
}
?>

<!-- iCheck -->
<?php my_load_js('plugins/iCheck/icheck.min.js'); ?>

<!-- validator -->
<?php my_load_js('plugins/validator/multifield.js'); ?>
<?php my_load_js('plugins/validator/validator.js?v=' . ASSETS_VERSION); ?>
<?php my_load_js('plugins/validator/language/' . DISPLAY_LANGUAGE . '.js?v=' . ASSETS_VERSION); ?>

<?php my_load_js('plugins/jquery-number-master/jquery.number.min.js'); ?>

<!-- validator -->
<script>
  ValidateOptions = {
    classes: {
      item: 'item',
      alert: 'alert',
      bad: 'bad'
    }
  }
  var validator = new FormValidator(ValidatorMessages, ValidateOptions);

  for (var i = 0; i < document.forms.length; i++) {
    var form = document.forms[i];
    if ($(form).hasClass('validateform')) {

    } else {
      continue;
    }

    form.addEventListener('blur', function (e) {
      validator.checkField.call(validator, e.target)
    }, true);

    form.addEventListener('input', function (e) {
      validator.checkField.call(validator, e.target);
    }, true);

    form.addEventListener('change', function (e) {
      validator.checkField.call(validator, e.target)
    }, true);

    $('form.validateform input').focusin(function () {
      $(this).parent().parent().find('.alert').remove();
    })
  }

  $('form.validateform').submit(function (e) {
    e.preventDefault();

    // evaluate the form using generic validaing
    validatorResult = validator.checkAll(e.target);

    if (validatorResult.valid) {
      var ajaxcall = $(this).attr("data-ajaxcall");
      if (ajaxcall) {
        if (typeof window[ajaxcall] === 'function') {
          window[ajaxcall]();
        }
      } else {
        this.submit();
      }
    }

    return false;
  });
</script>
<!-- /validator -->

<!-- Custom Theme Scripts -->
<?php my_load_js('js/admin.js?v=' . ASSETS_VERSION); ?>

<!-- footer content -->
<!--footer>
  @2017, PSP
</footer-->
<!-- /footer content -->
</div>
</div>
</body>
</html>
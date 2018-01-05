<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Datatables -->
<?php
//my_load_js('plugins/moment/moment.min.js');
//my_load_js('js/moment/moment.min.js');
my_load_js('js/datepicker/daterangepicker.js');

//my_load_js('plugins/bootstrap-daterangepicker/moment.min.js');
//my_load_js('plugins/bootstrap-daterangepicker/daterangepicker.js');
?>

<script>
  var custom_daterangepicker_local = {
    english: {
      customRangeLabel: 'Custom',
      firstDay: 1
    },
    spanish: {
      applyLabel: 'Aplicar',
      cancelLabel: 'Cancelar',
      fromLabel: 'De',
      toLabel: 'A',
      customRangeLabel: 'Personalizado',
      daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
      monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
      firstDay: 1
    }
  };

  var custom_daterangepicker_option1 = {
    showDropdowns: true,
    showWeekNumbers: true,
    buttonClasses: ['btn btn-default'],
    applyClass: 'btn-small btn-primary',
    cancelClass: 'btn-small',
    format: 'MMMM DD, YY',
    ranges: {
      '<?php ___("Last 7 Days"); ?>': [moment().subtract(6, 'days'), moment()],
      '<?php ___("Last 30 Days"); ?>': [moment().subtract(29, 'days'), moment()],
      '<?php ___("Last Month"); ?>': [moment().startOf('month'), moment()],
      '<?php ___("Last Year"); ?>': [moment().startOf('year'), moment()]
    },
    startDate: moment().subtract(29, 'days'),
    endDate: moment(),
    maxDate: moment(),
    locale: custom_daterangepicker_local.<?php echo DISPLAY_LANGUAGE; ?>
  };

  var custom_daterangepicker_option2 = {
    showDropdowns: true,
    showWeekNumbers: true,
    buttonClasses: ['btn btn-default'],
    applyClass: 'btn-small btn-primary',
    cancelClass: 'btn-small',
    format: 'MMM DD, YY',
    ranges: {
      '<?php ___("Last 7 Days"); ?>': [moment().subtract(6, 'days'), moment()],
      '<?php ___("Last 30 Days"); ?>': [moment().subtract(29, 'days'), moment()],
      '<?php ___("Last Month"); ?>': [moment().startOf('month'), moment().endOf('month')],
      '<?php ___("Last Year"); ?>': [moment().startOf('year'), moment().endOf('year')]
    },
    startDate: moment().startOf('year'),
    endDate: moment().endOf('year'),
    locale: custom_daterangepicker_local.<?php echo DISPLAY_LANGUAGE; ?>
  };
</script>
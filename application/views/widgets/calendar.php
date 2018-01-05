<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
my_load_css("plugins/fullcalendar/fullcalendar.min.css");
//my_load_css("plugins/fullcalendar/dist/fullcalendar.print.css");

$CI->add_js("plugins/fullcalendar/fullcalendar.min.js");
if (DISPLAY_LANGUAGE != 'english') {
  $CI->add_js("plugins/fullcalendar/locale/" . DISPLAY_LANGUAGE . ".js");
}
?>
<style>

  #script-warning {
    display: none;
    background: #eee;
    border-bottom: 1px solid #ddd;
    padding: 0 10px;
    line-height: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 12px;
    color: red;
    margin-bottom: 20px;
  }

  #calendar-loading {
    display: none;
    position: absolute;
    top: 10px;
    right: 10px;
  }

  .fc-event {
    padding: 5px;
  }

  .fc-event.event-plan {
    background-color: #1ABB9C;
  }

  .fc-event.event-running {
    background-color: #349ADB;
  }

  .fc-event.event-end {
    background-color: #999;
  }

  .fc-event.event-cancel {
    background-color: #E74C3C;
  }

  .fc-event span {
    line-height: 1.5em;
  }
</style>

<div class="x_panel">
  <div class="x_title">
    <h2>
      <?php ___("PT Data"); ?>
      <small>
        <i class="fa fa-square" style="color:#349ADB"></i> <?php ___("Imported"); ?>&nbsp;
      </small>
    </h2>
    <ul class="nav navbar-left panel_toolbox">
    </ul>
    <div class="clearfix"></div>
  </div>
  <div class="x_content">
    <div id='calendar-loading'>loading...</div>
    <div id='calendar'></div>
  </div>
</div>
<!-- FullCalendar -->
<script>

  $(window).load(function () {
    var date = new Date(),
      d = date.getDate(),
      m = date.getMonth(),
      y = date.getFullYear(),
      started,
      categoryClass;
    var date_list;
    var calendar = $('#calendar').fullCalendar({
      weekNumbers: true,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,basicWeek,basicDay'
      },
      selectable: true,
      selectHelper: true,
      editable: false,
      events: {
        url: "<?php echo base_url("ptdatas/ajax_get_date");?>",
        error: function()
        {
          alert("error");
        },
        success: function(data)
        {
          date_list = data;
          if (data) {
            $(".fc-week tr").find('td').each (function() {
              for (var i = 0; i < data.length ; i++) {
                if (data[i] == $(this).attr("data-date")) {
                  $(this).css("background-color","#349ADB");
                }
              }
            });
          }

        }
      },

    });

  });
</script>
<!-- /FullCalendar -->
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var URL = window.location.href.split('?')[0],
        $BODY = $('body'),
        $MENU_TOGGLE = $('#menu_toggle'),
        $SIDEBAR_MENU = $('#sidebar-menu'),
        $SIDEBAR_FOOTER = $('.sidebar-footer'),
        $LEFT_COL = $('.left_col'),
        $RIGHT_COL = $('.right_col'),
        $NAV_MENU = $('.nav_menu'),
        $FOOTER = $('footer');

// Sidebar
$(document).ready(function () {

  // TODO: This is some kind of easy fix, maybe we can improve this
  var setContentHeight = function () {
    // reset height

    $RIGHT_COL.css('min-height', $(window).height());

    /*var bodyHeight = $BODY.height(),
     leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
     contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;
     
     // normalize content
     contentHeight -= $NAV_MENU.height() + $FOOTER.height();
     
     $RIGHT_COL.css('min-height', contentHeight);*/
  };

  $SIDEBAR_MENU.find('a').on('click', function (ev) {
    var $li = $(this).parent();

    if ($li.is('.active')) {
      $li.removeClass('active');
      $('ul:first', $li).slideUp(function () {
        setContentHeight();
      });
    } else {
      // prevent closing menu if we are on child menu
      if (!$li.parent().is('.child_menu')) {
        $SIDEBAR_MENU.find('li').removeClass('active');
        $SIDEBAR_MENU.find('li ul').slideUp();
      }

      $li.addClass('active');

      $('ul:first', $li).slideDown(function () {
        setContentHeight();
      });
    }
  });

  // toggle small or large menu
  $MENU_TOGGLE.on('click', function () {
    if ($BODY.hasClass('nav-md')) {
      $BODY.removeClass('nav-md').addClass('nav-sm');
      $LEFT_COL.removeClass('scroll-view').removeAttr('style');

      if ($SIDEBAR_MENU.find('li').hasClass('active')) {
        $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
      }
    } else {
      $BODY.removeClass('nav-sm').addClass('nav-md');

      if ($SIDEBAR_MENU.find('li').hasClass('active-sm')) {
        $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
      }
    }

    setContentHeight();

    $(window).resize();
  });

  // check active menu
  /*$SIDEBAR_MENU.find('a[href="' + URL + '"]').parent('li').addClass('current-page');
   
   $SIDEBAR_MENU.find('a').filter(function () {
   return this.href == URL;
   }).parent('li').addClass('current-page').parents('ul').slideDown(function () {
   setContentHeight();
   }).parent().addClass('active');*/

  setContentHeight();

  // recompute content when resizing
  $(window).smartresize(function () {
    setContentHeight();
  });

  if ($('input.date-picker').length) {
    $('input.date-picker').datepicker({
      language: my_js_options.language,
      format: my_js_options.date_full_format,
      todayBtn: "linked",
      autoclose: true
    }).on("changeDate", function(e) {
      var $parent = $(e.target).parent();
      if ($parent.hasClass("bad")) {
        $parent.removeClass("bad");
        $parent.find('.alt').remove();
      }
    });
  }

  if ($('input.datetime-picker').length) {
    $('input.datetime-picker').datetimepicker({
      language: my_js_options.language,
      format: my_js_options.datetime1_full_format,
      autoclose: true,
      todayBtn: "linked",
      weekStart: 1
    }).on("changeDate", function(e) {
      var $parent = $(e.target).parent();
      if ($parent.hasClass("bad")) {
        $parent.removeClass("bad");
        $parent.find('.alt').remove();
      }
    });
  }

  initInputFileGroup();
});
// /Sidebar

// Panel toolbox
$(document).ready(function () {
  $('.collapse-link').on('click', function () {
    var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');

    // fix for some div with hardcoded fix class
    if ($BOX_PANEL.attr('style')) {
      $BOX_CONTENT.slideToggle(200, function () {
        $BOX_PANEL.removeAttr('style');
      });
    } else {
      $BOX_CONTENT.slideToggle(200);
      $BOX_PANEL.css('height', 'auto');
    }

    $ICON.toggleClass('fa-chevron-up fa-chevron-down');
  });

  $('.close-link').click(function () {
    var $BOX_PANEL = $(this).closest('.x_panel');

    $BOX_PANEL.remove();
  });
});
// /Panel toolbox

// Tooltip
$(document).ready(function () {
  tooltip_init();
});
// /Tooltip

function tooltip_init() {
  $('[data-toggle="tooltip"]').tooltip({
    container: 'body',
    html: true
  });
}

// Progressbar
if ($(".progress .progress-bar")[0]) {
  $('.progress .progress-bar').progressbar();
  // bootstrap 3
}
// /Progressbar

// Switchery
$(document).ready(function () {
  if ($(".js-switch")[0]) {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
      var switchery = new Switchery(html, {
        color: '#26B99A'
      });
    });
  }
});
// /Switchery

// iCheck
$(document).ready(function () {
  if ($("input.flat")[0]) {
    $(document).ready(function () {
      $('input.flat').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      });

      if ($(".long-radio-groups").length) {
        $(".long-radio-groups .radio-groups").each(function () {
          $(this).find("input[type=checkbox]").each(function () {
            $(this).on('ifChanged', function () {
              $div_groups = $(this).parent().parent().parent();
              $div_labels = $("#" + $div_groups.attr('id') + "-labels");

              var labels = [];

              $div_groups.find("input[type=checkbox]:checked").each(function () {
                labels.push($("label[for=" + $(this).attr('id') + "]").text().trim());
              });

              $div_labels.text(labels.join(", "));
            })
          })
        })
      }

      if ($(".radio-groups-allcheck").length) {
        $(".radio-groups-allcheck").each(function () {
          var id = $(this).attr("radio-groups");
          $div_groups = $("#" + id);
          $("#" + id + "-allcheck").on('ifChanged', function () {
            var id = $(this).attr("radio-groups");
            $div_groups = $("#" + id);
            if ($(this).prop('checked')) {
              $div_groups.find(".icheckbox_flat-green").not(".checked").find(".iCheck-helper").trigger('click');
            } else {
              $div_groups.find(".icheckbox_flat-green.checked .iCheck-helper").trigger('click');
            }
          });
        });
      }
    });
  }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
  checkState = '';
  $(this).parent().parent().parent().addClass('selected');
  countChecked();
});
$('table input').on('ifUnchecked', function () {
  checkState = '';
  $(this).parent().parent().parent().removeClass('selected');
  countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
  checkState = '';
  $(this).parent().parent().parent().addClass('selected');
  countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
  checkState = '';
  $(this).parent().parent().parent().removeClass('selected');
  countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
  checkState = 'all';
  countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
  checkState = 'none';
  countChecked();
});

function countChecked() {
  if (checkState === 'all') {
    $(".bulk_action input[name='table_records']").iCheck('check');
  }
  if (checkState === 'none') {
    $(".bulk_action input[name='table_records']").iCheck('uncheck');
  }

  var checkCount = $(".bulk_action input[name='table_records']:checked").length;

  if (checkCount) {
    $('.column-title').hide();
    $('.bulk-actions').show();
    $('.action-cnt').html(checkCount + ' Records Selected');
  } else {
    $('.column-title').show();
    $('.bulk-actions').hide();
  }
}

// Accordion
$(document).ready(function () {
  $(".expand").on("click", function () {
    $(this).next().slideToggle(200);
    $expand = $(this).find(">:first-child");

    if ($expand.text() == "+") {
      $expand.text("-");
    } else {
      $expand.text("+");
    }
  });
});

// NProgress
if (typeof NProgress != 'undefined') {
  $(document).ready(function () {
    NProgress.start();
  });

  $(window).load(function () {
    NProgress.done();
  });
}

/**
 * Resize function without multiple trigger
 *
 * Usage:
 * $(window).smartresize(function(){
 *     // code here
 * });
 */
(function ($, sr) {
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
    var timeout;

    return function debounced() {
      var obj = this,
              args =
              arguments;
      function delayed() {
        if (!execAsap)
          func.apply(obj, args);
        timeout = null;
      }

      if (timeout)
        clearTimeout(timeout);
      else if (execAsap)
        func.apply(obj, args);

      timeout = setTimeout(delayed, threshold || 100);
    };
  };

  // smartresize
  jQuery.fn[sr] = function (fn) {
    return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
  };

})(jQuery, 'smartresize');

function initInputFileGroup() {
  $(".input-file-group").each(function () {
    var fileGroup = $(this);
    $filetext = $(this).find("input[type=text]");
    $filetag = $(this).find("input[type=file]");
    $btn = $(this).find("button");

    $btn.click(function () {
      fileGroup.find("input[type=file]").trigger("click");
    });

    $filetag.change(function () {
      var filepath = $(this).val();
      if (filepath.lastIndexOf("/") > 0) {
        fileGroup.find("input[type=text]").val(filepath.split('/').pop());
      } else {
        fileGroup.find("input[type=text]").val(filepath.split('\\').pop());
      }
    });
  });
}

// Show Real Time
var showdatetimeTimeout = null;
function showDateTime() {
  try {
//    $("#current-day").text((new Date()).toLocaleString([], my_js_options.day_options));
//    $("#current-time").text((new Date()).toLocaleString([], my_js_options.time_options));

    $("#current-day").text(moment().tz(my_js_options.timezone).format("MMMM D, YYYY"));
    $("#current-time").text(moment().tz(my_js_options.timezone).format('HH:mm:ss'));
  } catch (e) {
  }

  if (showdatetimeTimeout) {
    clearTimeout(showdatetimeTimeout);
    showdatetimeTimeout = null;
  }

  showdatetimeTimeout = setTimeout(function () {
    showDateTime();
  }, 1000);
}


$(document).ready(function () {
  showDateTime();
});



// Validate Error
$(document).ready(function () {
  $('.form-validation-errors .alert-info').each(function () {
    var field = $(this).attr('data-validate-field');
    if ($("#form-group-" + field).length > 0) {
      $("#form-group-" + field).addClass("bad");
    }

    $(this).click(function () {
      $("#em-" + field).focus();
      $("#form-group-" + field).removeClass("bad");
      $(this).fadeOut();
    });

    $(this).find('button').click(function () {
      $("#em-" + field).focus();
      $("#form-group-" + field).removeClass("bad");
    });
  });
});
// /Tooltip
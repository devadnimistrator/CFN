$.datepicker._updateDatepicker_original = $.datepicker._updateDatepicker;
$.datepicker._updateDatepicker = function (inst) {
  $.datepicker._updateDatepicker_original(inst);
  var afterShow = this._get(inst, 'afterShow');
  if (afterShow) {
    afterShow.apply(
            (inst.input ? inst.input[0] : null),
            [(inst.input ? inst.input.val() : ''), inst, inst.dpDiv.find('td:has(a)')]
            );
  }
}

var _oldShow = $.fn.fadeIn;
$.fn.fadeIn = function (speed, oldCallback) {
  return $(this).each(function () {
    var obj = $(this),
            newCallback = function () {
              if ($.isFunction(oldCallback)) {
                oldCallback.apply(obj);
              }
              obj.trigger('afterVisible');
            };

    // you can trigger a before show if you want
    obj.trigger('beforeVisible');

    // now use the old function to show the element passing the new callback
    _oldShow.apply(obj, [speed, newCallback]);
  });
}

function RangeDatepicker(fromInput, endInput, options) {
  var datePickerOptions = {
    dateFormat: 'm/dd/y',
    numberOfMonths: 1,
    changedFrom: false,
    changedEnd: false
  }

  if (options) {
    datePickerOptions = $.extend({}, datePickerOptions, options);
  }
  
  var fromInput = $("#" + fromInput);
  var endInput = $("#" + endInput);
  var selectedInput = null;
  
  var beforeSelectedInput = null;
  
  var fromSelectedDate = false;
  var endSelectedDate = false;
  
  

  function formattedDate(date) {
    var month = String(date.getMonth() + 1);
    var day = String(date.getDate());
    var year = String(date.getFullYear());

    if (month.length < 2)
      month = '0' + month;
    if (day.length < 2)
      day = '0' + day;

    return year + "-" + month + "-" + day;
  }

  function getCalendarDayClass(date) {
    var _class = '';

    var selectedTime = formattedDate(date),
            fromTime = formattedDate(new Date(fromSelectedDate)),
            endTime = formattedDate(new Date(endSelectedDate));

    if (selectedTime == fromTime && selectedTime == endTime) {
      _class = 'date-range-selected date-range-from date-range-end';
    } else if (selectedTime == fromTime) {
      _class = 'date-range-selected date-range-from';
    } else if (selectedTime == endTime) {
      _class = 'date-range-selected date-range-end';
    } else if (fromSelectedDate && fromTime < selectedTime && selectedTime < endTime) {
      _class = 'date-range-selected'
    }

    return _class;
  }
  
  function visibleDatePicker(canlendarDiv) {
    var inputOffset = selectedInput.offset();
    canlendarDivOffset = canlendarDiv.offset();
    if (canlendarDivOffset.top < inputOffset.top) {
      canlendarDiv.addClass('ui-datepicker-up-position');
    } else {
      canlendarDiv.removeClass('ui-datepicker-up-position');
    }
  }

  this.init = function () {
    fromInput.datepicker({
      numberOfMonths: datePickerOptions.numberOfMonths,
      dateFormat: datePickerOptions.dateFormat,
      showAnim: 'fadeIn',
      beforeShow: function () {
        setTimeout(function () {
          $('.ui-datepicker').css('z-index', 999);
        }, 0);
        selectedInput = fromInput;
      },
      beforeShowDay: function (date) {
        return [true, getCalendarDayClass(date)];
      },
      onSelect: function (dateText) {
        fromSelectedDate = new Date(dateText);
        console.log(fromSelectedDate);
        if (endSelectedDate && fromSelectedDate.getDate() >= endSelectedDate.getDate()) {
          endSelectedDate = new Date().setDate(fromSelectedDate.getDate() + 3);
          endInput.datepicker('setDate', new Date(endSelectedDate));
        }
        
        endInput.datepicker('option', 'minDate', new Date(new Date().setDate(fromSelectedDate.getDate() + 1)));
        
        if (datePickerOptions.changedFrom) {
          datePickerOptions.changedFrom.apply();
        }
      },
      onClose: function () {
        //if (!endSelectedDate) {
          endInput.datepicker('show');
        //  beforeSelectedInput = 'from';
        /*} else {
          if (beforeSelectedInput == 'end') {
            beforeSelectedInput = '';
          } else {
            endInput.datepicker('show');
            beforeSelectedInput = 'from';
          }          
        }*/
      },
      afterShow: function (input, inst, td) {
        $(td).hover(function () {
          $(this).addClass('date-pickup');
        }, function () {
          $(this).removeClass('date-pickup');
        });
      }
    }).on("change", function () {
      //$("#dropoff_date").datepicker( "option", "minDate", getPicDropDate( this ) );
    });

    endInput.datepicker({
      numberOfMonths: datePickerOptions.numberOfMonths,
      dateFormat: datePickerOptions.dateFormat,
      showAnim: 'fadeIn',
      minDate: fromInput.datepicker({
        dateFormat: datePickerOptions.dateFormat
      }).val(),
      beforeShow: function () {
        setTimeout(function () {
          $('.ui-datepicker').css('z-index', 999);
        }, 0);
        selectedInput = endInput;
      },
      beforeShowDay: function (date) {
        return [true, getCalendarDayClass(date)];
      },
      onSelect: function (dateText) {
        endSelectedDate = new Date(dateText);
        if (datePickerOptions.changedEnd) {
          datePickerOptions.changedEnd.apply();
        }
      },
      afterShow: function (input, inst, td) {
        $(td).hover(function () {
          $(this).addClass('date-dropoff');
        }, function () {
          $(this).removeClass('date-dropoff');
        });
      },
      onClose: function () {
        if (!fromSelectedDate) {
          fromInput.datepicker('show');
          //beforeSelectedInput = 'end';
        } else {
          /*if (beforeSelectedInput == 'from') {
            beforeSelectedInput = '';
          } else {
            //fromInput.datepicker('show');
            beforeSelectedInput = 'end';
          }*/          
        }
      }
    }).on("change", function () {
      //fromInput.datepicker( "option", "maxDate", getPicDropDate( this ) );
    });

    $("#ui-datepicker-div").bind('afterVisible', function () {
      visibleDatePicker($(this));
    });
  }

  this.setFromDate = function (date) {
    fromSelectedDate = date;
    fromInput.datepicker('setDate', new Date(fromSelectedDate));
  }

  this.setEdnDate = function (date) {
    endSelectedDate = date;
    endInput.datepicker('setDate', new Date(endSelectedDate));
  }

  this.init();
}
//! moment.js locale configuration
//! locale : Spanish [es]
//! author : Julio Napurí : https://github.com/julionc

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined'
          && typeof require === 'function' ? factory(require('../moment')) :
          typeof define === 'function' && define.amd ? define(['../moment'], factory) :
          factory(global.moment)
}(this, (function (moment) {
  'use strict';


  var monthsShortDot = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
  var monthsShort = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

  var spanish = moment.defineLocale('spanish', {
    months: 'enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_diciembre'.split('_'),
    monthsShort: function (m, format) {
      if (!m) {
        return monthsShortDot;
      } else if (/-MMM-/.test(format)) {
        return monthsShort[m.month()];
      } else {
        return monthsShortDot[m.month()];
      }
    },
    monthsParseExact: true,
    weekdays: 'domingo_lunes_martes_miércoles_jueves_viernes_sábado'.split('_'),
    weekdaysShort: 'dom._lun._mar._mié._jue._vie._sáb.'.split('_'),
    weekdaysMin: 'do_lu_ma_mi_ju_vi_sá'.split('_'),
    weekdaysParseExact: true,
    longDateFormat: {
      LT: 'H:mm',
      LTS: 'H:mm:ss',
      L: 'DD/MM/YYYY',
      LL: 'D [de] MMMM [de] YYYY',
      LLL: 'D [de] MMMM [de] YYYY H:mm',
      LLLL: 'dddd, D [de] MMMM [de] YYYY H:mm'
    },
    calendar: {
      sameDay: function () {
        return '[hoy a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
      },
      nextDay: function () {
        return '[mañana a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
      },
      nextWeek: function () {
        return 'dddd [a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
      },
      lastDay: function () {
        return '[ayer a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
      },
      lastWeek: function () {
        return '[el] dddd [pasado a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
      },
      sameElse: 'L'
    },
    relativeTime: {
      future: 'en %s',
      past: 'hace %s',
      s: 'unos segundos',
      m: 'un minuto',
      mm: '%d minutos',
      h: 'una hora',
      hh: '%d horas',
      d: 'un día',
      dd: '%d días',
      M: 'un mes',
      MM: '%d meses',
      y: 'un año',
      yy: '%d años'
    },
    dayOfMonthOrdinalParse: /\d{1,2}º/,
    ordinal: '%dº',
    week: {
      dow: 1, // Monday is the first day of the week.
      doy: 4  // The week that contains Jan 4th is the first week of the year.
    }
  });

  return spanish;

})));

/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood@virginbroadband.com.au) and Stéphane Nahmani (sholby@sholby.net). */
jQuery(function($){
    $.datepicker.regional['en'] = {
        dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true
    };
    $.datepicker.setDefaults($.datepicker.regional['en']);
});
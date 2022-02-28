"use strict";

(function ($, window) {
  window.anm_settings = window.anm_settings || {};

  window.anm_settings.select_appropriate_radio = function (e) {
    if (0 === $(e.target).val().length) {
      $(e.target).closest('fieldset').find('input[type="radio"]').first().prop('checked', true);
    } else {
      $(e.target).prevAll('label').first().find('input[type="radio"]').prop('checked', true);
    }
  };

  window.anm_settings.append_select2_events = function (select2obj) {
    select2obj.on('select2:select', window.anm_settings.select_appropriate_radio).on('select2:unselect', window.anm_settings.select_appropriate_radio).on('change', window.anm_settings.select_appropriate_radio);
  };
})(jQuery, window);
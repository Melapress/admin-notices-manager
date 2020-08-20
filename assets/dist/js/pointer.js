"use strict";

/**
 * Pointer helper.
 *
 * @since 1.0.0
 */
jQuery(document).ready(function ($) {
  function open_pointer(i) {
    var pointer = wpws_pointers[i];
    var options = $.extend(pointer.options, {
      close: function close() {
        $.post(ajaxurl, {
          pointer: pointer.pointer_id,
          action: 'wpws_dismiss_wp_pointer'
        });
      }
    }); //	open the pointer

    $(pointer.target).first().pointer(options).pointer('open');
  } //	open the first pointer


  open_pointer(0);
});
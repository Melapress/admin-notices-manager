"use strict";

(function ($) {
  var AdminNoticesManager = {
    container: null,
    counter_link: null,
    migration_delay: 100,
    migration_interval: null,
    migration_start: 0,
    migration_limit: 5000,
    popup_delay: 50,
    popup_interval: null,
    popup_start: 0,
    popup_limit: 1000,
    removal_interval: null,
    system_messages: [],
    init: function init() {
      var _this2 = this;

      $('body').append('<div id="anm-container" style="display: none;"></div>');
      this.container = $('#anm-container');
      this.counter_link = $('#wp-admin-bar-anm_notification_count');
      this.initTriggers();
      this.migration_start = new Date().getTime();
      this.migration_interval = setInterval(function () {
        _this2.transferNotices();
      }, this.migration_delay);
      var smCount = anm_i18n.system_messages.length;

      for (var i = 0; i < smCount; i++) {
        var systemMessage = anm_i18n.system_messages[i];
        this.system_messages.push(systemMessage.replace(/%[sdf]/g, ''));
      }
    },
    getCurrentCounterValue: function getCurrentCounterValue() {
      var counter_elm = $('.anm-notification-counter span.count');

      if (0 == counter_elm.length) {
        return 0;
      }

      return parseInt(counter_elm.html(), 10);
    },
    getNoticeType: function getNoticeType(noticeElm) {
      var jqNotice = $(noticeElm);

      if (jqNotice.hasClass('notice-system')) {
        return 'system';
      }

      if (jqNotice.hasClass('notice-error')) {
        return 'error';
      }

      if (jqNotice.hasClass('notice-info')) {
        return 'information';
      }

      if (jqNotice.hasClass('notice-warning')) {
        return 'warning';
      }

      if (jqNotice.hasClass('notice-success') || jqNotice.hasClass('updated')) {
        return 'success';
      }

      return 'no';
    },
    checkMigrationInterval: function checkMigrationInterval() {
      //	clear the interval after given time or when there are no notices left to move
      var now = new Date().getTime();
      var time_diff = now - this.migration_start;

      if (time_diff > this.migration_limit) {
        //	stop interval
        clearInterval(this.migration_interval);
        this.migration_interval = null;
      }
    },
    transferNotices: function transferNotices() {
      var _this3 = this;

      var notices = $('#wpbody-content .wrap').find('div.updated, div.error, div.notice, #message').not('.hidden'); //	filter out the system notices

      notices.each(function (index, notice) {
        var smCount = _this3.system_messages.length;

        for (var i = 0; i < smCount; i++) {
          var systemMessage = _this3.system_messages[i];

          if (notice.innerHTML.indexOf(systemMessage) > 0) {
            $(notice).addClass('notice-system');
          }
        }
      });
      var notifications_count = 0;
      var _container = this.container;
      notices.each(function (index, notice) {
        var noticeType = _this3.getNoticeType(notice);

        var actionTypeKey = 'system' === noticeType ? 'wordpress_system_admin_notices' : noticeType + '_level_notices';
        var actionType = anm_i18n.settings[actionTypeKey];

        if ('hide' === actionType) {
          $(notice).remove();
        } else if ('popup-only' === actionType) {
          //	detach notices from the original place and increase the counter
          $(notice).detach().appendTo(_container);
          notifications_count++;
        }
      }); //	number of notifications

      var count_to_show = notifications_count; //	increase counter if already exists

      if (0 < $('.anm-notification-counter').length) {
        count_to_show += this.getCurrentCounterValue();
      }

      this.updateCounterBubble(count_to_show);
      this.checkMigrationInterval();
    },
    updateCounterBubble: function updateCounterBubble(count) {
      if (0 !== count) {
        if (0 < $('.anm-notification-counter').length) {
          var counter_elm = $('.anm-notification-counter span.count');
          counter_elm.html(count);
        } else {
          var title = anm_i18n.title;
          this.counter_link.find('a').html(title);
          var bubble_html = '<div class="anm-notification-counter' + ' wp-core-ui wp-ui-notification">' + '<span aria-hidden="true" class="count">' + count + '</span>' + '<span class="screen-reader-text">' + count + ' ' + title + '</span>' + '</div>';
          this.counter_link.attr('data-popup-title', title);
          this.counter_link.find('a').append(bubble_html);
          this.counter_link.addClass('has-data');
        }
      }
    },
    adjustModalHeight: function adjustModalHeight() {
      $('#TB_ajaxContent').css({
        width: '100%',
        height: $('#TB_window').height() - $('#TB_title').outerHeight() - 22 + 'px',
        padding: '2px 0px 20px 0px'
      }); //	clear the interval after given time

      if (this.popup_interval) {
        var now = new Date().getTime();
        var time_diff = now - this.popup_start;

        if (time_diff > this.popup_limit) {
          clearInterval(this.popup_interval);
          this.popup_interval = null;
        }
      }
    },
    checkNoticeRemoval: function checkNoticeRemoval() {
      if (!$('#TB_ajaxContent').height()) {
        if (this.removal_interval) {
          clearInterval(this.removal_interval);
        }

        return;
      } //	if the popup is open, check if any notices have been removed and update the count accordingly


      var notices_present_count = $('#TB_ajaxContent').children().not(':hidden').length;
      var displayed_count = this.getCurrentCounterValue();

      if (displayed_count !== notices_present_count) {
        this.updateCounterBubble(notices_present_count);
      }
    },
    initTriggers: function initTriggers() {
      var _this = this;

      this.counter_link.click(function () {
        if (_this.popup_interval) {
          clearInterval(_this.popup_interval);
          _this.popup_interval = null;
        }

        if (0 == _this.getCurrentCounterValue()) {
          return false;
        } //	open the ThickBox popup


        tb_show(_this.counter_link.attr('data-popup-title'), '#TB_inline?inlineId=anm-container'); //	start height adjustment using interval (there is no callback nor event to hook into)

        _this.popup_start = new Date().getTime();
        _this.popup_interval = setInterval(function () {
          _this.adjustModalHeight.call(_this);
        }, _this.popup_delay);

        if (_this.removal_interval) {
          clearInterval(_this.removal_interval);
        }

        _this.removal_interval = setInterval(function () {
          _this.checkNoticeRemoval.call(_this);
        }, _this.popup_delay);
        return false;
      });
      $(window).resize(function () {
        //	adjust thick box modal height on window resize
        _this.adjustModalHeight.call(_this);
      });
    }
  };
  AdminNoticesManager.init();
})(jQuery);
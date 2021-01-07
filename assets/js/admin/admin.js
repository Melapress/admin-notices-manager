(function ($) {
	const AdminNoticesManager = {
		container: null,
		counter_link: null,
		migration_delay: 100,
		migration_interval: null,
		migration_start: 0,
		migration_limit: 2000,
		popup_delay: 50,
		popup_interval: null,
		popup_start: 0,
		popup_limit: 1000,
		system_messages: [],
		init () {
			$('body').append('<div id="anm-container" style="display: none;"></div>')
			this.container = $('#anm-container')
			this.counter_link = $('#wp-admin-bar-anm_notification_count')

			this.init_triggers()

			this.migration_start = new Date().getTime()
			this.migration_interval = setInterval(() => {
				this.transfer_notices()
			}, this.migration_delay)

			const smCount = anm_i18n.system_messages.length
			for (let i = 0; i < smCount; i++) {
				const systemMessage = anm_i18n.system_messages[i]
				this.system_messages.push(systemMessage.replace(/%[sdf]/g, ''))
			}
		},
		get_current_counter_value () {
			let counter_elm = $('.anm-notification-counter span.count')
			if (0 == counter_elm.length) {
				return 0
			}
			return parseInt(counter_elm.html(), 10)
		},
		get_notice_type (noticeElm) {
			var jqNotice = $(noticeElm)
			if (jqNotice.hasClass('notice-system')) {
				return 'system'
			}

			if (jqNotice.hasClass('notice-error')) {
				return 'error'
			}

			if (jqNotice.hasClass('notice-info')) {
				return 'information'
			}

			if (jqNotice.hasClass('notice-warning')) {
				return 'warning'
			}

			if (jqNotice.hasClass('notice-success') || jqNotice.hasClass('updated')) {
				return 'success'
			}

			return 'no'
		},
		check_migration_interval () {
			//	clear the interval after given time or when there are no notices left to move
			let now = new Date().getTime()
			let time_diff = now - this.migration_start
			if (time_diff > this.migration_limit || 0 == $('#wpbody-content ,wrap').children('div.updated, div.error, div.notice, #message').not('.hidden').length) {

				//	stop interval
				clearInterval(this.migration_interval)
				this.migration_interval = null
			}
		},
		transfer_notices () {
			let notices = $('#wpbody-content .wrap').children('div.updated, div.error, div.notice, #message').not('.hidden')

			//	filter out the system notices
			notices = notices.filter((index, notice) => {
				const smCount = this.system_messages.length
				for (let i = 0; i < smCount; i++) {
					const systemMessage = this.system_messages[i]
					if (notice.innerHTML.indexOf(systemMessage) > 0) {
						$(notice).addClass('notice-system')
					}
				}
				return true
			})

			if (1 > notices.length) {
				this.counter_link.find('a').html(anm_i18n.title_empty)
				this.check_migration_interval()
				return
			}

			let notifications_count = 0
			const _container = this.container
			notices.each((index, notice) => {
				const noticeType = this.get_notice_type(notice)
				const actionTypeKey = ('system' === noticeType) ? 'wordpress_system_admin_notices' : noticeType + '_level_notices'
				const actionType = anm_i18n.settings[actionTypeKey]
				if ('hide' === actionType) {
					$(notice).remove()
				} else if ('popup-only' === actionType) {
					//	detach notices from the original place and increase the counter
					$(notice).detach().appendTo(_container)
					notifications_count++
				}
			})

			if (0 === notifications_count) {
				this.counter_link.find('a').html(anm_i18n.title_empty)
				this.check_migration_interval()
				return
			}

			//	increase counter if already exists
			if (0 < $('.anm-notification-counter').length) {
				let counter_elm = $('.anm-notification-counter span.count')
				let existing_count = this.get_current_counter_value()
				counter_elm.html(existing_count + notifications_count)
			} else {
				let title = anm_i18n.title
				this.counter_link.find('a').html(title)
				const bubble_html = '<div class="anm-notification-counter' +
					' wp-core-ui wp-ui-notification">' +
					'<span aria-hidden="true" class="count">' + notifications_count + '</span>' +
					'<span class="screen-reader-text">' + notifications_count + ' ' + title + '</span>' +
					'</div>'

				this.counter_link.attr('data-popup-title', title)
				this.counter_link.find('a').append(bubble_html)
				this.counter_link.addClass('has-data')
			}

			this.check_migration_interval()
		},
		adjust_modal_height () {
			$('#TB_ajaxContent').css({
				width: '100%',
				height: ($('#TB_window').height() - $('#TB_title').outerHeight() - 22) + 'px',
				padding: '2px 0px 20px 0px'
			})

			//	clear the interval after given time
			if (this.popup_interval) {
				let now = new Date().getTime()
				let time_diff = now - this.popup_start
				if (time_diff > this.popup_limit) {
					clearInterval(this.popup_interval)
					this.popup_interval = null
				}
			}
		},
		init_triggers () {
			let _this = this
			this.counter_link.click(function () {
				if (_this.popup_interval) {
					clearInterval(_this.popup_interval)
					_this.popup_interval = null
				}

				if (0 == _this.get_current_counter_value()) {
					return false
				}

				//	open the ThickBox popup
				tb_show(_this.counter_link.attr('data-popup-title'), '#TB_inline?inlineId=anm-container')

				//	start height adjustment using interval (there is no callback nor event to hook into)
				_this.popup_start = new Date().getTime()
				_this.popup_interval = setInterval(function () {
					_this.adjust_modal_height.call(_this)
				}, _this.popup_delay)
				return false
			})

			$(window).resize(function () {

				//	adjust thick box modal height on window resize
				_this.adjust_modal_height.call(_this)
			})
		}
	}

	AdminNoticesManager.init()
}(jQuery))

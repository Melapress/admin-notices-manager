( function( $ ) {
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
		init() {
			$( 'body' ).append( '<div id="anm-container" style="display: none;"></div>' );
			this.container = $( '#anm-container' );
			this.counter_link = $( '#wp-admin-bar-anm_notification_count' );

			this.init_triggers();

			this.migration_start = new Date().getTime();
			this.migration_interval = setInterval( () => {
				this.transfer_notices();
			}, this.migration_delay );
		},
		transfer_notices() {
			let notices = $( '#wpbody-content .wrap' ).find( 'div.updated, div.error, div.notice, #message' ).not( '.hidden' );
			let notifications_count = notices.length;
			if ( 1 > notifications_count ) {
				return;
			}

			notices.detach().appendTo( this.container );

			//	increase counter if already exists
			if ( 0 < $( '.anm-notification-counter' ).length ) {
				let counter_elm = $( '.anm-notification-counter span.count' );
				let existing_count = parseInt( counter_elm.html(), 10 );
				counter_elm.html( existing_count + notifications_count );
			} else {
				let title = this.counter_link.find( 'a' ).text();
				const bubble_html = '<div class="anm-notification-counter' +
					' wp-core-ui wp-ui-notification">' +
					'<span aria-hidden="true" class="count">' + notifications_count + '</span>' +
					'<span class="screen-reader-text">' + notifications_count + ' ' + title + '</span>' +
					'</div>';

				this.counter_link.attr( 'data-popup-title', title );
				this.counter_link.find( 'a' ).append( bubble_html );
				this.counter_link.show();
			}

			//	clear the interval after given time or when there are no notices left to move
			let now = new Date().getTime();
			let time_diff = now - this.migration_start;
			if ( time_diff > this.migration_limit || 0 == $( '#wpbody-content' ).find( 'div.updated, div.error, div.notice, #message' ).not( '.hidden' ).length ) {

				//	stop interval
				clearInterval( this.migration_interval );
				this.migration_interval = null
			}
		},
		adjust_modal_height() {
			$( '#TB_ajaxContent' ).css({
				width: '100%',
				height: ( $( '#TB_window' ).height() - $( '#TB_title' ).outerHeight() - 22 ) + 'px',
				padding: '2px 0px 20px 0px'
			});

			//	clear the interval after given time
			if ( this.popup_interval ) {
				let now = new Date().getTime();
				let time_diff = now - this.popup_start;
				if (time_diff > this.popup_limit) {
					clearInterval(this.popup_interval);
					this.popup_interval = null
				}
			}
		},
		init_triggers() {
			let _this = this;
			this.counter_link.click( function() {
				if ( _this.popup_interval ) {
					clearInterval( _this.popup_interval );
					_this.popup_interval = null
				}

				//	open the ThickBox popup
				tb_show( _this.counter_link.attr( 'data-popup-title' ), '#TB_inline?inlineId=anm-container' );

				//	start height adjustment using interval (there is no callback nor event to hook into)
				_this.popup_start = new Date().getTime();
				_this.popup_interval = setInterval( function() {
					_this.adjust_modal_height.call(_this);
				}, _this.popup_delay );
				return false;
			});

			$( window ).resize(function() {

				//	adjust thick box modal height on window resize
				_this.adjust_modal_height.call(_this);
			});
		}
	};

	AdminNoticesManager.init();
}( jQuery ) );

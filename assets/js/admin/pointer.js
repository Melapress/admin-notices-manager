/**
 * Pointer helper.
 *
 * @since 1.0.0
 */
jQuery( document ).ready( function( $ ) {
	function open_pointer( i ) {
		const pointer = wpws_pointers[i];
		let options = pointer.options;
		/*
		let options = $.extend( pointer.options, {
			close: function() {
				$.post( ajaxurl, {
					pointer: pointer.id,
					action: 'wpws_dismiss_wp_pointer'
				});
			}
		});
*/
		console.log(pointer);
		$( pointer.target ).first().pointer( options ).pointer( 'open' );
	}

	//	open the first pointer
	open_pointer( 0 );
});

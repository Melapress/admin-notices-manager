<?php
/**
 * Class adding plugin's pointers.
 *
 * @package AdminNoticesManager
 */

declare(strict_types=1);

namespace AdminNoticesManager;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AdminNoticesManager\PluginUpdatedBanner' ) ) {
	/**
	 * Responsible for update notices.
	 *
	 * @since 1.6.0
	 */
	class PluginUpdatedBanner {

		/**
		 * Show notice to recently updated plugin.
		 *
		 * @return void
		 *
		 * @since 1.6.0
		 */
		public static function plugin_was_updated_banner() {
			$show_update_notice = get_site_option( 'anm_update_notice_needed', false );

			if ( $show_update_notice ) {
				?>
				<!-- Copy START -->
				<div class="anm-plugin-update update-message">
					<div class="anm-plugin-update-content">
						<h2 class="anm-plugin-update-title"><?php esc_html_e( 'Admin Notices Manager has been updated to version', 'admin-notices-manager' ); ?> <?php echo esc_attr( ADMIN_NOTICES_MANAGER_VERSION ); ?>.</h2>
						<p class="anm-plugin-update-text">
							<?php esc_html_e( 'You are now running the latest version of Admin Notices Manager. To see what\'s been included in this update, refer to the plugin\'s release notes and change log where we list all new features, updates, and bug fixes.', 'admin-notices-manager' ); ?>							
						</p>
						<a href="https://wordpress.org/plugins/admin-notices-manager/#developers" target="_blank" class="anm-cta-link"><?php esc_html_e( 'Read the release notes', 'admin-notices-manager' ); ?></a>
					</div>
					<button aria-label="Close button" class="anm-plugin-update-close" data-dismiss-nonce="<?php echo esc_attr( wp_create_nonce( 'anm_dismiss_update_notice_nonce' ) ); ?>"></button>
				</div>
				<!-- Copy END -->
				
				<script type="text/javascript">
				jQuery( '.anm-plugin-update' ).insertAfter( '.anm-notices-wrapper' );
				//<![CDATA[
				jQuery(document).ready(function( $ ) {
					jQuery( 'body' ).on( 'click', '.anm-plugin-update-close', function ( e ) {
						var nonce  = jQuery( '.anm-plugin-update [data-dismiss-nonce]' ).attr( 'data-dismiss-nonce' );
						
						jQuery.ajax({
							type: 'POST',
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							async: true,
							data: {
								action: 'dismiss_anm_update_notice',
								nonce : nonce,
							},
							success: function ( result ) {		
								jQuery( '.anm-plugin-update' ).slideUp( 300 );
							}
						});
					});
				});
				//]]>
				</script>
				
				<style type="text/css">
					/* Melapress brand font 'Quicksand' — There maybe be a preferable way to add this but this seemed the most discrete. */
					@font-face {
						font-family: 'Quicksand';
						src: url('<?php echo \esc_url( ADMIN_NOTICES_MANAGER_URL ); ?>assets/fonts/Quicksand-VariableFont_wght.woff2') format('woff2');
						font-weight: 100 900; /* This indicates that the variable font supports weights from 100 to 900 */
						font-style: normal;
					}
					
					.anm-plugin-update, .anm-plugin-data-migration {
						background-color: #1A3060;
						border-radius: 7px;
						color: #fff;
						display: flex;
						justify-content: space-between;
						align-items: center;
						padding: 1.66rem;
						position: relative;
						overflow: hidden;
						transition: all 0.2s ease-in-out;
						margin-top: 20px;
						margin-right: 20px;
						width: calc(100% - 80px);
					}
				

					.anm-plugin-update-content {
						max-width: 45%;
					}
					
					.anm-plugin-update-title {
						margin: 0;
						font-size: 20px;
						font-weight: bold;
						font-family: Quicksand, sans-serif;
						line-height: 1.44rem;
						color: #fff;
					}
					
					.anm-plugin-update-text {
						margin: .25rem 0 0;
						font-size: 0.875rem;
						line-height: 1.3125rem;
					}
					
					.anm-plugin-update-text a:link {
						color: #FF8977;
					}
					
					.anm-cta-link {
						border-radius: 0.25rem;
						background: #FF8977;
						color: #0000EE;
						font-weight: bold;
						text-decoration: none;
						font-size: 0.875rem;
						padding: 0.675rem 1.3rem .7rem 1.3rem;
						transition: all 0.2s ease-in-out;
						display: inline-block;
						margin: .5rem auto;
					}
					
					.anm-cta-link:hover {
						background: #0000EE;
						color: #FF8977;
					}
					
					.anm-plugin-update-close {
						background-image: url(<?php echo esc_url( ADMIN_NOTICES_MANAGER_URL ) . 'assets/images/close-icon-rev.svg'; ?>); /* Path to your close icon */
						background-size: cover;
						width: 18px;
						height: 18px;
						border: none;
						cursor: pointer;
						position: absolute;
						top: 20px;
						right: 20px;
						background-color: transparent;
					}
					
					.anm-plugin-update::before {
						content: '';
						background-image: url(<?php echo esc_url( ADMIN_NOTICES_MANAGER_URL ) . 'assets/images/anm-updated-bg.png'; ?>); /* Background image only displayed on desktop */
						background-size: 100%;
						background-repeat: no-repeat;
						background-position: 100% 51%;
						position: absolute;
						top: 0;
						right: 0;
						bottom: 0;
						left: 0;
						z-index: 0;
					}
					
					.anm-plugin-update-content, .anm-plugin-update-close {
						z-index: 1;
					}
					
					@media (max-width: 1200px) {
						.anm-plugin-update::before {
							display: none;
						}
					
						.anm-plugin-update-content {
							max-width: 100%;
						}
					}

					.anm-plugin-data-migration {
						background-color: #D9E4FD;						
					}

					.anm-plugin-data-migration * {
						color: #1A3060;
					}

					.anm-plugin-data-migration .anm-plugin-update-content {
						min-height: 80px;
					}
						
					#spinning-wrapper {
						position: absolute;
						right: -20px;
						height: 300px;
						width: 300px;
					}

					#spinning-wrapper .dashicons {
						height: 300px;
						height: 300px;
						font-size: 300px;
					}

					#spinning-wrapper  * {
						color: #8AAAF1 !important;
					}

					#spinning-wrapper.active {
						-webkit-animation: spin 4s infinite linear;
					}

					@-webkit-keyframes spin {
						0%  {-webkit-transform: rotate(0deg);}
						100% {-webkit-transform: rotate(360deg);}   
					}
				</style>
				<?php
			}
		}

		/**
		 * Handle notice dismissal.
		 *
		 * @return void
		 *
		 * @since 1.6.0
		 */
		public static function dismiss_update_notice() {
			// Grab POSTed data.
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

			// Check nonce.
			if ( ! current_user_can( 'manage_options' ) || empty( $nonce ) || ! $nonce || ! wp_verify_nonce( $nonce, 'anm_dismiss_update_notice_nonce' ) ) {
				wp_send_json_error( esc_html__( 'Nonce Verification Failed.', 'admin-notices-manager' ) );
			}

			delete_site_option( 'anm_update_notice_needed' );

			wp_send_json_success( esc_html__( 'Complete.', 'admin-notices-manager' ) );
		}
	}
}

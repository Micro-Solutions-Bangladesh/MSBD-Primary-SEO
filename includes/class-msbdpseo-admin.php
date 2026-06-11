<?php
/**
 * Admin class.
 *
 * @package MSBD_Primary_SEO
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MSBDPSEO_Admin' ) ) :

	/**
	 * Admin settings pages for site and network contexts.
	 */
	final class MSBDPSEO_Admin {

		/**
		 * Site admin page slug.
		 *
		 * @var string
		 */
		private const SITE_PAGE_SLUG = 'msbd-primary-seo';

		/**
		 * Site Schema.org settings page slug.
		 *
		 * @var string
		 */
		private const SITE_SCHEMA_PAGE_SLUG = 'msbd-primary-seo-schema';

		/**
		 * Network admin page slug.
		 *
		 * @var string
		 */
		private const NETWORK_PAGE_SLUG = 'msbd-primary-seo-network';

		/**
		 * Network Schema.org settings page slug.
		 *
		 * @var string
		 */
		private const NETWORK_SCHEMA_PAGE_SLUG = 'msbd-primary-seo-network-schema';

		/**
		 * Site save action.
		 *
		 * @var string
		 */
		private const SITE_SAVE_ACTION = 'msbdpseo_save_site_settings';

		/**
		 * Site Schema.org save action.
		 *
		 * @var string
		 */
		private const SITE_SCHEMA_SAVE_ACTION = 'msbdpseo_save_site_schema_settings';

		/**
		 * Network save action.
		 *
		 * @var string
		 */
		private const NETWORK_SAVE_ACTION = 'msbdpseo_save_network_settings';

		/**
		 * Network Schema.org save action.
		 *
		 * @var string
		 */
		private const NETWORK_SCHEMA_SAVE_ACTION = 'msbdpseo_save_network_schema_settings';

		/**
		 * Nonce field name.
		 *
		 * @var string
		 */
		private const NONCE_NAME = 'msbdpseo_nonce';

		/**
		 * Singleton instance.
		 *
		 * @var MSBDPSEO_Admin|null
		 */
		private static $instance = null;

		/**
		 * Initialize admin class.
		 *
		 * @return MSBDPSEO_Admin
		 */
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {
			add_action( 'admin_menu', array( $this, 'register_site_menu' ) );
			add_action( 'admin_init', array( $this, 'register_site_settings' ) );
			add_action( 'admin_init', array( $this, 'maybe_save_site_settings' ) );
			add_action( 'admin_init', array( $this, 'maybe_save_site_schema_settings' ) );
			add_filter( 'plugin_row_meta', array( $this, 'add_site_settings_meta_link' ), 10, 2 );
			add_action( 'add_meta_boxes', array( $this, 'register_social_image_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_social_image_meta_box' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				add_action( 'network_admin_menu', array( $this, 'register_network_menu' ) );
				add_action( 'network_admin_edit_' . self::NETWORK_SAVE_ACTION, array( $this, 'save_network_settings' ) );
				add_action( 'network_admin_edit_' . self::NETWORK_SCHEMA_SAVE_ACTION, array( $this, 'save_network_schema_settings' ) );
				add_action( 'network_admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			}
		}

		/**
		 * Register normal site admin menu.
		 *
		 * @return void
		 */
		public function register_site_menu() {
			add_menu_page(
				__( 'MSBD Primary SEO', 'msbd-primary-seo' ),
				__( 'Primary SEO', 'msbd-primary-seo' ),
				'manage_options',
				self::SITE_PAGE_SLUG,
				array( $this, 'render_site_settings_page' ),
				'dashicons-search',
				81
			);

			add_submenu_page(
				self::SITE_PAGE_SLUG,
				__( 'MSBD Primary SEO Settings', 'msbd-primary-seo' ),
				__( 'Settings', 'msbd-primary-seo' ),
				'manage_options',
				self::SITE_PAGE_SLUG,
				array( $this, 'render_site_settings_page' )
			);

			add_submenu_page(
				self::SITE_PAGE_SLUG,
				__( 'Basic Schema.org JSON-LD', 'msbd-primary-seo' ),
				__( 'Schema.org JSON-LD', 'msbd-primary-seo' ),
				'manage_options',
				self::SITE_SCHEMA_PAGE_SLUG,
				array( $this, 'render_site_schema_settings_page' )
			);
		}

		/**
		 * Register network admin menu.
		 *
		 * @return void
		 */
		public function register_network_menu() {
			add_menu_page(
				__( 'MSBD Primary SEO - Network Settings', 'msbd-primary-seo' ),
				__( 'Primary SEO', 'msbd-primary-seo' ),
				'manage_network_options',
				self::NETWORK_PAGE_SLUG,
				array( $this, 'render_network_settings_page' ),
				'dashicons-search',
				81
			);

			add_submenu_page(
				self::NETWORK_PAGE_SLUG,
				__( 'MSBD Primary SEO - Network Settings', 'msbd-primary-seo' ),
				__( 'Settings', 'msbd-primary-seo' ),
				'manage_network_options',
				self::NETWORK_PAGE_SLUG,
				array( $this, 'render_network_settings_page' )
			);

			add_submenu_page(
				self::NETWORK_PAGE_SLUG,
				__( 'Basic Schema.org JSON-LD - Network Settings', 'msbd-primary-seo' ),
				__( 'Schema.org JSON-LD', 'msbd-primary-seo' ),
				'manage_network_options',
				self::NETWORK_SCHEMA_PAGE_SLUG,
				array( $this, 'render_network_schema_settings_page' )
			);
		}

		/**
		 * Add a site settings link to the plugin row meta on the Site Admin plugins screen.
		 *
		 * @param array<int|string, string> $links Plugin row meta links.
		 * @param string                   $file  Current plugin basename.
		 * @return array<int|string, string>
		 */
		public function add_site_settings_meta_link( $links, $file ) {
			if ( MSBDPSEO_BASENAME !== $file ) {
				return $links;
			}

			if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
				return $links;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return $links;
			}

			$links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( admin_url( 'admin.php?page=' . self::SITE_PAGE_SLUG ) ),
				esc_html__( 'Site Settings', 'msbd-primary-seo' )
			);

			return $links;
		}

		/**
		 * Register site settings for WordPress awareness.
		 *
		 * @return void
		 */
		public function register_site_settings() {
			foreach ( MSBDPSEO_Helper::get_fields() as $field_id => $field ) {
				register_setting(
					'msbdpseo_site_settings',
					MSBDPSEO_Helper::option_key( $field_id ),
					array(
						'type'              => 'string',
						'sanitize_callback' => array( 'MSBDPSEO_Helper', 'sanitize_seo_code' ),
						'default'           => '',
					)
				);
			}

			foreach ( MSBDPSEO_Helper::get_image_fields() as $field_id => $field ) {
				register_setting(
					'msbdpseo_site_settings',
					MSBDPSEO_Helper::option_key( $field_id ),
					array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => 0,
					)
				);
			}

		}

		/**
		 * Save site-level settings.
		 *
		 * @return void
		 */
		public function maybe_save_site_settings() {
			if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
				return;
			}

			$action = isset( $_POST['msbdpseo_action'] )
				? sanitize_key( wp_unslash( $_POST['msbdpseo_action'] ) )
				: '';

			if ( self::SITE_SAVE_ACTION !== $action ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to manage these settings.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			check_admin_referer( self::SITE_SAVE_ACTION, self::NONCE_NAME );

			$this->save_posted_values( 'site', 'general' );

			$redirect_url = add_query_arg(
				array(
					'page'             => self::SITE_PAGE_SLUG,
					'settings-updated' => 'true',
				),
				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect_url );
			exit;
		}

		/**
		 * Save site-level Schema.org JSON-LD settings.
		 *
		 * @return void
		 */
		public function maybe_save_site_schema_settings() {
			if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
				return;
			}

			$action = isset( $_POST['msbdpseo_action'] )
				? sanitize_key( wp_unslash( $_POST['msbdpseo_action'] ) )
				: '';

			if ( self::SITE_SCHEMA_SAVE_ACTION !== $action ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to manage these settings.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			check_admin_referer( self::SITE_SCHEMA_SAVE_ACTION, self::NONCE_NAME );

			$this->save_posted_values( 'site', 'schema' );

			$redirect_url = add_query_arg(
				array(
					'page'             => self::SITE_SCHEMA_PAGE_SLUG,
					'settings-updated' => 'true',
				),
				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect_url );
			exit;
		}

		/**
		 * Save network-level settings.
		 *
		 * @return void
		 */
		public function save_network_settings() {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				wp_die(
					esc_html__( 'Network settings are only available on WordPress Multisite.', 'msbd-primary-seo' ),
					esc_html__( 'Invalid request', 'msbd-primary-seo' ),
					array( 'response' => 400 )
				);
			}

			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to manage network settings.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			check_admin_referer( self::NETWORK_SAVE_ACTION, self::NONCE_NAME );

			$this->save_posted_values( 'network', 'general' );

			$redirect_url = add_query_arg(
				array(
					'page'    => self::NETWORK_PAGE_SLUG,
					'updated' => 'true',
				),
				network_admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect_url );
			exit;
		}

		/**
		 * Save network-level Schema.org JSON-LD settings.
		 *
		 * @return void
		 */
		public function save_network_schema_settings() {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				wp_die(
					esc_html__( 'Network settings are only available on WordPress Multisite.', 'msbd-primary-seo' ),
					esc_html__( 'Invalid request', 'msbd-primary-seo' ),
					array( 'response' => 400 )
				);
			}

			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to manage network settings.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			check_admin_referer( self::NETWORK_SCHEMA_SAVE_ACTION, self::NONCE_NAME );

			$this->save_posted_values( 'network', 'schema' );

			$redirect_url = add_query_arg(
				array(
					'page'    => self::NETWORK_SCHEMA_PAGE_SLUG,
					'updated' => 'true',
				),
				network_admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect_url );
			exit;
		}

		/**
		 * Save posted values for a settings section.
		 *
		 * @param string $scope   Either site or network.
		 * @param string $section Either general or schema.
		 * @return void
		 */
		private function save_posted_values( $scope, $section = 'general' ) {
			if ( ! $this->verify_settings_nonce( $scope, $section ) ) {
				wp_die(
					esc_html__( 'Security check failed. Please reload the page and try again.', 'msbd-primary-seo' ),
					esc_html__( 'Invalid request', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			$posted    = $this->get_posted_settings_array( 'msbdpseo' );
			$overrides = $this->get_posted_settings_array( 'msbdpseo_override' );

			if ( 'schema' === $section ) {
				$this->save_schema_values( $posted, $scope, $overrides );
				return;
			}

			$this->save_general_values( $posted, $scope, $overrides );
		}

		/**
		 * Verify the settings form nonce before processing posted settings data.
		 *
		 * @param string $scope   Either site or network.
		 * @param string $section Either general or schema.
		 * @return bool
		 */
		private function verify_settings_nonce( $scope, $section ) {
			$action = 'schema' === $section
				? ( 'network' === $scope ? self::NETWORK_SCHEMA_SAVE_ACTION : self::SITE_SCHEMA_SAVE_ACTION )
				: ( 'network' === $scope ? self::NETWORK_SAVE_ACTION : self::SITE_SAVE_ACTION );

			$nonce = filter_input( INPUT_POST, self::NONCE_NAME, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			if ( ! is_string( $nonce ) || '' === $nonce ) {
				return false;
			}

			return (bool) wp_verify_nonce( $nonce, $action );
		}

		/**
		 * Retrieve a posted plugin settings array. Values are sanitized later per field type.
		 *
		 * @param string $key Posted array key.
		 * @return array<string, mixed>
		 */
		private function get_posted_settings_array( $key ) {
			$key = sanitize_key( $key );

			if ( '' === $key ) {
				return array();
			}

			$posted = filter_input( INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			return is_array( $posted ) ? $posted : array();
		}

		/**
		 * Save posted general SEO code and image values.
		 *
		 * @param array<string, mixed> $posted    Posted plugin values.
		 * @param string               $scope     Either site or network.
		 * @param array<string, mixed> $overrides Posted site override flags.
		 * @return void
		 */
		private function save_general_values( $posted, $scope, $overrides = array() ) {
			foreach ( MSBDPSEO_Helper::get_fields() as $field_id => $field ) {
				$raw_value = '';

				if ( isset( $posted[ $field_id ] ) && is_scalar( $posted[ $field_id ] ) ) {
					$raw_value = (string) $posted[ $field_id ];
				}

				$value = MSBDPSEO_Helper::sanitize_seo_code( $raw_value );

				if ( 'network' === $scope ) {
					MSBDPSEO_Helper::update_network_option_value( $field_id, $value );
				} else {
					MSBDPSEO_Helper::update_site_option_value( $field_id, $value );
					MSBDPSEO_Helper::update_site_override( $field_id, isset( $overrides[ $field_id ] ) );
				}
			}

			foreach ( MSBDPSEO_Helper::get_image_fields() as $field_id => $field ) {
				$attachment_id = 0;

				if ( isset( $posted[ $field_id ] ) && is_scalar( $posted[ $field_id ] ) ) {
					$attachment_id = absint( $posted[ $field_id ] );
				}

				if ( 'network' === $scope ) {
					MSBDPSEO_Helper::update_network_image_option_value( $field_id, $attachment_id );
				} else {
					MSBDPSEO_Helper::update_site_image_option_value( $field_id, $attachment_id );
					MSBDPSEO_Helper::update_site_override( $field_id, isset( $overrides[ $field_id ] ) );
				}
			}
		}

		/**
		 * Save posted Schema.org JSON-LD values.
		 *
		 * @param array<string, mixed> $posted    Posted plugin values.
		 * @param string               $scope     Either site or network.
		 * @param array<string, mixed> $overrides Posted site override flags.
		 * @return void
		 */
		private function save_schema_values( $posted, $scope, $overrides = array() ) {
			foreach ( MSBDPSEO_Helper::get_schema_fields() as $field_id => $field ) {
				$type      = isset( $field['type'] ) ? $field['type'] : 'text';
				$raw_value = 'checkbox' === $type ? '0' : '';

				if ( isset( $posted[ $field_id ] ) && is_scalar( $posted[ $field_id ] ) ) {
					$raw_value = (string) $posted[ $field_id ];
				}

				MSBDPSEO_Helper::update_schema_value( $field_id, $raw_value, $scope );

				if ( 'site' === $scope ) {
					MSBDPSEO_Helper::update_site_override( $field_id, isset( $overrides[ $field_id ] ) );
				}
			}
		}

		/**
		 * Render site settings page.
		 *
		 * @return void
		 */
		public function render_site_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to view this page.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			$values = $this->get_site_values();

			?>
			<div class="wrap">
				<h1><?php echo esc_html__( 'MSBD Primary SEO', 'msbd-primary-seo' ); ?></h1>

				<?php
				$this->render_notice_from_query_arg(
					'settings-updated',
					__( 'Site SEO settings saved.', 'msbd-primary-seo' )
				);
				?>

				<p>
					<?php
					echo esc_html__(
						'Add site-specific SEO scripts, meta tags, verification tags, noscript markup, or comments.',
						'msbd-primary-seo'
					);
					?>
				</p>

				<?php if ( function_exists( 'is_multisite' ) && is_multisite() ) : ?>
					<p>
						<?php
						echo esc_html__(
							'On multisite network activation, network SEO settings are inherited unless a field is overridden on this site.',
							'msbd-primary-seo'
						);
						?>
					</p>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SITE_PAGE_SLUG ) ); ?>">
					<?php wp_nonce_field( self::SITE_SAVE_ACTION, self::NONCE_NAME ); ?>
					<input type="hidden" name="msbdpseo_action" value="<?php echo esc_attr( self::SITE_SAVE_ACTION ); ?>" />

					<?php $this->render_settings_table( $values, 'site' ); ?>

					<?php submit_button( __( 'Save Site Settings', 'msbd-primary-seo' ) ); ?>
				</form>
			</div>
			<?php
		}

		/**
		 * Render site Schema.org JSON-LD settings page.
		 *
		 * @return void
		 */
		public function render_site_schema_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to view this page.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			$values = $this->get_site_values();

			?>
			<div class="wrap">
				<h1><?php echo esc_html__( 'Basic Schema.org JSON-LD', 'msbd-primary-seo' ); ?></h1>

				<?php
				$this->render_notice_from_query_arg(
					'settings-updated',
					__( 'Schema.org JSON-LD settings saved.', 'msbd-primary-seo' )
				);
				?>

				<p>
					<?php
					echo esc_html__(
						'Enable lightweight structured data output for safe, general schema types. JSON-LD is printed in the document head.',
						'msbd-primary-seo'
					);
					?>
				</p>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SITE_SCHEMA_PAGE_SLUG ) ); ?>">
					<?php wp_nonce_field( self::SITE_SCHEMA_SAVE_ACTION, self::NONCE_NAME ); ?>
					<input type="hidden" name="msbdpseo_action" value="<?php echo esc_attr( self::SITE_SCHEMA_SAVE_ACTION ); ?>" />

					<?php $this->render_schema_settings_table( $values, 'site' ); ?>

					<?php submit_button( __( 'Save Schema.org Settings', 'msbd-primary-seo' ) ); ?>
				</form>
			</div>
			<?php
		}

		/**
		 * Render network settings page.
		 *
		 * @return void
		 */
		public function render_network_settings_page() {
			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to view this page.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			$values = $this->get_network_values();

			?>
			<div class="wrap">
				<h1><?php echo esc_html__( 'MSBD Primary SEO - Network Settings', 'msbd-primary-seo' ); ?></h1>

				<?php
				$this->render_notice_from_query_arg(
					'updated',
					__( 'Network SEO settings saved.', 'msbd-primary-seo' )
				);
				?>

				<p>
					<?php
					echo esc_html__(
						'Network-level SEO settings act as defaults for every subsite. A subsite value is used only when that field is explicitly overridden on the subsite.',
						'msbd-primary-seo'
					);
					?>
				</p>

				<form method="post" action="<?php echo esc_url( network_admin_url( 'edit.php?action=' . self::NETWORK_SAVE_ACTION ) ); ?>">
					<?php wp_nonce_field( self::NETWORK_SAVE_ACTION, self::NONCE_NAME ); ?>

					<?php $this->render_settings_table( $values, 'network' ); ?>

					<?php submit_button( __( 'Save Network Settings', 'msbd-primary-seo' ) ); ?>
				</form>
			</div>
			<?php
		}

		/**
		 * Render network Schema.org JSON-LD settings page.
		 *
		 * @return void
		 */
		public function render_network_schema_settings_page() {
			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die(
					esc_html__( 'Sorry, you are not allowed to view this page.', 'msbd-primary-seo' ),
					esc_html__( 'Permission denied', 'msbd-primary-seo' ),
					array( 'response' => 403 )
				);
			}

			$values = $this->get_network_values();

			?>
			<div class="wrap">
				<h1><?php echo esc_html__( 'Basic Schema.org JSON-LD - Network Settings', 'msbd-primary-seo' ); ?></h1>

				<?php
				$this->render_notice_from_query_arg(
					'updated',
					__( 'Network Schema.org JSON-LD settings saved.', 'msbd-primary-seo' )
				);
				?>

				<p>
					<?php
					echo esc_html__(
						'Configure network-level structured data defaults. A subsite inherits each schema setting unless that field is explicitly overridden on the subsite.',
						'msbd-primary-seo'
					);
					?>
				</p>

				<form method="post" action="<?php echo esc_url( network_admin_url( 'edit.php?action=' . self::NETWORK_SCHEMA_SAVE_ACTION ) ); ?>">
					<?php wp_nonce_field( self::NETWORK_SCHEMA_SAVE_ACTION, self::NONCE_NAME ); ?>

					<?php $this->render_schema_settings_table( $values, 'network' ); ?>

					<?php submit_button( __( 'Save Network Schema.org Settings', 'msbd-primary-seo' ) ); ?>
				</form>
			</div>
			<?php
		}

		/**
		 * Get site-level saved values.
		 *
		 * @return array<string, string>
		 */
		private function get_site_values() {
			$values = array();

			foreach ( MSBDPSEO_Helper::get_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_site_option_value( $field_id, '' );
			}

			foreach ( MSBDPSEO_Helper::get_image_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_site_image_option_value( $field_id );
			}

			foreach ( MSBDPSEO_Helper::get_schema_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_schema_value( $field_id, 'site', '' );
			}

			return $values;
		}

		/**
		 * Get network-level saved values.
		 *
		 * @return array<string, string>
		 */
		private function get_network_values() {
			$values = array();

			foreach ( MSBDPSEO_Helper::get_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_network_option_value( $field_id, '' );
			}

			foreach ( MSBDPSEO_Helper::get_image_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_network_image_option_value( $field_id );
			}

			foreach ( MSBDPSEO_Helper::get_schema_fields() as $field_id => $field ) {
				$values[ $field_id ] = MSBDPSEO_Helper::get_schema_value( $field_id, 'network', '' );
			}

			return $values;
		}

		/**
		 * Render settings table.
		 *
		 * @param array<string, string> $values Saved values.
		 * @param string               $scope  Either site or network.
		 * @return void
		 */
		private function render_settings_table( $values, $scope = 'site' ) {
			?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( MSBDPSEO_Helper::get_fields() as $field_id => $field ) : ?>
						<?php
						$field_id_attr = 'msbdpseo-' . str_replace( '_', '-', $field_id );
						$value         = isset( $values[ $field_id ] ) ? $values[ $field_id ] : '';
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $field_id_attr ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
								</label>
							</th>
							<td>
								<textarea
									id="<?php echo esc_attr( $field_id_attr ); ?>"
									name="msbdpseo[<?php echo esc_attr( $field_id ); ?>]"
									class="large-text code"
									rows="10"
									spellcheck="false"
								><?php echo esc_textarea( $value ); ?></textarea>

								<?php $this->render_site_override_control( $field_id, 'text', $scope ); ?>

								<?php if ( ! empty( $field['description'] ) ) : ?>
									<p class="description">
										<?php echo wp_kses_post( $field['description'] ); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>

					<?php foreach ( MSBDPSEO_Helper::get_image_fields() as $field_id => $field ) : ?>
						<?php
						$field_id_attr = 'msbdpseo-' . str_replace( '_', '-', $field_id );
						$value         = isset( $values[ $field_id ] ) ? absint( $values[ $field_id ] ) : 0;
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $field_id_attr ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
								</label>
							</th>
							<td>
								<?php $this->render_image_field( $field_id_attr, 'msbdpseo[' . $field_id . ']', $value ); ?>

								<?php $this->render_site_override_control( $field_id, 'image', $scope ); ?>

								<?php if ( ! empty( $field['description'] ) ) : ?>
									<p class="description">
										<?php echo esc_html( $field['description'] ); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}




		/**
		 * Render Schema.org JSON-LD settings table.
		 *
		 * @param array<string, string> $values Saved values.
		 * @param string               $scope  Either site or network.
		 * @return void
		 */
		private function render_schema_settings_table( $values, $scope = 'site' ) {
			?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( MSBDPSEO_Helper::get_schema_fields() as $field_id => $field ) : ?>
						<?php
						$field_id_attr = 'msbdpseo-' . str_replace( '_', '-', $field_id );
						$type          = isset( $field['type'] ) ? $field['type'] : 'text';
						$value         = isset( $values[ $field_id ] ) ? $values[ $field_id ] : '';
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $field_id_attr ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
								</label>
							</th>
							<td>
								<?php $this->render_schema_field( $field_id_attr, 'msbdpseo[' . $field_id . ']', $type, $value ); ?>

								<?php $this->render_site_override_control( $field_id, $type, $scope ); ?>

								<?php if ( ! empty( $field['description'] ) ) : ?>
									<p class="description">
										<?php echo esc_html( $field['description'] ); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}


		/**
		 * Register the per-post/page social image meta box.
		 *
		 * @return void
		 */
		public function register_social_image_meta_box() {
			$post_types = array( 'post', 'page' );

			/**
			 * Filter post types that should support the custom social image field.
			 *
			 * @param array<int, string> $post_types Post type names.
			 */
			$post_types = apply_filters( 'msbdpseo_social_image_post_types', $post_types );

			foreach ( $post_types as $post_type ) {
				if ( ! post_type_exists( $post_type ) ) {
					continue;
				}

				add_meta_box(
					'msbdpseo-social-image',
					__( 'MSBD Social Image', 'msbd-primary-seo' ),
					array( $this, 'render_social_image_meta_box' ),
					$post_type,
					'side',
					'low'
				);
			}
		}

		/**
		 * Render the per-post/page social image meta box.
		 *
		 * @param WP_Post $post Current post object.
		 * @return void
		 */
		public function render_social_image_meta_box( $post ) {
			$image_id = absint( get_post_meta( $post->ID, MSBDPSEO_Helper::SOCIAL_IMAGE_META_KEY, true ) );

			wp_nonce_field( 'msbdpseo_save_social_image', 'msbdpseo_social_image_nonce' );
			$this->render_image_field( 'msbdpseo-social-image-id', 'msbdpseo_social_image_id', $image_id );
			?>
			<p class="description">
				<?php echo esc_html__( 'Fallback social image for this post/page. The featured image is still used first when available.', 'msbd-primary-seo' ); ?>
			</p>
			<?php
		}

		/**
		 * Save the per-post/page social image meta box.
		 *
		 * @param int $post_id Current post ID.
		 * @return void
		 */
		public function save_social_image_meta_box( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['msbdpseo_social_image_nonce'] ) ) {
				return;
			}

			$nonce = sanitize_text_field( wp_unslash( $_POST['msbdpseo_social_image_nonce'] ) );

			if ( ! wp_verify_nonce( $nonce, 'msbdpseo_save_social_image' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$image_id = isset( $_POST['msbdpseo_social_image_id'] )
				? absint( wp_unslash( $_POST['msbdpseo_social_image_id'] ) )
				: 0;

			if ( $image_id ) {
				update_post_meta( $post_id, MSBDPSEO_Helper::SOCIAL_IMAGE_META_KEY, $image_id );
			} else {
				delete_post_meta( $post_id, MSBDPSEO_Helper::SOCIAL_IMAGE_META_KEY );
			}
		}

		/**
		 * Enqueue media uploader assets for plugin screens and post editor screens.
		 *
		 * @param string $hook_suffix Current admin hook suffix.
		 * @return void
		 */
		public function enqueue_admin_assets( $hook_suffix ) {
			$should_enqueue = in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true );

			if ( ! $should_enqueue ) {
				$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
				$should_enqueue = in_array( $page, array( self::SITE_PAGE_SLUG, self::SITE_SCHEMA_PAGE_SLUG, self::NETWORK_PAGE_SLUG, self::NETWORK_SCHEMA_PAGE_SLUG ), true );
			}

			if ( ! $should_enqueue ) {
				return;
			}

			wp_enqueue_media();
			wp_register_script( 'msbdpseo-admin', false, array( 'jquery' ), MSBDPSEO_VERSION, true );
			wp_enqueue_script( 'msbdpseo-admin' );

			$script = <<<'JS'
(function ($) {
	'use strict';

	$(document).on('click', '.msbdpseo-select-image', function (event) {
		event.preventDefault();

		var $button = $(this);
		var $field = $button.closest('.msbdpseo-image-field');
		var frame = wp.media({
			title: $button.data('title') || 'Select Social Image',
			button: {
				text: $button.data('button') || 'Use this image'
			},
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

			$field.find('.msbdpseo-image-id').val(attachment.id);
			$field.find('.msbdpseo-image-preview').html('<img src="' + imageUrl + '" alt="" style="max-width:100%;height:auto;display:block;margin-bottom:8px;" />');
			$field.find('.msbdpseo-remove-image').show();
		});

		frame.open();
	});

	$(document).on('click', '.msbdpseo-remove-image', function (event) {
		event.preventDefault();

		var $field = $(this).closest('.msbdpseo-image-field');
		$field.find('.msbdpseo-image-id').val('0');
		$field.find('.msbdpseo-image-preview').empty();
		$(this).hide();
	});
})(jQuery);
JS;

			wp_add_inline_script( 'msbdpseo-admin', $script );
		}

		/**
		 * Render the site override checkbox for a field when network defaults apply.
		 *
		 * @param string $field_id Field ID.
		 * @param string $type     Field type.
		 * @param string $scope    Either site or network.
		 * @return void
		 */
		private function render_site_override_control( $field_id, $type, $scope ) {
			if ( 'site' !== $scope || ! MSBDPSEO_Helper::should_use_network_defaults() ) {
				return;
			}

			$is_overridden = MSBDPSEO_Helper::is_site_override_enabled( $field_id );
			$status        = $this->get_network_value_status_label( $field_id, $type );
			?>
			<p class="description msbdpseo-override-control">
				<label>
					<input
						type="checkbox"
						name="msbdpseo_override[<?php echo esc_attr( $field_id ); ?>]"
						value="1"
						<?php checked( $is_overridden ); ?>
					/>
					<?php echo esc_html__( 'Override the network value for this site.', 'msbd-primary-seo' ); ?>
				</label>
				<br />
				<?php echo esc_html( $status ); ?>
			</p>
			<?php
		}

		/**
		 * Get a human-readable status for the network value inherited by a subsite.
		 *
		 * @param string $field_id Field ID.
		 * @param string $type     Field type.
		 * @return string
		 */
		private function get_network_value_status_label( $field_id, $type ) {
			if ( 'image' === $type ) {
				$has_value = 0 < MSBDPSEO_Helper::get_network_image_option_value( $field_id );
			} elseif ( 'checkbox' === $type ) {
				$has_value = (bool) absint( MSBDPSEO_Helper::get_network_option_value( $field_id, '0' ) );

				return $has_value
					? __( 'Network value: enabled.', 'msbd-primary-seo' )
					: __( 'Network value: disabled.', 'msbd-primary-seo' );
			} else {
				$has_value = '' !== trim( MSBDPSEO_Helper::get_network_option_value( $field_id, '' ) );
			}

			return $has_value
				? __( 'Network value: set.', 'msbd-primary-seo' )
				: __( 'Network value: not set.', 'msbd-primary-seo' );
		}

		/**
		 * Render a schema setting field.
		 *
		 * @param string $id    Field ID attribute.
		 * @param string $name  Field name attribute.
		 * @param string $type  Field type.
		 * @param mixed  $value Saved value.
		 * @return void
		 */
		private function render_schema_field( $id, $name, $type, $value ) {
			if ( 'checkbox' === $type ) {
				?>
				<label>
					<input
						type="checkbox"
						id="<?php echo esc_attr( $id ); ?>"
						name="<?php echo esc_attr( $name ); ?>"
						value="1"
						<?php checked( (bool) absint( $value ) ); ?>
					/>
					<?php echo esc_html__( 'Enable', 'msbd-primary-seo' ); ?>
				</label>
				<?php
				return;
			}

			if ( 'image' === $type ) {
				$this->render_image_field( $id, $name, absint( $value ) );
				return;
			}

			if ( in_array( $type, array( 'textarea', 'textarea_urls' ), true ) ) {
				?>
				<textarea
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					class="large-text code"
					rows="5"
					spellcheck="false"
				><?php echo esc_textarea( (string) $value ); ?></textarea>
				<?php
				return;
			}

			$input_type = 'url' === $type ? 'url' : 'text';
			?>
			<input
				type="<?php echo esc_attr( $input_type ); ?>"
				id="<?php echo esc_attr( $id ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				class="regular-text"
				value="<?php echo esc_attr( (string) $value ); ?>"
			/>
			<?php
		}

		/**
		 * Render reusable image selector field.
		 *
		 * @param string $id          Field ID attribute.
		 * @param string $name        Field name attribute.
		 * @param int    $image_id    Attachment ID.
		 * @return void
		 */
		private function render_image_field( $id, $name, $image_id ) {
			$image_id  = absint( $image_id );
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
			?>
			<div class="msbdpseo-image-field">
				<input
					type="hidden"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					class="msbdpseo-image-id"
					value="<?php echo esc_attr( (string) $image_id ); ?>"
				/>

				<div class="msbdpseo-image-preview" style="margin-bottom:8px;max-width:320px;">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:100%;height:auto;display:block;margin-bottom:8px;" />
					<?php endif; ?>
				</div>

				<button
					type="button"
					class="button msbdpseo-select-image"
					data-title="<?php echo esc_attr__( 'Select Social Image', 'msbd-primary-seo' ); ?>"
					data-button="<?php echo esc_attr__( 'Use this image', 'msbd-primary-seo' ); ?>"
				>
					<?php echo esc_html__( 'Select Image', 'msbd-primary-seo' ); ?>
				</button>

				<button
					type="button"
					class="button-link-delete msbdpseo-remove-image"
					style="margin-left:8px;<?php echo $image_id ? '' : 'display:none;'; ?>"
				>
					<?php echo esc_html__( 'Remove Image', 'msbd-primary-seo' ); ?>
				</button>
			</div>
			<?php
		}

		/**
		 * Render saved notice from query argument.
		 *
		 * @param string $query_arg Query argument.
		 * @param string $message   Notice message.
		 * @return void
		 */
		private function render_notice_from_query_arg( $query_arg, $message ) {
			$value = isset( $_GET[ $query_arg ] )
				? sanitize_text_field( wp_unslash( $_GET[ $query_arg ] ) )
				: '';

			if ( ! in_array( $value, array( '1', 'true' ), true ) ) {
				return;
			}

			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $message ); ?></p>
			</div>
			<?php
		}
	}

endif;

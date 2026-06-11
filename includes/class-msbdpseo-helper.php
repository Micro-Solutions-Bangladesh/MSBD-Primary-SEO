<?php
/**
 * Helper class.
 *
 * @package MSBD_Primary_SEO
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MSBDPSEO_Helper' ) ) :

	/**
	 * Helper methods for options, sanitization, and frontend output.
	 */
	final class MSBDPSEO_Helper {

		/**
		 * Post meta key for per-post/page social image attachment ID.
		 *
		 * @var string
		 */
		public const SOCIAL_IMAGE_META_KEY = '_msbdpseo_social_image_id';

		/**
		 * Allowed plugin option fields.
		 *
		 * @var array<string, string>
		 */
		private static $option_keys = array(
			'before_head_tag_end'      => '_msbdpseo_before_head_tag_end',
			'body_tag_start'           => '_msbdpseo_body_tag_start',
			'before_body_tag_end'      => '_msbdpseo_before_body_tag_end',
			'default_social_image'     => '_msbdpseo_default_social_image',
			'schema_enable_website'    => '_msbdpseo_schema_enable_website',
			'schema_enable_org'        => '_msbdpseo_schema_enable_org',
			'schema_org_name'          => '_msbdpseo_schema_org_name',
			'schema_org_logo'          => '_msbdpseo_schema_org_logo',
			'schema_org_url'           => '_msbdpseo_schema_org_url',
			'schema_org_same_as'       => '_msbdpseo_schema_org_same_as',
			'schema_enable_local'      => '_msbdpseo_schema_enable_local',
			'schema_local_name'        => '_msbdpseo_schema_local_name',
			'schema_local_url'         => '_msbdpseo_schema_local_url',
			'schema_local_logo'        => '_msbdpseo_schema_local_logo',
			'schema_local_telephone'   => '_msbdpseo_schema_local_telephone',
			'schema_local_price_range' => '_msbdpseo_schema_local_price_range',
			'schema_local_address'     => '_msbdpseo_schema_local_address',
			'schema_enable_breadcrumb' => '_msbdpseo_schema_enable_breadcrumb',
			'schema_enable_article'    => '_msbdpseo_schema_enable_article',
			'schema_enable_webpage'    => '_msbdpseo_schema_enable_webpage',
		);

		/**
		 * Register frontend output hooks.
		 *
		 * @return void
		 */
		public static function register_frontend_hooks() {
			add_action( 'wp_head', array( __CLASS__, 'output_before_head_tag_end' ), 99 );
			add_action( 'wp_body_open', array( __CLASS__, 'output_body_tag_start' ), 5 );
			add_action( 'wp_footer', array( __CLASS__, 'output_before_body_tag_end' ), 99 );
		}

		/**
		 * Get all supported code textarea settings fields.
		 *
		 * @return array<string, array<string, string>>
		 */
		public static function get_fields() {
			return array(
				'before_head_tag_end' => array(
					'label'       => __( 'SEO Code Before </head>', 'msbd-primary-seo' ),
					'description' => __( 'Outputs SEO-related scripts, meta tags, verification tags, structured data, or comments before the closing &lt;/head&gt; tag.', 'msbd-primary-seo' ),
					'comment'     => 'Before Head End',
				),
				'body_tag_start' => array(
					'label'       => __( 'SEO Code After <body>', 'msbd-primary-seo' ),
					'description' => __( 'Outputs SEO-related scripts, noscript tags, markup, tracking snippets, or comments immediately after the opening &lt;body&gt; tag.', 'msbd-primary-seo' ),
					'comment'     => 'After Body Start',
				),
				'before_body_tag_end' => array(
					'label'       => __( 'SEO Code Before </body>', 'msbd-primary-seo' ),
					'description' => __( 'Outputs SEO-related scripts, markup, tracking snippets, or comments before the closing &lt;/body&gt; tag.', 'msbd-primary-seo' ),
					'comment'     => 'Before Body End',
				),
			);
		}

		/**
		 * Get all supported image settings fields.
		 *
		 * @return array<string, array<string, string>>
		 */
		public static function get_image_fields() {
			return array(
				'default_social_image' => array(
					'label'       => __( 'Default Social Image', 'msbd-primary-seo' ),
					'description' => __( 'Used as the fallback Open Graph and Twitter image when a post/page does not have a usable social image. Recommended size: 1200 × 630 pixels.', 'msbd-primary-seo' ),
				),
			);
		}

		/**
		 * Get schema fields.
		 *
		 * @return array<string, array<string, string>>
		 */
		public static function get_schema_fields() {
			return array(
				'schema_enable_website' => array(
					'label'       => __( 'Enable WebSite Schema', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Adds a basic WebSite JSON-LD node on the front page.', 'msbd-primary-seo' ),
				),
				'schema_enable_org' => array(
					'label'       => __( 'Enable Organization Schema', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Adds Organization JSON-LD using the fields below.', 'msbd-primary-seo' ),
				),
				'schema_org_name' => array(
					'label'       => __( 'Organization Name', 'msbd-primary-seo' ),
					'type'        => 'text',
					'description' => __( 'Example: MCQ Academy.', 'msbd-primary-seo' ),
				),
				'schema_org_logo' => array(
					'label'       => __( 'Organization Logo', 'msbd-primary-seo' ),
					'type'        => 'image',
					'description' => __( 'Logo used in Organization and LocalBusiness schema.', 'msbd-primary-seo' ),
				),
				'schema_org_url' => array(
					'label'       => __( 'Organization Website URL', 'msbd-primary-seo' ),
					'type'        => 'url',
					'description' => __( 'Leave empty to use the site home URL.', 'msbd-primary-seo' ),
				),
				'schema_org_same_as' => array(
					'label'       => __( 'SameAs Social Links', 'msbd-primary-seo' ),
					'type'        => 'textarea_urls',
					'description' => __( 'Enter one public profile URL per line, such as Facebook, LinkedIn, YouTube, X, GitHub, or other official profiles.', 'msbd-primary-seo' ),
				),
				'schema_enable_local' => array(
					'label'       => __( 'Enable LocalBusiness Schema', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Optional. Use only for a real local business with a physical or service-area presence.', 'msbd-primary-seo' ),
				),
				'schema_local_name' => array(
					'label'       => __( 'LocalBusiness Name', 'msbd-primary-seo' ),
					'type'        => 'text',
					'description' => __( 'Leave empty to reuse the organization name.', 'msbd-primary-seo' ),
				),
				'schema_local_url' => array(
					'label'       => __( 'LocalBusiness URL', 'msbd-primary-seo' ),
					'type'        => 'url',
					'description' => __( 'Leave empty to reuse the organization URL or site home URL.', 'msbd-primary-seo' ),
				),
				'schema_local_logo' => array(
					'label'       => __( 'LocalBusiness Logo', 'msbd-primary-seo' ),
					'type'        => 'image',
					'description' => __( 'Leave empty to reuse the organization logo.', 'msbd-primary-seo' ),
				),
				'schema_local_telephone' => array(
					'label'       => __( 'LocalBusiness Telephone', 'msbd-primary-seo' ),
					'type'        => 'text',
					'description' => __( 'Optional public business phone number.', 'msbd-primary-seo' ),
				),
				'schema_local_price_range' => array(
					'label'       => __( 'LocalBusiness Price Range', 'msbd-primary-seo' ),
					'type'        => 'text',
					'description' => __( 'Optional. Example: $, $$, or $$$.', 'msbd-primary-seo' ),
				),
				'schema_local_address' => array(
					'label'       => __( 'LocalBusiness Address', 'msbd-primary-seo' ),
					'type'        => 'textarea',
					'description' => __( 'Optional plain-text address. Keep it consistent with your website contact page and Google Business Profile.', 'msbd-primary-seo' ),
				),
				'schema_enable_breadcrumb' => array(
					'label'       => __( 'Enable BreadcrumbList Schema', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Adds lightweight breadcrumb JSON-LD for singular posts, pages, and supported taxonomy archives.', 'msbd-primary-seo' ),
				),
				'schema_enable_article' => array(
					'label'       => __( 'Enable Article / BlogPosting Schema for Posts', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Adds BlogPosting JSON-LD for standard posts. Uses featured/social image when available.', 'msbd-primary-seo' ),
				),
				'schema_enable_webpage' => array(
					'label'       => __( 'Enable WebPage Schema for Pages', 'msbd-primary-seo' ),
					'type'        => 'checkbox',
					'description' => __( 'Adds WebPage JSON-LD for standard pages.', 'msbd-primary-seo' ),
				),
			);
		}

		/**
		 * Get stored option key for a field.
		 *
		 * @param string $field Field name.
		 * @return string
		 */
		public static function option_key( $field ) {
			$field = sanitize_key( (string) $field );

			if ( isset( self::$option_keys[ $field ] ) ) {
				return self::$option_keys[ $field ];
			}

			return '_msbdpseo_' . $field;
		}

		/**
		 * Get the option key used to store a site-level override flag.
		 *
		 * @param string $field Field name.
		 * @return string
		 */
		public static function override_option_key( $field ) {
			return '_msbdpseo_override_' . sanitize_key( (string) $field );
		}

		/**
		 * Check whether the plugin is network activated.
		 *
		 * Network defaults should be read only when the plugin is active network-wide.
		 * A normal per-site activation on multisite must behave like a site-only plugin.
		 *
		 * @return bool
		 */
		public static function is_network_activated() {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() || ! function_exists( 'get_site_option' ) ) {
				return false;
			}

			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			return is_array( $active_plugins ) && isset( $active_plugins[ MSBDPSEO_BASENAME ] );
		}

		/**
		 * Check whether subsites should inherit network defaults.
		 *
		 * @return bool
		 */
		public static function should_use_network_defaults() {
			return self::is_network_activated();
		}

		/**
		 * Check whether a site has an explicit override flag saved for a field.
		 *
		 * @param string $field Field name.
		 * @return bool
		 */
		public static function has_explicit_site_override( $field ) {
			return null !== get_option( self::override_option_key( $field ), null );
		}

		/**
		 * Infer an override for older installs that saved site values before override flags existed.
		 *
		 * @param string $field Field name.
		 * @return bool
		 */
		private static function infer_site_override( $field ) {
			$schema_fields = self::get_schema_fields();

			if ( isset( self::get_image_fields()[ $field ] ) ) {
				return 0 < self::get_site_image_option_value( $field );
			}

			if ( isset( $schema_fields[ $field ] ) ) {
				$type = isset( $schema_fields[ $field ]['type'] ) ? $schema_fields[ $field ]['type'] : 'text';

				if ( 'image' === $type ) {
					return 0 < self::get_site_image_option_value( $field );
				}

				if ( 'checkbox' === $type ) {
					return (bool) absint( self::get_site_option_value( $field, '0' ) );
				}
			}

			return '' !== trim( self::get_site_option_value( $field, '' ) );
		}

		/**
		 * Check whether a subsite overrides a network field.
		 *
		 * @param string $field Field name.
		 * @return bool
		 */
		public static function is_site_override_enabled( $field ) {
			if ( ! self::should_use_network_defaults() ) {
				return true;
			}

			$override = get_option( self::override_option_key( $field ), null );

			if ( null !== $override ) {
				return (bool) absint( $override );
			}

			return self::infer_site_override( $field );
		}

		/**
		 * Update a site-level override flag.
		 *
		 * @param string $field   Field name.
		 * @param bool   $enabled Whether the field should override the network value.
		 * @return bool
		 */
		public static function update_site_override( $field, $enabled ) {
			if ( ! self::should_use_network_defaults() ) {
				return true;
			}

			return update_option( self::override_option_key( $field ), $enabled ? '1' : '0', false );
		}

		/**
		 * Get a merged text/code option using site override first, otherwise network default.
		 *
		 * @param string $field   Field name.
		 * @param string $default Default value.
		 * @return string
		 */
		public static function get_merged_option_value( $field, $default = '' ) {
			if ( self::should_use_network_defaults() && ! self::is_site_override_enabled( $field ) ) {
				return self::get_network_option_value( $field, $default );
			}

			$site_value = self::get_site_option_value( $field, '' );

			return '' !== trim( $site_value ) ? $site_value : $default;
		}

		/**
		 * Get a merged image option using site override first, otherwise network default.
		 *
		 * @param string $field Field name.
		 * @return int
		 */
		public static function get_merged_image_option_value( $field ) {
			if ( self::should_use_network_defaults() && ! self::is_site_override_enabled( $field ) ) {
				return self::get_network_image_option_value( $field );
			}

			return self::get_site_image_option_value( $field );
		}

		/**
		 * Get site option.
		 *
		 * @param string $field   Field name.
		 * @param string $default Default value.
		 * @return string
		 */
		public static function get_site_option_value( $field, $default = '' ) {
			$value = get_option( self::option_key( $field ), $default );

			return is_string( $value ) ? $value : $default;
		}

		/**
		 * Update site option.
		 *
		 * @param string $field Field name.
		 * @param string $value Value.
		 * @return bool
		 */
		public static function update_site_option_value( $field, $value ) {
			return update_option( self::option_key( $field ), self::sanitize_seo_code( $value ), false );
		}

		/**
		 * Get site image option.
		 *
		 * @param string $field Field name.
		 * @return int
		 */
		public static function get_site_image_option_value( $field ) {
			return absint( get_option( self::option_key( $field ), 0 ) );
		}

		/**
		 * Update site image option.
		 *
		 * @param string $field         Field name.
		 * @param mixed  $attachment_id Attachment ID.
		 * @return bool
		 */
		public static function update_site_image_option_value( $field, $attachment_id ) {
			return update_option( self::option_key( $field ), absint( $attachment_id ), false );
		}

		/**
		 * Get network option.
		 *
		 * @param string $field   Field name.
		 * @param string $default Default value.
		 * @return string
		 */
		public static function get_network_option_value( $field, $default = '' ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() || ! function_exists( 'get_site_option' ) ) {
				return $default;
			}

			$value = get_site_option( self::option_key( $field ), $default );

			return is_string( $value ) ? $value : $default;
		}

		/**
		 * Update network option.
		 *
		 * @param string $field Field name.
		 * @param string $value Value.
		 * @return bool
		 */
		public static function update_network_option_value( $field, $value ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() || ! function_exists( 'update_site_option' ) ) {
				return false;
			}

			return update_site_option( self::option_key( $field ), self::sanitize_seo_code( $value ) );
		}

		/**
		 * Get network image option.
		 *
		 * @param string $field Field name.
		 * @return int
		 */
		public static function get_network_image_option_value( $field ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() || ! function_exists( 'get_site_option' ) ) {
				return 0;
			}

			return absint( get_site_option( self::option_key( $field ), 0 ) );
		}

		/**
		 * Update network image option.
		 *
		 * @param string $field         Field name.
		 * @param mixed  $attachment_id Attachment ID.
		 * @return bool
		 */
		public static function update_network_image_option_value( $field, $attachment_id ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() || ! function_exists( 'update_site_option' ) ) {
				return false;
			}

			return update_site_option( self::option_key( $field ), absint( $attachment_id ) );
		}

		/**
		 * Get schema setting value by scope.
		 *
		 * The merged scope uses the site value only when the subsite has enabled an
		 * explicit override. Otherwise, it inherits the network value when the plugin
		 * is network activated.
		 *
		 * @param string $field   Field name.
		 * @param string $scope   Scope: site, network, or merged.
		 * @param mixed  $default Default value.
		 * @return mixed
		 */
		public static function get_schema_value( $field, $scope = 'merged', $default = '' ) {
			$fields = self::get_schema_fields();

			if ( ! isset( $fields[ $field ] ) ) {
				return $default;
			}

			$type = $fields[ $field ]['type'];

			if ( 'image' === $type ) {
				if ( 'network' === $scope ) {
					return self::get_network_image_option_value( $field );
				}

				if ( 'merged' === $scope ) {
					return self::get_merged_image_option_value( $field );
				}

				return self::get_site_image_option_value( $field );
			}

			if ( 'checkbox' === $type ) {
				if ( 'network' === $scope ) {
					return (bool) absint( self::get_network_option_value( $field, '0' ) );
				}

				if ( 'merged' === $scope && self::should_use_network_defaults() && ! self::is_site_override_enabled( $field ) ) {
					return (bool) absint( self::get_network_option_value( $field, '0' ) );
				}

				return (bool) absint( self::get_site_option_value( $field, '0' ) );
			}

			if ( 'network' === $scope ) {
				return self::get_network_option_value( $field, (string) $default );
			}

			if ( 'merged' === $scope && self::should_use_network_defaults() && ! self::is_site_override_enabled( $field ) ) {
				$network_value = self::get_network_option_value( $field, '' );

				return '' !== trim( $network_value ) ? $network_value : $default;
			}

			$site_value = self::get_site_option_value( $field, '' );

			return '' !== trim( $site_value ) ? $site_value : $default;
		}

		/**
		 * Update schema setting value by scope.
		 *
		 * @param string $field Field name.
		 * @param mixed  $value Raw value.
		 * @param string $scope Scope.
		 * @return bool
		 */
		public static function update_schema_value( $field, $value, $scope = 'site' ) {
			$fields = self::get_schema_fields();

			if ( ! isset( $fields[ $field ] ) ) {
				return false;
			}

			$type = $fields[ $field ]['type'];

			if ( 'image' === $type ) {
				return 'network' === $scope ? self::update_network_image_option_value( $field, $value ) : self::update_site_image_option_value( $field, $value );
			}

			$value = self::sanitize_schema_value( $value, $type );

			return 'network' === $scope ? self::update_network_option_value( $field, $value ) : self::update_site_option_value( $field, $value );
		}

		/**
		 * Sanitize schema option values.
		 *
		 * @param mixed  $value Raw value.
		 * @param string $type  Field type.
		 * @return string
		 */
		public static function sanitize_schema_value( $value, $type ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				return '';
			}

			$value = wp_check_invalid_utf8( str_replace( "\0", '', (string) $value ) );

			if ( 'checkbox' === $type ) {
				return $value ? '1' : '0';
			}

			if ( 'url' === $type ) {
				return esc_url_raw( $value );
			}

			if ( 'textarea_urls' === $type ) {
				$urls = preg_split( '/\r\n|\r|\n/', $value );
				$urls = is_array( $urls ) ? $urls : array();
				$urls = array_filter( array_map( 'esc_url_raw', array_map( 'trim', $urls ) ) );

				return implode( "\n", array_unique( $urls ) );
			}

			if ( 'textarea' === $type ) {
				return sanitize_textarea_field( $value );
			}

			return sanitize_text_field( $value );
		}

		/**
		 * Determine whether current user may save raw HTML/scripts.
		 *
		 * @return bool
		 */
		public static function current_user_can_save_unfiltered_code() {
			if ( current_user_can( 'manage_network_options' ) ) {
				return true;
			}

			return current_user_can( 'unfiltered_html' );
		}

		/**
		 * Sanitize SEO code textarea values.
		 *
		 * @param mixed $value Raw value.
		 * @return string
		 */
		public static function sanitize_seo_code( $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				return '';
			}

			$value = (string) $value;
			$value = str_replace( "\0", '', $value );
			$value = wp_check_invalid_utf8( $value );

			if ( self::current_user_can_save_unfiltered_code() ) {
				return $value;
			}

			return wp_kses_post( $value );
		}

		/**
		 * Determine whether frontend output should run.
		 *
		 * @return bool
		 */
		public static function should_output_frontend() {
			if ( is_admin() ) {
				return false;
			}

			if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
				return false;
			}

			if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
				return false;
			}

			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return false;
			}

			if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
				return false;
			}

			return true;
		}

		/**
		 * Output code before closing head tag.
		 *
		 * @return void
		 */
		public static function output_before_head_tag_end() {
			self::output_social_image_meta();
			self::output_schema_json_ld();
			self::output_location( 'before_head_tag_end' );
		}

		/**
		 * Output code after opening body tag.
		 *
		 * @return void
		 */
		public static function output_body_tag_start() {
			self::output_location( 'body_tag_start' );
		}

		/**
		 * Output code before closing body tag.
		 *
		 * @return void
		 */
		public static function output_before_body_tag_end() {
			self::output_location( 'before_body_tag_end' );
		}

		/**
		 * Output saved SEO code for a location.
		 *
		 * On network activation, a subsite inherits the network value unless it has
		 * enabled a site-level override for the field. The network and site values are
		 * intentionally not printed together because the site value is an override.
		 *
		 * @param string $field Field name.
		 * @return void
		 */
		public static function output_location( $field ) {
			if ( ! self::should_output_frontend() ) {
				return;
			}

			$fields = self::get_fields();

			if ( ! isset( $fields[ $field ] ) ) {
				return;
			}

			$comment = $fields[ $field ]['comment'];
			/* translators: %s: SEO output location label. */
			$label   = sprintf( __( '%s - Site', 'msbd-primary-seo' ), $comment );
			$value   = self::get_site_option_value( $field, '' );

			if ( self::should_use_network_defaults() && ! self::is_site_override_enabled( $field ) ) {
				/* translators: %s: SEO output location label. */
				$label = sprintf( __( '%s - Network Inherited', 'msbd-primary-seo' ), $comment );
				$value = self::get_network_option_value( $field, '' );
			}

			if ( '' !== trim( $value ) ) {
				self::print_code_block( $label, $value );
			}
		}

		/**
		 * Output Open Graph and Twitter image tags using the configured fallback chain.
		 *
		 * @return void
		 */
		public static function output_social_image_meta() {
			if ( ! self::should_output_frontend() ) {
				return;
			}

			$image_id = self::get_social_image_id();

			if ( ! $image_id ) {
				return;
			}

			$image = wp_get_attachment_image_src( $image_id, 'full' );

			if ( empty( $image[0] ) ) {
				return;
			}

			$image_url = $image[0];
			$width     = ! empty( $image[1] ) ? absint( $image[1] ) : 0;
			$height    = ! empty( $image[2] ) ? absint( $image[2] ) : 0;

			echo "\n<!-- MSBD Primary SEO: Social Image -->\n";
			echo '<meta property="og:image" content="' . esc_url( $image_url ) . '" />' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $image_url ) . '" />' . "\n";

			if ( $width && $height ) {
				echo '<meta property="og:image:width" content="' . esc_attr( (string) $width ) . '" />' . "\n";
				echo '<meta property="og:image:height" content="' . esc_attr( (string) $height ) . '" />' . "\n";
			}

			echo "<!-- /MSBD Primary SEO: Social Image -->\n";
		}

		/**
		 * Output schema JSON-LD.
		 *
		 * @return void
		 */
		public static function output_schema_json_ld() {
			if ( ! self::should_output_frontend() ) {
				return;
			}

			$graph = self::get_schema_graph();

			if ( empty( $graph ) ) {
				return;
			}

			$data = array(
				'@context' => 'https://schema.org',
				'@graph'   => array_values( $graph ),
			);

			echo "\n<!-- MSBD Primary SEO: Schema.org JSON-LD -->\n";
			echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
			echo "<!-- /MSBD Primary SEO: Schema.org JSON-LD -->\n";
		}

		/**
		 * Build schema graph.
		 *
		 * @return array<int, array<string, mixed>>
		 */
		public static function get_schema_graph() {
			$graph = array();

			if ( self::get_schema_value( 'schema_enable_website', 'merged', false ) && ( is_front_page() || is_home() ) ) {
				$graph[] = self::build_website_schema();
			}

			if ( self::get_schema_value( 'schema_enable_org', 'merged', false ) ) {
				$organization = self::build_organization_schema();

				if ( ! empty( $organization ) ) {
					$graph[] = $organization;
				}
			}

			if ( self::get_schema_value( 'schema_enable_local', 'merged', false ) ) {
				$local_business = self::build_local_business_schema();

				if ( ! empty( $local_business ) ) {
					$graph[] = $local_business;
				}
			}

			if ( self::get_schema_value( 'schema_enable_breadcrumb', 'merged', false ) ) {
				$breadcrumb = self::build_breadcrumb_schema();

				if ( ! empty( $breadcrumb ) ) {
					$graph[] = $breadcrumb;
				}
			}

			if ( self::get_schema_value( 'schema_enable_article', 'merged', false ) && is_singular( 'post' ) ) {
				$article = self::build_article_schema();

				if ( ! empty( $article ) ) {
					$graph[] = $article;
				}
			}

			if ( self::get_schema_value( 'schema_enable_webpage', 'merged', false ) && is_singular( 'page' ) ) {
				$webpage = self::build_webpage_schema();

				if ( ! empty( $webpage ) ) {
					$graph[] = $webpage;
				}
			}

			/**
			 * Filter generated Schema.org graph nodes before output.
			 *
			 * @param array<int, array<string, mixed>> $graph Schema graph nodes.
			 */
			return apply_filters( 'msbdpseo_schema_graph', array_filter( $graph ) );
		}

		/**
		 * Build WebSite schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_website_schema() {
			return array_filter(
				array(
					'@type' => 'WebSite',
					'@id'   => trailingslashit( home_url( '/' ) ) . '#website',
					'url'   => home_url( '/' ),
					'name'  => get_bloginfo( 'name' ),
				),
				array( __CLASS__, 'not_empty' )
			);
		}

		/**
		 * Build Organization schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_organization_schema() {
			$name    = self::get_schema_value( 'schema_org_name', 'merged', get_bloginfo( 'name' ) );
			$url     = self::get_schema_value( 'schema_org_url', 'merged', home_url( '/' ) );
			$logo_id = absint( self::get_schema_value( 'schema_org_logo', 'merged', 0 ) );
			$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
			$same_as = self::get_schema_same_as_urls();

			$schema = array_filter(
				array(
					'@type'  => 'Organization',
					'@id'    => trailingslashit( home_url( '/' ) ) . '#organization',
					'name'   => $name,
					'url'    => $url,
					'logo'   => $logo,
					'sameAs' => $same_as,
				),
				array( __CLASS__, 'not_empty' )
			);

			return ! empty( $schema['name'] ) ? $schema : array();
		}

		/**
		 * Build LocalBusiness schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_local_business_schema() {
			$org_name = self::get_schema_value( 'schema_org_name', 'merged', get_bloginfo( 'name' ) );
			$org_url  = self::get_schema_value( 'schema_org_url', 'merged', home_url( '/' ) );
			$name     = self::get_schema_value( 'schema_local_name', 'merged', $org_name );
			$url      = self::get_schema_value( 'schema_local_url', 'merged', $org_url );
			$logo_id  = absint( self::get_schema_value( 'schema_local_logo', 'merged', 0 ) );

			if ( ! $logo_id ) {
				$logo_id = absint( self::get_schema_value( 'schema_org_logo', 'merged', 0 ) );
			}

			$logo = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

			$schema = array_filter(
				array(
					'@type'      => 'LocalBusiness',
					'@id'        => trailingslashit( home_url( '/' ) ) . '#localbusiness',
					'name'       => $name,
					'url'        => $url,
					'logo'       => $logo,
					'image'      => $logo,
					'telephone'  => self::get_schema_value( 'schema_local_telephone', 'merged', '' ),
					'priceRange' => self::get_schema_value( 'schema_local_price_range', 'merged', '' ),
					'address'    => self::build_local_address_schema(),
				),
				array( __CLASS__, 'not_empty' )
			);

			return ! empty( $schema['name'] ) ? $schema : array();
		}

		/**
		 * Build PostalAddress from plain text.
		 *
		 * @return array<string, string>
		 */
		private static function build_local_address_schema() {
			$address = self::get_schema_value( 'schema_local_address', 'merged', '' );

			if ( '' === trim( $address ) ) {
				return array();
			}

			return array(
				'@type'         => 'PostalAddress',
				'streetAddress' => $address,
			);
		}

		/**
		 * Build BreadcrumbList schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_breadcrumb_schema() {
			$items = array(
				array(
					'name' => __( 'Home', 'msbd-primary-seo' ),
					'url'  => home_url( '/' ),
				),
			);

			if ( is_singular() ) {
				$post_id   = get_queried_object_id();
				$post_type = get_post_type( $post_id );

				if ( 'post' === $post_type ) {
					$categories = get_the_category( $post_id );

					if ( ! empty( $categories[0] ) ) {
						$items[] = array(
							'name' => $categories[0]->name,
							'url'  => get_category_link( $categories[0] ),
						);
					}
				}

				if ( 'page' === $post_type ) {
					$ancestors = array_reverse( get_post_ancestors( $post_id ) );

					foreach ( $ancestors as $ancestor_id ) {
						$items[] = array(
							'name' => get_the_title( $ancestor_id ),
							'url'  => get_permalink( $ancestor_id ),
						);
					}
				}

				$items[] = array(
					'name' => get_the_title( $post_id ),
					'url'  => get_permalink( $post_id ),
				);
			} elseif ( is_category() || is_tag() || is_tax() ) {
				$term = get_queried_object();

				if ( $term instanceof WP_Term ) {
					$items[] = array(
						'name' => $term->name,
						'url'  => get_term_link( $term ),
					);
				}
			} else {
				return array();
			}

			$item_list = array();
			$position  = 1;

			foreach ( $items as $item ) {
				if ( empty( $item['name'] ) || empty( $item['url'] ) || is_wp_error( $item['url'] ) ) {
					continue;
				}

				$item_list[] = array(
					'@type'    => 'ListItem',
					'position' => $position,
					'name'     => wp_strip_all_tags( $item['name'] ),
					'item'     => esc_url_raw( $item['url'] ),
				);

				++$position;
			}

			if ( count( $item_list ) < 2 ) {
				return array();
			}

			return array(
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $item_list,
			);
		}

		/**
		 * Build BlogPosting schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_article_schema() {
			$post_id = get_queried_object_id();

			if ( ! $post_id ) {
				return array();
			}

			$schema = array_filter(
				array(
					'@type'            => 'BlogPosting',
					'@id'              => get_permalink( $post_id ) . '#blogposting',
					'mainEntityOfPage' => get_permalink( $post_id ),
					'headline'         => get_the_title( $post_id ),
					'description'      => self::get_post_excerpt_for_schema( $post_id ),
					'image'            => self::get_schema_image_url_for_post( $post_id ),
					'datePublished'    => get_the_date( DATE_W3C, $post_id ),
					'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
					'author'           => self::get_author_schema( $post_id ),
					'publisher'        => self::get_publisher_schema(),
				),
				array( __CLASS__, 'not_empty' )
			);

			return ! empty( $schema['headline'] ) ? $schema : array();
		}

		/**
		 * Build WebPage schema.
		 *
		 * @return array<string, mixed>
		 */
		private static function build_webpage_schema() {
			$post_id = get_queried_object_id();

			if ( ! $post_id ) {
				return array();
			}

			$schema = array_filter(
				array(
					'@type'            => 'WebPage',
					'@id'              => get_permalink( $post_id ) . '#webpage',
					'url'              => get_permalink( $post_id ),
					'name'             => get_the_title( $post_id ),
					'description'      => self::get_post_excerpt_for_schema( $post_id ),
					'datePublished'    => get_the_date( DATE_W3C, $post_id ),
					'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
					'mainEntityOfPage' => get_permalink( $post_id ),
				),
				array( __CLASS__, 'not_empty' )
			);

			return ! empty( $schema['name'] ) ? $schema : array();
		}

		/**
		 * Get sameAs URLs from option.
		 *
		 * @return array<int, string>
		 */
		private static function get_schema_same_as_urls() {
			$value = self::get_schema_value( 'schema_org_same_as', 'merged', '' );
			$urls  = preg_split( '/\r\n|\r|\n/', (string) $value );
			$urls  = is_array( $urls ) ? $urls : array();
			$urls  = array_filter( array_map( 'esc_url_raw', array_map( 'trim', $urls ) ) );

			return array_values( array_unique( $urls ) );
		}

		/**
		 * Get publisher schema reference or inline organization.
		 *
		 * @return array<string, string>
		 */
		private static function get_publisher_schema() {
			$organization = self::build_organization_schema();

			if ( ! empty( $organization['@id'] ) ) {
				return array(
					'@id' => $organization['@id'],
				);
			}

			return array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
			);
		}

		/**
		 * Get author schema.
		 *
		 * @param int $post_id Post ID.
		 * @return array<string, string>
		 */
		private static function get_author_schema( $post_id ) {
			$author_id = (int) get_post_field( 'post_author', $post_id );
			$name      = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
			$url       = $author_id ? get_author_posts_url( $author_id ) : '';

			return array_filter(
				array(
					'@type' => 'Person',
					'name'  => $name,
					'url'   => $url,
				),
				array( __CLASS__, 'not_empty' )
			);
		}

		/**
		 * Get schema image URL for a post.
		 *
		 * @param int $post_id Post ID.
		 * @return string
		 */
		private static function get_schema_image_url_for_post( $post_id ) {
			$image_id = absint( get_post_thumbnail_id( $post_id ) );

			if ( ! $image_id ) {
				$image_id = absint( get_post_meta( $post_id, self::SOCIAL_IMAGE_META_KEY, true ) );
			}

			if ( ! $image_id ) {
				$image_id = self::get_social_image_id();
			}

			return $image_id ? (string) wp_get_attachment_image_url( $image_id, 'full' ) : '';
		}

		/**
		 * Get plain excerpt for schema.
		 *
		 * @param int $post_id Post ID.
		 * @return string
		 */
		private static function get_post_excerpt_for_schema( $post_id ) {
			$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 30 );

			return sanitize_text_field( $excerpt );
		}

		/**
		 * Check whether a value is not empty for array_filter.
		 *
		 * @param mixed $value Value.
		 * @return bool
		 */
		public static function not_empty( $value ) {
			return ! ( null === $value || false === $value || '' === $value || array() === $value );
		}

		/**
		 * Get the social image attachment ID using the plugin fallback order.
		 *
		 * Fallback order:
		 * 1. Post featured image.
		 * 2. Custom post/page social image.
		 * 3. Site default social image.
		 * 4. Network default social image.
		 *
		 * @return int
		 */
		public static function get_social_image_id() {
			$image_id = 0;

			if ( is_singular() ) {
				$post_id = get_queried_object_id();

				if ( $post_id ) {
					$image_id = absint( get_post_thumbnail_id( $post_id ) );

					if ( ! $image_id ) {
						$image_id = absint( get_post_meta( $post_id, self::SOCIAL_IMAGE_META_KEY, true ) );
					}
				}
			}

			if ( ! $image_id ) {
				$image_id = self::get_merged_image_option_value( 'default_social_image' );
			}

			/**
			 * Filter the selected social image attachment ID.
			 *
			 * @param int $image_id Attachment ID.
			 */
			return absint( apply_filters( 'msbdpseo_social_image_id', $image_id ) );
		}

		/**
		 * Print a frontend code block with clear comments.
		 *
		 * @param string $label Block label.
		 * @param string $code  Raw saved SEO code.
		 * @return void
		 */
		private static function print_code_block( $label, $code ) {
			$label = sanitize_text_field( $label );

			echo "\n<!-- MSBD Primary SEO: " . esc_html( $label ) . " -->\n";
			echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "\n<!-- /MSBD Primary SEO -->\n";
		}
	}

endif;

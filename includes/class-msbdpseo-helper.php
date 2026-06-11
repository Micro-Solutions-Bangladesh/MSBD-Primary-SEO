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
			'before_head_tag_end'  => '_msbdpseo_before_head_tag_end',
			'body_tag_start'       => '_msbdpseo_body_tag_start',
			'before_body_tag_end'  => '_msbdpseo_before_body_tag_end',
			'default_social_image' => '_msbdpseo_default_social_image',
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
				'body_tag_start'      => array(
					'label'       => __( 'SEO Code After <body>', 'msbd-primary-seo' ),
					'description' => __( 'Outputs SEO-related scripts, noscript markup, tracking snippets, or comments right after the opening &lt;body&gt; tag. The active theme must support wp_body_open().', 'msbd-primary-seo' ),
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
		 * Get the stored option key for a field.
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
		 * Get a normal site-level option.
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
		 * Update a normal site-level option.
		 *
		 * @param string $field Field name.
		 * @param string $value Option value.
		 * @return bool
		 */
		public static function update_site_option_value( $field, $value ) {
			return update_option(
				self::option_key( $field ),
				self::sanitize_seo_code( $value ),
				false
			);
		}

		/**
		 * Get a site-level image attachment ID option.
		 *
		 * @param string $field Field name.
		 * @return int
		 */
		public static function get_site_image_option_value( $field ) {
			return absint( get_option( self::option_key( $field ), 0 ) );
		}

		/**
		 * Update a site-level image attachment ID option.
		 *
		 * @param string $field         Field name.
		 * @param mixed  $attachment_id Attachment ID.
		 * @return bool
		 */
		public static function update_site_image_option_value( $field, $attachment_id ) {
			return update_option(
				self::option_key( $field ),
				absint( $attachment_id ),
				false
			);
		}

		/**
		 * Get a network-level option.
		 *
		 * @param string $field   Field name.
		 * @param string $default Default value.
		 * @return string
		 */
		public static function get_network_option_value( $field, $default = '' ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				return $default;
			}

			if ( ! function_exists( 'get_site_option' ) ) {
				return $default;
			}

			$value = get_site_option( self::option_key( $field ), $default );

			return is_string( $value ) ? $value : $default;
		}

		/**
		 * Update a network-level option.
		 *
		 * @param string $field Field name.
		 * @param string $value Option value.
		 * @return bool
		 */
		public static function update_network_option_value( $field, $value ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				return false;
			}

			if ( ! function_exists( 'update_site_option' ) ) {
				return false;
			}

			return update_site_option(
				self::option_key( $field ),
				self::sanitize_seo_code( $value )
			);
		}

		/**
		 * Get a network-level image attachment ID option.
		 *
		 * @param string $field Field name.
		 * @return int
		 */
		public static function get_network_image_option_value( $field ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				return 0;
			}

			if ( ! function_exists( 'get_site_option' ) ) {
				return 0;
			}

			return absint( get_site_option( self::option_key( $field ), 0 ) );
		}

		/**
		 * Update a network-level image attachment ID option.
		 *
		 * @param string $field         Field name.
		 * @param mixed  $attachment_id Attachment ID.
		 * @return bool
		 */
		public static function update_network_image_option_value( $field, $attachment_id ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				return false;
			}

			if ( ! function_exists( 'update_site_option' ) ) {
				return false;
			}

			return update_site_option(
				self::option_key( $field ),
				absint( $attachment_id )
			);
		}

		/**
		 * Determine whether the current user may save raw HTML/scripts.
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
		 * @param mixed $value Raw submitted value.
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

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				$network_value = self::get_network_option_value( $field, '' );

				if ( '' !== trim( $network_value ) ) {
					self::print_code_block(
						sprintf(
							/* translators: %s: Output location label. */
							__( '%s - Network', 'msbd-primary-seo' ),
							$comment
						),
						$network_value
					);
				}
			}

			$site_value = self::get_site_option_value( $field, '' );

			if ( '' !== trim( $site_value ) ) {
				self::print_code_block(
					sprintf(
						/* translators: %s: Output location label. */
						__( '%s - Site', 'msbd-primary-seo' ),
						$comment
					),
					$site_value
				);
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

			$image_url = esc_url( $image[0] );
			$width     = ! empty( $image[1] ) ? absint( $image[1] ) : 0;
			$height    = ! empty( $image[2] ) ? absint( $image[2] ) : 0;

			echo "\n<!-- MSBD Primary SEO: Social Image -->\n";
			echo '<meta property="og:image" content="' . $image_url . '" />' . "\n";
			echo '<meta name="twitter:image" content="' . $image_url . '" />' . "\n";

			if ( $width && $height ) {
				echo '<meta property="og:image:width" content="' . esc_attr( (string) $width ) . '" />' . "\n";
				echo '<meta property="og:image:height" content="' . esc_attr( (string) $height ) . '" />' . "\n";
			}

			echo "<!-- /MSBD Primary SEO: Social Image -->\n";
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
				$image_id = self::get_site_image_option_value( 'default_social_image' );
			}

			if ( ! $image_id ) {
				$image_id = self::get_network_image_option_value( 'default_social_image' );
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

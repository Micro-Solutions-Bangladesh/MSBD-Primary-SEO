<?php
/**
 * Public functions.
 *
 * @package MSBD_Primary_SEO
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'msbdpseo_get_option_key' ) ) {
	/**
	 * Get plugin option key for a field.
	 *
	 * @param string $field Field name.
	 * @return string
	 */
	function msbdpseo_get_option_key( $field ) {
		return MSBDPSEO_Helper::option_key( $field );
	}
}

if ( ! function_exists( 'msbdpseo_get_site_option' ) ) {
	/**
	 * Get site-level SEO option.
	 *
	 * @param string $field   Field name.
	 * @param string $default Default value.
	 * @return string
	 */
	function msbdpseo_get_site_option( $field, $default = '' ) {
		return MSBDPSEO_Helper::get_site_option_value( $field, $default );
	}
}

if ( ! function_exists( 'msbdpseo_update_site_option' ) ) {
	/**
	 * Update site-level SEO option.
	 *
	 * @param string $field Field name.
	 * @param string $value Option value.
	 * @return bool
	 */
	function msbdpseo_update_site_option( $field, $value ) {
		return MSBDPSEO_Helper::update_site_option_value( $field, $value );
	}
}

if ( ! function_exists( 'msbdpseo_get_network_option' ) ) {
	/**
	 * Get network-level SEO option.
	 *
	 * @param string $field   Field name.
	 * @param string $default Default value.
	 * @return string
	 */
	function msbdpseo_get_network_option( $field, $default = '' ) {
		return MSBDPSEO_Helper::get_network_option_value( $field, $default );
	}
}

if ( ! function_exists( 'msbdpseo_is_network_activated' ) ) {
	/**
	 * Check whether this plugin is network activated.
	 *
	 * @return bool
	 */
	function msbdpseo_is_network_activated() {
		return MSBDPSEO_Helper::is_network_activated();
	}
}

if ( ! function_exists( 'msbdpseo_is_site_override_enabled' ) ) {
	/**
	 * Check whether a subsite overrides a network-level field.
	 *
	 * @param string $field Field name.
	 * @return bool
	 */
	function msbdpseo_is_site_override_enabled( $field ) {
		return MSBDPSEO_Helper::is_site_override_enabled( $field );
	}
}

if ( ! function_exists( 'msbdpseo_get_merged_option' ) ) {
	/**
	 * Get the effective SEO option for the current site.
	 *
	 * On network activation, this returns the site override value when enabled;
	 * otherwise it returns the inherited network value.
	 *
	 * @param string $field   Field name.
	 * @param string $default Default value.
	 * @return string
	 */
	function msbdpseo_get_merged_option( $field, $default = '' ) {
		return MSBDPSEO_Helper::get_merged_option_value( $field, $default );
	}
}

if ( ! function_exists( 'msbdpseo_update_network_option' ) ) {
	/**
	 * Update network-level SEO option.
	 *
	 * @param string $field Field name.
	 * @param string $value Option value.
	 * @return bool
	 */
	function msbdpseo_update_network_option( $field, $value ) {
		return MSBDPSEO_Helper::update_network_option_value( $field, $value );
	}
}

if ( ! function_exists( 'msbdpseo_should_output_frontend' ) ) {
	/**
	 * Check whether SEO output should run in the current request.
	 *
	 * @return bool
	 */
	function msbdpseo_should_output_frontend() {
		return MSBDPSEO_Helper::should_output_frontend();
	}
}

if ( ! function_exists( 'msbdpseo_output_location' ) ) {
	/**
	 * Output a specific SEO location manually.
	 *
	 * @param string $field Field name.
	 * @return void
	 */
	function msbdpseo_output_location( $field ) {
		MSBDPSEO_Helper::output_location( $field );
	}
}

if ( ! function_exists( 'msbdpseo_get_site_image_option' ) ) {
	/**
	 * Get site-level image attachment ID option.
	 *
	 * @param string $field Field name.
	 * @return int
	 */
	function msbdpseo_get_site_image_option( $field ) {
		return MSBDPSEO_Helper::get_site_image_option_value( $field );
	}
}

if ( ! function_exists( 'msbdpseo_update_site_image_option' ) ) {
	/**
	 * Update site-level image attachment ID option.
	 *
	 * @param string $field         Field name.
	 * @param int    $attachment_id Attachment ID.
	 * @return bool
	 */
	function msbdpseo_update_site_image_option( $field, $attachment_id ) {
		return MSBDPSEO_Helper::update_site_image_option_value( $field, $attachment_id );
	}
}

if ( ! function_exists( 'msbdpseo_get_network_image_option' ) ) {
	/**
	 * Get network-level image attachment ID option.
	 *
	 * @param string $field Field name.
	 * @return int
	 */
	function msbdpseo_get_network_image_option( $field ) {
		return MSBDPSEO_Helper::get_network_image_option_value( $field );
	}
}

if ( ! function_exists( 'msbdpseo_update_network_image_option' ) ) {
	/**
	 * Update network-level image attachment ID option.
	 *
	 * @param string $field         Field name.
	 * @param int    $attachment_id Attachment ID.
	 * @return bool
	 */
	function msbdpseo_update_network_image_option( $field, $attachment_id ) {
		return MSBDPSEO_Helper::update_network_image_option_value( $field, $attachment_id );
	}
}

if ( ! function_exists( 'msbdpseo_get_social_image_id' ) ) {
	/**
	 * Get selected social image attachment ID using the plugin fallback order.
	 *
	 * @return int
	 */
	function msbdpseo_get_social_image_id() {
		return MSBDPSEO_Helper::get_social_image_id();
	}
}

if ( ! function_exists( 'msbdpseo_get_schema_graph' ) ) {
	/**
	 * Get generated Schema.org graph nodes for the current request.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function msbdpseo_get_schema_graph() {
		return MSBDPSEO_Helper::get_schema_graph();
	}
}

if ( ! function_exists( 'msbdpseo_output_schema_json_ld' ) ) {
	/**
	 * Output generated Schema.org JSON-LD for the current request.
	 *
	 * @return void
	 */
	function msbdpseo_output_schema_json_ld() {
		MSBDPSEO_Helper::output_schema_json_ld();
	}
}

<?php
/**
 * Plugin Name: MSBD Primary SEO
 * Plugin URI:  https://github.com/Micro-Solutions-Bangladesh/MSBD-Primary-SEO
 * Description: Lightweight SEO utility plugin for injecting SEO scripts, meta tags, and markup into frontend head, body start, and footer locations. Supports single-site and multisite.
 * Version:     1.0.0
 * Requires at least: 5.3
 * Requires PHP: 7.4
 * Author: Micro Solutions BD
 * Author URI: https://microsolutionsbd.com/
 * Text Domain: msbd-primary-seo
 * Domain Path: /languages
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package MSBD_Primary_SEO
 */

defined( 'ABSPATH' ) || exit;

define( 'MSBDPSEO_VERSION', '1.0.0' );
define( 'MSBDPSEO_FILE', __FILE__ );
define( 'MSBDPSEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'MSBDPSEO_URL', plugin_dir_url( __FILE__ ) );
define( 'MSBDPSEO_BASENAME', plugin_basename( __FILE__ ) );

require_once MSBDPSEO_PATH . 'includes/class-msbdpseo-helper.php';
require_once MSBDPSEO_PATH . 'includes/functions.php';
require_once MSBDPSEO_PATH . 'includes/class-msbdpseo-admin.php';

/**
 * Initialize the plugin.
 *
 * @return void
 */
function msbdpseo_init_plugin() {
	load_plugin_textdomain(
		'msbd-primary-seo',
		false,
		dirname( MSBDPSEO_BASENAME ) . '/languages'
	);

	MSBDPSEO_Helper::register_frontend_hooks();

	if ( is_admin() ) {
		MSBDPSEO_Admin::init();
	}
}

add_action( 'plugins_loaded', 'msbdpseo_init_plugin' );

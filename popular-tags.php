<?php
/**
 * Plugin Name: Popular Tags
 * Plugin URI: https://ericwijaya.com/
 * Description: Create custom popular tags widget based on views.
 * Version: 1.0.0
 * Author: Eric Wijaya
 * Author URI: https://ericwijaya.com
 * Requires at least: 3.3
 * Tested up to: 4.0
 * License: GPLv2 or later
 *
 * Text Domain: popular-tags
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$plugin_dir = dirname( __FILE__ );
$plugin_dir_rel = dirname( plugin_basename( __FILE__ ) );
$plugin_url = plugin_dir_url( __FILE__ );

define( 'EC_POPULAR_TAGS_URL', $plugin_url );

if ( file_exists( $plugin_dir . '/class/ec-popular-tags.php' ) ) {
	require_once $plugin_dir . '/class/ec-popular-tags.php';
}

if ( file_exists( $plugin_dir . '/class/popular-tags-widget.php' ) ) {
	require_once $plugin_dir . '/class/popular-tags-widget.php';
}

// Load sidebar class
add_action( 'plugins_loaded', array( 'EC_Popular_Tags', 'get_instance' ) );


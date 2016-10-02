<?php
/**
 * Plugin Name: WP Feature Flags
 *
 * @package WP Feature Flags.
 *
 * Description: Add feature flag interface and API to WordPress
 * Version: 1.0.0
 * Text Domain: wp-feature-flags
 * Author: Mike Selander
 * Author URI: https://mikeselander.com
 */

namespace WP_Feature_Flags

/**
 * Retrieve the plugin instance.
 *
 * @return object Plugin
 */
function plugin() {
	static $instance;

	if ( null === $instance ) {
		$instance = new Plugin();
	}

	return $instance;
}

// Load the plugin.
plugin();

// Set our definitions for later use.
plugin()->set_definitions(
	(object) array(
		'basename'   => plugin_basename( __FILE__ ),
		'directory'  => plugin_dir_path( __FILE__ ),
		'file'       => __FILE__,
		'slug'       => 'wp-feature-flags',
		'url'        => plugin_dir_url( __FILE__ ),
		'assets_url' => plugin_dir_url( __FILE__ ) . '/assets',
		'version'    => '1.0.0'
	)
);

plugin()->register_hooks( new Admin() )
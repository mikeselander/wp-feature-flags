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

namespace WP_Feature_Flags;

/**
 * Autoloader callback.
 *
 * Converts a class name to a file path and requires it if it exists.
 *
 * @param string $class Class name.
 */
function feature_flags_autoloader( $class ) {
	$namespace = explode( '\\', $class );

	if ( __NAMESPACE__ !== $namespace[0] ){
		return;
	}

	$class = str_replace( __NAMESPACE__ . '\\', '', $class );

	$class = strtolower( preg_replace( '/(?<!^)([A-Z])/', '-\\1', $class ) );
	$file  = dirname( __FILE__ ) . '/classes/class-' . $class . '.php';

	if ( is_readable( $file ) ) {
		require_once( $file );
	}
}
spl_autoload_register( __NAMESPACE__ . '\feature_flags_autoloader' );

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

// Load helpers
require_once 'helpers/utilities.php';

plugin()->register_hooks( new Admin() )
        ->register_hooks( new Ajax() );

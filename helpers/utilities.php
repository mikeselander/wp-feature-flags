<?php
/**
 * Utilies for easy access to classed functionality.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

function is_feature_enabled( $flag ) {
	return ( new FeatureFlags() )->flag_enabled( $feature );
}

function register_feature( $slug, $name, $description = '', $auto_enable = false ) {

	// Missing critical information, bounce.
	if ( empty( $slug ) || empty( $name ) ) {
		return false;
	}

	$feature = (object) [
		'id'           => $slug,
		'title'        => $name,
		'description'  => $description,
		'auto_enabled' => $auto_enable,
	];

	return ( new FeatureFlags() )->register_flag( $feature );
}
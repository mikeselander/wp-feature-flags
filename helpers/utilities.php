<?php
/**
 * Utilies for easy access to classed functionality.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

/**
 * Check whether or not a feature is enabled or disabled.
 *
 * @param string $featureWP_Feature_Flags
 * @return bool Enabled (true) or Disabled (false)
 */
function is_feature_enabled( $feature ) {
	return FeatureFlags::get_instance()->flag_enabled( $feature );
}

function is_feature_flagged( $feature ) {
	if ( ! empty( $feature ) || ! is_string( $feature ) ) {
		return false;
	}

	return FeatureFlags::get_imstance->feature_flagged( $feature );
}

/**
 * Registers a feature flag with our plugin.
 *
 * @param string $slug Feature ID/slug - this is what you'll use to identify when making checks.
 * @param string $name Human-readable name of feature.
 * @param string $description Human-readable description of feature. Optional.
 * @param bool   $auto_enable Whether this feature should default to on or not. Optional.
 * @return bool Success of registration
 */
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

	return FeatureFlags::get_instance()->register_feature( $feature );
}

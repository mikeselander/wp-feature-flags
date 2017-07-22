<?php
/**
 * Class to handle registration, activation, and checking of feature flags.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

class FeatureFlags {

	/**
	 * instance
	 * Hold our class instance for single instantiation.
	 *
	 * @var FeatureFlags
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Flags option name.
	 *
	 * @var string
	 */
	public $flags_option = 'feature_flags';

	/**
	 * Hold all registered flags.
	 *
	 * @var array
	 */
	public $flags = [];

	/**
	 * Set and get an instance of this class.
	 *
	 * @return FeatureFlags
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register a feature for use in admin list and reference.
	 *
	 * @param object $flag Flag data [ id, title, description, auto_enable ]
	 * @return bool Success or failure
	 */
	public function register_feature( $flag ) {
		// if we have nothing to parse, bounce.
		if ( empty( $flag ) ) {
			return false;
		}

		// Add this flag to our filter of all flags.
		$this->flags[] = $flag;

		// If our flag is already registered in our option, skip next step.
		if ( isset( $this->get_flag_statuses()[ $flag->id ] ) ) {
			return true;
		}

		// Register our option in our setting and enable it if need be.
		if ( $flag->auto_enabled ) {
			$this->enable_flag( $flag->id );
		}

		return true;
	}

	/**
	 * Turn a flag on.
	 *
	 * @param string $flag Flag ID/slug.
	 */
	public function enable_flag( $flag_id ) {
		$this->update_flag( $flag_id, 'enabled' );

		/**
		 * Allow developer to run their own code after a flag has been enabled.
		 *
		 * @param string $flag_id Flag ID
		 */
		do_action( 'enable_feature_flag', $flag_id );
	}

	/**
	 * Turn a flag off.
	 *
	 * @param string $flag Flag ID/slug.
	 */
	public function disable_flag( $flag_id ) {
		$this->update_flag( $flag_id, 'disabled' );

		/**
		 * Allow developer to run their own code after a flag has been disabled.
		 *
		 * @param string $flag_id Flag ID
		 */
		do_action( 'disable_feature_flag', $flag_id );
	}

	/**
	 * Update a flag's status in our option.
	 *
	 * @param string $flag Flag ID/slug.
	 * @param string $status New status (enabled or disabled).
	 */
	private function update_flag( $flag, $status ) {
		$all_flags = (array) $this->get_flag_statuses();

		// If we don't have a value or the value is not the status attempted - update it.
		if ( ! isset( $all_flags[ $flag ] ) || $status !== $all_flags[ $flag ] ) {
			$all_flags[ $flag ] = $status;
			update_option( $this->flags_option, wp_json_encode( $all_flags ), true );
		}
	}

	/**
	 * Is this flag enabled?
	 *
	 * @param string $flag Flag ID/slug.
	 * @return bool Enabled (true) or Disabled (false)
	 */
	public function flag_enabled( $flag ) {
		$flag_statuses = $this->get_flag_statuses();
		$all_flags     = apply_filters( 'available_feature_flags', [] );

		// Does this flag exist?
		if ( ! isset( $all_flags[ $flag ] ) ) {
			// TODO: not sure what to return here, can't do false because we might have expired a flag
		}

		// Is flag auto-enabled?
		if ( isset( $all_flags[ $flag ] ) && $all_flags[ $flag ]->auto_enabled ) {
			return true;
		}

		// Is flag network-enabled?
		if ( is_multisite() && $this->flag_network_enabled( $flag ) ) {
			return true;
		}

		// Have we turned this feature on?
		if ( 'enabled' === $flag_statuses[ $flag ] ) {
			return true;
		}

		return false;
	}

	/**
	 * Is this flag enabled network-wide?
	 *
	 * @param string $flag Flag ID/slug.
	 * @return bool Enabled (true) or Disabled (false)
	 */
	public function flag_network_enabled( $flag ) {
		if ( ! is_multisite() ) {
			return false;
		}

		return false;
	}

	/**
	 * Enable a feature for every site in a network.
	 *
	 * @return bool success of enabling.
	 */
	public function network_enable_flag() {
		if ( ! is_multisite() ) {
			return false;
		}
	}

	/**
	 * Override flag status for one page load.
	 *
	 * @param string $flag Flag ID/name.
	 */
	public function spoof_flag_enabled( $flag ) {

	}

	/**
	 * Get all current flag statuses from our option.
	 *
	 * @return array All flag statuses.
	 */
	public function get_flag_statuses() {
		return (array) json_decode( get_option( $this->flags_option ), true );
	}

	/**
	 * Getter for all registered features.
	 *
	 * @return array Flags registered with the plugins
	 */
	public function get_features() {
		return $this->flags;
	}

}

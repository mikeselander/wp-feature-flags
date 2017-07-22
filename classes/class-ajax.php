<?php
/**
 * Class to handle all AJAX actions.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

class Ajax {
	/**
	 * Access to plugin definitions.
	 *
	 * @var TheSun_Post_Cloner_Plugin
	 * @access private
	 */
	private $plugin;

	/**
	 * Run hooks that the class relies on.
	 */
	public function hooks() {
		add_action( 'wp_ajax_ff_enable_flag', [ $this, 'enable_flag' ] );
		add_action( 'wp_ajax_ff_disable_flag', [ $this, 'disable_flag' ] );
	}

	/**
	 * Set a reference to the main plugin instance.
	 *
	 * @param Plugin $plugin Main plugin instance.
	 * @return Ajax
	 */
	public function set_plugin( $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	private function check_ajax_request() {
		check_ajax_referer( 'feature-flags-ajax', 'nonce' );

		if ( ! Admin::can_user_flag() ) {
			wp_send_json_error( __( 'User does not have correct permissions.', 'wp-feature-flags' ) );
		}
	}

	public function check_flag_sandbox() {

	}

	public function enable_flag() {
		$this->check_ajax_request();

		//@todo:: run sandbox here.

		$flag_id = sanitize_key( $_POST['flag_id'] );
		FeatureFlags::get_instance()->enable_flag( $flag_id );

		wp_send_json_success( __( 'Flag Enabled', 'wp-feature-flags' ) );
	}

	public function disable_flag() {
		$this->check_ajax_request();

		$flag_id = sanitize_key( $_POST['flag_id'] );
		FeatureFlags::get_instance()->disable_flag( $flag_id );

		wp_send_json_success( __( 'Flag Disabled', 'wp-feature-flags' ) );
	}
}

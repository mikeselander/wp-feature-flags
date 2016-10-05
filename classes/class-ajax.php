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
	 * Easy way to access all of our defined paths & info.
	 *
	 * @var object
	 * @access private
	 */
	private $definitions;

	/**
	 * Run hooks that the class relies on.
	 */
	public function hooks() {
		$this->definitions = $this->plugin->get_definitions();
	}

	/**
	 * Set a reference to the main plugin instance.
	 *
	 * @param Plugin $plugin Main plugin instance.
	 */
	public function set_plugin( $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	private function check_request() {

	}

	public function check_flag_sandbox() {

	}

	public function enable_flag() {
		
	}
	
	public function disable_flag() {
		
	}

}
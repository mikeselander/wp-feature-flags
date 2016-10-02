<?php
/**
 * Class to register, load, and display an admin interface.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

class Admin {
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

	public function register_flag_page() {

	}

	public function register_network_admin_page() {

	}

	public function flag_page() {

	}

	private function flag_row() {

	}
}
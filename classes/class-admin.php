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
	 * @var Plugin
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

		add_action( 'admin_menu', [ $this, 'register_flag_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_styles' ], 10, 1 );
	}

	/**
	 * Set a reference to the main plugin instance.
	 *
	 * @param $plugin Plugin instance.
	 * @return Admin
	 */
	public function set_plugin( $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	/**
	 * Register a site settings page.
	 */
	public function register_flag_page() {
		add_submenu_page(
			'options-general.php',
			__( 'Feature Flags', 'wp-feature-flags' ),
			__( 'Feature Flags', 'wp-feature-flags' ),
			apply_filters( 'feature-flags-can-flag-capability', 'manage_options' ),
			'feature_flags',
			[ $this, 'admin_page' ]
		);
	}

	/**
	 * Load our styles in the admin area.
	 */
	public function load_styles( $page ) {
		// Only load styles on the edit screen.
//		if ( 'options-general.php' !== $page ) {
//			return;
//		}

		wp_enqueue_style( 'feature-flag-styles', $this->definitions->assets_url . '/feature-flags.css', [], $this->definitions->version );
		wp_enqueue_script( 'feature-flag-script', $this->definitions->assets_url . '/feature-flags.js', [], $this->definitions->version );

		wp_localize_script( 'feature-flag-script', 'featureFlags', [
			'nonce' => wp_create_nonce( 'feature-flags-ajax' ),
		] );
	}

	/**
	 * Register a network settings page.
	 */
	public function register_network_admin_page() {

	}

	/**
	 * Print our admin page and list table.
	 */
	public function admin_page() {
		$list_table = new FeatureListTable();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<h2><?php esc_html_e( 'Feature Flags', 'wp-feature-flags' ); ?></h2>
			<?php $list_table->display(); ?>
		</div>
		<?php
	}

	/**
	 *
	 *
	 * @return bool
	 */
	public static function can_user_flag() {

		/**
		 *
		 * Minimum capability required to trigger feature flags.
		 */
		$capability = apply_filters( 'feature-flags-can-flag-capability', 'manage_options' );

		return current_user_can( $capability );
	}
}

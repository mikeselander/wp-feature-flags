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

		add_action( 'admin_init', [ $this, 'register_flag_page' ] );
	}

	public function register_flag_page() {
		add_submenu_page(
			'options-general.php',
			__( 'Feature Flags', 'wp-feature-flags' ),
			__( 'Feature Flags', 'wp-feature-flags' ),
			'manage_options',
			'feature_flags',
			[ $this, 'flag_page' ]
		)
	}

	public function register_network_admin_page() {

	}

	public function flag_page() {
		$list_table = new Feature_List_Table();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<h2><?php esc_html_e( 'All Feature Flags', 'wp-feature-flags' ); ?></h2>
			<?php $list_table->display(); ?>
		</div>
		<?php
	}
}

<?php
/**
 * List table for feature flags.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

// @todo:: this should not be in this file.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FeatureListTable extends \WP_List_Table {

	private $feature_flags;

	public function __construct() {
		$this->feature_flags = FeatureFlags::get_instance();

		parent::__construct( [
			'singular' => __( 'Feature Flag', 'wp-feature-flags' ),
			'plural'   => __( 'Feature Flags', 'wp-feature-flags' ),
		] );
	}

	/**
	 * Prepare the items for the table to process
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * Define column used in list table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = [
			'title'       => 'Title',
			'description' => 'Description',
			'enabled'     => 'Enabled',
		];

		return $columns;
	}

	/**
	 * Define the sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'title' => [ 'title', false ],
		];
	}

	/**
	 * Get the table data.
	 *
	 * @return array $all data for the table
	 */
	private function table_data() {
		$data = [];

		$flag_statuses = $this->feature_flags->get_flag_statuses();
		$all_flags     = $this->feature_flags->get_features();

		foreach ( $all_flags as $flag ) {
			$data[] = [
				'title'       => esc_html( $flag->title ) . '<br><i>(' . esc_html( $flag->id ) . ')</i>',
				'description' => esc_html( $flag->description ),
				'enabled'     => $this->on_off_switch( $flag->id, $flag_statuses[ $flag->id ], $flag->auto_enabled ),
			];
		}

		return $data;
	}

	public function on_off_switch( $id, $enabled, $auto_enabled ) {
		$wrapper_classes = [ 'feature-toggle-wrapper' ];
		$status = $enabled;

		// If the feature is auto-enabled we want to disable access to the trigger.
		if ( $auto_enabled ) {
			$wrapper_classes[] = 'disabled';
			$status            = 'enabled';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( join( ' ', $wrapper_classes ) ); ?>">
			<input id="toggle-<?php echo esc_attr( $id ); ?>" type="checkbox" class="feature-toggle" data-flag-id="<?php echo esc_attr( $id ); ?>" <?php checked( 'enabled', $status ); ?>>
			<label for="toggle-<?php echo esc_attr( $id ); ?>" class="toggle-button"></label>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param  array $item Data
	 * @param  String $column_name - Current column name
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
			case 'description':
			case 'enabled':
				return $item[ $column_name ];

			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'title';
		$order   = 'asc';

		// @todo:: nonce needed here.

		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
		}

		// If order is set use this as the order
		if ( ! empty( $_GET['order'] ) ) {
			$order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
		}

		$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( 'asc' === $order ) {
			return $result;
		}

		return -$result;
	}
}

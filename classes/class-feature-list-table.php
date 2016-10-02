<?php
/**
 * List table for feature flags.
 *
 * @package WP Feature Flags.
 */

namespace WP_Feature_Flags;

if( ! class_exists( 'WP_List_Table' ) ) {
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

		$perPage     = 20;
		$currentPage = $this->get_pagenum();
		$totalItems  = count( $data );

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * Define column used in list table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'id'          => 'ID',
			'title'       => 'Title',
			'description' => 'Description',
			'enabled'     => 'Enabled',
		);

		return $columns;
	}

	/**
	 * Define the sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'title' => [ 'title', false ]
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
				'id'           => esc_html( $flag->id ),
				'title'        => esc_html( $flag->title ),
				'description'  => esc_html( $flag->description ),
				'enabled'      => $flag_statuses[ $flag->id ],
				'auto_enabled' => $flag->auto_enabled,
			];
		}

		return $data;
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param  array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'id':
			case 'title':
			case 'description':
			case 'enabled':
				return $item[ $column_name ];

			default:
				return print_r( $item, true ) ;
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
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if( ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}


		$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );

		if( $order === 'asc' ) {
			return $result;
		}

		return -$result;
	}
}

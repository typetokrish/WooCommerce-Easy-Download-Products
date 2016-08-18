<?php
/**
 * WP LIST Table Implemntation handler
 *
 * @package     Easy-Download-For-Products \ Plugin Downloads
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
//Exteds the Wp_LIST_TABLE

class TK_EasyDownloads_Downloads extends WP_List_Table{
	
	public function __construct() {
		global $wpdb;
		parent::__construct( [
			'singular' => __( 'Download', 'tk_ed_downloads' ), //singular name of the listed records
			'plural'   => __( 'Downloads', 'tk_ed_downloads' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
		$this->table_name = $wpdb->prefix . "tk_ed_downloads";

	}
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  global $wpdb;	
	  $table_name = $wpdb->prefix . "tk_ed_downloads";
	  $sql = "SELECT COUNT(*) FROM {$table_name}";	
	  return $wpdb->get_var( $sql );
	}
	/*
	 * Get paginated results from the table
	*/
	public static function get_downloads( $per_page = 5, $page_number = 1 ) {
	  	global $wpdb;
		$table_name = $wpdb->prefix . "tk_ed_downloads";
		$sql = "SELECT * FROM {$table_name}";	
		if ( ! empty( $_GET['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
			$sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
		}else{
			$sql.=' ORDER BY `time` DESC ';
		}
		
		$sql .= " LIMIT $per_page";	
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );		
		return $result;
	}
	
	/**
	 * Delete a download request 
	 *
	 * @param int $id customer ID
	 */
	public static function delete_request( $id ) {
	  	global $wpdb;
		$table_name = $wpdb->prefix . "tk_ed_downloads";
		$wpdb->delete(
			"{$table_name}",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}
	/*
	 * Custom message to show no records
	*/
	public function no_items() {
	  _e( 'No Download Request avaliable.', 'tk_ed_downloads' );
	}
	
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {	
	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'sp_delete_downloads' );	
	  $title = '<strong>' . $item['name'] . '</strong>';	
	  $actions = [
		'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
	  ];
	  return $title . $this->row_actions( $actions );
	}
	
	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {	
	  switch ( $column_name ) {
		case 'email': 
			return $item[$column_name];
		case 'time': 
			return $item[$column_name];
		case 'product':		
			$product=wc_get_product( $item[$column_name] );		
			return '<a href="'.$product->post->guid.'" target="_blank">'.$product->post->post_title.'</a>';		
		case 'ip': 
			return $item[$column_name];
		default:
		  return '-'; 
	  }
	}
	
	
	
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
	  return sprintf(
		'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	  );
	}
	
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = [
		'cb'      => '<input type="checkbox" />',
		'name'    => __( 'Customer Name', 'tk_ed_downloads' ),
		'email' => __( 'Email', 'tk_ed_downloads' ),
		'time'    => __( 'Date', 'tk_ed_downloads' ),
		'product'    => __( 'Product', 'tk_ed_downloads' ),
		'ip'    => __( 'IP', 'tk_ed_downloads' )
	  ];
	
	  return $columns;
	}
	
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  $sortable_columns = array(
		'name' => array( 'name', true ),
		'product' => array( 'product', false )
	  );
	
	  return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
	  $actions = [
		'bulk-delete' => 'Delete'
	  ];
	
	  return $actions;
	}
	
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
	
	   	$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable
		);
	
	  /** Process bulk action */
	  $this->process_bulk_action();
	
	  $per_page     = $this->get_items_per_page( 'downloads_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();
	
	  $this->set_pagination_args( [
		'total_items' => $total_items, //WE have to calculate the total number of items
		'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ] );	
	  $this->items = self::get_downloads( $per_page, $current_page );
	 
	}
	
	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {
	
		// In our file that handles the request, verify the nonce.
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );
	
		if ( ! wp_verify_nonce( $nonce, 'sp_delete_downloads' ) ) {
		  die( 'Go get a life script kiddies' );
		}
		else {
		  self::delete_request( absint( $_GET['customer'] ) );	
		  wp_redirect( esc_url( add_query_arg() ) );
		  exit;
		}
	
	  }
	
	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		   || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {
	
		$delete_ids = esc_sql( $_POST['bulk-delete'] );
	
		// loop over the array of record IDs and delete them
		foreach ( $delete_ids as $id ) {
		  self::delete_request( $id );
	
		}	
		wp_redirect( esc_url( add_query_arg() ) );
		exit;
	  }
	}
	
	
	
}
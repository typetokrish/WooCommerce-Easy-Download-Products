<?php
/**
 * Activation handler
 *
 * @package     Easy-Download-For-Products \ Plugin Activation
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
class TK_EasyDownloads_Activation{
	
	public function run(){		
		global $wpdb;
		$table_name = $wpdb->prefix . "tk_ed_downloads"; 
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  name varchar(160) NOT NULL,
		  email varchar(160) NOT NULL,
		  product bigint(9) NOT NULL,
		  ip varchar(36),
		  PRIMARY KEY (`id`)  
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	public function deactivation(){
		//deactivated plugin//
		//no crrent ctions to execute
	}
	
	public function uninstall(){
		global $wpdb;
		$table_name = $wpdb->prefix . "tk_ed_downloads"; 
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "DROP TABLE $table_name ";		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}
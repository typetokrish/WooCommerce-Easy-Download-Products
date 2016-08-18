<?php
/**
 * Activation handler
 *
 * @package     Easy-Download-For-Products \ Plugin Init
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
class TK_EasyDownloads_Init{
	
	public function run(){
		add_action('admin_init', array($this,'restrict_admin'), 1 );
		add_action('admin_init', array($this,'setup_settings'), 2 );		
		add_action('admin_menu', array($this,'menu_settings'));
	}
	public function restrict_admin(){
		if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			wp_die( __( 'You are not allowed to access this part of the site' ) );
		}
	}
	
	
	public function menu_settings(){
		
		//Generate the menu structure on side menus//
		//Settings Menu for Downloads//
		add_submenu_page('woocommerce',
			__('All Product Downloads','tk-easy-downloads'), //title of the page
			__( 'Download History','tk-easy-downloads'),	//Menu title	
			 'manage_options',								//Required permission to use this
			 'tk-ed-downloads',								//id or the slug for the menu item : unique/
			 array($this,'display_history'),				//Callback to display the page content//
			 '',											//
			 $position = 1 );								//Position of the menu element//
			 	
		add_options_page(
			'WO Easy Downloads',
			'WO Easy Downloads',
			'manage_options',
			'tk-ed-options-page',
			array($this,'display_settings')
		);		
	}
	
	function display_history(){
		$obj	=	new TK_EasyDownloads_Product();
		$obj->download_results();	
	}
	function setup_settings(){
		$obj	=	new TK_EasyDownloads_Settings();
		$obj->init();		
	}
	function display_settings(){
		
		$obj	=	new TK_EasyDownloads_Settings();		
		return $obj->display();
	}
	
	
}
<?php
/**
 * Plugin Name: WooCommerce - TK Easy Download Products
 * Plugin URI: https://github.com/typetokrish/WooCommerce-Easy-Downlod-Products
 * Description: WooCommerce - TK Easy Download Products - allows to set up products that can be downloaded easily without adding to cart. This requires user to submit a form and the download link will be on their email.
 * Author: Kiran Krishnan
 * Author URI: https://github.com/typetokrish/
 * Version: 1.0.0
 * License: GPL2
 * 
 * Text Domain: tk-easy-downloads
 * Domain Path: /languages
 *
 * @package         Easy-Download-For-Products
 * @author          Kiran Krishnan
 * @copyright       Copyright (c) 2016
 *
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/*
 * TK EasyDownloads  Entry clas handled hooks and implementation logic
 * Settings screen and the functionality reside in further class-functions
*/

if( !class_exists( 'TK_EasyDownloads' ) ) {
	
	class TK_EasyDownloads{
		
		private static $instance;
		
		public function __construct(){
			/*
			* Set up the config / constants variables
			*/
			define('TK_ED_PLUGIN_DIR',plugin_dir_path( __FILE__ ));
		
		
		}
		/*
		 * Get the Instance of the TK_EasyDownloads class 
		 * Ensure single object exists
		*/
		public function instance(){
			if( !self::$instance ) {
               self::$instance = new TK_EasyDownloads();
			   self::$instance->init();
			   self::$instance->hooks();
               
            }
            return self::$instance;
		}
		/*
		 * Initialize the plugin features 
		 * @ void
		 * return null
		*/
		public function init(){			
			if(!class_exists('TK_EasyDownloads_Init')){
				require_once('includes/init.php');
			}
			if(!class_exists('TK_EasyDownloads_Settings')){
				require_once('includes/settings.php');
			}
			if(!class_exists('TK_EasyDownloads_Downloads')){
				require_once('includes/downloads.php');
			}
			$init=new TK_EasyDownloads_Init();
			$init->run();
			
		}
		/*
		 * Initialize the run time hooks
		 * @ nill
		*/
		public function hooks(){
			if(!class_exists('TK_EasyDownloads_Product')){
				require_once('includes/product.php');
			}
			
			$products=new TK_EasyDownloads_Product();
			$products->run();
		}
	}
	
}


/*
 * Plugin Load Hooks
 * Create the TK_EasyDownloads object when plugin loaded
*/
function TK_EasyDownloads_load(){
	return TK_EasyDownloads::instance();
}
add_action( 'plugins_loaded', 'TK_EasyDownloads_load' );  // load plugin hook//

/*
* Activation Hooks
*/

function tk_easy_dwonlaod_activation() {
	if(!class_exists('TK_EasyDownloads_Activation')){
    	require_once('includes/activation.php');
	}
	$start=new TK_EasyDownloads_Activation();
	$start->run();
}
/*Deactivation*/
function tk_easy_dwonlaod_deactivation() {
	if(!class_exists('TK_EasyDownloads_Activation')){
    	require_once('includes/activation.php');
	}
	$start=new TK_EasyDownloads_Activation();
	$start->deactivation();
}

/*Uninstall*/
function tk_easy_dwonlaod_uninstall() {
	if(!class_exists('TK_EasyDownloads_Activation')){
    	require_once('includes/activation.php');
	}
	$start=new TK_EasyDownloads_Activation();
	$start->uninstall();
}
//register plugin life time hooks (activation/deactivation/uninstall)
register_activation_hook( __FILE__, 'tk_easy_dwonlaod_activation' );
register_deactivation_hook( __FILE__, 'tk_easy_dwonlaod_deactivation' );
register_uninstall_hook( __FILE__, 'tk_easy_dwonlaod_uninstall' );

/*
* Add required JS/CSS files from both plugin and other libs
*/
if(is_admin()){
	wp_enqueue_script('tk_ed_admin_js', plugin_dir_url(__FILE__) . 'js/admin.js');
}else{
	wp_enqueue_script('tk_ed_user_js', plugin_dir_url(__FILE__) . 'js/easy-downloads-tk.js');
	wp_localize_script( 'tk_ed_user_js', 'tk_ed_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-position' );
	wp_enqueue_style('tk_ed_user_css', plugin_dir_url(__FILE__) . 'css/easy-downloads-tk.css');
	
	$queryui = $wp_scripts->query('jquery-ui-core');
    // load the jquery ui theme
    $url = "http://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";
    wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
}

?>
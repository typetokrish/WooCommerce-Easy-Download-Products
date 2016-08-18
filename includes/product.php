<?php
/**
 * Activation handler
 *
 * @package     Easy-Download-For-Products \ Plugin Init
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
class TK_EasyDownloads_Product{
	
	public function run(){		
		//Create Hooks / Filter / Actions for WooCOmmerce//
		
		add_filter('woocommerce_product_options_downloads',array($this,'tk_ed_product_downloadable'));
		add_action( 'woocommerce_process_product_meta', array($this,'tk_ed_save_product_fields') );
		add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'tk_ed_addtocart_link') );		
		
		if(is_admin()){
			add_action( 'wp_ajax_tk_ed_display_download_display', array($this,'tk_ed_display_download_display') );
			add_action( 'wp_ajax_tk_ed_submit_form', array($this,'tk_ed_submit_form') );
		}
		add_action( 'wp_ajax_nopriv_tk_ed_display_download_display', array($this,'tk_ed_display_download_display') );
		add_action( 'wp_ajax_nopriv_tk_ed_submit_form', array($this,'tk_ed_submit_form') );
		
		/*
		 * Remove the Action hook for add to cart on templte and define a custom hook
		*/
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		add_action( 'woocommerce_single_product_summary',  array($this,'tk_ed_single_add_to_cart'), 30 );

	}
	
	/*
	* add product cutom field 
	*/
	public function tk_ed_product_downloadable($options){
		global $woocommerce, $post;  
		  echo '<div class="options_group">';
		  $text=woocommerce_wp_checkbox( 
			array( 
				'id'          => 'tk_ed_immediate_download', 
				'label'       => __( 'Immediate Download', 'tk-easy-downloads' ), 
				'placeholder' => '',
				'desc_tip'    => 'true',
				'description' => __( 'Selecting this option gives immediate download for products', 'tk-easy-downloads' ) 
			)
		);
		  echo $text; 
		  echo '</div>';	
		
	}
	/*
	 * Save product fields
	*/
	function tk_ed_save_product_fields( $post_id ){	
		$woocommerce_text_field = $_POST['tk_ed_immediate_download'];
		if( !empty( $woocommerce_text_field ) )
			update_post_meta( $post_id, 'tk_ed_immediate_download', esc_attr( $woocommerce_text_field ) );
	
	}
	/*
	 * Add to cart button over ride for single product
	 * Removed the hook and do custom
	*/
	public function tk_ed_single_add_to_cart($button){
		
		global $product;
		 
		 if($product->is_downloadable('yes')){
			$immediate_download = get_post_meta( $product->id, 'tk_ed_immediate_download', true ); 
			if($immediate_download=='yes'){
			$button= sprintf( '<form class="cart" enctype="multipart/form-data" method="post"><a href="javascript:void();" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s single_add_to_cart_button alt" onclick="tk_ed_show_popup(\'%s\')">'.__('Download Now','tk-easy-downloads').'</a></form>',
			  
			  esc_attr( $product->id ),
			  esc_attr( $product->get_sku() ),
			  esc_attr( isset( $quantity ) ? $quantity : 1 ),
			  $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			  esc_attr( $product->product_type ),
			  esc_attr( $product->id )
			  );
			  echo $button;
			  return true;
			}
		 }
		 do_action( 'woocommerce_' . $product->product_type . '_add_to_cart' );
	}
	
	/*
	 * Modifieds the add to cart link on products listing 
	*/
	function tk_ed_addtocart_link($button){		
		 global $product;
		 
		 if($product->is_downloadable('yes')){
			$immediate_download = get_post_meta( $product->id, 'tk_ed_immediate_download', true ); 
			if($immediate_download=='yes'){
			return sprintf( '<a href="javascript:void();" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s" onclick="tk_ed_show_popup(\'%s\')">'.__('Download Now','tk-easy-downloads').'</a>',
			  
			  esc_attr( $product->id ),
			  esc_attr( $product->get_sku() ),
			  esc_attr( isset( $quantity ) ? $quantity : 1 ),
			  $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			  esc_attr( $product->product_type ),
			  esc_attr( $product->id )
			  );
			}
		 }
		 return  $button;
		 
	}
	
	/*
	 Ajax call for pop upcontent display
	*/
	public function tk_ed_display_download_display(){
		$id	=	$_POST['id'];
		$response = json_encode( array( 'success' => true ) );	
		$tk_ed_opt_pp_heading				=	get_option('tk_ed_opt_pp_heading',true); 
		$tk_ed_opt_pp_description			=	get_option('tk_ed_opt_pp_description',true); 
		$tk_ed_opt_pp_name_label			=	get_option('tk_ed_opt_pp_name_label',true); 
		$tk_ed_opt_pp_name_placeholder		=	get_option('tk_ed_opt_pp_name_placeholder',true); 
		$tk_ed_opt_pp_email_label			=	get_option('tk_ed_opt_pp_email_label',true); 
		$tk_ed_opt_pp_email_placeholder		=	get_option('tk_ed_opt_pp_email_placeholder',true); 
		$tk_ed_opt_pp_button_text			=	get_option('tk_ed_opt_pp_button_text',true);
		$tk_ed_opt_pp_show_download_link	=	get_option('tk_ed_opt_pp_show_download_link',true);
		$template=file_get_contents(dirname(dirname(__FILE__)).'/templates/form.php');
		$template	=	str_replace('{heading}',$tk_ed_opt_pp_heading,$template);
		$template	=	str_replace('{description}',$tk_ed_opt_pp_description,$template);
		$template	=	str_replace('{label_name}',$tk_ed_opt_pp_name_label,$template);
		$template	=	str_replace('{place_name}',$tk_ed_opt_pp_name_placeholder,$template);
		$template	=	str_replace('{label_email}',$tk_ed_opt_pp_email_label,$template);
		$template	=	str_replace('{place_email}',$tk_ed_opt_pp_email_placeholder,$template);
		$template	=	str_replace('{place_button}',$tk_ed_opt_pp_button_text,$template);	
		$template	=	str_replace('{tk_ed_hidden}',$id,$template);
		$template	=	str_replace('{info_text}',__('* All fields are required','tk-easy-downloads'),$template);
		
		echo $template;
		exit;
	}
	
	/*
	* Handle the ajax form submit 
	*/
	public function tk_ed_submit_form(){
		$resp=array('error'=>1,'message'=>'Processed');
		if(isset($_POST)){
			$name	=	trim(strip_tags($_POST['name']));
			$email	=	trim(strip_tags($_POST['email']));
			$id		=	trim(strip_tags($_POST['product']));
			if(empty($name) || empty($email) || empty($id)){				
				$resp['message']=__('Enter required data','tk-easy-downloads');
			}else{
				//get the download links//
				try{
					global $wpdb;
					$table_name = $wpdb->prefix . "tk_ed_downloads";
					//Get the product and Downloadblae links//
					$product=wc_get_product( $id );	
					$downloads = $product->get_files();					
					//now save the result to download list//
					$data	=	array(	'time'=>date('Y-m-d H:i:s'),'name'=>addslashes($name),'email'=>addslashes($email),
										'product'=>$id,'ip'=>$_SERVER['REMOTE_ADDR']);
					$wpdb->insert( $table_name, $data, array() );
					//Format Email and send download links to emails//
					$mail_subject=get_option('tk_ed_opt_pp_mail_subject',true);
					$mail_content=get_option('tk_ed_opt_pp_mail_content',true);
					$links='';
					$attach=array();
					foreach( $downloads as $key => $each_download ) {
					  $links.= '<p><a href="'.$each_download["file"].'">Download</a></p>';
					  $attach[]=$each_download["file"];
					}
					$mail_content	=	str_replace('{download_links}',$links,$mail_content);
					$mail_content	=	str_replace('{name}',$name,$mail_content);
					wp_mail($email,$mail_subject,$mail_content,array(),$attach);					
					$resp['error']=0;
					$resp['message']=get_option('tk_ed_opt_pp_success_message',true);	
				}catch(Exception $c){
					$resp['message']=__('Unknown error occured','tk-easy-downloads');
				}
							
			}
		}
		ob_flush();
		header('Content-type : application/json');
		echo json_encode($resp);
		exit;
	}
	
	/*
	 * Display the Results of the download request
	 * on admin submenu
	*/
	function download_results(){
		global $wpdb;
		$table_name = $wpdb->prefix . "tk_ed_downloads";
		
		$table=	new TK_EasyDownloads_Downloads();
		
		?>
        <div class="wrap">
        	<h2><?php _e('Download Request History (TK Easy Downloads)','tk-easy-downloads');?></h2>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                            <?php
								$table->prepare_items(); 
								$table->display(); 
								?>
                            </form>
                        </div>
                   </div>
               </div>
           </div>            
        </div>
		<?php
	}
	
	
	
}
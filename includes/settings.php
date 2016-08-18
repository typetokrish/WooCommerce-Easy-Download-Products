<?php
/**
 * Settings handler
 *
 * @package     Easy-Download-For-Products \ Plugin Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class TK_EasyDownloads_Settings{
		
	public function init(){	
		/*
		 * Generate custom options page
		 * This is invoked from one of tthe admin_init hooks on  plugin load
		*/
		$setting_group	=	'tk-ed-options';				//Settings group name
		$setting_option	=	'tk-ed-options-values';			//Section for settings
		$section_name	=	'tk-ed-options-section-main';	//Section for settings
		$page			=	'tk-ed-options-page';			//Page on which we have to load the settings
		/*
		 * Defin the settings fields //
		*/
		$this->options=array(
			'tk_ed_opt_pp_heading'=>array('title'=>__('Popup Heading','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_description'=>array('title'=>__('Popup Description','tk-easy-downloads'),'type'=>'textarea'),
			'tk_ed_opt_pp_name_label'=>array('title'=>__('Label for Name','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_name_placeholder'=>array('title'=>__('Placeholder for Name','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_email_label'=>array('title'=>__('Label for Email','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_email_placeholder'=>array('title'=>__('Placeholder for Email','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_button_text'=>array('title'=>__('Popup HeadingDownload button text','tk-easy-downloads'),'type'=>'text'),			
			'tk_ed_opt_pp_mail_subject'=>array('title'=>__('Mail Subject','tk-easy-downloads'),'type'=>'text'),
			'tk_ed_opt_pp_mail_content'=>array('title'=>__('Mail Content','tk-easy-downloads'),'type'=>'textarea'),	
			'tk_ed_opt_pp_success_message'=>array('title'=>__('Success message','tk-easy-downloads'),'type'=>'textarea'),	
		);
		/*
		 * Add a section for the settings screen :
		*/
		add_settings_section(
				$section_name,														//Settings ID
				__('Easy Downloads Settings','tk-easy-downloads'), 					//Name
				array($this,'plugin_section_text'), 								//Callback
				$page																//Page
		);
		/*
		 * Loop through the options defined and generate actual fields and register 
		 * on the settings screen 
		*/
		foreach($this->options as $option=>$set){			
			$args			=	$set;
			$args['key']	=	$option;
			//Add the settings field//
			add_settings_field( 
				$option, 										//option name
				$set['title'], 									//title
				array($this,'field_display'),					//callback
				$page, 											//Page to apply
				$section_name, 									//Section
				$args
			);		
		}
		//register settings field for each option//
		foreach($this->options as $option=>$set){			
			register_setting(
				$setting_group, 										//setting group name
				$option													//option name
			);				
		}
		
		add_filter('whitelist_options', array($this,'whitelist_options'));

			
	}
	function whitelist_options( $options ) {
		global $new_whitelist_options;	
		$page			=	'tk-ed-options-page';	
		$options[$page] = array();
		foreach($this->options as $option=>$set){			
			$options[$page][]=$option;			
		}		
		return $options;
	}
	/*
	 * Text to show as header for the settings section
	*/
	function plugin_section_text() {		
		_e('Chanage your configuration for WooCommerce Easy Downalods','tk-easy-downloads');
	}
	
	/*
	 * Display actual input fields
	 * Set up each field type and display 
	 * Call back function for add_settings_field  
	 * @ params : $input(arra of field specification)
	 * returns null
	*/
	function field_display($input){
		$field=$input;
		if($field['type']=='text'){
			?>
        	<input type="text" name="<?php echo $field['key'];?>" value="<?php echo esc_attr( get_option($field['key']) ); ?>" />       
        	<?php
			}elseif($field['type']=='textarea'){
				?>				
       			 <textarea rows="2" name="<?php echo $field['key'];?>" ><?php echo esc_attr( get_option($field['key']) ); ?></textarea>       
				<?php
			}
			elseif($field['type']=='checkbox'){
				?>
				<input type="checkbox" value="yes" name="<?php echo $field['key'];?>" <?php if( esc_attr( get_option($field['key']) )=='yes'){?> checked="checked"<?php } ?> />
				<?php
			}
	}
	/*
	 * Display the settings screen
	 * Call back on admin_menu
	*/	
	function display(){		
		$setting_group	=	'tk-ed-options';
		$setting_option	=	'tk-ed-options-values';
		$section_name	=	'tk-ed-options-section-main';
		$page			=	'tk-ed-options-page';
		?>
		<div class="wrap">
        	<h2><?php _e('WooCommerce Easy Downloads Settings','tk-easy-downloads');?></h2>
             <form method="post" action="options.php">  
                <?php settings_fields($setting_group ); ?>
                <?php do_settings_sections( $page ); ?>     
                <?php submit_button(); ?> 
            </form>
    	</div>
		<?php
	}	
	
}